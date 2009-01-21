#include <stdio.h>
#include <string.h>
#include <inttypes.h>

//TODO: should this probe be dropped? the format is barely used anywhere & not much software supports it

#include "mediaprobe.h"

const unsigned char mng_sig[8] = {138, 77, 78, 71, 13, 10, 26, 10};

/**
 * MNG: Multiple-image Network Graphics
 */
int probe_mng(FILE *f, uint8_t *buf, int len, int info)
{
	//FIXME minimum size of MNG?
	if (len < 10 || memcmp(buf, mng_sig, 8) != 0) return E_PROBEFAIL;

	//XXX is this to be considered video? its conceptually the same to animated gif

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
