CONTRIBUTING
============

There are several ways you can help contribute to meza.

## Report bugs

You may help us by reporting bugs and feature request via the [issue tracker](/issues). 

## Improve documentation

Meza documentation may not be in lock-step with the actual code or features. Please make updates to documentation at https://www.mediawiki.org/wiki/Meza It is a wiki - be bold! You can also work on or report a [documentation issue](/issues?q=is%3Aissue%20label%3Adoc) in the issue tracker.

## Provide patches

We have a long list of features to add and bugs to fix. We'd greatly appreciate any assistance making Meza better.

## Testing

Meza pulls together many complex systems. To ensure higher quality, we've begun
implementing Continuous Integration and Continuous Delivery (CI/CD) measures
and automated quality gates such as commit-hooks and GitHub Actions.

For Ansible, there are [test strategies](https://docs.ansible.com/ansible/latest/reference_appendices/test_strategies.html) that should be followed in this project.

The first line of testing is "linting". We use ansible-lint and 
yamllint via a convenient script: [lint-files.sh](/src/scripts/lint-files.sh) 
See the documentation at [LINTING.md](/LINTING.md)

Call it like so:
```bash
./src/scripts/lint-files.sh /opt/meza/src/playbooks/site.yml
```

In order to properly run the linters, you **should** establish a Python virtual environment named '.venv' in the project root for Meza:
```bash
cd /opt/meza
python3.6 -m venv .venv
source .venv/bin/activate
```

Then, simply running `lint-files.sh` will check for the linters and install them
for you. (ie. `pip install ansible-lint yamllint`)

Any time you need to re-establish your Python Virtual Environment, just 

```bash
cd /opt/meza
source .venv/bin/activate
```

### Syntax Check
The second line of defense in Ansible is to run a playbook **syntax-check**.
To automatically pick up the correct Ansible configuration, change to the 'config'
directory first. You do not need to do this on a provisioned host (ie. controller).
However, your local developer workstation must be setup with some Ansible collections.

```bash
# assumes you are working with a local source repo at ~/src/meza
# pickup our ansible.cfg
cd ~/src/meza/config
# install our requirements
ansible-galaxy collection install -vvv -r ../requirements.yml
# show the installed Ansible Collections
ansible-galaxy collection list
# perform a syntax check
ansible-playbook ../src/playbooks/site.yml --syntax-check
```

```bash
# DO NOT DO this cumbersome approach that ends up installing collections globally
ansible-galaxy collection install ansible.posix
ansible-galaxy collection install gluster.gluster
ansible-galaxy collection install community.mysql
ANSIBLE_CONFIG=./config/ansible.cfg ansible-playbook src/playbooks/site.yml --syntax-check
```

### Dry Run
You can run an Ansible playbook in "**check mode**" to see what changes would be made,
without actually making them.

To do a dry run, you must use or target an actual (provisioned by Meza) controller or
similar node, not your local workstation. Although no changes will be made,
playbook tests and conditions still must be met to succeed.

You should run the check as the 'meza-ansible' user. If you run as root, it will fail due
to incorrect ownership of git repositories. **In general, always become the meza-ansible user
to work on Meza's source tree and to issue meza commands.**

```bash
# run an interactive login shell as `meza-ansible` (reads the user's shell profile files, etc.)
sudo -i -u meza-ansible
# change to the project's ansible configuration directory to pickup ansible.cfg
cd /opt/meza/config
ansible-playbook /opt/meza/src/playbooks/site.yml --check -c local -i /opt/conf-meza/secret/monolith/hosts
```

### Molecule
Molecule is a framework for [testing Ansible](https://wiki.freephile.org/wiki/Ansible#Testing) roles with scenarios, including lint, syntax, and idempotence tests. It is typically used for roles, but can be adapted
for playbooks. We will endeavor to better organize Meza playbooks and roles with
Molecule tests for all. Molecule works with various drivers like Docker, Podman
and Vagrant. This is very similar to running CI through GitHub Actions and Runners.
So, a pre-requisite to using Molecule will be to ensure a good Meza vagrantfile
and/or Dockerfile.

### GitHub Actions
We've started to use GitHub Actions to run automated tests on commits and merges.

### Testinfra?
We probably don't want to go down the path of Py.test and Testinfra until all
the "low-hanging fruit" of testing Ansible with Ansible is complete. 
But maybe it's a future goal.

### Testing suggestions
(Ed. note: I'm leaving these here, but we really do not need to test things like
Visual Editor on every change in Meza. And, tests that we *do* want to perform need to be
automated with e.g. Selenium or Playwright, the MediaWiki API, Ansible, PHPUnit
and CI/CD integration. The goal is to turn this manual checklist into a repeatable
automated test suite.)

#### Minimal testing

These tests should be performed on all changes

* Set `m_force_debug: true` in `/opt/conf-meza/public/public.yml`
* Create page with wikitext editor
* Create page with VisualEditor
* Verify adding images to pages with VisualEditor
* Verify adding edit summary in VisualEditor
* Verify search works
* Verify ElasticSearch works by searching with a typo (e.g. search for "Test Paeg" when looking for "Test Page")
* Verify file uploads work
* Verify thumbnailing works (upload a larger image and verify small images are generated)
* Verify `sudo meza create wiki` successfully creates a wiki

#### Desired testing

The following tests should be performed if time allows, or if a change is likely to affect any test.

* Verify `import-wikis.sh` imports multiple wikis
* Verify image security: users unable to view images when not logged in
  * Test access to images when not logged into the wiki (use another browser)
    * Go to a file page with a logged in user and click the image and open in a new tab; verify you can view the image
    * Open that same image in another browser without being logged in; verify you can view the image
  * Create `/opt/conf-meza/public/postLocalSettings.d/permissions.php` to remove anonymous viewing:
    * `$wgGroupPermissions['*']['read'] = false;`
    * After each change run `sudo meza deploy <env> --tags mediawiki --skip-tags latest,update.php,verify-wiki` to quickly pick up config changes
  * Test access to images from both browsers:
    * Verify logged in user can view image
    * Verify anonymous user CANNOT view image
  * Attempt to directly access image via URI like `http://example.com/wikis/<wiki-id>/images/a/a1/Image.png`
    * Verify logged in user CANNOT view image
    * Verify anonymous user CANNOT view image

#### Pre-release testing requirements

TBD
