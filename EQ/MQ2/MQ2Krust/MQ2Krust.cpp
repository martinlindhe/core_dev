/*
todo: uppdatera class-check vid varje puls eller nåt?

*/

#include "../MQ2Plugin.h"

VOID TargetByHP(PSPAWNINFO pChar, PCHAR szLine);
VOID AssistTarget(PSPAWNINFO pChar, PCHAR szLine);
VOID ShieldLowest(PSPAWNINFO pChar, PCHAR szLine);
VOID ReplyTarget(PSPAWNINFO pChar, PCHAR szLine);

PreSetup("MQ2Krust");
PLUGIN_VERSION(1.0);

class MQ2KrustType *pKrustType=0;

class MQ2KrustType : public MQ2Type
{
private:
	char Temp[MAX_STRING];

	bool HasBST, HasCLR, HasDRU, HasSHD, HasSHM, HasENC, HasPAL, HasMAG, HasNEC, HasRNG;
	bool CasterClass, MeleeClass, HybridClass, TankClass, HealerClass;

	bool HasBuff(char *name)
	{
		for (int n=0; n<25; n++)
		{
			if (PSPELL pSpell=GetSpellByID(GetCharInfo2()->Buff[n].SpellID))
			{
				if (!stricmp(name,pSpell->Name) || (strstr(pSpell->Name,"Rk. II") && !strnicmp(name,pSpell->Name,strlen(pSpell->Name)-8))) return true;
			} 
		}
		return false;
	}

	void InitClassCheck()
	{
		HasBST = false, HasCLR = false, HasDRU = false, HasSHD = false, HasSHM = false;
		HasENC = false, HasPAL = false, HasMAG = false, HasNEC = false, HasRNG = false;

		PCHARINFO pChInfo = GetCharInfo();

		if (pRaid && pRaid->RaidMemberCount)
		{
			//check raid classes
			//WriteChatf("In raid with %d members", pRaid->RaidMemberCount);

			for (DWORD index=0; index<pRaid->RaidMemberCount; index++)
			{
				switch ((PlayerClass) pRaid->RaidMember[index].nClass)
				{
					case Beastlord:		HasBST = true; break;
					case Cleric:		HasCLR = true; break;
					case Druid:			HasDRU = true; break;
					case Shadowknight:	HasSHD = true; break;
					case Shaman:		HasSHM = true; break;
					case Enchanter:		HasENC = true; break;
					case Paladin:		HasPAL = true; break;
					case Mage:			HasMAG = true; break;
					case Necromancer:	HasNEC = true; break;
					case Ranger:		HasRNG = true; break;
				}
			}

		}
		else if (pGroup && (pGroup->MemberExists[0] || pGroup->MemberExists[1] || pGroup->MemberExists[2] || pGroup->MemberExists[3] || pGroup->MemberExists[4]))
		{
			for (int index=0; index<5; index++)
			{ 
				if (pGroup->MemberExists[index] && pGroup->pMember[index])
				switch ((PlayerClass) pGroup->pMember[index]->Class)
				{	//check group classes
					case Beastlord:		HasBST = true; break;
					case Cleric:		HasCLR = true; break;
					case Druid:			HasDRU = true; break;
					case Shadowknight:	HasSHD = true; break;
					case Shaman:		HasSHM = true; break;
					case Enchanter:		HasENC = true; break;
					case Paladin:		HasPAL = true; break;
					case Mage:			HasMAG = true; break;
					case Necromancer:	HasNEC = true; break;
					case Ranger:		HasRNG = true; break;
				}
			}
		}

		switch ((PlayerClass) pChInfo->pSpawn->Class)
		{
			case Cleric:		HasCLR = true; break;
			case Druid:			HasDRU = true; break;
			case Shaman:		HasSHM = true; break;
			case Mage:			HasMAG = true; break;
			case Enchanter:		HasENC = true; break;
			case Necromancer:	HasNEC = true; break;
			case Wizard:		break;
			case Warrior:		break;
			case Shadowknight:	HasSHD = true; break;
			case Paladin:		HasPAL = true; break;
			case Beastlord:		HasBST = true; break;
			case Ranger:		HasRNG = true; break;
			case Bard:			break;
			case Monk:			break;
			case Rogue:			break;
			case Berserker:		break;
		}
	}

