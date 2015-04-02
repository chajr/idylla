#!/bin/bash
# Here one have only git specific task, phpcs is run from shell/phpcs

# PHP CodeSniffer pre-commit hook for git
#
# @author Soenke Ruempler <soenke@ruempler.eu>
# @author Sebastian Kaspari <s.kaspari@googlemail.com>
#
# see the README
if [ -e $CONFIG_FILE ]; then
    . $CONFIG_FILE
fi

TMP_STAGING=".tmp_staging"

# simple check if code sniffer is set up correctly
if [ ! -x $PHPCS_BIN ]; then
    echo "PHP CodeSniffer bin not found or executable -> $PHPCS_BIN"
    exit 1
else
    php $PHPCS_BIN
fi

# stolen from template file
if git rev-parse --verify HEAD
then
    against=HEAD
else
    # Initial commit: diff against an empty tree object
    against=d63324bf5e278d32e4d708ac57826258bb987178
fi

# this is the magic:
# retrieve all files in staging area that are added, modified or renamed
# but no deletions etc
FILES=$(git diff-index --name-only --cached --diff-filter=ACMR $against -- )

if [ "$FILES" == "" ]; then
    exit 0
fi

# create temporary copy of staging area
if [ -e $TMP_STAGING ]; then
    rm -rf $TMP_STAGING
fi
mkdir $TMP_STAGING

# match files against whitelist
FILES_TO_CHECK=""
for FILE in $FILES
do
    echo "$FILE" | egrep -q "$PHPCS_FILE_PATTERN"
    RETVAL=$?
    if [ "$RETVAL" -eq "0" ]
    then
        FILES_TO_CHECK="$FILES_TO_CHECK $FILE"
    fi
done

if [ "$FILES_TO_CHECK" == "" ]; then
    exit 0
fi


# Copy contents of staged version of files to temporary staging area
# because we only want the staged version that will be commited and not
# the version in the working directory
STAGED_FILES=""
for FILE in $FILES_TO_CHECK
do
  ID=$(git diff-index --cached $against $FILE | cut -d " " -f4)

  # create staged version of file in temporary staging area with the same
  # path as the original file so that the phpcs ignore filters can be applied
  mkdir -p "$TMP_STAGING/$(dirname $FILE)"
  git cat-file blob $ID > "$TMP_STAGING/$FILE"
  export STAGED_FILES="$STAGED_FILES $TMP_STAGING/$FILE"
done

./shell/phpcs.sh
RETVAL=$?

# delete temporary copy of staging area
rm -rf $TMP_STAGING

exit $RETVAL
