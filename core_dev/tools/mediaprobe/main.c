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

//image format parsers
#include "probe_bmp.h"
#include "probe_gif.h"
#include "probe_jpeg.h"
#include "probe_mng.h"
#include "probe_png.h"

//container format parsers
#include "probe_asf.h"

//audio format parsers
#include "probe_mp3.h"

int main(int argc, char** argv)
{
	FILE *f;

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

	int info = 0;	///< output detailed info?
	if (argc >= 3) {
		if (!strcmp(argv[2], "-info")) info = 1;
		else {
			printf("Unknown parameter: %s\n", argv[2]);
			goto fail;
		}
	}

	//image format parsers
	if (probe_bmp (f, len, info) == E_PROBESUCCESS) goto finish;
	if (probe_gif (f, len, info) == E_PROBESUCCESS) goto finish;
	if (probe_jpeg(f, len, info) == E_PROBESUCCESS) goto finish;
	if (probe_mng (f, len, info) == E_PROBESUCCESS) goto finish;
	if (probe_png (f, len, info) == E_PROBESUCCESS) goto finish;

	//container format parsers
	if (probe_asf (f, len, info) == E_PROBESUCCESS) goto finish;

	//audio format parsers
	if (probe_mp3 (f, len, info) == E_PROBESUCCESS) goto finish;

	//unknown mime format:
	printf("application/octet-stream\n");
	//hex_dump(buf, readlen);

finish:
	fclose(f);
	exit(0);

fail:
	fclose(f);
	exit(1);
}