	/******************************

		HP & MELEE BUFFS

	******************************/
	void UpdateMeleeBuffs(MQ2TYPEVAR &Dest)
	{
		PCHARINFO	pChInfo = GetCharInfo();

		Dest.Type=pStringType; 
		Dest.Ptr=&Temp[0];
		Temp[0] = 0;

		InitClassCheck();

		if (HasDRU &&
			!HasBuff("Direwild Skin") && !HasBuff("Blessing of the Direwild") &&
			!HasBuff("Steeloak Skin") && !HasBuff("Blessing of Steeloak") &&
			!HasBuff("Blessing of the Nine") && !HasBuff("Protection of the Nine") &&
			!HasBuff("Tenacity") && !HasBuff("Hand of Tenacity") &&
			!HasBuff("Conviction") && !HasBuff("Hand of Conviction") &&
			!HasBuff("Virtue") && !HasBuff("Hand of Virtue") &&
			!HasBuff("Temperance") && !HasBuff("Blessing of Temperance"))
		{
			strcat(Temp, "DIREWILD ");
		}

		if (HasCLR && HasDRU &&
			!HasBuff("Symbol of Elushar") && !HasBuff("Elushar's Mark") &&
			!HasBuff("Symbol of Balikor") && !HasBuff("Balikor's Mark") &&
			!HasBuff("Symbol of Kazad") && !HasBuff("Kazad`s Mark") &&
			!HasBuff("Jeron's Mark") &&
			!HasBuff("Tenacity") && !HasBuff("Hand of Tenacity") &&
			!HasBuff("Conviction") && !HasBuff("Hand of Conviction") &&
			!HasBuff("Virtue") && !HasBuff("Hand of Virtue"))
		{
			strcat(Temp, "SYMBOL ");
		}

		if (!HasDRU && HasCLR &&
			!HasBuff("Direwild Skin") && !HasBuff("Blessing of the Direwild") &&
			!HasBuff("Steeloak Skin") && !HasBuff("Blessing of Steeloak") &&
			!HasBuff("Blessing of the Nine") && !HasBuff("Protection of the Nine") &&
			!HasBuff("Symbol of Elushar") && !HasBuff("Elushar's Mark") &&
			!HasBuff("Symbol of Balikor") && !HasBuff("Balikor's Mark") &&
			!HasBuff("Symbol of Kazad") && !HasBuff("Kazad`s Mark") &&
			!HasBuff("Jeron's Mark") &&
			!HasBuff("Tenacity") && !HasBuff("Hand of Tenacity") &&
			!HasBuff("Conviction") && !HasBuff("Hand of Conviction") &&
			!HasBuff("Virtue") && !HasBuff("Hand of Virtue") &&
			!HasBuff("Temperance") && !HasBuff("Blessing of Temperance"))
		{
			strcat(Temp, "TENA ");
		}

		if ((HasPAL || HasBST || HasRNG) &&
			!HasBuff("Brell's Stony Guard") && !HasBuff("Brell's Steadfast Aegis") &&
			!HasBuff("Brell's Brawny Bulwark") && !HasBuff("Brell's Stalwart Shield") &&
			!HasBuff("Brell's Mountainous Barrier") &&
			!HasBuff("Spiritual Vim") && !HasBuff("Spiritual Vitality") &&
			!HasBuff("Spiritual Vigor") && !HasBuff("Spiritual Strength") &&
			!HasBuff("Strength of the Forest Stalker") && !HasBuff("Strength of the Hunter") &&
			!HasBuff("Strength of Tunare") &&
			!HasBuff("Protection of the Minohten") && !HasBuff("Ward of the Hunter"))
		{
			if (pChInfo->pSpawn->Class == 4) { //ranger(4)
				strcat(Temp, "PotM ");
			} else if (HasPAL) {
				strcat(Temp, "BRELLS ");
			} else if (HasBST && pChInfo->pSpawn->Class != 2) {//skip cleric(2), Yaulp dont stack with SV
				strcat(Temp, "SV ");
			} else if (HasRNG) {
				strcat(Temp, "SotFS ");
			}
		}


		//4 = Ranger
		if (pChInfo->pSpawn->Class == 4)
		{	
			if (!HasBuff("Strength of the Hunter") && !HasBuff("Strength of the Forest Stalker") &&
				!HasBuff("Aura of Rage") && !HasBuff("Nature's Precision"))
			{
				strcat(Temp, "ATK ");
			}

			if (!HasBuff("Eagle Eye") && !HasBuff("Eyes of the Owl"))
			{
				strcat(Temp, "EYE ");
			}

			if (!HasBuff("Call of Lightning") && !HasBuff("Jolting Blades") &&
				!HasBuff("Sylvan Call") && !HasBuff("Cry of Thunder"))
			{
				strcat(Temp, "PROC ");
			}

			if (!HasBuff("Consumed by the Hunt"))
			{
				strcat(Temp, "CbtH ");
			}
		}

		if ((HasSHM || HasBST) &&
			!HasBuff("Dire Focusing") && !HasBuff("Talisman of the Dire") &&
			!HasBuff("Wunshi's Focusing") && !HasBuff("Talisman of Wunshi") &&
			!HasBuff("Focus of the Seventh") && !HasBuff("Focus of Soul") &&
			!HasBuff("Focus of Amilan") && !HasBuff("Focus of Alladnu") &&
			!HasBuff("Talisman of Kragg") &&
			!HasBuff("Shield of the Arcane") &&
			!HasBuff("Armor of the Pious") && !HasBuff("Armor of the Zealot"))
		{
			strcat(Temp, "FOCUS ");
		}

		if ((HasENC || HasSHM || HasBST) && !CasterClass &&
			!HasBuff("Speed of Ellowind") && !HasBuff("Hastening of Ellowind") &&
			!HasBuff("Speed of Salik") && !HasBuff("Hastening of Salik") &&
			!HasBuff("Vallon's Quickening") && !HasBuff("Speed of Vallon") &&
			!HasBuff("Celerity") && !HasBuff("Talisman of Celerity") &&
			!HasBuff("Alacrity") && !HasBuff("Talisman of Alacrity") &&
			!HasBuff("Swift like the Wind") && !HasBuff("Wonderous Rapidity") &&
			!HasBuff("Twitching Speed") &&
			!HasBuff("Elixir of Speed IX") && !HasBuff("Elixir of Speed X"))
		{
			strcat(Temp, "HASTE ");
		}


		if ((HasMAG || HasDRU) && TankClass &&
			!HasBuff("Fireskin") && !HasBuff("Circle of Fireskin") &&
			!HasBuff("Magmaskin") && !HasBuff("Circle of Magmaskin") &&
			!HasBuff("Aspect of Elemental Agony") &&
			!HasBuff("Viridifloral Shield") && !HasBuff("Legacy of Viridiflora") &&
			!HasBuff("Nettle Shield") && !HasBuff("Circle of Nettles"))
		{
			strcat(Temp, "DS ");
		}

		if (HasRNG && TankClass && !HasBuff("Guard of the Earth"))
		{
			strcat(Temp, "GotE ");
		}

		if (HasRNG && !CasterClass && pChInfo->pSpawn->Class != 4 &&		//skip rangers(4)
			!HasBuff("Snarl of the Predator") && !HasBuff("Howl of the Predator") &&
			!HasBuff("Call of the Predator") && !HasBuff("Spirit of the Predator"))
		{
			strcat(Temp, "PREDATOR ");
		}

		if ((HasSHM || HasDRU) && !CasterClass &&
			!HasBuff("Champion") &&
			!HasBuff("Mammoth's Strength") && !HasBuff("Lion's Strength") &&
			!HasBuff("Talisman of Might") && !HasBuff("Spirit of Might"))
		{
			strcat(Temp, "MAMMOTH ");
		}

		if (HasSHM &&
			!HasBuff("Preternatural Foresight") && !HasBuff("Talisman of Foresight") &&
			!HasBuff("Spirit of Sense") && !HasBuff("Talisman of Sense"))
		{
			strcat(Temp, "FORESIGHT ");
		}

		if (HasSHM &&
			!HasBuff("Talisman of Fortitude") && !HasBuff("Spirit of Fortitude"))
		{
			strcat(Temp, "FORTITUDE ");
		}

		if (HasCLR && TankClass &&
			(!HasBuff("Ward of Valiance") && !HasBuff("Ward of Gallantry"))
			&&
			(
			HasBuff("Symbol of Elushar") || HasBuff("Elushar's Mark") ||
			HasBuff("Symbol of Balikor") || HasBuff("Balikor's Mark") ||
			HasBuff("Symbol of Kazad") || HasBuff("Kazad`s Mark")
			)
			&&
			(
			HasBuff("Direwild Skin") || HasBuff("Blessing of the Direwild") ||
			HasBuff("Steeloak Skin") || HasBuff("Blessing of Steeloak") ||
			HasBuff("Blessing of the Nine") || HasBuff("Protection of the Nine")
			))
		{
			strcat(Temp, "WOV ");
		}

		if (pChInfo->pSpawn->Class == 2 &&		//cleric (2)
			!HasBuff("Armor of the Sacred") && !HasBuff("Armor of the Pious") &&
			!HasBuff("Armor of the Zealot") &&
			(
			HasBuff("Tenacity") || HasBuff("Hand of Tenacity") ||
			HasBuff("Conviction") || HasBuff("Hand of Conviction") ||
			HasBuff("Virtue") || HasBuff("Hand of Virtue")
			))
		{
			strcat(Temp, "ARMOR ");
		}

		//todo: ${Zone.ShortName.NotEqual[Tacvi]}
		if ((pChInfo->pSpawn->Class == 4 || pChInfo->pSpawn->Class == 6) &&		//ranger(4), druid(6)
			!HasBuff("Viridicoat") && !HasBuff("Nettlecoat") && !HasBuff("Briarcoat"))
		{
			strcat(Temp, "COAT ");
		}

		if (!strlen(Temp)) {
			sprintf(Temp, "OK");
		}
	}

