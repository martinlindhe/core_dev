#!/bin/sh

REPO=martin-svn
PROJ=krust

cd /home/martin/dev/
svn update $REPO

# check if current ver is same as .lastver
svnversion $REPO > backup-krust.currver
cmp -s backup-$PROJ.lastver backup-$PROJ.currver
if [ "$?" = "0" ]; then
	echo no change made. exiting
	exit 0
fi

zip -9r krust-ui-r`svnversion $REPO`.zip martin-svn/EQ/UIFiles/krust/ -x "*.svn/*"
zip -9r krust_no_mq-ui-r`svnversion $REPO`.zip martin-svn/EQ/UIFiles/krust_no_mq/ -x "*.svn/*"
./upload-startwars.kermit nofuture.se/krust/svn krust-ui-r`svnversion $REPO`.zip krust_no_mq-ui-r`svnversion $REPO`.zip

mv backup-$PROJ.currver backup-$PROJ.lastver
