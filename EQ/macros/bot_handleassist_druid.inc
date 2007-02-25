|*
 todo:

	battlespam checker för om en mob snarats, + gör en spawncount check på npc med det namnet inom viss
	range så vet vi om en mobb e snarad av en sk så skippar druiden autosnaring

 - end nuke loop efter att man zonat

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

#define HEAL_ONLY_MODE			0						| heal only mode = only does heal tank & snare mob
#define USE_CH							0

#define DO_SNARE						0						| shall i snare mobs? even snares in HEAL_ONLY_MODE
#define DO_DEBUFF						1						| debuff will be cast once per mob, not in heal only mode
#define DO_EPIC							1						| click epic 1.5/2.0 when it is ready? never do epic in HEAL_ONLY_MODE
#define DO_AA_DS						1						| Auto cast Wrath of the Wild anytime it is up. First on yourself, if you already have the buff, cast it on MA (auto OFF in HEAL_ONLY_MODE)

#define DO_REPTILE					1						| casts skin of the reptile on tank at beginning of fight, good for monk tanks :)
#define DO_REPTILE_PLAYER		Keosden

| Percent levels
#define QHEAL_PERCENT				35					| todo: om vi har clr i gruppen, kör på t.ex 40%, annars på t.ex 70%
#define CHEAL_PERCENT				85
#define SNARE_PERCENT				99
#define DEBUFF_PERCENT			97
#define STARTNUKE_PERCENT		95
#define LOM_PERCENT					30					| mana % limit, dont nuke below this limit


| Will use the first one if memorized when script starts, else the other
#define NUKE_SPELLNAME_COLD			"Rime Crystals Rk. II"
#define DEBUFF_SPELLNAME_COLD		"Icefall Breath Rk. II"

#define NUKE_SPELLNAME_FIRE			"Equinox Burn Rk. II"
#define DEBUFF_SPELLNAME_FIRE		"Blistering Sunray Rk. II"

#define SNARE_SPELLNAME					"Serpent Vines"
#define EPIC_ITEMNAME						"Staff of Everliving Brambles"			| "Staff of Living Brambles"

#define CHEAL_SPELLNAME					"Karana's Renewal"									| Only used in HEAL_ONLY_MODE
#define QHEAL_SPELLNAME					"Pure Life Rk. II"
#define QHEAL2_SPELLNAME				"Adrenaline Surge Rk. II"						| even faster qheal spell, used if player % is low & spell is ready

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
	/if (${Me.Class.Name.Equal[Druid]}) /call DebugMsg "DEBUFF: ${Target.CleanName} (FIRE, AC)"
/return

#event DEBUFF_DruCold "#1#s body is covered in a film of ice."
Sub Event_DEBUFF_DruCold(string Line, string Name)
	| DRU/67: Glacier Breath
	| DRU/72: Icefall Breath
	/if (${Me.Class.Name.Equal[Druid]}) /call DebugMsg "DEBUFF: ${Target.CleanName} (COLD)"
/return





Sub DruSnareMob(int SpawnId)

	/target npc id ${SpawnId}
	/delay 2s ${Target.ID}==${SpawnId}

	/if (${Ini[INI_FILE, ImmuneSnare_${Zone.ShortName}, ${Target.CleanName}, NOT_FOUND]}) {
		/call DebugMsg "IMMUNESNARE: ${Target.CleanName} (learned previously)"
		/goto :DoneSnaring
	}

	/if (${Ini[INI_FILE, DontRun_${Zone.ShortName}, ${Target.CleanName}, NOT_FOUND]}) {
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

		/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME" "QHEAL2_SPELLNAME"
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
				/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME" "QHEAL2_SPELLNAME"
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
				/call DebugMsg "Can't snare, mob is oor/no LoS!"
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
		| Dont spam heal on inc and such
		/if (${Target.PctHPs} < 98) /call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME" "QHEAL2_SPELLNAME"
		/goto :Loop
	}
	
	/if (${oldid}) {
		/target id ${oldid}
		/delay 2s ${Target.ID}==${oldid}
	}

/return


