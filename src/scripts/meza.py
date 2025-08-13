#!/usr/bin/env python
#
# meza command
#

import signal
import sys
import getopt
import os
import yaml
import jinja2

# Get installation directory, typically /opt, but configurable elsewhere
install_dir = os.path.dirname(os.path.dirname(
    os.path.dirname(os.path.dirname(os.path.realpath(__file__)))))

#
# It'd be much better to pull this from config/paths.yml, but doing so requires processing YAML and Jinja
# and Meza with Python 2.7 doesn't seem to have the dependiencies in place to make that happen easily.
#
defaults = {
    "m_i18n": "{}/meza/config/i18n".format(install_dir),
    "m_meza_data": "{}/data-meza".format(install_dir),
    "m_logs_deploy": "{}/data-meza/logs/deploy/deploy.log".format(install_dir),
    "m_logs": "{}/data-meza/logs".format(install_dir),
    "m_local_secret": "{}/conf-meza/secret".format(install_dir),
    "m_home": "{}/conf-meza/users".format(install_dir),
    "m_config_vault": "{}/conf-meza/vault".format(install_dir),
}

# Handle pressing of ctrl-c. Make sure to remove lock file when deploying.
deploy_lock_environment = False


def sigint_handler(sig, frame):
    """
    Signal handler for SIGINT (Ctrl+C) interrupt.

    Args:
        sig (int): The signal number.
        frame (frame): The current stack frame.
    """
    print('Cancelling...')
    if deploy_lock_environment:
        print('Deploy underway...removing lock file')
        unlock_deploy(deploy_lock_environment)
    sys.exit(1)


signal.signal(signal.SIGINT, sigint_handler)


def load_yaml(filepath):
    """
    Load YAML data from a file.

    Args:
        filepath (str): The path to the YAML file.

    Returns:
        dict: The parsed YAML data.

    Raises:
        yaml.YAMLError: If there is an error parsing the YAML file.
    """
    with open(filepath, 'r') as stream:
        try:
            return yaml.load(stream, Loader=yaml.Loader)
        except yaml.YAMLError as exc:
            print(exc)


# Hard-coded for now, because I'm not sure where to set it yet
language = "en"
i18n = load_yaml(os.path.join(defaults['m_i18n'], language + ".yml"))


def main(argv):
    """
    The main function of the script.

    Parameters:
    - argv (list): The list of command-line arguments passed to the script.

    Returns:
    None
    """

    # meza requires a command parameter. No first param, no command. Display
    # help. Also display help if explicitly specifying help.
    if len(argv) == 0:
        display_docs('base')
        sys.exit(1)
    elif argv[0] in ('-h', '--help'):
        display_docs('base')
        sys.exit(0)  # asking for help doesn't give error code
    elif argv[0] in ('-v', '--version'):
        import subprocess
        version = subprocess.check_output(
            ["git", "--git-dir={}/meza/.git".format(install_dir), "describe", "--tags", "--always"])
        commit = subprocess.check_output(
            ["git", "--git-dir={}/meza/.git".format(install_dir), "rev-parse", "HEAD"])
        print("Meza " + version.strip().decode())
        print("Commit " + commit.strip().decode())
        print("Mediawiki EZ Admin")
        print("")
        sys.exit(0)

    # Every command has a sub-command. No second param, no sub-command. Display
    # help for that specific sub-command.
    # sub-command "update" does not require additional directives
    if len(argv) == 1 and argv[0] != "update":
        display_docs(argv[0])
        sys.exit(1)
    elif len(argv) == 2 and argv[1] in ('--help', '-h'):
        display_docs(argv[0])
        sys.exit(0)

    command = argv[0]
    command_fn = "meza_command_{}".format(argv[0]).replace("-", "_")

    # if command_fn is a valid Python function, pass it all remaining args
    if command_fn in globals() and callable(globals()[command_fn]):
        globals()[command_fn](argv[1:])
    else:
        print()
        print("{} is not a valid command".format(command))
        sys.exit(1)


def meza_command_deploy(argv):
    """
    Deploy the environment specified by the given argv.

    Args:
        argv (list): A list of command-line arguments.

    Returns:
        None
    """
    env = argv[0]

    rc = check_environment(env)

    lock_success = request_lock_for_deploy(env)

    if not lock_success:
        print("Deploy for environment {} in progress. Exiting".format(env))
        sys.exit(1)

    # return code != 0 means failure
    if rc != 0:
        if env == "monolith":
            meza_command_setup_env(env, True)
        else:
            sys.exit(rc)

    more_extra_vars = {}

    # strip environment off of it
    argv = argv[1:]

    # save state of args before stripping -o and --overwrite
    args_string = ' '.join(argv)

    # if argv[1:] includes -o or --overwrite
    if len(set(argv).intersection({"-o", "--overwrite"})) > 0:
        # remove -o and --overwrite from args;
        argv = [value for value in argv[:]
                if value not in ["-o", "--overwrite"]]

        more_extra_vars['force_overwrite_from_backup'] = True

    if len(set(argv).intersection({"--no-firewall"})) > 0:
        # remove --no-firewall from args:
        argv = [value for value in argv[:] if value not in ["--no-firewall"]]

        more_extra_vars['firewall_skip_tasks'] = True

    if len(more_extra_vars) == 0:
        more_extra_vars = False

    import hashlib
    start = get_datetime_string()
    unique = hashlib.sha1((start + env).encode('utf-8')).hexdigest()[:8]

    write_deploy_log(start, env, unique, 'start', args_string)

    shell_cmd = playbook_cmd('site', env, more_extra_vars)
    if len(argv) > 0:
        shell_cmd = shell_cmd + argv

    deploy_log = get_deploy_log_path(env)

    return_code = meza_shell_exec(shell_cmd, True, deploy_log)

    unlock_deploy(env)
    if return_code == 0:
        condition = 'complete'
    else:
        condition = 'failed'

    end = get_datetime_string()
    write_deploy_log(end, env, unique, condition, args_string)

    meza_shell_exec_exit(return_code)

#
# Intended to be used by cron job to check for changes to config and meza. Can
# also be called with `meza autodeploy <env>`
#


