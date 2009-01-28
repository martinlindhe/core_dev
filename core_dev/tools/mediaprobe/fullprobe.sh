#!/bin/bash
#
# script to help test mediaprobe on a large set of files

DIR='/home/ml/Desktop/shared/mp3/'
MATCH='*.mp3'

cd $DIR
find -name "$MATCH" > /tmp/fullprobe.tmp

while read LINE
do
	RES=`mediaprobe "$DIR$LINE"`
	if [ $RES == "application/octet-stream" ]; then
		echo "ERROR: $DIR$LINE"
	fi
done < /tmp/fullprobe.tmp
