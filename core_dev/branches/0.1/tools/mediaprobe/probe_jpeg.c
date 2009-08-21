/**
 * JPEG image probe
 *
 * Status: ?
 *
 * http://fileformatwiki.org/index.php/Jpeg
 */

//TODO: show header data

#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

/**
 * Look for JPEG image
 */
int probe_jpeg(FILE *f, int len, int info)
{
	fseek(f, 0, SEEK_SET);

	if (len < 0x10 || read16be(f) != MKTAG2(0xFF, 0xD8))
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
