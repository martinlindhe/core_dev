//From: http://www.redguides.com/forums/showthread.php?t=11696&page=1&pp=40
//Last updated 2006-12-27 

// MQ2Trek.cpp : Defines the entry point for the DLL application.
//
////////////////////////////////////////////////////////////////////////////////
// Available /commands :
////////////////////////////////////////////////////////////////////////////////
//
//	/warp s[uccor]					- Warps you to zone safe/succor spot.
//	/warp t[arget][ n|s|e|w <dist>]	- Warps you to your current target.
//										If n, s, e or w is specified the dist.
//										must also be given, then you'll warp to
//										the location <dist> units in the chosen
//										direction of your current target.
//	/warp i[tem][ n|s|e|w <dist>]	- Warps you to your current itemtarget.
//										If n, s, e or w is specified the dist.
//										must also be given, then you'll warp to
//										the location <dist> units in the chosen
//										direction of your current itemtarget.
//	/warp d[ir] <distance>			- Warps you <distance> units in the
//										direction you're currently facing.
//	/warp z <distance>				- Warps you <distance> units on the Z axis.
//										Same as /zwarp.
//	/warp loc <Y X Z>				- Warps you to the specified Y X Z coord.
//	/warp wp <WP>					- Warps you to a previously saved Waypoint.
//	/warp last						- Warps you to the last spot you warped to
//										with any of the above commands.
//
//	/zwarp <distance>				- Works like the /warp z command. Left in
//										for legacy.
//
//	/zone [force ]<Zone>[ WP]		- (Chain)Zones you to the specified Zone and
//										if specified, at the waypoint in that
//										zone.
//										Zone must be the Zone.ShortName (ex. 
//										poknowledge and not plane of knowledge).
//										If the command is used with the "force"
//										parameter, it will try to zone you
//										directly to the specified Zone, you'll
//										most likely	Crash To Desktop (CTD) in
//										the process.
//
//	/findpath <Zone>				- Shows the path /zone will take, to bring
//										you to the specified Zone.
//
//	/fade succor					- Fades you to the zone safe/succor spot.
//	/fade loc <Y X Z>				- Fades you to the specified coordinates.
//	/fade <WP>						- Fades you to the specified Waypoint.
//
//	/waypoint add <WP>				- Works like the /zone setwp command.
//	/waypoint del <WP>				- Works like the /zone clearwp command.
//	/waypoint list					- Lists all the waypoints set in your
//										current zone.
//
//	/evac							- Works like the /fade succor command.
//	/succor							- Works like the /fade succor command.
//	/exactloc						- Gives you your exact location in the zone.
//	/gate							- Teleports you to your bind spot.
//
////////////////////////////////////////////////////////////////////////////////
// Comment the following out in your MQ2CommandsAPI.cpp:
////////////////////////////////////////////////////////////////////////////////
//
//	if (!stricmp(Command,"/warp"))
//	{
//		Function=0;
//	}
//
////////////////////////////////////////////////////////////////////////////////
// Comment the following out from MQ2Commands.cpp due to anti-warp coding
////////////////////////////////////////////////////////////////////////////////
//
// VOID PluginCmdSort(VOID)
// {
//  /*
//    PMQCOMMAND pCmd=pCommands;
//    int i;
//    while(pCmd) {
//        if (pCmd->EQ==0) {
//            //
//            for(i=0;i<sizeof(keyarray)/4;i+=4) {
//                if (!stricmp(pCmd->Command, (char *)&keyarray[i])) {
//	            pCmd->Function=CmdCmd;
//                }
//            }
//        }
//        pCmd=pCmd->pNext;
//    }
//  */
// }
//
//
////////////////////////////////////////////////////////////////////////////////
// Change the following line in your EQData.h file to the following:
////////////////////////////////////////////////////////////////////////////////
//
// #define   MAX_ZONES                                    0x189
//
////////////////////////////////////////////////////////////////////////////////
#define PLUGIN_NAME	"MQ2Trek"	// Plugin Name
#define PLUGIN_DATE	20061227	// Plugin Date
#define PLUGIN_VERS	2.1.0		// Plugin Version

#include "../MQ2Plugin.h"

#define CDisplay__MoveLocalPlayerToSuccorCoords 0x45F100 //20070117
#define LocalCEverQuest__DoTheZone  0x4D51C0 //20070117

PreSetup("MQ2Trek");

#undef ZoneToGoTo
// IF you have Infinity set to anything other than this, Visual Studio 2k5
// will freak out.  I managed to find this tidbit through a google search and
// have had no problems in any version of Visual Studio.
#define INFINITY ((1 << (8*sizeof (int) - 6)) - 4)


#ifdef PKT_UPDATE_POSITION
#undef PKT_UPDATE_POSITION
#endif
#define PKT_UPDATE_POSITION 0x178a		// 06-16-06
#ifdef PKT_CHANNEL_MESSAGE
#undef PKT_CHANNEL_MESSAGE
#endif
#define PKT_CHANNEL_MESSAGE 0xB5A		// 06-16-06
#define PKT_MOVEMENT_TRACKING 0x7b0f
bool HaveWarped = false;

#undef dothewrap
#undef wrap
#undef zwrap
#undef ExLoc
#undef waypoint

