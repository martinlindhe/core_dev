/******************************************************************************
    MQ2Main.dll: MacroQuest2's extension DLL for EverQuest 
    Copyright (C) 2002-2003 Plazmic, 2003-2005 Lax 

    This program is free software; you can redistribute it and/or modify 
    it under the terms of the GNU General Public License, version 2, as published by 
    the Free Software Foundation. 

    This program is distributed in the hope that it will be useful, 
    but WITHOUT ANY WARRANTY; without even the implied warranty of 
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
    GNU General Public License for more details. 
******************************************************************************/ 
#ifdef PRIVATE
#include "eqgame-private.h" 
#endif

#define __ClientName                                              "eqgame" 
#define __ExpectedVersionDate                                     "Jul 10 2007"
#define __ExpectedVersionTime                                     "19:38:34"
#define __ActualVersionDate                                        0x6ECF24
#define __ActualVersionTime                                        0x6ECF30

#define __ClientOverride                                           0 
#define __MacroQuestWinClassName                                  "__MacroQuestTray" 
#define __MacroQuestWinName                                       "MacroQuest" 

// Memory Protection 
#define __MemChecker0                                              0x4EE950
#define __MemChecker1                                              0x63D980
#define __MemChecker2                                              0x51F560
#define __MemChecker3                                              0x51F610
#define __MemChecker4                                              0x607320
#define __EncryptPad0                                              0x746A20
#define __EncryptPad1                                              0x750258
#define __EncryptPad2                                              0x748F80
#define __EncryptPad3                                              0x749380
#define __EncryptPad4                                              0x74F970

// Direct Input
#define DI8__Main                                                  0x97E204
#define DI8__Keyboard                                              0x97E208
#define DI8__Mouse                                                 0x97E20C
#define __AltTimerReady                                            0x91A342
#define __Attack                                                   0x9779BA
#define __Autofire                                                 0x9779BB
#define __BindList                                                 0x741C50
#define __Clicks                                                   0x91946C
#define __CommandList                                              0x742450
#define __CurrentMapLabel                                          0x9892D4
#define __CurrentSocial                                            0x73E4DC
#define __DoAbilityAvailable                                       0x91A2DC
#define __DoAbilityList                                            0x94F500
#define __DrawHandler                                              0x99812C
#define __GroupCount                                               0x8F9B90
#define __GroupLeader                                              0x8F9CEC
#define __Guilds                                                   0x8FB6B0
#define __gWorld                                                   0x8FB5F0
#define __HotkeyPage                                               0x9768EC
#define __HWnd                                                     0x97E1AC
#define __InChatMode                                               0x9193A8
#define __LastTell                                                 0x91AC08
#define __LMouseHeldTime                                           0x91948C
#define __Mouse                                                    0x97E210
#define __MouseLook                                                0x91943A
#define __NetStatusToggle                                          0x91943C
#define __PCNames                                                  0x91A660
#define __RangeAttackReady                                         0x91A340
#define __RMouseHeldTime                                           0x919488
#define __RunWalkState                                             0x9193AC
#define __ScreenMode                                               0x849660
#define __ScreenX                                                  0x919370
#define __ScreenY                                                  0x919374
#define __ScreenXMax                                               0x919378
#define __ScreenYMax                                               0x91937C
#define __ServerHost                                               0x8F9AE4
#define __ServerName                                               0x94F4C0
#define __ShowNames                                                0x91A534
#define __Socials                                                  0x94F5C0


