/*
From http://www.macroquest2.com/phpBB2/viewtopic.php?t=14171

Last edited by TheZ on Sun Feb 18, 2007 10:55 pm; edited 8 times in total
*/

//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//
// Project: MQ2Rez.cpp
// Author: TheZ, made from an amalgamation of dewey and s0rcier's code
//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//

#define    PLUGIN_NAME    "MQ2Rez"     // Plugin Name
#define    PLUGIN_FLAG      0xF9FF      // Plugin Auto-Pause Flags (see InStat)
#define    CURSOR_WAIT         250      // Cursor Wait After Manipulation
#define    LOOTME_WAIT        2400      // Lootme Wait Delay to complete operations
#define    LOOTME_MORE         400      // Lootme More Delay add when receiving new items.

//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//

#ifndef PLUGIN_API
  #include "../MQ2Plugin.h"
  PreSetup(PLUGIN_NAME);
  #include <map>
#endif PLUGIN_API

#define         NOID        -1
DWORD            Initialized   =false;            // Plugin Initialized?
DWORD            Conditions   =false;            // Window Conditions and Character State
DWORD            SkipExecuted=false;            // Skip Executed Timer
PCONTENTS     CursorContents();
long          InStat();
long          SetBOOL(long Cur, PCHAR Val, PCHAR Sec="", PCHAR Key="");
long          SetLONG(long Cur, PCHAR Val, PCHAR Sec="", PCHAR Key="", bool ZeroIsOff=false);
void          WinClick(CXWnd *Wnd, PCHAR ScreenID, PCHAR ClickNotification, DWORD KeyState=0);
bool          WinState(CXWnd *Wnd);
long       CursorRandom      = 0;        // Cursor Random Wait
DWORD      CursorTimer       = 0;        // Cursor Timer
long       LootMeHandle      = false;    // LootMe Handle?
long       LootMeHandle2      = false;
char       CorpseName[128];              // Corpse Name?
bool       CorpseDone        = false;    // Corpse Done Looting?
bool       CorpseMine        = false;    // Corpse Mine Flags?
long       CorpseLast        = 0;        // Corpse Last Slot Looted?
long       CorpseFind        = 0;        // Corpse Find Item Total?
DWORD      CorpseOpen        = 0;        // Corpse Open Time Counters?
DWORD      CorpseTime        = 0;        // Corpse Time Counters?
long       AutoRezAccept  = false;    // Take Rez box?
int        AutoRezPct     = 0;        // Take Rez %
long       AutoRezSpawn    = false;    // Let respawn window time out or port to bind.
DWORD      AutoRezTimer   = 0;       // How long after zone to take rez.
long       HaveIBeenRezzed = false;
long ClickWait=0;
long LootWait=0;
long RezDone = false;
long RezClicked = false;
long LootSilent = false;
char szCommand[MAX_STRING];
long RezCommandOn = false;
char szTemp[MAX_STRING];
//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//

bool WinState(CXWnd *Wnd) {
  return (Wnd && ((PCSIDLWND)Wnd)->Show);
}

void WinClick(CXWnd *Wnd, PCHAR ScreenID, PCHAR ClickNotification, DWORD KeyState) {
  if(Wnd) if(CXWnd *Child=Wnd->GetChildItem(ScreenID)) {
    BOOL KeyboardFlags[4];
    *(DWORD*)&KeyboardFlags=*(DWORD*)&((PCXWNDMGR)pWndMgr)->KeyboardFlags;
    *(DWORD*)&((PCXWNDMGR)pWndMgr)->KeyboardFlags=KeyState;
    SendWndClick2(Child,ClickNotification);
    *(DWORD*)&((PCXWNDMGR)pWndMgr)->KeyboardFlags=*(DWORD*)&KeyboardFlags;
  }
}

long SetLONG(long Cur,PCHAR Val, PCHAR Sec, PCHAR Key, bool ZeroIsOff,long Maxi) {
  char ToStr[16]; char Buffer[128]; long Result=atol(Val);
  if(Result && Result>Maxi) Result=Maxi; itoa(Result,ToStr,10);
  if(Sec[0] && Key[0]) WritePrivateProfileString(Sec,Key,ToStr,INIFileName);
  sprintf(Buffer,"%s::%s (\ag%s\ax)",Sec,Key,(ZeroIsOff && !Result)?"\aroff":ToStr);
  WriteChatColor(Buffer);
  return Result;
}