VOID dothewrap(float y, float x, float z);
VOID wrap(PSPAWNINFO pChar, PCHAR szLine);
VOID zwrap(PSPAWNINFO pChar, PCHAR szLine);
VOID ExLoc(PSPAWNINFO pChar);
VOID waypoint(PSPAWNINFO pChar, PCHAR szLine);


typedef struct
{
	int connections[100]; // An array of edges which has this as the starting node
	int numconnect;
} FWVertice;

typedef struct
{
	int Zone;
	char Name[50];
	char Phrase[20][50];
	int Destination[20];
	float X,Y,Z;
	int DestCnt;
} NPCTeleporter;

FWVertice *V=NULL;
NPCTeleporter NPCs[100];
int NPCCnt;
int* distances=NULL;
int* predecessor=NULL;

class LocalCEverQuest;
class LocalCEverQuest
{
public:
	__declspec(dllexport) char * LocalCEverQuest::DoTheZone(int, char *, int, int, float, float, float, int);
};

#ifdef LocalCEverQuest__DoTheZone
FUNCTION_AT_ADDRESS(char * LocalCEverQuest::DoTheZone(int, char *, int, int, float, float, float, int), LocalCEverQuest__DoTheZone);
#endif

LocalCEverQuest **ppLEQ;
#define pLEQ (*ppLEQ)

VOID Setup();
bool UseNPC(PSPAWNINFO pChar, int dest);
PLUGIN_API VOID OnZoned(PSPAWNINFO pChar, PCHAR szLine);
PLUGIN_API VOID OnPulse(VOID);
DWORD ListSimilarZones(PCHAR ZoneShortName);
VOID ChangeZones(PSPAWNINFO pChar, PCHAR szLine);
VOID FloydWarshall(FWVertice* vertices, int nodecount);
VOID FindPath(PSPAWNINFO pChar, PCHAR szLine);
VOID SimFade(PSPAWNINFO pChar, PCHAR szLine);
VOID SimGate(PSPAWNINFO pChar, PCHAR szLine);
VOID SimSuccor(PSPAWNINFO pChar, PCHAR szLine);
VOID SetupNextZone(PSPAWNINFO pChar, int dest);
VOID MyOnZoned(PSPAWNINFO pChar);

bool ZoneChange=false;
float X, Y, Z;
int Heading;
int DestZone;
int ChainZone[100];
int ChainZoneCnt = 0;
int DestType;			// 0=use supplied coords 1=succorpoint
int ChainZoneType;		// 0=use supplied coords 1=succorpoint
int ZoneReason;

int LastKnownZone = -1;	//don't fire MyOnZoned when you first log in

VOID MyOnZoned(PSPAWNINFO pChar)
{
	if (ChainZoneCnt>0)
	{
		if (pChar->Zone != ChainZone[ChainZoneCnt])
		{
			ChainZoneCnt = 0;
			return;
		}
		ChainZoneCnt--;
		DestZone = ChainZone[ChainZoneCnt];
		if (ChainZoneCnt == 0) DestType = ChainZoneType;
		WriteChatColor("Attempting ChainZone...", USERCOLOR_DEFAULT);
		if (UseNPC(pChar, DestZone) == false)
		{
			ZoneChange = true;
		}
	}
	return;
}

VOID SetupNextZone(PSPAWNINFO pChar, int dest)
{
	CHAR sKeyData[MAX_STRING] = {0};
	CHAR sDefault[MAX_STRING] = "none";
	GetPrivateProfileString(GetShortZone(dest), "default", sDefault, sKeyData, MAX_STRING, INIFileName);
	if (_stricmp(sKeyData, "none") != 0)
	{
		sscanf(sKeyData, "%f %f %f %d", &Y, &X, &Z, &Heading);
		DestType = 0;
	}
	else
	{
		Y = pChar->Y;
		X = pChar->X;
		Z = pChar->Z;
		HaveWarped = true;
		Heading = (int)pChar->Heading;
		DestType = 1;
	}
	return;
}

PLUGIN_API VOID OnPulse(VOID)
{
	PSPAWNINFO pChar = NULL;
	if (ppCharSpawn && pCharSpawn) {
		pChar = (PSPAWNINFO)pCharSpawn;
		if ((pChar) && (!gZoning))
		{
			if (LastKnownZone == -1) LastKnownZone = pChar->Zone;
			if (pChar->Zone != LastKnownZone)
			{
				LastKnownZone = pChar->Zone;
				MyOnZoned(pChar);
			}
		}
	}

	char aa[100] = "test";
	if(ZoneChange)
	{
		ZoneChange = false;
		pLEQ->DoTheZone(DestZone, aa, DestType, ZoneReason, Y, X, Z, Heading);
	}
	return;
}

DWORD ListSimilarZones(PCHAR PartialName)
{
	CHAR szMsg[MAX_STRING] = "Bad Zone.ShortName. Suggest: ";
	CHAR szName[MAX_STRING] = {0};
	char *partial, *longname;

	PZONELIST pZone = NULL;

	partial=_strlwr(_strdup(PartialName));
	if (!ppWorldData | !pWorldData) return -1;
	for (int nIndex = 0; nIndex < MAX_ZONES+1; nIndex++)
	{
		pZone = ((PWORLDDATA)pWorldData)->ZoneArray[nIndex];
		if (pZone)
		{
			longname = _strlwr(_strdup(pZone->LongName));
			if (strstr(longname, partial))
			{
				sprintf(szName, "%s(%s) ", pZone->LongName, pZone->ShortName);
				if ((strlen(szMsg)+strlen(szName)) >= 300)
				{
					WriteChatColor(szMsg, USERCOLOR_DEFAULT);
					szMsg[0] = 0;
				}
				strcat(szMsg, szName);
			}
			free(longname);
		}
	}
	WriteChatColor(szMsg, USERCOLOR_DEFAULT);
	free(partial);
	return -1;
}