//// 
//Section 1: Vital Offsets 
//// 
#define instCRaid                                                  0x913300
#define instEQZoneInfo                                             0x9195D8
#define instKeypressHandler                                        0x977ABC
#define pinstActiveBanker                                          0x8FB688
#define pinstActiveCorpse                                          0x8FB68C
#define pinstActiveGMaster                                         0x8FB690
#define pinstActiveMerchant                                        0x8FB684
#define pinstAltAdvManager                                         0x84A348
#define pinstAuraMgr                                               0x75538C
#define pinstBandageTarget                                         0x8FB670
#define pinstCamActor                                              0x849E3C
#define pinstCDBStr                                                0x8494E0
#define pinstCDisplay                                              0x8FB698
#define pinstCEverQuest                                            0x97E384
#define pinstCharData                                              0x8FB654
#define pinstCharSpawn                                             0x8FB67C
#define pinstControlledMissile                                     0x8FB650
#define pinstControlledPlayer                                      0x8FB67C
#define pinstCSidlManager                                          0x997928
#define pinstCXWndManager                                          0x997920
#define instDynamicZone                                            0x918DC0
#define pinstDZMember                                              0x918ED0
#define pinstDZTimerInfo                                           0x918ED4
#define pinstEQItemList                                            0x8FB638
#define instEQMisc                                                 0x849498
#define pinstEQSoundManager                                        0x84A36C
#define instExpeditionLeader                                       0x918E0A
#define instExpeditionName                                         0x918E4A
#define instGroup                                                  0x8F9B90
#define pinstGroup                                                 0x8F9B8C
#define pinstImeManager                                            0x99792C
#define pinstLocalPlayer                                           0x8FB668
#define pinstModelPlayer                                           0x8FB694
#define pinstPCData                                                0x8FB654
#define pinstSelectedItem                                          0x98912C
#define pinstSkillMgr                                              0x97B1B8
#define pinstSpawnManager                                          0x97B134
#define pinstSpellManager                                          0x97B1BC
#define pinstSpellSets                                             0x9768F0
#define pinstStringTable                                           0x8FB60C
#define pinstSwitchManager                                         0x8F97E0
#define pinstTarget                                                0x8FB680
#define pinstTargetObject                                          0x8FB658
#define pinstTargetSwitch                                          0x8FB65C
#define pinstTaskMember                                            0x849478
#define pinstTrackTarget                                           0x8FB674
#define pinstTradeTarget                                           0x8FB664
#define instTributeActive                                          0x8494BD
#define pinstViewActor                                             0x849E38
#define pinstWorldData                                             0x8FB634


