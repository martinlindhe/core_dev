#ifndef PROBE_ASF_H
#define PROBE_ASF_H

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

int probe_asf(FILE *f, int len, int info);
void print_guid(uint8_t *buf);

#endif /* PROBE_ASF_H */
