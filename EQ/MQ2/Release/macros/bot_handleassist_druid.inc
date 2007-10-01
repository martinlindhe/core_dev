|*
 todo:

	battlespam checker för om en mob snarats, + gör en spawncount check på npc med det namnet inom viss
	range så vet vi om en mobb e snarad av en sk så skippar druiden autosnaring

	- ändra nuke loopen:
		1) när man fått ett target, vänta tills det droppar till "start nuke pct", t.ex 96% hp
		2) spara % hp i en variabel när min nuke landar (?). sen nuka inte igen förräns % hp är lägre än förra gången


	- läsa DO_SNARE från ini, av/på beroende på zon:
		ashengate:av
		frostcrypt: av
		splitpaw:av
		tipt:på
		vergalid mines:på
		arcstone:på

	- autodetecta vilken healspell som är memorizad

	- reagera på "Your target resisted the XXX spell." för snares
 
*|


#define DO_SNARE						0						| shall i snare mobs?
#define DO_DEBUFF						1						| debuff will be cast once per mob, not in heal only mode
#define DO_EPIC							1						| click epic 1.5/2.0 when it is ready?
#define DO_AA_DS						1						| Auto cast Wrath of the Wild anytime it is up. First on yourself, if you already have the buff, cast it on MA

#define DO_REPTILE					0						| casts skin of the reptile on tank at beginning of fight, good for monk tanks :)
#define DO_REPTILE_PLAYER		Yamakasi

| Percent levels
#define SNARE_PERCENT				99
#define DEBUFF_PERCENT			97
| 95% is perfect for raids & lv75 groups
#define STARTNUKE_PERCENT		95


#define LOM_PERCENT					10					| mana % limit, dont nuke below this limit


| Will use the first one if memorized when script starts, else the other
#define NUKE_SPELLNAME_COLD			"Rime Crystals Rk. III"
#define DEBUFF_SPELLNAME_COLD		"Icefall Breath Rk. II"

#define NUKE_SPELLNAME_FIRE			"Equinox Burn Rk. III"
#define DEBUFF_SPELLNAME_FIRE		"Blistering Sunray Rk. II"
#define DEBUFF_SPELLNAME_FIRE_2	"Hand of Ro"
#define NUKE_DOT_SPELLNAME_FIRE_3	"Nature's Blazing Wrath"

#define SNARE_SPELLNAME					"Serpent Vines"
#define EPIC_ITEMNAME						"Staff of Everliving Brambles"

#define DRUID_QH1								"Pure Life Rk. III"
#define DRUID_QH2								"Adrenaline Surge Rk. III"						| even faster qheal spell, used if player % is low & spell is ready
#define DRUID_INSTANTHEAL				"Convergence of Spirits"

|----------------------------------------------

#event DEBUFF_DruSerpentVines "#1# is ensnared in the embrace of earth."
Sub Event_DEBUFF_DruSerpentVines(string Line, string Name)
	| DRU/69: Serpent Vines
	/if (${Me.Class.Name.Equal[Druid]} && ${timerSnareCounter}) /varset mobIsSnared	1
/return

#event DEBUFF_DruNaturesSerenity "#1#'s mind calms."
Sub Event_DEBUFF_DruNaturesSerenity(string Line, string Name)
	| DRU/67: Natures Serenity
	/if (${Me.Class.Name.Equal[Druid]}) /call DebugMsg "PACIFIED: ${Target.CleanName} (${Target.ID})"
/return

#event DEBUFF_DruImmolationOfSun "#1# is immolated by the flames of the sun."
Sub Event_DEBUFF_DruImmolationOfSun(string Line, string Name)
	| DRU/67: Immolation of the Sun
	| DRU/71: Blistering Sunray
	/if (${Me.Class.Name.Equal[Druid]}) /call DebugMsg "DEBUFFED: ${Target.CleanName} (DoT: FIRE, AC)"
/return