VOID ChangeZones(PSPAWNINFO pChar, PCHAR szLine)
{
	CHAR szMsg[MAX_STRING] = {0};
	CHAR szParam[MAX_STRING] = {0};
	CHAR sZoneName[MAX_STRING] = {0};
	CHAR sWPName[MAX_STRING] = {0};
	CHAR sKeyData[MAX_STRING] = {0};
	DWORD ZoneToGoTo;
	int rLen;
	int i, j, cnt = 0;
	bool IgnoreChain = false;
	int Param = 1;

	GetArg(szParam, szLine, Param);
	if (_stricmp(szParam, "force") == 0)
	{
		IgnoreChain = true;
		Param++;
	}
	GetArg(sZoneName, szLine, Param++);
	ZoneToGoTo = GetZoneID(sZoneName);
	if (ZoneToGoTo == -1)
	{
		ListSimilarZones(sZoneName);
		return;
	}
	Setup();
	if (IgnoreChain == false)
	{
		i = pChar->Zone;
		j = ZoneToGoTo;

		if (distances[i*MAX_ZONES+j] == 0)
		{
			WriteChatColor("Poof! You are already there.", CONCOLOR_RED);
			return;
		}
		if (distances[i*MAX_ZONES+j] == INFINITY)
		{
			WriteChatColor("I don't know a route to that zone.", CONCOLOR_RED);
			return;
		}
	}
	GetArg(sWPName, szLine, Param++);
	if (sWPName[0] != 0)
	{
		CHAR sDefault[MAX_STRING] = "none";
		GetPrivateProfileString(sZoneName, sWPName, sDefault, sKeyData, MAX_STRING, INIFileName);
		if (_stricmp(sKeyData, "none") == 0)
		{
			rLen = GetPrivateProfileString(sZoneName, NULL, sDefault, sKeyData, MAX_STRING, INIFileName);
			for (int i = 0; i < rLen-1; i++) if (sKeyData[i] == 0) sKeyData[i] = ',';
			sprintf(szMsg, "Bad Waypoint. Suggest: %s", sKeyData);
			WriteChatColor(szMsg, USERCOLOR_DEFAULT);
			return;
		}
		sscanf(sKeyData, "%f %f %f %d", &Y, &X, &Z, &Heading);
		DestType = 0;
	}
	else
	{
		sprintf(sWPName, "default");
		CHAR sDefault[MAX_STRING] = "none";
		GetPrivateProfileString(sZoneName, sWPName, sDefault, sKeyData, MAX_STRING, INIFileName);
		if (_stricmp(sKeyData, "none") != 0)
		{
			sscanf(sKeyData, "%f %f %f %d", &Y, &X, &Z, &Heading);
			DestType = 0;
		}
		else
		{
			Y = pChar->Y;
			X = pChar->X;
			Z = pChar->Z;
			Heading = (int)pChar->Heading;
			HaveWarped = true;
			DestType = 1;
		}
	}

	ChainZoneCnt = 0;   //reset in case we failed to finish previous chain-zone attempt
	if (IgnoreChain == false)
	{
		while (i != j)
		{
			ChainZone[ChainZoneCnt++] = j;
			j = predecessor[i*MAX_ZONES+j];
		}
		if (ChainZoneCnt > 1)
		{
			ChainZoneType = DestType;
			DestType = 1;
		}
		DestZone = ChainZone[--ChainZoneCnt];
	}
	else
	{
		DestZone = ZoneToGoTo;
	}

	sprintf(szMsg, "Zoneing...");
	WriteChatColor(szMsg, USERCOLOR_DEFAULT);
	ZoneReason = 0;
	if (UseNPC(pChar,DestZone) == false) ZoneChange = true;
	return;
}

VOID FindPath(PSPAWNINFO pChar, PCHAR szLine)
{
	CHAR sDest[MAX_STRING]={0};
	int ZoneToGoTo;
	int i, j, cnt = 0, stops[100];

	GetArg(sDest, szLine, 1);
	if (sDest[0] == 0)
	{
		WriteChatColor("Usage: /FindPath <ShortZoneName>", CONCOLOR_RED);
		return;
	}
	ZoneToGoTo = GetZoneID(sDest);

	if (ZoneToGoTo == -1) {
		ListSimilarZones(sDest);
		return;
	}

	Setup();

	i = pChar->Zone;
	j = ZoneToGoTo;

	if (distances[i*MAX_ZONES+j] == 0)
	{
		WriteChatColor("Poof! You are already there.", CONCOLOR_RED);
		return;
	}
	if (distances[i*MAX_ZONES+j] == INFINITY)
	{
		WriteChatColor("I don't know a route to that zone.", CONCOLOR_RED);
		return;
	}
	WriteChatColor("My path:", CONCOLOR_RED);
	while (i != j)
	{
		stops[cnt++] = j;
		j = predecessor[i*MAX_ZONES+j];
	}
	while (cnt > 0)
	{
		cnt--;
		WriteChatColor(GetShortZone(stops[cnt]), USERCOLOR_DEFAULT);
	}
}

