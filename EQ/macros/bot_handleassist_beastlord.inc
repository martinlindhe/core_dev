#define SLOW_SPELL		"Sha's Revenge"
#define DO_SLOW				1

#define NUKE_SPELL		"Glacier Spear"

#define DO_NUKE				0

#event DEBUFF_BstShasRevenge				"#1# loses their fighting edge."
Sub Event_DEBUFF_BstShasRevenge(string Line, string Name)
	|BST/65: Sha's Revenge
	/if (${Me.Class.Name.Equal[Beastlord]}) {
		/if (${Target.ID} && ${timerSlowCounter}) {
			/varset mobIsSlowed 1
		}
	} else {
		|/echo ${Name} chanter slowed
	}
/return

#event DEBUFF_BstShasRevengeFailed			"Your target resisted the Sha's Revenge spell." 
Sub Event_DEBUFF_BstShasRevengeFailed(string Line)
	/varset mobResistedSlow 1
/return

|fixme: other spells with this message?
#event DEBUFF_BstHalfSlowed					"Your spell was mostly successful." 
#event DEBUFF_BstHalfSlowed					"Your spell was partially successful." 
Sub Event_DEBUFF_BstHalfSlowed(string Line)
	/if (${Me.Class.Name.Equal[Beastlord]}) {
		/if (${Target.ID} && ${timerSlowCounter}) {
			/varset mobIsPartialSlowed 1
		}
	}
/return

|fixme: this is basically identical to enchanter version, merge into one function
Sub BstSlowMob(int SpawnId)

	/declare failedSlowCount int local 0

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
	
	/if (${AbortLoop}) {
		/i msg IRC_CHANNEL # Slow loop aborted
		/goto :DoneSlowing
	}

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
				/varcalc failedSlowCount ${failedSlowCount}+1
				/i msg IRC_CHANNEL RESISTED SLOW: ${Target.CleanName} (try ${failedSlowCount})
				|/g RESISTED SLOW > ${Target.CleanName} <
				
				/if (${failedSlowCount} >= 4) {
					/i msg IRC_CHANNEL # Giving up slowing!! failed ${failedSlowCount} times
					/goto :DoneSlowing
				}
				/delay 3s
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

Sub HandleAssistCallBeastlord(int SpawnId)

	/if (!${SpawnId}) {
		/return
	}
	
	/varset AbortLoop 1
	/doevents

	/declare zoneId int local ${Zone.ID}

	/stick off

	/target npc id ${SpawnId}
	/delay 20

	/i msg IRC_CHANNEL # Assisting on ${Target.CleanName} (${Me.PctMana}m)

	:WaitTillTargetClose
	/if (!${Target.ID}) {
		/if (${Spawn[id ${SpawnId} npc].ID}) {
			/target npc id ${SpawnId}
			/delay 20
		} else {
			/i msg IRC_CHANNEL # Target died, aborting
			/goto :Done
		}
	}
	/if ((${Target.Distance} > 50) || !${Target.LineOfSight}) {
		/doevents
		/goto :WaitTillTargetClose
	}
	:TargetIsClose


	/if (DO_SLOW && ${Target.PctHPs} > 20) {
		|Dont bother slowing mobs below 20% hp
		/call BeastlordWaitForTargetHP 97
		/call BstSlowMob ${SpawnId}
	}

	/if (${Target.ID} != ${SpawnId}) {
		/target npc id ${SpawnId}
		/delay 20		
	}

	/if (DO_SLOW) {
		/stick 15 id ${SpawnId}
	} else {
		/stick 15 behind id ${SpawnId}
	}
	/attack on
	/if (${Me.Pet.ID}) {
		/pet attack
	}

	/if (DO_NUKE) {
		/call BeastlordWaitForTargetHP 85
	}
	/varset AbortLoop 0

	:Loop
		/doevents
		/if (${Zone.ID} != ${zoneId}) {
			/i msg IRC_CHANNEL # Stopping loop, zoned
			/return
		}

		/if (!${Spawn[npc id ${SpawnId}].ID}) {
			/i msg IRC_CHANNEL # Ending nuking (mob dead) (${Me.PctMana}m)
			/goto :Done
		}			

		/target npc id ${SpawnId}
		/doevents
		/delay 20

		/if (${Target.Type.NotEqual[NPC]}) {
			/i msg IRC_CHANNEL # Got wrong target: ${Target.CleanName} (not a npc!)
			/target npc id ${SpawnId}
			/delay 20
			/goto :Loop
		}
		/if (${AbortLoop}) {
			/goto :Done
		}
		/if (${Me.PctMana} < 30) {
			/i msg IRC_CHANNEL LOM - Medding (${Me.PctMana}m)
			/goto :Done
		}
		/if (${Me.TargetOfTarget.ID} == ${Me.ID}) {
			/i msg IRC_CHANNEL # Got aggro, waiting
			/delay 10s
		}

		/if (DO_NUKE) {

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
					|/i msg IRC_CHANNEL # Nuking ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)
					/call Cast "NUKE_SPELL"
					/delay 10s
				} else {
					/i msg IRC_CHANNEL # Ending nuking (mob ${Target.PctHPs}% hp) (${Me.PctMana}m)
					/goto :Done
				}
			}
		}

	/goto :Loop

	:Done

	/call UpdateBuffs
	|/call HandleEndOfFightCaster

/return

Sub BeastlordWaitForTargetHP(int pct)

	|todo: backup heala kanske? heala pet?

	:Loop
	/if (!${Target.ID}) {
		/return
	}
	/if (${Target.PctHPs} > ${pct}) {
		/doevents
		/goto :Loop
	}

/return

Sub BeastlordUpdateBuffs
	/declare oldSpell string local ${Me.Gem[7]}

	/target myself

	|Level 70 beastlord buffs
	/call RefreshBuff "Spiritual Vitality" 50
	/call RefreshBuff "Spiritual Ascendance" 50
	
	|pet & stuff
	/if (!${Me.Pet.ID}) {
		/i msg IRC_CHANNEL # Making pet
		/call RefreshBuff "Spirit of Rashara" 0
		|fixme: delay needed?
		|/delay 2s
	}

	|buff pet
	/if (${Me.Pet.ID}) {
		|pet haste:
		/call RefreshPetBuff "Growl of the Beast" 0
		|pet proc buff: irionu=stun proc
		/call RefreshPetBuff "Spirit of Irionu" 0
		
		/call RefreshPetBuff "Tiny Companion" 0
	}


	/if (${Me.Gem[7].Name.NotEqual["${oldSpell}"]}) {
		/delay 2s
		/echo # Restoring old spell gem
		/memspell 7 "${oldSpell}"
	}

/return