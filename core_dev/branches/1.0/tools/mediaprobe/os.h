#ifndef OS_H
#define OS_H

// swaps a 16-bit value
static inline uint16_t swap16(uint16_t v)
{
  return (v<<8)|(v>>8);
}

// swaps a 32-bit value
static inline uint32_t swap32(uint32_t v)
{
  return (v<<24)|((v<<8)&0xff0000)|((v>>8)&0xff00)|(v>>24);
}

#ifdef WORDS_BIGENDIAN

#define READ16LE(x) \
  swap16(*((uint16_t *)(x)))
#define READ32LE(x) \
  swap32(*((uint32_t *)(x)))
#define WRITE16LE(x,v) \
  *((u16 *)x) = swap16((v))
#define WRITE32LE(x,v) \
  *((u32 *)x) = swap32((v))

#else /* WORDS_BIGENDIAN */

#define READ16LE(x) \
  *((uint16_t *)x)
#define READ32LE(x) \
  *((uint32_t *)x)
#define WRITE16LE(x,v) \
  *((uint16_t *)x) = (v)
#define WRITE32LE(x,v) \
  *((uint32_t *)x) = (v)
#endif

#endif /* OS_H */
