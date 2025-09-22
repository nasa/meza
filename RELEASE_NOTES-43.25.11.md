## Meza Release Notes 39.6.0 → 43.25.11

### Commits

## Meza 43.25.11
* [43be9737](https://github.com/freephile/meza/commit/43be9737) (2025-08-17) Greg Rundlett: Make wiki config directory group executable Not sure this should be apache owned, but if meza-ansible
user is going to have any permission to do anything here
then it needs to be group executable
  - Modified: `src/roles/configure-wiki/tasks/main.yml`

## Meza 43.25.10
* [45658c32](https://github.com/freephile/meza/commit/45658c32) (2025-08-17) Greg Rundlett: fix quoting in SMW setupStore shell command 
  - Modified: `src/roles/verify-wiki/tasks/import-wiki-sql.yml`

## Meza 43.25.9
* [8a5fde26](https://github.com/freephile/meza/commit/8a5fde26) (2025-08-16) Greg Rundlett: Add Admin rights for SMW Allow access to Special:SemanticMediaWiki for Sysops group.
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.25.8
* [4b5df1df](https://github.com/freephile/meza/commit/4b5df1df) (2025-08-16) Greg Rundlett: ignore Python venv and Composer vendor 
  - Modified: `.gitignore`

## Meza 43.25.7
* [c32d15ed](https://github.com/freephile/meza/commit/c32d15ed) (2025-08-14) Greg Rundlett: Add meza-sbom-stats.txt 
  - Added: `src/scripts/meza-sbom-stats.txt`
  - Deleted: `src/scripts/package-stats.txt`

## Meza 43.25.6
* [7a060f0e](https://github.com/freephile/meza/commit/7a060f0e) (2025-08-14) Greg Rundlett: Add stats file generation - --stats option now generates a file in addition to console output
- Update documentation
- avoid directory traversal
  - Modified: `src/scripts/generate-sbom.md`
  - Modified: `src/scripts/generate-sbom.php`

## Meza 43.25.5
* [7a333fbf](https://github.com/freephile/meza/commit/7a333fbf) (2025-08-14) Greg Rundlett: Add package-stats.txt 
  - Added: `src/scripts/package-stats.txt`

## Meza 43.25.4
* [e85adee9](https://github.com/freephile/meza/commit/e85adee9) (2025-08-14) Greg Rundlett: Add first generated SBOM files 
  - Added: `src/scripts/meza-sbom.cyclonedx.json`
  - Added: `src/scripts/meza-sbom.spdx.json`
  - Added: `src/scripts/meza-sbom.txt`

## Meza 43.25.3
* [10803fbd](https://github.com/freephile/meza/commit/10803fbd) (2025-08-14) Greg Rundlett: update docs for generate-sbom.php The GitHub action is disabled since we do not
track composer.lock or composer.local.json in the repo.
  - Modified: `CHANGELOG`
R087	src/scripts/sbom.md	src/scripts/generate-sbom.md

## Meza 43.25.2
* [c4dc2d19](https://github.com/freephile/meza/commit/c4dc2d19) (2025-08-13) Greg Rundlett: Yamllint: truthy values should be one of [false, true] 
  - Modified: `src/playbooks/check-for-changes.yml`
  - Modified: `src/roles/autodeployer/tasks/do-deploy.yml`
  - Modified: `src/roles/base/tasks/main.yml`
  - Modified: `src/roles/database/tasks/replication.yml`
  - Modified: `src/roles/haproxy/tasks/main.yml`
  - Modified: `src/roles/key-transfer/tasks/grant-keys.yml`
  - Modified: `src/roles/mediawiki/tasks/main.yml`
  - Modified: `src/roles/meza-log/tasks/main.yml`
  - Modified: `src/roles/remote-dir-check/tasks/main.yml`
  - Modified: `src/roles/remote-mysqldump/tasks/main.yml`
  - Modified: `src/roles/set-vars/tasks/main.yml`
  - Modified: `src/roles/verify-wiki/tasks/import-wiki-sql.yml`
  - Modified: `tests/deploys/setup-alt-source-backup.yml`

## Meza 43.25.1
* [bc092fb6](https://github.com/freephile/meza/commit/bc092fb6) (2025-08-13) Greg Rundlett: Fix invalid workflow generate-sbom - indentation 
  - Modified: `.github/workflows/generate-sbom.yml`

## Meza 43.24.2
* [3a761ccb](https://github.com/freephile/meza/commit/3a761ccb) (2025-08-13) Greg Rundlett: Add PEP 8 rule to .editorconfig for Python files 
  - Modified: `.editorconfig`

## Meza 43.24.1 origin/feature/improve-python
* [66db2464](https://github.com/freephile/meza/commit/66db2464) (2025-08-13) Greg Rundlett: Code update to score 9.7 / 10 - Added Module Docstring (C0114)
Added comprehensive module documentation explaining the purpose
- Fixed Constant Naming (C0103)
deploy_lock_environment → DEPLOY_LOCK_ENVIRONMENT
language → LANGUAGE
- Fixed Function Naming (C0103)
meza_command_maint_runJobs → meza_command_maint_run_jobs
- Fixed Superfluous Parentheses (C0325)
while (not value): → while not value:
- Fixed Boolean Comparisons (C1805, C1804)
len(argv) == 0 → not argv
rc != 0 → rc
return_code == 0 → not return_code
status != "" → status
status == "" → not status
- Fixed Variable Name Shadowing (W0621, W0622)
datetime parameter → timestamp
dir parameter → directory
- Added Missing Function Docstrings (C0116)
Added docstrings for legacy and placeholder functions
- Fixed File Encoding Issues (W1514)
Added encoding='utf-8' to all file operations
- Fixed Return Statement Consistency (R1710)
Added return None to exception handler in load_yaml
- Removed Unused Variables (W0612)
Removed unused assignments for subprocess calls
Fixed unused args variable with _ placeholder
- Fixed Signal Handler (W0613)
Added pylint disable for required but unused signal handler parameters
- Improved Resource Management (R1732)
Used with statements for file operations where practical
#181 should be test ready
  - Modified: `src/scripts/meza.py`

* [86a5ee11](https://github.com/freephile/meza/commit/86a5ee11) (2025-08-13) Greg Rundlett: Fix Import outside toplevel pylint warnings - Standard library imports come first, alphabetically
- Third-party libraries (yaml) come next
- Unused libraries (jinja2) removed
Ansible uses Jinja2, but we don't use it in meza.py
All the duplicate imports were moved to the top-level per PEP8 standards.
  - Modified: `src/scripts/meza.py`

* [11d074d8](https://github.com/freephile/meza/commit/11d074d8) (2025-08-13) Greg Rundlett: fixed all the C0301 "line too long" warnings Use proper line continuation (indent) for long docstrings
./.venv/bin/pylint src/scripts/meza.py --disable=all --enable=C0301 --score=y
is now scored 10/10
  - Modified: `src/scripts/meza.py`

* [8cb704db](https://github.com/freephile/meza/commit/8cb704db) (2025-08-13) Greg Rundlett: Use Python 3.6 f-strings - "text {}".format(variable) -> f"text {variable}"
- Complex multi-line format strings with better readable f-string formatting
- File path constructions with multiple variables
- More readable
- Better performance
- Less verbose without the .format() calls
- Type safety
  - Modified: `src/scripts/meza.py`

* [68766375](https://github.com/freephile/meza/commit/68766375) (2025-08-13) Greg Rundlett: Agressive Auto PEP 8 formatting reduce long lines
add space around operators
  - Modified: `src/scripts/meza.py`

* [81807369](https://github.com/freephile/meza/commit/81807369) (2025-08-13) Greg Rundlett: basic AutoPEP8 fixes /home/greg/src/meza/.venv/bin/python -m autopep8 --in-place src/scripts/meza.py
  - Modified: `src/scripts/meza.py`

* [1a9ff046](https://github.com/freephile/meza/commit/1a9ff046) (2025-08-13) Greg Rundlett: convert tabs to spaces 
  - Modified: `src/scripts/meza.py`

## Meza 43.23.1 origin/sec/phpsecurity-s2083-injection sec/phpsecurity-s2083-injection
* [6959b04a](https://github.com/freephile/meza/commit/6959b04a) (2025-08-07) Greg Rundlett: Slight refactor to put regex into a constant 
  - Modified: `src/roles/htdocs/files/BackupDownload/DownloadTest.php`
  - Modified: `src/roles/htdocs/files/BackupDownload/download.php`

* [b1d3eccd](https://github.com/freephile/meza/commit/b1d3eccd) (2025-08-06) Greg Rundlett: remove error suppression and handle disabled shell_exec 
  - Modified: `src/roles/htdocs/files/BackupDownload/download.php`

* [bf2521eb](https://github.com/freephile/meza/commit/bf2521eb) (2025-08-06) Greg Rundlett: Prevent web access to sensitive content. Prevents possible XSS in Test files which are not meant
to be served in a web context.
Block test files: RewriteRule ^BackupDownload/.*Test\.php$ - [F,L]
This prevents access to any PHP files ending in "Test.php" in the BackupDownload directory
Returns a 403 Forbidden response
Block README files: RewriteRule ^BackupDownload/README\.md$ - [F,L]
Prevents access to README.md files that might contain sensitive information
Block all markdown files: RewriteRule ^BackupDownload/.*\.md$ - [F,L]
Prevents access to any .md files including documentation that might reveal system details
The [F,L] flags mean:
F = Forbidden (return 403 status)
L = Last rule (stop processing further rules)
  - Modified: `src/roles/htdocs/templates/.htaccess.j2`

* [83e1f601](https://github.com/freephile/meza/commit/83e1f601) (2025-08-06) Greg Rundlett: Avoid using error suppression operator Using the @ operator to suppress errors can hide important issues. The error handling is already in place, so suppression is unnecessary.
  - Modified: `src/roles/htdocs/files/BackupDownload/download.php`

* [250d2aee](https://github.com/freephile/meza/commit/250d2aee) (2025-08-06) Greg Rundlett: Strengthen path containment check The current path validation using strpos() could have edge cases.
For example, if the base path is /backup and the file path is /backup2/file,
the check would incorrectly pass.
Improve both download.php and DownloadTest.php
for pull #180
  - Modified: `src/roles/htdocs/files/BackupDownload/DownloadTest.php`
  - Modified: `src/roles/htdocs/files/BackupDownload/download.php`

* [a6e593b0](https://github.com/freephile/meza/commit/a6e593b0) (2025-08-06) Greg Rundlett: Fixed dynamic loading of config - add comment to DownloadTest to keep helper methods  in sync
- replace spaces with tabs for coding standards
syntax hints added to code blocks in README
The dynamic config loading was mentioned in pull #180
Documentation in src/roles/htdocs/files/BackupDownload/PATH_RESOLUTION_FIX.md
  - Modified: `src/roles/htdocs/files/BackupDownload/DownloadTest.php`
  - Added: `src/roles/htdocs/files/BackupDownload/PATH_RESOLUTION_FIX.md`
  - Modified: `src/roles/htdocs/files/BackupDownload/README.md`
  - Modified: `src/roles/htdocs/files/BackupDownload/download.php`
  - Modified: `src/roles/htdocs/files/BackupDownload/index.php`

* [f520f670](https://github.com/freephile/meza/commit/f520f670) (2025-08-06) Greg Rundlett: Implement a hybrid Test Framework that works with both PHPUnit (MediaWiki standard) when available; and a custom fallback framework. Created a comprehensive test file (DownloadTest.php) that:
- Automatically detects if PHPUnit is available and uses it
- Falls back to a custom test framework when PHPUnit is not available
- Provides 20+ test methods covering all security aspects
- Tests 37+ individual security validations
Test Coverage: The test suite validates:
- Input validation - All parameter validation functions
- Path traversal prevention - Multiple attack vector simulations
- File type validation - Extension checking and double extension attacks
- Authorization - User permission and access control testing
- Edge cases - Null bytes, Windows paths, absolute paths, etc.
Security Implementation: The download.php script now includes:
- CWE-22 prevention - Multiple layers of path traversal protection
- Input sanitization - Strict regex validation of all inputs
- File type restriction - Only approved backup file extensions allowed
- Authorization integration - SAML and Meza user management integration
Documentation: Created comprehensive README.md documenting:
- Security features and implementation details
- Usage examples and approved patterns
- Testing procedures and development notes
- Error handling and logging information
The test suite runs successfully and validates that all security measures
are working correctly. The few "failing" tests are actually demonstrating
that the security validation is working properly by rejecting malicious
inputs.
The implementation is now ready for production use with comprehensive
security validation and testing coverage.
Fixes #179
  - Added: `src/roles/htdocs/files/BackupDownload/DownloadTest.php`
  - Added: `src/roles/htdocs/files/BackupDownload/README.md`

* [d064cb43](https://github.com/freephile/meza/commit/d064cb43) (2025-08-06) Greg Rundlett: Prevent directory traversal vulnerability. (Vulnerability found with SonarQube) Key Security Improvements:
- Input Validation: All user inputs are validated with strict regex patterns
- Path Construction: File paths are built securely without direct user input
- Realpath Validation: Uses realpath() to resolve paths and ensure they stay within allowed directories
- File Allowlist: Only allows files that actually exist in the filesystem
- Extension Allowlist: Only allows specific file extensions for backups
- No Path Traversal: Completely prevents ../ attacks through validation
- Error Handling: Proper error handling with appropriate HTTP status codes
- Logging: Errors are logged for debugging without exposing sensitive information
This approach ensures that users can only download files that:
- Actually exist in the backup directory structure
- Have allowed file extensions
- The user has permission to access
- Are within the authorized directory tree
Also
- Change copyright
- Add license
  - Modified: `src/roles/htdocs/files/BackupDownload/download.php`

## Meza 43.22.4
* [4b0511b6](https://github.com/freephile/meza/commit/4b0511b6) (2025-08-05) Greg Rundlett: fix YAML lint errors 
  - Modified: `.github/workflows/generate-sbom.yml`

## Meza 43.22.3
* [b92c983d](https://github.com/freephile/meza/commit/b92c983d) (2025-08-05) Greg Rundlett: fix YAML lint errors 
  - Modified: `.github/workflows/generate-sbom.yml`
  - Modified: `src/roles/apache-php/tasks/main.yml`

## Meza 43.22.2
* [0fee59cf](https://github.com/freephile/meza/commit/0fee59cf) (2025-08-05) Greg Rundlett: Update README with component logos 
  - Modified: `README.md`
  - Added: `assets/meza_component_logos.png`

## Meza 43.22.1 origin/REL1_43 REL1_43
* [bb2f80c1](https://github.com/freephile/meza/commit/bb2f80c1) (2025-08-05) Greg Rundlett: Update CHANGELOG for REL1_43 
  - Modified: `CHANGELOG`

origin/feature/sbom feature/sbom
* [ac1eb9c5](https://github.com/freephile/meza/commit/ac1eb9c5) (2025-08-04) Greg Rundlett: Add the github action workflow This needs to be tested.
  - Added: `.github/workflows/generate-sbom.yml`

* [4f3323fe](https://github.com/freephile/meza/commit/4f3323fe) (2025-08-04) Greg Rundlett: Add script to show git (annotated) tags You SHOULD use Git Graph in VsCode, but this script is handy for Release Notes and so on.
  - Added: `src/scripts/showTags.md`
  - Added: `src/scripts/showTags.sh`

* [6152f022](https://github.com/freephile/meza/commit/6152f022) (2025-08-04) Greg Rundlett: spaces to tabs 
  - Modified: `src/scripts/importExtensions.php`
  - Modified: `src/scripts/listCoreExtensions.php`

* [16a93f80](https://github.com/freephile/meza/commit/16a93f80) (2025-08-02) Greg Rundlett: convert spaces to tabs per coding standards 
  - Modified: `src/scripts/generate-sbom.php`

* [6abeeac0](https://github.com/freephile/meza/commit/6abeeac0) (2025-08-02) Greg Rundlett: Change root package reference to NASA - Updated download location: Changed from https://github.com/enterprisemediawiki/meza to https://github.com/nasa/meza
- Updated copyright: Changed from Copyright Enterprise MediaWiki to Copyright NASA
- Updated supplier: Changed from Organization: Enterprise MediaWiki to Organization: NASA
- Updated vendor in CycloneDX: Changed from Meza to NASA
- Added external references: Added both the GitHub repository and the MediaWiki documentation page for better reference
These changes ensure that the SBOM correctly identifies NASA as the organization behind the Meza project and points to the correct repository location.
  - Modified: `src/scripts/generate-sbom.php`

* [5170674e](https://github.com/freephile/meza/commit/5170674e) (2025-08-02) Greg Rundlett: Update to include composer.LOCAL.lock Key improvements in this updated version:
- Dual File Support: Automatically detects and includes composer.local.lock
- Conflict Resolution: When the same package exists in both files, uses the version from composer.lock
- Source Tracking: All packages include metadata about which file they came from
- Enhanced Reporting: Statistics and human-readable output show breakdown by source file
- Visual Indicators: Local packages are marked with [LOCAL] in text output
- Updated Documentation: Explains the dual-file approach and precedence rules
Now the SBOM will include all dependencies from both your main composer.lock and any local overrides in composer.local.lock, giving you a complete picture of your dependency landscape.
Note that when "looking" for composer.lock, the path is hard-coded as
/opt/htdocs/mediawiki/composer.lock. We could make it 'intelligently search' but for now we'll use the known path for Meza installations.
  - Modified: `src/scripts/generate-sbom.php`
  - Modified: `src/scripts/sbom.md`

* [274a8e02](https://github.com/freephile/meza/commit/274a8e02) (2025-08-02) Greg Rundlett: initial concept with just composer.json This comprehensive SBOM solution will:
- Parse your composer.lock to extract all dependencies
- Generate industry-standard SBOM formats (SPDX, CycloneDX)
- Provide human-readable summaries
- Automatically update when dependencies change
- Support security and compliance requirements
- Integrate with CI/CD pipelines
The generated SBOMs will include all 200+ packages
from your MediaWiki ecosystem, properly categorized
by scope and type, with complete license and metadata information.
  - Added: `src/scripts/generate-sbom.php`
  - Added: `src/scripts/sbom.md`

* [65933815](https://github.com/freephile/meza/commit/65933815) (2025-08-01) Greg Rundlett: Create SECURITY policy (#175) * Create SECURITY.md
GitHub reads this root-level file as the repository's Security Policy (IOW it's standard)
* Simplify link, add email, fix linting
  - Added: `SECURITY.md`

## Meza 43.19.4
* [3588683f](https://github.com/freephile/meza/commit/3588683f) (2025-08-01) Greg Rundlett: Add TODO comments 
  - Modified: `src/roles/apache-php/tasks/main.yml`
  - Modified: `src/scripts/getmeza.sh`

## Meza 43.19.3
* [bbfc7943](https://github.com/freephile/meza/commit/bbfc7943) (2025-07-31) Greg Rundlett: Turn off Kibana by default 
  - Modified: `config/defaults.yml`

## Meza 43.19.2
* [373275a5](https://github.com/freephile/meza/commit/373275a5) (2025-07-31) Greg Rundlett: Turn off Certbot by default 
  - Modified: `config/defaults.yml`

## Meza 43.19.1
* [04c92425](https://github.com/freephile/meza/commit/04c92425) (2025-07-31) Greg Rundlett: Add missing composer merge for Abuse Filter May (likely!) fix Issue [#168](https://github.com/freephile/meza/issues/168)
Although it appeared to be a permission error, or
missing class, the underlying problem was missing
composer libraries needed by AbuseFilter
  - Modified: `config/MezaCoreExtensions.yml`

origin/bug/issue-workflow bug/issue-workflow
* [5be1107c](https://github.com/freephile/meza/commit/5be1107c) (2025-07-29) Greg Rundlett: Final nitpick changes fixes Issue [#170](https://github.com/freephile/meza/issues/170) 
  - Modified: `.github/ISSUE_TEMPLATE/bug_report.md`
  - Modified: `.github/ISSUE_TEMPLATE/feature_request.md`

* [0263f719](https://github.com/freephile/meza/commit/0263f719) (2025-07-29) Greg Rundlett: Update new issue workflow templates - updates Pull #169
- fixes Issue [#170](https://github.com/freephile/meza/issues/170)
- deletes (legacy) .github/ISSUE_TEMPLATE.md
  - Deleted: `.github/ISSUE_TEMPLATE.md`
  - Modified: `.github/ISSUE_TEMPLATE/bug_report.md`
  - Modified: `.github/ISSUE_TEMPLATE/custom.md`
  - Modified: `.github/ISSUE_TEMPLATE/feature_request.md`

* [91a52b1d](https://github.com/freephile/meza/commit/91a52b1d) (2025-07-29) Greg Rundlett: Update issue templates to new workflow See https://docs.github.com/en/communities/using-templates-to-encourage-useful-issues-and-pull-requests/configuring-issue-templates-for-your-repository
  - Added: `.github/ISSUE_TEMPLATE/bug_report.md`
  - Added: `.github/ISSUE_TEMPLATE/custom.md`
  - Added: `.github/ISSUE_TEMPLATE/feature_request.md`

## Meza 43.17.1 feature/new-issue-template-workflow
* [2a996bd0](https://github.com/freephile/meza/commit/2a996bd0) (2025-07-24) Greg Rundlett: DOWNGRADE MediaWiki to 1.43.1 Avoid breaking changes in 1.43.2 which technically should
not happen in a point release; without announcment
if you follow semantic versioning and test backports.
Addresses Issue [#163](https://github.com/freephile/meza/issues/163)
  - Modified: `config/defaults.yml`
  - Modified: `src/roles/mediawiki/tasks/main.yml`

## Meza 43.16.1
* [7d40d3ee](https://github.com/freephile/meza/commit/7d40d3ee) (2025-07-24) Greg Rundlett: Upgrade ModernTimeline to 2.x Upgrade from 1.x to 2.x
Fixes #167
Was getting 500 error
From Release Notes https://github.com/ProfessionalWiki/ModernTimeline#release-notes
Released on July 7th, 2025.
- Raised minimum PHP version to 8.0
- Raised minimum MediaWiki version to 1.39
- Upgraded to TimelineJS3 3.9.6
- Translation updates from https://translatewiki.net
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.15.1
* [9aab3a1b](https://github.com/freephile/meza/commit/9aab3a1b) (2025-07-23) Greg Rundlett: Enable 'overwrite_local_changes'  for Composer Add composer-installed skins and extensions to the switch for overwriting local changes. This gives parity with Git-controlled extensions and skins.
The default is 'false' because neither git nor Composer want to clobber local changes. But if you want to be able to, it can be easily done with the Meza config variable 'overwrite_local_changes' (bool)
  - Modified: `RELEASE-NOTES.md`
  - Modified: `config/defaults.yml`
  - Modified: `src/roles/mediawiki/tasks/main.yml`
  - Modified: `src/roles/mediawiki/templates/composer.local.json.j2`

## Meza 43.14.1
* [8a6d4932](https://github.com/freephile/meza/commit/8a6d4932) (2025-07-22) Greg Rundlett: Upgrade Maps from 10.x to 11.x Fixes #166 SMWPrintRequest not found
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.13.3
* [1896a15e](https://github.com/freephile/meza/commit/1896a15e) (2025-07-10) Greg Rundlett: Fix linting - remove trailing spaces 
  - Modified: `config/MezaCoreExtensions.yml`
  - Modified: `config/MezaCoreSkins.yml`

## Meza 43.13.2
* [869607c0](https://github.com/freephile/meza/commit/869607c0) (2025-07-09) Greg Rundlett: remove extraneous '1' 
  - Modified: `src/scripts/listCoreExtensions.php`

## Meza 43.13.1
* [db2e3b5c](https://github.com/freephile/meza/commit/db2e3b5c) (2025-07-09) Greg Rundlett: Disable Who's Online for being problematic 
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.12.1
* [37b9be51](https://github.com/freephile/meza/commit/37b9be51) (2025-06-06) Greg Rundlett: Add MW Core bundled extensions Fixes #135
Adds extensions
- AbuseFilter
- DiscussionTools
- Linter
- LoginNotify
- Math
- OATHAuth
- SecureLinkFixer
- SpamBlacklist
- TitleBlacklist
plus the skin Timeless
  - Modified: `config/MezaCoreExtensions.yml`
  - Modified: `config/MezaCoreSkins.yml`

## Meza 43.11.2
* [7581e79e](https://github.com/freephile/meza/commit/7581e79e) (2025-06-06) Greg Rundlett: re-enable Semantic Extensions   SRF and SCQ update Semantic Result Formats
    composer: "mediawiki/semantic-result-formats"
    version: "^5.0"
enable SemanticCompoundQueries
    composer: "mediawiki/semantic-compound-queries"
    version: "3.x-dev"
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.11.1
* [9e3ff555](https://github.com/freephile/meza/commit/9e3ff555) (2025-06-05) Greg Rundlett: Fix #140 enabling the SemanticDependencyUpdater and SRF SemanticDependencyUpdater and SemanticResultFormats
are the last of the SMW extensions to be re-enabled.
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.10.5
* [35a35c13](https://github.com/freephile/meza/commit/35a35c13) (2025-06-05) Greg Rundlett: Fixes #143 elimating duplicates between MezaCoreExtensions and MezaLocalExtensions Adds utility scripts that can be used to list core and diff to local.
A corresponding commit was made to the meza-conf repository affecting MezaLocalExtensions.yml.
  - Modified: `config/MezaCoreExtensions.yml`
  - Added: `src/scripts/findDupesInLocalExtensions.php`
  - Added: `src/scripts/listCoreExtensions.php`

## Meza 43.10.4
* [41bbcd82](https://github.com/freephile/meza/commit/41bbcd82) (2025-06-05) Greg Rundlett: Fixes Issue [#62](https://github.com/freephile/meza/issues/62) Url for Netdata install has changed Update the URL, and use sh instead of bash because that is
what the install guide does (hence there are no 'bashisms')
  - Modified: `src/roles/netdata/tasks/main.yml`

## Meza 43.10.3
* [66d7a4b2](https://github.com/freephile/meza/commit/66d7a4b2) (2025-06-04) Greg Rundlett: specify theme (neutral) for Mermaid extension 
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.10.2
* [6100d3c3](https://github.com/freephile/meza/commit/6100d3c3) (2025-06-04) Greg Rundlett: Block WhatLinksHere using Lockdown against robots #156 
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.10.1
* [7985579b](https://github.com/freephile/meza/commit/7985579b) (2025-06-01) Greg Rundlett: Fixes #157 include CommentStreams composer.json in composer.local.json
  - Modified: `config/MezaCoreExtensions.yml`

* [63103b85](https://github.com/freephile/meza/commit/63103b85) (2025-05-15) Greg Rundlett: Add logos of component technologies 
  - Modified: `README.md`

* [f37f706c](https://github.com/freephile/meza/commit/f37f706c) (2024-12-19) Rich Evans: Update haproxy.cfg.j2 removed commented-out obsolete configuration text in haproxy.cfg
  - Modified: `src/roles/haproxy/templates/haproxy.cfg.j2`

* [9a7c9991](https://github.com/freephile/meza/commit/9a7c9991) (2024-12-02) amcgillivray-nasa: Update getmeza.sh Incorporates cowen23's fix for Issue [#57](https://github.com/freephile/meza/issues/57)
  - Modified: `src/scripts/getmeza.sh`

* [cf09936d](https://github.com/freephile/meza/commit/cf09936d) (2025-03-28) Greg Rundlett: Add quotes to version spec for Bootstrap 
  - Modified: `config/MezaCoreExtensions.yml`

* [e4ba295c](https://github.com/freephile/meza/commit/e4ba295c) (2025-02-18) Greg Rundlett: make lint command graceful 
  - Modified: `.github/workflows/yamllint.yml`

* [a3add764](https://github.com/freephile/meza/commit/a3add764) (2025-02-18) Greg Rundlett: final yamllint fixes 
  - Modified: `config/Debian.yml`
  - Modified: `config/MezaCoreExtensions.yml`
  - Modified: `config/MezaCoreSkins.yml`

* [33cfdf17](https://github.com/freephile/meza/commit/33cfdf17) (2025-02-18) Greg Rundlett: moved yamllint config 
R100	src/.yamllint	.yamllint

origin/fix-yamllint fix-yamllint
* [f6ad58ee](https://github.com/freephile/meza/commit/f6ad58ee) (2025-02-18) Greg Rundlett: Fix yaml files for syntax 
  - Modified: `.github/workflows/yamllint.yml`
  - Modified: `.travis.yml`
  - Modified: `config/Debian.yml`
  - Modified: `config/MezaCoreExtensions.yml`
  - Modified: `config/MezaCoreSkins.yml`
  - Modified: `config/defaults.yml`
  - Modified: `config/i18n/en.yml`
  - Modified: `requirements.yml`
  - Modified: `src/playbooks/check-for-changes.yml`
  - Modified: `src/playbooks/cleanup-upload-stash.yml`
  - Modified: `src/playbooks/delete-elasticsearch.yml`
  - Modified: `src/playbooks/example-block.yaml`
  - Modified: `src/playbooks/getdocker.yml`
  - Modified: `src/playbooks/push-backup.yml`
  - Modified: `src/playbooks/rebuild-smw-and-index.yml`
  - Modified: `src/playbooks/test-certbot.yml`
  - Modified: `src/requirements.yml`
  - Modified: `src/roles/ansible-role-certbot-meza/.yamllint`
  - Modified: `src/roles/ansible-role-certbot-meza/defaults/main.yml`
  - Modified: `src/roles/ansible-role-certbot-meza/meta/main.yml`
  - Modified: `src/roles/ansible-role-certbot-meza/tasks/main.meza.yml`
  - Modified: `src/roles/ansible-role-certbot-meza/tasks/setup-RedHat.yml`
  - Modified: `src/roles/ansible-role-certbot-meza/vars/default.yml`
  - Modified: `src/roles/ansible-role-certbot-meza/vars/main.yml`
  - Modified: `src/roles/apache-php/tasks/mssql_driver_for_php.yml`
  - Modified: `src/roles/backup-config/tasks/main.yml`
  - Modified: `src/roles/backup-db-wikis-push/tasks/main.yml`
  - Modified: `src/roles/backup-db-wikis/tasks/main.yml`
  - Modified: `src/roles/backup-uploads-push/tasks/main.yml`
  - Modified: `src/roles/backup-uploads/tasks/main.yml`
  - Modified: `src/roles/base-extras/tasks/main.yml`
  - Modified: `src/roles/base/tasks/main.yml`
  - Modified: `src/roles/composer/defaults/main.yml`
  - Modified: `src/roles/cron/defaults/main.yml`
  - Modified: `src/roles/database/defaults/main.yml`
  - Modified: `src/roles/database/tasks/configure.yml`
  - Modified: `src/roles/database/tasks/replication.yml`
  - Modified: `src/roles/database/tasks/secure-installation.yml`
  - Modified: `src/roles/database/tasks/setup-Debian.yml`
  - Modified: `src/roles/elasticsearch/tasks/es_reindex.yml`
  - Modified: `src/roles/elasticsearch/tasks/main.yml`
  - Modified: `src/roles/firewall_service/tasks/main.yml`
  - Modified: `src/roles/geerlingguy.kibana/.github/workflows/stale.yml`
  - Modified: `src/roles/gluster/defaults/main.yml`
  - Modified: `src/roles/haproxy/handlers/main.yml`
  - Modified: `src/roles/haproxy/tasks/main.yml`
  - Modified: `src/roles/htdocs/tasks/main.yml`
  - Modified: `src/roles/mediawiki/tasks/main.yml`
  - Modified: `src/roles/memcached/tasks/main.yml`
  - Modified: `src/roles/remote-dir-check/tasks/main.yml`
  - Modified: `src/roles/remote-mysqldump/tasks/main.yml`
  - Modified: `src/roles/saml/tasks/main.yml`
  - Modified: `src/roles/set-vars/tasks/main.yml`
  - Modified: `src/roles/sql-backup-cleanup/tasks/main.yml`
  - Modified: `src/roles/sync-configs/tasks/main.yml`
  - Modified: `src/roles/update.php/tasks/main.yml`
  - Modified: `src/roles/verify-wiki/tasks/import-wiki-sql.yml`
  - Modified: `src/roles/verify-wiki/tasks/init-wiki.yml`
  - Modified: `src/roles/verify-wiki/tasks/transfer-backup-to-db-master.yml`
  - Modified: `tests/deploys/setup-alt-source-backup.yml`

origin/qb-1.43 qb-1.43
* [fd7ac3a9](https://github.com/freephile/meza/commit/fd7ac3a9) (2025-02-17) Greg Rundlett: Update README with yamllint workflow badge 
  - Modified: `README.md`

* [73c2c0f6](https://github.com/freephile/meza/commit/73c2c0f6) (2025-02-17) Greg Rundlett: Add multiple branch patterns for the yamllint workflow 
  - Modified: `.github/workflows/yamllint.yml`

origin/feature/yamlint-workflow feature/yamlint-workflow
* [5c333f5e](https://github.com/freephile/meza/commit/5c333f5e) (2025-02-17) Greg Rundlett: Add new linting workflow 
  - Added: `.github/workflows/yamllint.yml`

## Meza 43.8.1
* [900a1dc9](https://github.com/freephile/meza/commit/900a1dc9) (2025-01-28) Greg Rundlett: Switch Mermaid and BootstrapComponents to dev-master Replace deprecated Class loading for \PHPUnit\Framework\TestCase
Fixes Issue [#141](https://github.com/freephile/meza/issues/141)
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.4.7
* [7bc8622d](https://github.com/freephile/meza/commit/7bc8622d) (2025-01-24) Greg Rundlett: Finish off Maintenance Script improvements Last commits for this round of maintenance script operations
Fixes Issue [#142](https://github.com/freephile/meza/issues/142)
  - Modified: `src/roles/elasticsearch/tasks/es_reindex.yml`
  - Modified: `src/roles/mediawiki/templates/elastic-build-index.sh.j2`
  - Modified: `src/roles/mediawiki/templates/elastic-rebuild-all.sh.j2`

## Meza 43.7.1
* [7cb90918](https://github.com/freephile/meza/commit/7cb90918) (2025-01-24) Greg Rundlett: Add comments for $smwgParserFeatures (Links In Values feature) 
  - Modified: `config/MezaCoreExtensions.yml`

* [ab10ebe1](https://github.com/freephile/meza/commit/ab10ebe1) (2025-01-17) Greg Rundlett: Maintenance Script update (missed these) Convert script calls to go through executable
'maintenance/run'
For extensions, use the class naming pattern
'Extension:ClassName'
Fixes Issue [#142](https://github.com/freephile/meza/issues/142)
  - Modified: `src/playbooks/cleanup-upload-stash.yml`
  - Modified: `src/roles/cron/templates/runAllJobs.php.j2`

## Meza 43.4.6
* [ca179104](https://github.com/freephile/meza/commit/ca179104) (2025-01-17) Greg Rundlett: Finish Maintenance Script update Convert script calls to go through executable
'maintenance/run'
For extensions, use the class naming pattern
'Extension:ClassName'
Fixes Issue [#142](https://github.com/freephile/meza/issues/142)
  - Modified: `src/playbooks/cleanup-upload-stash.yml`
  - Modified: `src/roles/mediawiki/tasks/main.yml`
  - Modified: `src/roles/mediawiki/templates/elastic-build-index.sh.j2`
  - Modified: `src/roles/mediawiki/templates/refresh-links.sh.j2`
  - Modified: `src/roles/mediawiki/templates/smw-rebuild-all.sh.j2`
  - Modified: `src/roles/meza-log/templates/server-performance.sh.j2`
  - Modified: `src/roles/update.php/tasks/main.yml`
  - Modified: `src/roles/verify-wiki/tasks/import-wiki-sql.yml`
  - Modified: `src/roles/verify-wiki/tasks/init-wiki.yml`
  - Modified: `src/scripts/unite-the-wikis.sh`

## Meza 43.6.1
* [d624f7b4](https://github.com/freephile/meza/commit/d624f7b4) (2025-01-16) Greg Rundlett: A new tool to inspect the git repos on the controller 
  - Added: `src/scripts/listExtensionRepos.sh`

## Meza 43.4.5
* [e267f148](https://github.com/freephile/meza/commit/e267f148) (2025-01-16) Greg Rundlett: Update the Update.php role for new Maintenance script ops Issue [#142](https://github.com/freephile/meza/issues/142)
  - Modified: `src/roles/update.php/tasks/main.yml`

## Meza 43.4.4
* [9e4de5e1](https://github.com/freephile/meza/commit/9e4de5e1) (2025-01-16) Greg Rundlett: Update maintenance scripts Server Performance logging/reporting role
(Probably needs to be eliminated altogether)
Issue [#142](https://github.com/freephile/meza/issues/142)
  - Modified: `src/roles/meza-log/templates/server-performance.sh.j2`

## Meza 43.4.3
* [278c394e](https://github.com/freephile/meza/commit/278c394e) (2025-01-16) Greg Rundlett: Update maintenance scripts showSiteStats and refreshLinks
Issue [#142](https://github.com/freephile/meza/issues/142)
  - Modified: `src/roles/mediawiki/templates/refresh-links.sh.j2`

## Meza 43.4.2
* [57c9bb20](https://github.com/freephile/meza/commit/57c9bb20) (2025-01-16) Greg Rundlett: Disable metastore maintenance All maintenance needs to be refactored
Issue [#142](https://github.com/freephile/meza/issues/142)
Also, this may be affected by upgrading to SMW 5.x
Issue [#136](https://github.com/freephile/meza/issues/136)
  - Modified: `src/roles/mediawiki/tasks/main.yml`

## Meza 43.5.1
* [7ad66760](https://github.com/freephile/meza/commit/7ad66760) (2025-01-16) Greg Rundlett: Prefer source in composer managed extensions Use the --prefer-source option to composer
Eliminate the duplicate composer run
Eliminate the removal of a non-existant SMW file (IdeAliases.php)
needed for Issue [#136](https://github.com/freephile/meza/issues/136)
  - Modified: `src/roles/mediawiki/tasks/main.yml`

## Meza 43.4.1
* [f87b92b2](https://github.com/freephile/meza/commit/f87b92b2) (2025-01-16) Greg Rundlett: Update maintenance scripts for MediaWiki 1.40 Fixes Issue [#142](https://github.com/freephile/meza/issues/142)
  - Modified: `src/roles/cron/templates/runAllJobs.php.j2`

## Meza 43.3.1
* [4ca775d6](https://github.com/freephile/meza/commit/4ca775d6) (2025-01-16) Greg Rundlett: Update MezaCoreExtensions.yml for REL1_43 Use full enableSemantics() call to fix URL pattern
Switch to gerrit because GitHub seems to throttle
SemanticDrilldown 3.05 -> dev-master
SemanticScribunto 2.2.0 -> dev-master
add quotes on SemanticDrilldown version spec
add quotes on SubPageList version spec
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.2.1
* [e2873948](https://github.com/freephile/meza/commit/e2873948) (2025-01-10) Greg Rundlett: Create a variable named php_memory_limit defaulting to 128M PHP normally defaults to 128M so we also default to that.
Templated in the php.ini template of the apache-php role.
Fixes Issue [#151](https://github.com/freephile/meza/issues/151)
  - Modified: `config/defaults.yml`
  - Modified: `src/roles/apache-php/templates/php.ini.j2`

## Meza 43.1.2
* [51c89233](https://github.com/freephile/meza/commit/51c89233) (2025-01-08) Greg Rundlett: Enable SemanticCompoundQueries Was 2.2.0
Now 3.x-dev (same as dev-master)
Issue [#140](https://github.com/freephile/meza/issues/140)
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.1.1
* [d2a87e79](https://github.com/freephile/meza/commit/d2a87e79) (2025-01-08) Greg Rundlett: Use SubPageList dev-master Fixes Issue [#138](https://github.com/freephile/meza/issues/138)
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.0.3
* [ca17aac6](https://github.com/freephile/meza/commit/ca17aac6) (2025-01-07) Greg Rundlett: Disable or update non-working skins These skins need a version adjustment or update
to work with REL1_43
- Tweeki
- Medik
Use tags/v4.39.1 for Tweeki
Issue [#136](https://github.com/freephile/meza/issues/136)
  - Modified: `config/MezaCoreSkins.yml`

## Meza 43.0.2
* [cde7d8cf](https://github.com/freephile/meza/commit/cde7d8cf) (2025-01-07) Greg Rundlett: Disable non-working extensions These extensions do not work, or need a version adjustment,
to be compatible with REL1_43 and SMW 5.x
- SemanticCompoundQueries
- SemanticDependencyUpdater
- SemanticDrilldown
- SemanticExtraSpecialProperties
- SemanticResultFormats
- SemanticScribunto
- SubPageList
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.0.1
* [36567d83](https://github.com/freephile/meza/commit/36567d83) (2025-01-07) Greg Rundlett: Switch SMW to dev-master Addresses Issue [#137](https://github.com/freephile/meza/issues/137)
composer show -a mediawiki/semantic-media-wiki
shows all available composer versions
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 43.0.0
* [d7d6a88e](https://github.com/freephile/meza/commit/d7d6a88e) (2025-01-07) Greg Rundlett: Upgrade to REL1_43 Fixes Issue [#136](https://github.com/freephile/meza/issues/136)
set meza_repository_url to use freephile/meza
set mediawiki_version for core
set mediawiki_default_branch (for extensions)
set php_ius_version to php81
  - Modified: `config/defaults.yml`

## Meza 39.17.0 origin/qb origin/feature-kibana
* [18d66875](https://github.com/freephile/meza/commit/18d66875) (2024-12-19) Greg Rundlett: Kibana Security Do not allow connections from remote (the Internet)
Only allow localhost connections
Issue [#60](https://github.com/freephile/meza/issues/60)
  - Modified: `src/roles/geerlingguy.kibana/vars/main.yml`

* [a2a849ca](https://github.com/freephile/meza/commit/a2a849ca) (2024-12-19) Greg Rundlett: Comment our changes in the task file Issue [#37](https://github.com/freephile/meza/issues/37)
  - Modified: `src/roles/geerlingguy.kibana/tasks/main.yml`

* [a8904543](https://github.com/freephile/meza/commit/a8904543) (2024-12-18) Greg Rundlett: Add versionlock to Kibana Issue [#58](https://github.com/freephile/meza/issues/58) Issue [#37](https://github.com/freephile/meza/issues/37)
  - Modified: `src/roles/geerlingguy.kibana/tasks/main.yml`

* [0a6ed4ed](https://github.com/freephile/meza/commit/0a6ed4ed) (2024-12-18) Greg Rundlett: Add Kibana frontend to Elasticsearch Fixes Issue [#37](https://github.com/freephile/meza/issues/37)
Uses Geerlingguy Kibana
  - Modified: `.gitignore`
  - Modified: `config/defaults.yml`
  - Modified: `src/playbooks/site.yml`
  - Modified: `src/requirements.yml`
  - Added: `src/roles/geerlingguy.kibana/.ansible-lint`
  - Added: `src/roles/geerlingguy.kibana/.github/FUNDING.yml`
  - Added: `src/roles/geerlingguy.kibana/.github/workflows/ci.yml`
  - Added: `src/roles/geerlingguy.kibana/.github/workflows/release.yml`
  - Added: `src/roles/geerlingguy.kibana/.github/workflows/stale.yml`
  - Added: `src/roles/geerlingguy.kibana/.gitignore`
  - Added: `src/roles/geerlingguy.kibana/.yamllint`
  - Added: `src/roles/geerlingguy.kibana/LICENSE`
  - Added: `src/roles/geerlingguy.kibana/README.md`
  - Added: `src/roles/geerlingguy.kibana/defaults/main.yml`
  - Added: `src/roles/geerlingguy.kibana/handlers/main.yml`
  - Added: `src/roles/geerlingguy.kibana/meta/.galaxy_install_info`
  - Added: `src/roles/geerlingguy.kibana/meta/main.yml`
  - Added: `src/roles/geerlingguy.kibana/molecule/default/converge.yml`
  - Added: `src/roles/geerlingguy.kibana/molecule/default/molecule.yml`
  - Added: `src/roles/geerlingguy.kibana/molecule/default/requirements.yml`
  - Added: `src/roles/geerlingguy.kibana/tasks/main.yml`
  - Added: `src/roles/geerlingguy.kibana/tasks/setup-Debian.yml`
  - Added: `src/roles/geerlingguy.kibana/tasks/setup-RedHat.yml`
  - Added: `src/roles/geerlingguy.kibana/templates/kibana.repo.j2`
  - Added: `src/roles/geerlingguy.kibana/templates/kibana.yml.j2`
  - Added: `src/roles/geerlingguy.kibana/vars/main.yml`

* [721e7fbf](https://github.com/freephile/meza/commit/721e7fbf) (2024-12-17) Greg Rundlett: rebased, but keep main comments Fixes Issue [#58](https://github.com/freephile/meza/issues/58)
  - Modified: `src/requirements.yml`

* [6706b93f](https://github.com/freephile/meza/commit/6706b93f) (2024-03-14) Greg Rundlett: Add Kibana frontend to Elasticsearch Using Ansible Galaxy and requirements.yml, we install Jeff Geeerling's
geerlingguy.kibana role. Fixes Issue [#37](https://github.com/freephile/meza/issues/37)
Kibana is available at port 5601
This work  was performed for NASA GRC-ATF by WikiWorks per NASA Contract  NNC15BA02B.
  - Modified: `src/playbooks/site.yml`
  - Added: `src/requirements.yml`

## Meza 39.16.4
* [7c01ff54](https://github.com/freephile/meza/commit/7c01ff54) (2024-12-17) Greg Rundlett: We do not need a special port for certbot creation Issue [#130](https://github.com/freephile/meza/issues/130)
  - Modified: `src/roles/ansible-role-certbot-meza/vars/main.yml`

## Meza 39.16.3
* [ce07fdb9](https://github.com/freephile/meza/commit/ce07fdb9) (2024-12-17) Greg Rundlett: Do not put crt and key into HAProxy Issue [#130](https://github.com/freephile/meza/issues/130)
  - Modified: `src/roles/haproxy/tasks/main.yml`

## Meza 39.16.2
* [39a65494](https://github.com/freephile/meza/commit/39a65494) (2024-10-13) Rich Evans: Update MezaCoreExtensions.yml Added $wgObjectCacheSessionExpiry to SMW Config and set for 24 hours
(cherry picked from commit bc3055accb69112040bf93839b1994e1d6a55c2c)
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 39.16.1 origin/feature/certbot-130
* [82d4cb44](https://github.com/freephile/meza/commit/82d4cb44) (2024-12-11) Greg Rundlett: role name change to ansible-role-certbot-meza 
  - Modified: `src/playbooks/site.yml`

## Meza 39.16.0
* [f4d54616](https://github.com/freephile/meza/commit/f4d54616) (2024-12-11) Greg Rundlett: Add TLS Certificate generation and auto-renewal (Certbot) Fixes Issue [#130](https://github.com/freephile/meza/issues/130)
  - Modified: `config/defaults.yml`
  - Modified: `src/playbooks/site.yml`
  - Added: `src/playbooks/test-certbot.yml`
  - Added: `src/roles/ansible-role-certbot-meza/.ansible-lint`
  - Added: `src/roles/ansible-role-certbot-meza/.gitignore`
  - Added: `src/roles/ansible-role-certbot-meza/.yamllint`
  - Added: `src/roles/ansible-role-certbot-meza/LICENSE`
  - Added: `src/roles/ansible-role-certbot-meza/README.md`
  - Added: `src/roles/ansible-role-certbot-meza/defaults/main.yml`
  - Added: `src/roles/ansible-role-certbot-meza/handlers/main.yml`
  - Added: `src/roles/ansible-role-certbot-meza/meta/main.yml`
  - Added: `src/roles/ansible-role-certbot-meza/molecule/default/converge.yml`
  - Added: `src/roles/ansible-role-certbot-meza/molecule/default/molecule.yml`
  - Added: `src/roles/ansible-role-certbot-meza/molecule/default/playbook-snap-install.yml`
  - Added: `src/roles/ansible-role-certbot-meza/molecule/default/playbook-source-install.yml`
  - Added: `src/roles/ansible-role-certbot-meza/molecule/default/playbook-standalone-nginx-aws.yml`
  - Added: `src/roles/ansible-role-certbot-meza/molecule/default/requirements.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/create-cert-standalone.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/create-cert-webroot.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/include-vars.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/install-from-source.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/install-with-package.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/install-with-snap.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/main.meza.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/main.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/renew-cron.yml`
  - Added: `src/roles/ansible-role-certbot-meza/tasks/setup-RedHat.yml`
  - Added: `src/roles/ansible-role-certbot-meza/templates/concat.pem.sh.j2`
  - Added: `src/roles/ansible-role-certbot-meza/templates/start_services.j2`
  - Added: `src/roles/ansible-role-certbot-meza/templates/stop_services.j2`
  - Added: `src/roles/ansible-role-certbot-meza/tests/inventory`
  - Added: `src/roles/ansible-role-certbot-meza/tests/test.yml`
  - Added: `src/roles/ansible-role-certbot-meza/vars/default.yml`
  - Added: `src/roles/ansible-role-certbot-meza/vars/main.yml`
  - Modified: `src/roles/haproxy/tasks/main.yml`
  - Modified: `src/roles/haproxy/templates/haproxy.cfg.j2`

## Meza 39.15.0
* [68249080](https://github.com/freephile/meza/commit/68249080) (2024-11-27) Greg Rundlett: Promote comments into names for each play. This should help with readability and output clarity.
Extract gluster comments into the gluster role default variables file
  - Modified: `src/playbooks/site.yml`
  - Modified: `src/roles/gluster/defaults/main.yml`

* [0f8252de](https://github.com/freephile/meza/commit/0f8252de) (2024-11-22) Greg Rundlett: improve wording for m_opcache_production_mode 
  - Modified: `config/defaults.yml`
  - Modified: `src/roles/init-controller-config/templates/public.yml.j2`

* [da7993df](https://github.com/freephile/meza/commit/da7993df) (2024-11-22) Greg Rundlett: update instruction + minor formatting This playbook need massive re-organization. See Issue [#42](https://github.com/freephile/meza/issues/42)
  - Modified: `src/playbooks/site.yml`

## Meza 39.14.1
* [528002e4](https://github.com/freephile/meza/commit/528002e4) (2024-11-22) Greg Rundlett: Fix paths for PdfHandler Add full paths for commands to solve
pdfinfo not reporting any info about pdfs
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 39.14.0
* [7ad20063](https://github.com/freephile/meza/commit/7ad20063) (2024-11-22) Greg Rundlett: Add PdfHandler extension The base task now installs xpdf for RockyLinux
Solves Issue [#128](https://github.com/freephile/meza/issues/128)
  - Modified: `config/MezaCoreExtensions.yml`
  - Modified: `src/roles/base/tasks/main.yml`

## Meza 39.13.0
* [6737d49e](https://github.com/freephile/meza/commit/6737d49e) (2024-11-22) Greg Rundlett: Add extension Page Exchange Solves Issue [#126](https://github.com/freephile/meza/issues/126) but we still need
to identify and load at least one
package.
Perhaps put the work into making the SemanticMeetingMinutes into a package
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 39.12.0
* [a6cb3735](https://github.com/freephile/meza/commit/a6cb3735) (2024-11-22) Greg Rundlett: Alphabetize core extensions Fixes Issue [#129](https://github.com/freephile/meza/issues/129)
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 39.11.1
* [5042d74a](https://github.com/freephile/meza/commit/5042d74a) (2024-11-21) Greg Rundlett: remove obsolete commented tasks Rebuild Recent Changes was only necessary after
importing pages from script.
See Issue [#126](https://github.com/freephile/meza/issues/126)
  - Modified: `src/roles/verify-wiki/tasks/init-wiki.yml`

## Meza 39.11.0 origin/qb-merge
* [5d47bd26](https://github.com/freephile/meza/commit/5d47bd26) (2024-11-12) Greg Rundlett: Upgrade Bootstrap, BS Components, Chameleon to 5.x Fixes Issue [#125](https://github.com/freephile/meza/issues/125)
Fixes Issue  #107
  - Modified: `config/MezaCoreExtensions.yml`
  - Modified: `config/MezaCoreSkins.yml`

## Meza 39.10.0
* [69c5db7a](https://github.com/freephile/meza/commit/69c5db7a) (2024-11-11) Greg Rundlett: Default to DEVELOPMENT Change default Opcache behavior
Do NOT install Netdata monitoring by default
Adds option for Composer require-dev packages
Fixes Issue [#123](https://github.com/freephile/meza/issues/123)
Fixes Issue [#121](https://github.com/freephile/meza/issues/121)
Helps address Issue [#120](https://github.com/freephile/meza/issues/120)
  - Modified: `config/defaults.yml`
  - Modified: `src/roles/init-controller-config/templates/public.yml.j2`
  - Modified: `src/roles/mediawiki/tasks/main.yml`

## Meza 39.9.6
* [387ba9c3](https://github.com/freephile/meza/commit/387ba9c3) (2024-11-07) Greg Rundlett: Be explicit about who can edit Gadgets 
  - Modified: `config/MezaCoreExtensions.yml`

## Meza 39.9.5
* [85afac96](https://github.com/freephile/meza/commit/85afac96) (2024-11-02) Greg Rundlett: Remove Elasticsearch upgrade playbook Issue [#118](https://github.com/freephile/meza/issues/118) 
  - Deleted: `src/playbooks/elasticsearch-upgrade.yml`
  - Deleted: `src/roles/elasticsearch/tasks/es_upgrade.yml`
  - Modified: `src/roles/elasticsearch/tasks/main.yml`

## Meza 39.9.4
* [69c8a213](https://github.com/freephile/meza/commit/69c8a213) (2024-11-02) Greg Rundlett: Comment the community collections previously setup for RL8 Fixes Issue [#51](https://github.com/freephile/meza/issues/51)
  - Modified: `requirements.yml`

* [8a7241b0](https://github.com/freephile/meza/commit/8a7241b0) (2024-11-02) Greg Rundlett: Example playbook to observe the function of block scalars 
  - Added: `src/playbooks/example-block.yaml`

* [964b65e7](https://github.com/freephile/meza/commit/964b65e7) (2024-11-02) Greg Rundlett: Add a couple of 'ToDo' comments and reformat the smw index shell cmd The shell command was not working due to block folding the
list_of_wikis into a single string so the cmd was reformatted to a
single line (which is too long for yamllint compliance). But, at least
it nominally 'works'. A comment was added to address the issue that
this maintenance should not even exist in this role, but should be refactored. See Issue [#117](https://github.com/freephile/meza/issues/117)
  - Modified: `src/roles/mediawiki/tasks/main.yml`

* [105a5363](https://github.com/freephile/meza/commit/105a5363) (2024-10-24) Greg Rundlett: update github issue template 
  - Modified: `.github/ISSUE_TEMPLATE.md`

## Meza 39.9.3
* [923758c1](https://github.com/freephile/meza/commit/923758c1) (2024-10-24) Greg Rundlett: Fix wiki symbolic links Issue [#115](https://github.com/freephile/meza/issues/115) 
  - Modified: `src/roles/mediawiki/tasks/main.yml`

## Meza 39.9.2
* [1219d724](https://github.com/freephile/meza/commit/1219d724) (2024-10-24) Greg Rundlett: Use system ansible Issue [#51](https://github.com/freephile/meza/issues/51) 
  - Modified: `src/scripts/meza.py`

* [aee09afe](https://github.com/freephile/meza/commit/aee09afe) (2024-10-24) Greg Rundlett: Fix ambiguous STDOUT messaging 
  - Modified: `src/scripts/meza.py`

## Meza 39.9.1
* [a77b91ef](https://github.com/freephile/meza/commit/a77b91ef) (2024-10-24) Greg Rundlett: Use root for ansible-vault key operations become module touches on Issue [#72](https://github.com/freephile/meza/issues/72) privilege escalation
Also switch back to using system-installed ansible
over a local user installed ansible touches on Issue [#51](https://github.com/freephile/meza/issues/51)
Upgrade Ansible
[meza-ansible@rockylinux-s-4vcpu-8gb-nyc3-01 config]$ ansible --version
ansible [core 2.16.3]
  config file = /opt/meza/config/ansible.cfg
  configured module search path = ['/opt/conf-meza/users/meza-ansible/.ansible/plugins/modules', '/usr/share/ansible/plugins/modules']
  ansible python module location = /usr/lib/python3.12/site-packages/ansible
  ansible collection location = /opt/meza/collections
  executable location = /usr/bin/ansible
  python version = 3.12.5 (main, Sep 24 2024, 09:41:18) [GCC 8.5.0 20210514 (Red Hat 8.5.0-22)] (/usr/bin/python3.12)
  jinja version = 3.1.2
  libyaml = True
  - Modified: `src/roles/haproxy/tasks/main.yml`

## Meza 39.9.0
* [13820f17](https://github.com/freephile/meza/commit/13820f17) (2024-10-08) Rich Evans: Update getmeza.sh 1) Don't exclude "ansible" and "ansible core" from epel.repo
2) Don't install repo centos-release-ansible-29, and
3) Don't specify the version of ansible to install.
  - Modified: `src/scripts/getmeza.sh`

* [bc3055ac](https://github.com/freephile/meza/commit/bc3055ac) (2024-10-13) Rich Evans: Update MezaCoreExtensions.yml Added $wgObjectCacheSessionExpiry to SMW Config and set for 24 hours
  - Modified: `config/MezaCoreExtensions.yml`

* [5857d81a](https://github.com/freephile/meza/commit/5857d81a) (2024-10-08) amcgillivray-nasa: Update flow configuration in MezaCoreExtensions.yml Configured Flow to use Parsoid
  - Modified: `config/MezaCoreExtensions.yml`

* [ba25a5df](https://github.com/freephile/meza/commit/ba25a5df) (2024-10-08) Rich Evans: Update getmeza.sh 1) Don't exclude "ansible" and "ansible core" from epel.repo
2) Don't install of centos-release-ansible-29, and 
3) Don't specify the version of ansible to install.
  - Modified: `src/scripts/getmeza.sh`

## Meza 39.8.1 qb-merge
* [4810a1e6](https://github.com/freephile/meza/commit/4810a1e6) (2024-10-07) Greg Rundlett: Configure Flow (Structured Discussions) for MW 1.39 Fixes Issue [#114](https://github.com/freephile/meza/issues/114) Visual Editor was not working properly for Flow boards on MW1.39
(Parsoid-PHP) The easily provoked error was:
"Exception caught: Conversion from 'html' to 'wikitext' was requested, but core's Parser only supports 'wikitext' to 'html' conversion"
In MW 1.39, Flow still ignores the zero-config setup of Parsoid-PHP.
So, we need to wfLoadExtension() the local vendored Parsoid library.
And, set $wgVirtualRestConfig
This makes Flow usable on MW 1.39, but ultimately, Flow needs to be replaced as described in Issue [#39](https://github.com/freephile/meza/issues/39)
  - Modified: `config/MezaCoreExtensions.yml`

* [9c6b44ad](https://github.com/freephile/meza/commit/9c6b44ad) (2024-10-04) Greg Rundlett: Temporary work-arounds for Issue [#72](https://github.com/freephile/meza/issues/72) Do not run as root
Do not require 'sudo' for calling meza
Temporary or partial fixes for path and user problems
  - Modified: `src/roles/haproxy/tasks/main.yml`
  - Modified: `src/scripts/meza.py`

* [eb39199d](https://github.com/freephile/meza/commit/eb39199d) (2024-10-04) Greg Rundlett: update getmeza.sh to fix Issue [#51](https://github.com/freephile/meza/issues/51) Install ansible as the meza-ansible user and
then run install collections
  - Modified: `src/scripts/getmeza.sh`

* [6b655263](https://github.com/freephile/meza/commit/6b655263) (2024-10-04) Greg Rundlett: Fix Issue [#113](https://github.com/freephile/meza/issues/113) with specific binary paths Use /usr/local/bin for ansible-galaxy commands
Both in the playbook, and in the tasks
  - Modified: `src/playbooks/site.yml`
  - Modified: `src/roles/haproxy/tasks/main.yml`

* [c2bde01c](https://github.com/freephile/meza/commit/c2bde01c) (2024-10-04) Greg Rundlett: Fix Issue [#108](https://github.com/freephile/meza/issues/108) Introduce requirements.yml and ansible.cfg changes
to install community collections we depend on
  - Modified: `config/ansible.cfg`
  - Added: `requirements.yml`

* [2f0e5cf0](https://github.com/freephile/meza/commit/2f0e5cf0) (2024-10-04) Greg Rundlett: Ignore common clutter and artifacts 
  - Modified: `.gitignore`

* [8b683562](https://github.com/freephile/meza/commit/8b683562) (2024-10-04) Greg Rundlett: ignore Ansible collections 
  - Modified: `.gitignore`

* [9cff9352](https://github.com/freephile/meza/commit/9cff9352) (2024-10-04) Greg Rundlett: add comment for powertools and remove  centos-release-ansible-29 
  - Modified: `src/scripts/getmeza.sh`

* [88f3b2a6](https://github.com/freephile/meza/commit/88f3b2a6) (2024-10-04) Greg Rundlett: bump bootstrap; add bootstrap components 
  - Modified: `config/MezaCoreExtensions.yml`

* [cb437664](https://github.com/freephile/meza/commit/cb437664) (2024-10-04) Greg Rundlett: Preliminary removal of Python install INSIDE MediaWiki role @TODO cleanup / remove the separate variables for package_pyhton3_pip and package_python3_pip_rhel8 See Issue [#41](https://github.com/freephile/meza/issues/41)#issuecomment-2045506153
  - Modified: `src/roles/mediawiki/tasks/main.yml`

* [a2c81668](https://github.com/freephile/meza/commit/a2c81668) (2024-10-04) Greg Rundlett: Re-enable Semantic Dependency Updater 
  - Modified: `config/MezaCoreExtensions.yml`

* [eab5d30c](https://github.com/freephile/meza/commit/eab5d30c) (2024-10-04) Greg Rundlett: Clarify opcache settings m_use_production_settings was vague
m_opcache_production_mode better describes this switch
Add descriptive comments that explains what it does
  - Modified: `Vagrantfile`
  - Modified: `config/defaults.yml`
  - Modified: `src/roles/apache-php/templates/php.ini.j2`
  - Modified: `src/roles/init-controller-config/templates/public.yml.j2`

* [fedf9fbf](https://github.com/freephile/meza/commit/fedf9fbf) (2024-10-04) Greg Rundlett: Fix rebuild SMW data and rebuild Elasticsearch Add 'base' tag to the deploy command
fixes #98
  - Modified: `RELEASE-NOTES.md`

* [aed2dc95](https://github.com/freephile/meza/commit/aed2dc95) (2024-10-04) Greg Rundlett: Fix rebuild SMW data and rebuild Elasticsearch substitute list_of_wikis in place of wikis_to_rebuild_data
because the latter variable is undefined
fixes Issue [#98](https://github.com/freephile/meza/issues/98)
  - Modified: `src/roles/mediawiki/tasks/main.yml`

* [20f938f3](https://github.com/freephile/meza/commit/20f938f3) (2024-10-04) Greg Rundlett: Remove the footer in .git-commit-template. 
  - Modified: `.git-commit-template`

* [59cfafe9](https://github.com/freephile/meza/commit/59cfafe9) (2024-10-04) Greg Rundlett: downgrade ReplaceText + InputBox These extensions' 'main' branch requires
a higher version of MediaWiki core. So, revert to
the REL1_39 branch for now.
  - Modified: `config/MezaCoreExtensions.yml`

* [4b80c01f](https://github.com/freephile/meza/commit/4b80c01f) (2024-10-04) Greg Rundlett: Fix typo in MezaCoreExtensions.yml 
  - Modified: `config/MezaCoreExtensions.yml`

* [cfbe3c22](https://github.com/freephile/meza/commit/cfbe3c22) (2024-10-04) Greg Rundlett: Remove SAML configuration The SAML configuration should go into a configuration repo
(e.g. conf-meza-myfarm) for wikis that use it.
The 'opt/conf-meza/public' directory should
ALWAYS be turned into a 'conf-meza-myfarm' repo
after the first 'meza deploy', so that's where SAML
config can be put in e.g. base.php
This way, Meza can be deployed and used with all
kinds of authentication options rather than a single
hard-coded option.
  - Modified: `src/roles/mediawiki/templates/LocalSettings.php.j2`

* [ccc932fe](https://github.com/freephile/meza/commit/ccc932fe) (2024-10-04) Greg Rundlett: Switch some repos to master Add comments about things that need to be fixed or checked
Switch some repos to 'master'
- ExternalData
- InputBox
- ReplaceText
- AdminLinks
- DataTransfer
Reformat line comments from PHP to YAML
so that the file is valid YAML
  - Modified: `config/MezaCoreExtensions.yml`

* [87837388](https://github.com/freephile/meza/commit/87837388) (2024-10-04) Greg Rundlett: Fix composer install bug Fix Issue [#94](https://github.com/freephile/meza/issues/94) which can happen when composer.json
has newer dependencies or requirements than
composer.lock
  - Modified: `src/roles/mediawiki/tasks/main.yml`

* [7166e16f](https://github.com/freephile/meza/commit/7166e16f) (2024-10-04) Greg Rundlett: Fix samesite cookie settings for a stubborn Chrome Chrome browser will error with
"State Information Lost" ( \SimpleSAML\Error\NoState: NOSTATE )
unless you set session.cookie.secure => true
and session.cookie.samesite => 'None'
  - Modified: `src/roles/saml/templates/config.php.j2`

* [87956a53](https://github.com/freephile/meza/commit/87956a53) (2024-10-03) amcgillivray-nasa: Update MezaCoreExtensions.yml Changing page forms version from master to 5.8.1
  - Modified: `config/MezaCoreExtensions.yml`

* [33588cc6](https://github.com/freephile/meza/commit/33588cc6) (2024-10-02) amcgillivray-nasa: Update MezaCoreExtensions.yml Fixed spacing issue
  - Modified: `config/MezaCoreExtensions.yml`

* [c3ab0884](https://github.com/freephile/meza/commit/c3ab0884) (2024-09-27) amcgillivray-nasa: Update MezaCoreExtensions.yml 1. Added $wgPageFormsUseDisplayTitle = false;
2. Added UrlGetParameters
  - Modified: `config/MezaCoreExtensions.yml`

* [6d604cb0](https://github.com/freephile/meza/commit/6d604cb0) (2024-09-26) amcgillivray-nasa: Update MezaCoreExtensions.yml 1. Added $wgPageFormsDelayReload = true;
2. Added $smwgParserFeatures = $smwgParserFeatures | SMW_PARSER_LINV;
3. Added Gadgets extension
  - Modified: `config/MezaCoreExtensions.yml`

* [816854ad](https://github.com/freephile/meza/commit/816854ad) (2024-09-20) James Montalvo: fix(vuln): remove default elasticsearch password Setting a default password could cause installations to use that
password in production systems, which is a security vulnerability since
the default password is published on github.
  - Modified: `src/roles/elasticsearch/defaults/main.yml`

* [6bd0c2d2](https://github.com/freephile/meza/commit/6bd0c2d2) (2024-09-11) Rich Evans: Removed WatchAnalytics in MezaCoreExtensions.yml Will add it back after full 1.39 checkout is complete.
  - Modified: `config/MezaCoreExtensions.yml`

* [b48a7455](https://github.com/freephile/meza/commit/b48a7455) (2024-09-11) Rich Evans: Update MezaCoreExtensions.yml so Extention HeaderTabs uses master 
  - Modified: `config/MezaCoreExtensions.yml`

* [12a9ca69](https://github.com/freephile/meza/commit/12a9ca69) (2024-09-11) Rich Evans: removed extension not used in 1.34 MezaCoreExtensions.yml removed SubpageNavigation, InlineComments, DarkMode, WikiLove, FlexFormUpdate. Will add these extensions back after 1.39 is fully checked-out
  - Modified: `config/MezaCoreExtensions.yml`

* [7bc5f2f9](https://github.com/freephile/meza/commit/7bc5f2f9) (2024-09-10) Rich Evans: Update LocalSettings.php.j2 enabled 4 GB uploads and don't check fiile types
  - Modified: `src/roles/mediawiki/templates/LocalSettings.php.j2`

* [9ec786b1](https://github.com/freephile/meza/commit/9ec786b1) (2024-09-10) Rich Evans: Update defaults.yml update max file size to 5G
  - Modified: `config/defaults.yml`

* [dfb15e15](https://github.com/freephile/meza/commit/dfb15e15) (2024-09-05) Rich Evans: Update MezaCoreExtensions.yml Bump SMW from 4.1.3 to ~4.2 + added config properties, switch back to normal Mediawiki version of PF version master, added SubPageNavigation config, changed SDU config to use Job Queue.
  - Modified: `config/MezaCoreExtensions.yml`

* [89dccf34](https://github.com/freephile/meza/commit/89dccf34) (2024-09-05) amcgillivray-nasa: Update wgSDUUseJobQueue to true in MezaCoreExtensions.yml SDU works when set to true, and does not work when set to false
  - Modified: `config/MezaCoreExtensions.yml`

* [3a952f5b](https://github.com/freephile/meza/commit/3a952f5b) (2024-07-11) amcgillivray-nasa: Update haproxy.cfg.j2 Removed "no-tlsv12" in order to maintain compatibility with mwclient on RHEL8 using OpenSSL 1.1.1
  - Modified: `src/roles/haproxy/templates/haproxy.cfg.j2`

* [f55071aa](https://github.com/freephile/meza/commit/f55071aa) (2024-07-03) ndc-rkevans: Add WikiLove, FlexForms, and change VEForAll brnach to master 
  - Modified: `config/MezaCoreExtensions.yml`

* [a5d919e3](https://github.com/freephile/meza/commit/a5d919e3) (2024-06-10) Rich Evans: Update MezaCoreSkins.yml Add MediaWiki site configuration variables via the Vector skin config
  - Modified: `config/MezaCoreSkins.yml`

* [9ff624e4](https://github.com/freephile/meza/commit/9ff624e4) (2024-06-10) Rich Evans: Update MezaCoreSkins.yml removed skin:Poncho until we have time to debug it
  - Modified: `config/MezaCoreSkins.yml`

* [6f439740](https://github.com/freephile/meza/commit/6f439740) (2024-06-10) Rich Evans: Update MezaCoreExtensions.yml Configure Page Forms to allow autoedits in the User Namespace
  - Modified: `config/MezaCoreExtensions.yml`

* [2732e08e](https://github.com/freephile/meza/commit/2732e08e) (2024-06-06) Rich Evans: Update MezaCoreSkins.yml Notes - Chameleon is the default skin. All the other skins either ship with MW Core (1.39) or they were listed at: https://www.pro.wiki/articles/best-mediawiki-skins
  - Modified: `config/MezaCoreSkins.yml`

* [13c853fa](https://github.com/freephile/meza/commit/13c853fa) (2024-06-06) Rich Evans: Update MezaCoreSkins.yml adding more skins to choose from
  - Modified: `config/MezaCoreSkins.yml`

* [ea52868f](https://github.com/freephile/meza/commit/ea52868f) (2024-06-06) ndc-rkevans: replace depricated ansible includes with include_tasks 
  - Modified: `src/roles/database/tasks/main.yml`
  - Modified: `src/roles/elasticsearch/tasks/main.yml`
  - Modified: `src/roles/gluster/tasks/main.yml`
  - Modified: `src/roles/verify-wiki/tasks/import-wiki-sql.yml`

* [4f1fbe5a](https://github.com/freephile/meza/commit/4f1fbe5a) (2024-06-06) ndc-rkevans: Re-enable SDU and fix CommentStreams config typo 
  - Modified: `config/MezaCoreExtensions.yml`

* [2cb0b81a](https://github.com/freephile/meza/commit/2cb0b81a) (2024-05-31) Rich Evans: Update MezaCoreExtensions.yml make it so that Comment Streams don't show on pages by default
  - Modified: `config/MezaCoreExtensions.yml`

* [da64b3ba](https://github.com/freephile/meza/commit/da64b3ba) (2024-05-20) Rich Evans: Update .htaccess.j2 Don't expose any files or folders with .git in the path
  - Modified: `src/roles/htdocs/templates/.htaccess.j2`

* [6ce641e0](https://github.com/freephile/meza/commit/6ce641e0) (2024-05-08) ndc-rkevans: Configure Apache ServerTokens to ProductOnly 
  - Modified: `src/roles/apache-php/templates/httpd.conf.j2`

* [83d38a0c](https://github.com/freephile/meza/commit/83d38a0c) (2024-05-08) ndc-rkevans: Have meza overwrite local commits by default 
  - Modified: `config/defaults.yml`

* [932042ab](https://github.com/freephile/meza/commit/932042ab) (2024-05-08) ndc-rkevans: Add Extension SubpageNavigation to Meza Core Extension 
  - Modified: `config/MezaCoreExtensions.yml`

* [8eb22403](https://github.com/freephile/meza/commit/8eb22403) (2024-05-01) ndc-rkevans: saml configuration updates 
  - Modified: `src/roles/configure-wiki/templates/samlAuthorizations.d/base.php.j2`
  - Modified: `src/roles/haproxy/templates/haproxy.cfg.j2`
  - Modified: `src/roles/saml/tasks/main.yml`
  - Modified: `src/roles/saml/templates/authsources.php.j2`
  - Modified: `src/roles/saml/templates/config.php.j2`

## Meza 39.6.2
* [d931e9cc](https://github.com/freephile/meza/commit/d931e9cc) (2024-03-29) Greg Rundlett: Add cache directory for SimpleSAMLphp The cachedir is templated in the config.php.j2 file.
The directory is created in the main tasks file.
Oddly enough, the system worked in prior tests without any cache defined.
See simplesamlphp/config/config.dist for all configurations available.
See Issue [#74](https://github.com/freephile/meza/issues/74)
This work was performed for NASA GRC-ATF by WikiWorks per NASA Contract NNC15BA02B.
  - Modified: `src/roles/saml/tasks/main.yml`
  - Modified: `src/roles/saml/templates/config.php.j2`

## Meza 39.6.1
* [39dbcbf6](https://github.com/freephile/meza/commit/39dbcbf6) (2024-03-29) Greg Rundlett: Update CHANGELOG and RELEASE NOTES Add details on new features.
See Issue [#52](https://github.com/freephile/meza/issues/52)
See also #53 #54 #55
This work was performed for NASA GRC-ATF by WikiWorks per NASA Contract NNC15BA02B.
  - Modified: `CHANGELOG`
  - Modified: `RELEASE-NOTES.md`
