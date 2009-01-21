#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

/**
 * Look for GIF image
 */
int probe_gif(FILE *f, uint8_t *buf, int len, int info)
{
	//FIXME minimum possible size of a GIF?

	if (len < 10 || (!TAG6(buf,'G','I','F','8','7','a') && !TAG6(buf,'G','I','F','8','9','a')))
		return E_PROBEFAIL;

	if (!info) {
		printf("image/gif\n");
	} else {

		printf("Format: GIF\n");
		printf("Mediatype: image\n");
		printf("Mimetype: image/gif\n");

		//FIXME detect animated gif
	}

	return E_PROBESUCCESS;
}