#event DEBUFF_DruHoR "#1# is immolated by blazing flames."
Sub Event_DEBUFF_DruHoR(string Line, string Name)
	| DRU/61: Hand of Ro
	/if (${Me.Class.Name.Equal[Druid]}) /call DebugMsg "DEBUFFED: ${Target.CleanName} (HoR: FIRE, ATK, AC)"
/return

#event DEBUFF_DruCold "#1#s body is covered in a film of ice."
Sub Event_DEBUFF_DruCold(string Line, string Name)
	| DRU/67: Glacier Breath
	| DRU/72: Icefall Breath
	/if (${Me.Class.Name.Equal[Druid]}) /call DebugMsg "DEBUFFED: ${Target.CleanName} (IB: AC, +Nuke))"
/return

#event DEBUFF_DruHoarFrost "#1# is outlined in white ice."
Sub Event_DEBUFF_DruHoarFrost(string Line, string Name)
	| DRU/74: Hoar Frost
	/if (${Me.Class.Name.Equal[Druid]}) /call DebugMsg "DEBUFFED: ${Target.CleanName} (HoarFrost: ATK, AC)"
/return

#event DEBUFF_DruidEpic	"#1# is cursed by nature's blight."
#event DEBUFF_DruidEpic	"#1# is caught in the fury of the seasons."
Sub Event_DEBUFF_DruidEpic(string line, string name)
	/call DebugMsg "DEBUFFED: ${name} (DRU EPIC)"
/return



Sub DruSnareMob(int SpawnId)

	/target npc id ${SpawnId}
	/delay 2s ${Target.ID}==${SpawnId}

	/if (${Ini[INI_FILE, ImmuneSnare_${Zone.ShortName}, ${Target.CleanName}, 0]}) {
		/call DebugMsg "IMMUNESNARE: ${Target.CleanName} (learned previously)"
		/goto :DoneSnaring
	}

	/if (${Ini[INI_FILE, DontRun_${Zone.ShortName}, ${Target.CleanName}, 0]}) {
		/call DebugMsg "DONT RUN: ${Target.CleanName} (says INI)"
		/goto :DoneSnaring
	}
	
	| /call DebugMsg "Starting snare loop..."

	/varset mobIsSnared	0
	/varset mobSnareImmune 0
	/varset mobNotTakeHold 0
	/varset AbortLoop 0
	:SnareLoop
		/doevents

		/call HandleGroupHeal QHEAL_PERCENT
		/if (!${Spawn[id ${SpawnId} npc].ID}) {
			/call DebugMsg "Stopping snare loop, target dead"
			/goto :DoneSnaring
		}

		/if (${Target.ID} != ${SpawnId}) {
			/target npc id ${SpawnId}
			/delay 2s ${Target.ID}==${SpawnId}
		}

		/if (${Target.LineOfSight} && ${Target.Distance} < ${Me.Gem[${Me.Gem["SNARE_SPELLNAME"]}].Range} ) {

			/if (${Target.Type.NotEqual[NPC]}) {
				/call DebugMsg "Aborting on target, not a npc!"
				/goto :DoneSnaring
			}
			| /call DebugMsg "Snaring ${Target.CleanName} (${Me.PctMana}m)"
			/call WaitForSpellReady "SNARE_SPELLNAME"
			/varset timerSnareCounter 7s
			/call MQ2Cast "SNARE_SPELLNAME"
		
			| Loop until mob is snared
			:DruidHandleSnareLoop
				/doevents
				/call HandleGroupHeal QHEAL_PERCENT
				/if (!${timerSnareCounter} || ${mobIsSnared} || ${mobSnareImmune} || ${mobNotTakeHold}) {
					/if (${mobNotTakeHold}) {
						/call DebugMsg "CANTSNARE: ${Target.CleanName} (dont take hold)"
						/goto :DoneSnaring
					}
					/if (${mobSnareImmune}) {
						/ini "INI_FILE" "ImmuneSnare_${Zone.ShortName}" "${Target.CleanName}" "1"
						/call DebugMsg "IMMUNESNARE: ${Target.CleanName}"
						/goto :DoneSnaring
					}
					/if (!${mobIsSnared}) {
						/call DebugMsg "Snare failed, retrying!"
						/goto :SnareLoop
					} else {
						/call DebugMsg "SNARED: ${Target.CleanName} (${Target.ID})"
						/goto :DoneSnaring
					}
				}
			/goto :DruidHandleSnareLoop

		} else {
			/if (${Target.ID} && !${AbortLoop}) {
				/call DebugMsg "Can't snare, mob is oor/no LoS/spell not memorized!"
				/doevents
				/delay 3s
				/goto :SnareLoop
			}
		}
			
	:DoneSnaring
