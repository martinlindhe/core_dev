EXE = mediaprobe

OBJS = \
    debug.o      \
    \
    probe_bmp.o  \
    probe_gif.o  \
    probe_jpeg.o \
    probe_mng.o  \
    probe_png.o  \
    \
    probe_asf.o  \
    \
    main.o

CC = gcc
CFLAGS = -Wall -O3
LFLAGS = -s

all: $(OBJS)
	$(CC) $(CFLAGS) -o $(EXE) $(OBJS) $(LFLAGS)

clean:
	@rm -f *.o $(EXE)

install:
	cp $(EXE) /usr/bin

upx:
	upx -9 $(EXE)
