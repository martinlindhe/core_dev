#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

/**
 * Look for JPEG image
 */
int probe_jpeg(FILE *f, uint8_t *buf, int len, int info)
{
	//FIXME minimum size of JPEG?
	if (len < 0x10 || !TAG2(buf, 0xFF, 0xD8))
		return E_PROBEFAIL;

	if (!info) {
		printf("image/jpeg\n");
	} else {
		printf("Format: JPEG\n");
		printf("Mediatype: image\n");
		printf("Mimetype: image/jpeg\n");
	}

	return E_PROBESUCCESS;
}
