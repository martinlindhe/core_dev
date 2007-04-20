@echo off

del test.avi
ffmpeg -i VIDEO_00007.3gp -f avi -b 150 -an test.avi
