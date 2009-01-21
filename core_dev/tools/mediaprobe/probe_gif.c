#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

int probe_gif(FILE *f, uint8_t *buf, int len, int info)
{
	//XXX need GIF87a sample!!!!!

	/* Look for GIF image */
	if (len < 10 || (!TAG6(buf,'G','I','F','8','7','a') && !TAG6(buf,'G','I','F','8','9','a'))) return E_PROBEFAIL;

	//FIXME minimum possible size of a GIF?
	//FIXME detect animated gif
	printf("image/gif\n");
	if (info) {
		printf("GIF file\n");
	}

	return E_PROBESUCCESS;
}
