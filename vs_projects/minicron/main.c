#include <stdlib.h>
#include <stdio.h>
#include <stdarg.h>
#include <string.h>
#include <time.h>

#include <windows.h> //för Sleep()


#include "types.h"
#include "strings.h"

typedef struct {
	uint32 interval;
	uint32 count;
	uint32 times_executed;
	char *name;
	char *command;
} event;

event EVENT_ARRAY[10];
int total_events;

char *config_file = "..\\minicron.cfg";

#define MAX_BUFLEN		100
int parse_config()
{
	FILE *fp;
	char *buf, *section_name;
	int len, val;
	
	int current = -1;

	printf("Reading %s ...\n\n", config_file);

	fp = fopen(config_file, "rb");
	if (fp == NULL) {
		printf("Error: File %s not found.\n", config_file);
		return -1;
	}

	buf = malloc(MAX_BUFLEN);
	section_name = malloc(MAX_BUFLEN);

	while (!feof(fp)) {
		fgets(buf, MAX_BUFLEN, fp);
		trim(buf);
		
		len = (int)strlen(buf);

		if (len && buf[0] != ';')
		{
			/* Parse config line */
			if (buf[0] == '[' && buf[len-1] == ']')
			{
				memcpy(section_name, buf+1, len-2);
				section_name[len-2] = 0;
				
				if (strncmp(section_name, "settings", 8) != 0) current++;
				//printf("section %s = %d\n", section_name, current);
			} else {
				if (strncmp(section_name, "settings", 8) == 0)
				{
					//todo: read service settings here
					//printf("Reading setting %s\n", buf);

				} else {
					//printf("[%s]: %s\n", section_name, buf);
					
					if (strncmp(buf, "interval=", 9) == 0)
					{
						sscanf(buf+9, "%u", &val);
						EVENT_ARRAY[current].interval = val;
						//printf("# %d interval: %d\n", current, EVENT_ARRAY[current].interval);
					}
					else if (strncmp(buf, "do=", 3) == 0)
					{
						EVENT_ARRAY[current].command = malloc(len-2); //fixme: mer än 1 do=kommando på samma event är lika med kaos här

						memcpy(EVENT_ARRAY[current].command, buf+3, len-3);
						EVENT_ARRAY[current].command[len-3] = 0;
						
						//printf("# %d do: %s\n", current, EVENT_ARRAY[current].command);
					} else {
						printf("Unknown setting: %s\n", buf);
					}
				}
			}
		}
	}

	total_events = current+1;

	fclose(fp);
	free(buf);
	free(section_name);

	return 0;
}

void list_config()
{
	int i;

	printf("Found %d events to execute:\n", total_events);

	for (i=0; i<total_events; i++)
	{
		if (EVENT_ARRAY[i].command) {
			printf("Command %d: %s every %d second\n", i, EVENT_ARRAY[i].command, EVENT_ARRAY[i].interval);
			
		}
	}
}

//inline
int find_current_event()
{
	int i;
	
	for (i=0; i<total_events; i++)
	{
		if (EVENT_ARRAY[i].count == EVENT_ARRAY[i].interval)
		{
			EVENT_ARRAY[i].count = 0;
			return i;
		}
	}
	return -1;
}

int main(int argc, char *argv[])
{
	int i, cnt, current_event = -1;

	double time_diff;
	time_t time_now, time_started = time(NULL);

	//init:
	for (i=0; i<10; i++) {
		EVENT_ARRAY[i].interval = 0;
		EVENT_ARRAY[i].count = 0;
		EVENT_ARRAY[i].times_executed = 0;
		EVENT_ARRAY[i].command = NULL;
	}

	if (parse_config() != 0) {
		printf("parse_config() failed, exiting");
		return 0;
	}
	
	list_config();
	printf("Executing %d events...\n\n", total_events);


	/* main loop */
	cnt = 0;
	do {
		//update event counters
		for (i=0; i<total_events; i++)
			EVENT_ARRAY[i].count++;

		//find out if any event should be processed right now
		check_again:
		current_event = find_current_event();

		if (current_event >= 0)
		{
			time_now = time(NULL);
			time_diff = difftime(time_now, time_started);

			EVENT_ARRAY[current_event].times_executed++;
			printf("%f: Execution %d of %s\n", time_diff, EVENT_ARRAY[current_event].times_executed, EVENT_ARRAY[current_event].command);

			//printf("Server is now offline. It was running for %s, serving %d clients.\n", timediff_string(time_diff), serv_total_served);

			//todo: open new thread to execure event in
			
			/* Are there more events to process at the moment? */
			goto check_again;
		}

		cnt++;
		//printf("time %d\n", cnt);

		//linux/cygwin: sleep(1);

		Sleep(1000);

	} while (1);

	return 0;
}
