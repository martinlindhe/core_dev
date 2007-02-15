#define SLOW_SPELL		"Forlorn Deeds"

#define NUKE_SPELL		"Insanity"

#define DO_MEZZ				0


#event DEBUFF_EncForlornDeeds				"#1# slows down."
Sub Event_DEBUFF_EncForlornDeeds(string Line, string Name)
	|ENC/57: Forlorn Deeds
	/if (${Me.Class.Name.Equal[Enchanter]}) {
		/if (${Target.ID} && ${timerSlowCounter}) {
			/varset mobIsSlowed 1
		}
	} else {
		|/echo ${Name} chanter slowed
	}
/return

#event DEBUFF_EncForlornDeedsFailed			"Your target resisted the Forlorn Deeds spell." 
Sub Event_DEBUFF_EncForlornDeedsFailed(string Line)
	/varset mobResistedSlow 1
/return

|fixme: other spells with this message?
#event DEBUFF_EncHalfSlowed					"Your spell was mostly successful." 
Sub Event_DEBUFF_EncHalfSlowed(string Line)
	/if (${Me.Class.Name.Equal[Enchanter]}) {
		/if (${Target.ID} && ${timerSlowCounter}) {
			/varset mobIsPartialSlowed 1
		}
	}
/return

#event DEBUFF_EncBliss						"#1#'s jaw falls as they begin to drool."
Sub Event_DEBUFF_EncBliss(string Line, string Name)
	|ENC/64: Bliss
	/if (${Me.Class.Name.Equal[Enchanter]}) {
		|fixme: skapa en lista med current mezzed spawns så chantern kan auto-varna i irc när mez håller på att fada
		/i msg IRC_CHANNEL MEZZED: ${Name} (${Target.ID}) 0.9 min
		/if (${Name.Equal[${Target.CleanName}]}) {
			|/g MEZZED > ${Target.CleanName} <
		} else {
			/i msg IRC_CHANNEL BLABLALBLALBLA: mezzed with not same name as target... detta ska inte hända! (om du inte spelar manuellt)
			/g MEZZED > ${Name} <
		}

		/declare i int local 0

		/for i 1 to 10
			/if (!${mobMezzedID[${i}]}) {
				/if (${mobMezzedID[${i}]} == ${Target.ID}) {
					|update on re-mez
					/i msg IRC_CHANNEL # Updating mob mezzed info in slot ${i}
					/varset mobMezzedTime[${i}] ${Time.Time24}
				} else {
					/i msg IRC_CHANNEL # Saving mob mezzed info in slot ${i}
					/varset mobMezzedID[${i}] ${Target.ID}
					|0.9 minuter på bliss = 54 sekunder
					/varset mobMezzedTime[${i}] ${Time.Time24}
					/return
				}
			}
		/next i

	}
/return

#event DEBUFF_EncBlissFailed			"Your target resisted the Bliss spell." 
Sub Event_DEBUFF_EncBlissFailed(string Line)
	|/i msg IRC_CHANNEL # RESISTED MEZ: ${Target.CleanName} (${Target.ID})
	/g RESISTED MEZZ > ${Target.CleanName} < HELP!!
/return

#event DEBUFF_EncHowlOfTashan				"#1# glances nervously about."
Sub Event_DEBUFF_EncHowlOfTashan(string Line, string Name)
	|ENC/61: Howl of Tashan
	/if (${Me.Class.Name.Equal[Enchanter]}) {
		/if (${Target.ID}) {
			/i msg IRC_CHANNEL TASHED: ${Target.CleanName} (${Target.ID})
			|/g TASHED > ${Target.CleanName} <
		}
	}
/return