VOID Setup()
{
	char sKey[300];
	char sKeyData[300];
	char *p;
	int i;
	if (distances != NULL) free(distances);
	if (predecessor != NULL) free(predecessor);
	if (V != NULL) free(V);
	V = (FWVertice *)malloc(MAX_ZONES*sizeof(FWVertice));
	int tmp[100], cnt;
	for (i = 0; i < MAX_ZONES; i++)
	{
		sprintf(sKey, "%d", i);
		V[i].numconnect = 0;
		GetPrivateProfileString("ZoneConnections", sKey, "none", sKeyData, 300, INIFileName);
		if (_stricmp(sKeyData,"none") != 0)
		{
			strtok(sKeyData, "\"");
			strtok(NULL, "\"");
			cnt = 0;
			while ((p = strtok(NULL, ",")) != NULL)
			{
				tmp[cnt++] = atoi(p);
			}
			V[i].numconnect = cnt+1;
			for (int j = 0; j < cnt; j++) V[i].connections[j] = tmp[j];
		}
		V[i].connections[V[i].numconnect++] = GetCharInfo2()->ZoneBoundID; //always connected to my bind point
	}

	//NPCTeleporter support
	NPCCnt = 0;
	for (i = 0; i < 100; i++)
	{
		sprintf(sKey, "%d", (i+1));
		GetPrivateProfileString("NPCTeleporters", sKey, "none", sKeyData, 300, INIFileName);
		if (_stricmp(sKeyData, "none") == 0) break;
		NPCCnt++;
		NPCs[i].Zone = atoi(strtok(sKeyData, " \""));
		strcpy(NPCs[i].Name, strtok(NULL, "\""));
		NPCs[i].Y = (float)atof(strtok(NULL, " "));
		NPCs[i].X = (float)atof(strtok(NULL, " "));
		NPCs[i].Z = (float)atof(strtok(NULL, " "));
		NPCs[i].DestCnt = 0;
		p=strtok(NULL, " \"");
		while (p != NULL)
		{
			NPCs[i].Destination[NPCs[i].DestCnt] = atoi(p);
			strcpy(NPCs[i].Phrase[NPCs[i].DestCnt], strtok(NULL, "\""));
			V[NPCs[i].Zone].connections[V[NPCs[i].Zone].numconnect++] = NPCs[i].Destination[NPCs[i].DestCnt];
			NPCs[i].DestCnt++;
			p = strtok(NULL, " \"");
		}
	}

	FloydWarshall(V, MAX_ZONES);
}

bool UseNPC(PSPAWNINFO pChar, int dest)
{
	for (int i = 0; i < NPCCnt; i++)
	{
		if (NPCs[i].Zone != pChar->Zone) continue;
		for (int j = 0; j < NPCs[i].DestCnt; j++)
		{
			if (NPCs[i].Destination[j] == dest)
			{
				//Code borrowed directly from MQ2CSum
				// setup move packet
				struct _MOVEPKT {
					/*0000*/ unsigned short SpawnID;
					/*0002*/ unsigned short TimeStamp;
					/*0004*/ int Heading:16;
					/*0006*/ int unknown1:16;     //??
					/*0008*/ float DeltaZ;        // ?? not sure
					/*0012*/ int Animation:16;
					/*0014*/ int padding014:16;   //??
					/*0016*/ int DeltaHeading:16; //?? not sure
					/*0018*/ int unknown2:16;     //??
					/*0020*/ float Y;
					/*0024*/ float DeltaY;        //?? not sure
					/*0028*/ float DeltaX;        //?? not sure
					/*0032*/ float Z;
					/*0036*/ float X;
				} P;
				struct _MSGPACKET {
					/*0000*/ char target[64];
					/*0064*/ char sender[64];
					/*0128*/ unsigned int language;
					/*0132*/ unsigned int channel;
					/*0136*/ char padding136[8];
					/*0144*/ unsigned int languageskill;
					/*0148*/ char message[100];
				} M;

				// init packets
				ZeroMemory(&P, sizeof(P));
				ZeroMemory(&M, sizeof(M));
				P.SpawnID = (unsigned short)pChar->SpawnID;
				P.Heading = (unsigned int)(pChar->Heading * 4);

				PSPAWNINFO psTarget = NULL;
				Target(pChar, NPCs[i].Name);
				if (ppTarget && pTarget)
				{
					psTarget = (PSPAWNINFO)pTarget;
				}
				if (psTarget)
				{
					strcpy(M.target, psTarget->Name);
				}
				strcpy(M.sender, pChar->Name);
				M.channel = 8;
				M.languageskill = 100;

				// jump to
				P.Z = NPCs[i].Z;
				P.Y = NPCs[i].Y;
				P.X = NPCs[i].X;
				sprintf(M.message, "%s", NPCs[i].Phrase[j]);
				HaveWarped = true;
				SendEQMessage(PKT_CHANNEL_MESSAGE, &M,sizeof(M));
				SendEQMessage(PKT_UPDATE_POSITION, &P, sizeof(P));

				return true;
			}
		}
	}
	return false;
}