//// 
//Section 2:  UI Related Offsets 
//// 
#define pinstCTextOverlay                                          0x7538C0
#define pinstCAudioTriggersWindow                                  0x75388C
#define pinstCCharacterSelect                                      0x849D48
#define pinstCFacePick                                             0x849D00
#define pinstCNoteWnd                                              0x849D04
#define pinstCBookWnd                                              0x849D08
#define pinstCPetInfoWnd                                           0x849D0C
#define pinstCTrainWnd                                             0x849D10
#define pinstCSkillsWnd                                            0x849D14
#define pinstCSkillsSelectWnd                                      0x849D18
#define pinstCCombatSkillSelectWnd                                 0x849D1C
#define pinstCFriendsWnd                                           0x849D20
#define pinstCAuraWnd                                              0x849D24
#define pinstCRespawnWnd                                           0x849D28
#define pinstCBandolierWnd                                         0x849D2C
#define pinstCPotionBeltWnd                                        0x849D30
#define pinstCAAWnd                                                0x849D34
#define pinstCGroupSearchFiltersWnd                                0x849D38
#define pinstCLoadskinWnd                                          0x849D3C
#define pinstCAlarmWnd                                             0x849D40
#define pinstCMusicPlayerWnd                                       0x849D44
#define pinstCMailWnd                                              0x849D4C
#define pinstCMailCompositionWnd                                   0x849D50
#define pinstCMailAddressBookWnd                                   0x849D54
#define pinstCRaidWnd                                              0x849D5C
#define pinstCRaidOptionsWnd                                       0x849D60
#define pinstCBreathWnd                                            0x849D64
#define pinstCMapViewWnd                                           0x849D68
#define pinstCMapToolbarWnd                                        0x849D6C
#define pinstCEditLabelWnd                                         0x849D70
#define pinstCTargetWnd                                            0x849D74
#define pinstCColorPickerWnd                                       0x849D78
#define pinstCPlayerWnd                                            0x849D7C
#define pinstCOptionsWnd                                           0x849D80
#define pinstCBuffWindowNORMAL                                     0x849D84
#define pinstCBuffWindowSHORT                                      0x849D88
#define pinstCharacterCreation                                     0x849D8C
#define pinstCCursorAttachment                                     0x849D90
#define pinstCCastingWnd                                           0x849D94
#define pinstCCastSpellWnd                                         0x849D98
#define pinstCSpellBookWnd                                         0x849D9C
#define pinstCInventoryWnd                                         0x849DA0
#define pinstCBankWnd                                              0x849DA4
#define pinstCQuantityWnd                                          0x849DA8
#define pinstCLootWnd                                              0x849DAC
#define pinstCActionsWnd                                           0x849DB0
#define pinstCCombatAbilityWnd                                     0x849DB4
#define pinstCMerchantWnd                                          0x849DB8
#define pinstCTradeWnd                                             0x849DBC
#define pinstCSelectorWnd                                          0x849DC0
#define pinstCBazaarWnd                                            0x849DC4
#define pinstCBazaarSearchWnd                                      0x849DC8
#define pinstCGiveWnd                                              0x849DCC
#define pinstCTrackingWnd                                          0x849DD0
#define pinstCInspectWnd                                           0x849DD4
#define pinstCSocialEditWnd                                        0x849DD8
#define pinstCFeedbackWnd                                          0x849DDC
#define pinstCBugReportWnd                                         0x849DE0
#define pinstCVideoModesWnd                                        0x849DE4
#define pinstCTextEntryWnd                                         0x849DEC
#define pinstCFileSelectionWnd                                     0x849DF0
#define pinstCCompassWnd                                           0x849DF4
#define pinstCPlayerNotesWnd                                       0x849DF8
#define pinstCGemsGameWnd                                          0x849DFC
#define pinstCTimeLeftWnd                                          0x849E00
#define pinstCPetitionQWnd                                         0x849E04
#define pinstCSoulmarkWnd                                          0x849E08
#define pinstCStoryWnd                                             0x849E0C
#define pinstCJournalTextWnd                                       0x849E10
#define pinstCJournalCatWnd                                        0x849E14
#define pinstCBodyTintWnd                                          0x849E18
#define pinstCServerListWnd                                        0x849E1C
#define pinstCAvaZoneWnd                                           0x849E20
#define pinstCBlockedBuffWnd                                       0x849E24
#define pinstCBlockedPetBuffWnd                                    0x849E28
#define pinstCInvSlotMgr                                           0x849E2C
#define pinstCContainerMgr                                         0x849E30
#define pinstCAdventureLeaderboardWnd                              0x986964
#define pinstCAdventureRequestWnd                                  0x986980
#define pinstCAltStorageWnd                                        0x9869E0
#define pinstCAdventureStatsWnd                                    0x98699C
#define pinstCBarterMerchantWnd                                    0x986B94
#define pinstCBarterSearchWnd                                      0x986BB0
#define pinstCBarterWnd                                            0x986BCC
#define pinstCChatManager                                          0x986D94
#define pinstCDynamicZoneWnd                                       0x986E70
#define pinstCEQMainWnd                                            0x986EE0
#define pinstCFellowshipWnd                                        0x986F44
#define pinstCFindLocationWnd                                      0x986F78
#define pinstCGroupSearchWnd                                       0x987028
#define pinstCGroupWnd                                             0x987044
#define pinstCGuildBankWnd                                         0x987060
#define pinstCGuildMgmtWnd                                         0x989080
#define pinstCGuildTributeMasterWnd                                0x9890A0
#define pinstCHotButtonWnd                                         0x9890BC
#define pinstCHotButtonWnd1                                        0x9890BC
#define pinstCHotButtonWnd2                                        0x9890C0
#define pinstCHotButtonWnd3                                        0x9890C4
#define pinstCHotButtonWnd4                                        0x9890C8
#define pinstCItemDisplayManager                                   0x989160
#define pinstCItemExpTransferWnd                                   0x989180
#define pinstCLeadershipWnd                                        0x9891FC
#define pinstCLFGuildWnd                                           0x989218
#define pinstCMIZoneSelectWnd                                      0x98933C
#define pinstCAdventureMerchantWnd                                 0x989434
#define pinstCConfirmationDialog                                   0x989450
#define pinstCPopupWndManager                                      0x989450
#define pinstCProgressionSelectionWnd                              0x989484
#define pinstCPvPLeaderboardWnd                                    0x9894A0
#define pinstCPvPStatsWnd                                          0x9894BC
#define pinstCSystemInfoDialogBox                                  0x989E24
#define pinstCTargetOfTargetWnd                                    0x989E40
#define pinstCTaskSelectWnd                                        0x989E74
#define pinstCTaskTemplateSelectWnd                                0x989E90
#define pinstCTaskWnd                                              0x989EAC
#define pinstCTipWndOFDAY                                          0x989EF8
#define pinstCTipWndCONTEXT                                        0x989EFC
#define pinstCTitleWnd                                             0x989F18
#define pinstCTradeskillWnd                                        0x989F5C
#define pinstCTributeBenefitWnd                                    0x989FA8
#define pinstCTributeMasterWnd                                     0x989FC4
#define pinstCContextMenuManager                                   0x997984
#define pinstCVoiceMacroWnd                                        0x97B26C
#define pinstCHtmlWnd                                              0x97B288


