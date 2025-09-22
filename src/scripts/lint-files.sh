#!/bin/bash
#
# Meza Linting Script - Run appropriate linters on files
# Usage: ./lint-files.sh [file1] [file2] ... or ./lint-files.sh (for all files)
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[LINT]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"

# Change to project root
cd "$PROJECT_ROOT"

# Activate virtual environment if it exists
VENV_PATH="$PROJECT_ROOT/.venv"
if [ -d "$VENV_PATH" ] && [ -f "$VENV_PATH/bin/activate" ]; then
    print_status "Activating virtual environment: $VENV_PATH"
    # shellcheck source=/dev/null
    source "$VENV_PATH/bin/activate"
    # Add venv bin to PATH
    export PATH="$VENV_PATH/bin:$PATH"
else
    print_warning "Virtual environment not found at $VENV_PATH"
    print_status "Using system-wide Python packages"
fi

# Check if linting tools are available
check_tools() {
    local missing_tools=()
    local install_cmd=""

    # Determine installation method based on environment
    if [ -n "$VIRTUAL_ENV" ] || [ -d "$PROJECT_ROOT/.venv" ]; then
        install_cmd="pip install ansible-lint yamllint"
    else
        install_cmd="pip install --user ansible-lint yamllint"
    fi

    if ! command -v ansible-lint >/dev/null 2>&1; then
        missing_tools+=("ansible-lint")
    fi

    if ! command -v yamllint >/dev/null 2>&1; then
        missing_tools+=("yamllint")
    fi

    if [ ${#missing_tools[@]} -gt 0 ]; then
        print_warning "Missing linting tools: ${missing_tools[*]}"
        print_status "Install with: $install_cmd"
        return 1
    fi

    return 0
}

# Function to lint YAML files
lint_yaml() {
    local file="$1"
    print_status "Linting YAML file: $file"

    if yamllint "$file"; then
        print_success "YAML lint passed: $file"
        return 0
    else
        print_error "YAML lint failed: $file"
        return 1
    fi
}

# Function to lint Ansible files
lint_ansible() {
    local file="$1"
    print_status "Linting Ansible file: $file"

    # Use ansible-lint with project config
    if ansible-lint "$file"; then
        print_success "Ansible lint passed: $file"
        return 0
    else
        print_error "Ansible lint failed: $file"
        return 1
    fi
}

# Function to determine file type and run appropriate linter
lint_file() {
    local file="$1"
    local exit_code=0

    # Skip files in excluded directories
    if [[ "$file" =~ ^\.venv/ ]] || [[ "$file" =~ ^vendor/ ]] || [[ "$file" =~ ^\.cache/ ]]; then
        print_status "Skipping excluded file: $file"
        return 0
    fi

    # Check if file exists
    if [ ! -f "$file" ]; then
        print_warning "File not found: $file"
        return 0
    fi

    # Determine file type and lint accordingly
    case "$file" in
        *.yml|*.yaml)
            # Check if it's an Ansible file
            if [[ "$file" =~ ^src/playbooks/ ]] || [[ "$file" =~ ^src/roles/.*/tasks/ ]] || [[ "$file" =~ ^src/roles/.*/handlers/ ]] || [[ "$file" =~ ^src/roles/.*/vars/ ]] || [[ "$file" =~ ^src/roles/.*/defaults/ ]]; then
                lint_ansible "$file" || exit_code=1
            else
                lint_yaml "$file" || exit_code=1
            fi
            ;;
        *.py)
            print_status "Python linting not configured for: $file"
            ;;
        *)
            print_status "No linter configured for: $file"
            ;;
    esac

    return $exit_code
}

# Main function
main() {
    print_status "Starting Meza file linting..."

    # Check if tools are available
    if ! check_tools; then
        exit 1
    fi

    local files=()
    local exit_code=0

    # If no arguments provided, find all relevant files
    if [ $# -eq 0 ]; then
        print_status "No files specified, finding all YAML files..."
        # Find all YAML files, excluding certain directories
        mapfile -t files < <(find . -name "*.yml" -o -name "*.yaml" | grep -v -E "^\./(\.venv|vendor|\.cache|tests/docker)" | sort)
    else
        files=("$@")
    fi

    print_status "Found ${#files[@]} files to lint"

    # Lint each file
    local failed_files=()
    for file in "${files[@]}"; do
        if ! lint_file "$file"; then
            failed_files+=("$file")
            exit_code=1
        fi
    done

    # Summary
    echo
    print_status "Linting complete!"

    if [ $exit_code -eq 0 ]; then
        print_success "All files passed linting checks!"
    else
        print_error "Linting failed for ${#failed_files[@]} files:"
        for file in "${failed_files[@]}"; do
            echo "  - $file"
        done
    fi

    return $exit_code
}

# Run main function with all arguments
main "$@"
