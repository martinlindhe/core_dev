#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

const unsigned char png_sig[8] = {137, 80, 78, 71, 13, 10, 26, 10};
const unsigned char mng_sig[8] = {138, 77, 78, 71, 13, 10, 26, 10};

int probe_png(FILE *f, uint8_t *buf, int len, int info)
{
	//FIXME minimum size of PNG?
	//FIXME minimum size of MNG?
	if (len < 10) return E_PROBEFAIL;

	/* Look for PNG image */
	if (memcmp(buf, png_sig, 8) == 0) {

		printf("image/png\n");
		if (info) {
			printf("PNG file\n");
		}
		return E_PROBESUCCESS;
	}

	/* Look for MNG image */
	if (memcmp(buf, mng_sig, 8) == 0) {

		//FIXME need sample file
		printf("video/x-mng\n");	//XXX correct mime?
		if (info) {
			printf("MNG file\n");
		}
		return E_PROBESUCCESS;
	}

	return E_PROBEFAIL;
}
