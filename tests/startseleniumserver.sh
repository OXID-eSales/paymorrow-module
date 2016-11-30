#!/bin/bash

Xvfb +extension RANDR -fp /usr/share/fonts/X11/misc/ :22 -screen 0 1024x768x16 2>&1 &
export DISPLAY=:22
export FIREFOX_PATH=/usr/lib/firefox/
export PATH=$PATH:$FIREFOX_PATH
export LD_LIBRARY_PATH=/usr/lib/x86_64-linux-gnu/
java -jar ./selenium-server-standalone-2.39.0.jar -multiWindow
