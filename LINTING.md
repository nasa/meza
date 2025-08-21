# Meza Linting System

## Overview

The Meza project includes comprehensive linting tools to ensure code quality and consistency across all YAML files, Ansible playbooks, and roles. The linting system is designed to catch syntax errors, style violations, and Ansible best practices violations before they're committed to the repository.

## Available Tools

### 1. Automated Linting Script
**Location**: `src/scripts/lint-files.sh`

This is the main linting tool that automatically detects file types and runs appropriate linters:

```bash
# Lint specific files
./src/scripts/lint-files.sh src/roles/mediawiki/tasks/main.yml
./src/scripts/lint-files.sh src/playbooks/verify-permissions.yml

# Lint all YAML files in the project
./src/scripts/lint-files.sh

# Lint multiple specific files
./src/scripts/lint-files.sh config/defaults.yml src/playbooks/site.yml
```

### 2. Individual Linters

#### YAML Linter (yamllint)
- **Configuration**: `.yamllint`
- **Usage**: `yamllint <file.yml>`
- **Purpose**: Checks YAML syntax, formatting, and style

#### Ansible Linter (ansible-lint)
- **Configuration**: `.ansible-lint`
- **Usage**: `ansible-lint <playbook.yml>`
- **Purpose**: Checks Ansible best practices, FQCN usage, and playbook structure

### 3. Git Pre-commit Hook
**Location**: `.git/hooks/pre-commit`

Automatically runs linting on staged YAML files before commits:
- Prevents commits with linting errors
- Can be bypassed with `git commit --no-verify` if needed
- Uses the same virtual environment as the main linting script

## Configuration Files

### .yamllint
```yaml
---
extends: default

rules:
  line-length:
    max: 140
    level: warning
  comments:
    min-spaces-from-content: 1 # prettier compatibility
```

### .ansible-lint
```yaml
---
profile: production  # Strictest linting profile

exclude_paths:
  - .venv/
  - .cache/
  - vendor/
  - tests/docker/
  - src/roles/geerlingguy.kibana/
  - src/roles/ansible-role-certbot-meza/

skip_list:
  - yaml[line-length]  # Allow longer lines for readability
  - name[casing]      # Allow flexible naming conventions
  - risky-file-permissions  # Some meza tasks require specific permissions
```

## Virtual Environment Integration

The linting system automatically uses the project's virtual environment (`.venv/`) when available:

- **Automatic detection**: Checks for `.venv/bin/activate`
- **Fallback**: Uses system-wide packages if virtual environment is not available
- **Consistent tooling**: Ensures all developers use the same linter versions

## Agent Rules for Code Quality

### Mandatory Pre-Edit Workflow

**BEFORE editing any YAML file**:
1. Run `./src/scripts/lint-files.sh <file>` to check current state
2. Note any existing errors

**AFTER making changes**:
1. Run linting again to verify no new errors introduced
2. Fix all linting errors before proceeding
3. Only suggest changes to users after linting passes

### Example Agent Workflow

```bash
# 1. Check file before editing
./src/scripts/lint-files.sh src/roles/example/tasks/main.yml

# 2. Make changes to file
# ... edit file ...

# 3. Verify changes don't introduce new errors
./src/scripts/lint-files.sh src/roles/example/tasks/main.yml

# 4. If errors exist, fix them before presenting to user
```

## Common Linting Issues and Fixes

### FQCN (Fully Qualified Collection Names)
**Error**: `Use FQCN for builtin module actions (file)`
**Fix**: Change `file:` to `ansible.builtin.file:`

### Jinja2 Spacing
**Error**: `Jinja2 spacing could be improved`
**Fix**: Add spaces around pipe operators: `{{ var|filter }}` → `{{ var | filter }}`

### Trailing Spaces
**Error**: `Trailing spaces`
**Fix**: Remove whitespace at end of lines

### Variable Naming in Roles
**Error**: `Variables names from within roles should use rolename_ as a prefix`
**Fix**: Prefix register variables with role name: `register: mediawiki_result`

## Installation Requirements

The linting tools are included in the project's virtual environment:
```bash
# Install in virtual environment (recommended)
source .venv/bin/activate
pip install ansible-lint yamllint

# Or install globally
pip install --user ansible-lint yamllint
```

## Troubleshooting

### Linting Tool Not Found
If you see "command not found" errors:
1. Ensure virtual environment is activated: `source .venv/bin/activate`
2. Install missing tools: `pip install ansible-lint yamllint`
3. Check PATH includes `.venv/bin/`

### Configuration Conflicts
If ansible-lint reports configuration issues:
1. Check `.ansible-lint` configuration file
2. Ensure no conflicting configurations in subdirectories
3. Update configuration to match ansible-lint requirements

### Performance Issues
For large projects:
1. Use specific file paths instead of linting everything
2. Exclude unnecessary paths in `.ansible-lint`
3. Consider running linting in parallel for multiple files

## Integration with Development Workflow

### VS Code Integration
Add to `.vscode/tasks.json`:
```json
{
    "label": "Lint Current File",
    "type": "shell",
    "command": "./src/scripts/lint-files.sh",
    "args": ["${file}"],
    "group": "build",
    "presentation": {
        "reveal": "always",
        "panel": "new"
    }
}
```

### CI/CD Integration
For automated testing:
```bash
# In CI pipeline
./src/scripts/lint-files.sh
exit_code=$?
if [ $exit_code -ne 0 ]; then
    echo "Linting failed! Please fix errors before merging."
    exit 1
fi
```