long SetBOOL(long Cur, PCHAR Val, PCHAR Sec, PCHAR Key) {
  char buffer[128]; long result=0;
  if(!strnicmp(Val,"false",5) || !strnicmp(Val,"off",3) || !strnicmp(Val,"0",1))    result=0;
  else if(!strnicmp(Val,"true",4) || !strnicmp(Val,"on",2) || !strnicmp(Val,"1",1)) result=1;
  else result=(!Cur)&1;
  if(Sec[0] && Key[0]) WritePrivateProfileString(Sec,Key,result?"1":"0",INIFileName);
  sprintf(buffer,"%s::%s (%s)",Sec,Key,result?"\agon\ax":"\agoff\ax");
  WriteChatColor(buffer);
  return result;
}

long InStat() {
  Conditions=0x00000000;
  if(WinState(FindMQ2Window("GuildTributeMasterWnd")))                Conditions|=0x0001;
  if(WinState(FindMQ2Window("TributeMasterWnd")))                     Conditions|=0x0002;
  if(WinState(FindMQ2Window("GuildBankWnd")))                         Conditions|=0x0004;
  if(WinState((CXWnd*)pTradeWnd))                                     Conditions|=0x0008;
  if(WinState((CXWnd*)pMerchantWnd))                                  Conditions|=0x0010;
  if(WinState((CXWnd*)pBankWnd))                                      Conditions|=0x0020;
  if(WinState((CXWnd*)pGiveWnd))                                      Conditions|=0x0040;
  if(WinState((CXWnd*)pSpellBookWnd))                                 Conditions|=0x0080;
  if(WinState((CXWnd*)pLootWnd))                                      Conditions|=0x0200;
  if(WinState((CXWnd*)pInventoryWnd))                                 Conditions|=0x0400;
  if(WinState((CXWnd*)pCastingWnd))                                   Conditions|=0x1000;
  if(GetCharInfo()->standstate==STANDSTATE_CASTING)                   Conditions|=0x2000;
  if(((((PSPAWNINFO)pCharSpawn)->CastingAnimation)&0xFF)!=0xFF) Conditions|=0x4000;
  if(GetCharInfo()->Stunned)                                          Conditions|=0x0100;
  if((Conditions&0x0600)!=0x0600 && (Conditions&0x0600))                Conditions|=0x0800;
  return Conditions;
}

PCONTENTS CursorContents() {
  return GetCharInfo2()->Cursor;
}


PCHAR LootSlot[]=
{
  "LW_LootSlot0",  "LW_LootSlot1",  "LW_LootSlot2",  "LW_LootSlot3",
  "LW_LootSlot4",  "LW_LootSlot5",  "LW_LootSlot6",  "LW_LootSlot7",
  "LW_LootSlot8",  "LW_LootSlot9",  "LW_LootSlot10", "LW_LootSlot11",
  "LW_LootSlot12", "LW_LootSlot13", "LW_LootSlot14", "LW_LootSlot15",
  "LW_LootSlot16", "LW_LootSlot17", "LW_LootSlot18", "LW_LootSlot19",
  "LW_LootSlot20", "LW_LootSlot21", "LW_LootSlot22", "LW_LootSlot23",
  "LW_LootSlot24", "LW_LootSlot25", "LW_LootSlot26", "LW_LootSlot27",
  "LW_LootSlot28", "LW_LootSlot29", "LW_LootSlot30", "LW_LootSlot31"
};

//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//

bool ShouldILoot(PCONTENTS Item) {
   if(CorpseMine) return true;
  return false;
}

bool GotCorpseName(PSTR Buffer) {
  Buffer[0]=0;
  if(pLootWnd) if(CXWnd *Child=(CXWnd*)pLootWnd->GetChildItem("LW_CorpseName")) {
    char InputCXStr[128];
    ZeroMemory(InputCXStr,sizeof(InputCXStr));
    GetCXStr(Child->WindowText,InputCXStr,sizeof(InputCXStr));
    if(PCHAR CorpsePTR=strstr(InputCXStr,"'s corpse")) CorpsePTR[0]=0;
    if(!pActiveCorpse && InputCXStr[0]) SetCXStr(&Child->WindowText,Buffer);
    else strcpy(Buffer,InputCXStr);
  }
  return (Buffer[0]!=0);
}