//// 
// Section 3: Miscellaneous Offsets 
//// 
#define __CastRay                                                  0x4B00A0
#define __ConvertItemTags                                          0x4A4A30
#define __ExecuteCmd                                               0x4960C0
#define __get_melee_range                                          0x49B120
#define __GetGaugeValueFromEQ                                      0x5DD920
#define __GetLabelFromEQ                                           0x5DE130
#define __NewUIINI                                                 0x5DD510
#define __ProcessGameEvents                                        0x4DB6F0
#define __SendMessage                                              0x4BD780
#define CrashDetected                                              0x51E660
#define DrawNetStatus                                              0x4F1680
#define Util__FastTime                                             0x63DFD0


//// 
// Section 4: Function Offsets 
//// 
// AltAdvManager 
#define AltAdvManager__GetCalculatedTimer                          0x470A60
#define AltAdvManager__IsAbilityReady                              0x470AA0
#define AltAdvManager__GetAltAbility                               0x470BF0

// CBankWnd 
#define CBankWnd__GetNumBankSlots                                  0x534A60

// CBazaarSearchWnd 
#define CBazaarSearchWnd__HandleBazaarMsg                          0x53DFF0

// CButtonWnd 
#define CButtonWnd__SetCheck                                       0x6688C0

// CChatManager 
#define CChatManager__GetRGBAFromIndex                             0x550D10
#define CChatManager__InitContextMenu                              0x5514A0

// CChatService
#define CChatService__GetNumberOfFriends                           0x62E5D0
#define CChatService__GetFriendName                                0x62E5E0

// CChatWindow 
#define CChatWindow__CChatWindow                                   0x555620
#define CChatWindow__WndNotification                               0x556010

// CComboWnd 
#define CComboWnd__DeleteAll                                       0x64F7B0
#define CComboWnd__Draw                                            0x64F960
#define CComboWnd__GetCurChoice                                    0x64F750
#define CComboWnd__GetListRect                                     0x64FC40
#define CComboWnd__GetTextRect                                     0x64F7E0
#define CComboWnd__InsertChoice                                    0x64FCB0
#define CComboWnd__SetColors                                       0x64F6E0
#define CComboWnd__SetChoice                                       0x64F710

// CContainerWnd 
#define CContainerWnd__HandleCombine                               0x55BC90
#define CContainerWnd__vftable                                     0x6F3258

// CDisplay 
#define CDisplay__CleanGameUI                                      0x45FDA0
#define CDisplay__GetClickedActor                                  0x45D640
#define CDisplay__GetUserDefinedColor                              0x45CAA0
#define CDisplay__GetWorldFilePath                                 0x45C010
#define CDisplay__is3dON                                           0x45ABC0
#define CDisplay__ReloadUI                                         0x46BD60
#define CDisplay__WriteTextHD2                                     0x461560

// CEditBaseWnd 
#define CEditBaseWnd__SetMaxChars                                  0x52ED80
#define CEditBaseWnd__SetSel                                       0x6737D0

