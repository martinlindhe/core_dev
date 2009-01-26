/**
 * PNG image probe
 *
 * Status: ?
 *
 * http://fileformatwiki.org/index.php/Png
 */

//TODO: show header data

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

const unsigned char png_sig[8] = {137, 80, 78, 71, 13, 10, 26, 10};

/**
 * Look for PNG image
 */
int probe_png(FILE *f, int len, int info)
{
	fseek(f, 0, SEEK_SET);

	uint8_t *buf = malloc(8);

	if (fread(buf, sizeof(char), 8, f) != 8) {
		free(buf);
		return E_READERROR;
	}

	if (len < 10 || memcmp(buf, png_sig, 8) != 0) {
		free(buf);
		return E_PROBEFAIL;
	}

	free(buf);

	if (!info) {
		printf("image/png\n");
	} else {
		printf("Format: PNG\n");
		printf("Mediatype: image\n");
		printf("Mimetype: image/png\n");
	}

	return E_PROBESUCCESS;
}