	/******************************

		Caster buffs (C6+SA+spell haste)

	******************************/
	void UpdateCasterBuffs(MQ2TYPEVAR &Dest)
	{
		PCHARINFO	pChInfo = GetCharInfo();

		Dest.Type=pStringType; 
		Dest.Ptr=&Temp[0];
		Temp[0] = 0;

		InitClassCheck();
		InitMyClassCheck();

		if (MeleeClass) {
			sprintf(Temp, "OK");
			return;
		}

		if (HasCLR &&
			!HasBuff("Aura of Purpose") && !HasBuff("Blessing of Purpose") &&
			!HasBuff("Aura of Devotion") && !HasBuff("Blessing of Devotion") &&
			!HasBuff("Aura of Reverence") && !HasBuff("Blessing of Reverence"))
		{
			strcat(Temp, "SPELLHASTE ");
		}

		if (HasBST &&
			!HasBuff("Spiritual Enlightenment") && !HasBuff("Spiritual Ascendance") &&
			!HasBuff("Spiritual Dominion"))
		{
			strcat(Temp, "SE ");
		}

		if (HasENC &&
			!HasBuff("Voice of Intuition") && !HasBuff("Seer's Intuition") &&
			!HasBuff("Voice of Clairvoyance") && !HasBuff("Clairvoyance") &&
			!HasBuff("Voice of Quellious") && !HasBuff("Tranquility") &&
			!HasBuff("Koadic's Endless Intellect") && !HasBuff("Elixir of Clarity X"))
		{
			strcat(Temp, "CRACK ");
		}

		if ((pChInfo->pSpawn->Class == 4 || pChInfo->pSpawn->Class == 6) &&		//ranger(4), druid(6)
			!HasBuff("Mask of the Wild") && !HasBuff("Eyes of the Owl") &&
			!HasBuff("Mask of the Forest") && !HasBuff("Mask of the Stalker"))
		{
			strcat(Temp, "MASK ");
		}

		if (pChInfo->pSpawn->Class == 6	&& pChInfo->pSpawn->Level>=75 && !HasBuff("Second Life"))	//druid(6)
		{
			strcat(Temp, "2NDLIFE ");
		}

		//todo: check aura!
		//if ((${Me.Class.Name.Equal["Druid"]} && ${Me.Level}>=70) && !${Me.Song["Aura of Life Effect"].ID}) /varset str ${str} AURA

		if (!strlen(Temp)) {
			sprintf(Temp, "OK");
		}
	}

