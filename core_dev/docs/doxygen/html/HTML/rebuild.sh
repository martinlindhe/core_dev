#!/bin/sh
#
# rebuild.sh: rebuild hypertext with the previous context.
#
# Usage:
#	% sh rebuild.sh
#
cd /home/martin/dev/m2w/core_dev/core && GTAGSCONF=':suffixes=c,h,y,s,S,java,c++,cc,cpp,cxx,hxx,hpp,C,H,php,php3,phtml:skip=GPATH,GTAGS,GRTAGS,GSYMS,HTML/,html/,tags,TAGS,ID,y.tab.c,y.tab.h,.notfunction,cscope.out,.gdbinit,SCCS/,RCS/,CVS/,CVSROOT/,{arch}/,.svn/,.cvsrc,.cvsignore,.cvspass,.cvswrappers,.deps/,autom4te.cache/,.snprj/:GTAGS=/usr/bin/gtags-parser -dt %s:GRTAGS=/usr/bin/gtags-parser -dtr %s:GSYMS=/usr/bin/gtags-parser -dts %s:' htags -g -s -a -n -v -w -t core_dev /home/martin/dev/m2w/core_dev/docs/doxygen/html
