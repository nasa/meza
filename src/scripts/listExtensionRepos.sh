#!/bin/bash

# A script that checks if your Extensions are themselves git repositories.
# IOW, it lists all subdirectories one level deep 
# and checks if they contain a .git directory.

# We need to know where to look
if [ -z "$1" ] || [ "$1" == "--help" ]; then
  cat << USAGE
Usage: $0 <directory> [--repos-only|--non-repos]
  <directory> is the path to the directory containing the extensions.
  [--repos-only|--non-repos] is an optional flag to filter the output so that
  it shows only repositories or only non-repositories.
  e.g. $0 /opt/htdocs/mediawiki/extensions --repos-only
  
  Given a directory, the script will list all subdirectories one level deep
  and report whether or not they are git repositories.

  For those that are repositories, it will also list the remotes.

Safe directories:
  If you get errors about safe directories, try running the script as the USER
  who owns the directories. In other words, do not run it as root!

  Safe directories is a git feature to avoid accidentally running git commands in 
  untrusted directories owned by other users of your system.

  See https://git-scm.com/docs/git-config#Documentation/git-config.txt-safedirectory

  You can add the following to your .gitconfig file:
  [safe]
	directory = /opt/htdocs/mediawiki/extensions/VisualEditor
	directory = /opt/htdocs/mediawiki/extensions/Flow

  Using Git 2.46 (Q3 2024), giving a directory with /* appended to it will allow 
  access to all repositories under the named directory.
  e.g. 'git config --global --add safe.directory /opt/htdocs/mediawiki/extensions/*'

  Or, you can add all version controlled directories to your .gitconfig file by 
  trying something like this:
find /opt/htdocs/mediawiki/extensions -maxdepth 2 -name '.git' -type d -exec bash -c
 'git config --global --add safe.directory ${0%}' {} \;
  (not really advised - just become the right USER to avoid the warnings)
USAGE
  exit 1
fi

# Determine the output mode
# By default, show both repos and non-repos
SHOW_REPOS=true
SHOW_NON_REPOS=true
# Given an optional flag, filter the output
if [ "$2" == "--repos-only" ]; then
  SHOW_NON_REPOS=false
elif [ "$2" == "--non-repos" ]; then
  SHOW_REPOS=false
fi

# Find all subdirectories one level deep
for dir in "$1"/*/; do
  if [ -d "$dir" ]; then
    if [ -d "$dir/.git" ]; then
      if [ "$SHOW_REPOS" = true ]; then
        echo "Directory: $dir"
        echo "  is a git repo"
        git -C "$dir" remote -v
      fi
    else
      if [ "$SHOW_NON_REPOS" = true ]; then
        echo "Directory: $dir"
        echo "  is not version controlled."
      fi
    fi
  fi
done