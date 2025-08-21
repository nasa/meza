# Meza AI Coding Agent Instructions

## ⚠️ CRITICAL: MANDATORY LINTING REQUIREMENT ⚠️

**BEFORE making ANY changes to YAML files, Ansible playbooks, or roles, you MUST:**

1. **Run linting**: `./src/scripts/lint-files.sh <file>` to check current state
2. **After changes**: Run linting again to verify no new errors introduced
3. **Fix all errors**: Before proceeding or suggesting changes to the user
4. **See**: `LINTING.md` for complete linting documentation

**Example Workflow:**
```bash
# Check before editing
./src/scripts/lint-files.sh src/roles/mediawiki/tasks/main.yml
# Make changes
# Check after editing
./src/scripts/lint-files.sh src/roles/mediawiki/tasks/main.yml
```

## Project Overview
Meza is the leading MediaWiki deployment automation platform built with Ansible. It provides a single `meza` command-line interface to deploy, manage, and maintain complex MediaWiki environments across multiple servers with features like VisualEditor, CirrusSearch, and many other carefully selected extensions.

## Architecture & Key Components

### Core Structure
- **Primary CLI**: `src/scripts/meza.py` - Main Python CLI with commands like `deploy`, `backup`, `create`, `setup-env`
- **Ansible Core**: `src/playbooks/site.yml` - Main deployment playbook that orchestrates all roles
- **Configuration Layer**: `config/` directory with `defaults.yml`, `paths.yml`, and OS-specific configs
- **Role-Based Architecture**: `src/roles/` contains 40+ specialized Ansible roles for different components

### Key Architectural Patterns

#### Path Resolution System
All paths are dynamically resolved from the meza binary location:
```python
# In meza.py and set-vars role
install_dir = dirname(dirname(dirname(dirname(realpath(which meza)))))
# Typically resolves to /opt, but configurable
```

#### Multi-Environment Support
- Environments defined in `/opt/conf-meza/secret/<env>/` and `/opt/conf-meza/public/<env>/`
- Each environment has separate inventory files, secrets, and configuration
- Deploy locks prevent concurrent deployments: `/opt/data-meza/env-{env}-deploy.lock`

#### Command Dispatch Pattern
```python
# meza.py uses function naming convention
def meza_command_deploy(argv):     # maps to: meza deploy
def meza_command_setup_env(argv):  # maps to: meza setup-env
def meza_command_backup(argv):     # maps to: meza backup
```

## Critical Development Workflows

### Local Development
```bash
# Setup development environment
meza setup-dev
# Deploy to local environment (first run creates the 'demo' wiki)
meza deploy monolith -vvv
# Create test wiki
meza create wiki testwiki
```

### Testing Commands (Not Obvious from Files)
```bash
# Test Ansible syntax before deployment
ANSIBLE_CONFIG=/opt/meza/config/ansible.cfg ansible-playbook /opt/meza/src/playbooks/site.yml --syntax-check

# Quick config-only deploy (skip slow tasks)
meza deploy <env> --tags mediawiki --skip-tags latest,update.php,verify-wiki

# Force debug mode for troubleshooting
# Set in /opt/conf-meza/public/<env>/public.yml:
m_force_debug: true
```

### Deployment Lock Management
```bash
# Check if environment is deploying
meza deploy-check <env>  # returns 0=not deploying, 1=deploying
# Manually unlock stuck deployments
meza deploy-unlock <env>
# Kill running deployment
meza deploy-kill <env>
```

## Project-Specific Conventions

### Configuration Hierarchy (Order of Precedence)
1. Environment-specific: `/opt/conf-meza/secret/<env>/secret.yml`
2. Public environment: `/opt/conf-meza/public/<env>/public.yml`
3. OS-specific: `config/RedHat.yml` or `config/Debian.yml`
4. Core defaults: `config/defaults.yml`
5. Path definitions: `config/paths.yml`

### Ansible Role Patterns
- **set-vars role**: Always runs first to establish all path variables and config hierarchy
- **sync-configs role**: Synchronizes configuration between controller and target servers
- **Role dependencies**: Many roles depend on `set-vars` being run first
- **Jinja2 templating**: Extensive use of `{{ m_variable }}` pattern for paths and configs

### MediaWiki Integration Points
- **Wiki creation**: `configure-wiki` role handles LocalSettings.php generation
- **Extension management**: `config/MezaCoreExtensions.yml` defines default extension set
- **Local Extension management**: `conf-meza/public/MezaLocalExtensions.yml` defines default *additional* extension set managed by the local instance
- **Update workflows**: `update.php` role handles MediaWiki database updates
- **File uploads**: GlusterFS used for multi-server setups (`m_uploads_dir` override)