// CEditWnd 
#define CEditWnd__DrawCaret                                        0x65C9E0
#define CEditWnd__GetCharIndexPt                                   0x65D840
#define CEditWnd__GetDisplayString                                 0x65CB70
#define CEditWnd__GetHorzOffset                                    0x65CE10
#define CEditWnd__GetLineForPrintableChar                          0x65D2F0
#define CEditWnd__GetSelStartPt                                    0x65DA80
#define CEditWnd__GetSTMLSafeText                                  0x65CFB0
#define CEditWnd__PointFromPrintableChar                           0x65D400
#define CEditWnd__SelectableCharFromPoint                          0x65D580
#define CEditWnd__SetEditable                                      0x65CF80

// CEverQuest 
#define CEverQuest__ClickedPlayer                                  0x4C1910
#define CEverQuest__DropHeldItemOnGround                           0x4C64F0
#define CEverQuest__dsp_chat                                       0x4C7180
#define CEverQuest__DoTellWindow                                   0x4C6620
#define CEverQuest__EnterZone                                      0x4DA1E0
#define CEverQuest__GetBodyTypeDesc                                0x4BEFB0
#define CEverQuest__GetClassDesc                                   0x4BE710
#define CEverQuest__GetClassThreeLetterCode                        0x4BED10
#define CEverQuest__GetDeityDesc                                   0x4BF620
#define CEverQuest__GetLangDesc                                    0x4BF340
#define CEverQuest__GetRaceDesc                                    0x4BF5F0
#define CEverQuest__InterpretCmd                                   0x4C7B00
#define CEverQuest__LeftClickedOnPlayer                            0x4D8D90
#define CEverQuest__LMouseUp                                       0x4DAF50
#define CEverQuest__RightClickedOnPlayer                           0x4D9160
#define CEverQuest__RMouseUp                                       0x4DA800
#define CEverQuest__SetGameState                                   0x4C19E0

// CGaugeWnd 
#define CGaugeWnd__CalcFillRect                                    0x566D30
#define CGaugeWnd__CalcLinesFillRect                               0x566DA0
#define CGaugeWnd__Draw                                            0x567130

// CGuild
#define CGuild__FindMemberByName                                   0x4173A0

// CHotButtonWnd 
#define CHotButtonWnd__DoHotButton                                 0x57CBA0

// CInvSlotMgr 
#define CInvSlotMgr__FindInvSlot                                   0x5847C0
#define CInvSlotMgr__MoveItem                                      0x584960

// CInvSlotWnd
#define CInvSlotWnd__DrawTooltip                                   0x585860

// CInvSLot
#define CInvSlot__SliderComplete                                   0x583740

// CItemDisplayWnd 
#define CItemDisplayWnd__SetItem                                   0x593510
#define CItemDisplayWnd__SetSpell                                  0x5DB8B0

// CLabel 
#define CLabel__Draw                                               0x598B70

