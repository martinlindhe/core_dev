EXE = mediaprobe
OBJS = main.o probe_asf.o
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
