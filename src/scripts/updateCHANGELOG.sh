#!/bin/bash
# filepath: ./src/scripts/updateCHANGELOG.sh
# Usage: ./src/scripts/updateCHANGELOG.sh

# This script updates the CHANGELOG file by finding the latest commit hash in
# the top of the CHANGELOG file, and then using git log --pretty to prepend new content.

git log --pretty $(grep -P -om 1 'commit (\d{8})' CHANGELOG | cut -d' ' -f 2)..HEAD > tmp.log
(cat tmp.log; echo; cat CHANGELOG) > tmpfile && mv tmpfile CHANGELOG
rm tmp.log
echo "Updated CHANGELOG"
# Note: You shouldn't need to manually edit the CHANGELOG after running this script
# because the generate-release-notes.sh script creates a separate RELEASE_NOTES-<version>.md file.