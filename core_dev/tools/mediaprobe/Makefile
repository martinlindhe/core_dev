EXE = mediaprobe

OBJS = \
    io.o         \
    debug.o      \
    \
    probe_bmp.o  \
    probe_gif.o  \
    probe_jpeg.o \
    probe_mng.o  \
    probe_png.o  \
    \
    probe_mp3.o  \
    probe_asf.o  \
    \
    main.o

CC = gcc

CFLAGS = -Wall -O3
#CFLAGS += -g -DDEBUG

LFLAGS =
#LFLAGS = -s

all: $(OBJS)
	$(CC) $(CFLAGS) -o $(EXE) $(OBJS) $(LFLAGS)

clean:
	rm -f *.o $(EXE)

test:
	/bin/bash ./testsuite.sh

install:
	cp $(EXE) /usr/bin

upx:
	upx -9 $(EXE)