BOOL IsWindowOpen(PCHAR WindowName)
{
   PCSIDLWND pWnd=(PCSIDLWND)FindMQ2Window(WindowName);
   if (!pWnd) return false;
   return (BOOL)pWnd->Show;
}




int ExpRezBox(void)
{

   CXWnd *Child;
   CXWnd *pWnd;
   char InputCXStr[128],*p;
   int v;

   pWnd=(CXWnd *)FindMQ2Window("ConfirmationDialogBox");
   if(pWnd)
   {
      if (((PCSIDLWND)(pWnd))->Show==0) return -1;
      Child=pWnd->GetChildItem("cd_textoutput");
      if(Child)
      {
         ZeroMemory(InputCXStr,sizeof(InputCXStr));
         GetCXStr(Child->SidlText,InputCXStr,sizeof(InputCXStr));
         p = strstr(InputCXStr,"(");
         if (!p) return -1;
         v = atoi(p+1);
         p = strstr(p,"percent");
         if (!p) return -1;

         return v;
      }
   }
   return -1;
}



void AutoRezCommand(PSPAWNINFO pCHAR, PCHAR zLine) {
   bool ShowInfo=false;
   bool NeedHelp=false;
  char Parm1[MAX_STRING];
  char Parm2[MAX_STRING];
  GetArg(Parm1,zLine,1);
  GetArg(Parm2,zLine,2);

  if(!stricmp("help",Parm1)) NeedHelp=true;
//Accept rez?
  else if(!stricmp("accept",Parm1) && (!stricmp("on",Parm2)))
     AutoRezAccept=SetBOOL(AutoRezAccept ,Parm2 ,"MQ2Rez","Accept");
  else if(!stricmp("accept",Parm1) && (!stricmp("off",Parm2)))
     AutoRezAccept=SetBOOL(AutoRezAccept ,Parm2,"MQ2Rez","Accept");
//What percent?
  else if(!stricmp("pct",Parm1)) {
     WritePrivateProfileString("MQ2Rez","RezPct",Parm2,INIFileName);
     AutoRezPct=atoi(Parm2);
  }
//Should I spawn first?
  else if(!stricmp("spawn",Parm1) && (!stricmp("on",Parm2)))
     AutoRezSpawn=SetBOOL(AutoRezSpawn ,Parm2 ,"MQ2Rez","Spawn");
  else if(!stricmp("spawn",Parm1) && (!stricmp("off",Parm2)))
     AutoRezSpawn=SetBOOL(AutoRezSpawn ,Parm2,"MQ2Rez","Spawn");
//Should I loot my corpse?
  else if(!stricmp("loot",Parm1) && (!stricmp("on",Parm2)))
       LootMeHandle=SetBOOL(LootMeHandle ,Parm2 ,"MQ2Rez","Active");
  else if(!stricmp("loot",Parm1) && !stricmp("off",Parm2))
       LootMeHandle=SetBOOL(LootMeHandle ,Parm2,"MQ2Rez","Active");
//Toggle silent looting
  else if(!stricmp("silent",Parm1) && !stricmp("on",Parm2))
        LootSilent=SetBOOL(LootSilent ,Parm2,"MQ2Rez","Silent");
  else if(!stricmp("silent",Parm1) && !stricmp("off",Parm2))
        LootSilent=SetBOOL(LootSilent ,Parm2,"MQ2Rez","Silent");
//Do I want a command executed after being rezed?
  else if(!stricmp("command",Parm1) && !stricmp("on",Parm2))
        LootSilent=SetBOOL(LootSilent ,Parm2,"MQ2Rez","RezCommandOn");
  else if(!stricmp("command",Parm1) && !stricmp("off",Parm2))
        LootSilent=SetBOOL(RezCommandOn ,Parm2,"MQ2Rez","RezCommandOn");
//What command should I execute?
 else if(!stricmp("setcommand",Parm1)) {
     WritePrivateProfileString("MQ2Rez","Command Line",Parm2,INIFileName);
     WriteChatf("Command set to: \ag%s\ax",Parm2);
  }
//Help??
  else if(!stricmp("",Parm1))
  {
     ShowInfo=TRUE;
     NeedHelp=TRUE;
  }
  if(NeedHelp) {
   WriteChatColor("Usage:");
   WriteChatColor("/rez -> displays settings");
   WriteChatColor("/rez accept on|off -> Toggle auto-accepting rezbox");
   WriteChatColor("/rez spawn  on|off -> Toggles going to bind point after death");
   WriteChatColor("/rez pct # -> Autoaccepts rezes only if they are higher than # percent");
   WriteChatColor("/rez loot on|off -> Toggle looting corpse when opened and when rezzed");
   WriteChatColor("/rez silent -> Toggle messages for looting individual items");
   WriteChatColor("/rez command on|off -> Toggle use of a command after looting out corpse");
   WriteChatColor("/rez setcommand mycommand -> Set the command that you want.");
   WriteChatColor("/rez help");
  }
  if (ShowInfo)
  {
     WriteChatf("MQ2Rez Accept(\ag%s\ax).",(AutoRezAccept?"on":"off"));
     WriteChatf("MQ2Rez Spawn(\ag%s\ax).",(AutoRezSpawn?"on":"off"));
     WriteChatf("MQ2Rez Loot(\ag%s\ax).",(LootMeHandle?"on":"off"));
     WriteChatf("MQ2Rez AcceptPct(\ag%d\ax).",AutoRezPct);
     WriteChatf("MQ2Rez Silent(\ag%s\ax).",(LootSilent?"on":"off"));
     WriteChatf("MQ2Rez Command(\ag%s\ax).",(RezCommandOn?"on":"off"));
     strcpy(szTemp,szCommand);
     WriteChatf("Command line set to: \ag%s\ax",szTemp);
  }
}