// CListWnd 
#define CListWnd__AddColumn                                        0x64F4F0
#define CListWnd__AddColumn1                                       0x64EFE0
#define CListWnd__AddLine                                          0x64EBC0
#define CListWnd__AddString                                        0x64ED90
#define CListWnd__CalculateFirstVisibleLine                        0x64BEF0
#define CListWnd__CalculateVSBRange                                0x64DB60
#define CListWnd__ClearAllSel                                      0x64B660
#define CListWnd__CloseAndUpdateEditWindow                         0x64C500
#define CListWnd__Compare                                          0x64C8B0
#define CListWnd__Draw                                             0x64D860
#define CListWnd__DrawColumnSeparators                             0x64D6D0
#define CListWnd__DrawHeader                                       0x64B830
#define CListWnd__DrawItem                                         0x64CFC0
#define CListWnd__DrawLine                                         0x64D370
#define CListWnd__DrawSeparator                                    0x64D770
#define CListWnd__EnsureVisible                                    0x64BF40
#define CListWnd__ExtendSel                                        0x64CEF0
#define CListWnd__GetColumnMinWidth                                0x64B400
#define CListWnd__GetColumnWidth                                   0x64B340
#define CListWnd__GetCurSel                                        0x64AE00
#define CListWnd__GetHeaderRect                                    0x64AF10
#define CListWnd__GetItemAtPoint                                   0x64C210
#define CListWnd__GetItemAtPoint1                                  0x64C280
#define CListWnd__GetItemData                                      0x64B0F0
#define CListWnd__GetItemHeight                                    0x64BC90
#define CListWnd__GetItemIcon                                      0x64B280
#define CListWnd__GetItemRect                                      0x64C000
#define CListWnd__GetItemText                                      0x64B130
#define CListWnd__GetSelList                                       0x64EEC0
#define CListWnd__GetSeparatorRect                                 0x64C7F0
#define CListWnd__RemoveLine                                       0x64F4A0
#define CListWnd__SetColors                                        0x64AE80
#define CListWnd__SetColumnJustification                           0x64B460
#define CListWnd__SetColumnWidth                                   0x64B3C0
#define CListWnd__SetCurSel                                        0x64AE40
#define CListWnd__SetItemColor                                     0x64E790
#define CListWnd__SetItemData                                      0x64B700
#define CListWnd__SetItemText                                      0x64E710
#define CListWnd__ShiftColumnSeparator                             0x64CE60
#define CListWnd__Sort                                             0x64F520
#define CListWnd__ToggleSel                                        0x64B5D0

// CMapViewWnd 
#define CMapViewWnd__CMapViewWnd                                   0x5ABA30

// CMerchantWnd 
#define CMerchantWnd__DisplayBuyOrSellPrice                        0x5AD420
#define CMerchantWnd__RequestBuyItem                               0x5AE6B0
#define CMerchantWnd__RequestSellItem                              0x5AD680
#define CMerchantWnd__SelectBuySellSlot                            0x5AE3F0

// CObfuscator
#define CObfuscator__doit                                          0x62FC40

// CSidlManager 
#define CSidlManager__FindScreenPieceTemplate1                     0x6643C0

// CSidlScreenWnd 
#define CSidlScreenWnd__CalculateHSBRange                          0x654EC0
#define CSidlScreenWnd__CalculateVSBRange                          0x654E10
#define CSidlScreenWnd__ConvertToRes                               0x655621
#define CSidlScreenWnd__CreateChildrenFromSidl                     0x655E20
#define CSidlScreenWnd__CSidlScreenWnd1                            0x6570E0
#define CSidlScreenWnd__CSidlScreenWnd2                            0x657190
#define CSidlScreenWnd__dCSidlScreenWnd                            0x656990
#define CSidlScreenWnd__DrawSidlPiece                              0x655B00
#define CSidlScreenWnd__EnableIniStorage                           0x6555C0
#define CSidlScreenWnd__GetSidlPiece                               0x655D00
#define CSidlScreenWnd__Init1                                      0x656EF0
#define CSidlScreenWnd__LoadIniInfo                                0x655ED0
#define CSidlScreenWnd__LoadIniListWnd                             0x655770
#define CSidlScreenWnd__LoadSidlScreen                             0x655930
#define CSidlScreenWnd__StoreIniInfo                               0x655100
#define CSidlScreenWnd__WndNotification                            0x655A60

// CSkillMgr
#define CSkillMgr__GetSkillCap                                     0x512EC0

// CSliderWnd 
#define CSliderWnd__GetValue                                       0x675660
#define CSliderWnd__SetValue                                       0x675760
#define CSliderWnd__SetNumTicks                                    0x675CA0

// CSpellBookWnd 
#define CSpellBookWnd__MemorizeSet                                 0x5DA600

