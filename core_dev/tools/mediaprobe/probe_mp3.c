/**
 * MP3 audio probe
 *
 * Status: works with a few samples
 *
 * ID3 tags:
 * http://www.id3.org/id3v2.3.0
 * http://www.id3.org/id3v2.4.0-structure
 *
 * MPEG format:
 * http://www.mpgedit.org/mpgedit/mpeg_format/MP3Format.html
 */

//TODO id3: handle more id3 versions
//TODO id3: show details of the id3 tag (artis, title, year etc)
//TODO id3: separate id3 code in it's own functions since other formats can use it

//TODO mp3: show details of the mp3 (duration, audio channels, bitrate etc)

#include <stdio.h>
#include <string.h>
#include <inttypes.h>

#include "mediaprobe.h"

/**
 * Look for MP3 audio
 */
int probe_mp3(FILE *f, int len, int info)
{
	fseek(f, 0, SEEK_SET);

	if (len < 0x10)
		return E_PROBEFAIL;

	//find & skip ID3 tags
	uint32_t id3_tag = read32be(f);
	if (id3_tag == MKTAG4('I', 'D', '3', 3) ||	//ID3v2.3 tag (most commonly used in the wild)
		id3_tag == MKTAG4('I', 'D', '3', 4)) 	//ID3v2.4 tag (current standard since 2000)
	{
		uint8_t id3_minor_ver = read8(f); //(0x00) of id3 minor version field
		uint8_t id3_flags = read8(f);
		uint32_t id3_size = read32be_ss(f);

#ifdef DEBUG
		printf("id3 minor version: 0x%02X\n", id3_minor_ver );
		printf("id3 flags: 0x%02X\n", id3_flags );
		printf("id3 size: 0x%08X\n", id3_size );
#endif
		if (!id3_flags) {
			skip(f, id3_size);	//XXX this will point on 0xFF 0xF? bytes (frame start marker for mpeg)
		} else {
			/*
			   The ID3v2 tag size is the sum of the byte length of the extended
			   header, the padding and the frames after unsynchronisation. If a
			   footer is present this equals to ('total size' - 20) bytes, otherwise
			   ('total size' - 10) bytes.
			*/
			printf("ERROR unhandled id3 flags!\n");	//TODO: id3 tag can have "extended header" etc
		}
	} else {
		fseek(f, 0, SEEK_SET);
	}

	uint32_t mpeg_header = read32be(f);
	if (mpeg_header && 0xFFF00000 == 0xFFF00000) {

		if (!info) {
			printf("audio/mpeg\n"); //FF2 = 'audio/x-mpeg', IE7 = 'audio/mpeg'
		} else {
			printf("Format: MP3\n");
			printf("Mediatype: audio\n");
			printf("Mimetype: audio/mpeg\n");
		}

		return E_PROBESUCCESS;
	}

	//printf("cant find mpeg frame start marker: %08x\n", mpeg_header);	//FFFA9344

	return E_PROBEFAIL;
}
