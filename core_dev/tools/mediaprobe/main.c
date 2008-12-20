/**
 * Copyright (c) 2008 Martin Lindhe
 *
 * mediaprobe is distributed under the BSD licence
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <inttypes.h>

static void hex_dump(uint8_t *buf, int size);

const unsigned char pngsig[8] = {137, 80, 78, 71, 13, 10, 26, 10};
const unsigned char mngsig[8] = {138, 77, 78, 71, 13, 10, 26, 10};

int main(int argc, char** argv)
{
	FILE *f;
	int len, readlen;
	uint8_t *buf = 0;

	if (argc < 2){
		printf("USAGE: %s <filename>\n", argv[0]);
		return 1;
	}

	f = fopen(argv[1], "rb");
	if (!f) {
		perror(argv[1]);
		return 2;
	}

	fseek(f, 0, SEEK_END);
	len = ftell(f);
	fseek(f, 0, SEEK_SET);

	if (len < 10) {
		printf("Input file is too small to probe\n");
		goto fail;
	}

	readlen = len;
	if (len > 1024) readlen = 1024;

	buf = malloc(readlen);

	if (fread(buf, sizeof(char), readlen, f) != readlen) {
		printf("Failed to read header. wanted %d, got %d\n", len, readlen);
		goto fail;
	}

	/* Look for GIF image */
	if (len >= 10 && buf[0] == 'G' && buf[1] == 'I' && buf[2] == 'F' && buf[3] == '8' &&  (buf[4] == '7' || buf[4] == '9') && buf[5] == 'a')
	{
		//FIXME minimum possible size of a GIF?
		//FIXME detect animated gif
		printf("GIF file\n");
		goto finish;
	}

	/* Look for BMP image */
	if (len >= 0x0E && buf[0] == 'B' && buf[1] == 'M') {
		//bmp file header is 0x0E bytes
		//mime: image/x-ms-bmp
		printf("BMP file\n");
		goto finish;
	}

	/* Look for PNG image */
	if (len >= 10 && memcmp(buf, pngsig, 8) == 0) {
		//FIXME minimum size of PNG?
		printf("PNG file\n");
		goto finish;
	}

	/* Look for MNG image */
	if (len >= 10 && memcmp(buf, mngsig, 8) == 0) {
		//FIXME minimum size of MNG?
		//FIXME need sample file
		printf("MNG file\n");
		goto finish;
	}

	/* Look for JPEG image */
	if (len >= 0x10 && buf[0] == 0xFF && buf[1] == 0xD8) {
		//FIXME minimum size of JPEG?
		printf("JPEG file\n");
		goto finish;
	}


	//FIXME detect TIFF & need sample file


	printf("Can't detect format. Hex dump of header:\n");
	hex_dump(buf, readlen);


	finish:
	fclose(f);
	exit(0);


	fail:
	fclose(f);
	exit(1);
}
/*
static int asf_probe(AVProbeData *pd)
{
	if (!memcmp(pd->buf, &asf_header, sizeof(GUID)))
		return AVPROBE_SCORE_MAX;
	else
		return 0;
}
*/

//function based on ffmpeg/libavformat/utils.c "hex_dump_internal"
static void hex_dump(uint8_t *buf, int size)
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
