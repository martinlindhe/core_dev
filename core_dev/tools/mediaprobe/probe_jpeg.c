#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

int probe_jpeg(FILE *f, uint8_t *buf, int len, int info)
{
	/* Look for JPEG image */
	if (len < 0x10 || !TAG2(buf, 0xFF, 0xD8)) return E_PROBEFAIL;

	//FIXME minimum size of JPEG?
	printf("image/jpeg\n");
	if (info) {
		printf("JPEG file\n");
	}

	return E_PROBESUCCESS;
}