VOID FloydWarshall(FWVertice* vertices, int nodecount) // Vertices numbered from 0 to nodecount-1
{
	distances = (int*) malloc(nodecount*nodecount*sizeof(int)*8);
	predecessor = (int*) malloc(nodecount*nodecount*sizeof(int)*8);
	int i,j,k;

	for(i = 0; i < nodecount; i++)
	{
		for(j = 0; j < nodecount; j++)
		{
			distances[i*nodecount+j] = 0;
			predecessor[i*nodecount+j] = i;
		}
	}
	for(i = 0; i < nodecount; i++)
	{
		for(j = 0; j < vertices[i].numconnect; j++)
		{
			distances[i*nodecount + vertices[i].connections[j]] =1;
//			vertices[i].connections[j].weight;
		}
		for(j = 0; j < nodecount; j++)
		{
			if(!distances[i*nodecount+j] && (i^j))
			{
				// i ^ j returns 0 if they are equal
				distances[i*nodecount+j] = INFINITY;
			}
		}
	}
	for(k = 0; k < nodecount; k++)
	{
		for(i = 0; i < nodecount; i++)
		{
			for(j = 0; j < nodecount; j++)
			{
				if(distances[i*nodecount+j] > distances[i*nodecount+k] + distances[k*nodecount+j])
				{
					distances[i*nodecount+j] = distances[i*nodecount+k] + distances[k*nodecount+j];
					predecessor[i*nodecount+j] = predecessor[k*nodecount+j];
				}
			}
		}
	}
}

 VOID SimGate(PSPAWNINFO pChar, PCHAR szLine)
{
	CHAR szMsg[MAX_STRING] = {0};
	PCHARINFO2 pChar2 = GetCharInfo2();

	sprintf(szMsg, "Gating...");
	WriteChatColor(szMsg, USERCOLOR_DEFAULT);
	DestZone = pChar2->ZoneBoundID;
	DestType = 0;
	ZoneReason = 11;
	Y = pChar2->ZoneBoundY;
	X = pChar2->ZoneBoundX;
	Z = pChar2->ZoneBoundZ;
	Heading = 0;
	HaveWarped = true;
	ZoneChange = true;
	return;
}


VOID SimFade(PSPAWNINFO pChar, PCHAR szLine)
{
	PZONEINFO TheZone = (PZONEINFO) pZoneInfo;
	CHAR szMsg[MAX_STRING] = {0};
	CHAR sWPName[MAX_STRING] = {0};
	CHAR szY[MAX_STRING] = {0};
	CHAR szX[MAX_STRING] = {0};
	CHAR szZ[MAX_STRING] = {0};
	if (pChar->Instance != 0)
	{
		DestZone = *((int *)(&(pChar->Instance)-1));
	}
	else
	{
		DestZone = pChar->Zone;
	}
	GetArg(sWPName, szLine, 1);
	if (sWPName[0] != 0) {
		if (_stricmp(sWPName, "succor") == 0) {
			DestType = 0;
			ZoneReason = 0;
			Y = TheZone->Unknown0x24c[0];
			X = TheZone->Unknown0x24c[1];
			Z = TheZone->Unknown0x24c[2];
			Heading =(int)pChar->Heading;
			sprintf(szMsg, "Fading to succor (Loc: %f %f %f)...", Y, X, Z);
		} else if (_stricmp(sWPName, "loc") == 0) {
			GetArg(szY, szLine, 2);
			GetArg(szX, szLine, 3);
			GetArg(szZ, szLine, 4);
			if ((szY[0] == 0) || (szX[0] == 0) || (szZ[0] == 0)) {
				WriteChatColor("You must provide <y x z> if going to a location.", CONCOLOR_RED);
				return;
			}
			Y = (float)atof(szY);
			X = (float)atof(szX);
			Z = (float)atof(szZ);
			sprintf(szMsg, "Fading to Loc: %f %f %f ...", Y, X, Z);
			Heading = (int)pChar->Heading;
		} else {
			char szLoc[MAX_STRING] = {0};
			char szBuf[MAX_STRING] = {0};
			char szDestWrapX[MAX_STRING] = {0};
			char szDestWrapY[MAX_STRING] = {0};
			char szDestWrapZ[MAX_STRING] = {0};
			char szMsg[MAX_STRING] = {0};

			GetPrivateProfileString(TheZone->ShortName, sWPName, "", szLoc, MAX_STRING, INIFileName);
			if (!strnicmp(szLoc, "", 1)) {
				sprintf(szMsg, "Waypoint \'%s\' does not exist.", sWPName);
				WriteChatColor(szMsg, COLOR_LIGHTGREY);
				return;
			} else {
				// get destination locs
				GetArg(szDestWrapY, szLoc, 1);
				GetArg(szDestWrapX, szLoc, 2);
				GetArg(szDestWrapZ, szLoc, 3);
				Y = (float)atof(szDestWrapY);
				X = (float)atof(szDestWrapX);
				Z = (float)atof(szDestWrapZ);

				// get heading
				GetArg(szBuf, szLoc, 4);
				Heading = (int)atof(szBuf);

				sprintf(szMsg, "Fading to waypoint \'%s\' (Loc: %f %f %f)...", sWPName, Y, X, Z);
			}
		}
	} else {
		Y = pChar->Y;
		X = pChar->X;
		Z = pChar->Z;
		Heading = (int)pChar->Heading;
		sprintf(szMsg, "Fading (Loc: %f %f %f)...", Y, X, Z);
	}
	WriteChatColor(szMsg, USERCOLOR_DEFAULT);
	DestType = 0;
	ZoneReason = 0;
	HaveWarped = true;
	ZoneChange = true;
	return;
}
// Modified to correspond with as little editing as possible to the eqdata.h file
// thx TheZ
VOID SimSuccor(PSPAWNINFO pChar, PCHAR szLine)
{
	PZONEINFO TheZone = (PZONEINFO) pZoneInfo;
	CHAR szMsg[MAX_STRING] = {0};
	if (pChar->Instance != 0)
	{
		DestZone = *((int *)(&(pChar->Instance)-1));
	}
	else
	{
		DestZone = pChar->Zone;
	}
	sprintf(szMsg, "Succoring...");
	WriteChatColor(szMsg, USERCOLOR_DEFAULT);
	DestType = 0;
	ZoneReason = 0;
	Y = TheZone->Unknown0x24c[0];
	X = TheZone->Unknown0x24c[1];
	Z = TheZone->Unknown0x24c[2];
	HaveWarped = true;
	Heading =(int)pChar->Heading;
	ZoneChange = true;
	return;
}

