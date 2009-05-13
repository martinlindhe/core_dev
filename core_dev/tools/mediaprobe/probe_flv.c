/**
 * FLV video probe
 *
 * Status: ?
 *
 * http://fileformatwiki.org/index.php/FLV
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

//TODO: implement some proper parsing

/**
 * Look for FLV file
 */
int probe_flv(FILE *f, int len, int info)
{
	fseek(f, 0, SEEK_SET);

	if (read8(f) == 'F' && read8(f) == 'L' && read8(f) == 'V')
	{
		if (!info) {
			printf("video/x-flv\n");	//XXX or video/mp4 according to adobe?
		} else {
			printf("Format: FLV\n");
			printf("Mediatype: video\n");
			printf("Mimetype: video/x-flv\n");
		}
		return E_PROBESUCCESS;
	}

	return E_PROBEFAIL;
}