def meza_command_autodeploy(argv):
    """
    Perform an autodeploy for the specified environment.

    Args:
        argv (list): List of command line arguments.

    Returns:
        None
    """
    env = argv[0]

    rc = check_environment(env)

    lock_success = request_lock_for_deploy(env)

    if not lock_success:
        print("Deploy for environment {} in progress. Exiting".format(env))
        sys.exit(1)

    # return code != 0 means failure
    if rc != 0:
        sys.exit(rc)

    more_extra_vars = False

    # strip environment off of it
    argv = argv[1:]

    if len(argv) > 0:
        more_extra_vars = {
            'deploy_type': argv[0]
        }
        argv = argv[1:]  # strip deploy type off

    if len(argv) > 0:
        more_extra_vars['deploy_args'] = argv[0]
        argv = argv[1:]  # strip deploy args off

    shell_cmd = playbook_cmd('check-for-changes', env, more_extra_vars)
    if len(argv) > 0:
        shell_cmd = shell_cmd + argv

    return_code = meza_shell_exec(shell_cmd)

    unlock_deploy(env)  # double check

    meza_shell_exec_exit(return_code)

# Just a wrapper on deploy that does some notifications. This needs some
# improvement. FIXME. Lots of duplication between this and meza_command_deploy
# and meza_command_autodeploy


def meza_command_deploy_notify(argv):
    """
    Deploy the environment and notify the deployment status.

    Args:
        argv (list): List of command-line arguments.

    Returns:
        None
    """
    env = argv[0]

    rc = check_environment(env)

    lock_success = request_lock_for_deploy(env)

    if not lock_success:
        print("Deploy for environment {} in progress. Exiting".format(env))
        sys.exit(1)

    # return code != 0 means failure
    if rc != 0:
        sys.exit(rc)

    more_extra_vars = False

    # strip environment off of it
    argv = argv[1:]

    if len(argv) > 0:
        more_extra_vars = {
            'deploy_type': argv[0]
        }
        argv = argv[1:]  # strip deploy type off

    if len(argv) > 0:
        more_extra_vars['deploy_args'] = argv[0]
        argv = argv[1:]  # strip deploy args off

    shell_cmd = playbook_cmd('deploy-notify', env, more_extra_vars)
    if len(argv) > 0:
        shell_cmd = shell_cmd + argv

    return_code = meza_shell_exec(shell_cmd)

    unlock_deploy(env)  # double check

    meza_shell_exec_exit(return_code)


def request_lock_for_deploy(env):
    """
    Requests a lock for deployment.

    Args:
        env (str): The environment for which the lock is requested.

    Returns:
        dict or bool: If the lock is successfully created, returns a dictionary
        containing the process ID (pid) and timestamp of the lock creation.
        Otherwise, returns False.

    Raises:
        None

    """
    import os
    import datetime

    lock_file = get_lock_file_path(env)

    if os.path.isfile(lock_file):
        print("Deploy lock file already exists at {}".format(lock_file))
        return False
    else:
        print("Create deploy lock file at {}".format(lock_file))
        pid = str(os.getpid())
        timestamp = datetime.datetime.now().strftime("%Y-%m-%d_%H%M%S")

        # Before creating lock file, this global must be set in order for ctrl-c
        # interrupts (SIGINT) to be properly managed (SIGINT will call
        # sigint_handler function)
        global deploy_lock_environment
        deploy_lock_environment = env

        with open(lock_file, 'w') as f:
            f.write("{}\n{}".format(pid, timestamp))
            f.close()

        import grp

        try:
            grp.getgrnam('apache')
            meza_chown(lock_file, 'meza-ansible', 'apache')
        except KeyError:
            print(
                'Group apache does not exist. Using "wheel" as the group for our lock file.')
            meza_chown(lock_file, 'meza-ansible', 'wheel')

        os.chmod(lock_file, 0o664)

        return {"pid": pid, "timestamp": timestamp}


def unlock_deploy(env):
    """
    Removes the lock file for the specified environment.

    Args:
        env (str): The environment for which to unlock the deployment.

    Returns:
        bool: True if the lock file was successfully removed, False otherwise.
    """
    import os
    lock_file = get_lock_file_path(env)
    if os.path.exists(lock_file):
        os.remove(lock_file)
        return True
    else:
        return False


def get_lock_file_path(env):
    """
    Get the path of the lock file for the given environment.

    Args:
        env (str): The environment name.

    Returns:
        str: The path of the lock file.
    """
    import os
    lock_file = os.path.join(
        defaults['m_meza_data'], "env-{}-deploy.lock".format(env))
    return lock_file


def write_deploy_log(datetime, env, unique, condition, args_string):
    """
    Write deployment log to a file.

    Args:
        datetime (str): The timestamp of the deployment.
        env (str): The environment in which the deployment is performed.
        unique (str): A unique identifier for the deployment.
        condition (str): The condition of the deployment.
        args_string (str): A string representation of the deployment arguments.

    Returns:
        None
    """
    import subprocess
    import os

    deploy_log = defaults['m_logs_deploy']

    line = "{}\t{}\t{}\t{}\t{}\t{}\t{}\t{}\t{}\n".format(
        datetime,
        env,
        unique,
        condition,
        get_git_describe_tags("{}/meza".format(install_dir)),
        get_git_hash("{}/meza".format(install_dir)),
        get_git_hash("{}/conf-meza/secret".format(install_dir)),
        get_git_hash("{}/conf-meza/public".format(install_dir)),
        args_string
    )

    log_dir = os.path.dirname(os.path.realpath(deploy_log))

    if not os.path.isdir(log_dir):
        os.makedirs(log_dir)

    with open(deploy_log, "a") as myfile:
        myfile.write(line)

# "meza deploy-check <ENV>" to return 0 on no deploy, 1 on deploy is active


def meza_command_deploy_check(argv):
    """
    Check if a Meza environment is currently deploying.

    Args:
        argv (list): A list of command-line arguments. The first argument should be the environment name.

    Returns:
        None

    Raises:
        SystemExit: If the lock file exists, indicating that the environment is currently deploying.

    """
    import os
    env = argv[0]
    lock_file = get_lock_file_path(env)
    if os.path.isfile(lock_file):
        print("Meza environment '{}' deploying; {} exists".format(env, lock_file))
        sys.exit(1)
    else:
        print("Meza environment '{}' not deploying".format(env))
        sys.exit(0)


def meza_command_deploy_lock(argv):
    """
    Locks the specified environment for deployment.

    Args:
        argv (list): A list containing the command-line arguments. The first element should be the environment name.

    Returns:
        None
    """
    env = argv[0]
    success = request_lock_for_deploy(env)
    if success:
        print("Environment '{}' locked for deploy".format(env))
        sys.exit(0)
    else:
        print("Environment '{}' could not be locked".format(env))
        sys.exit(1)


