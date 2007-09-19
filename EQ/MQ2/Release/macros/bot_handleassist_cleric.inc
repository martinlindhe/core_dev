|todo: use pious light (3.8 sec heal) more
|Cleric bot code
|----------------------------------------------
|Configuration:
#define CHEAL_SPELLNAME		"Complete Healing"
#define QHEAL_SPELLNAME		"Pious Remedy"
#define YAULP_SPELLNAME		"Yaulp VII"
#define NUKE_SPELLNAME		"Reproach"

|% of when to start CH & quick heals
#define CHEAL_PERCENT		55
#define QHEAL_PERCENT		30

|rss: ch 86, qh 60

|sewers: ch 80, quick 50%
|tipt: ch 96, qick 70%
|mpg: ch 85, quick 55%
|unrest: ch 55, quick 35

|Turn this to 0 to stop nuking too
#define DO_NUKE				1

|fixme: cleric kasta reverse DS kod
#define DO_REVERSEDS		0
#define REVERSEDS_SPELLNAME	"Mark of the Blameless"

|----------------------------------------------

#event DEBUFF_ClrPlacate "#1# looks very calm."
Sub Event_DEBUFF_ClrPlacate(string Line, string Name)
	|CLR/70: Placate
	/if (${Me.Class.Name.Equal[Cleric]}) {
		/if (${Target.ID}) {
			/i msg IRC_CHANNEL PACIFIED: ${Target.CleanName} (${Target.ID})
		}
	}
/return

Sub DoYaulp
	
	/if (${Me.PctMana} == 100) /return

	/if (${Me.Buff["Spiritual Vitality"].ID}) {
		|yaulp stackar inte med SV
		/i msg IRC_CHANNEL # Dropping SV for Yaulp!
		/notify BuffWindow Buff${Math.Calc[${Me.Buff["Spiritual Vitality"].ID}-1].Int} leftmouseup
		/return
	}
	
	/if (${Me.Buff["Summon Drogmor"].ID} || ${Me.Buff["Summon Horse"].ID}) {
		/return
	}

	/if (!${Me.Buff["YAULP_SPELLNAME"].ID}) {
		/call WaitForSpellReady "YAULP_SPELLNAME"
		|/i msg IRC_CHANNEL YAULP: YAULP_SPELLNAME
		/call Cast "YAULP_SPELLNAME"
	}
/return

Sub HandleAssistCallCleric(int SpawnId)

	/if (!${SpawnId}) {
		/return
	}

	/declare zoneId int local ${Zone.ID}
	/declare tankId int local 0
	/declare tankName string local

	|fixme: delegate main tank somehow / guess - first warrior - and save in ini
	/call FindTank
	/varset tankId ${Macro.Return}
	/if (${tankId} == 0) {
		/i msg IRC_CHANNEL # ERRROR !!! No tank found!!!!!!!!!!!!!!!
		/return
	}

	/varset tankName ${Spawn[id ${tankId}].Name}

	/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME"

	/i msg IRC_CHANNEL # Starting healing of ${tankName} (${Me.PctMana}m)
	/target id ${tankId}
	
	
	/if ((${Spawn[id ${tankId} pc].PctHPs} > 95) && (${Spawn[id ${SpawnId}].Level} < 70)) {
		|Only yaulp on inc if mobs are not so uber tough
		/call DoYaulp
	}

	/varset AbortLoop 0
	:ClericLoop
		/doevents
		/if (${AbortLoop}) {
			/goto :Done
		}
		/if (${Zone.ID} != ${zoneId}) {
			/i msg IRC_CHANNEL # Stopping loop, zoned
			/return
		}
		/if ((${Spawn[id ${tankId} pc].PctHPs} <= CHEAL_PERCENT) && (${Spawn[id ${tankId} pc].Distance} < 100)) {
			/if (${Spawn[id ${tankId} pc].PctHPs} > QHEAL_PERCENT) {
				/target pc ${tankName}
				/call WaitForSpellReady "CHEAL_SPELLNAME"
				/i msg IRC_CHANNEL # CH > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)
				/call Cast "CHEAL_SPELLNAME"
				|fixme: loop while its being casted check target hp
				|--- call Cast returns after spell is casted done
			} else  {
				/target pc ${tankName}
				/call WaitForSpellReady "QHEAL_SPELLNAME"
				/i msg IRC_CHANNEL # PR > ${Target.Name} < at ${Target.PctHPs}% (${Me.PctMana}m)
				/call Cast "QHEAL_SPELLNAME"
			}
		}
		/if (${Spawn[id ${tankId} pc].PctHPs} > 95) {
			/call DoYaulp
		}

		/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME"
		/if (${Target.ID} != ${tankId}) {
			/target pc ${tankName}
			|need a bit delay to update HoTT
			/delay 30
			|fixme: loop until HoTT gets updated
		}

		/if (!${Spawn[npc id ${SpawnId}].ID}) {
			/call HandleGroupHeal QHEAL_PERCENT "QHEAL_SPELLNAME"
			/i msg IRC_CHANNEL # Ending (mob dead) (${Me.PctMana}m)
			/goto :Done
		}

		/if (${Me.TargetOfTarget.ID} && ${Me.TargetOfTarget.Type.NotEqual[Corpse]}) {

			/if (DO_NUKE && (${Me.PctMana} >= 70) && (${Spawn[id ${tankId} pc].PctHPs} >= CHEAL_PERCENT) && (${Me.TargetOfTarget.PctHPs} <= 75)) {
				|/i msg IRC_CHANNEL # Start attempt nuke.
				/target npc id ${Me.TargetOfTarget.ID}
				/delay 20

				/if (${Target.Type.NotEqual[NPC]}) {
					/i msg IRC_CHANNEL # Aborting nuking on ${Target.Name}, not a npc!
					/goto :ClericLoop
				}

				/if (${Target.ID} && (${Target.PctHPs} > 10) && ${Me.SpellReady["NUKE_SPELLNAME"]}) {
					|/i msg IRC_CHANNEL Spell rdy.
					/if (${Target.LineOfSight} && (${Target.Distance} < ${Me.Gem[${Me.Gem["NUKE_SPELLNAME"]}].Range})) {
						/i msg IRC_CHANNEL # Nuking ${Target.CleanName} at ${Target.PctHPs}% (${Me.PctMana}m)
						/call Cast "NUKE_SPELLNAME"
						/target pc ${tankName}
					}
				}
			}
		}
	/goto :ClericLoop

	:Done
	|heal group members below 70% at end of fight
	/call HandleGroupHeal 70 "QHEAL_SPELLNAME"

	/call UpdateBuffs
	/call HandleEndOfFightCaster
/return

|50 ticks = 5 minutes
Sub ClericUpdateBuffs

	/declare oldSpell string local ${Me.Gem[7]}

	/target myself

	|Level 70 cleric buffs
	/if (!${Spawn[group dru].ID} && !${Me.Buff["Steeloak Skin"].ID} && !${Me.Buff["Blessing of Steeloak"].ID} ) {
		|fixme: Stackar inte heller med Wunshi
		/call RefreshBuff "Armor of the Pious" 100
	}

	/if (${Spawn[group dru].ID} && !${Me.Buff["Conviction"].ID}  && !${Me.Buff["Hand of Conviction"].ID} ) {
		|Fixme: If group member is druid only
		/call RefreshBuff "Balikor's Mark" 50
	}

	/call RefreshBuff "Aura of Devotion" 40

	/if (${Me.Gem[7].Name.NotEqual["${oldSpell}"]}) {
		/delay 2s
		/echo # Restoring old spell gem
		/memspell 7 "${oldSpell}"
	}
/return