PLUGIN_API BOOL OnSendPacket(DWORD Type, PVOID Packet, DWORD Size)
{
	if (Type == PKT_MOVEMENT_TRACKING && HaveWarped)
	{
		HaveWarped = false;
		long nextByte = 12;
		for(unsigned long i = 0; i < Size; i++)
		{
			if (i == nextByte)
			{
				PBYTE Detection = (PBYTE)Packet+i;
				if (*Detection == 0x03) {
					*Detection = 0x01;
					return false;
				}
				nextByte = i + 17;
			}
		}
		return true;
	}
	return true;
}

VOID ExLoc(PSPAWNINFO pChar, PCHAR szLine)
{
	CHAR LocMsg[MAX_STRING] = {0};
	sprintf(LocMsg, "Your location is %3.6f, %3.6f, %3.6f", pChar->Y, pChar->X, pChar->Z);
	WriteChatColor(LocMsg);
	return;
}

VOID dothewrap(float y, float x, float z)
{
	PZONEINFO Zone = (PZONEINFO)pZoneInfo;
	float SafeY = Zone->Unknown0x24c[0];
	float SafeX = Zone->Unknown0x24c[1];
	float SafeZ = Zone->Unknown0x24c[2];

	Zone->Unknown0x24c[0] = y;
	Zone->Unknown0x24c[1] = x;
	Zone->Unknown0x24c[2] = z;

	CHAR szMsg[MAX_STRING] = {0};
	sprintf(szMsg, "Warping to: %3.2f, %3.2f, %3.2f.", Zone->Unknown0x24c[0], Zone->Unknown0x24c[1], Zone->Unknown0x24c[2]);
	WriteChatColor(szMsg, COLOR_PURPLE);

	DWORD MLPTSC = CDisplay__MoveLocalPlayerToSuccorCoords;
	__asm call dword ptr [MLPTSC];

	Zone->Unknown0x24c[0] = SafeY;
	Zone->Unknown0x24c[1] = SafeX;
	Zone->Unknown0x24c[2] = SafeZ;
}

VOID zwrap(PSPAWNINFO pChar, PCHAR szLine)
{
	CHAR Z[MAX_STRING] = {0};
	CHAR szMsg[MAX_STRING] = {0};
	GetArg(Z, szLine, 1);
	sprintf(szMsg, "z %s", Z);
	wrap(pChar, szMsg);
	return;
}

