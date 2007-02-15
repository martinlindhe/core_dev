EXE = minicron.exe
OBJS = main.o strings.o
CFLAGS = -Wall -O3 -march=athlon
# -ffast-math
LFLAGS = -s

all: $(OBJS)
	gcc $(CFLAGS) -o $(EXE) $(OBJS) $(LFLAGS)

clean:
	@rm -f *.o $(EXE)

upx:
	upx -9 $(EXE)