Sub EncSlowMob(int SpawnId)

	|/i msg IRC_CHANNEL # starting slow loop
	:StartSlow
	/varset mobResistedSlow 0
	/varset mobIsSlowed 0
	/varset mobIsPartialSlowed 0
	/varset AbortLoop 0

	:SlowLoop
	/target npc id ${SpawnId}
	/doevents
	/delay 10

	/if (!${Target.ID}) {
		/i msg IRC_CHANNEL # Lost target, aborting slow
		/goto :DoneSlowing
	}

	/if (${Target.LineOfSight} && ${Target.Distance} < ${Me.Gem[${Me.Gem["SLOW_SPELL"]}].Range} ) {
		/if (${Target.Type.NotEqual[NPC]}) {
			/i msg IRC_CHANNEL # Aborting on target, not a npc!
			/goto :DoneSlowing
		}
		/call WaitForSpellReady "SLOW_SPELL"
		/i msg IRC_CHANNEL # Attempting to slow ${Target.CleanName} (${Target.ID})
		/varset timerSlowCounter 200
		/call Cast "SLOW_SPELL"
		/doevents
		
		/if (!${timerSlowCounter} || ${mobIsSlowed} || ${mobIsPartialSlowed} || ${mobResistedSlow}) {
			/if (${mobIsPartialSlowed}) {
				/i msg IRC_CHANNEL PARTIALLY SLOWED: ${Target.CleanName} (${Target.ID})
				|/g PARTIALLY SLOWED > ${Target.CleanName} <
				/goto :DoneSlowing
			}
			/if (${mobIsSlowed}) {
				/i msg IRC_CHANNEL SLOWED: ${Target.CleanName} (${Target.ID})
				|/i msg IRC_CHANNEL SLOWED: ${Target.CleanName} (${Target.ID}) 3.5 min
				|/g SLOWED > ${Target.CleanName} <
				/goto :DoneSlowing
			}
			/if (${mobResistedSlow}) {
				/i msg IRC_CHANNEL RESISTED SLOW: ${Target.CleanName}
				|/g RESISTED SLOW > ${Target.CleanName} <
				/goto :StartSlow
			} else {
				/i msg IRC_CHANNEL Slow timer ran out!?!?! This shall never trigger!
				/goto :DoneSlowing
			}
		}
		/goto :SlowLoop

	} else {
		/if (${Target.ID} && !${AbortLoop}) {
			/i msg IRC_CHANNEL # Can't slow, mob is oor/no LoS!
			/doevents
			/delay 3s
			/goto :SlowLoop
		} else {
			/goto :DoneSlowing
		}
	}

	:DoneSlowing
	|/i msg IRC_CHANNEL # ending slow loop
/return

Sub EncHandleAdds(int SpawnId)

	/declare AddId int local 0
	/declare PetId int local 0
	/declare i int local 0

	|Show list of currently mezzed mobs, and remove obsolete entries
	|Todo: Clean list upon zoning!
	/for i 1 to 10
		/if (${mobMezzedID[${i}]}) {
			/if (${Spawn[npc id ${mobMezzedID[${i}]}].ID} || ${Spawn[pet id ${mobMezzedID[${i}]}].ID}) {
				/i msg IRC_CHANNEL # Mob ID ${mobMezzedID[${i}]} mezzed at ${mobMezzedTime[${i}]}, now: ${Time.Time24}
			} else {
				|fixme: also clean up > 24 sec old timers
				|clean up
				/i msg IRC_CHANNEL # debug: removing Mob ID ${mobMezzedID[${i}]} from mez list (dead/despawned)
				/varset mobMezzedID[${i}] 0
				/varset mobMezzedTime[${i}]
			}
		}
	/next i

	|todo here: check if found add is already mezzed, and when

	|Check if there is more than one NPC nearby
	|This code will auto mez one add within 50 range
	/varset AddId ${Spawn[radius 50 notid ${SpawnId} npc].ID}
	/if (${AddId}) {
		/target npc id ${AddId}
		/delay 10
		/i msg IRC_CHANNEL # ADD! Attempting to mez ${Target.CleanName} (${AddId})
		/if (${Target.ID} == ${AddId}) {
			
			/declare lookup int local ${Ini[INI_FILE, DontMez_${Zone.ShortName}, ${Target.CleanName}, NOT_FOUND]}
			/if (${lookup}) {
				/i msg IRC_CHANNEL DONTMEZ: ${Target.CleanName} (from INI)
				/return
			}			
			/call WaitForSpellReady "Bliss"
			/call Cast "Bliss"
			/doevents
			|fixme: trigga event om spellen resistades och spamma irc
		}
	}

	|Check if SpawnId has a pet, in that case mez it too
	/varset PetId ${Spawn[npc id ${SpawnId}].Pet.ID}
	/if (${PetId}) {
		/target pet id ${PetId}
		/delay 10
		/i msg IRC_CHANNEL # ADD! Attempting to mez PET ${Target.CleanName} (${PetId})
		/if (${Target.ID} == ${PetId}) {
			/call WaitForSpellReady "Bliss"
			/call Cast "Bliss"
			/doevents
			|fixme: trigga event om spellen resistades och spamma irc
		}
	}

/return

