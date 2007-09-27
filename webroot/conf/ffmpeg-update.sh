cd ffmpeg-svn
svn update
make distclean
./configure --enable-gpl --enable-pthreads --enable-libx264 --enable-libamr-nb --enable-libamr-wb --enable-libxvid --enable-libmp3lame --enable-libogg --enable-libvorbis --enable-libgsm
make
