|OMM:
#Event OMM_DT "You feel a gaze of deadly power focusing on you."
Sub Event_OMM_DT(string Line)
	/echo [${Time}] OMM DT on me!! Clicking Mirrored Mask
	/popup OMM DT on me!!  Clicking Mirrored Mask
	/delay 1s
	/call Cast "Mirrored Mask" item
/return

#Event OMM_OtherFailed "Overlord Mata Muram fixes his gaze on #1#"
Sub Event_OMM_OtherFailed(string Line, string Name)
  /echo [${Time}] OMM: ${Name} failed to click!
/return

|AM mark of death notice:
#Event MarkOfDeathYou "You have been marked for death."
Sub Event_MarkOfDeathYou(string Line)
	/echo MARK OF DEATH ON ME!
	/popup MARK OF DEATH ON ME!
	/target myself
	|/delay 1s
	|/call Cast "Shield of the Immaculate" item
/return

|vishmitar DT - say "shoulder my burden" to cloud
#Event Vish "You sense your doom approaching."
Sub Event_Vish(string Line)
	/echo DT ON ME! say 'shoulder my burden' to mournful spirit (${SpawnCount[npc mournful spirit]} is up)
	/popup DT ON ME! say 'shoulder my burden' to mournful spirit (${SpawnCount[npc mournful spirit]} is up)
/return

#Event LayOnHands "#1# feels a healing touch."
Sub Event_LayOnHands(string Line, string Name)
	|also triggers on paladin group heals
	/echo ${Name} got Lay on Hands
/return


#Event GiftOfMana "You have been granted a gift of mana!"
Sub Event_GiftOfMana(string Line)
	/echo [${Time}] Gift of Mana triggered
	/popup Gift of Mana triggered!
/return

#Event GiftOfRadiantMana "You have been granted a gift of radiant mana!"
Sub Event_GiftOfRadiantMana(string Line)
	/echo [${Time}] Gift of Radiant Mana triggered
	/popup Gift of Radiant Mana triggered!
/return

#Event MezBreak "#1# has been awakened by #2#."
Sub Event_MezBreak(string Line, string Mob, string MezBreaker)
	/echo MEZBREAK: ${Mob} wakened by ${MezBreaker} [${Spawn[PC ${MezBreaker}].Class.ShortName}]
/return

#Event RangerWS "#1#'s weapons begin to spin."
Sub Event_RangerWS(string Line, string Name)
	/echo ${Name} goes WS
/return

#Event MonkSlowed "#1#'s attacks are slowed by intense pain."
Sub Event_MonkSlowed(string Line, string Name)
	/echo  ${Name} SLOWED (MNK, 25%)
/return

#event MySpellInterrupted "Your spell is interrupted."
Sub Event_MySpellInterrupted(string Line)
	/echo # Casting interrupted!
/return


#Event Necro_ManaDumped "A foreign surge of mana refreshes your mind."
Sub Event_Necro_ManaDumped(string Line)
	|Sedulous Subversion, NEC/56
	/echo # Mana dumped (+150 mana)
/return

|Chest debuff, useful for mpg chest trial (fixme verify!!!!!!!!!!!!!):

|Efter Reebo's Greater Augury:
#Event Chest_CursedTrap	"#1#ingenaning"
Sub Event_Chest_CursedTrap(string x)
	/echo blabla cursed trap disarmed
/return
#Event Chest_NotCursedTrap	"#1#ingenaning"
Sub Event_Chest_NotCursedTrap(string x)
	/echo blabla cursed trap disarmed
/return


|Efter Reebo's Greater Exorcism
#Event Chest_DisarmSuccess "#1#ingenaning"
Sub Event_Chest_DisarmSuccess(string x)
	/echo blabla cursed trap disarmed
/return
#Event Chest_DisarmFailure "#1#ingenaning"
Sub Event_Chest_DisarmFailure(string x)
	/echo blabla cursed trap disarmed
/return

|Efter Reebo's Greater Cleansing
#Event Chest_DestroySuccess "#1#ingenaning"
Sub Event_Chest_DestroySuccess(string x)
	/echo blabla cursed trap disarmed
/return
#Event Chest_DestroyFailure "#1#ingenaning"
Sub Event_Chest_DestroyFailure(string x)
	/echo blabla cursed trap disarmed
/return


