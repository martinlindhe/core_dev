/**
 * $Id$
 *
 * Probes input file for detected media types
 * Defaults to output detected mime-type
 * Can be instructed to output more detailed
 * information of several media types.
 *
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

#define TAG6(o,a,b,c,d,e,f) (o[0]==a && o[1]==b && o[2]==c && o[3]==d && o[4]==e && o[5]==f)
#define TAG2(o,a,b)         (o[0]==a && o[1]==b)

int main(int argc, char** argv)
{
	FILE *f;
	uint8_t *buf = 0;

	if (argc < 2){
		printf("USAGE: %s <filename> [params]\n", argv[0]);
		printf("\n");
		printf("Param ---- Usage\n");
		printf(" -info     Display file info\n");
		return 1;
	}

	f = fopen(argv[1], "rb");
	if (!f) {
		perror(argv[1]);
		return 2;
	}

	fseek(f, 0, SEEK_END);
	int len = ftell(f);
	fseek(f, 0, SEEK_SET);

	if (len < 10) {
		printf("Input file is too small to probe\n");
		goto fail;
	}

	int readlen = len;
	if (len > 1024) readlen = 1024;

	buf = malloc(readlen);

	if (fread(buf, sizeof(char), readlen, f) != readlen) {
		printf("Failed to read header. wanted %d, got %d\n", len, readlen);
		goto fail;
	}

	int info = 0;	///< output mime info?
	if (argc >= 3) {
		if (!strcmp(argv[2], "-info")) info = 1;
		else {
			printf("Unknown parameter: %s\n", argv[2]);
		}
	}

	/* Look for JPEG image */
	if (len >= 0x10 && TAG2(buf, 0xFF, 0xD8)) {
		//FIXME minimum size of JPEG?
		printf("image/jpeg\n");
		if (info) {
			printf("JPEG file\n");
		}
		goto finish;
	}

	/* Look for PNG image */
	if (len >= 10 && memcmp(buf, pngsig, 8) == 0) {
		//FIXME minimum size of PNG?
		printf("image/png\n");
		if (info) {
			printf("PNG file\n");
		}
		goto finish;
	}

	/* Look for GIF image */
	if (len >= 10 && (TAG6(buf,'G','I','F','8','7','a') || TAG6(buf,'G','I','F','8','9','a')))
	{
		//FIXME minimum possible size of a GIF?
		//FIXME detect animated gif
		printf("image/gif\n");
		if (info) {
			printf("GIF file\n");
		}
		goto finish;
	}

	/* Look for BMP image */
	if (len >= 0x0E && TAG2(buf, 'B', 'M')) {
		//bmp file header is 0x0E bytes
		printf("image/bmp\n");	//XXX or image/x-ms-bmp ?
		if (info) {
			printf("BMP file\n");
		}
		goto finish;
	}

	/* Look for MNG image */
	if (len >= 10 && memcmp(buf, mngsig, 8) == 0) {
		//FIXME minimum size of MNG?
		//FIXME need sample file
		printf("video/x-mng\n");	//XXX correct mime?
		if (info) {
			printf("MNG file\n");
		}
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