## Development Guidelines

### When Modifying meza.py
- Add new commands using `meza_command_<name>` function pattern
- Always validate argv length before accessing arguments
- Use `defaults` dictionary for consistent path handling
- Implement proper lock file management for deployment-related commands

### When Creating Ansible Roles
- Always include `set-vars` as dependency or include it in main playbook
- Use `{{ m_install }}/meza/` prefix for all Meza-related paths
- Follow the sync-configs pattern for multi-server deployments
- Test with both single server and multi-server inventories

### PHP Code Standards (MediaWiki Integration)
- **Follow MediaWiki coding conventions**: Use MediaWiki's PSR-2 based coding standards
- **Security practices**: Always sanitize user input, use MediaWiki's built-in escaping functions
- **Database interactions**: Use MediaWiki's database abstraction layer, never raw SQL
- **Namespace usage**: Use proper MediaWiki namespaces and avoid global scope pollution
- **Documentation**: Include PHPDoc comments for all functions and classes
- **Error handling**: Use MediaWiki's logging and error handling mechanisms

### Configuration Changes
- Test changes with `--check` mode first
- Use `--tags` and `--skip-tags` for targeted deployments
- Remember config hierarchy: secret.yml overrides public.yml overrides defaults.yml
- Validate YAML syntax with yamllint (CI requirement)

### Testing Requirements
- All changes must pass YAML linting (`yamllint`)
- Manual testing should include: wiki creation, VisualEditor, search, file uploads
- For security changes: test image access permissions with anonymous users
- Use Docker test framework: `tests/docker/run-tests.sh monolith-from-scratch` (NOTE: this is experimental)

## External Dependencies & Integration
- **MediaWiki Core**: Git SEMVER, submodules, and composer dependencies for version management
- **Composer**: PHP dependency management for MediaWiki extensions
- **Elasticsearch**: Search backend requiring specific Java/memory configuration
- **GlusterFS**: Distributed file system for multi-server uploads
- **HAProxy**: Load balancing for multi-server deployments
- **Certbot/Let's Encrypt**: SSL certificate automation

## Common Pitfalls
- Don't modify files in `/opt/meza/` during deployment (use `m_local_secret` paths)
- Always use absolute paths in Ansible (relative paths cause issues)
- Lock files must be cleaned up on deployment interruption
- Environment-specific secrets should never be committed to git
- Multi-server deployments require careful SSH key management between nodes

## Mandatory Code Quality Requirements

### **CRITICAL: Always Run Linting Before Suggesting Changes**
Before making ANY changes to YAML files, Python files, or Ansible playbooks/roles, you MUST:

1. **Run linting tools** to check for syntax and style errors
2. **Verify changes don't introduce new linting errors**
3. **Use the project's linting script**: `./src/scripts/lint-files.sh [files...]`

#### Linting Commands
```bash
# Lint specific files
./src/scripts/lint-files.sh src/roles/mediawiki/tasks/main.yml
./src/scripts/lint-files.sh src/playbooks/verify-permissions.yml

# Lint all YAML files in project
./src/scripts/lint-files.sh

# Manual linting commands
yamllint <file.yml>                    # For YAML syntax/style
ansible-lint <playbook.yml>            # For Ansible best practices
```

#### Pre-Edit Workflow
1. **BEFORE editing any file**: Run `./src/scripts/lint-files.sh <file>` to check current state
2. **AFTER making changes**: Run linting again to verify no new errors introduced
3. **If linting fails**: Fix all errors before proceeding or suggesting the changes to user

#### Linting Configuration
- **yamllint config**: `.yamllint` (140 char line length, relaxed rules)
- **ansible-lint config**: `.ansible-lint` (production profile, FQCN enforcement)
- **Excluded paths**: `.venv/`, `vendor/`, `tests/docker/`, external roles

### Code Quality Standards
- **YAML**: Follow yamllint rules, use consistent indentation (2 spaces)
- **Ansible**: Use FQCN for modules (`ansible.builtin.file` not `file`)
- **Python**: Follow PEP 8 style guidelines
- **No trailing whitespace** in any files
- **Proper file permissions**: Executable scripts must have `chmod +x`

## Debugging Commands
```bash
# View deployment logs
meza deploy-tail <env>
# Check deployment status
meza deploy-log <env>
# Verify configuration loading
ansible-playbook site.yml --list-tasks
# Test specific role in isolation
ansible-playbook site.yml --tags <role-name> --check
```