|#Event Druid_SkinToVines "#1#xxxxx"
|Sub Event_Druid_SkinToVines(string Line, string Name)
|	/echo ${Name} Skin to Vines
|/return


|Druid debuffs
|note: immolation of ro DRU/61 has same text "immolated by blazing flames."
#Event MobSnared	"#1# has been ensnared."
#Event MobQuickSnared	"#1#'s legs are covered in mire thorns."
#Event MobQuickSnared	"#1# is ensnared in the embrace of earth."
#Event MobRooted	"#1# is entrapped by roots."
#Event MobRooted	"#1# becomes entwined in roots."
#Event MobRooted	"#1# adheres to the ground." 
#Event MobRooted	"#1#'s legs are encased in clinging roots."
#Event MobHandOfRo	"#1# is immolated by blazing flames."
#Event MobRosIllumination	"#1# is surrounded by an outline of cold flame."
#Event MobSunsCorona		"#1# is outlined in white fire."
#Event MobImmolationSun		"#1# is immolated by the flames of the sun."
#Event MobEci		"#1# encrusted with a thin layer of ice."
#Event MobGlacierBreath "#1#s body is covered in a film of ice."
#Event MobVengeanceSun	"#1#'s body is encased in the flames of the sun."
#Event MobWaspSwarm	"#1# is consumed in a swarm of black wasps."
#Event MobSwarmOfFireants "#1# is consumed in a swarm of fireants."
|note: winged death have same text as swarming death
#Event MobSwarmingDeath "#1# is engulfed by a swarm of deadly insects."
#Event MobSylvanEmbers	"#1#'s body is covered in sylvan embers."
#Event MobSunscorch	"#1#'s body is encased in the flames of the sun."
#Event MobDruidEpic15	"#1# is cursed by nature's blight."
#Event MobDruidEpic20	"#1# is caught in the fury of the seasons."

#Event MobGlamourOfTunare	"#1# is surrounded by a Tunarian glamour."
#Event MobDruidPaxed	"#1#'s mind calms."
#Event MobDruidStunned	"#1# is struck by a bolt of static."
#Event MobFlameLick		"#1# is surrounded by flickering flames."


|Ranger debuffs
|ranger DoN snares lv61 Earthen Embrace, lv69 Earthen Shackles:
#Event MobRangerSnared	"#1#'s legs are entrapped in serpent vines."
#Event MobRangerSnared	"#1#'s legs are entrapped in living earth."
#Event MobRangerSlowed	"#1# is slowed by the embracing earth."

|Bard debuffs
#Event MobBardSlowed	"#1#'s rhythm slows."
#Event MobBardMezzed	"#1# falls unconscious."
#Event MobBardSnared	"#1#'s legs are wrapped in chains of torment."

|Rogue debuffs
#Event MobRogueMezzed	"#1# looks around franticly."

|Cleric debuffs
#Event MobPaxed		"#1# looks less aggressive."

|Chanter debuffs
#Event MobSlowed	"#1# slows down."
#Event MobTashed	"#1# glances nervously about."
#Event MobSpasm		"#1# loses control of their muscles."

#Event MobMezzed	"#1# has been mesmerized."
#Event MobMezzed	"#1#'s jaw falls as they begin to drool."
#Event MobMezzed	"#1# falls asleep."
#Event MobMezzed	"#1# stares off into the distance."
#Event MobMezzed	"#1# stares at the ground quietly."
#Event MobMezzed	"#1# gazes happily into space."



|necro debuffs:
#Event MobNecMezzed	"#1# stops moving."
#Event MobNecMezzed	"#1# stops moaning."
#Event MobNecScentMidnight "#1# is marked by the scent of midnight."
#Event MobNecScentTerris "#1# is surrounded by a dark haze."

|Wizard debuffs
#Event MobWizSnared	"#1# is shackled to the ground."

|Necro debuffs
#Event MobSnaredNecro "#1# is engulfed in an embracing darkness."
#Event MobSnaredNecro "#1# is covered in bubbling black shadows."
#Event MobSnaredNecro "#1# is gripped by shadows of fear and terror."
#Event MobSnaredNecro "#1# is covered in coruscating black shadows."

|SK debuffs
#Event MobSnaredSK	"#1# engulfed by darkness."
#Event MobSnaredSK	"#1# is engulfed by a festering darkness."

|Bst debuffs
#Event MobBSTSlowed  "#1# loses their fighting edge."
#Event MobBSTSlowed  "#1# feels lethargic."
#Event MobBSTSlowed  "#1#'s arms slow down."