	/******************************

		RESIST BUFFS

	******************************/
	void UpdateResistBuffs(MQ2TYPEVAR &Dest)
	{
		PCHARINFO	pChInfo = GetCharInfo();

		Dest.Type=pStringType; 
		Dest.Ptr=&Temp[0];
		Temp[0] = 0;

		InitClassCheck();

		//fixme: läs in zoneinfo från INI
		///declare ZoneAllowsLevitate int local ${Ini[ZONEINFO_FILE, ZoneInfo, ${Zone.ShortName}_Levitate, 0]}
		//if (${HasNEC} && ${ZoneAllowsLevitate} && !${Me.Buff["Dead Men Floating"].ID} && !${Me.Buff["Dead Man Floating"].ID}) /varset str ${str} DMF


		if (HasENC && !HasBuff("Guard of Druzzil"))
		{
			strcat(Temp, "GoD ");
		}

		if (HasDRU && !HasBuff("Protection of Seasons"))
		{
			strcat(Temp, "PoS ");
		}

		if ((HasDRU || HasRNG) && pRaid && pRaid->RaidMemberCount && !HasBuff("Circle of Summer"))
		{
			strcat(Temp, "SUMMER ");
		}

		//Wishka har CORRUPT resist komponent, den behövs i AG/FC raids
		if ((HasSHM || HasBST) &&
			!HasBuff("Protection of Wishka") && !HasBuff("Talisman of the Tribunal") &&	!HasBuff("Talisman of Jasinth"))
		{
			if (HasSHM) {
				strcat(Temp, "Wishka ");
			} else {
				strcat(Temp, "Jasinth ");
			}
		}

		//todo: visa bara i AG/FC raider
		//zone shortname: vergalid_raid  = här behövs corrupt resists
		if ((HasDRU || HasCLR) && pRaid && pRaid->RaidMemberCount &&
			!HasBuff("Shared Purity") && !HasBuff("Resist Corruption"))
		{
			strcat(Temp, "CORRUPT ");
		}

		if (!strlen(Temp)) {
			sprintf(Temp, "OK");
		}
	}

public:
	enum KrustMembers
	{
		MeleeBuffs=1,		//String
		CasterBuffs,		//String
		ResistBuffs,		//String
		Caster,				//Bool
		Melee,				//Bool
		Hybrid,				//Bool
		Tank,				//Bool
		Healer				//Bool
	};