def meza_command_deploy_unlock(argv):
    """
    Unlocks the deploy lock for the specified environment.

    Args:
        argv (list): A list containing the command-line arguments. The first element should be the environment name.

    Returns:
        None

    Raises:
        None
    """
    env = argv[0]
    success = unlock_deploy(env)
    if success:
        print("Environment '{}' deploy lock removed".format(env))
        sys.exit(0)
    else:
        print("Environment '{}' is not deploying".format(env))
        sys.exit(1)


def meza_command_deploy_kill(argv):
    """
    Terminate a Meza deployment process for the specified environment.

    Args:
        argv (list): A list of command-line arguments. The first argument should be the environment.

    Returns:
        None

    Raises:
        None
    """
    env = argv[0]
    lock_file = get_lock_file_path(env)
    if os.path.isfile(lock_file):
        print("Meza environment {} deploying; killing...".format(env))
        di = get_deploy_info(env)
        os.system("kill $(ps -o pid= --ppid {})".format(di['pid']))
        import time
        time.sleep(2)
        os.system(
            'wall "Meza deploy terminated using \'meza deploy-kill\' command."')
        sys.exit(0)
    else:
        print("Meza environment '{}' not deploying".format(env))
        sys.exit(1)


def get_deploy_info(env):
    """
    Retrieves deployment information for a given environment.

    Args:
        env (str): The name of the environment.

    Returns:
        dict: A dictionary containing the deployment information, including the process ID (pid) and timestamp.

    Raises:
        FileNotFoundError: If the lock file for the environment does not exist.

    """
    import os
    lock_file = get_lock_file_path(env)
    if not os.path.isfile(lock_file):
        raise FileNotFoundError("Environment '{}' not deploying".format(env))
    with open(lock_file, 'r') as f:
        pid = f.readline()
        timestamp = f.readline()
        f.close()
        return {"pid": pid, "timestamp": timestamp}


def get_deploy_log_path(env):
    """
    Get the path to the deploy log file for a given environment.

    Args:
        env (str): The environment name.

    Returns:
        str: The path to the deploy log file.

    """
    timestamp = get_deploy_info(env)["timestamp"]
    filename = "{}-{}.log".format(env, timestamp)

    log_dir = os.path.join(defaults['m_logs'], 'deploy-output')
    log_path = os.path.join(log_dir, filename)

    if not os.path.isdir(log_dir):
        os.makedirs(log_dir)

    return log_path


def meza_command_deploy_log(argv):
    """
    Get the deploy log path for the specified environment.

    Args:
        argv (list): A list containing the environment name.

    Returns:
        str: The path to the deploy log file.

    """
    env = argv[0]
    print(get_deploy_log_path(env))


def meza_command_deploy_tail(argv):
    """
    Display the tail of the deploy log for the specified environment.

    Args:
        argv (list): A list of command-line arguments. The first element should be the environment.

    Returns:
        None
    """
    env = argv[0]
    os.system(" ".join(["tail", "-f", get_deploy_log_path(env)]))


def get_git_hash(dir):
    """
    Get the git hash of a directory.

    Args:
        dir (str): The directory path.

    Returns:
        str: The git hash of the directory if it is a git repository, otherwise returns "not-a-git-repo".
    """
    import subprocess
    import os

    git_dir = "{}/.git".format(dir)

    if os.path.isdir(git_dir):
        try:
            commit = subprocess.check_output(
                ["git", "--git-dir={}".format(git_dir), "rev-parse", "HEAD"]).strip()
        except BaseException:
            commit = "git-error"
        return commit
    else:
        return "not-a-git-repo"


def get_git_describe_tags(dir):
    """
    Get the description of the latest Git tag in the specified directory.

    Args:
        dir (str): The directory path.

    Returns:
        str: The description of the latest Git tag if the directory is a Git repository,
             "git-error" if there was an error executing the Git command,
             or "not-a-git-repo" if the directory is not a Git repository.
    """
    import subprocess
    import os

    git_dir = "{}/.git".format(dir)

    if os.path.isdir(git_dir):
        try:
            tags = subprocess.check_output(
                ["git", "--git-dir={}".format(git_dir), "describe", "--tags", "--always"]).strip()
        except BaseException:
            tags = "git-error"
        return tags
    else:
        return "not-a-git-repo"

# env
# dev
# dev-networking --> vbox-networking ??
# docker


def meza_command_setup(argv):
    """
    Sets up the command for the Meza script.

    Args:
        argv (list): The list of command line arguments.

    Returns:
        None
    """
    sub_command = argv[0]
    if sub_command == "dev-networking":
        sub_command = "dev_networking"  # hyphen not a valid function character
    command_fn = "meza_command_setup_" + sub_command

    # if command_fn is a valid Python function, pass it all remaining args
    if command_fn in globals() and callable(globals()[command_fn]):
        globals()[command_fn](argv[1:])
    else:
        print()
        print(sub_command + " is not a valid sub-command for setup")
        sys.exit(1)


def meza_command_update(argv):
    """
    Update the Meza repository to a specific version or branch.

    Args:
        argv (list): List of command-line arguments.

    Returns:
        None

    Raises:
        None

    """
    import subprocess

    # This function executes many Git commands that need to be from /otp/meza
    os.chdir("/opt/meza")

    # Define a special git remote repository so we can control its settings
    # Else, a user using Vagrant may have their origin remote setup for SSH
    # but these commands need HTTPS.
    meza_remote = "mezaremote"

    check_remotes = subprocess.check_output(
        ["git", "remote"]).strip().split("\n")
    if meza_remote not in check_remotes:
        add_remote = subprocess.check_output(
            ["git", "remote", "add", meza_remote,
                "https://github.com/nasa/meza.git"]
        )

    # Get latest commits and tags from mezaremote
    fetch = subprocess.check_output(["git", "fetch", meza_remote])
    fetch = subprocess.check_output(["git", "fetch", meza_remote, "--tags"])
    tags_text = subprocess.check_output(["git", "tag", "-l"])

    if len(argv) == 0:
        # print fetch.strip()
        print("The following versions are available:")
        print(tags_text.strip())
        print("")
        closest_tag = subprocess.check_output(
            ["git", "describe", "--tags", "--always"])
        print("You are currently on version {}".format(closest_tag.strip()))
        print("To change versions, do 'sudo meza update <version>'")
    elif len(argv) > 1:
        print("Unknown argument {}".format(argv[1]))
    else:
        # Needed else 'git status' gives bad response
        status = subprocess.check_output(
            ["git", "status", "--untracked-files=no", "--porcelain"])
        status = status.strip()
        if status != "":
            print("'git status' not empty:\n{}".format(status))

        version = argv[0]
        if status == "":
            tags = tags_text.split("\n")
            branches = subprocess.check_output(
                ["git", "branch", "-a"]).strip().split("\n")
            branches = map(str.strip, branches)
            if version in tags:
                version_type = "at version"
                tag_version = "tags/{}".format(version)
                checkout = subprocess.check_output(
                    ["git", "checkout", tag_version], stderr=subprocess.STDOUT)
            elif version in branches or "* {}".format(version) in branches:
                version_type = "on branch"
                checkout = subprocess.check_output(
                    ["git", "checkout", version], stderr=subprocess.STDOUT)
                reset = subprocess.check_output(
                    ["git", "reset", "--hard", "mezaremote/{}".format(version)])
            elif "remotes/{}/{}".format(meza_remote, version) in branches:
                version_type = "on branch"
                checkout = subprocess.check_output(
                    ["git", "checkout", "-b", version, '-t',
                        "{}/{}".format(meza_remote, version)],
                    stderr=subprocess.STDOUT,
                )
            else:
                print("{} is not a valid version or branch".format(version))
                sys.exit(1)
            print("")
            print("")
            print("Meza now {} {}".format(version_type, version))
            print("Now deploy changes with 'sudo meza deploy <environment>'")
        else:
            print(
                "Files have been modified in /opt/meza. Clean them up before proceeding.")
            print("MSG: {}".format(status))

