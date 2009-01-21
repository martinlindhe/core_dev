#include <stdio.h>
#include <inttypes.h>

#include "debug.h"

//function based on ffmpeg/libavformat/utils.c "hex_dump_internal"
void hex_dump(uint8_t *buf, int size)
{
	int len, i, j, c;

	for(i=0;i<size;i+=16) {
		len = size - i;
		if (len > 16)
			len = 16;
		printf("%08x ", i);
		for(j=0;j<16;j++) {
			if (j < len)
				printf(" %02x", buf[i+j]);
			else
				printf("   ");
		}
		printf(" ");
		for(j=0;j<len;j++) {
			c = buf[i+j];
			if (c < ' ' || c > '~')
				c = '.';
			printf("%c", c);
		}
		printf("\n");
	}
}