	//Constructor
	MQ2KrustType():MQ2Type("Krust")
	{
		TypeMember(MeleeBuffs);
		TypeMember(CasterBuffs);
		TypeMember(ResistBuffs);
		TypeMember(Caster);
		TypeMember(Melee);
		TypeMember(Hybrid);
		TypeMember(Tank);
		TypeMember(Healer);

		InitMyClassCheck();
	}

	//Destructor
	~MQ2KrustType()
	{
	}

	bool GetMember(MQ2VARPTR VarPtr, PCHAR Member, PCHAR Index, MQ2TYPEVAR &Dest) 
	{
		PMQ2TYPEMEMBER pMember = MQ2KrustType::FindMember(Member); 

		if (!pMember) return false; 

		switch ((KrustMembers)pMember->ID) 
		{ 
			case MeleeBuffs:
				UpdateMeleeBuffs(Dest);
				return true;

			case CasterBuffs:
				UpdateCasterBuffs(Dest);
				return true;

			case ResistBuffs:
				UpdateResistBuffs(Dest);
				return true;

			case Caster:
				Dest.DWord=CasterClass;
				Dest.Type=pBoolType;
				return true;

			case Melee:
				Dest.DWord=MeleeClass;
				Dest.Type=pBoolType;
				return true;

			case Hybrid:
				Dest.DWord=HybridClass;
				Dest.Type=pBoolType;
				return true;

			case Tank:
				Dest.DWord=TankClass;
				Dest.Type=pBoolType;
				return true;

			case Healer:
				Dest.DWord=HealerClass;
				Dest.Type=pBoolType;
				return true;
		}

		return false;
	}