/return

Sub DruidWaitForTargetHP(int ID, int pct)

	/declare oldid int local ${Target.ID}

	/if (${Target.ID} != ${ID}) {
		/target id ${ID}
		/delay 2s ${Target.ID}==${ID}
	}

	:Loop
	/if (${Target.PctHPs} > ${pct}) {
		/doevents

		/call HandleGroupHeal QHEAL_PERCENT
		/goto :Loop
	}
	
	/if (${oldid}) {
		/target id ${oldid}
		/delay 2s ${Target.ID}==${oldid}
	}

/return


Sub HandleAssistCallDruid(int SpawnId)

	/if (!${SpawnId}) /return
	
	/varset currentSpawn ${SpawnId}

	| global druid variables:
	/if (!${Defined[reptileTimer]}) /declare reptileTimer timer outer 0

	/declare tankId int local 0
	/declare tankName string local

	/declare nukeName string local
	/declare debuffName string local

	/if (${Me.Gem[NUKE_SPELLNAME_COLD]}) {
		/varset nukeName NUKE_SPELLNAME_COLD
	} else /if (${Me.Gem[NUKE_SPELLNAME_FIRE]}) {
		/varset nukeName NUKE_SPELLNAME_FIRE
	} else {
		/echo FATAL: NO NUKE MEMORIZED!
		/endmacro
	}
	
	/if (${Me.Gem[DEBUFF_SPELLNAME_COLD]}) {
		/varset debuffName DEBUFF_SPELLNAME_COLD
	} else /if (${Me.Gem[DEBUFF_SPELLNAME_FIRE]}) {
		/varset debuffName DEBUFF_SPELLNAME_FIRE
	} else /if (${Me.Gem[DEBUFF_SPELLNAME_FIRE_2]}) {
		/varset debuffName DEBUFF_SPELLNAME_FIRE_2
	} else /if (${Me.Gem[NUKE_DOT_SPELLNAME_FIRE_3]}) {
		/varset debuffName NUKE_DOT_SPELLNAME_FIRE_3
	} else {
		/echo FATAL: NO DEBUFF MEMORIZED!
		/endmacro
	}

	/call FindTank
	/varset tankId ${Macro.Return}
	/varset tankName ${Spawn[id ${tankId}].Name}

	/call HandleGroupHeal QHEAL_PERCENT

	/target npc id ${SpawnId}

	| Send in pet if u have one
	/if (${Me.Pet.ID}) {
		/call DruidWaitForTargetHP ${SpawnId} 96
		/if (${Target.ID} != ${SpawnId}) {
			/target npc id ${SpawnId}
			/delay 2s ${Target.ID}==${SpawnId}
		}
		/if (${Target.Distance} < 100) {
			/call DebugMsg "Sending in pet"
			/pet attack
		}
	}

	/if (DO_DEBUFF && ${Target.Type.Equal[NPC]} && ${Me.Standing} && (${Target.PctHPs} >= 80) && (${Me.PctMana} > LOM_PERCENT) && (${Spawn[id ${SpawnId}].Level} >= 62)) {
		/call DruidWaitForTargetHP ${SpawnId} DEBUFF_PERCENT
		/call WaitForSpellReady "${debuffName}"
		/call DebugMsg "Debuffing ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)"
		/call BotCast "${debuffName}" NukeCallback
		/doevents
	}

	/if (DO_SNARE) {
		/call DruidWaitForTargetHP ${SpawnId} SNARE_PERCENT
		/call HandleGroupHeal QHEAL_PERCENT
		/call DruSnareMob ${SpawnId}
	}

	/if (DO_AA_DS) /call DruidRefreshAA_DS

	/varset AbortLoop 0
	:DruidLoop
		/doevents
		/if (!${Spawn[npc id ${SpawnId}].ID}) {
			/call DebugMsg "Ending nuking (mob dead) (${Me.PctMana}m)"
			/goto :DruidDone
		}

		| Breaks out of combat on FD mobs (Deathknell)
		/if (${Target.Feigning}) /goto :DruidDone

		/if (${AbortLoop}) /goto :DruidDone

		/call HandleGroupHeal QHEAL_PERCENT

		/if (DO_REPTILE && !${reptileTimer} && ${Me.SpellReady["Skin of the Reptile"]} && ${Spawn[pc DO_REPTILE_PLAYER].Distance} < 100) {
			/varset reptileTimer 90s
			/target pc DO_REPTILE_PLAYER
			/delay 10
			/call DebugMsg "REPTILE > ${Target.Name} < (${Me.PctMana}m)"
			/call BotCast "Skin of the Reptile" NukeCallback
		}

		/if (DO_EPIC && !${Window[LootWnd].Open} && !${Me.Stunned} && ${Me.Standing} && !${FindItem[EPIC_ITEMNAME].Timer} && ${Target.ID} && (${Target.PctHPs} > 50) && (${Target.PctHPs} < 100) && ${Target.Type.Equal[NPC]} && (${Target.Level} >= 60) && ${Target.LineOfSight} && ${Target.Distance} < 100) {
			/call DebugMsg "Debuffing ${Target.CleanName} (DRU EPIC)"
			/call MQ2Cast "EPIC_ITEMNAME" item
		}
		
		/if (DO_AA_DS) /call DruidRefreshAA_DS

		/if ((${Target.ID} != ${SpawnId}) && ${Spawn[npc id ${SpawnId}].ID} && ${Spawn[npc id ${SpawnId}].LineOfSight}) {
			/target npc id ${SpawnId}
			/delay 2s ${Target.ID}==${SpawnId}
		}

		/if (!${Window[LootWnd].Open} && ${Me.Standing} && !${Me.Stunned} && ${Zone.ShortName.NotEqual["Frostcrypt"]} && ${Zone.ShortName.NotEqual["frostcrypt_raid"]} && ${Zone.ShortName.NotEqual["Valdeholm"]} && ${Zone.ShortName.NotEqual["valdeholm_smallevent"]} && ${Zone.ShortName.NotEqual["valdeholm_raid"]} && !${FindItem["Everliving Bramble Tunic"].Timer} && !${FindItem["Everliving Bramble Tunic"].InvSlot.Slot} && ${Spawn[npc id ${SpawnId}].LineOfSight} && (${Spawn[npc id ${SpawnId}].Distance} < 200)) {
			/call DebugMsg "Casting nuke clicky"
			/call MQ2Cast "Everliving Bramble Tunic" item
		}

		/if (!${timerNuke} && ${Me.Standing} && !${Me.Moving} !${Me.Stunned} && (${Target.ID}==${SpawnId}) && ${Target.Type.Equal[NPC]} && (${Target.PctHPs} <= STARTNUKE_PERCENT) && ${Target.LineOfSight} && ${Me.SpellReady[${nukeName}]} && !${Window[LootWnd].Open} && !${Window[SpellBookWnd].Open} && ${Spawn[npc id ${SpawnId}].ID} && ${Spawn[npc id ${SpawnId}].LineOfSight} && (${Me.TargetOfTarget.ID} != ${Me.ID}) && (${Target.Distance} < ${Me.Gem[${Me.Gem[${nukeName}]}].Range}) && (${Me.PctMana} > LOM_PERCENT)) {
			/call DebugMsg "Nuking ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)"
			/call BotCast "${nukeName}" NukeCallback
			/varset timerNuke 5s
		}

		/call HandleGroupHeal QHEAL_PERCENT

		/if (!${Window[LootWnd].Open} && ${Me.Standing} && !${FindItem["Bark-Inlaid Leather Tunic"].Timer} && !${FindItem["Bark-Inlaid Leather Tunic"].InvSlot.Slot}) {
			/call FindTank
			/if (${Macro.Return}) {
				/target id ${Macro.Return}
				/delay 1s ${Target.ID}==${Macro.Return}
				/call DebugMsg "DS Tunic on > ${Target.Name} <"
				/call MQ2Cast "Bark-Inlaid Leather Tunic" item
			}
		}

		/if (${Me.Standing} && !${Me.Moving} && !${Me.Casting.ID} && !${Window[LootWnd].Open} && (${Me.PctMana} < LOM_PERCENT) && !${Me.Buff["Summon Drogmor"].ID}) {
			/if (${Spawn[npc radius 40 notid ${SpawnId}].LineOfSight}) {							
				/call DebugMsg "LOM (${Me.PctMana}m) - But ${SpawnCount[npc radius 40 notid ${SpawnId}]} adds in camp!"
				/delay 1s
			} else {
				/call DebugMsg "LOM - Medding (${Me.PctMana}m)"
				/delay 10
				/varset timerNuke 3m
				/if (${Me.Standing} && !${Me.Moving} && !${Me.Casting.ID}) /sit
			}
		}

	/goto :DruidLoop

	:DruidDone
	/call HandleGroupHeal QHEAL_PERCENT
	
	/call UpdateBuffs
	/call HandleEndOfFightCaster