# FIXME #824: This function is big.


def meza_command_setup_env(argv, return_not_exit=False):
    """
    Sets up the environment for the Meza deployment.

    Args:
        argv (str or list): The command line arguments passed to the function.
            If a string is provided, it is treated as the environment name.
            If a list is provided, the first element is treated as the environment name.
        return_not_exit (bool, optional): If True, the function returns the exit code instead of exiting the program.
            Defaults to False.

    Returns:
        int or None: The exit code if `return_not_exit` is True, otherwise None.

    Raises:
        Exception: If an error occurs while parsing the command line arguments.

    """

    import json
    import string

    if isinstance(argv, str):
        env = argv
    else:
        env = argv[0]

    if not os.path.isdir("/opt/conf-meza"):
        os.mkdir("/opt/conf-meza")

    if not os.path.isdir("/opt/conf-meza/secret"):
        os.mkdir("/opt/conf-meza/secret")

    if os.path.isdir("/opt/conf-meza/secret/" + env):
        print("")
        print("Environment {} already exists".format(env))
        sys.exit(1)

    fqdn = db_pass = private_net_zone = False
    try:
        opts, args = getopt.getopt(
            argv[1:], "", ["fqdn=", "db_pass=", "private_net_zone="])
    except Exception as e:
        print(str(e))
        print('meza setup env <env> [options]')
        sys.exit(1)
    for opt, arg in opts:
        if opt == "--fqdn":
            fqdn = arg
        elif opt == "--db_pass":
            # This will put the DB password on the command line, so should
            # only be done in testing cases
            db_pass = arg
        elif opt == "--private_net_zone":
            private_net_zone = arg
        else:
            print("Unrecognized option " + opt)
            sys.exit(1)

    if not fqdn:
        fqdn = prompt("fqdn")

    if not db_pass:
        db_pass = prompt_secure("db_pass")

    # No need for private networking. Set to public.
    if env == "monolith":
        private_net_zone = "public"
    elif not private_net_zone:
        private_net_zone = prompt("private_net_zone")

    # Ansible environment variables
    env_vars = {
        'env': env,

        'fqdn': fqdn,
        'private_net_zone': private_net_zone,

        # Set all db passwords the same
        'mysql_root_pass': db_pass,
        'wiki_app_db_pass': db_pass,
        'db_slave_pass': db_pass,

        # Generate a random secret key
        'wg_secret_key': random_string(num_chars=64, valid_chars=string.ascii_letters + string.digits)

    }

    server_types = [
        'load_balancers',
        'app_servers',
        'memcached_servers',
        'db_slaves',
        'elastic_servers',
        'backup_servers',
        'logging_servers']

    for stype in server_types:
        if stype in os.environ:
            env_vars[stype] = [x.strip() for x in os.environ[stype].split(',')]
        elif stype == "db_slaves":
            # unless db_slaves are explicitly set, don't configure any
            env_vars["db_slaves"] = []
        elif "default_servers" in os.environ:
            env_vars[stype] = [x.strip()
                               for x in os.environ["default_servers"].split(',')]
        else:
            env_vars[stype] = ['localhost']

    if "db_master" in os.environ:
        env_vars["db_master"] = os.environ["db_master"].strip()
    elif "default_servers" in os.environ:
        env_vars["db_master"] = os.environ["default_servers"].strip()
    else:
        env_vars["db_master"] = 'localhost'

    json_env_vars = json.dumps(env_vars)

    # Create temporary extra vars file in secret directory so passwords
    # are not written to command line. Putting in secret should make
    # permissions acceptable since this dir will hold secret info, though it's
    # sort of an odd place for a temporary file. Perhaps /root instead?
    extra_vars_file = os.path.join(
        defaults['m_local_secret'], "temp_vars.json")
    if os.path.isfile(extra_vars_file):
        os.remove(extra_vars_file)
    f = open(extra_vars_file, 'w')
    f.write(json_env_vars)
    f.close()

    # Make sure temp_vars.json is accessible. On the first run of deploy it is
    # possible that user meza-ansible will not be able to reach this file,
    # specifically if the system has a restrictive umask set (e.g 077).
    meza_chown(defaults['m_local_secret'], 'meza-ansible', 'wheel')
    meza_chown(extra_vars_file, 'meza-ansible', 'wheel')
    os.chmod(extra_vars_file, 0o664)

    shell_cmd = playbook_cmd("setup-env") + \
        ["--extra-vars", '@' + extra_vars_file]
    rc = meza_shell_exec(shell_cmd)

    os.remove(extra_vars_file)

    print("")
    print("Please review your host file. Run command:")
    print("  sudo vi /opt/conf-meza/secret/{}/hosts".format(env))
    print("Please review your secret config. Run command:")
    print("  sudo vi /opt/conf-meza/secret/{}/secret.yml".format(env))
    if return_not_exit:
        return rc
    else:
        sys.exit(rc)


