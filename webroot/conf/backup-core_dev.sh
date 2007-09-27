#!/bin/sh

REPO=martin-svn

cd /home/martin/dev/

svn update $REPO

# check if current ver is same as .lastver
svnversion $REPO > backup-core_dev.currver
cmp -s backup-core_dev.lastver backup-core_dev.currver
if [ "$?" = "0" ]; then
	echo no change made. exiting
	exit 0
fi

zip -9r core_dev-r`svnversion $REPO`.zip martin-svn/webroot/core_dev -x "*.svn/*"
./upload-startwars.kermit startwars.org/core_dev core_dev-r`svnversion $REPO`.zip

mv backup-core_dev.currver backup-core_dev.lastver