VOID wrap(PSPAWNINFO pChar, PCHAR szLine)
{
	static float LastY;
	static float LastX;
	static float LastZ;
	float OffsetX = 0;
	float OffsetY = 0;

	bRunNextCommand = TRUE;
	PSPAWNINFO psTarget = NULL;
	PZONEINFO TheZone = (PZONEINFO) pZoneInfo;
	PGROUNDITEM pItem = (PGROUNDITEM)pGroundTarget;

	CHAR command[MAX_STRING]; GetArg(command, szLine, 1);
	CHAR Y[MAX_STRING]; GetArg(Y, szLine, 2);
	CHAR X[MAX_STRING]; GetArg(X, szLine, 3);
	CHAR Z[MAX_STRING]; GetArg(Z, szLine, 4);

	if (!strnicmp(command, "s", 1) &&	// succor
		!strnicmp(command, "t", 1) &&	// target
		!strnicmp(command, "i", 1) &&	// item
		!strnicmp(command, "d", 1) &&	// direction
		!strnicmp(command, "z", 1) &&	// zwarp
		!strnicmp(command, "lo", 2) &&	// location
		!strnicmp(command, "w", 1) &&	// waypoint
		!strnicmp(command, "la", 2))	// last
	{
		WriteChatColor("Usage: /warp <s[uccor]|t[arget]|i[tem]|last|loc <y x z>|d[ir] <dist>|z <dist>|wp <name>>", CONCOLOR_RED);
		return;
	}
	else
	{
		if (!strnicmp(command, "t", 1))
		{
			if (ppTarget && pTarget) {
				psTarget = (PSPAWNINFO)pTarget;
			}
			if (!psTarget) {
				WriteChatColor("You must have a target for /warp target.", CONCOLOR_RED);
				return;
			}
			if (!strnicmp(Y, "N", 1))
			{
				OffsetY = (float)atof(X);
			}
			if (!strnicmp(Y, "S", 1))
			{
				OffsetY = -(float)atof(X);
			}
			if (!strnicmp(Y, "W", 1))
			{
				OffsetX = (float)atof(X);
			}
			if (!strnicmp(Y, "E", 1))
			{
				OffsetX = -(float)atof(X);
			}
			LastY = psTarget->Y + OffsetY;
			LastX = psTarget->X + OffsetX;
			LastZ = psTarget->Z;
			dothewrap(LastY, LastX, LastZ);
		}
		else if (!strnicmp(command, "s", 1))
		{
			static float north = 0;
			((PSPAWNINFO)pCharSpawn)->Heading = north;
			DWORD MLPTSC = CDisplay__MoveLocalPlayerToSuccorCoords;
			__asm call dword ptr [MLPTSC];
			return;
		}
		else if (!strnicmp(command, "lo", 2))
		{
			if ((Y[0] == 0) || (X[0] == 0) || (Z[0] == 0))
			{
				WriteChatColor("You must provide <y> <x> <z> if going to a location.", CONCOLOR_RED);
				return;
			}
			LastY = (float)atof(Y);
			LastX = (float)atof(X);
			LastZ = (float)atof(Z);
			dothewrap(LastY, LastX, LastZ);
			return;
		}
		else if (!strnicmp(command, "i", 1))
		{
			if (!pGroundTarget)
			{
				WriteChatColor("You must have am item targeted for /warp item. Use /itemtarget to aquire target.", CONCOLOR_RED);
				return;
			}
			if (!strnicmp(Y, "N", 1))
			{
				OffsetY = (float)atof(X);
			}
			if (!strnicmp(Y, "S", 1))
			{
				OffsetY = -(float)atof(X);
			}
			if (!strnicmp(Y, "W", 1))
			{
				OffsetX = (float)atof(X);
			}
			if (!strnicmp(Y, "E", 1))
			{
				OffsetX = -(float)atof(X);
			}
			LastY = (float)pItem->Y + OffsetY;
			LastX = (float)pItem->X + OffsetX;
			LastZ = (float)pItem->Z;
			dothewrap(LastY, LastX, LastZ);
		}
		else if (!strnicmp(command, "la", 2))
		{
			if ((LastY == 0) || (LastX == 0) || (LastZ == 0))
			{
				WriteChatColor("You must have warped before to use this command!.", CONCOLOR_RED);
				return;
			}
			dothewrap(LastY, LastX, LastZ);
			return;
		}
		else if (!strnicmp(command, "d", 1))
		{
			if (Y[0] == 0) {
				WriteChatColor("You MUST provide <dist> if going in your current direction.", CONCOLOR_RED);
				return;
			}
			FLOAT angle = (FLOAT)((pChar->Heading)*0.0123);
			FLOAT disSuccorgoto = (FLOAT)atof(Y);
			LastY = pChar->Y + (float)(disSuccorgoto * cos(angle));
			LastX = pChar->X + (float)(disSuccorgoto * sin(angle));
			LastZ = pChar->Z;
			dothewrap(LastY, LastX, LastZ);
			return;
		}
		else if (!strnicmp(command, "z", 1))
		{
			if (Y[0] == 0) {
				WriteChatColor("Usage: /warp z <dist>", CONCOLOR_RED);
				return;
			}
			LastY = pChar->Y;
			LastX = pChar->X;
			LastZ = pChar->Z + (float)atof(Y);
			dothewrap(LastY, LastX, LastZ);
			return;
		}
		{
			char szLoc[MAX_STRING] = {0};
			char szName[MAX_STRING] = {0};
			char szBuf[MAX_STRING] = {0};
			char szHeading[MAX_STRING] = {0};
			char szDestwrapX[MAX_STRING] = {0};
			char szDestwrapY[MAX_STRING] = {0};
			char szDestwrapZ[MAX_STRING] = {0};
			char szMsg[MAX_STRING] = {0};

			GetArg(szName, szLine, 2);

			if(!strnicmp(szName, "", 1))
			{
				WriteChatColor("You didn't specify a waypoint.", COLOR_LIGHTGREY);
			}
			else
			{
				GetPrivateProfileString(TheZone->ShortName, szName, "", szLoc, MAX_STRING, INIFileName);

				if (!strnicmp(szLoc, "", 1))
				{
					sprintf(szMsg, "Waypoint \'%s\' does not exist.", szName);
					WriteChatColor(szMsg, COLOR_LIGHTGREY);
				}
				else
				{
					// get destination locs
					GetArg(szDestwrapX, szLoc, 2);
					GetArg(szDestwrapY, szLoc, 1);
					GetArg(szDestwrapZ, szLoc, 3);

					// get heading
					GetArg(szBuf, szLoc, 4);
					FLOAT szHeading = (FLOAT)(atof(szBuf));
					szHeading = szHeading*0.703125f;
					sprintf(szMsg, "fast heading %f", szHeading);
					Face(pChar, szMsg);

					// store target location as last location and warp
					LastY = (float)atof(szDestwrapY);
					LastX = (float)atof(szDestwrapX);
					LastZ = (float)atof(szDestwrapZ);
					dothewrap(LastY, LastX, LastZ);
				}
			}
		}
	}
}

