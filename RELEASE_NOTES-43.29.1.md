origin/bug/191-maintenance-scripts bug/191-maintenance-scripts
* [d6310e8e](https://github.com/freephile/meza/commit/d6310e8e) (2025-08-25) Greg Rundlett: Add comment about Issue [#72](https://github.com/freephile/meza/issues/72) (deploy without sudo) 

M	src/scripts/getmeza.sh
* [9802d43d](https://github.com/freephile/meza/commit/9802d43d) (2025-08-25) Greg Rundlett: Fix maintenance script quoting For Issue [#191](https://github.com/freephile/meza/issues/191)


M	src/roles/mediawiki/templates/refresh-links.sh.j2
M	src/roles/verify-wiki/tasks/init-wiki.yml
* [06c2b554](https://github.com/freephile/meza/commit/06c2b554) (2025-08-24) Greg Rundlett: Fix maintenance script quoting + best practices - Use FQCN
- Improve spacing
- Name all tasks
- Remove quotes from task names
- Improve block order to put tags first

For Issue [#191](https://github.com/freephile/meza/issues/191)


M	src/roles/update.php/tasks/main.yml
* [db3fb090](https://github.com/freephile/meza/commit/db3fb090) (2025-08-24) Greg Rundlett: Fix maintenance script quoting Here we fix templated shell scripts
For Issue [#191](https://github.com/freephile/meza/issues/191)


M	src/roles/cron/templates/runAllJobs.php.j2
M	src/roles/mediawiki/templates/elastic-build-index.sh.j2
M	src/roles/mediawiki/templates/smw-rebuild-all.sh.j2
M	src/roles/meza-log/templates/server-performance.sh.j2
* [88a74acb](https://github.com/freephile/meza/commit/88a74acb) (2025-08-24) Greg Rundlett: Fix maintenance script quoting Issue [#191](https://github.com/freephile/meza/issues/191)


M	src/playbooks/cleanup-upload-stash.yml
M	src/roles/mediawiki/tasks/cirrus_metastore_upgrade.yml
* [61b7e63b](https://github.com/freephile/meza/commit/61b7e63b) (2025-08-24) Greg Rundlett: Fixing maintenance script quoting Issue [#191](https://github.com/freephile/meza/issues/191)


M	src/scripts/unite-the-wikis.sh
* [e36b8067](https://github.com/freephile/meza/commit/e36b8067) (2025-08-24) Greg Rundlett: Remove commented task CirrusSearch:Metastore --upgrade Fixed in d0a1ecb3 with new cirrus_metastore_upgrade.yml task

--no-verify used in commit bc we still have
69 failure(s), 0 warning(s) on 2 files.


M	src/roles/mediawiki/tasks/main.yml

## Meza 43.28.4 use-ansible-best-practices
* [a7bec3ef](https://github.com/freephile/meza/commit/a7bec3ef) (2025-08-22) Greg Rundlett: Fix most Ansible fatal linting errors; Add requirements.yml - Fixes Issue [#187](https://github.com/freephile/meza/issues/187)
- Fixes Issue [#46](https://github.com/freephile/meza/issues/46)
- Related to Issue [#47](https://github.com/freephile/meza/issues/47)
- Fixes Issue [#90](https://github.com/freephile/meza/issues/90) with requirements.yml - and CentOS is obsolete, so see #15


M	requirements.yml
M	src/playbooks/site.yml
M	src/roles/ansible-role-certbot-meza/tasks/create-cert-standalone.yml
M	src/roles/ansible-role-certbot-meza/tasks/install-from-source.yml
M	src/roles/apache-php/tasks/main.yml
M	src/roles/apache-php/tasks/mssql_driver_for_php.yml
M	src/roles/apache-php/tasks/php-redhat8.yml
M	src/roles/apache-php/tasks/profiling.yml
M	src/roles/backup-uploads/tasks/main.yml
M	src/roles/base-config-scripts/tasks/main.yml
M	src/roles/base/tasks/main.yml
M	src/roles/composer/tasks/global-require.yml
M	src/roles/composer/tasks/main.yml
M	src/roles/configure-wiki/tasks/main.yml
M	src/roles/cron/tasks/main.yml
M	src/roles/database/tasks/configure.yml
M	src/roles/database/tasks/databases.yml
M	src/roles/database/tasks/replication.yml
M	src/roles/database/tasks/secure-installation.yml
M	src/roles/delete-wiki-wrapper/tasks/main.yml
M	src/roles/dump-db-wikis/tasks/main.yml
M	src/roles/elasticsearch/tasks/es_reindex.yml
M	src/roles/elasticsearch/tasks/main.yml
M	src/roles/geerlingguy.kibana/tasks/main.yml
M	src/roles/geerlingguy.kibana/tasks/setup-RedHat.yml
M	src/roles/gluster/tasks/main.yml
M	src/roles/haproxy/tasks/main.yml
M	src/roles/htdocs/tasks/main.yml
M	src/roles/lua/tasks/main.yml
M	src/roles/mediawiki/tasks/main.yml
M	src/roles/meza-log/tasks/main.yml
M	src/roles/saml/tasks/main.yml
M	src/roles/sync-configs/tasks/main.yml
M	src/roles/umask-set/tasks/main.yml
M	src/roles/verify-wiki/tasks/import-wiki-sql.yml
M	src/roles/verify-wiki/tasks/main.yml
M	tests/deploys/setup-alt-source-backup.yml

## Meza 43.28.3
* [6749c666](https://github.com/freephile/meza/commit/6749c666) (2025-08-22) Greg Rundlett: Adopt standard .yamlint for compatibility The default configuration works better with ansible-lint in our CI
because 'Prettier' is the most popular formatter in Python
https://ansible.readthedocs.io/projects/lint/rules/yaml/#yamllint-configuration

Issue [#46](https://github.com/freephile/meza/issues/46)


M	.yamllint

## Meza 43.28.2
* [9c8132e9](https://github.com/freephile/meza/commit/9c8132e9) (2025-08-22) Greg Rundlett: Fix Fully Qualified Collection Name (FQCN) best practices Just for these files:

- src/roles/base/tasks/main.yml
- src/roles/base/tasks/parsoid-cleanup.yml
- src/roles/mediawiki/defaults/main.yml
- src/roles/setup-env/tasks/main.yml

pertains to Issue [#46](https://github.com/freephile/meza/issues/46)


M	src/roles/base/tasks/main.yml
M	src/roles/base/tasks/parsoid-cleanup.yml
M	src/roles/mediawiki/defaults/main.yml
M	src/roles/setup-env/tasks/main.yml

## Meza 43.28.1
* [68c45e66](https://github.com/freephile/meza/commit/68c45e66) (2025-08-22) Greg Rundlett: Default to ansible_env.USER for ansible_user When all plays are executed by the 'Application User' (meza-ansible)
then the ansible environment will know the USER as 'meza-ansible'

This works even in instances when ansible.cfg is not read in.

relevant to Issue [#186](https://github.com/freephile/meza/issues/186)


M	config/paths.yml
M	src/playbooks/site.yml
M	src/roles/ansible-role-certbot-meza/tasks/main.meza.yml
M	src/roles/base/tasks/main.yml
M	src/roles/cron/tasks/main.yml
M	src/roles/database/tasks/secure-installation.yml
M	src/roles/init-controller-config/tasks/main.yml
M	src/roles/mediawiki/tasks/main.yml
M	src/roles/meza-log/tasks/main.yml
M	src/roles/saml/tasks/main.yml
M	src/roles/setup-env/tasks/main.yml
M	src/roles/verify-permissions/tasks/main.yml

## Meza 43.27.3
* [67ff2e2b](https://github.com/freephile/meza/commit/67ff2e2b) (2025-08-21) Greg Rundlett: Add pre-commit framework Use the pre-commit framework https://pre-commit.com/
to manage pre-commit hooks.

With the new .pre-commit-config.yaml file we now have automatic
linting and fixing before errors can be commited into the repo.

To avoid (skip) the pre-commit tasks, use '--no-verify'

NEW requirements-dev.txt is for Python


A	.pre-commit-config.yaml
A	requirements-dev.txt

## Meza 43.27.2
* [2619b97c](https://github.com/freephile/meza/commit/2619b97c) (2025-08-21) Greg Rundlett: remove blank space 

M	src/scripts/lint-files.sh
M	src/scripts/meza.py
M	src/scripts/shell-functions/linux-user.sh

## Meza 43.27.1
* [b3badfcf](https://github.com/freephile/meza/commit/b3badfcf) (2025-08-21) Greg Rundlett: Fix users, groups, file permissions AND linting ---- Key Changes ----
1. Variable Structure:

Distribution-specific variables (RedHat.yml vs Debian.yml):

- user_apache: apache vs www-data
- group_apache: apache vs www-data
- group_wheel: wheel vs sudo

Common variables (paths.yml):

REMOVED m_meza_owner in favor of ansible_user defined in ansible.cfg
RENAMED m_meza_group to m_group (pattern for meza variables)
m_htdocs_owner: "{{ m_user }}" (resolves to meza-ansible)
m_logs_owner: "{{ m_user }}" (resolves to meza-ansible)

2. Usage Patterns:

✅ user_apache: 15 consistent references across roles
✅ group_apache: 33 consistent references across roles
✅ m_htdocs_owner: 9 consistent references across roles
✅ group_wheel: 18 consistent references across roles

3. Tools:
3.1 Enhanced linux-user.sh script for managing Linux users and groups
- mf_add_ssh_user() automatically adds meza-ansible to required groups
- Detects and adds to apache/www-data group for web file access
- Ensures wheel group membership for sudo access
- Provides user feedback when groups are added

3.2 Added verify-permissions playbook and role
- Verifies group memberships, directory permissions, and write access
- Provides detailed output for troubleshooting

You can now run the verification playbook to check if
permissions are correctly set:
    ansible-playbook -i hosts src/playbooks/verify-permissions.yml

Systematically setup all critical dirs w proper ownership and perms

Sticky bit (2775) on all data dirs for consistent group inheritance
Covers: m_meza_data, m_cache_directory, m_logs, m_backups

🔧 Fixes Applied
1. Replaced hardcoded values in roles and playbooks:

- Changed "meza-ansible" to "{{ ansible_user }}"
- Changed "apache" to "{{ user_apache }}"
- Changed "wheel" to "{{ group_wheel }}"

2. Applied proper group permissions with group sticky bit on dirs

3. Improved group detection and fallback in Meza.py

4. Enhanced lock file management better group detection, proper perms

5. Updated cache directory perms to use apache group and sticky bit

---- NEW Linting Improvements ----
- Added Ansible + YAML linting instructions for GitHub Copilot AI agent
- Added .ansible-lint.yml for linting configuration
- Added LINTING.md for linting guidelines
- Added lint-files.sh script for linting files

🔧 Fixes Applied

2. Fixed YAML formatting:

- Corrected brace spacing for ansible-lint compliance
- Removed a trailing space

📋 Verification Results
All modified configuration files pass YAML linting ✅
No hardcoded user/group references remain ✅
Cross-platform compatibility improved ✅
Variable definitions are complete and consistent ✅
The system now properly uses distribution-specific variables for
user_apache/group_apache
(apache/apache on RedHat, www-data/www-data on Debian)
while maintaining consistent references across all roles and playbooks.


A	.ansible-lint
A	.github/copilot-instructions.md
A	LINTING.md
M	config/Debian.yml
M	config/RedHat.yml
M	config/paths.yml
M	src/playbooks/site.yml
A	src/playbooks/verify-permissions.yml
M	src/roles/ansible-role-certbot-meza/tasks/main.meza.yml
M	src/roles/base/tasks/main.yml
M	src/roles/configure-wiki/tasks/main.yml
M	src/roles/cron/tasks/main.yml
M	src/roles/database/tasks/secure-installation.yml
M	src/roles/init-controller-config/tasks/main.yml
M	src/roles/mediawiki/tasks/main.yml
M	src/roles/meza-log/tasks/main.yml
M	src/roles/saml/tasks/main.yml
M	src/roles/update.php/tasks/main.yml
A	src/roles/verify-permissions/tasks/main.yml
M	src/roles/verify-wiki/tasks/main.yml
A	src/scripts/lint-files.sh
M	src/scripts/meza.py
M	src/scripts/shell-functions/linux-user.sh
M	tests/deploys/setup-alt-source-backup.yml

## Meza 43.25.14
* [d89466b6](https://github.com/freephile/meza/commit/d89466b6) (2025-08-21) Greg Rundlett: Add message about RELEASE_NOTES-43.25.11.md 

M	RELEASE-NOTES.md

## Meza 43.25.13
* [e100e377](https://github.com/freephile/meza/commit/e100e377) (2025-08-20) Greg Rundlett: Ensure task names and handler names match Task names should start with an Uppercase letter
Ensure the handler names match the task


M	src/roles/database/handlers/main.yml
M	src/roles/database/tasks/configure.yml
M	src/roles/firewalld/handlers/main.yml
M	src/roles/firewalld/tasks/main.yml
M	src/roles/geerlingguy.kibana/handlers/main.yml
M	src/roles/geerlingguy.kibana/tasks/main.yml

## Meza 43.26.5 origin/feature/ehance-elasticsearch-183 feature/ehance-elasticsearch-183
* [30791abc](https://github.com/freephile/meza/commit/30791abc) (2025-08-20) Greg Rundlett: Make task name and handler name match 

M	src/roles/elasticsearch/tasks/main.yml

## Meza 43.25.12
* [bf90d10b](https://github.com/freephile/meza/commit/bf90d10b) (2025-08-19) Greg Rundlett: Add release notes for 39.5.0 through 43.25.11 

A	RELEASE_NOTES-43.25.11.md

## Meza 43.26.4
* [d0a1ecb3](https://github.com/freephile/meza/commit/d0a1ecb3) (2025-08-16) Greg Rundlett: Add MediaWiki CirrusSearch metastore update and indexing The index template is a conservative mapping covering typical
CirrusSearch indices (wiki_*_content*, wiki_*_general*).
Adjust mappings/analysis as needed for your data.

You can enable the process by setting
mediawiki_cirrus_metastore_upgrade: true
in inventory/group_vars/host_vars.
mediawiki_cirrus_metastore_upgrade defaults to false
in the role default vars file.
It will only run once due to the marker file.

new files:
- src/roles/mediawiki/tasks/cirrus_metastore_upgrade.yml
- src/roles/mediawiki/tasks/files/cirrussearch_index_template.json

extraneous:
- remove unsupported RedHat 7 yum Python install
Still need to addres
https://github.com/freephile/meza/issues/41#issuecomment-2045506153


M	src/roles/mediawiki/defaults/main.yml
A	src/roles/mediawiki/tasks/cirrus_metastore_upgrade.yml
A	src/roles/mediawiki/tasks/files/cirrussearch_index_template.json
M	src/roles/mediawiki/tasks/main.yml

## Meza 43.26.3
* [b41853e4](https://github.com/freephile/meza/commit/b41853e4) (2025-08-15) Greg Rundlett: Add retry and backoff to index building New Ansible variables:
elasticsearch_index_retry_max: 5
elasticsearch_index_retry_initial: 5

Retry/backoff defaults for CirrusSearch/ForceSearchIndex calls during bulk indexing.
These can be overridden per-host/group via inventory, or via environment variables.
The Environment variables are RETRY_MAX and RETRY_INITIAL.


M	config/defaults.yml
M	src/roles/mediawiki/templates/elastic-build-index.sh.j2

## Meza 43.26.2
* [22d977a1](https://github.com/freephile/meza/commit/22d977a1) (2025-08-15) Greg Rundlett: yum is now dnf Besides that, fixed Ansible lint errors.
- Find them with:
ansible-lint $(find ./src/roles/elasticsearch/ -name '*.yml')
- Learn them from https://ansible.readthedocs.io/projects/lint/rules/


M	src/roles/elasticsearch/handlers/main.yml
M	src/roles/elasticsearch/tasks/es_reindex.yml
M	src/roles/elasticsearch/tasks/main.yml

## Meza 43.26.1
* [c6734682](https://github.com/freephile/meza/commit/c6734682) (2025-08-15) Greg Rundlett: Improve elastic index building; add reindexing - Made per-wiki bootstrap script safer and faster:
src/roles/mediawiki/templates/elastic-build-index.sh.j2
Added strict shell flags, flock-based locking, traps, disable-refresh/replica optimization
before bulk indexing, restore settings after, and improved logging.

- Reworked reindex script for zero-downtime alias swap and robustness
src/roles/elasticsearch/templates/elastic-reindex.sh.j2
This script was previously not used anywhere!
Added strict shell flags, locking, optimized create/reindex steps (disable refresh/replicas),
attempted atomic alias swap (fallback safe two-step), preserved aliases handling, better
logging, and placeholders for snapshots.

- add new default var elasticsearch_reindex: false
- when setting elasticsearch_reindex: true in inventory, host_vars, group_vars,
or in the role default vars, then the es_reindex.yml tasks will be executed.

TEST
Locking prevents concurrent runs.
Reindex produces the new index and aliases swap as expected.
Search is not interrupted during alias swap.
Index settings (refresh_interval, replicas) are toggled and restored.


M	src/roles/elasticsearch/defaults/main.yml
M	src/roles/elasticsearch/tasks/main.yml
M	src/roles/elasticsearch/templates/elastic-reindex.sh.j2
M	src/roles/mediawiki/templates/elastic-build-index.sh.j2
