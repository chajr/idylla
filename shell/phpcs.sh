#!/bin/bash

if [ -e $CONFIG_FILE ]; then
    . $CONFIG_FILE
fi

# simple check if code sniffer is set up correctly
if [ ! -x $PHPCS_BIN ]; then
    echo "PHP CodeSniffer bin not found or executable -> $PHPCS_BIN"
    exit 1
fi

# execute the code sniffer
if [ "$PHPCS_IGNORE" != "" ]; then
    IGNORE="--ignore=$PHPCS_IGNORE"
else
    IGNORE=""
fi

if [ "$PHPCS_SNIFFS" != "" ]; then
    SNIFFS="--sniffs=$PHPCS_SNIFFS"
else
    SNIFFS=""
fi

if [ "$PHPCS_ENCODING" != "" ]; then
    ENCODING="--encoding=$PHPCS_ENCODING"
else
    ENCODING=""
fi

if [ "$PHPCS_IGNORE_WARNINGS" == "1" ]; then
    IGNORE_WARNINGS="-n"
else
    IGNORE_WARNINGS=""
fi

OUTPUT=$($PHPCS_BIN -s $IGNORE_WARNINGS --standard=$PHPCS_CODING_STANDARD \
$ENCODING $IGNORE $SNIFFS $STAGED_FILES $@)
RETVAL=$?

if [ $RETVAL -ne 0 ]; then
    echo "$OUTPUT" | less
fi

echo "staged files:":
echo $STAGED_FILES;
echo ""

exit $RETVAL