Sub HandleAssistCallDruid(int SpawnId)

	/if (!${SpawnId}) /return

	| global druid variables:
	/if (!${Defined[reptileTimer]}) /declare reptileTimer timer outer 0

	/declare tankId int local 0
	/declare tankName string local

	/declare nukeName string local
	/declare debuffName string local

	/if (!HEAL_ONLY_MODE) {
		/if (${Me.Gem[NUKE_SPELLNAME_COLD]}) {
			/varset nukeName NUKE_SPELLNAME_COLD
		} else /if (${Me.Gem[NUKE_SPELLNAME_FIRE]}) {
			/varset nukeName NUKE_SPELLNAME_FIRE
		} else {
			/echo NO NUKE MEMORIZED!
			/endmacro
		}
	
		/if (${Me.Gem[DEBUFF_SPELLNAME_COLD]}) {
			/varset debuffName DEBUFF_SPELLNAME_COLD
		} else /if (${Me.Gem[DEBUFF_SPELLNAME_FIRE]}) {
			/varset debuffName DEBUFF_SPELLNAME_FIRE
		} else {
			/echo NO DEBUFF MEMORIZED!
			/endmacro
		}
	}

	/declare zoneId int local ${Zone.ID}
	
	/call FindTank
	/varset tankId ${Macro.Return}
	/varset tankName ${Spawn[id ${tankId}].Name}

	/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME" "QHEAL2_SPELLNAME"

	/if (!HEAL_ONLY_MODE) /target npc id ${SpawnId}

	| /call DebugMsg "Assisting on ${Target.Name} (${Me.PctMana}m)"

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

	/if (DO_DEBUFF && !HEAL_ONLY_MODE && ${Target.Type.Equal[NPC]} && ${Me.Standing} && (${Target.PctHPs} >= 80) && (${Me.PctMana} > LOM_PERCENT) && (${Spawn[id ${SpawnId}].Level} >= 62)) {
		/call DruidWaitForTargetHP ${SpawnId} DEBUFF_PERCENT
		/call WaitForSpellReady "${debuffName}"
		/call DebugMsg "Debuffing ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)"
		/call BotCast "${debuffName}" NukeCallback
		/doevents
	}

	/if (DO_SNARE) {
		/call DruidWaitForTargetHP ${SpawnId} SNARE_PERCENT
		/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME" "QHEAL2_SPELLNAME"
		/call DruSnareMob ${SpawnId}
	}

	/if (!HEAL_ONLY_MODE && DO_AA_DS) {
		/call DruidRefreshAA_DS
	}

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

		/if (${Zone.ID} != ${zoneId}) {
			/call DebugMsg "Stopping loop, zoned"
			/return
		}

		/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME" "QHEAL2_SPELLNAME"

		/if (DO_REPTILE && !${reptileTimer} && ${Me.SpellReady["Skin of the Reptile"]} && ${Spawn[pc DO_REPTILE_PLAYER].Distance} < 100) {
			/varset reptileTimer 90s
			/target pc DO_REPTILE_PLAYER
			/delay 10
			/call DebugMsg "REPTILE > ${Target.Name} < (${Me.PctMana}m)"
			/call BotCast "Skin of the Reptile" NukeCallback
		}

		/if (HEAL_ONLY_MODE && USE_CH && !${Window[LootWnd].Open} && ${Target.Name.Equal[${tankName}]} && (${Target.PctHPs} <= CHEAL_PERCENT) && (${Target.Distance} < 100)) {
			/if (${Target.Name.NotEqual[${tankName}]}) {
				/target pc ${tankName}
				/delay 5
			}
			/call WaitForSpellReady "CHEAL_SPELLNAME"
			/call DebugMsg "CH > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)"
			/call BotCast "CHEAL_SPELLNAME" HealCallback
		}

		/if (DO_EPIC && !HEAL_ONLY_MODE && !${Window[LootWnd].Open} && !${Me.Stunned} && ${Me.Standing} && !${FindItem[EPIC_ITEMNAME].Timer} && ${Target.ID} && (${Target.PctHPs} > 50) && (${Target.PctHPs} < 100) && ${Target.Type.Equal[NPC]} && (${Target.Level} >= 60) && ${Target.LineOfSight} && ${Target.Distance} < 100) {
			/call DebugMsg "DEBUFF: ${Target.CleanName} (Druid Epic)"
			/call MQ2Cast "EPIC_ITEMNAME" item
		}
		
		/if (!HEAL_ONLY_MODE && DO_AA_DS) /call DruidRefreshAA_DS

		/if (!HEAL_ONLY_MODE && (${Target.ID} != ${SpawnId}) && ${Spawn[npc id ${SpawnId}].ID} && ${Spawn[npc id ${SpawnId}].LineOfSight}) {
			/target npc id ${SpawnId}
			/delay 2s ${Target.ID}==${SpawnId}
		}

		/if (!HEAL_ONLY_MODE && !${Window[LootWnd].Open} && ${Me.Standing} && ${Zone.ShortName.NotEqual["Frostcrypt"]} && !${FindItem["Everliving Bramble Tunic"].Timer} && ${Spawn[npc id ${SpawnId}].LineOfSight} && (${Spawn[npc id ${SpawnId}].Distance} < 200)) {
			/call DebugMsg "Casting nuke clicky"
			/call MQ2Cast "Everliving Bramble Tunic" item
		}

		/if (!HEAL_ONLY_MODE && !${timerNuke} && ${Me.Standing} && !${Window[LootWnd].Open} && !${Window[SpellBookWnd].Open} && ${Spawn[npc id ${SpawnId}].ID} && ${Spawn[npc id ${SpawnId}].LineOfSight} && (${Me.TargetOfTarget.ID} != ${Me.ID})) {
			/target npc id ${SpawnId}
			/delay 2s ${Target.ID}==${SpawnId}
			/call WaitForSpellReady "${nukeName}"
			/if (${Target.ID} && ${Target.LineOfSight} && !${Me.Moving} !${Me.Stunned} && ${Target.Type.Equal[NPC]} && (${Target.Distance} < ${Me.Gem[${Me.Gem[${nukeName}]}].Range}) && (${Me.PctMana} > LOM_PERCENT)) {
				/call DruidWaitForTargetHP ${SpawnId} STARTNUKE_PERCENT
				/call DebugMsg "Nuking ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)"
				/call BotCast "${nukeName}" NukeCallback
				/varset timerNuke 5s
			}
		}

		/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME" "QHEAL2_SPELLNAME"

		/if (!${Me.Moving} && ${Me.Standing} && !${Window[LootWnd].Open} && (${Me.PctMana} < LOM_PERCENT) && !${Me.Buff["Summon Drogmor"].ID}) {
			/if (${Spawn[npc radius 40 notid ${SpawnId}].LineOfSight}) {							
				/call DebugMsg "LOM (${Me.PctMana}m) - But ${SpawnCount[npc radius 40 notid ${SpawnId}]} adds in camp!"
				/delay 1s
			} else {
				/call DebugMsg "LOM - Medding (${Me.PctMana}m)"
				/varset timerNuke 2m
				/if (${Me.Standing} && !${Me.Moving} && !${Me.Casting.ID}) /sit
			}
		}

	/goto :DruidLoop

	:DruidDone
	| heal group members below 40% at end of fight
	| fixme: only call if i am in HEAL MODE
	/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME" "QHEAL2_SPELLNAME"
	
	/call UpdateBuffs
	/call HandleEndOfFightCaster