/return

| Level 75 druid buffs & clickies
| todo: return if 1 buff refresh is done, let this function be called often and do buffs 1 by 1 when needed instead of all at once (in case mobs are inc so bot dont waste times rebuffing)
Sub DruidUpdateBuffs

	| Clear debuffs:
	/if (${Me.AltAbilityReady["Radiant Cure"]} && (${Me.Buff["Malo"].ID} || ${Me.Buff["Wind of Tashanian"].ID} || ${Me.Buff["Poxed Web"].ID} || ${Me.Buff["Malis Rk. III"].ID} || ${Me.Buff["Curse of Darkness"].ID} || ${Me.Buff["Flesh Rot"].ID} || ${Me.Buff["Suffocating Mist"].ID} || ${Me.Buff["Tainted Arrow Salvo"].ID} || ${Me.Buff["Depleting Wave"].ID})) {
		/call DebugMsg "Curing debuffs"
		/casting "Radiant Cure" alt
		|3.5 sec cast time
		/delay 40
	}

	| Dont waste time rebuffing if i am healing!
	/if (HEAL_ONLY_MODE && ${SpawnCount[npc radius 100]} && ${Zone.Name.NotEqual["Guild Lobby"]} && ${Zone.Name.NotEqual["Guild Hall"]}) /return

	| Razorthorn (ragea_raid) trash mobs cast AE that overwrites Mask of the Wild
	/if (${Zone.ShortName.NotEqual["ragea_raid"]}) {
		/call RefreshBuff "Mask of the Wild" 30
		/if (${Macro.Return}) /return
	}

	/call RefreshBuff "Viridicoat Rk. II" 30
	/if (${Macro.Return}) /return

	/if (${Me.SpellReady["Second Life Rk. II"]}) {
		/call RefreshBuff "Second Life Rk. II" 20
		/if (${Macro.Return}) /return
	}

	/if (${Group.Members}) {
		/if (!${Raid.Members}) /call RefreshBuff "Blessing of the Direwild Rk. III" 50
		/if (${Macro.Return}) /return
		/if (!${Me.Song["Aura of Life Effect"].ID}) {
			| this is a hack because it seems to fade sometimes when you move or lag
			/delay 5
			/if (!${Me.Song["Aura of Life Effect"].ID}) {
				/call RefreshBuff "Aura of Life" 0
				/if (${Macro.Return}) /return
			}
		}
		/if (!${Raid.Members} && !HEAL_ONLY_MODE) {
			/if (${Me.Gem["Legacy of Viridiflora Rk. II"]}) {
				/call RefreshBuff "Legacy of Viridiflora Rk. II" 50
				/if (${Macro.Return}) /return
			}
			/if (${Me.Gem["Blessing of Oak"]}) {
				/call RefreshBuff "Blessing of Oak" 50
				/if (${Macro.Return}) /return
			}
		}
	}

	/if (${Zone.ShortName.Equal["ashengate_raid"]} || ${Zone.ShortName.Equal["frostcrypt_raid"]}) {
		/call RefreshBuff "Resist Corruption Rk. III"
		/if (${Macro.Return}) /return
	}

	/if (!${Raid.Members} && !${SpawnCount[npc radius 100]} && !HEAL_ONLY_MODE) {
		| Clicky - Shield of Dreams
		/call RefreshBuff "Brazier of Endless Flame" 20
		/if (${Macro.Return}) /return
	}

	| Clicky - Reyfin's Random Musings
	/call RefreshBuff "Earring of Pain Deliverance" 20
	/if (${Macro.Return}) /return

	| Clicky - Form of Endurance V
	/call RefreshBuff "Frost-Scarred Giant Hide Bracer" 20
	/if (${Macro.Return}) /return

	| Clicky - Geomantra III
	/call RefreshBuff "Crimson Mask of Triumph" 20
	/if (${Macro.Return}) /return

	/if (${Raid.Members} < 18) {
		| Clicky - HP buff (symbol overwrites it)
		/call RefreshBuff "Orb of Duskmold" 20
		/if (${Macro.Return}) /return
	}

	| Pet buffs
	/if (${Me.Pet.ID} && ${Me.Pet.CleanName.NotEqual["${Me.Name}`s familiar"]}) {
		/if (!${Me.PetBuff["Savage Spirit"]}) {
			/call DebugMsg "Refreshing pet haste"
			/call MQ2Cast "Sand Eroded Shell" item
		}
		/call RefreshPetBuff "Mammoth's Strength" 50
	}

	| Shrink myself
	/if (${Raid.Members} && ${Me.Height} > 2.00) /call ShrinkSelf "Bracelet of the Shadow Hive"

	/if (!HEAL_ONLY_MODE && DO_AA_DS) /call DruidRefreshAA_DS
	
/return

Sub DruidRefreshAA_DS

	/if (!${Me.AltAbilityReady["Wrath of the Wild"]} || ${Me.Moving} || ${Me.Sitting} || ${Me.Casting.ID} || ${Target.ID} || ${Window[LootWnd].Open}) /return

	| only cast when we are possibly in a fight (npc's near)
	/if (${Raid.Members} && !${SpawnCount[npc radius 100]}) /return

	/declare tankId int local 0

	/if (!${Me.Buff["Wrath of the Wild"].ID} && !${Raid.Members}) {
		/call DebugMsg "Buffing self with AA DS"
		/target myself
		/call MQ2Cast "Wrath of the Wild" alt
		/delay 1s
	} else {
		/call FindTank
		/varset tankId ${Macro.Return}
		/if (${tankId}) {
			/if (${Spawn[id ${tankId}].Distance} < 100) {
				/call DebugMsg "Buffing ${Spawn[id ${tankId}].Name} with AA DS"
				/target id ${tankId}
				/call MQ2Cast "Wrath of the Wild" alt
				/delay 1s
			}
		}
	}
/return