	bool FromData(MQ2VARPTR &VarPtr, MQ2TYPEVAR &Source) {
		return false;
	}

	bool FromString(MQ2VARPTR &VarPtr, PCHAR Source) {
		return false;
	}

	void InitMyClassCheck()
	{
		PCHARINFO pChInfo = GetCharInfo();

		if (!gbInZone || !pChInfo || !pChInfo->pSpawn) return;

		CasterClass = false, MeleeClass = false, HybridClass = false, TankClass = false, HealerClass = false;

		switch ((PlayerClass) pChInfo->pSpawn->Class)
		{
			case Cleric:		CasterClass = true; HealerClass = true; break;
			case Druid:			CasterClass = true; HealerClass = true; break;
			case Shaman:		CasterClass = true; HealerClass = true; break;
			case Mage:			CasterClass = true; break;
			case Enchanter:		CasterClass = true; break;
			case Necromancer:	CasterClass = true; break;
			case Wizard:		CasterClass = true; break;
			case Warrior:		TankClass = true; MeleeClass = true; break;
			case Shadowknight:	TankClass = true; HybridClass = true; break;
			case Paladin:		TankClass = true; HybridClass = true; break;
			case Beastlord:		HybridClass = true; break;
			case Ranger:		HybridClass = true; break;
			case Bard:			HybridClass = true; break;
			case Monk:			MeleeClass = true; break;
			case Rogue:			MeleeClass = true; break;
			case Berserker:		MeleeClass = true; break;
		}
	}
};


BOOL dataKrust(PCHAR Index, MQ2TYPEVAR &Dest)
{
	Dest.DWord=1;
	Dest.Type=pKrustType;
	return true;
} 


// Called once, when the plugin is to initialize
PLUGIN_API VOID InitializePlugin(VOID)
{
	DebugSpewAlways("Initializing MQ2Krust");

	pKrustType = new MQ2KrustType;
	AddMQ2Data("Krust", dataKrust); 

	// Add commands, MQ2Data items, hooks, etc.
	AddCommand("/targethp", TargetByHP);
	AddCommand("/assisttarget", AssistTarget);
	AddCommand("/shieldlowest", ShieldLowest);
	AddCommand("/reptar", ReplyTarget);	 //rt replacement which skips range check
}

// Called once, when the plugin is to shutdown
PLUGIN_API VOID ShutdownPlugin(VOID)
{
	DebugSpewAlways("Shutting down MQ2Target");

	RemoveMQ2Data("Krust");
	delete pKrustType;

	// Remove commands, MQ2Data items, hooks, etc.
	RemoveCommand("/targethp");
	RemoveCommand("/assisttarget");
	RemoveCommand("/shieldlowest");
	RemoveCommand("/reptar");
}

// Called after entering a new zone
PLUGIN_API VOID OnZoned(VOID)
{
	DebugSpewAlways("MQ2Krust::OnZoned()");

	pKrustType->InitMyClassCheck();
} 