//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//

PLUGIN_API VOID SetGameState(DWORD GameState) {
  if(GameState==GAMESTATE_INGAME) {
     if(!Initialized) {
      Initialized=true;
      sprintf(INIFileName,"%s\\%s_%s.ini",gszINIPath,EQADDR_SERVERNAME,GetCharInfo()->Name);
      LootMeHandle =GetPrivateProfileInt("MQ2Rez","Active" ,0,INIFileName);
      AutoRezAccept=GetPrivateProfileInt("MQ2Rez","Accept" ,0,INIFileName);
      AutoRezSpawn =GetPrivateProfileInt("MQ2Rez","Spawn"  ,0,INIFileName);
      AutoRezPct   =GetPrivateProfileInt("MQ2Rez","RezPct" ,0,INIFileName);
      LootSilent   =GetPrivateProfileInt("MQ2Rez","Silent" ,0,INIFileName);
      RezCommandOn =GetPrivateProfileInt("MQ2Rez","RezCommandOn" ,0,INIFileName);
      GetPrivateProfileString("MQ2Rez","Command Line","DISABLED",szTemp,MAX_STRING,INIFileName);
      if(!strcmp(szTemp,"DISABLED")) {
        RezCommandOn = false;
     } else {
        strcpy(szCommand,szTemp);
     }
    }
  } else if(GameState!=GAMESTATE_LOGGINGIN) {
     if(Initialized) {
        LootMeHandle=0;
        Initialized=0;
    }
  }
}

VOID Rezzy(PSPAWNINFO pChar, PCHAR szLine) {
   DoCommand(GetCharInfo()->pSpawn,"/squelch /notify RespawnWnd RW_OptionsList listselect 2");
   DoCommand(GetCharInfo()->pSpawn,"/squelch /notify RespawnWnd RW_SelectButton leftmouseup");
   RezClicked = true;
}

PLUGIN_API VOID InitializePlugin() {
  AddCommand("/rezzme",Rezzy);
  AddCommand("/rez",AutoRezCommand);
}

PLUGIN_API VOID ShutdownPlugin() {
  RemoveCommand("/rez");
  RemoveCommand("/rezzme");
}

typedef struct _FIXLOOTWINDOW {
/*0x000*/ struct _CSIDLWND Wnd;
/*0x160*/ BYTE      Unknown0x160[0x08];
/*0x168*/ DWORD     ItemSlot[0x22];
/*0x1f0*/ PCONTENTS ItemDesc[NUM_INV_SLOTS];
} FIXLOOTWINDOW, *PFIXLOOTWINDOW;

//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=//

