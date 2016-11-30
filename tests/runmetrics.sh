#!/bin/bash
pdepend --summary-xml=metrics.xml --ignore=tests ../
php MC_Metrics.php metrics.xml>metrics.txt