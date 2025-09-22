#!/bin/bash
# filepath: ./src/scripts/generate-release-notes.sh

# We want to generate RELEASE_NOTES in the project root, so
# run this script from the project root directory.
# Usage: ./src/scripts/generate-release-notes.sh <START_REF> <END_REF>
# Example: ./src/scripts/generate-release-notes.sh 43.25.11 43.29.1

# This script is a more formal way to generate release notes by parsing git commit messages.
# Based on the simple CLI approach of
#
# export START=43.25.11;
# export END=43.29.1;
# git log --no-merges --name-status --format="%(decorate:prefix=,suffix=%n,tag=%n## Meza ,separator= )* %h (%as) %an: %s %b" $START..$END \
# | sed -E 's/\* ([0-9a-f]{8})/\* [\1](https:\/\/github.com\/freephile\/meza\/commit\/\1)/g'  \
# | sed -E 's/Issue # ?([0-9]{1,4})/Issue [#\1](https:\/\/github.com\/freephile\/meza\/issues\/\1)/Ig' \
# > RELEASE_NOTES-$END.md
#
# See https://wiki.freephile.org/wiki/RELEASE_NOTES for background.

set -euo pipefail

if [[ $# -ne 2 ]]; then
  cat <<HERE
ERROR, missing commit refs

Usage: $0 <START_REF> <END_REF>

Normally you should start with the tag that was last documented
And end with the tag that is being documented.
HERE
  exit 1
fi

START="$1"
END="$2"
RELEASE_NOTES="RELEASE_NOTES-$END.md"

# Get git log with name-status, separating commits with a unique marker
git log --no-merges --name-status --format="__COMMIT__%n%(decorate:prefix=,suffix=%n,tag=%n## Meza ,separator= )* %h (%as) %an: %s %b" "$START..$END" > tmp.log

{
  echo "## Meza Release Notes $START → $END"
  echo
  echo "### Commits"
  echo
  awk '
	BEGIN { IGNORECASE=1 }
    BEGIN { in_commit=0 }
    /^__COMMIT__/ {
      if (in_commit) print "";
      in_commit=1;
      next
    }
    /^\* / {
      # Transform SHA to markdown link
      if (match($0, /^\* ([0-9a-f]{8})/, m)) {
        sha=m[1];
        sub("\\* " sha, "* [" sha "](https://github.com/freephile/meza/commit/" sha ")", $0);
      }
    }
    /^[ACDMRTUXB]\t/ {
      split($0, parts, "\t");
      status=parts[1];
      file=parts[2];
      if (status=="A") print "  - Added: `" file "`";
      else if (status=="C") print "  - Copied: `" file "`";
      else if (status=="D") print "  - Deleted: `" file "`";
      else if (status=="M") print "  - Modified: `" file "`";
      else if (status=="R") print "  - Renamed: `" file "`";
      else if (status=="T") print "  - Type changed: `" file "`";
      else if (status=="U") print "  - Unmerged: `" file "`";
      else if (status=="X") print "  - Unknown: `" file "`";
      else if (status=="B") print "  - Broken pairing: `" file "`";
      next
    }
    NF>0 {
      # Transform Issue references to markdown links on all lines
      while (match($0, /Issue # ?([0-9]{1,4})/, m)) {
        issue_num = m[1];
        replacement = "Issue [#" issue_num "](https://github.com/freephile/meza/issues/" issue_num ")";
        $0 = substr($0, 1, RSTART - 1) replacement substr($0, RSTART + RLENGTH);
      }
      print $0
    }
  ' tmp.log
} > "$RELEASE_NOTES"

rm -f tmp.log

echo "Release notes generated: $RELEASE_NOTES"