// CStmlWnd
#define CStmlWnd__AppendSTML                                       0x671AC0
#define CStmlWnd__CalculateVSBRange                                0x669E10
#define CStmlWnd__CanBreakAtCharacter                              0x669F50
#define CStmlWnd__FastForwardToEndOfTag                            0x66AA80
#define CStmlWnd__GetNextTagPiece                                  0x66A9A0
#define CStmlWnd__GetSTMLText                                      0x5555C0
#define CStmlWnd__GetThisChar                                      0x691A00
#define CStmlWnd__GetVisiableText                                  0x66BCC0
#define CStmlWnd__InitializeWindowVariables                        0x66DE20
#define CStmlWnd__MakeStmlColorTag                                 0x669510
#define CStmlWnd__MakeWndNotificationTag                           0x6695B0
#define CStmlWnd__StripFirstSTMLLines                              0x671840
#define CStmlWnd__UpdateHistoryString                              0x66C520

// CTabWnd 
#define CTabWnd__Draw                                              0x674ED0
#define CTabWnd__DrawCurrentPage                                   0x674840
#define CTabWnd__DrawTab                                           0x674620
#define CTabWnd__GetCurrentPage                                    0x674B40
#define CTabWnd__GetPageClientRect                                 0x6742F0
#define CTabWnd__GetPageFromTabIndex                               0x674550
#define CTabWnd__GetPageInnerRect                                  0x674350
#define CTabWnd__GetTabInnerRect                                   0x6744D0
#define CTabWnd__GetTabRect                                        0x6743E0
#define CTabWnd__IndexInBounds                                     0x6745ED
#define CTabWnd__InsertPage                                        0x6750F0
#define CTabWnd__SetPage                                           0x674B80
#define CTabWnd__SetPageRect                                       0x674E00
#define CTabWnd__UpdatePage                                        0x675070

// CTextOverlay 
#define CTextOverlay__DisplayText                                  0x413F70

// CTextureFont
#define CTextureFont__DrawWrappedText                              0x6574F0

// CXMLDataManager 
#define CXMLDataManager__GetXMLData                                0x67AD80

// CXMLSOMDocumentBase 
#define CXMLSOMDocumentBase__XMLRead                               0x64AB70

// CXRect 
#define CXRect__CenterPoint                                        0x5341C0

// CXStr 
// WARNING:  Be sure that none of these offsets are identical! 
// 
// Note:  dCXStr, CXStr1, &amp; CXStr3 can be found in the 'BookWindow' constructor. 
#define CXStr__CXStr                                               0x4122D0
#define CXStr__CXStr1                                              0x403580
#define CXStr__CXStr3                                              0x63F710
#define CXStr__dCXStr                                              0x66A090
#define CXStr__operator_equal1                                     0x63F8D0
#define CXStr__operator_plus_equal1                                0x6407C0

// CXWnd 
#define CXWnd__BringToTop                                          0x6504A0
#define CXWnd__Center                                              0x653BB0
#define CXWnd__ClrFocus                                            0x6501D0
#define CXWnd__DoAllDrawing                                        0x654860
#define CXWnd__DrawChildren                                        0x654990
#define CXWnd__DrawColoredRect                                     0x650700
#define CXWnd__DrawTooltip                                         0x654780
#define CXWnd__DrawTooltipAtPoint                                  0x653A00
#define CXWnd__GetBorderFrame                                      0x650BC0
#define CXWnd__GetChildWndAt                                       0x653500
#define CXWnd__GetClientClipRect                                   0x650B00
#define CXWnd__GetFirstChildWnd                                    0x650540
#define CXWnd__GetNextChildWnd                                     0x6534C0
#define CXWnd__GetNextSib                                          0x650560
#define CXWnd__GetScreenClipRect                                   0x653DD0
#define CXWnd__GetScreenRect                                       0x650DA0
#define CXWnd__GetTooltipRect                                      0x650930
#define CXWnd__GetWindowTextA                                      0x4E69E0
#define CXWnd__IsActive                                            0x6585D0
#define CXWnd__IsDescendantOf                                      0x650B70
#define CXWnd__IsReallyVisible                                     0x6534A0
#define CXWnd__IsType                                              0x676B10
#define CXWnd__Move                                                0x652FD0
#define CXWnd__Move1                                               0x653070
#define CXWnd__ProcessTransition                                   0x650470
#define CXWnd__Refade                                              0x650280
#define CXWnd__Resize                                              0x653F10
#define CXWnd__Right                                               0x653D20
#define CXWnd__SetFirstChildPointer                                0x651010
#define CXWnd__SetFocus                                            0x652300
#define CXWnd__SetKeyTooltip                                       0x651080
#define CXWnd__SetMouseOver                                        0x651050
#define CXWnd__SetNextSibPointer                                   0x651030
#define CXWnd__StartFade                                           0x6504C0