def meza_command_setup_dev(argv):
    """
    Set up development environment for Meza.

    This function prompts for dev_users, dev_git_user, and dev_git_user_email.
    It then configures git settings for each dev_user, installs and configures vsftpd,
    and sets up firewall rules. Finally, it prints a message with instructions to
    setup SFTP in Sublime Text and exits the script.

    Args:
        argv: The command line arguments passed to the script.

    Returns:
        None
    """
    dev_users = prompt("dev_users")
    dev_git_user = prompt("dev_git_user")
    dev_git_user_email = prompt("dev_git_user_email")

    for dev_user in dev_users.split(' '):
        os.system(
            "sudo -u {} git config --global user.name '{}'".format(dev_user, dev_git_user))
        os.system(
            "sudo -u {} git config --global user.email {}".format(dev_user, dev_git_user_email))
        os.system(
            "sudo -u {} git config --global color.ui true".format(dev_user))

    # ref:
    # https://www.liquidweb.com/kb/how-to-install-and-configure-vsftpd-on-centos-7/
    os.system("yum -y install vsftpd")
    os.system(
        "sed -r -i 's/anonymous_enable=YES/anonymous_enable=NO/g;' /etc/vsftpd/vsftpd.conf")
    os.system(
        "sed -r -i 's/local_enable=NO/local_enable=YES/g;' /etc/vsftpd/vsftpd.conf")
    os.system(
        "sed -r -i 's/write_enable=NO/write_enable=YES/g;' /etc/vsftpd/vsftpd.conf")

    # Start FTP and setup firewall
    os.system("systemctl restart vsftpd")
    os.system("systemctl enable vsftpd")
    os.system("firewall-cmd --permanent --add-port=21/tcp")
    os.system("firewall-cmd --reload")

    print("To setup SFTP in Sublime Text, see:")
    print("https://wbond.net/sublime_packages/sftp/settings#Remote_Server_Settings")
    sys.exit()

# Remove in 32.x


def meza_command_setup_dev_networking(argv):
    print("Function removed. Instead do:")
    print("  sudo bash /opt/meza/src/scripts/dev-networking.sh")
    sys.exit(1)

# @FIXME this is obsolete (because it's for CentOS) but should be updated


def meza_command_setup_docker(argv):
    """
    Set up Docker for the Meza command.

    Args:
        argv (list): The command line arguments.

    Returns:
        None
    """
    shell_cmd = playbook_cmd("getdocker")
    rc = meza_shell_exec(shell_cmd)
    sys.exit(0)


def meza_command_create(argv):
    """
    Create a wiki using prompts else use wiki-promptless. So the 'form' of the
    command is 'meza create wiki' or 'meza create wiki-promptless'.

    Environment must be specified as the second argument.

    If it is 'promptless', the third and fourth arguments must be the wiki_id
    and wiki_name, respectively.

    Args:
        argv (list): The command line arguments passed to the function.

    Returns:
        None

    Raises:
        SystemExit: If the required arguments are not provided.

    """
    sub_command = argv[0]

    if sub_command in ("wiki", "wiki-promptless"):

        if len(argv) < 2:
            print("You must specify an environment: 'meza create wiki ENV'")
            sys.exit(1)

        env = argv[1]

        rc = check_environment(env)
        if rc > 0:
            meza_shell_exec_exit(rc)

        playbook = "create-" + sub_command

        if sub_command == "wiki-promptless":
            if len(argv) < 4:
                print("create wiki-promptless requires wiki_id and wiki_name arguments")
                sys.exit(1)
            shell_cmd = playbook_cmd(
                playbook, env, {'wiki_id': argv[2], 'wiki_name': argv[3]})
        else:
            shell_cmd = playbook_cmd(playbook, env)

        rc = meza_shell_exec(shell_cmd)
        meza_shell_exec_exit(rc)


def meza_command_delete(argv):
    """
    Delete a specific component in the Meza environment.

    Args:
        argv (list): List of command-line arguments.

    Returns:
        None

    Raises:
        SystemExit: If the sub-command is not valid or if the environment is not specified.

    """

    sub_command = argv[0]

    if sub_command not in ("wiki", "wiki-promptless", "elasticsearch"):
        print("{} is not a valid sub-command for delete".format(sub_command))
        sys.exit(1)

    if len(argv) < 2:
        print(
            "You must specify an environment: 'meza delete {} ENV'".format(sub_command))
        sys.exit(1)

    env = argv[1]

    rc = check_environment(env)
    if rc > 0:
        meza_shell_exec_exit(rc)

    playbook = "delete-" + sub_command

    if sub_command == "wiki-promptless":
        if len(argv) < 3:
            print("delete wiki-promptless requires wiki_id")
            sys.exit(1)
        shell_cmd = playbook_cmd(playbook, env, {'wiki_id': argv[2]})
    else:
        shell_cmd = playbook_cmd(playbook, env)

    rc = meza_shell_exec(shell_cmd)
    meza_shell_exec_exit(rc)


def meza_command_backup(argv):
    """
    Perform a backup operation for the specified environment.

    Args:
        argv (list): A list of command-line arguments.

    Returns:
        None
    """

    env = argv[0]

    rc = check_environment(env)
    if rc != 0:
        meza_shell_exec_exit(rc)

    shell_cmd = playbook_cmd('backup', env) + argv[1:]
    rc = meza_shell_exec(shell_cmd)

    meza_shell_exec_exit(rc)


def meza_command_setbaseconfig(argv):
    """
    Executes the 'setbaseconfig' playbook command for the specified environment.

    Args:
        argv (list): A list of command-line arguments passed to the function. The first element should be the environment.

    Returns:
        None
    """
    env = argv[0]

    rc = check_environment(env)
    if rc != 0:
        meza_shell_exec_exit(rc)

    shell_cmd = playbook_cmd('setbaseconfig', env) + argv[1:]
    rc = meza_shell_exec(shell_cmd)

    meza_shell_exec_exit(rc)


def meza_command_destroy(argv):
    print("command not yet built")


# FIXME #825: It would be great to have this function automatically map all
#             scripts in MediaWiki's maintenance directory to all wikis. Then
#             you could do:
#   $ meza maint runJobs + argv            --> run jobs on all wikis
#   $ meza maint createAndPromote + argv   --> create a user on all wikis
def meza_command_maint(argv):
    """
    Executes the specified maintenance sub-command.

    Args:
        argv (list): List of command-line arguments.

    Returns:
        None

    Raises:
        SystemExit: If the specified sub-command is not valid.

    """
    # FIXME #711: This has no notion of environments and won't work in polylith

    sub_command = argv[0]
    command_fn = "meza_command_maint_" + sub_command

    # if command_fn is a valid Python function, pass it all remaining args
    if command_fn in globals() and callable(globals()[command_fn]):
        globals()[command_fn](argv[1:])
    else:
        print("")
        print(sub_command + " is not a valid sub-command for maint")
        sys.exit(1)