#Event MobBSTSnared  "#1# has been hobbled by the spirit of the swamp."

|Shaman debuffs
#Event MobSpasm			"#1#'s muscles spasm uncontrollably."
#Event MobPutridDecay	"#1# skin becomes slimey and translucent."
#Event MobShmSlowed		"#1# yawns."
#Event MobMalos			"#1# looks very uncomfortable."

|Weapon proc's
#Event MobRageProc	"#1# face contorts with rage."

|Mob doing bad shit
#Event MobGating	"#1# begins to cast the gate spell."
#Event MobHealing	"#1# begins to cast a spell. <Complete Healing>"
#Event MobInterrupted "#1#'s casting is interrupted!"
#Event MobAnnulled	"#1# feel dispelled."
#Event MobAnnulled	"#1# feels annulled."

|note: bestow divine aura is targetable instacast, so cant be sure if it is cleric or some1 else going da for this one
#Event PlayerDA		"#1# begins to cast a spell. <Bestow Divine Aura>"
#Event PlayerDA		"#1# begins to cast a spell. <Divine Aura>"

|cleric divine barrier:
|#Event PlayerDA		"#1# begins to cast a spell. <Divine Barrier>"
#Event PlayerDA		"#1# is surrounded by a divine barrier."


|clickies, biolumescent orb:
#Event MobBlinded	"#1# is blinded by a flash of light."

|Bad shit happening me
#Event MeAnnulled	"You feel annulled."
#Event MeAnnulled	"You feel dispelled."
#Event MeAnnulled	"You feel a bit dispelled."












|Raid: Sullon Zek in Razorthorn
|divine aura (curse spreading)
#Event CursedBySullonSpreading "You have succumbed to the ferocity of Sullon Zek"

|fizzle (curse spreading)
#Event CursedBySullonSpreading "Your knees tremble from the wrath of Sullon Zek"

Sub Event_CursedBySullonSpreading(string Line)
	/echo SULLON ZEK CURSED! RUN BEFORE SPREADING!!
	/popup SULLON ZEK CURSED! RUN BEFORE SPREADING!!
/return


|Spell slow (not spreading)
#Event CursedBySullon "You fall under the curse of Sullon Zek"
Sub Event_CursedBySullon(string Line)
	/echo Spell slow cursed by Sullon Zek (not spreading)
/return



|fixme: confirm this trigger
#Event DI_Fired "#1# has been rescued by divine intervention!"
Sub Event_DI_Fired(string line, string name)
	/echo [${Time}] ${name} was saved by DI!
/return

#Event DI	"#1# feels the watchful eyes of the gods upon them."
Sub Event_DI(string line, string name)
	/echo [${Time}] ${name} got DI
/return

#Event ME_DI "You feel the watchful eyes of the gods upon you."
Sub Event_ME_DI(string line)
	/echo [${Time}] I got DI
/return


#Event MobProcStunned "#1# staggers painfully."
Sub Event_MobProcStunned(string Line, string Name)
	/if (!${Raid.Members}) {
		/echo ${Name} STUNNED (proc)
	}
/return


Sub Event_MobTashed(string line, string name)
	/if (${Spawn[${name}].Type.NotEqual["PC"]}) {
		/echo ${name} Tash (-50 MR)
	}
/return

Sub Event_MobMalos(string line, string name)
	/if (${Spawn[${name}].Type.NotEqual["PC"]}) {
		/echo ${name} Malos (SHM/MAG) (-70? CR/MR/PR/FR)
	}
/return


Sub Event_MobHandOfRo(string line, string name)
	/echo ${name} Hand of Ro (-72 FR, -15 AC, -100 ATK)
/return

Sub Event_MobRosIllumination(string line, string name)
	/echo ${name} Ro's Illumination (-14 AC, -80 ATK)
/return

Sub Event_MobSunsCorona(string line, string name)
	/echo ${name} Sun's Corona (-19 AC, -90 ATK)
/return

Sub Event_MobImmolationSun(string line, string name)
	/echo ${name} DoT Immolation of the Sun (-40 FR, -36 AC)
/return

Sub Event_MobSylvanEmbers(string line, string name)
	/echo ${name} DoT Sylvan Embers (-40 FR, -30 AC)
/return

Sub Event_MobSunscorch(string line, string name)
	/echo ${name} DoT Sunscorch (DRU)