PLUGIN_API VOID OnPulse() {
   if (!AutoRezAccept && LootMeHandle) LootMeHandle2= true;

if (RezClicked && ClickWait>35)
         {
           DoCommand(GetCharInfo()->pSpawn,"/squelch /notify RespawnWnd RW_OptionsList listselect 2");
         if(ClickWait>75) return;
            DoCommand(GetCharInfo()->pSpawn,"/squelch /notify RespawnWnd RW_SelectButton leftmouseup");
            ClickWait=0;
            RezClicked=false;
            RezDone = true;
            return;
     }
else if(RezClicked) {
      ClickWait++;
     return;
}

   if(RezDone && LootWait>35) {

               DoCommand(GetCharInfo()->pSpawn,"/target mycorpse");
         if(LootWait>65) return;
               DoCommand(GetCharInfo()->pSpawn,"/loot");
          if(LootWait>75) return;

           LootWait=0;
           RezDone=false;
            if (LootMeHandle) LootMeHandle2 = true;
            return;
}
   else if (RezDone) {
      LootWait++;

      return;
   }
      static int RespawnWndCnt = 0;
      static int RezBoxCnt = 0;

      if (IsWindowOpen("RespawnWnd")) {
         RespawnWndCnt++;
      }
      else
         RespawnWndCnt=0;

   if (AutoRezAccept && (ExpRezBox()>=AutoRezPct) ) {
         RezBoxCnt++;
   }
      else
         RezBoxCnt=0;
      if (AutoRezSpawn && RespawnWndCnt)
      {
         WinClick(FindMQ2Window("RespawnWnd"),"RW_SelectButton","leftmouseup",1);
         return;
      }
      if (AutoRezAccept && RezBoxCnt > 0)
      {
         WriteChatColor("Accepting Rez now");
         DoCommand(GetCharInfo()->pSpawn,"/notify ConfirmationDialogBox Yes_Button leftmouseup");
         RezClicked = true;
      }
       if(Initialized && gbInZone && pCharSpawn && GetCharInfo2() && !(PLUGIN_FLAG&InStat())) {
         DWORD PulseTimer=(DWORD)clock();
   if(LootMeHandle2 && ((PSPAWNINFO)pCharSpawn)->CastingSpellID==NOID) {
        if(GotCorpseName(CorpseName)) {
           if(!CorpseOpen) {
          CorpseMine=(0==stricmp(CorpseName,GetCharInfo()->Name)); if(!CorpseMine) return;
          WriteChatf("MQ2Rez::\ayLOOTING\ax <\ag%s\ax>.",CorpseName);
           CorpseTime=PulseTimer+LOOTME_WAIT;
           CorpseOpen=PulseTimer+500;
           CorpseDone=false;
          CorpseLast=-1;
          CorpseFind=0;
         }
        for(int slot=CorpseFind; slot<=NUM_INV_SLOTS; slot++)
           if(((PFIXLOOTWINDOW)pLootWnd)->ItemSlot[slot]!=0xFFFFFFFF) {
              CorpseTime+=LOOTME_MORE;
              CorpseFind++;
           }
          if(PulseTimer>CorpseTime) {
            if(!CorpseDone) {
            WriteChatf("MQ2Rez::\ayCLOSING\ax <\ag%s\ax>.",CorpseName);
            WinClick((CXWnd*)pLootWnd,"DoneButton","leftmouseup",0);
              CorpseDone=true;
              LootMeHandle2=false;
            }
          }
          if(!CorpseDone && PulseTimer>CorpseOpen)
             for(int slot=(CorpseLast<0)?0:CorpseLast; slot<CorpseFind; slot++) {
                if(PCONTENTS Corpse=((PFIXLOOTWINDOW)pLootWnd)->ItemDesc[slot]) if(ShouldILoot(Corpse)) {
                  if(slot!=CorpseLast) {
               if(!LootSilent) {
               WriteChatf("MQ2Rez::\ayLOOTING\ax <\ag%s\ax>.",Corpse->Item->Name);
            }
                     WinClick((CXWnd*)pLootWnd,LootSlot[slot],"rightmouseup",0);
                     CorpseTime=PulseTimer+LOOTME_WAIT;
                     CorpseLast=slot;
              }
                 return;
               }
             }
        return;
      }
      CorpseOpen=false;
      if(RezCommandOn) {
        strcpy(szTemp,szCommand);
        HideDoCommand(GetCharInfo()->pSpawn, szTemp, FromPlugin);
     }
    }
  }
} 