/**
 * MNG image probe
 *
 * Status: ?
 */

//TODO: should this probe be dropped? the format is barely used anywhere & not much software supports it
//XXX: is this to be considered video? its conceptually the same to animated gif

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

const unsigned char mng_sig[8] = {138, 77, 78, 71, 13, 10, 26, 10};

/**
 * MNG: Multiple-image Network Graphics
 */
int probe_mng(FILE *f, int len, int info)
{
	fseek(f, 0, SEEK_SET);

	uint8_t *buf = malloc(8);

	if (fread(buf, sizeof(char), 8, f) != 8)
		return E_READERROR;

	if (len < 10 || memcmp(buf, mng_sig, 8) != 0)
		return E_PROBEFAIL;

	free(buf);

	if (!info) {
		//XXX: MNG does not yet have a registered MIME media type, but video/x-mng or image/x-mng can be used.
		printf("video/x-mng\n");	//XXX correct mime?
	} else {
		printf("Format: MNG\n");
		printf("Mediatype: image\n");
		printf("Mimetype: video/x-mng\n");
	}

	return E_PROBESUCCESS;
}
