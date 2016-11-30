#!/bin/bash

TESTDIR=$(dirname $0);

CODECOVERAGE=1 \
COVERAGE='--configuration phpunit.xml --coverage-clover '$TESTDIR'/certification/coverage-clover.xml' \
$TESTDIR/runtests.sh

$TESTDIR/../../../../oxmd/src/bin/oxmd $TESTDIR/../ $TESTDIR/certification/coverage-clover.xml text \
--reportfile-text certification/certification.txt \
--exclude changed_full/,core/oxpspaymorrowmodule.php,docs/,out/,tests/,translations/,vendor/,views/,metadata.php
