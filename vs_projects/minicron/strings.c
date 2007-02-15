#include <stdio.h>
#include <string.h>
#include <wchar.h>

#include "types.h"

/* Collection of string handling functions */

void trim(char *buf)
{
	int i,j,x=0;

	/* Replace all line breaks and tabs with spaces */
	for (i=0; i<(int)strlen(buf); i++)
	{
		if (buf[i] == '\t') buf[i] = ' ';
		if (buf[i] == '\r') buf[i] = ' ';
		if (buf[i] == '\n') buf[i] = ' ';
	}

	/* LTRIM */
	for (i=0; i<(int)strlen(buf); i++)
		if (buf[i] != ' ') break;

	for (j=i; j<(int)strlen(buf); j++)
		buf[x++] = buf[j];

	buf[x]=0;

	/* RTRIM */
	if (strlen(buf) == 0) return;
	for (i=(int)strlen(buf)-1; i>0; i--)
		if (buf[i] != ' ') break;

	buf[i+1]=0;
}

void wctrim(wchar_t *buf)
{
	int i,j,x=0;
	
	/* LTRIM */
	for (i=0; i<(int)wcslen(buf); i++) {
		if (buf[i] == '\t') buf[i] = ' ';
		if (buf[i] == '\r') buf[i] = ' ';
		if (buf[i] == '\n') buf[i] = ' ';
	}
	
	for (i=0; i<(int)wcslen(buf); i++) {
		if (buf[i] != ' ') break;
	}
	for (j=i; j<(int)wcslen(buf); j++) {
		buf[x++] = buf[j];
	}
	buf[x]=0;

	/* RTRIM */
	if (wcslen(buf) == 0) return;
	for (i=(int)wcslen(buf)-1; i>0; i--) {
		if (buf[i] != ' ') break;
	}
	buf[i+1]=0;
}
