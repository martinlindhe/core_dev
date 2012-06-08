#!/bin/bash
#
# run this from the root directory:
#  $ tools/makedoxy.sh

DOX_FILE=Doxyfile

doxygen $DOX_FILE
# > /dev/null 2>&1
