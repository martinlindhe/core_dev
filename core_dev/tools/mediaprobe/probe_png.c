#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

const unsigned char png_sig[8] = {137, 80, 78, 71, 13, 10, 26, 10};

/**
 * Look for PNG image
 */
int probe_png(FILE *f, uint8_t *buf, int len, int info)
{
	//FIXME minimum size of PNG?
	if (len < 10 || memcmp(buf, png_sig, 8) != 0) return E_PROBEFAIL;

	if (!info) {
		printf("image/png\n");
	} else {
		printf("Format: PNG\n");
		printf("Mediatype: image\n");
		printf("Mimetype: image/png\n");
	}

	return E_PROBESUCCESS;
}