Sub EnchanterWaitForTargetHP(int pct)

	/declare SpawnId int local ${Target.ID}

	:Loop
	/doevents
	/if (!${SpawnId}) {
		/return
	}
	|If normal rune has faded during fight, and im getting hit, fire off eldritch rune if it is up
	/if (!${Me.Buff["Arcane Rune"].ID} && (${Me.PctHPs} < 99)) {
		/if (${Me.AltAbilityReady["Eldritch Rune"]}) {
			/i msg IRC_CHANNEL # EEP! Using Eldritch Rune!
			/aa act Eldritch Rune
		}
	}

	
	/if (${Target.PctHPs} > ${pct}) {
		/call EncHandleAdds ${SpawnId}
		/goto :Loop
	}

/return

Sub HandleAssistCallEnchanter(int SpawnId)

	/if (!${SpawnId}) {
		/return
	}

	/declare zoneId int local ${Zone.ID}

	|/target npc id ${SpawnId}
	|/i msg IRC_CHANNEL # Assisting on ${Target.CleanName} (${Me.PctMana}m)
	
	/target npc id ${SpawnId}

	/if (${Target.PctHPs} > 20) {
		|Dont bother tashing mobs below 20%
		/call EnchanterWaitForTargetHP 96
		|Dont bother slowing mobs below 20% hp
		/call WaitForSpellReady "Howl of Tashan"
		|/i msg IRC_CHANNEL # Tashing ${Target.CleanName}
		/call Cast "Howl of Tashan"
		/doevents
	}

	/if (${Target.PctHPs} > 20) {
		|Dont bother slowing mobs below 20% hp
		/call EnchanterWaitForTargetHP 93
		/call EncSlowMob ${SpawnId}
	}

	/call EnchanterWaitForTargetHP 79
	/varset AbortLoop 0
	:Loop
		/doevents
		/if (${Zone.ID} != ${zoneId}) {
			/i msg IRC_CHANNEL # Stopping loop, zoned
			/return
		}
		/if (DO_MEZZ) {
			/call EncHandleAdds ${SpawnId}
		}
		/target npc id ${SpawnId}
		/doevents

		/if (${Target.Type.NotEqual[NPC]}) {
			/i msg IRC_CHANNEL # Got wrong target: ${Target.CleanName} (not a npc!)
			/delay 5
			/goto :Loop
		}
		/if (${AbortLoop}) {
			/goto :Done
		}
		/if (${Me.PctMana} < 40) {
			/i msg IRC_CHANNEL LOM - Medding (${Me.PctMana}m)
			/goto :Done
		}
		/if (${Me.TargetOfTarget.ID} == ${Me.ID}) {
			/i msg IRC_CHANNEL # Got aggro, waiting
			/delay 10s
		}

		/call WaitForSpellReady "NUKE_SPELL"
		/doevents
		/if (${AbortLoop}) {
			/i msg IRC_CHANNEL # Aborting nukage!
			/goto :Done
		}
		
		/target npc id ${SpawnId}
		/doevents
		/delay 10
		/if (${Target.ID}) {
			/if (${Target.PctHPs} > 5) {
				/i msg IRC_CHANNEL # Nuking ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)
				/call Cast "NUKE_SPELL"
				/delay 10s
			} else {
				/i msg IRC_CHANNEL # Ending nuking (mob ${Target.PctHPs}% hp) (${Me.PctMana}m)
				/goto :Done
			}
		} else {
			/if (!${Spawn[npc id ${SpawnId}].ID}) {
				/if (DO_MEZZ) {
					/call EncHandleAdds ${SpawnId}
				}
				/i msg IRC_CHANNEL # Ending nuking (mob dead) (${Me.PctMana}m)
				/goto :Done
			}			
			/goto :Done
		}
	/goto :Loop

	:Done

	/call UpdateBuffs
	/call HandleEndOfFightCaster

/return

Sub EnchanterUpdateBuffs
	/declare oldSpell string local ${Me.Gem[7]}

	/target myself

	|Level 65 enchanter buffs
	/call RefreshBuff "Arcane Rune" 50
	/call RefreshBuff "Shield of Maelin" 100
	/call RefreshBuff "Voice of Quellious" 50
	/call RefreshBuff "Vallon's Quickening" 50
	/call RefreshBuff "Guard of Druzzil" 40

	/if (${Me.Gem[7].Name.NotEqual["${oldSpell}"]}) {
		/delay 2s
		/echo # Restoring old spell gem
		/memspell 7 "${oldSpell}"
	}

/return