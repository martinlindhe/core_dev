/**
 * GIF image probe
 *
 * Status: ?
 *
 * http://fileformatwiki.org/index.php/Gif
 */

//TODO: show header data

#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

/**
 * Look for GIF image
 */
int probe_gif(FILE *f, int len, int info)
{
	fseek(f, 0, SEEK_SET);

	uint32_t h1 = read32be(f);
	uint16_t h2 = read16be(f);

	if (len < 10 || h1 != MKTAG4('G','I','F','8'))
		return E_PROBEFAIL;

	if (h2 != MKTAG2('7','a') && h2 != MKTAG2('9','a'))
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
