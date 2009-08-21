/**
 * BMP image probe
 *
 * Status: Mostly complete
 *
 * http://fileformatwiki.org/index.php/Bmp
 */

//TODO: read directly into header structs
//TODO: parse & display CIEXYZTRIPLE endpoint data

#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

void show_bmp_winv3(FILE *f)
{
	int compr;
	printf("Width: %d\n", (signed) read32le(f));        //0x12
	printf("Height: %d\n", (signed) read32le(f));       //0x16
	printf("Color Planes: %d\n", read16le(f)); //0x1A	- uninteresting (must be 1)
	printf("BPP: %d\n", read16le(f));          //0x1C
	compr = read32le(f); //0x1E
	printf("Compression: ");
	switch (compr) {
		case 0: printf("BI_RGB\n"); break;
		case 1: printf("BI_RLE8\n"); break;
		case 2: printf("BI_RLE4\n"); break;
		case 3: printf("BI_BITFIELDS\n"); break;
		case 4: printf("BI_JPEG\n"); break;
		case 5: printf("BI_PNG\n"); break;
		default:
			printf("ERROR unknown %d\n", compr);
	}

	printf("Image data size: %d\n", read32le(f));       //0x22
	printf("Horizontal resolution: %d\n", (signed) read32le(f)); //0x26
	printf("Vertical resolution: %d\n", (signed) read32le(f)); //0x2A
	printf("Palette colors: %d\n", read32le(f));   //0x2E    - uninteresting
	printf("Important colors: %d\n", read32le(f)); //0x32    - uninteresting
}

void show_bmp_winv4(FILE *f)
{
	printf("Red mask: 0x%08X\n", read32le(f));   //0x36
	printf("Green mask: 0x%08X\n", read32le(f)); //0x3A
	printf("Blue mask: 0x%08X\n", read32le(f));  //0x3E
	printf("Alpha mask: 0x%08X\n", read32le(f)); //0x42
	printf("CS Type: %d\n", read32le(f));        //0x46

	//FIXME show endpoint data
	skip(f, 12 * 3); //XXX 12 bytes for each of R, G, B (???). microsoft call the struct CIEXYZTRIPLE

	printf("Red gamma: 0x%08X\n", read32le(f));	  //0x6A
	printf("Green gamma: 0x%08X\n", read32le(f)); //0x6E
	printf("Blue gamma: 0x%08X\n", read32le(f));  //0x72
}

void show_bmp_winv5(FILE *f)
{
	printf("Intent: 0x%08X\n", read32le(f));       //0x76
	printf("Profile data: 0x%08X\n", read32le(f)); //0x7A
	printf("Profile size: 0x%08X\n", read32le(f)); //0x7E
	printf("Reserved: 0x%08X\n", read32le(f));     //0x82
}

/**
 * Look for BMP image
 */
int probe_bmp(FILE *f, int len, int info)
{
	fseek(f, 0, SEEK_SET);

	//bmp header is 0x0E bytes
	if (len <= 0x10 || read16be(f) != MKTAG2('B', 'M'))
		return E_PROBEFAIL;

	int hdr_len = read32le(f);

	if (len != hdr_len /*&& hdr_len != 14*/) {
		printf("ERROR bmp hdr len = %d (file len %d)\n", hdr_len, len);
		return E_PROBEFAIL;
	}

	if (!info) {
		//XXX or image/x-ms-bmp ?
		printf("image/bmp\n");
	} else {
		printf("Format: BMP\n");
		printf("Mediatype: image\n");
		printf("Mimetype: image/bmp\n");

		skip(f, 4);	//4 reserved bytes
		skip(f, 4); //0x0A bitmap data offset (unused)

		uint32_t dib_size = read32le(f);

		switch (dib_size) {
			case 12: //OS/2 V1 - BITMAPCOREHEADER
				printf("Width: %d\n", (signed) read16le(f));        //0x12
				printf("Height: %d\n", (signed) read16le(f));	    //0x14
				printf("Color Planes: %d\n", (signed) read16le(f));	//0x16 - uninteresting (must be 1)
				printf("BPP: %d\n", read16le(f));                   //0x18
				break;

			case 40: //Windows V3 BITMAPINFOHEADER
				show_bmp_winv3(f);
				break;

			case 64: //OS/2 V2 - ?
				show_bmp_winv3(f);
				printf("Units: %d\n", read16le(f));          //0x36
				printf("Reserved: %d\n", read16le(f));       //0x38
				printf("Recording: %d\n", read16le(f));      //0x3A
				printf("Rendering: %d\n", read16le(f));      //0x3C
				printf("Size1: %d\n", read32le(f));          //0x3E
				printf("Size2: %d\n", read32le(f));          //0x42
				printf("Color Encoding: %d\n", read32le(f)); //0x46
				printf("Identifier: 0x%08X\n", read32le(f)); //0x4A
				break;

			case 108: //Windows V4 - BITMAPV4HEADER
				show_bmp_winv3(f);
				show_bmp_winv4(f);
				break;

			case 124: //Windows V5 - BITMAPV5HEADER
				show_bmp_winv3(f);
				show_bmp_winv4(f);
				show_bmp_winv5(f);
				break;

			default:
				printf("Unknown dib hdr size: %d\n", dib_size);
		}
	}

	return E_PROBESUCCESS;
}
