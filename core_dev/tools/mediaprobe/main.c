/**
 * $Id$
 *
 * Probes input file for detected media types
 * Defaults to output detected mime-type
 * Can be instructed to output more detailed
 * information of several media types.
 *
 * Copyright (c) 2008-2009 Martin Lindhe
 *
 * mediaprobe is distributed under the BSD licence
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <inttypes.h>

static void hex_dump(uint8_t *buf, int size);
static void print_guid(uint8_t *buf);

const unsigned char pngsig[8] = {137, 80, 78, 71, 13, 10, 26, 10};
const unsigned char mngsig[8] = {138, 77, 78, 71, 13, 10, 26, 10};

const unsigned char asf_sig[16] =
	{0x30, 0x26, 0xB2, 0x75, 0x8E, 0x66, 0xCF, 0x11, 0xA6, 0xD9, 0x00, 0xAA, 0x00, 0x62, 0xCE, 0x6C};

const unsigned char asf_stream_properties_object[16] =
	{0x91, 0x07, 0xDC, 0xB7, 0xB7, 0xA9, 0xCF, 0x11, 0x8E, 0xE6, 0x00, 0xC0, 0x0C, 0x20, 0x53, 0x65};


const unsigned char asf_stream_audio[16] =
	{0x40, 0x9E, 0x69, 0xF8, 0x4D, 0x5B, 0xCF, 0x11, 0xA8, 0xFD, 0x00, 0x80, 0x5F, 0x5C, 0x44, 0x2B};

const unsigned char asf_stream_video[16] =
	{0xC0, 0xEF, 0x19, 0xBC, 0x4D, 0x5B, 0xCF, 0x11, 0xA8, 0xFD, 0x00, 0x80, 0x5F, 0x5C, 0x44, 0x2B};



#define TAG6(o,a,b,c,d,e,f) (o[0]==a && o[1]==b && o[2]==c && o[3]==d && o[4]==e && o[5]==f)
#define TAG2(o,a,b)         (o[0]==a && o[1]==b)

struct asf_header {
	uint8_t guid[16];
	uint64_t size;
	uint32_t num;
	uint8_t res1, res2;
};

struct asf_object {
	uint8_t guid[16];
	uint64_t size;
};

struct asf_stream_properties {
	uint8_t stream_type[16];
};

int main(int argc, char** argv)
{
	FILE *f;
	uint8_t *buf = 0;

	if (argc < 2){
		printf("USAGE: %s <filename> [params]\n", argv[0]);
		printf("\n");
		printf("Param ---- Usage\n");
		printf(" -info     Display file info\n");
		return 1;
	}

	f = fopen(argv[1], "rb");
	if (!f) {
		perror(argv[1]);
		return 2;
	}

	fseek(f, 0, SEEK_END);
	int len = ftell(f);
	fseek(f, 0, SEEK_SET);

	if (len < 10) {
		printf("Input file is too small to probe\n");
		goto fail;
	}

	int readlen = len;
	if (len > 65536) readlen = 65536;

	buf = malloc(readlen);

	if (fread(buf, sizeof(char), readlen, f) != readlen) {
		printf("Failed to read header. wanted %d, got %d\n", len, readlen);
		goto fail;
	}

	int info = 0;	///< output detailed info?
	if (argc >= 3) {
		if (!strcmp(argv[2], "-info")) info = 1;
		else {
			printf("Unknown parameter: %s\n", argv[2]);
		}
	}

	/* Look for JPEG image */
	if (len >= 0x10 && TAG2(buf, 0xFF, 0xD8)) {
		//FIXME minimum size of JPEG?
		printf("image/jpeg\n");
		if (info) {
			printf("JPEG file\n");
		}
		goto finish;
	}

	/* Look for PNG image */
	if (len >= 10 && memcmp(buf, pngsig, 8) == 0) {
		//FIXME minimum size of PNG?
		printf("image/png\n");
		if (info) {
			printf("PNG file\n");
		}
		goto finish;
	}

	/* Look for GIF image */
	if (len >= 10 && (TAG6(buf,'G','I','F','8','7','a') || TAG6(buf,'G','I','F','8','9','a')))
	{
		//FIXME minimum possible size of a GIF?
		//FIXME detect animated gif
		printf("image/gif\n");
		if (info) {
			printf("GIF file\n");
		}
		goto finish;
	}

	/* Look for BMP image */
	if (len >= 0x0E && TAG2(buf, 'B', 'M') && (len == buf[0x2])) {
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
		goto finish;
	}

	/* Look for MNG image */
	if (len >= 10 && memcmp(buf, mngsig, 8) == 0) {
		//FIXME minimum size of MNG?
		//FIXME need sample file
		printf("video/x-mng\n");	//XXX correct mime?
		if (info) {
			printf("MNG file\n");
		}
		goto finish;
	}

	//FIXME detect TIFF & need sample file




	struct asf_header hdr;

	fseek(f, 0, SEEK_SET);
	fread(&hdr, 30, 1, f);	//XXX sizeof(hdr) returnerar 32 men ska vara 30. WTF?!?!

	/* Look for ASF header */
	if (len > 16 && memcmp(hdr.guid, asf_sig, 16) == 0) {
		/*
		printf("size of header       : %ld\n", hdr.size);
		printf("number of hdr objects: %ld\n", hdr.num);
		*/
		struct asf_object obj;

		int i, is_video = 0;
		for (i=0; i<hdr.num; i++) {
			fread(&obj, sizeof(obj), 1, f);

			if (memcmp(obj.guid, asf_stream_properties_object, 16) == 0) {

				struct asf_stream_properties prop;

				fread(&prop, sizeof(prop), 1, f);	//XXX read whole object??

				if (memcmp(prop.stream_type, asf_stream_video, 16) == 0) {
					is_video = 1;
				} else if (memcmp(prop.stream_type, asf_stream_audio, 16) == 0) {
					//xxx
				} else {
					printf("unknown stream type guid: ");
					print_guid(prop.stream_type);
					printf("\n");
				}
				fseek(f, obj.size - sizeof(obj) - sizeof(prop), SEEK_CUR);

			} else {
				/*
				printf("unknown guid: ");
				print_guid(obj.guid);
				printf("\n");
				printf("size: %ld\n", obj.size);
				*/

				fseek(f, obj.size - sizeof(obj), SEEK_CUR);
			}
		}

		if (is_video) {
			printf("video/x-ms-wmv\n");
		} else {
			printf("audio/x-ms-wma\n");
		}

		if (info) {
			printf("ASF container\n");
		}
		goto finish;
	}



	printf("Can't detect format:\n");
	hex_dump(buf, readlen);


	finish:
	fclose(f);
	exit(0);


	fail:
	fclose(f);
	exit(1);
}

//function based on ffmpeg/libavformat/utils.c "hex_dump_internal"
static void hex_dump(uint8_t *buf, int size)
{
	int len, i, j, c;

	for(i=0;i<size;i+=16) {
		len = size - i;
		if (len > 16)
			len = 16;
		printf("%08x ", i);
		for(j=0;j<16;j++) {
			if (j < len)
				printf(" %02x", buf[i+j]);
			else
				printf("   ");
		}
		printf(" ");
		for(j=0;j<len;j++) {
			c = buf[i+j];
			if (c < ' ' || c > '~')
				c = '.';
			printf("%c", c);
		}
		printf("\n");
	}
}

static void print_guid(uint8_t *buf)
{
	int i;

	for(i=0; i<16; i++) {
		printf(" %02x", buf[i]);
	}
}
