#!/bin/bash

# test directory path
TESTDIR=$(dirname $0);

# trying to find the metadata file path
if [ ! $CODECOVERAGE ]; then
    if [ ! $oxMETADATA ]; then
        oxMETADATA=$TESTDIR'/../metadata.php';
    else
        if [ ! -e $oxMETADATA ]; then
            echo "Can't find the metdata file at "\'$oxMETADATA\'
            exit
        fi
    fi
fi

# if oxPATH was not set, then try to find it from the current script path
if [ ! $oxPATH ]; then
    oxPATH=$(dirname $(readlink -f $0))
    BASENAME=$(basename $oxPATH)

    #finding the modules directory path
    while [ $BASENAME != 'modules' ]; do
        oxPATH=$(dirname $oxPATH)
        BASENAME=$(basename $oxPATH)
        if [ $oxPATH == '/' ]; then
            echo "Please set the oxPATH value"
            exit
        fi
    done

    #going one directory up, to reach the shops root dir
    oxPATH=$(dirname $oxPATH);
else
    if [ ! -d $oxPATH ]; then
        echo "Can't find the shop directory" \'$oxPATH\'
        exit
    fi
fi

TARGET=$@;
if [[ ! $TARGET ]] ; then
    TARGET='unit';
fi;

oxPATH=$oxPATH oxMETADATA=$oxMETADATA \
php -d 'memory_limit=1024M' \
/usr/bin/phpunit --verbose --bootstrap $TESTDIR/bootstrap.php $COVERAGE \
$TESTDIR/$TARGET