def meza_command_maint_runJobs(argv):
    """
    Run maintenance jobs for available wikis.

    This function runs maintenance jobs for the available wikis in the `wikis_dir`.
    It selects the first wiki found and executes the `runAllJobs.php` script using
    the `php` command. If a specific wiki is provided as a command-line argument,
    the function runs maintenance jobs only for that wiki.

    Args:
        argv (list): List of command-line arguments.

    Returns:
        None
    """
    # FIXME #711: THIS FUNCTION SHOULD STILL WORK ON MONOLITHS, BUT HAS NOT BE
    #             RE-TESTED SINCE MOVING TO ANSIBLE. FOR NON-MONOLITHS IT WILL
    #             NOT WORK AND NEEDS TO BE ANSIBLE-IZED.

    wikis_dir = "{}/htdocs/wikis".format(install_dir)
    wikis = os.listdir(wikis_dir)
    for i in wikis:
        if os.path.isdir(os.path.join(wikis_dir, i)):
            anywiki = i
            break

    if not anywiki:
        print("No wikis available to run jobs")
        sys.exit(1)

    shell_cmd = ["WIKI=" + anywiki, "php",
                 "{}/meza/src/scripts/runAllJobs.php".format(install_dir)]
    if len(argv) > 0:
        shell_cmd = shell_cmd + ["--wikis=" + argv[1]]
    rc = meza_shell_exec(shell_cmd)

    meza_shell_exec_exit(rc)


def meza_command_maint_rebuild(argv):
    """
    Rebuilds the specified environment and executes the 'rebuild-smw-and-index' playbook command.

    Args:
        argv (list): A list of command-line arguments passed to the function. The first argument is the environment.

    Returns:
        None
    """
    env = argv[0]

    rc = check_environment(env)

    # return code != 0 means failure
    if rc != 0:
        meza_shell_exec_exit(rc)

    more_extra_vars = False

    # strip environment off of it
    argv = argv[1:]

    shell_cmd = playbook_cmd('rebuild-smw-and-index', env, more_extra_vars)
    if len(argv) > 0:
        shell_cmd = shell_cmd + argv

    rc = meza_shell_exec(shell_cmd)

    # exit with same return code as ansible command
    meza_shell_exec_exit(rc)


def meza_command_maint_cleanuploadstash(argv):
    """
    Clean up the upload stash directory for a given environment.

    Args:
        argv (list): List of command-line arguments.

    Returns:
        None
    """

    env = argv[0]

    rc = check_environment(env)

    # return code != 0 means failure
    if rc != 0:
        meza_shell_exec_exit(rc)

    more_extra_vars = False

    # strip environment off of it
    argv = argv[1:]

    shell_cmd = playbook_cmd('cleanup-upload-stash', env, more_extra_vars)
    if len(argv) > 0:
        shell_cmd = shell_cmd + argv

    rc = meza_shell_exec(shell_cmd)

    # exit with same return code as ansible command
    meza_shell_exec_exit(rc)


def meza_command_maint_encrypt_string(argv):
    """
    Encrypts a string using Ansible Vault.

    Args:
        argv (list): List of command-line arguments.

    Returns:
        None

    Raises:
        SystemExit: If the function is called without the required arguments.

    Example:
        encrypt_string requires value to encrypt. Ex:
          sudo meza maint encrypt_string <env> somesecretvalue
        Additionally, you can supply the variable name. Ex:
          sudo meza maint encrypt_string <env> somesecretvalue var_name
    """
    env = argv[0]

    rc = check_environment(env)

    # return code != 0 means failure
    if rc != 0:
        meza_shell_exec_exit(rc)

    # strip environment off of it
    argv = argv[1:]

    if len(argv) == 0:
        print("encrypt_string requires value to encrypt. Ex:")
        print("  sudo meza maint encrypt_string <env> somesecretvalue")
        print("Additionally, you can supply the variable name. Ex:")
        print("  sudo meza maint encrypt_string <env> somesecretvalue var_name")
        sys.exit(1)

    varvalue = argv[0]
    vault_pass_file = get_vault_pass_file(env)

    shell_cmd = ["ansible-vault", "encrypt_string",
                 "--vault-id", vault_pass_file, varvalue]

    # If name argument passed in, use it
    if len(argv) == 2:
        shell_cmd = shell_cmd + ["--name", argv[1]]

    # false = don't print command prior to running
    rc = meza_shell_exec(shell_cmd, False)

    # exit with same return code as ansible command
    meza_shell_exec_exit(rc)


# sudo meza maint decrypt_string <env> <encrypted_string>
def meza_command_maint_decrypt_string(argv):
    """
    Decrypts an encrypted string using Ansible Vault.

    Args:
        argv (list): List of command-line arguments.

    Returns:
        None

    Raises:
        SystemExit: If no encrypted string is provided as an argument.

    Example:
        To decrypt a string, run the following command:
        sudo meza maint decrypt_string <env> '$ANSIBLE_VAULT;1.1;AES256
        31386561343430626435373766393066373464656262383063303630623032616238383838346132
        6162313461666439346337616166396133616466363935360a373333313165343535373761333634
        62636634306632633539306436363866323639363332613363346663613235653138373837303337
        6133383864613430370a623661653462336565376565346638646238643132636663383761613966
        6566'
    """
    env = argv[0]

    rc = check_environment(env)

    # return code != 0 means failure
    if rc != 0:
        meza_shell_exec_exit(rc)

    # strip environment off of it
    argv = argv[1:]

    if len(argv) == 0:
        print("decrypt_string requires you to supply encrypted string. Ex:")
        print("""
        sudo meza maint decrypt_string <env> '$ANSIBLE_VAULT;1.1;AES256
        31386561343430626435373766393066373464656262383063303630623032616238383838346132
        6162313461666439346337616166396133616466363935360a373333313165343535373761333634
        62636634306632633539306436363866323639363332613363346663613235653138373837303337
        6133383864613430370a623661653462336565376565346638646238643132636663383761613966
        6566'
        """)
        sys.exit(1)

    encrypted_string = argv[0]
    vault_pass_file = get_vault_pass_file(env)

    tmp_file = write_vault_decryption_tmp_file(env, encrypted_string)

    shell_cmd = ["ansible-vault", "decrypt", tmp_file,
                 "--vault-password-file", vault_pass_file]

    # false = don't print command prior to running
    rc = meza_shell_exec(shell_cmd, False)

    decrypted_value = read_vault_decryption_tmp_file(env)

    print("")
    print("Decrypted value:")
    print(decrypted_value)

    # exit with same return code as ansible command
    meza_shell_exec_exit(rc)

