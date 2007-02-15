|todo:
|spara start position för warrior, iaf vilket håll han tittar åt, vänd tebax dit efter fighten

|used in bot.mac to auto kill mobs
#define AUTO_ATTACK_MOBS			0

#define AUTO_FACE_MOBS				1

#define DO_TAUNT							1

|min level of mobs to use bazu bellow on them:
#define BELLOW_MIN_LEVEL			61

|Proc from Fabled Obtenebrate Short Sword
#Event MobSnaredSK	"#1# is engulfed by a festering darkness."
Sub Event_MobSnaredSK(string Line, string Name)

	/if (${InvSlot[mainhand].Item.Name.Equal["Fabled Obtenebrate Short Sword"]} || ${InvSlot[offhand].Item.Name.Equal["Fabled Obtenebrate Short Sword"]}) {
		/if (${Name.Equal[${Target.CleanName}]}) {
			/i msg IRC_CHANNEL # SNARED: ${Target.CleanName} (dot-proc)
		}
	}
/return


Sub HandleAssistCallWarrior(int SpawnId)

	|När funktionen callas, spara me.x, me.y, me.z. sen om man e för långt därifrån, över 15 t.ex, försök gå tebax dit under fighten
	/declare StartX float local ${Me.X}
	/declare StartY float local ${Me.Y}
	/declare StartZ float local ${Me.Z}

	/if (!${SpawnId}) {
		/return
	}

	/declare zoneId int local ${Zone.ID}
	/target npc id ${SpawnId}
	
	/if (${Me.Sitting}) {
		/i msg IRC_CHANNEL # EEP im sitting on inc! standing!
		/stand
	}
	
	|vänta till target e tillräckligt nära, sen kör disc bazu bellow
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
		|/doevents
		/goto :WaitTillTargetClose
	}
	/if (!DO_TAUNT && (${Target.PctHPs} > 95)) {
		|if im not tanking, dont jump mob right away
		/delay 10
		/goto :WaitTillTargetClose
	}
	:TargetIsClose

	|Taunta & slå på attack
	/if (AUTO_FACE_MOBS) {
		/face fast
	}

	/attack on
	/if (${Plugin[MQ2MoveUtils].Name.Length}) {
		/stick 12 id ${SpawnId}
	}

	|fixme: denna returnerar FALSE ibland trots den e ready...
	/if (DO_TAUNT && ${Me.CombatAbilityReady["Bazu Bellow"]} && (${Target.Distance} < 50) && (${Target.Level} >= BELLOW_MIN_LEVEL)) {
		/i msg IRC_CHANNEL TAUNT: ${Target.CleanName} (${Target.ID})
		/doability "Bazu Bellow"
		/delay 30
	}	

	|loopa till target e död. om hott ändras, kör taunt
	:WarLoop
	/doevents
	/if (${Zone.ID} != ${zoneId}) {
		/i msg IRC_CHANNEL # Stopping loop, zoned
		/return
	}

	/if (!${Spawn[npc id ${SpawnId}].ID}) {
		/i msg IRC_CHANNEL # Ending meleeing (mob dead)
		/goto :Done
	}
	/if (AUTO_FACE_MOBS && ${Me.Combat} && (${Target.ID} == ${SpawnId}) && ${Target.LineOfSight}) {
		|short delay so you dont face the WHOLE time
		/face fast
		/delay 5
	}
	/if (${Me.Combat} && !${Me.Stunned}) {

		|Taunt mob
		/if (DO_TAUNT) {
			/if (${Group.Members} && ${Me.TargetOfTarget.ID} && (${Me.TargetOfTarget.ID} != ${Me.ID})) {
				/if (${Me.CombatAbilityReady["Bazu Bellow"]} && (${Target.Distance} < 50) && (${Target.Level} >= BELLOW_MIN_LEVEL)) {
					/i msg IRC_CHANNEL RE-TAUNT: ${Target.CleanName} (${Target.ID}) (${Me.TargetOfTarget.Name} has aggro)
					/doability "Bazu Bellow"
					/delay 10
				}
			}
			|just taunt
			/if (${Me.AbilityReady["Taunt"]}) {
				|/i msg IRC_CHANNEL # Taunt: ${Target.CleanName} (${Target.ID}) (${Me.TargetOfTarget.Name} has aggro)
				/doability  "Taunt"
				/delay 20
			}

			|Disarm, for more aggro
			/if (${Me.AbilityReady["Disarm"]}) {
				/doability "Disarm"
				/delay 20
			}
		}

		|Kick mob
		/if (${Me.AbilityReady["Kick"]}) {
			/doability "Kick"
			/delay 20
		}
	}
	/goto :WarLoop

	:Done

	|walk to start position
	|/i msg IRC_CHANNEL # Returning to start point
	|/call GotoFunction ${StartY} ${StartX} ${StartZ}
	|/echo goto ${StartY} ${StartX} ${StartZ}
	|/call WaitTilThere

	/call WarriorRequestBuffs

/return


Sub RequestBuff(string SpellName, int Duration)
	/if (!${Me.Buff[${SpellName}].ID} || (${Me.Buff[${SpellName}].Duration} < ${Duration})) {
		/if (!${Me.Book[${SpellName}]}) {
			/i msg IRC_CHANNEL BUFFREQ ${SpellName}
		} else {
			/i msg IRC_CHANNEL Self buffing: ${SpellName}
		}
	}
/return

Sub WarriorRequestBuffs

	/declare i int local 0
	
	/declare GroupHasBeastlord int local 0
	/declare GroupHasCleric int local 0
	/declare GroupHasDruid int local 0
	/declare GroupHasShadowKnight int local 0
	
	/if (!${Group.Members}) {
		/return
	}

	/for i 1 to 5
		/if (${Group.Member[${i}].Class.Name.Equal[Cleric]}) /varset GroupHasCleric 1
		/if (${Group.Member[${i}].Class.Name.Equal[Druid]}) /varset GroupHasDruid 1
		/if (${Group.Member[${i}].Class.Name.Equal[Beastlord]}) /varset GroupHasBeastlord 1
		/if (${Group.Member[${i}].Class.Name.Equal["Shadow Knight"]}) /varset GroupHasShadowKnight 1
	/next i

	/if (${GroupHasBeastlord}) {
		/call RequestBuff "Ferocity of Irionu" 20
	}

	/if (${GroupHasCleric} && !${Me.Buff["Conviction"].ID} && !${Me.Buff["Hand of Conviction"].ID}) {
		/if (!${Me.Buff["Balikor's Mark"].ID}) {
			/call RequestBuff "Symbol of Balikor" 50
		}
		/call RequestBuff "Ward of Valiance" 50
	}

	/if (${GroupHasDruid}) {
		/call RequestBuff "Lion's Strength" 50
		/if (!${Me.Buff["Blessing of Steeloak"].ID} && !${Me.Buff["Conviction"].ID} && !${Me.Buff["Hand of Conviction"].ID}) {
			/call RequestBuff "Steeloak Skin" 50
		}

		/if (!${Me.Buff["Fireskin"].ID} && !${Me.Buff["Nettle Shield"].ID}) {
			/call RequestBuff "Circle of Nettles" 30
		}
	}

	/if (DO_TAUNT && ${GroupHasShadowKnight}) {
		/call RequestBuff "Voice of Thule" 10
	}

/return