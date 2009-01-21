#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

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

const unsigned char asf_sig[16] =
	{0x30, 0x26, 0xB2, 0x75, 0x8E, 0x66, 0xCF, 0x11, 0xA6, 0xD9, 0x00, 0xAA, 0x00, 0x62, 0xCE, 0x6C};

const unsigned char asf_stream_properties_object[16] =
	{0x91, 0x07, 0xDC, 0xB7, 0xB7, 0xA9, 0xCF, 0x11, 0x8E, 0xE6, 0x00, 0xC0, 0x0C, 0x20, 0x53, 0x65};


const unsigned char asf_stream_audio[16] =
	{0x40, 0x9E, 0x69, 0xF8, 0x4D, 0x5B, 0xCF, 0x11, 0xA8, 0xFD, 0x00, 0x80, 0x5F, 0x5C, 0x44, 0x2B};

const unsigned char asf_stream_video[16] =
	{0xC0, 0xEF, 0x19, 0xBC, 0x4D, 0x5B, 0xCF, 0x11, 0xA8, 0xFD, 0x00, 0x80, 0x5F, 0x5C, 0x44, 0x2B};

void print_guid(uint8_t *buf)
{
	int i;

	for(i=0; i<16; i++) {
		printf(" %02x", buf[i]);
	}
}

int probe_asf(FILE *f, int len, int info)
{
	struct asf_header hdr;

	fseek(f, 0, SEEK_SET);

	//XXX sizeof(hdr) returnerar 32 men ska vara 30. WTF?!?! (amd64)
	//printf("sizeof(hdr) = %ld\n", sizeof(hdr) );

	if (fread(&hdr, 30, 1, f) != 1)
		return E_READERROR;

	/* Look for ASF header */
	if (len <= 16 || memcmp(hdr.guid, asf_sig, 16))
		return E_PROBEFAIL;

	/*
	printf("size of header       : %ld\n", hdr.size);
	printf("number of hdr objects: %ld\n", hdr.num);
	*/
	struct asf_object obj;

	int i, is_video = 0;
	for (i=0; i<hdr.num; i++) {

		if (fread(&obj, sizeof(obj), 1, f) != 1)
			return E_READERROR;

		if (memcmp(obj.guid, asf_stream_properties_object, 16) == 0) {

			struct asf_stream_properties prop;

			//XXX read whole object??
			if (fread(&prop, sizeof(prop), 1, f) != 1)
				return E_READERROR;

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

	return E_PROBESUCCESS;
}