// CXWndManager 
#define CXWndManager__DrawCursor                                   0x659A20
#define CXWndManager__DrawWindows                                  0x659660
#define CXWndManager__GetFirstChildWnd                             0x658F70
#define CXWndManager__GetKeyboardFlags                             0x6582B0
#define CXWndManager__HandleKeyboardMsg                            0x6587C0
#define CXWndManager__RemoveWnd                                    0x6586E0

// CDBStr
#define CDBStr__GetString                                          0x4594F0

// EQ_Character 
#define EQ_Character__CastRay                                      0x69D4B0
#define EQ_Character__CastSpell                                    0x423910
#define EQ_Character__Cur_HP                                       0x42B380
#define EQ_Character__GetAACastingTimeModifier                     0x41EAB0
#define EQ_Character__GetCharInfo2                                 0x621080
#define EQ_Character__GetFocusCastingTimeModifier                  0x41AB20
#define EQ_Character__GetFocusRangeModifier                        0x41AC50
#define EQ_Character__Max_Endurance                                0x42A040
#define EQ_Character__Max_HP                                       0x429EF0
#define EQ_Character__Max_Mana                                     0x4F6700
#define EQ_Character__doCombatAbility                              0x4F5580
#define EQ_Character__UseSkill                                     0x436DB0
#define EQ_Character__GetConLevel                                  0x4F2A40

// EQ_Item 
#define EQ_Item__CanDrop                                           0x4E8550
#define EQ_Item__GetItemLinkHash                                   0x615BC0
#define EQ_Item__IsStackable                                       0x60CEC0

// EQ_LoadingS 
#define EQ_LoadingS__SetProgressBar                                0x471AC0
#define EQ_LoadingS__Array                                         0x740160

// EQ_PC 
#define EQ_PC__DestroyHeldItemOrMoney                              0x4F9630
#define EQ_PC__GetAltAbilityIndex                                  0x61A400
#define EQ_PC__GetCombatAbility                                    0x61A490
#define EQ_PC__GetCombatAbilityTimer                               0x61A540
#define EQ_PC__GetItemTimerValue                                   0x4F47E0
#define EQ_PC__HasLoreItem                                         0x4F7C50

// EQItemList 
#define EQItemList__dEQItemList                                    0x4999C0
#define EQItemList__EQItemList                                     0x499910

// EQMisc
#define EQMisc__GetActiveFavorCost                                 0x458F70

// EQPlayer 
#define EQPlayer__ChangeBoneStringSprite                           0x4FE130
#define EQPlayer__dEQPlayer                                        0x5026F0
#define EQPlayer__DoAttack                                         0x50DE80
#define EQPlayer__EQPlayer                                         0x504EA0
#define EQPlayer__SetNameSpriteState                               0x500840
#define EQPlayer__SetNameSpriteTint                                0x4FE1A0
#define EQPlayer__IsBodyType_j                                     0x69CEB0

//EQPlayerManager
#define EQPlayerManager__GetSpawnByID                              0x505E30

// KeyPressHandler 
#define KeypressHandler__AttachAltKeyToEqCommand                   0x4EA100
#define KeypressHandler__AttachKeyToEqCommand                      0x4EA140
#define KeypressHandler__ClearCommandStateArray                    0x4E9F10
#define KeypressHandler__HandleKeyDown                             0x4E8B20
#define KeypressHandler__HandleKeyUp                               0x4E8E20
#define KeypressHandler__SaveKeymapping                            0x4E9FE0

// MapViewMap 
#define MapViewMap__Clear                                          0x5A7A50
#define MapViewMap__SaveEx                                         0x5A8420

#define OtherCharData__GetAltCurrency                              0x636D20

// StringTable 
#define StringTable__getString                                     0x60C8D0
