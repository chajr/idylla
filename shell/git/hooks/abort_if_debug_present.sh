#!/bin/bash
#
# this hook will looks for var_dump or debug string in changed files, and abort
# commit if there's one. "debug" may be used to mark places which you don't want
# to commit (i.e commented out validation)
#
# create executable .git/hooks/pre-commit and add this file
# shell/git/abort_if_debug.sh

VAR=$(git diff --cached | grep -wi "var_dump")
if [ ! -z "$VAR" ]; then
  echo "$VAR"
  echo "You've left a 'var_dump' or 'debug' in one of your files! Aborting commit..."
  echo "run command below to find your junk"
  echo "git diff --cached | grep -wiHn -C 10 'var_dump'"
  exit 1
fi