# @FIXME this is obsolete (because all Docker functionality is untested)
# Should be updated or removed - especially the hard-link to
# enterprisemediawiki


def meza_command_docker(argv):
    """
    Executes Docker commands based on the provided arguments.

    Args:
        argv (list): A list of command-line arguments.

    Raises:
        SystemExit: If the provided command is not valid or if required arguments are missing.

    Returns:
        None
    """

    if argv[0] == "run":

        if len(argv) == 1:
            docker_repo = "enterprisemediawiki/meza:max"
        else:
            docker_repo = argv[1]

        rc = meza_shell_exec(
            ["bash", "{}/meza/src/scripts/build-docker-container.sh".format(install_dir), docker_repo])
        meza_shell_exec_exit(rc)

    elif argv[0] == "exec":

        if len(argv) < 2:
            print("Please provide docker container id")
            meza_shell_exec(["docker", "ps"])
            sys.exit(1)
        else:
            container_id = argv[1]

        if len(argv) < 3:
            print("Please supply a command for your container")
            sys.exit(1)

        shell_cmd = ["docker", "exec", "--tty",
                     container_id, "env", "TERM=xterm"] + argv[2:]
        rc = meza_shell_exec(shell_cmd)

    else:
        print(argv[0] + " is not a valid command")
        sys.exit(1)


def meza_command_push_backup(argv):
    """
    Pushes the backup for the specified environment.

    Args:
        argv (list): A list of command-line arguments.

    Returns:
        None
    """
    env = argv[0]

    rc = check_environment(env)
    if rc != 0:
        meza_shell_exec_exit(rc)

    shell_cmd = playbook_cmd('push-backup', env) + argv[1:]
    rc = meza_shell_exec(shell_cmd)

    meza_shell_exec_exit(rc)


def playbook_cmd(playbook, env=False, more_extra_vars=False):
    """
    Constructs the command to execute an Ansible playbook.

    Args:
        playbook (str): The name of the playbook to execute.
        env (str, optional): The environment for the playbook execution. Defaults to False.
        more_extra_vars (dict, optional): Additional extra variables to pass to the playbook. Defaults to False.

    Returns:
        list: The constructed command as a list of strings.
    """
    command = ['sudo', '-u', 'meza-ansible', 'ansible-playbook',
               '{}/meza/src/playbooks/{}.yml'.format(install_dir, playbook)]
    if env:
        host_file = "{}/conf-meza/secret/{}/hosts".format(install_dir, env)

        # Meza _needs_ to be able to load this file. Be perhaps a little
        # overzealous and chown/chmod it everytime
        secret_file = '{}/conf-meza/secret/{}/secret.yml'.format(
            install_dir, env)
        meza_chown(secret_file, 'meza-ansible', 'wheel')
        os.chmod(secret_file, 0o660)

        # Setup password file if not exists (environment info is potentially
        # encrypted)
        vault_pass_file = get_vault_pass_file(env)

        command = command + ['-i', host_file,
                             '--vault-password-file', vault_pass_file]
        extra_vars = {'env': env}

    else:
        extra_vars = {}

    if more_extra_vars:
        for varname, value in more_extra_vars.items():
            extra_vars[varname] = value

    if len(extra_vars) > 0:
        import json
        command = command + \
            ["--extra-vars",
                "'{}'".format(json.dumps(extra_vars)).replace('"', '\\"')]

    return command

# FIXME install --> setup dev-networking, setup docker, deploy monolith
# (special case)