//todo: add a replacement to /rt to target spawn instantly if i have recieved a tell & he is in zone, no matter what distance
//targets the person you last got a /tell from, if player is in zone
VOID ReplyTarget(PSPAWNINFO pChar, PCHAR szLine)
{
//    if (!ppSpawnList) return;
    if (!pSpawnList) return;
    CHAR szArg[MAX_STRING] = {0};
    CHAR szMsg[MAX_STRING] = {0};
    CHAR szLLine[MAX_STRING] = {0};
    PCHAR szFilter = szLLine;

	_strlwr(strcpy(szLLine,szLine));

	//${MacroQuest.LastTell}
	if (EQADDR_LASTTELL[0]) //there is a "last tell"
	{
		//fixme: if PC in zone with this name then try to target
		{
		    PSPAWNINFO pSpawnClosest = NULL;
			SEARCHSPAWN SearchSpawn;
			ClearSearchSpawn(&SearchSpawn);

			WriteChatf("/reptar - attempting to target who i lastly got tell from: %s", &EQADDR_LASTTELL[0]);

			//skriv bara namn i szFilter
			szFilter = ParseSearchSpawnArgs(szArg,EQADDR_LASTTELL,&SearchSpawn);
			WriteChatf("szFilter: %s",szFilter);

			//vet inte om denna spelar roll:
			if (pTarget) SearchSpawn.FromSpawnID = ((PSPAWNINFO)pTarget)->SpawnID;

			CHAR szTemp[MAX_STRING] = {0};
			WriteChatf("spawn parse: %s", FormatSearchSpawn(szTemp,&SearchSpawn));
			//fixme: väljer just nu bara mej själv

			pSpawnClosest = SearchThroughSpawns(&SearchSpawn,pChar);
			if (pSpawnClosest) {
				WriteChatf("pSpawnClosest: %s",pSpawnClosest->Name);
			}

	        PSPAWNINFO *psTarget = NULL;
		    if (ppTarget && pSpawnClosest) {
			    psTarget = (PSPAWNINFO*)ppTarget;
				*psTarget = pSpawnClosest;
	            WriteChatf("Target - %s selected",pSpawnClosest->Name);
				szMsg[0]=0;
			} else {
				sprintf(szMsg,"Unable to target, address = 0");
			}



		}
	} else {
		WriteChatf("/reptar - i havent recieved any tells!");
	}

}

//assisttarget - gives you the HoTT target instantly, as a /assist without a name as parameter works but quick
VOID AssistTarget(PSPAWNINFO pChar, PCHAR szLine)
{
	PCHARINFO	pChInfo = GetCharInfo();

	if (pTarget && ppTarget)
	{
		PSPAWNINFO	Target = (PSPAWNINFO)pTarget;

		if (Target->SpawnID == pChInfo->pSpawn->SpawnID) {	//ignore if i am targeting myself
			return;
		}

		/* 1. If there is a target in HoTT, change to this target instantly */
		if (pChInfo->pSpawn->pTargetOfTarget)
		{
			if (pChInfo->pSpawn->pTargetOfTarget->SpawnID != Target->SpawnID) //dont do anything if target is targething himself
			if (ppTarget) {
				PSPAWNINFO	*psTarget = (PSPAWNINFO*)ppTarget;
				*psTarget = pChInfo->pSpawn->pTargetOfTarget;;
			}
		}
		/* 2. Else, do an old fashioned /assist */
		//fixme: can't tell if hott is enabled and my target has no target, or if hott is disabled!
		//	would save me from sending /assist in these cases
		else
		{
			//fixme: what is longest distance for /assist to work?
			if (GetDistance(Target->X,Target->Y) > 200) {
				WriteChatf("# Warning: /assisttarget - HoTT not enabled and target is too far away, aborting");
			} else {
				DoCommand(pChar, "/assist");
				WriteChatf("/assisttarget - Did old fashioned /assist");
			}
		}
	}
}


