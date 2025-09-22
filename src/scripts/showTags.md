# showTags.sh - Display Recent Git Tags

## Overview

This script displays information about the 3 most recent Git tags in the repository, formatted like email messages with detailed metadata.

## Usage

```bash
./showTags.sh
```

## Functionality

The script uses `git for-each-ref` to retrieve and format tag information:

- **Count**: Shows only the 3 most recent tags (`--count=3`)
- **Sorting**: Orders tags by author date in descending order (`--sort='-*authordate'`)
- **Target**: Operates on Git tags (`'refs/tags'`)

## Output Format

Each tag is displayed in an email-like format:

```
From: [Author Name] <author@email.com>
Subject: [Tag message/subject]
Date: [Author date]
Ref: [Full tag reference name]

[Tag body/description]

```

### Format Fields

- **From**: The name and email of the person who created the tag
- **Subject**: The first line of the tag message
- **Date**: When the tag was created (author date)
- **Ref**: The full reference name (e.g., `refs/tags/v1.0.0`)
- **Body**: The full tag message/description

## Example Output

```
From: John Doe <john.doe@example.com>
Subject: Release version 1.2.0
Date: Fri Jan 15 14:30:25 2024 -0500
Ref: refs/tags/v1.2.0

This release includes:
- Bug fixes for authentication
- Performance improvements
- New dashboard features

From: Jane Smith <jane.smith@example.com>
Subject: Hotfix 1.1.1
Date: Wed Jan 10 09:15:42 2024 -0500
Ref: refs/tags/v1.1.1

Emergency fix for security vulnerability CVE-2024-0001

From: John Doe <john.doe@example.com>
Subject: Major release 1.1.0
Date: Mon Jan 8 16:45:18 2024 -0500
Ref: refs/tags/v1.1.0

Major feature release with new API endpoints and UI updates.
```

## Use Cases

- **Release Notes**: Quickly view recent release information
- **Change Tracking**: See what changes were tagged recently  
- **Release Management**: Review tag messages and dates
- **Git History**: Understand recent tagging activity

## Requirements

- Git repository with annotated tags
- Git command-line tools installed
- Execute permission on the script

## Notes

- Only works with **annotated tags** (created with `git tag -a`)
- Lightweight tags may not display all information
- The `*` prefix in format specifiers refers to the tag object itself
- Date format follows Git's default format

## Related Commands

```bash
# List all tags
git tag

# Show specific tag information
git show v1.0.0

# Create an annotated tag
git tag -a v1.0.0 -m "Release version 1.0.0"
```