/return


Sub Event_MobEci(string line, string name)
	/echo ${name} Eci's (-55 CR, -25 AC)
/return

Sub Event_MobGlacierBreath(string line, string name)
	/echo ${name} Glacier Breath (-55 CR, -30 AC)
/return

Sub Event_MobDruidEpic15(string line, string name)
	/echo ${name} Druid Epic 1.5
/return

Sub Event_MobDruidEpic20(string line, string name)
	/echo ${name} Druid Epic 2.0
/return

Sub Event_MobGlamourOfTunare(string line, string name)
	/echo ${name} Glamour of Tunare (-20 MR)
/return

Sub Event_MobVengeanceSun(string line, string name)
	/echo ${name} DoT Vengeance of the Sun (DRU)
/return

Sub Event_MobWaspSwarm(string line, string name)
	/echo ${name} DoT Wasp Swarm (DRU)
/return

Sub Event_MobSwarmOfFireants(string line, string name)
	/echo ${name} DoT Swarm of Fireants (DRU)
/return


Sub Event_MobSwarmingDeath(string line, string name)
	/echo ${name} DoT Swarming Death (DRU)
/return


Sub Event_MobSlowed(string line, string name)
	/if (${line.Right[18].Equal["s body slows down."]}) {
		|enchanter desolate deeds says "x's body slows down" also
		/if (${Spawn[radius 200 PC ENC].ID} && !${Spawn[radius 200 PC SHM].ID}) {
			/echo ${name.Left[-7]} SLOWED (ENC)
			/return
		}
		/if (!${Spawn[radius 200 PC ENC].ID} && ${Spawn[radius 200 PC SHM].ID}) {
			/echo ${name.Left[-7]} SLOWED (SHM)
			/return
		}
		/echo ${name.Left[-7]} SLOWED (ENC/SHM)
		/return
	}
	|Warrior item Truncheon of Doom procs Tepid Deeds (mob slows down) aswell
	/if (${Spawn[radius 300 PC ENC].ID}) {
		/echo ${name} SLOWED (ENC)
	} else {
		/echo ${name} SLOWED (WAR proc)
	}
/return

Sub Event_MobBSTSnared(string line, string name)
	/echo ${name} SNARED (BST pet proc) 24 sec
/return

Sub Event_MobBSTSlowed(string line, string name)
	/echo ${name} SLOWED (BST)
/return

Sub Event_MobShmSlowed(string line, string name)
	/echo ${name} SLOWED (SHM)
/return

Sub Event_MobPutridDecay(string line, string name)
	/echo ${name} Putrid Decay (SHM) (-55 DR, -55 PR)
/return

Sub Event_MobSpasm(string line, string name)
	/echo ${name} Spasm (ENC/SHM) (-100 DEX/AGI/STR, -39 AC)
/return

Sub Event_MobSnaredSK(string line, string name)
	/echo ${name} SNARED (SK)
/return

Sub Event_MobSnared(string line, string name)
	/echo ${name} SNARED (15 min)
/return

Sub Event_MobQuickSnared(string line, string name)
	|druid DoN spell
	/echo ${name} SNARED (DRU 3 min)
/return

Sub Event_MobRangerSnared(string line, string name)
	|ranger DoN spell
	/echo ${name} SNARED (RNG 3 min)
/return

Sub Event_MobRangerSlowed(string line, string name)
	|ranger epic 1.0 proc
	/echo [${Time}] ${name} SLOWED 50% (RNG, 2.5 min)
	|/popup ${name} Ranger Slowed
	|/g ${name} Ranger Slowed
	/bandolier Activate Normal
/return

Sub Event_MobRooted(string line, string name)
	/if (${Spawn[${name}].Type.NotEqual["PC"]}) {
		/echo ${name} rooted
	}
/return

Sub Event_MobSnaredNecro(string line, string name)
	/echo ${name} SNARED (NEC)
/return

Sub Event_MobWizSnared(string line, string name)
	|Dagger of Entrapment
	/if (${Spawn[${name}].Type.NotEqual["PC"]}) {
		/echo ${name} SNARED (WIZ 2.5 min)
	}
/return


Sub Event_MobRageProc(string line, string name)
	|/echo ${name} Rage proc (BoC)
/return

