EXE = mediaprobe
OBJS = main.o
CFLAGS = -Wall -O3
LFLAGS = -s

all: $(OBJS)
	gcc $(CFLAGS) -o $(EXE) $(OBJS) $(LFLAGS)

clean:
	@rm -f *.o $(EXE)

upx:
	upx -9 $(EXE)
