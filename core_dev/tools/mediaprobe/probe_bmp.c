#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"
#include "probe_bmp.h"

int probe_bmp(FILE *f, uint8_t *buf, int len, int info)
{
	/* Look for BMP image */
	if (len < 0x0E || !TAG2(buf, 'B', 'M') || (len != buf[0x2])) return E_PROBEFAIL;

	//bmp file header is 0x0E bytes
	printf("image/bmp\n");	//XXX or image/x-ms-bmp ?
	if (info) {
		printf("BMP file\n");

		printf("  size of bmp : %d\n", buf[0x2]);	//xxx specify u32 read not byte
		printf("  bmp offset  : 0x%x\n", buf[0xa]);	//xxx specify u32 read not byte

		switch (buf[0xe]) {
			case 40:
				printf("  [DIB HEADER - Windows V3 BITMAPINFOHEADER]\n");
				printf("  width : %d\n", buf[0x12]);	//xxx s32 read not byte
				printf("  height: %d\n", buf[0x16]);	//xxx s32 read not byte
				printf("  bpp   : %d\n", buf[0x1c]);	//xxx u16 read!
				printf("  compression : %d\n", buf[0x1e]);	//xxx u32 read not byte
				break;

			default:
				printf("Unknown dib hdr size: %d\n", buf[0xe]);	//xxx specify u32 read not byte
		}
	}

	return E_PROBESUCCESS;
}