Sub AttemptToStun(string name)

	/if (${Me.CombatAbilityReady["Leopard Claw"]} && ${Me.Combat} && ${Target.ID} && ${Target.CleanName.Equal[${name}]}) {
		|MNK/67
		/echo # Using Leopard Claw to try to kill gating mob
		/aa act Leopard Claw
		/return
	}

	/declare StunSpellName string local
	
	|most stuns dont work > lv70, gale of stormborn does
	/if (${Spawn[NPC ${name}].Level} > 75) /return
	
	|CLR/68
	/if (${Me.SpellReady["Sound of Divinity"]}) /varset StunSpellName Sound of Divinity
		
	|DRU/73
	/if (${Me.SpellReady["Gale of the Stormborn"]}) /varset StunSpellName Gale of the Stormborn

	|DRU/69
	/if (${Me.SpellReady["Stormwatch"]}) /varset StunSpellName Stormwatch
	
	|if no stunspell spell memmed:
	/if (!${Me.Gem["${StunSpellName}"]}) /return

	|/echo # Debug: Stunning with ${StunSpellName}

	/if (${Me.Casting.ID} && ${Me.Casting.SpellType.Equal[Detrimental]} && ${Me.Casting.Name.NotEqual["${StunSpellName}"]}) {
		|interrupta, ENBART om spellen som kastas e en nuke
		/echo # ABORTING cast of ${Me.Casting.Name} for stun!
		/keypress d
		/delay 1
		/keypress d
	}

	/if (${Spawn[${name}].ID} && ${Spawn[${name}].LineOfSight}) {
		/target npc ${name}
		/delay 5
		/echo * Trying to stun ${name}
		/call Cast "${StunSpellName}"
	}

/return

Sub Event_MobGating(string line, string name)
	/echo [${Time}] Warning: ${name} gating!!!
	/call AttemptToStun "${name}"
/return

Sub Event_MobHealing(string line, string name)
	/if (${Spawn[${name}].Type.Equal["NPC"]}) {
		/echo [${Time}] Warning: ${name} casting CH!

		|dont mess with this in raids (not in tacvi, but good in mpg trial foresight (the mastery of corruption)
		/if (!${Raid.Members}) /call AttemptToStun "${name}"
	}
/return

Sub Event_MobInterrupted(string line, string name)
	/if (${Spawn[${name}].Type.NotEqual["PC"]} && ${Spawn[${name}].Type.NotEqual["Pet"]}) /echo [${Time}] ${name} [${Spawn[${name}].Class.ShortName}] interrupted
/return

Sub Event_MobAnnulled(string line, string name)
	/if (${Spawn[${name}].Type.NotEqual["PC"]}) /echo ${name} [${Spawn[${name}].Class.ShortName}] annulled
/return

Sub Event_MobBlinded(string line, string name)
	/echo ${name} Blinded
/return

Sub Event_PlayerDA(string line, string name)
	/echo [${Time}] ${name} goes DA
/return

Sub Event_MobPaxed(string line, string name)
	/echo ${name} pacified (CLR)
/return

Sub Event_MobBardSlowed(string line, string name)
	/echo ${name} SLOWED (BRD, 55%)
/return

Sub Event_MobBardSnared(string line, string name)
	/echo ${name} SNARED (BRD, 18 sec)
/return

Sub Event_MobDruidPaxed(string line, string name)
	/echo ${name} pacified (DRU)
/return

Sub Event_MobDruidStunned(string line, string name)
	/echo ${name} stunned (DRU)
/return

Sub Event_MobFlameLick(string line, string name)
	/echo ${name} Flame Lick (DRU, -3 AC)
/return

Sub Event_MobMezzed(string line, string name)
	/echo ${name} MEZZED (ENC)
/return

Sub Event_MobRogueMezzed(string line, string name)
	/echo ${name} MEZZED (ROG, 6 seconds)
/return

Sub Event_MobBardMezzed(string line, string name)
	/echo ${name} MEZZED (BRD)
/return

Sub Event_MobNecMezzed(string line, string name)
	/echo ${name} MEZZED (NEC)
/return

Sub Event_MobNecScentMidnight(string line, string name)
	/echo ${name} Scent of Midnight (NEC, -55 DR, -55 PR)
/return

Sub Event_MobNecScentTerris(string line, string name)
	/echo ${name} Scent of Terris (NEC, -36 FR, -36 DR, -36 PR)
/return

Sub Event_MeAnnulled
	/echo [${Time}] I was annulled!
/return

