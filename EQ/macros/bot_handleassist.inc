#include MQ2Cast_Spell_Routines.inc

/if (${Me.Class.Name.Equal[Druid]} {
	#include bot_handleassist_druid.inc
}

/if (${Me.Class.Name.Equal[Magician]} {
	#include bot_handleassist_mage.inc
}

#include bot_spells.inc


| #include bot_handleassist_cleric.inc
| #include bot_handleassist_enchanter.inc
| #include bot_handleassist_warrior.inc
| #include bot_handleassist_beastlord.inc
| #include bot_handleassist_shadowknight.inc
| #include bot_handleassist_wizard.inc


Sub DebugMsg(string Line)

	/echo ## ${Line}
	|/i msg IRC_CHANNEL ${Line}

/return

#event HANDLE_BotAssist "[MQ2] botassist #1#"
Sub Event_HANDLE_BotAssist(string Line, int spawnId)
	/if (${spawnId}) {
		/call HandleAssistCall ${spawnId}
	}
/return

#event HANDLE_Summoned	"You have been summoned!"
Sub Event_HANDLE_Summoned(string Line)
	/if (${Me.Casting.ID}) {
		/call DebugMsg "# I was summoned! - Ducking spell"
		/keypress d
		/delay 1
		/keypress d
	} else {
		/if (${Target.Distance} < 1) {
			|Moves back enough to do melee damage
			/call DebugMsg "# I was summoned - backing out"
			/keypress back hold
			/timed 1 /keypress back
		} else {
			/call DebugMsg "# I was summoned!"
		}
	}
	/varset timerNuke 5s
/return


#Event HANDLE_MobInterrupted "#1#'s casting is interrupted!"
Sub Event_HANDLE_MobInterrupted(string Line, string Name)

	/if (${Spawn[${Name}].Type.Equal["NPC"]}) {
		/echo # ${Name} [${Spawn[npc ${Name}].Class.ShortName}] interrupted
	}

/return

Sub WaitForTargetHP(int pct)
	/call DebugMsg "# Debug: I called WaitForTargetHP but shouldnt use it!"

	:Loop
	/if (!${Target.ID}) {
		/return
	}
	/if (${Target.PctHPs} > ${pct}) {
		/doevents
		/goto :Loop
	}

/return


#event DEBUFF_FailedSnare			"Your target is immune to changes in its run speed."
Sub Event_DEBUFF_FailedSnare(string Line)
	/varset mobSnareImmune 1
/return


#event DEBUFF_NotTakeHold			"Your spell did not take hold."
Sub Event_DEBUFF_NotTakeHold(string Line)
	/varset mobNotTakeHold 1
	/varset AbortLoop 1
	/call DebugMsg "note: not take hold on ${Target.CleanName}"
/return

Sub Init_HandleAssist

	/declare bot_handleassist_loaded		int	outer	1

	/declare timerSnareCounter				timer outer 0
	/declare timerSlowCounter				timer outer 0

	/declare mobIsSlowed					int outer	0
	/declare mobIsPartialSlowed				int outer	0
	/declare mobResistedSlow				int outer	0
	/declare mobIsSnared					int outer	0
	/declare mobSnareImmune					int outer	0
	/declare mobNotTakeHold					int outer	0
	
	|spawnid, time mezzed
	/declare mobMezzedID[10]				int outer	0
	/declare mobMezzedTime[10]				string outer 0
	
	/declare timerDruidEpic					timer outer 0
	/declare timerNuke 							timer outer 0

	
	/squelch /alias /botassist /echo botassist
	
	/echo # handleassist initialized	

/return

Sub HandleEndOfFightCaster

	/if (${Me.Standing} && (${Me.PctMana} < 85) && !${Me.Buff["Summon Drogmor"].ID} && !${Me.Buff["Summon Horse"].ID}) {

		/delay 20

		|/declare ZoneOutdoors int local ${Ini[ZONEINFO_FILE, ZoneInfo, ${Zone.ShortName}_Outdoors, NOT_FOUND]}
		|/declare ZoneNoMount int local ${Ini[ZONEINFO_FILE, ZoneInfo, ${Zone.ShortName}_NoMount, NOT_FOUND]}
		|/if (!${Me.Moving} && (${ZoneOutdoors} == 1) && (${ZoneNoMount} == 0)) {
		|		/call DebugMsg "# Summoning mount (${Me.PctMana}m)"
		|		/call MQ2Cast "${mcHUDmount}" item
		|   /return
		|}

		/if (${Spawn[radius 80 npc].LineOfSight}) {
			/call DebugMsg "# Can't med, mobs nearby (${Me.PctMana}m)"
		} else {
			/if (${Me.Standing} && !${Me.Moving} && !${Me.Casting.ID}) {
				/call DebugMsg "# Medding (${Me.PctMana}m)"
				/sit
			}
		}
	}

/return

#event HANDLE_MOB_GATING "#1# begins to cast the gate spell."
Sub Event_HANDLE_MOB_GATING(string line, string Name)

	/if (${Spawn[${Name}].Type.Equal["NPC"]}) {
		/call DebugMsg "# Warning: ${Name} gating!!!"
		/call AttemptToStun "${Name}"
	}

/return

#event HANDLE_MOB_HEALING "#1# begins to cast a spell. <Complete Healing>"
Sub Event_HANDLE_MOB_HEALING(string Line, string Name)

	/if (${Spawn[${Name}].Type.Equal["NPC"]}) {
		/call DebugMsg "# Warning: ${Name} casting CH!"
		/call AttemptToStun "${Name}"
	}

/return

|SpawnId is the id of the mob which we are trying to kill atm
Sub HandleAssistCall(int SpawnId)

	/if (!${bot_handleassist_loaded}) /call Init_HandleAssist

	| Check if target is listed as mob to ignore
	/if (${Ini["bot_ignoremobs.ini", ${Zone.ShortName}, ${Spawn[id ${SpawnId}].CleanName}, 0]}) {
		| /echo IGNORING ${Spawn[id ${SpawnId}].CleanName} - MOB WHITELISTED
		/return
	}

	/echo Killing >> ${Spawn[id ${SpawnId}].CleanName} <<

	/if (${Me.Sitting}) {
		|standing up on inc
		:StandLoop
		/if (${Spawn[id ${SpawnId}].Distance} > 100) /goto :StandLoop

		/delay ${Math.Calc[${Math.Rand[10]}+1]}
		/if (${Me.Sitting} && !${Me.Invis}) {
			/call DebugMsg "# Standing, mob distance: ${Spawn[id ${SpawnId}].Distance}"
			/stand
		}
	}

	/if (${Me.Class.Name.Equal["Druid"]}) {
		/call HandleAssistCallDruid ${SpawnId}
		/return
	}

	/if (${Me.Class.Name.Equal["Cleric"]}) {
		/call HandleAssistCallCleric ${SpawnId}
		/return
	}

	/if (${Me.Class.Name.Equal["Enchanter"]}) {
		/call HandleAssistCallEnchanter ${SpawnId}
		/return
	}

	/if (${Me.Class.Name.Equal["Warrior"]}) {
		/call HandleAssistCallWarrior ${SpawnId}
		/return
	}

	/if (${Me.Class.Name.Equal["Beastlord"]}) {
		/call HandleAssistCallBeastlord ${SpawnId}
		/return
	}

	/if (${Me.Class.Name.Equal["Shadow Knight"]}) {
		/call HandleAssistCallShadowKnight ${SpawnId}
		/return
	}
	
	/if (${Me.Class.Name.Equal["Wizard"]}) {
		/call HandleAssistCallWizard ${SpawnId}
		/return
	}

	/if (${Me.Class.Name.Equal["Magician"]}) {
		/call HandleAssistCallMage ${SpawnId}
		/return
	}

	/call DebugMsg "# FIXME: i cant handle assist for ${Me.Class}"
/return


Sub FindTank
	| Tries to figure out who is the tank

	/declare i int local 0

	/declare tankId int local 0
	/declare tankLvl int local 0

	| 1. find the closest tank class in group
	/for i 1 to 5
		/if ((${Group.Member[${i}].Distance} < 300) && !${Group.Member[${i}].State.Equal["DEAD"]} && (${Group.Member[${i}].Level} >= ${tankLvl}) && (${Group.Member[${i}].Class.Name.Equal["Warrior"]} || ${Group.Member[${i}].Class.Name.Equal["Shadow Knight"]} || ${Group.Member[${i}].Class.Name.Equal["Paladin"]})) {
			/varset tankId ${Group.Member[${i}].ID}
			/varset tankLvl ${Group.Member[${i}].Level}
		}
	/next i
	/if (${tankId}) /return ${tankId}

	| 2. find any tank nearby 
	/varset tankId ${Spawn[pc tank radius 200].ID}
	/if (${Spawn[id ${tankId}].Type.Equal[Aura]} || ${Spawn[id ${tankId}].Type.Equal[Mount]}) /return 0

	/return ${tankId}

/return


Sub UpdateBuffs

	/call CheckNeutralZone
	/if (${Macro.Return}) /return
	/if (${Me.Moving} || ${Me.Invis} || ${Me.State.Equal["HOVER"]}) /return

	/declare wasSitting int local 0
	/if (${Me.Sitting}) /varset wasSitting 1
	
	/if (${Me.Class.Name.Equal[Druid]}) {
		/call DruidUpdateBuffs
		/goto :BuffsDone
	}

	/if (${Me.Class.Name.Equal[Cleric]}) {
		/call ClericUpdateBuffs
		/goto :BuffsDone
	}

	/if (${Me.Class.Name.Equal[Enchanter]}) {
		/call EnchanterUpdateBuffs
		/goto :BuffsDone
	}

	/if (${Me.Class.Name.Equal[Beastlord]}) {
		/call BeastlordUpdateBuffs
		/goto :BuffsDone
	}

	/if (${Me.Class.Name.Equal["Shadow Knight"]}) {
		/call ShadowKnightUpdateBuffs
		/goto :BuffsDone
	}

	/if (${Me.Class.Name.Equal["Wizard"]}) {
		/call WizardUpdateBuffs
		/goto :BuffsDone
	}

	/if (${Me.Class.Name.Equal["Magician"]}) {
		/call MageUpdateBuffs
		/goto :BuffsDone
	}

	/call DebugMsg "# I miss a buff function for class ${Me.Class.Name}"

	:BuffsDone

	/if (${Me.Standing} && ${wasSitting} && !${Me.Moving}) /sit

/return

| fixme: make this a ini section?
Sub CheckNeutralZone
	/if (${Zone.Name.Equal["The Plane of Knowledge"]} || ${Zone.Name.Equal["The Plane of Tranquility"]} || ${Zone.Name.Equal["The Nexus"]} || ${Zone.Name.Equal["The Bazaar"]} || ${Zone.Name.Equal["Guild Lobby"]} || ${Zone.Name.Equal["Guild Hall"]}) /return 1
/return 0