VOID waypoint(PSPAWNINFO pChar, PCHAR szLine)
{
	PZONEINFO Zone = (PZONEINFO)pZoneInfo;
	CHAR szTemp[10] = {0};
	CHAR szData[MAX_STRING] = {0};
	CHAR szName[MAX_STRING] = {0};
	CHAR szCommand[MAX_STRING] = {0};
	CHAR szBuffer[MAX_STRING] = {0};
	CHAR szMsg[MAX_STRING] = {0};
	CHAR WaypointList[MAX_STRING*10] = {0};
	PCHAR pWaypointList = WaypointList;
	CHAR szKey[MAX_STRING] = {0};
	CHAR szValue[MAX_STRING] = {0};

	GetArg(szCommand, szLine, 1);
	if (!strnicmp(szCommand, "add", 3))
	{
		GetArg(szName, szLine, 2);
		if(!strnicmp(szName, "", 1))
		{
			WriteChatColor("You didn't specify a name for the waypoint.", COLOR_LIGHTGREY);
		}
		else
		{
			GetPrivateProfileString(Zone->ShortName, szName, "", szBuffer, MAX_STRING, INIFileName);
			if (!strnicmp(szBuffer, "", 1))
			{
				sprintf(szData, "%1.2f %1.2f %1.2f %d", pChar->Y, pChar->X, pChar->Z, (int)pChar->Heading);
				WritePrivateProfileString(Zone->ShortName, szName, szData, INIFileName);
				sprintf(szMsg, "Waypoint \'%s\' added.", szName);
				WriteChatColor(szMsg, COLOR_LIGHTGREY);
			}
			else
			{
				sprintf(szMsg, "Waypoint \'%s\' already exists.", szName);
				WriteChatColor(szMsg, COLOR_LIGHTGREY);
			}
		}
	}
	else if (!strnicmp(szCommand, "del", 3))
	{
		GetArg(szName, szLine, 2);
		if(!strnicmp(szName, "", 1))
		{
			WriteChatColor("You didn't specify a waypoint to delete.", COLOR_LIGHTGREY);
		}
		else
		{
			GetPrivateProfileString(Zone->ShortName, szName, "", szBuffer, MAX_STRING, INIFileName);
			if (!strnicmp(szBuffer, "", 1))
			{
				sprintf(szMsg, "Waypoint \'%s\' does not exist.", szName);
				WriteChatColor(szMsg, COLOR_LIGHTGREY);
			}
			else
			{
				WritePrivateProfileString(Zone->ShortName, szName, "", INIFileName);

				// rewrite the section minus the deleted waypoint
				GetPrivateProfileSection(Zone->ShortName, WaypointList, MAX_STRING*10, INIFileName);
				WritePrivateProfileSection(Zone->ShortName, "", INIFileName);
				pWaypointList = WaypointList;

				while (pWaypointList[0] != 0)
				{
					GetArg(szKey, pWaypointList, 1, 0, 0, 0, '=');
					GetArg(szValue, pWaypointList, 2, 0, 0, 0, '=');
					if (strnicmp(szValue, "", 1))
					{
						WritePrivateProfileString(Zone->ShortName, szKey, szValue, INIFileName);
					}
					pWaypointList += strlen(pWaypointList)+1;
				}

				sprintf(szMsg, "Waypoint \'%s\' deleted.", szName);
				WriteChatColor(szMsg, COLOR_LIGHTGREY);
			}
		}
	}
	else if (!strnicmp(szCommand, "list", 4))
	{
		GetPrivateProfileSection(Zone->ShortName, WaypointList, MAX_STRING*10, INIFileName);
		pWaypointList = WaypointList;
		sprintf(szMsg, "Waypoints for %s", Zone->LongName);
		WriteChatColor(szMsg, CONCOLOR_YELLOW);
		while (pWaypointList[0] != 0)
		{
			GetArg(szName, pWaypointList, 1, 0, 0, 0, '=');
			GetArg(szData, pWaypointList, 2, 0, 0, 0, '=');
			sprintf(szMsg, "- %s", szName);
			WriteChatColor(szMsg, COLOR_LIGHTGREY);
			pWaypointList += strlen(pWaypointList)+1;
		}
	}
	else
	{
		WriteChatColor("Invalid syntax.  Usage:", COLOR_LIGHTGREY);
		WriteChatColor("/waypoint add name", COLOR_LIGHTGREY);
		WriteChatColor("/waypoint del name", COLOR_LIGHTGREY);
		WriteChatColor("/waypoint list", COLOR_LIGHTGREY);
	}
}

// Called once, when the plugin is to initialize
PLUGIN_API VOID InitializePlugin(VOID)
{
	DebugSpewAlways("Initializing MQ2Trek");
	ppLEQ=(LocalCEverQuest**)pinstCEverQuest;
	AddCommand("/evac", SimSuccor);
	AddCommand("/exactloc", ExLoc);
	AddCommand("/fade", SimFade);
	AddCommand("/findpath", FindPath);
	AddCommand("/gate", SimGate);
	AddCommand("/succor", SimSuccor);
	AddCommand("/warp", wrap);
	AddCommand("/waypoint", waypoint);
	AddCommand("/zone", ChangeZones);
	AddCommand("/zwarp", zwrap);
}

// Called once, when the plugin is to shutdown
PLUGIN_API VOID ShutdownPlugin(VOID)
{
	DebugSpewAlways("Shutting down MQ2Trek");
	RemoveCommand("/evac");
	RemoveCommand("/exactloc");
	RemoveCommand("/fade");
	RemoveCommand("/findpath");
	RemoveCommand("/gate");
	RemoveCommand("/succor");
	RemoveCommand("/warp");
	RemoveCommand("/waypoint");
	RemoveCommand("/zone");
	RemoveCommand("/zwarp");
}