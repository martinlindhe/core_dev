/*	
	MQ2Vanguard by TeachersPet
	
	OnSendPacket borrowed from randomguy_01's MQ2Ghost
	
	This plugin protects people using warp from being banned
	by ensuring Movement deltas are set to zero.  That is SoE's
	primary red flag for warp and their only method of telling
	to my knowledge without actually witnessing the act.
*/

#include "../MQ2Plugin.h"

PreSetup("MQ2Vanguard");

DWORD memchecks_addr = (DWORD)GetProcAddress(ghModule, "memchecks");

#define OP_Movement 0x178a

// THIS STRUCT WAS MOST DEFINITELY NOT STOLEN FROM ODESSA (I think maybe please?)
typedef struct _MovePacket {
     unsigned short SpawnID;
     unsigned short TimeStamp;
     int ZHeading:12;
     unsigned int HeadingRemainder:2;
     unsigned int Heading:10;
     unsigned int paddingHeading:8;
     float DeltaY;
     unsigned int Animation:10;
     unsigned int paddingAnimation:22;
     float DeltaX;
     float Y;
     int DeltaHeading:10;
     int paddingDeltaHeading:22;
     float DeltaZ;
     float Z;
     float X;
} MovePacket, *PMovePacket;

BOOL PluginsSendPacket(DWORD Type, PVOID Packet, DWORD Size)
{
	typedef BOOL (__cdecl *fMQSendPacket)(DWORD, PVOID, DWORD);
	bool bSend = true;
	PMQPLUGIN pPlugin = pPlugins;
	while(pPlugin)
	{
		fMQSendPacket SendPacket = (fMQSendPacket)GetProcAddress(pPlugin->hModule, "OnSendPacket");
		if (SendPacket)
		{
			if (!SendPacket(Type, Packet, Size)) bSend = false;
		}
		pPlugin = pPlugin->pNext;
	}
	return bSend;
}

DETOUR_TRAMPOLINE_EMPTY(VOID memchecks_trampoline(PVOID, DWORD, PCHAR, DWORD, BOOL));
VOID memchecks_detour(PVOID A, DWORD B, PCHAR C, DWORD D, BOOL E)
{
	if (PluginsSendPacket(B, C, D)) memchecks_trampoline(A, B, C, D, E);
}

PLUGIN_API BOOL OnSendPacket(DWORD Type, PVOID Packet, DWORD Size)
{
	if (Type == OP_Movement)
	{
		PMovePacket MovePkt = (PMovePacket)Packet;
		if(MovePkt->DeltaX > 7 || MovePkt->DeltaX < -7)
			MovePkt->DeltaX = 0;
		if(MovePkt->DeltaY > 7 || MovePkt->DeltaY < -7)
			MovePkt->DeltaY = 0;
		if(MovePkt->DeltaZ > 7 || MovePkt->DeltaZ < -7)
			MovePkt->DeltaZ = 0;
	}
	return true;
}

PLUGIN_API VOID InitializePlugin(VOID)
{
	EzDetour(memchecks_addr, memchecks_detour, memchecks_trampoline);
}

PLUGIN_API VOID ShutdownPlugin(VOID)
{
	RemoveDetour(memchecks_addr);
}