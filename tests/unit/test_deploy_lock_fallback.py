#!/usr/bin/env python3
"""Regression tests for deploy lock ownership fallback logic."""

import importlib.util
import pathlib
import shutil
import tempfile
import types
import unittest
from unittest import mock


class DeployLockFallbackTest(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.repo_root = pathlib.Path(__file__).resolve().parents[2]
        cls.temp_layout = tempfile.TemporaryDirectory()
        module_root = pathlib.Path(cls.temp_layout.name) / "opt" / "meza"
        scripts_dir = module_root / "src" / "scripts"
        i18n_dir = module_root / "config" / "i18n"

        scripts_dir.mkdir(parents=True, exist_ok=True)
        i18n_dir.mkdir(parents=True, exist_ok=True)

        shutil.copy2(cls.repo_root / "src" / "scripts" / "meza.py", scripts_dir / "meza.py")
        shutil.copy2(cls.repo_root / "config" / "i18n" / "en.yml", i18n_dir / "en.yml")

        module_path = scripts_dir / "meza.py"
        spec = importlib.util.spec_from_file_location(
            "meza_module_under_test",
            module_path,
        )
        module = importlib.util.module_from_spec(spec)
        spec.loader.exec_module(module)
        cls.meza = module

    @classmethod
    def tearDownClass(cls):
        cls.temp_layout.cleanup()

    def test_request_lock_uses_fallback_user_and_group(self):
        with tempfile.TemporaryDirectory() as meza_data_dir:
            self.meza.defaults["m_meza_data"] = meza_data_dir

            def fake_getpwnam(username):
                if username == "meza-ansible":
                    raise KeyError(username)
                raise AssertionError(f"Unexpected user lookup: {username}")

            def fake_getgrnam(groupname):
                if groupname in ("apache", "www-data"):
                    raise KeyError(groupname)
                if groupname == "wheel":
                    return types.SimpleNamespace(gr_gid=10)
                raise AssertionError(f"Unexpected group lookup: {groupname}")

            with mock.patch.object(self.meza.os, "getpid", return_value=1234), \
                    mock.patch.object(self.meza.pwd, "getpwnam", side_effect=fake_getpwnam), \
                    mock.patch.object(
                        self.meza.pwd,
                        "getpwuid",
                        return_value=types.SimpleNamespace(pw_name="root"),
                    ), \
                    mock.patch.object(self.meza.grp, "getgrnam", side_effect=fake_getgrnam), \
                    mock.patch.object(self.meza, "meza_chown") as mocked_chown, \
                    mock.patch.object(self.meza.os, "chmod") as mocked_chmod:
                result = self.meza.request_lock_for_deploy("monolith")

            lock_file = pathlib.Path(meza_data_dir) / "env-monolith-deploy.lock"

            self.assertTrue(lock_file.exists())
            self.assertEqual("1234", result["pid"])
            mocked_chown.assert_called_once_with(str(lock_file), "root", "wheel")
            mocked_chmod.assert_called_once_with(str(lock_file), 0o664)


if __name__ == "__main__":
    unittest.main()