def meza_shell_exec(shell_cmd, print_command=True, log_file=False):
    """
    Executes a shell command and returns the return code.

    Args:
        shell_cmd (list): The shell command to execute, as a list of strings.
        print_command (bool, optional): Whether to print the command before executing it. Defaults to True.
        log_file (str, optional): The path to the log file to write the command output to. Defaults to False.

    Returns:
        int: The return code of the executed shell command.
    """
    # Get errors with user meza-ansible trying to write to the calling-user's
    # home directory if don't cd to a neutral location. By cd'ing to this
    # location you can pick up ansible.cfg and use vars there.
    starting_wd = os.getcwd()
    os.chdir("{}/meza/config".format(install_dir))

    #
    # FIXME #874: For some reason `sudo -u meza-ansible ...` started failing in
    #             fall 2017. Using `su meza-ansible -c "..."` works. It is not
    #             known why this started happening, but a fix was needed. This,
    #             despite being somewhat of a hack, seemed like the best way to
    #             address the issue at the time.
    #
    firstargs = ' '.join(shell_cmd[0:3])
    if firstargs == "sudo -u meza-ansible":
        cmd = "su meza-ansible -c \"{}\"".format(' '.join(shell_cmd[3:]))
    else:
        cmd = ' '.join(shell_cmd)

    if print_command:
        print(cmd)

    import subprocess

    if log_file:
        log = open(log_file, 'ab')
    proc = subprocess.Popen(
        cmd, shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
    for line in iter(proc.stdout.readline, b''):
        print(line.rstrip().decode())
        if log_file:
            log.write(line)
    proc.wait()

    rc = proc.returncode

    # Move back to original working directory
    os.chdir(starting_wd)

    return rc

# Return codes from function meza_shell_exec may either not be numbers or they
# may be out of the range accepted by sys.exit(). For example, return codes in
# the 30000 range were not being interpretted as failures. This function will
# instead take any non-zero return code and make it return the integer 1.


def meza_shell_exec_exit(return_code=0):
    """
    Exits the script with the given return code.

    Args:
        return_code (int): The return code to exit with.

    Returns:
        None
    """
    if int(return_code) > 0:
        print("Exiting with return code {}".format(return_code))
        sys.exit(1)
    else:
        sys.exit(0)


def get_vault_pass_file(env):
    """
    Get the path to the vault password file for the given environment.

    Args:
        env (str): The environment for which the vault password file is needed.

    Returns:
        str: The path to the vault password file.

    Raises:
        None

    """
    import pwd
    import grp

    home_dir = defaults['m_home']
    legacy_file = '{}/meza-ansible/.vault-pass-{}.txt'.format(home_dir, env)

    vault_dir = defaults['m_config_vault']
    vault_pass_file = '{}/vault-pass-{}.txt'.format(vault_dir, env)

    if not os.path.isfile(vault_pass_file):
        if not os.path.exists(vault_dir):
            os.mkdir(vault_dir)
            meza_chown(vault_dir, 'meza-ansible', 'wheel')
            os.chmod(vault_dir, 0o700)

        # If legacy vault password file exists copy that into new location.
        # Otherwise, create one in the new location
        if os.path.isfile(legacy_file):
            from shutil import copyfile
            copyfile(legacy_file, vault_pass_file)
        else:
            with open(vault_pass_file, 'w') as f:
                f.write(random_string(num_chars=64))
                f.close()

    # Run this everytime, since it should be fast and if meza-ansible can't
    # read this then you're stuck!
    meza_chown(vault_pass_file, 'meza-ansible', 'wheel')
    os.chmod(vault_pass_file, 0o600)

    return vault_pass_file


def write_vault_decryption_tmp_file(env, value):
    """
    Write the given value to a temporary file for vault decryption.

    Args:
        env (str): The environment name.
        value (str): The value to be written to the file.

    Returns:
        str: The path of the temporary file.

    """
    home_dir = defaults['m_home']
    temp_decrypt_file = '{}/meza-ansible/.vault-temp-decrypt-{}.txt'.format(
        home_dir, env)

    with open(temp_decrypt_file, 'w') as filetowrite:
        filetowrite.write(value)

    return temp_decrypt_file


def read_vault_decryption_tmp_file(env):
    """
    Read the contents of a temporary decryption file.

    Args:
        env (str): The environment name.

    Returns:
        str: The contents of the temporary decryption file, or "[decryption error]" if an error occurs.
    """
    home_dir = defaults['m_home']
    temp_decrypt_file = '{}/meza-ansible/.vault-temp-decrypt-{}.txt'.format(
        home_dir, env)

    f = open(temp_decrypt_file, "r")
    if f.mode == 'r':
        contents = f.read()
        f.close()
        os.remove(temp_decrypt_file)
    else:
        contents = "[decryption error]"

    return contents


def meza_chown(path, username, groupname):
    """
    Change the ownership of a file or directory to the specified user and group.

    Args:
        path (str): The path to the file or directory.
        username (str): The name of the user.
        groupname (str): The name of the group.

    Returns:
        None
    """
    import pwd
    import grp
    uid = pwd.getpwnam(username).pw_uid
    gid = grp.getgrnam(groupname).gr_gid
    os.chown(path, uid, gid)


def display_docs(name):
    """
    Display the contents of a text file with the given name.

    Args:
        name (str): The name of the text file to display.

    Returns:
        None
    """
    f = open('/opt/meza/manual/meza-cmd/{}.txt'.format(name), 'r')
    print(f.read())


def prompt(varname, default=False):
    """
    Prompts the user for input and returns the value entered.

    Args:
        varname (str): The name of the variable being prompted for.
        default (bool, optional): The default value to use if the user does not provide any input. Defaults to False.

    Returns:
        str: The value entered by the user.

    """
    # Pretext message is prior to the actual line the user types on. Input msg
    # is on the same line and will be repeated if the user does not give good
    # input
    pretext_msg = i18n["MSG_prompt_pretext_" + varname]
    input_msg = i18n["MSG_prompt_input_" + varname]

    print("")
    print(pretext_msg)

    value = input(input_msg)
    if default:
        # If there's a default, either use user entry or default
        value = value or default
    else:
        # If no default, keep asking until user supplies a value
        while (not value):
            value = input(input_msg)

    return value


def prompt_secure(varname):
    """
    Prompts the user to enter a secure value for the given variable name.

    Args:
        varname (str): The name of the variable for which the user is prompted.

    Returns:
        str: The secure value entered by the user.

    """
    import getpass

    # See prompt() for more info
    pretext_msg = i18n["MSG_prompt_pretext_" + varname]
    input_msg = i18n["MSG_prompt_input_" + varname]

    print()
    print(pretext_msg)

    value = getpass.getpass(input_msg)
    if not value:
        value = random_string()

    return value


def random_string(**params):
    """
    Generate a random string.

    Args:
        params (dict): Optional parameters for generating the random string.
            - num_chars (int): The length of the random string. Default is 32.
            - valid_chars (str): The characters to choose from when generating the random string.
              Default is all uppercase and lowercase letters, digits, and the special characters '!@$%^*'.

    Returns:
        str: The randomly generated string.

    """
    import string
    import random

    if 'num_chars' in params:
        num_chars = params['num_chars']
    else:
        num_chars = 32

    if 'valid_chars' in params:
        valid_chars = params['valid_chars']
    else:
        valid_chars = string.ascii_letters + string.digits + '!@$%^*'

    return ''.join(random.SystemRandom().choice(valid_chars)
                   for _ in range(num_chars))


# return code 0 success, 1+ failure
def check_environment(env):
    """
    Check if the specified environment exists and is valid.

    Args:
        env (str): The name of the environment to check.

    Returns:
        int: Returns 0 if the environment is valid, 1 otherwise.
    """
    import os

    conf_dir = "{}/conf-meza/secret".format(install_dir)

    env_dir = os.path.join(conf_dir, env)
    if not os.path.isdir(env_dir):

        if env == "monolith":
            return 1

        print()
        print('"{}" is not a valid environment.'.format(env))
        print("Please choose one of the following:")

        conf_dir_stuff = os.listdir(conf_dir)
        valid_envs = []
        for x in conf_dir_stuff:
            if os.path.isdir(os.path.join(conf_dir, x)):
                valid_envs.append(x)

        if len(valid_envs) > 0:
            for x in valid_envs:
                print("  " + x)
        else:
            print("  No environments configured")
            print("  Run command: meza setup env <environment name>")

        return 1

    host_file = os.path.join(env_dir, "hosts")
    if not os.path.isfile(host_file):
        print()
        print("{} not a valid file".format(host_file))
        return 1

    return 0

# http://stackoverflow.com/questions/1994488/copy-file-or-directories-recursively-in-python


def copy(src, dst):
    """
    Copy a file or directory from source to destination.

    Args:
        src (str): The path of the source file or directory.
        dst (str): The path of the destination directory.

    Raises:
        OSError: If an error occurs while copying the file or directory.

    Returns:
        None
    """
    import shutil
    import errno

    try:
        shutil.copytree(src, dst)
    except OSError as exc:
        if exc.errno == errno.ENOTDIR:
            shutil.copy(src, dst)
        else:
            raise


def get_datetime_string():
    """
    Returns the current date and time as a formatted string.

    Returns:
        str: The current date and time in the format 'YYYY-MM-DD HH:MM:SS'.
    """
    import time
    import datetime
    ts = time.time()
    st = datetime.datetime.fromtimestamp(ts).strftime('%Y-%m-%d %H:%M:%S')
    return st


if __name__ == "__main__":
    main(sys.argv[1:])
