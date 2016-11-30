#!/bin/bash

TARGET="${1/%.php/}"
if test -z "$TARGET" ; then
    TARGET="AllTestsSelenium"
fi

phpunit --bootstrap bootstrap_selenium.php $2 $3 $4 $5 $6 $7 $8 $9 ${TARGET//\//_}
