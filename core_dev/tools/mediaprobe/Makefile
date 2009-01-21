EXE = mediaprobe

OBJS =           \
    debug.o      \
	\
    probe_asf.o  \
	\
    probe_bmp.o  \
    probe_gif.o  \
    probe_jpeg.o \
	\
    main.o


CFLAGS = -Wall -O3
LFLAGS = -s

all: $(OBJS)
	gcc $(CFLAGS) -o $(EXE) $(OBJS) $(LFLAGS)

clean:
	@rm -f *.o $(EXE)

install:
	cp $(EXE) /usr/bin

upx:
	upx -9 $(EXE)