/* Target group member or HoTT depending on lowest HP% */
VOID TargetByHP(PSPAWNINFO pChar, PCHAR szLine)
{
	PCHARINFO	pChInfo = GetCharInfo();

	PSPAWNINFO me = GetCharInfo()->pSpawn; 

	PSPAWNINFO	pNewTarget = NULL;
	int			lowestHP = pChInfo->pSpawn->HPCurrent * 100 / pChInfo->pSpawn->HPMax; 

	/* 1. If I'm below 100% HP, start with myself */
	if (lowestHP < 100) {
		pNewTarget = pChInfo->pSpawn;
	}

	/* 2. Check target (if target PC is lower health than any else, we keep this as target) */
	if (pTarget)
	{
		PSPAWNINFO Target = (PSPAWNINFO)pTarget;
		if (Target->Type == SPAWN_PLAYER)	//ignore corpses, npc's
		if (GetDistance(me, Target) < 200)	//only care about players in range
		if (Target->SpawnID != pChInfo->pSpawn->SpawnID) //ignore myself
		if (Target->HPCurrent < lowestHP)
		{
			lowestHP = Target->HPCurrent;
			pNewTarget = Target;
		}
	}

	/* 3. Check target in HoTT */
	if (pTarget)
	if (pChInfo->pSpawn->pTargetOfTarget)
	if (pChInfo->pSpawn->pTargetOfTarget->Type == SPAWN_PLAYER)	//ignore corpses, npc's
	if (GetDistance(me, pChInfo->pSpawn->pTargetOfTarget) < 200)	//only care about players in range
	if (pChInfo->pSpawn->pTargetOfTarget->SpawnID != pChInfo->pSpawn->SpawnID) //ignore myself
	{
		if (pChInfo->pSpawn->pTargetOfTarget->HPCurrent < lowestHP) {
			//HoTT has lower % HP than I do, use HoTT instead
			lowestHP = pChInfo->pSpawn->pTargetOfTarget->HPCurrent;
			pNewTarget = pChInfo->pSpawn->pTargetOfTarget;
		}
	}

	/* 4. Checking group members if someone has even less % HP */
	if (pGroup->MemberExists[0] || pGroup->MemberExists[1] || pGroup->MemberExists[2] || pGroup->MemberExists[3] || pGroup->MemberExists[4]) {
		for (int index=0; index<5; index++) { 
			if (pGroup->MemberExists[index])
			if (pGroup->pMember[index])
			if (pGroup->pMember[index]->Type == SPAWN_PLAYER)	//ignore dead group mates
			if (GetDistance(me, pGroup->pMember[index]) < 200)	//only care about players in range

			if (pGroup->pMember[index]->HPCurrent < lowestHP) {
				lowestHP = pGroup->pMember[index]->HPCurrent;
				pNewTarget = pGroup->pMember[index];			
			}
		}
	}

	/* 5. Target the spawn with least HP */
	if (ppTarget && pNewTarget) {
		PSPAWNINFO	*psTarget = (PSPAWNINFO*)ppTarget;
		*psTarget = pNewTarget;
		//WriteChatf("/targethp - %s selected at %d%% HP", pNewTarget->Name, pNewTarget->HPCurrent);
	}
}

/* /shieldlowest, warrior target the lowest % HP group member and shield them if they are 30% hp or below */
VOID ShieldLowest(PSPAWNINFO pChar, PCHAR szLine)
{
	PSPAWNINFO me = GetCharInfo()->pSpawn; 

	PCHARINFO	pChInfo = GetCharInfo();
	PSPAWNINFO	pNewTarget = NULL;
	int			lowestHP = 100; 

	//todo: can out of group members be /shield'ed? in that case check target and hott for players

	/* 1. Checking group members if someone has even less % HP */
	if (pGroup->MemberExists[0] || pGroup->MemberExists[1] || pGroup->MemberExists[2] || pGroup->MemberExists[3] || pGroup->MemberExists[4]) {
		for (int index=0; index<5; index++) { 
			if (pGroup->MemberExists[index])
			if (pGroup->pMember[index])
			if (pGroup->pMember[index]->Type == SPAWN_PLAYER)	//ignore dead group mates
			if (GetDistance(me, pGroup->pMember[index]) < 200)	//only care about players in range
			if (pGroup->pMember[index]->HPCurrent <= 30)
			if (pGroup->pMember[index]->HPCurrent < lowestHP) {
				lowestHP = pGroup->pMember[index]->HPCurrent;
				pNewTarget = pGroup->pMember[index];			
			}
		}
	}

	/* 2. Target the spawn with least HP */
	if (ppTarget && pNewTarget) {
		PSPAWNINFO	*psTarget = (PSPAWNINFO*)ppTarget;
		*psTarget = pNewTarget;
		WriteChatf("/shieldlowest - Shielding %s %d%% HP", pNewTarget->Name, pNewTarget->HPCurrent);
		DoCommand(pChar, "/shield");
	}
}


