#!/bin/bash

TESTDIR=$(dirname $0);

CODECOVERAGE=1 \
COVERAGE='--configuration phpunit_dev.xml --coverage-html '$TESTDIR'/coverage' \
$TESTDIR/runtests.sh