/return


Sub DruidUpdateBuffs

	/declare oldSpell string local ${Me.Gem[7]}

	| Level 75 druid buffs
	/if (${Me.SpellReady["Second Life Rk. II"]}) /call RefreshBuff "Second Life Rk. II" 10
	
	| Clear debuffs:
	/if (${Me.AltAbilityReady["Resplendent Cascade"]} && (${Me.Buff["Malo"].ID} || ${Me.Buff["Wind of Tashanian"].ID} || ${Me.Buff["Poxed Web"].ID} || ${Me.Buff["Malis Rk. III"].ID})) {
		/call DebugMsg "Curing debuffs"
		/aa act Resplendent Cascade
		|3.5 sec cast time
		/delay 40
	}
	
	/if (DO_AA_DS) /call DruidRefreshAA_DS

	/call RefreshBuff "Mask of the Wild" 50
	/call RefreshBuff "Viridicoat Rk. II" 50

	/if (${Group.Members}) {
		/call RefreshBuff "Blessing of the Direwild Rk. II" 50
		/if (!${Me.Song["Aura of Life Effect"].ID}) /call RefreshBuff "Aura of Life" 0
		/if (!${Raid.Members} && !HEAL_ONLY_MODE) {
			/if (${Me.Gem["Legacy of Viridiflora Rk. II"]}) /call RefreshBuff "Legacy of Viridiflora Rk. II" 50
			/if (${Me.Gem["Blessing of Oak"]}) /call RefreshBuff "Blessing of Oak" 50
		}
	}

	/if (!${Raid.Members} && !${Spawn[npc radius 60].LineOfSight}) {
		/call RefreshBuff "Brazier of Endless Flame" 10
	}

	| Clickies
	/call RefreshBuff "Earring of Pain Deliverance" 10
	/call RefreshBuff "Orb of Duskmold" 10

	| Pet buffs
	/if (${Me.Pet.ID} && ${Me.Pet.CleanName.NotEqual["${Me.Name}`s familiar"]}) {
		/if (!${Me.PetBuff["Savage Spirit"]}) {
			/call DebugMsg "Refreshing pet haste"
			/call MQ2Cast "Sand Eroded Shell" item
		}
		/call RefreshPetBuff "Mammoth's Strength" 50
	}
	

	/if (${Me.Gem[7].Name.NotEqual["${oldSpell}"]}) {
		/delay 2s
		/memspell 7 "${oldSpell}"
		| /echo Restoring old spell gem
	}

	/if (!HEAL_ONLY_MODE && DO_AA_DS) /call DruidRefreshAA_DS
	
	| /squelch /target clear

/return

Sub DruidRefreshAA_DS

	/if (!${Me.AltAbilityReady["Wrath of the Wild"]} || ${Me.Moving} || ${Me.Sitting} || ${Me.Casting.ID} || ${Target.ID} || ${Window[LootWnd].Open}) /return

	/declare tankId int local 0

	/if (!${Me.Buff["Wrath of the Wild"].ID} && !${Raid.Members}) {
		/call DebugMsg "Buffing self with AA DS"
		/target myself
		/casting "Wrath of the Wild" alt
		/delay 1s
	} else {
		/call FindTank
		/varset tankId ${Macro.Return}
		/if (${tankId}) {
			/if (${Spawn[id ${tankId}].Distance} < 100) {
				/call DebugMsg "Buffing ${Spawn[id ${tankId}].Name} with AA DS"
				/target id ${tankId}
				/casting "Wrath of the Wild" alt
				/delay 1s
			}
		}
	}
/return