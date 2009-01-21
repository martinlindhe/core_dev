/**
 * $Id$
 *
 * Probes input file for detected media types
 * Defaults to output detected mime-type
 * Can be instructed to output more detailed
 * information of several media types.
 *
 * Copyright (c) 2008-2009 Martin Lindhe
 *
 * mediaprobe is distributed under the BSD licence
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <inttypes.h>

#include "mediaprobe.h"
#include "debug.h"

#include "probe_asf.h"

#include "probe_bmp.h"
#include "probe_gif.h"
#include "probe_jpeg.h"

const unsigned char pngsig[8] = {137, 80, 78, 71, 13, 10, 26, 10};
const unsigned char mngsig[8] = {138, 77, 78, 71, 13, 10, 26, 10};

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
	if (len > 1000) readlen = 1000;

	buf = malloc(readlen);

	if (fread(buf, sizeof(char), readlen, f) != readlen) {
		printf("Failed to read header. wanted %d, got %d\n", len, readlen);
		goto fail;
	}

	int info = 0;	///< output detailed info?
	if (argc >= 3) {
		if (!strcmp(argv[2], "-info")) info = 1;
		else {
			printf("Unknown parameter: %s\n", argv[2]);
		}
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


	if (probe_bmp (f, buf, len, info) == E_PROBESUCCESS) goto finish;
	if (probe_gif (f, buf, len, info) == E_PROBESUCCESS) goto finish;
	if (probe_jpeg(f, buf, len, info) == E_PROBESUCCESS) goto finish;

	if (probe_asf(f, len, info) == E_PROBESUCCESS) goto finish;




	printf("Can't detect format:\n");
	hex_dump(buf, readlen);


	finish:
	fclose(f);
	exit(0);


	fail:
	fclose(f);
	exit(1);
}
