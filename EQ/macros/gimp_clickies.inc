| gimp_clickies.inc

#Event ToggleClickies		"[MQ2] clickies"
Sub Event_ToggleClickies
	/if (!${gimp_clickies_loaded}) /call Init_Clickies
	/varset gimp_clickies_activated ${If[${gimp_clickies_activated},0,1]}
	/echo gimp_clickies.inc is ${If[${gimp_clickies_activated},ACTIVATED,DEACTIVATED]}
/return

|Casts all important clickies to be ready for a boss fight
#Event Charge		"[MQ2] charge"
Sub Event_Charge
	/echo # Casting charge clickies	
	/if (${mcHUDmanaregenClicky.Length}) /call Cast "${mcHUDmanaregenClicky}" item
	/if (${mcHUDmanapoolClicky.Length}) /call Cast "${mcHUDmanapoolClicky}" item
	/if (${useClickyAttackBuff} && ${mcHUDAttackClicky1.Length}) /call Cast "${mcHUDAttackClicky1}" item
	/if (${useClickyAttackBuff} && ${mcHUDAttackClicky3.Length}) /call Cast "${mcHUDAttackClicky3}" item
	/if (${useClickyAttackBuff} && ${mcHUDAttackClicky5.Length}) /call Cast "${mcHUDAttackClicky5}" item
	/if (${useClickyResists} && ${mcHUDResistClicky.Length}) /call Cast "${mcHUDResistClicky}" item
	/if (${useClickyHaste} && ${mcHUDOverHasteClicky.Length}) /call Cast "${mcHUDOverHasteClicky}" item
/return

Sub Init_Clickies
	/declare gimp_clickies_loaded				int	outer	1
	/declare gimp_clickies_activated		int outer	1
	/declare gimp_clickies_timer				timer outer 0
		
	|let user turn on the use of the following clickies from INI:
	/declare useClickyJboots								int	outer	${Ini[INI_FILE, ${Me.Name}, ClickyJboots, NOT_FOUND]}
	/declare useClickyBracerOfTheHidden			int	outer	${Ini[INI_FILE, ${Me.Name}, ClickyBracerOfTheHidden, NOT_FOUND]}
	/declare useClickyVoiceOfTheSerpent			int outer ${Ini[INI_FILE, ${Me.Name}, ClickyVoiceOfTheSerpent, NOT_FOUND]}
	/declare useClickyRapierOfSomberNotes		int outer ${Ini[INI_FILE, ${Me.Name}, ClickyRapierOfSomberNotes, NOT_FOUND]}
	| /declare ClickyIllusionName							string outer ${Ini[INI_FILE, ${Me.Name}, ClickyIllusion, NOT_FOUND]}
	| /declare ClickyIllusionActive					int outer ${Ini[INI_FILE, ${Me.Name}, ClickyIllusionActive, 1]}

	|new type of clicky handling, by type of clicky instead of by item name:
	/declare useClickyAttackBuff						int	outer	${Ini[INI_FILE, ${Me.Name}, useClickyAttack, NOT_FOUND]}
	/declare useClickyDS										int	outer	${Ini[INI_FILE, ${Me.Name}, useClickyDS, NOT_FOUND]}
	/declare useClickyResists								int	outer	${Ini[INI_FILE, ${Me.Name}, useClickyResists, NOT_FOUND]}
	/declare useClickyHaste									int	outer	${Ini[INI_FILE, ${Me.Name}, useClickyHaste, NOT_FOUND]}
	/declare useClickyAC										int	outer	${Ini[INI_FILE, ${Me.Name}, useClickyAC, NOT_FOUND]}

	/declare mcHUDmount							string	outer
	/declare mcHUDshrinker					string	outer
	/declare mcHUDmanaregenClicky		string	outer
	/declare mcHUDmanapoolClicky		string	outer
	/declare mcHUDhpbuffClicky			string	outer
	/declare mcHUDsummonPullItem		string	outer
	/declare mcHUDAttackClicky1			string	outer
	/declare mcHUDAttackClicky3			string	outer
	/declare mcHUDAttackClicky5			string	outer
	/declare mcHUDDSClicky2					string	outer
	/declare mcHUDDSClicky4					string	outer
	/declare mcHUDResistClicky			string	outer
	/declare mcHUDHasteClicky				string	outer
	/declare mcHUDOverHasteClicky		string	outer
	/declare mcHUDACClicky					string  outer

	|/call FindMountClicky
	|/varset mcHUDmount ${Macro.Return}
		
	/call FindShrinkClicky
	/varset mcHUDshrinker ${Macro.Return}
		
	/call FindManaregenClicky
	/varset mcHUDmanaregenClicky ${Macro.Return}
		
	/call FindManapoolClicky
	/varset mcHUDmanapoolClicky ${Macro.Return}
		
	/call FindHPBuffClicky
	/varset mcHUDhpbuffClicky ${Macro.Return}

	/call FindSummonPullItem
	/varset mcHUDsummonPullItem ${Macro.Return}

	/call FindAttackClicky1
	/varset mcHUDAttackClicky1 ${Macro.Return}

	/call FindAttackClicky3
	/varset mcHUDAttackClicky3 ${Macro.Return}

	/call FindAttackClicky5
	/varset mcHUDAttackClicky5 ${Macro.Return}

	/call FindDSClicky2
	/varset mcHUDDSClicky2 ${Macro.Return}
	
	/call FindDSClicky4
	/varset mcHUDDSClicky4 ${Macro.Return}

	/call FindResistClicky
	/varset mcHUDResistClicky ${Macro.Return}

	/call FindHasteClicky
	/varset mcHUDHasteClicky ${Macro.Return}

	/call FindOverHasteClicky
	/varset mcHUDOverHasteClicky ${Macro.Return}

	/call FindACClicky
	/varset mcHUDACClicky ${Macro.Return}


	|XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	|XXXX Adds aliases to the script
	|XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	/squelch /alias /shrinkgroup /echo shrinkgroup
	/squelch /alias /shrinkself /echo shrinkself
	/squelch /alias /makepulleritem /echo makepulleritem
	/squelch /alias /mount /echo clickmount
	/squelch /alias /illusion /echo illusion
	/squelch /alias /charge /echo charge

	|Installs class specific aliases
	/squelch /alias /rez /echo rez

	|Installs command to toggle module ON/OFF
	/squelch /alias /clickies /echo clickies

	/echo gimp_clickies.inc initialized
	/if (${mcHUDshrinker.Length}) /echo * Shrinker: ${mcHUDshrinker}
	| /if (${mcHUDmount.Length}) /echo * Mount: ${mcHUDmount}
	/if (${mcHUDmanaregenClicky.Length}) /echo * Manaregen clicky: ${mcHUDmanaregenClicky}
	/if (${mcHUDmanapoolClicky.Length}) /echo * Mana pool clicky: ${mcHUDmanapoolClicky}
	/if (${mcHUDsummonPullItem.Length}) /echo * Summon puller item: ${mcHUDsummonPullItem}
	| /if (${ClickyIllusionName.Length}>1) /echo * Illusion clicky: ${ClickyIllusionName} (spell ${FindItem[=${ClickyIllusionName}].Spell.Name})

	/if (${useClickyAttackBuff} && ${mcHUDAttackClicky1.Length}) /echo * Attack1 clicky: ${mcHUDAttackClicky1}
	/if (${useClickyAttackBuff} && ${mcHUDAttackClicky3.Length}) /echo * Attack3 clicky: ${mcHUDAttackClicky3}
	/if (${useClickyAttackBuff} && ${mcHUDAttackClicky5.Length}) /echo * Attack5 clicky: ${mcHUDAttackClicky5}
	/if (${useClickyDS} && ${mcHUDDSClicky2.Length}) /echo * DS2 clicky: ${mcHUDDSClicky2}
	/if (${useClickyDS} && ${mcHUDDSClicky4.Length}) /echo * DS4 clicky: ${mcHUDDSClicky4}
	/if (${useClickyResists} && ${mcHUDResistClicky.Length}) /echo * Resist clicky: ${mcHUDResistClicky}
	/if (${useClickyHaste} && ${mcHUDHasteClicky.Length}) /echo * Haste clicky: ${mcHUDHasteClicky}
	/if (${useClickyHaste} && ${mcHUDOverHasteClicky.Length}) /echo * Overhaste clicky: ${mcHUDOverHasteClicky}
	/if (${useClickyAC} && ${mcHUDACClicky.Length}) /echo * AC clicky: ${mcHUDACClicky}
/return

Sub DoClickies
	|Declarations of clickies:
	/if (!${gimp_clickies_loaded}) /call Init_Clickies
	/if (!${gimp_clickies_activated}) /return

	|Time to check if the player is busy with something, then cancel (chest item unequipped = assume you are naked)
	/if (${Me.Invis} || ${Me.Feigning} || ${Me.Moving} || ${Me.AFK} || ${Cursor.ID} || ${Me.Casting.ID} || ${Window[RespawnWnd].Open} || ${Window[SpellBookWnd].Open} || ${Window[BigBankWnd].Open} || ${Window[LootWnd].Open} || ${Window[TradeWnd].Open} || ${Window[MerchantWnd].Open} || ${Window[TradeskillWnd].Open} || ${Window[BarterWnd].Open} || !${InvSlot[chest].Item.ID}) /return

	/declare wasISitting	int local	0
	/if (${Me.Sitting}) /varset wasISitting 1


	|duration 100 = 10 minutes
	|fixme: make this a ini section?
	/if (${Zone.Name.Equal["The Plane of Knowledge"]} || ${Zone.Name.Equal["The Plane of Tranquility"]} || ${Zone.Name.Equal["The Nexus"]} || ${Zone.Name.Equal["The Bazaar"]} || ${Zone.Name.Equal["Guild Lobby"]} || ${Zone.Name.Equal["Guild Hall"]}) /return

	/declare ZoneOutdoors int local ${Ini[ZONEINFO_FILE, ZoneInfo, ${Zone.ShortName}_Outdoors, -1]}
	/if (${ZoneOutdoors} == -1) {
		/echo # Zone ${Zone.ShortName} data missing in INI, saving new data
		/ini "ZONEINFO_FILE" "ZoneInfo" "${Zone.ShortName}_Outdoors" "???"

		|If i have a levitate buff on, zone allows levitate, right :)
		/if (${Me.Buff["Spirit of Eagle"].ID} || ${Me.Buff["Flight of Eagles"].ID} || ${Me.Buff["Dead Men Floating"].ID} || ${Me.Buff["Levitation"].ID}) {
			/ini "ZONEINFO_FILE" "ZoneInfo" "${Zone.ShortName}_Levitate" "1"
		} else {
			/ini "ZONEINFO_FILE" "ZoneInfo" "${Zone.ShortName}_Levitate" "???"
		}
	}

	|Only refreshes if zone is marked as outdoor zone in INI_FILE under ZoneInfo section
	/if (${useClickyJboots} && ${ZoneOutdoors} && (${Me.FreeBuffSlots} > 2)) {
		/call BuffedRunSpeed
		/if (${Macro.Return} == 0) {
			/if (${FindItem[=Journeyman's Boots].InvSlot} && ${Me.Buff["JourneymanBoots"].Duration} < 40) {
				/echo Refreshing jboots
				/call Cast "Journeyman's Boots" item
				/goto :DoneRefreshingClickies
			}
			/if (${FindItem[=Fabled Journeyman's Boots].InvSlot} && ${Me.Buff["Bootstrutter's Blessing"].Duration} < 40) {
				/echo Refreshing jboots
				/call Cast "Fabled Journeyman's Boots" item
				/goto :DoneRefreshingClickies
			}
		}
	}

	/if (${useClickyDS} && ${mcHUDDSClicky2.Length} && (${Me.FreeBuffSlots} > 1)) {
		/call BuffedDS2
		/if (${Me.Buff["${Macro.Return}"].Duration} < 15) {
			/echo Refreshing DS2 clicky
			/call Cast "${mcHUDDSClicky2}" item
			|/goto :DoneRefreshingClickies
		}
	}
		
	/if (!${Raid.Members} && ${useClickyDS} && ${mcHUDDSClicky4.Length} && (${Me.FreeBuffSlots} > 1)) {
		/call BuffedDS4
		/if ((${Me.Buff["${Macro.Return}"].Duration} < 15)) {
			/echo Refreshing DS4 clicky
			/call Cast "${mcHUDDSClicky4}" item
			|/goto :DoneRefreshingClickies
		}
	}

	/if (${mcHUDmanaregenClicky.Length}) {
		/call BuffedManaRegen
		/if ((${Me.Buff["${Macro.Return}"].Duration} < 15) && !${Me.Combat}) {
			/echo Refreshing manaregen clicky, duration: ${Me.Buff["${Macro.Return}"].Duration}
			/call Cast "${mcHUDmanaregenClicky}" item
			/goto :DoneRefreshingClickies
		}
	}

	/if (${mcHUDmanapoolClicky.Length}) {
		/call BuffedManaPool
		/if ((${Me.Buff["${Macro.Return}"].Duration} < 15) && !${Me.Combat}) {
			/echo Refreshing mana pool clicky
			/call Cast "${mcHUDmanapoolClicky}" item
			/goto :DoneRefreshingClickies
		}
	}

	/if (${useClickyResists} && ${mcHUDResistClicky.Length} && (${Me.FreeBuffSlots} > 1)) {
		/call BuffedResists
		/if ((${Me.Buff["${Macro.Return}"].Duration} < 15) && !${Me.Combat}) {
			/echo Refreshing resist clicky
			/call Cast "${mcHUDResistClicky}" item
			|/goto :DoneRefreshingClickies
		}
	}

	|Refresh Slot1 attack buffs
	/if (${useClickyAttackBuff} && ${mcHUDAttackClicky1.Length} && (${Me.FreeBuffSlots} > 1)) {
		/call BuffedAttack1
		/if (${Macro.Return} == 0 && !${Me.Combat}) {
			/echo Refreshing Attack1 clicky
			/call Cast "${mcHUDAttackClicky1}" item
			/goto :DoneRefreshingClickies
		}
	}

	|Refresh Slot3 attack buffs
	/if (${useClickyAttackBuff} && ${mcHUDAttackClicky3.Length} && (${Me.FreeBuffSlots} > 1)) {
		/call BuffedAttack3
		/if (${Macro.Return} == 0 && !${Me.Combat}) {
			/echo Refreshing Attack3 clicky
			/call Cast "${mcHUDAttackClicky3}" item
			/goto :DoneRefreshingClickies
		}
	}

	|Refresh Slot5 attack buffs
	/if (${useClickyAttackBuff} && ${mcHUDAttackClicky5.Length} && (${Me.FreeBuffSlots} > 1)) {
		/call BuffedAttack5
		/if (${Macro.Return} == 0 && !${Me.Combat}) {
			/echo Refreshing Attack5 clicky
			/call Cast "${mcHUDAttackClicky5}" item
			/goto :DoneRefreshingClickies
		}
	}

	/if (${mcHUDhpbuffClicky.Length} && (${Me.FreeBuffSlots} > 1)) {
		/call BuffedHP
		/if ((${Me.Buff["${Macro.Return}"].Duration} < 30) && !${Me.Combat}) {
			/echo Refreshing hp buff clicky
			/call Cast "${mcHUDhpbuffClicky}" item
			/goto :DoneRefreshingClickies
		}
	}

	/if (${useClickyHaste} && ${mcHUDHasteClicky.Length}) {
		/call BuffedHaste
		/if ((${Me.Buff["${Macro.Return}"].Duration} < 10) && !${Me.Combat}) {
			/if ((${Target.ID} && ${Target.Type.Equal[NPC]} && (${Me.TargetOfTarget.ID} != ${Me.ID})) || (${Target.ID} == ${Me.ID}) || !${Target.ID} || (${Target.Distance} > 100)) {
				/echo Refreshing haste clicky
				/call Cast "${mcHUDHasteClicky}" item
				/goto :DoneRefreshingClickies
			}
		}
	}

	/if (${useClickyHaste} && !${Raid.Members} && ${mcHUDOverHasteClicky.Length}) {
		/call BuffedOverHaste
		/if ((${Me.Buff["${Macro.Return}"].Duration} < 10) && !${Me.Combat}) {
			/if ((${Target.ID} && ${Target.Type.Equal[NPC]} && (${Me.TargetOfTarget.ID} != ${Me.ID})) || (${Target.ID} == ${Me.ID}) || !${Target.ID} || (${Target.Distance} > 100)) {
				/echo Refreshing overhaste clicky
				/call Cast "${mcHUDOverHasteClicky}" item
				/goto :DoneRefreshingClickies
			}
		}
	}

	/if (${useClickyAC} && ${mcHUDACClicky.Length}) {
		/call BuffedAC
		/if ((${Me.Buff["${Macro.Return}"].Duration} < 30) && !${Me.Combat}) {
			/echo Refreshing AC clicky
			/call Cast "${mcHUDACClicky}" item
			/goto :DoneRefreshingClickies
		}
	}

	|XXXXXXXXXXXXXXXXXXXXXXXXX
	|XX below is more special clickies handling
	/if (${useClickyResists} && !${Me.Combat} && (!${Target.ID} || ${Target.ID} == ${Me.ID}) && (${Me.FreeBuffSlots} > 1)) {
		/if (${FindItem[=Bangle of Disease Warding].InvSlot} && !${Me.Buff["Talisman of the Tribunal"].ID} && !${Me.Buff["Talisman of Jasinth"].ID} && (${Me.Buff["Resist Disease"].Duration} < 20)) {
			/echo Refreshing Resist Disease
			/target myself
			/call Cast "Bangle of Disease Warding" item
			/goto :DoneRefreshingClickies
		}
	}

	/if (${useClickyBracerOfTheHidden} && !${Raid.Members} && (!${Target.ID} || ${Target.ID} == ${Me.ID})) {
		/if (${FindItem[=Bracer of the Hidden].InvSlot} && ${Me.Buff["See Invisible"].Duration} < 20) {
			/echo Refreshing See Invisible
			/target myself
			/call Cast "Bracer of the Hidden" item
			/goto :DoneRefreshingClickies
		}
	}

	|* /if ((${ClickyIllusionName.Length}>1) && ${ClickyIllusionActive} && !${Raid.Members} && (${Me.FreeBuffSlots} > 1)) {
		/if (${FindItem[=${ClickyIllusionName}].InvSlot} && ${Me.Buff["${FindItem[=${ClickyIllusionName}].Spell.Name}"].Duration} < 20) {
			/echo Refreshing illusion
			/call Cast "${ClickyIllusionName}" item
			/goto :DoneRefreshingClickies
		}
	} *|

	/if (${useClickyVoiceOfTheSerpent}) {
		/if (${FindItem[=Voice of the Serpent].InvSlot} && (${Me.Buff["Resonance"].Duration} < 5)) {
			/echo Refreshing Voice of the Serpent clicky
			/call Cast "Voice of the Serpent" item
			/goto :DoneRefreshingClickies
		}
	}
	
	/if (${useClickyRapierOfSomberNotes}) {
		/if (${FindItem[=Rapier of Somber Notes].InvSlot} && (${Me.Buff["Symphony of Battle"].Duration} < 5)) {
			/echo Refreshing Rapier of Somber Notes clicky
			/call Cast "Rapier of Somber Notes" item
			/goto :DoneRefreshingClickies
		}
	}
	
	|Druid AA DS refresh
	/if (!${Raid.Members} && !${Me.Moving} && !${Target.ID} && (${Me.FreeBuffSlots} > 2) && ${Me.AltAbilityReady["Wrath of the Wild"]} && !${Me.Buff["Wrath of the Wild"].ID}) {
		/echo # Refreshing druid AA DS
		/target myself
		/if (${Me.Sitting}) {
			/stand
			/delay 5
		}
		/docommand /alt activate 170
		/delay 4s
	}

	|/echo Done refreshing clickies
	:DoneRefreshingClickies
	/varset gimp_clickies_timer 60s
	/if (${wasISitting} && !${Me.Sitting} && !${Me.Moving} && !${Me.Casting.ID}) /sit
/return

Sub FindManaregenClicky
	/declare item string local
	|+2 mana per tick
	/if (${FindItem[=Earring of Spirited Mind].InvSlot})			 /varset item Earring of Spirited Mind
	/if (${FindItem[=Moonshadow Cloak].InvSlot}) 					/varset item Moonshadow Cloak
	/if (${FindItem[=Talisman of the Burrower].InvSlot}) 			/varset item Talisman of the Burrower
	/if (${FindItem[=Twilight Shroud].InvSlot}) 					/varset item Twilight Shroud
	/if (${FindItem[=Phylactery].InvSlot}) 							/varset item Phylactery

	|+ 5 mana per tick
	/if (${FindItem[=Glyphed Greaves of Conflict].InvSlot})			/varset item Glyphed Greaves of Conflict
	/if (${FindItem[=Discordant Leggings of Betrayal].InvSlot})		/varset item Discordant Leggings of Betrayal
	/if (${FindItem[=Chaos-Imbued Leather Leggings].InvSlot})		/varset item Chaos-Imbued Leather Leggings
	/if (${FindItem[=Muramite's Shield of Legend].InvSlot})			/varset item Muramite's Shield of Legend
	/if (${FindItem[=Celestial Cloak].InvSlot})						/varset item Celestial Cloak
	/if (${FindItem[=Irestone of Sharp Sight].InvSlot})				/varset item Irestone of Sharp Sight
	/if (${FindItem[=Lightning Blackened Chain Greaves].InvSlot})	/varset item Lightning Blackened Chain Greaves
	/if (${FindItem[=Shawl of Awakenings].InvSlot})					/varset item Shawl of Awakenings
	/if (${FindItem[=Aegis of Discordant Matter].InvSlot})			/varset item Aegis of Discordant Matter
	/if (${FindItem[=Koadic's Robe of Heightened Focus].InvSlot})	/varset item Koadic's Robe of Heightened Focus

	|+7 mana per tick
	/if (${FindItem[=Pendant of Discord].InvSlot})					/varset item Pendant of Discord
	/if (${FindItem[=Lana's Crystal Shield].InvSlot})				/varset item Lana's Crystal Shield
	
	|+8 mana per tick
	/if (${FindItem[=Earring of Dragonkin].InvSlot})				/varset item Earring of Dragonkin
	/if (${FindItem[=Gem-Inlaid Ritual Cloak].InvSlot})				/varset item Gem-Inlaid Ritual Cloak
	/if (${FindItem[=Girdle of Efficiency].InvSlot})				/varset item Girdle of Efficiency
	/if (${FindItem[=Shadowed Amulet of Anarchy].InvSlot})			/varset item Shadowed Amulet of Anarchy
	/if (${FindItem[=Shawl of Eternal Forces].InvSlot})				/varset item Shawl of Eternal Forces
	/if (${FindItem[=Visor of Distorted Vision].InvSlot})			/varset item Visor of Distorted Vision

	|+9 mana per tick
	/if (${FindItem[=Earring of Pain Deliverance].InvSlot})			/varset item Earring of Pain Deliverance

	|+10 mana per tick
	/if (${FindItem[=Earring of Dragonkin].InvSlot})						/varset item Earring of Dragonkin
	/if (${FindItem[=Vermilion Orb of Clairvoyance].InvSlot})		/varset item Vermilion Orb of Clairvoyance
	/if (${FindItem[=Buckler of Unliving].InvSlot})							/varset item Buckler of Unliving
/return ${item}

Sub FindManapoolClicky
	/declare item string local
	|+250 mana pool
	/if (${FindItem[=Crimson Ring of Judgment].InvSlot})			/varset item Crimson Ring of Judgment
	/if (${FindItem[=Shield of Mental Fortitude].InvSlot})			/varset item Shield of Mental Fortitude
	/if (${FindItem[=Silver Band of Secrets].InvSlot})				/varset item Silver Band of Secrets

	|+400 mana pool
	/if (${FindItem[=Muramite Signet Orb].InvSlot})					/varset item Muramite Signet Orb
	/if (${FindItem[=Tentric's Band of Endless Wisdom].InvSlot})	/varset item Tentric's Band of Endless Wisdom
	/if (${FindItem[=Eye of Dreams].InvSlot})										/varset item Eye of Dreams
	/if (${FindItem[=Grand Illsalin Robe].InvSlot})						/varset item Grand Illsalin Robe

	|+450 mana pool
	/if (${FindItem[=Xxeric's Matted-Fur Mask].InvSlot})			/varset item Xxeric's Matted-Fur Mask

	|+500 mana pool "Salik's Expansive Mind"
	|http://lucy.allakhazam.com/spell.html?id=6369&source=Live
	|no discovered items yet
/return ${item}


Sub FindHPBuffClicky
	/declare item string local

	|+90 hp - Form of Endurance I
	/if (${FindItem[=Trickster's Chalice].InvSlot})				/varset item Trickster's Chalice
	/if (${FindItem[=Blood Rage Choker].InvSlot})					/varset item Blood Rage Choker
	/if (${FindItem[=Supersonic Eargear].InvSlot})				/varset item Supersonic Eargear

	|+180 hp - Form of Endurance
	/if (${FindItem[=Veil of the Inferno].InvSlot})					/varset item Veil of the Inferno

	|+270 hp - Form of Endurance III
	/if (${FindItem[=Ring of the Beast].InvSlot})						/varset item Ring of the Beast

	/if (${FindItem[=Earring of Frozen Poison].InvSlot})			/varset item Earring of Frozen Poison
	/if (${FindItem[=Fractured Talon of the Clouded Dragon].InvSlot})		/varset item Fractured Talon of the Clouded Dragon
	/if (${FindItem[=Pristine Witheran Hide Drum].InvSlot})		/varset item Pristine Witheran Hide Drum

	/if (${FindItem[=Salt-Encrusted Eyepatch].InvSlot})			/varset item Salt-Encrusted Eyepatch
	/if (${FindItem[=Salty Chatty Eyepatch].InvSlot})				/varset item Salty Chatty Eyepatch
	/if (${FindItem[=Borilbeard, the Eyepatch].InvSlot})		/varset item Borilbeard, the Eyepatch

	|+360 hp - Form of Endurance IV
	/if (${FindItem[=Forgotten Artist's Mesh Wristwraps].InvSlot})		/varset item Forgotten Artist's Mesh Wristwraps
	/if (${FindItem[=Mayong's Sanguine Cloak].InvSlot})		/varset item Mayong's Sanguine Cloak
/return ${item}

Sub FindMountClicky
	/declare item string local
	
	/if (${FindItem[=Collapsable Roboboar].InvSlot})		/varset item Collapsable Roboboar
	/if (${FindItem[=Giant Red Drum].InvSlot})		/varset item Giant Red Drum
	/if (${FindItem[=Large Red Drum].InvSlot})		/varset item Large Red Drum
	/if (${FindItem[=Red Drum].InvSlot})			/varset item Red Drum
	/if (${FindItem[=Small Red Drum].InvSlot})		/varset item Small Red Drum
	/if (${FindItem[=Giant Green Drum].InvSlot})	/varset item Giant Green Drum
	/if (${FindItem[=Large Green Drum].InvSlot})	/varset item Large Green Drum
	/if (${FindItem[=Green Drum].InvSlot})			/varset item Green Drum
	/if (${FindItem[=Small Green Drum].InvSlot})	/varset item Small Green Drum
	/if (${FindItem[=Glowing Black Drum].InvSlot})	/varset item Glowing Black Drum
	/if (${FindItem[=Giant Black Drum].InvSlot})	/varset item Giant Black Drum
	/if (${FindItem[=Large Black Drum].InvSlot})	/varset item Large Black Drum
	/if (${FindItem[=Black Drum].InvSlot})			/varset item Black Drum
	/if (${FindItem[=Small Black Drum].InvSlot})	/varset item Small Black Drum
	/if (${FindItem[=Glowing White Drum].InvSlot})	/varset item Glowing White Drum
	/if (${FindItem[=Giant White Drum].InvSlot})	/varset item Giant White Drum
	/if (${FindItem[=Large White Drum].InvSlot})	/varset item Large White Drum
	/if (${FindItem[=White Drum].InvSlot})			/varset item White Drum
	/if (${FindItem[=Small White Drum].InvSlot})	/varset item Small White Drum
	/if (${FindItem[=Black Chain Bridle].InvSlot})			/varset item Black Chain Bridle
	/if (${FindItem[=Black Leather Bridle].InvSlot})		/varset item Black Leather Bridle
	/if (${FindItem[=Black Ornate Chain Bridle].InvSlot})	/varset item Black Ornate Chain Bridle
	/if (${FindItem[=Black Rope Bridle].InvSlot})			/varset item Black Rope Bridle
	/if (${FindItem[=Black Silken Bridle].InvSlot})			/varset item Black Silken Bridle
	/if (${FindItem[=Brown Chain Bridle].InvSlot})			/varset item Brown Chain Bridle
	/if (${FindItem[=Brown Leather Bridle].InvSlot})		/varset item Brown Leather Bridle
	/if (${FindItem[=Brown Ornate Chain Bridle].InvSlot})	/varset item Brown Ornate Chain Bridle
	/if (${FindItem[=Brown Rope Bridle].InvSlot})			/varset item Brown Rope Bridle
	/if (${FindItem[=Brown Silken Bridle].InvSlot})			/varset item Brown Silken Bridle
	/if (${FindItem[=Tan Chain Bridle].InvSlot})			/varset item Tan Chain Bridle
	/if (${FindItem[=Tan Leather Bridle].InvSlot})			/varset item Tan Leather Bridle
	/if (${FindItem[=Tan Rope Bridle].InvSlot})				/varset item Tan Rope Bridle
	/if (${FindItem[=Tan Silken Bridle].InvSlot})			/varset item Tan Silken Bridle
	/if (${FindItem[=White Chain Bridle].InvSlot})			/varset item  White Chain Bridle
	/if (${FindItem[=White Leather Bridle].InvSlot})		/varset item  White Leather Bridle
	/if (${FindItem[=White Ornate Chain Bridle].InvSlot})	/varset item  White Ornate Chain Bridle
	/if (${FindItem[=White Rope Bridle].InvSlot})			/varset item  White Rope Bridle
	/if (${FindItem[=White Silken Bridle].InvSlot})			/varset item  White Silken Bridle
	/if (${FindItem[=Black Chain Bridle and Barding].InvSlot})			/varset item Black Chain Bridle and Barding
	/if (${FindItem[=Black Leather Bridle and Barding].InvSlot})		/varset item Black Leather Bridle and Barding
	/if (${FindItem[=Black Ornate Chain Bridle and Barding].InvSlot})	/varset item Black Ornate Chain Bridle and Barding
	/if (${FindItem[=Black Rope Bridle and Barding].InvSlot})			/varset item Black Rope Bridle and Barding
	/if (${FindItem[=Black Silken Bridle and Barding].InvSlot})			/varset item Black Silken Bridle and Barding
	/if (${FindItem[=Brown Chain Bridle and Barding].InvSlot})			/varset item Brown Chain Bridle and Barding
	/if (${FindItem[=Brown Leather Bridle and Barding].InvSlot})		/varset item Brown Leather Bridle and Barding
	/if (${FindItem[=Brown Ornate Chain Bridle and Barding].InvSlot})	/varset item Brown Ornate Chain Bridle and Barding
	/if (${FindItem[=Brown Rope Bridle and Barding].InvSlot})			/varset item Brown Rope Bridle and Barding
	/if (${FindItem[=Brown Silken Bridle and Barding].InvSlot})			/varset item Brown Silken Bridle and Barding
	/if (${FindItem[=Tan Chain Bridle and Barding].InvSlot})			/varset item Tan Chain Bridle and Barding
	/if (${FindItem[=Tan Leather Bridle and Barding].InvSlot})			/varset item Tan Leather Bridle and Barding
	/if (${FindItem[=Tan Rope Bridle and Barding].InvSlot})				/varset item Tan Rope Bridle and Barding
	/if (${FindItem[=Tan Silken Bridle and Barding].InvSlot})			/varset item Tan Silken Bridle and Barding
	/if (${FindItem[=White Chain Bridle and Barding].InvSlot})			/varset item  White Chain Bridle and Barding
	/if (${FindItem[=White Leather Bridle and Barding].InvSlot})		/varset item  White Leather Bridle and Barding
	/if (${FindItem[=White Ornate Chain Bridle and Barding].InvSlot})	/varset item  White Ornate Chain Bridle and Barding
	/if (${FindItem[=White Rope Bridle and Barding].InvSlot})			/varset item  White Rope Bridle and Barding
	/if (${FindItem[=White Silken Bridle and Barding].InvSlot})			/varset item  White Silken Bridle and Barding
/return ${item}

Sub FindSummonPullItem
	/declare item string local
	/if (${FindItem[=A Dusty Soriz Worker Pouch].InvSlot})	/varset item A Dusty Soriz Worker Pouch
	/if (${FindItem[=Bloody Ancille's Pouch].InvSlot})		/varset item Bloody Ancille's Pouch
	/if (${FindItem[=Ton Po's Mystical Pouch].InvSlot})		/varset item Ton Po's Mystical Pouch
/return ${item}


Sub FindShrinkClicky
	/declare item string local
	/if (${FindItem[=Ant's Potion].InvSlot})				/varset item Ant's Potion
	/if (${FindItem[=5 Dose Ant's Potion].InvSlot})			/varset item 5 Dose Ant's Potion
	/if (${FindItem[=10 Dose Ant's Potion].InvSlot})		/varset item 10 Dose Ant's Potion
	/if (${FindItem[=Anizok's Minimizing Device].InvSlot})	/varset item Anizok's Minimizing Device
	/if (${FindItem[=Bestial Sandals].InvSlot})				/varset item Bestial Sandals
	/if (${FindItem[=Cobalt Bracer].InvSlot})				/varset item Cobalt Bracer
	/if (${FindItem[=Wand of Imperceptibility].InvSlot})	/varset item Wand of Imperceptibility
	/if (${FindItem[=Bracelet of the Shadow Hive].InvSlot})	/varset item Bracelet of the Shadow Hive
	/if (${FindItem[=Earring of Diminutiveness].InvSlot})	/varset item Earring of Diminutiveness
	/if (${item.Equal[NULL]}) /echo WARNING: Couldn't find a shrink clicky in inventory
/return ${item}

Sub FindAttackClicky1
	/declare item string local
	|Find Slot 1 +ATK clicky

	|+10 atk
	/if (${FindItem[=Golden Star Slippers].InvSlot})					/varset item Golden Star Slippers
	/if (${FindItem[=Grand Master's Slippers].InvSlot})					/varset item Grand Master's Slippers
	/if (${FindItem[=Silver Moon Slippers].InvSlot})					/varset item Silver Moon Slippers
	/if (${FindItem[=White Lotus Slippers].InvSlot})					/varset item White Lotus Slippers
	/if (${FindItem[=Faded Skull Medallion].InvSlot}) 					/varset item Faded Skull Medallion
	/if (${FindItem[=Berserkers Ring].InvSlot})							/varset item Berserkers Ring
	/if (${FindItem[=Reaper's Ring].InvSlot})				 			/varset item Reaper's Ring
	/if (${FindItem[=Shrunken Goblin Skull Earring].InvSlot}) 			/varset item Shrunken Goblin Skull Earring
	/if (${FindItem[=Spaulders of Battle Rage].InvSlot})				/varset item Spaulders of Battle Rage
	/if (${FindItem[=Sword of Pain].InvSlot}) 							/varset item Sword of Pain

	|+20 atk
	/if (${FindItem[=Jagged Sword of the Dragon Slayer].InvSlot}) 		/varset item Jagged Sword of the Dragon Slayer
	/if (${FindItem[=Totem of Elitist Rites].InvSlot}) 					/varset item Totem of Elitist Rites
	/if (${FindItem[=Crimson Bauble of Rites].InvSlot})					/varset item Crimson Bauble of Rites
	/if (${FindItem[=Fabled Shrunken Goblin Skull Earring].InvSlot})	/varset item Fabled Shrunken Goblin Skull Earring
/return ${item}

Sub FindAttackClicky3
	/declare item string local
	|Find Slot 3 +ATK clicky

	|+10 atk
	/if (${FindItem[=Fearsome Shield].InvSlot})						/varset item Fearsome Shield
	/if (${FindItem[=Pauldrons of Ferocity].InvSlot})			/varset item Pauldrons of Ferocity
/return ${item}

Sub FindAttackClicky5
	/declare item string local
	|Find Slot 5 +ATK clicky

	|+5 atk
	/if (${FindItem[=Rluas's Chromatic Ring].InvSlot})			/varset item Rluas's Chromatic Ring
	/if (${FindItem[=Fitz's Chromatic Earring].InvSlot})		/varset item Fitz's Chromatic Earring
	/if (${FindItem[=Sarthis's Chromatic Hoop].InvSlot})		/varset item Sarthis's Chromatic Hoop

	|+10 atk
	/if (${FindItem[=Ring of Dain Frostreaver IV].InvSlot})		/varset item Ring of Dain Frostreaver IV

	|+25 atk
	/if (${FindItem[=Irestone Band of Rage].InvSlot})			/varset item Irestone Band of Rage
	/if (${FindItem[=Serrated Dart of Energy].InvSlot})			/varset item Serrated Dart of Energy

	|+40 atk
	/if (${FindItem[=Veil of Intense Evolution].InvSlot})		/varset item Veil of Intense Evolution
/return ${item}

Sub FindDSClicky2
	/declare item string local
	|Slot 2 DS buffs

	|+4 DS
	/if (${FindItem[=Runed Scout's Breastplate].InvSlot})			/varset item Runed Scout's Breastplate
	/if (${FindItem[=Forest Stalker's Breastplate].InvSlot})	/varset item Forest Stalker's Breastplate
	/if (${FindItem[=Golden Leaf Breastplate].InvSlot})				/varset item Golden Leaf Breastplate

	|+5 DS
	/if (${FindItem[=Sarthis's Chromatic Hoop].InvSlot})			/varset item Sarthis's Chromatic Hoop
	/if (${FindItem[=Fitz's Chromatic Earring].InvSlot})			/varset item Fitz's Chromatic Earring
	/if (${FindItem[=Rluas's Chromatic Ring].InvSlot})				/varset item Rluas's Chromatic Ring
	/if (${FindItem[=Crown of the Tempest Guard].InvSlot})		/varset item Crown of the Tempest Guard
	/if (${FindItem[=Gauntlets of Dark Embers].InvSlot})			/varset item Gauntlets of Dark Embers
	/if (${FindItem[=Leggings of the Fiery Star].InvSlot})		/varset item Leggings of the Fiery Star
	/if (${FindItem[=Chestplate of Fiery Might].InvSlot})			/varset item Chestplate of Fiery Might
	/if (${FindItem[=Leggings of Fiery Might].InvSlot})				/varset item Leggings of Fiery Might

	|+8 DS
	/if (${FindItem[=Velium Coldain Insignia Ring].InvSlot})	/varset item Velium Coldain Insignia Ring
	/if (${FindItem[=Coldain Hero's Insignia Ring].InvSlot})	/varset item Coldain Hero's Insignia Ring

	|+10 DS
	/if (${FindItem[=Ring of Dain Frostreaver IV].InvSlot})		/varset item Ring of Dain Frostreaver IV
	
	|+18 DS
	/if (${FindItem[=Veil of Flames].InvSlot})								/varset item Veil of Flames
	/if (${FindItem[=Lute of the Mischiefmaker].InvSlot})			/varset item Lute of the Mischiefmaker
	
	|+20 DS
	/if (${FindItem[=Cloak of Retribution].InvSlot})					/varset item Cloak of Retribution
	
	|+25 DS
	/if (${FindItem[=Mask of Lament].InvSlot})								/varset item Mask of Lament
/return ${item}


Sub FindDSClicky4
	/declare item string local
	|Slot 4 DS buffs

	|+4 DS
	/if (${FindItem[=Cloak of Thorns].InvSlot})								/varset item Cloak of Thorns
	/if (${FindItem[=Crystallized Serpent Eye].InvSlot})			/varset item Crystallized Serpent Eye
	/if (${FindItem[=Orb of Captured Sunlight].InvSlot})			/varset item Orb of Captured Sunlight
	|Ring of Thunderous Spirits	is this type aswell but just 2 charges

	|+6 DS
	/if (${FindItem[=Bone Chill Shield].InvSlot})							/varset item Bone Chill Shield
	/if (${FindItem[=Cloak of Icy Shadows].InvSlot})					/varset item Cloak of Icy Shadows
	/if (${FindItem[=Mantle of the Accused].InvSlot})					/varset item Mantle of the Accused

	|+10 DS
	/if (${FindItem[=Puresteel Mantle].InvSlot})							/varset item Puresteel Mantle

	|+15 DS
	/if (${FindItem[=Girdle of Intense Durability].InvSlot})	/varset item Girdle of Intense Durability

	|+21 DS
	/if (${FindItem[=Cerulean Painted Veil].InvSlot})					/varset item Cerulean Painted Veil

	|+24 DS
	/if (${FindItem[=Pauldron of Dark Auspices].InvSlot})			/varset item Pauldron of Dark Auspices

	|+27 DS
	/if (${FindItem[=Stormeye Band].InvSlot})									/varset item Stormeye Band
/return ${item}

Sub FindResistClicky
	/declare item string local
	|Slot 1 resist buffs
	
	|+10 all resists "Soul Energy"
	/if (${FindItem[=Ring of Resistance].InvSlot})						/varset item Ring of Resistance
	/if (${FindItem[=Eriak's Ring of Guile].InvSlot})					/varset item Eriak's Ring of Guile
	/if (${FindItem[=Hoop of Fighting Focus].InvSlot})					/varset item Hoop of Fighting Focus
	/if (${FindItem[=Bladesoul's Spiritual Armguards].InvSlot})			/varset item Bladesoul's Spiritual Armguards
	/if (${FindItem[=Bladesoul's Spiritual Diadem].InvSlot})			/varset item Bladesoul's Spiritual Diadem
	
	|+15 all resists "Eternal Ward"
	/if (${FindItem[=Prismatic Ring of Resistance].InvSlot})			/varset item Prismatic Ring of Resistance
	/if (${FindItem[=Lavender Cloak of Destruction].InvSlot})			/varset item Lavender Cloak of Destruction

	|+20 all resists "Chaotic Ward"
	/if (${FindItem[=Necklace of the Steadfast Spirit].InvSlot})		/varset item Necklace of the Steadfast Spirit
	
	|+30 all resists "Unliving Ward"
	/if (${FindItem[=Ali-Ani, the Eyes of Mayong].InvSlot})						/varset item Ali-Ani, the Eyes of Mayong
	/if (${FindItem[=Doomstone Band of Seeing].InvSlot})						/varset item Doomstone Band of Seeing
	

	|below buff effects should stack with above if neither or one has Screech effect, see lucy blabla
	|but putting in same function for now until someone explains how they stack to me

	|+10 all resists "Form of Protection I"
	/if (${FindItem[=Dark Void Shoulders].InvSlot})									/varset item Dark Void Shoulders

	|+20 all resists "Form of Protection"
	/if (${FindItem[=Black Web Mantle].InvSlot})										/varset item Black Web Mantle
	/if (${FindItem[=Earring of Celestial Energy].InvSlot})					/varset item Earring of Celestial Energy
	/if (${FindItem[=Light Amphibian Hide Slippers].InvSlot})				/varset item Light Amphibian Hide Slippers

	|+30 all resists "Form of Protection III"
	/if (${FindItem[=Tainted Undershore Rock].InvSlot})							/varset item Tainted Undershore Rock

	|+40 all resists "Form of Protection IV"
	/if (${FindItem[=Wristguards of the Vampire Hunter].InvSlot})		/varset item Wristguards of the Vampire Hunter

	|+50 all resists "Aura of Pure Arcanum"
	/if (${FindItem[=Ancient Werewolf Skull].InvSlot})							/varset item Ancient Werewolf Skull
	/if (${FindItem[=Skull of the Den Lord].InvSlot})								/varset item Skull of the Den Lord
	/if (${FindItem[=Doomstone Band of Seeing].InvSlot})						/varset item The Skull of Den Lord Rakban
/return ${item}

Sub FindHasteClicky
	/declare item string local
	|Slot 1 haste clickies:

	|20% haste
	/if (${FindItem[=Eyepatch of Plunder].InvSlot})							/varset item Eyepatch of Plunder

	|Slot 3 haste clickies:
	|30% haste
	/if (${FindItem[=Belt of Marvelous Visions].InvSlot})				/varset item Belt of Marvelous Visions
	/if (${FindItem[=Stanos' Wicked Gauntlets].InvSlot})				/varset item Stanos' Wicked Gauntlets
	/if (${FindItem[=Jymall's Gloves of Frenzy].InvSlot})				/varset item Jymall's Gloves of Frenzy
	/if (${FindItem[=Kizrak's Gauntlets of Battle].InvSlot})		/varset item Kizrak's Gauntlets of Battle

	|50% haste
	/if (${FindItem[=Lizardscale Plated Girdle].InvSlot})				/varset item Lizardscale Plated Girdle
/return ${item}

Sub FindOverHasteClicky
	/declare item string local
	|Slot 1 overhaste clickies:
	|5% overhaste "Primal Guard"
	/if (${FindItem[=Prism of Dark Hues].InvSlot})					/varset item Prism of Dark Hues
	/if (${FindItem[=Silver Hoop of Speed].InvSlot})				/varset item Silver Hoop of Speed

	|8% overhaste "Taelosian Guard"
	/if (${FindItem[=Ilsin's Cloak].InvSlot})								/varset item Ilsin's Cloak
	/if (${FindItem[=Ring of Organic Darkness].InvSlot})		/varset item Ring of Organic Darkness
	
	|10% overhaste "Ancient Power"
	/if (${FindItem[=Symbol of the Overlord].InvSlot})			/varset item Symbol of the Overlord
/return ${item}

Sub FindACClicky
	/declare item string local

	|+27 AC "Form of Defense I"
	/if (${FindItem[=Bony Dark Web Ring].InvSlot})						/varset item Bony Dark Web Ring
	/if (${FindItem[=Shroud of the Fallen Defender].InvSlot})	/varset item Shroud of the Fallen Defender

	|+54 AC "Form of Defense"
	/if (${FindItem[=Band of Primordial Energy].InvSlot})				/varset item Band of Primordial Energy
	/if (${FindItem[=Executioner's Cincture].InvSlot})					/varset item Executioner's Cincture
	
	|+81 AC "Form of Defense III"
	/if (${FindItem[=Hanvar's Hoop].InvSlot})										/varset item Hanvar's Hoop
	/if (${FindItem[=Skull of Vishimtar].InvSlot})							/varset item Skull of Vishimtar
	
	|+108 AC "Form of Defense IV"
	/if (${FindItem[=Wristplates of the Fallen Saint].InvSlot})		/varset item Wristplates of the Fallen Saint
/return ${item}


#Event ClickMount				"[MQ2] clickmount"
Sub Event_ClickMount
	/if (!${mcHUDmount.Length}) /return
	/call Cast "${mcHUDmount}" item
/return

#Event RezClicky				"[MQ2] rez"
Sub Event_RezClicky
	/if (${Me.Class.Name.NotEqual["Cleric"]} || !${Target.ID} || ${Target.Type.NotEqual[Corpse]}) /return
	/echo Ressurecting ${Target.Name}
	/call Cast "Water Sprinkler of Nem Ankh" item
/return

#Event ShrinkSelf            "[MQ2] shrinkself"
Sub Event_ShrinkSelf
	/if (!${mcHUDshrinker.Length}) {
		/echo # ERROR: Cannot shrink self, No shrinker clicky found!
		/return
	}
	|ShrinkSelf code by Harden
	|Lets you Shrink yourself without trying to shrink everyone in group.
	|useage == /shrinkself
	
	/declare i int local 0
	/declare oldId int local 0
	
	/if (${Target.ID} && (${Target.ID} != ${Me.ID})) /varset oldId ${Target.ID}

	/echo # Shrinking Self

	|Note: When you are max shrinked, your Height == 2.00
	/target myself
	:Shrink
	/if ((${Me.Height} > 2.00)) {
		/call Cast "${mcHUDshrinker}" item
		/goto :Shrink
	}
	/echo # Done Shrinking

	/if (${oldId}) /target id ${oldId}
/return

#Event ShrinkGroup			"[MQ2] shrinkgroup"
Sub Event_ShrinkGroup
	/if (!${mcHUDshrinker.Length}) /return
	|Note: Not all shrink items work on targets
	|Bracelet of Shadow hive does. Wand of Impercibility doesnt.
	/declare i int local 0

	/echo Shrinking group
	/for i 0 to 5
		:Shrink
		/if ((${Group.Member[${i}].Height} > 2.00) && ${Group.Member[${i}].Type.Equal["PC"]}) {
			/if ((${Group.Member[${i}].Distance} < 100) && ${Group.Member[${i}].LineOfSight}) {
				|Skip MNK/BRD/SHD for pullage, and RNG for LoS problems
				/if (${Group.Member[${i}].Class.Name.NotEqual["Monk"]} && ${Group.Member[${i}].Class.Name.NotEqual["Bard"]} && ${Group.Member[${i}].Class.Name.NotEqual["Ranger"]}) {
					/target pc ${Group.Member[${i}]}
					/echo Shrinking ${Group.Member[${i}]} from ${Group.Member[${i}].Height}
					/call Cast "${mcHUDshrinker}" item
					/goto :Shrink
				} else {
					/echo Skipping ${Group.Member[${i}]} (${Group.Member[${i}].Class.ShortName})
				}
			} else {
				/echo Skipping ${Group.Member[${i}]}, OOR, size ${Group.Member[${i}].Height}
			}
		}
	/next i

	/echo Done shrinking
/return

#Event ClickyIllusion "[MQ2] illusion#1#"
Sub Event_ClickyIllusion(string Line, string name)
	/if (${name.Left[1].Equal[" "]}) /varset name ${name.Right[-1]}
	/if (${name.Equal[aon]}) /varset name Amulet of Necropotence
	/if (${name.Equal[woodelf]}) /varset name Crown of Deceit

	/if (${name.Length}) {
		|If i have item in inventory, and item has a spell click effect, then set it

		/if (${FindItem[=${name}].Spell.ID}) {
			/echo # Saving illusion item  ${FindItem[=${name}].Name} (spell ${FindItem[=${name}].Spell.Name}) to INI
			/ini "INI_FILE" "${Me.Name}" "ClickyIllusion" "${FindItem[=${name}].Name}"
			/varset ClickyIllusionName ${FindItem[=${name}].Name}
			/call Cast "${ClickyIllusionName}" item
		} else {
			/if (${FindItem[=${name}].ID} {
				/echo # /illusion error: The item ${name} is not a illusion clicky
			} else {
				/echo # /illusion error: The item ${name} was not found in your inventory
			}
		}
	} else {
		|Toggles casting of illusion clicky ON/OFF

		/if (${ClickyIllusionActive}) {
			/echo # Disabling illusion clicky
			/varset ClickyIllusionActive 0
			/ini "INI_FILE" "${Me.Name}" "ClickyIllusionActive" "0"
		} else {
			/echo # Enabling illusion clicky
			/varset ClickyIllusionActive 1
			/ini "INI_FILE" "${Me.Name}" "ClickyIllusionActive" "1"
			/if (!${Me.Buff["${FindItem[=${ClickyIllusionName}].Spell.Name}"].ID}) /call Cast "${ClickyIllusionName}" item
		}
	}
/return

|Casts the item clicky 5 times and auto inventories the summoned items
#Event MakePullerItem		"[MQ2] makepulleritem#1#"
Sub Event_MakePullerItem(string Line, int n)
	/if (!${mcHUDsummonPullItem.Length}) /return
	/declare i			int local 0
	/declare ammount	int local 5
	/if (${n}) /varset ammount ${n}

	/echo Clicking ${mcHUDsummonPullItem} ${ammount} times
	
	/for i 1 to ${ammount}
		/echo ${i}/${ammount}
		/call Cast "${mcHUDsummonPullItem}" item
		/delay 1s
		/autoinventory
	/next i
	/autoinventory
/return

Sub BuffedHaste
	/declare HasteBuff string local

	|clicky buffs:
	/if (${Me.Buff["Captain Nalots Quickening"].ID})	/varset HasteBuff Captain Nalots Quickening
	/if (${Me.Buff["Miraculous Visions"].ID})					/varset HasteBuff Miraculous Visions
	/if (${Me.Buff["Marvelous Visions"].ID})					/varset HasteBuff Marvelous Visions
	/if (${Me.Buff["Twitching Speed"].ID})						/varset HasteBuff Twitching Speed

	|Potions:
	/if (${Me.Buff["Elixir of Speed V"].ID})					/varset HasteBuff Elixir of Speed V
	/if (${Me.Buff["Elixir of Speed VI"].ID})					/varset HasteBuff Elixir of Speed VI
	/if (${Me.Buff["Elixir of Speed VII"].ID})				/varset HasteBuff Elixir of Speed VII
	/if (${Me.Buff["Elixir of Speed VIII"].ID})				/varset HasteBuff Elixir of Speed VIII
	/if (${Me.Buff["Elixir of Speed IX"].ID})					/varset HasteBuff Elixir of Speed IX
	/if (${Me.Buff["Elixir of Speed X"].ID})					/varset HasteBuff Elixir of Speed X

	|todo: incomplete list
	|spell buffs:
	/if (${Me.Buff["Quickness"].ID})							/varset HasteBuff Quickness
	/if (${Me.Buff["Celerity"].ID})								/varset HasteBuff Celerity
	/if (${Me.Buff["Alacrity"].ID})								/varset HasteBuff Alacrity
	/if (${Me.Buff["Talisman of Alacrity"].ID})		/varset HasteBuff Talisman of Alacrity
	/if (${Me.Buff["Talisman of Celerity"].ID})		/varset HasteBuff Talisman of Celerity
	/if (${Me.Buff["Visions of Grandeur"].ID})		/varset HasteBuff Visions of Grandeur
	/if (${Me.Buff["Swift like the Wind"].ID})		/varset HasteBuff Swift like the Wind
	/if (${Me.Buff["Aanya's Quickening"].ID})			/varset HasteBuff Aanya's Quickening
	/if (${Me.Buff["Vallon's Quickening"].ID})		/varset HasteBuff Vallon's Quickening
	/if (${Me.Buff["Speed of Vallon"].ID})				/varset HasteBuff Speed of Vallon
	/if (${Me.Buff["Speed of Ellowind"].ID})				/varset HasteBuff Speed of Ellowind
	/if (${Me.Buff["Speed of Ellowind Rk. II"].ID})				/varset HasteBuff Speed of Ellowind	Rk. II
	/if (${Me.Buff["Speed of Ellowind Rk. III"].ID})				/varset HasteBuff Speed of Ellowind	Rk. III
	/if (${Me.Buff["Hastening of Ellowind"].ID})				/varset HasteBuff Hastening of Ellowind	
	/if (${Me.Buff["Hastening of Ellowind Rk. II"].ID})				/varset HasteBuff Hastening of Ellowind	Rk. II
	/if (${Me.Buff["Hastening of Ellowind Rk. III"].ID})				/varset HasteBuff Hastening of Ellowind Rk. III	
	/if (${Me.Buff["Hastening of Salik"].ID})			/varset HasteBuff Hastening of Salik
	/if (${Me.Buff["Speed of Salik"].ID})					/varset HasteBuff Speed of Salik
	/if (${Me.Buff["Wonderous Rapidity"].ID})			/varset HasteBuff Wonderous Rapidity
/return ${HasteBuff}

Sub BuffedOverHaste
	/declare HasteBuff string local
	|clicky buffs:
	/if (${Me.Buff["Primal Guard"].ID})				/varset HasteBuff Primal Guard
	/if (${Me.Buff["Taelosian Guard"].ID})		/varset HasteBuff Taelosian Guard
	/if (${Me.Buff["Ancient Power"].ID})			/varset HasteBuff Ancient Power
/return ${HasteBuff}

Sub BuffedAC
	/declare ACBuff string local
	/if (${Me.Buff["Form of Defense"].ID})			/varset ACBuff Form of Defense
	/if (${Me.Buff["Form of Defense I"].ID})		/varset ACBuff Form of Defense I
	/if (${Me.Buff["Form of Defense III"].ID})	/varset ACBuff Form of Defense III
	/if (${Me.Buff["Form of Defense IV"].ID})		/varset ACBuff Form of Defense IV
/return ${ACBuff}

Sub BuffedRunSpeed
	/declare SpeedBuff int local 0
	/if (${Me.Buff["Spirit of the Shrew"].ID})			/varset SpeedBuff 30
	/if (${Me.Buff["Pack Shrew"].ID})					/varset SpeedBuff 30
	/if (${Me.Buff["Feral Pack"].ID})					/varset SpeedBuff 30
	/if (${Me.Buff["Spirit of Bih`Li"].ID})				/varset SpeedBuff 55
	/if (${Me.Buff["Spirit of Wolf"].ID})				/varset SpeedBuff 55
	/if (${Me.Buff["Pack Spirit"].ID})					/varset SpeedBuff 55
	/if (${Me.Buff["Share Wolf Form"].ID})				/varset SpeedBuff 60
	/if (${Me.Buff["Spirit of Eagle"].ID})				/varset SpeedBuff 60
	/if (${Me.Buff["Share Form of the Great Wolf"].ID})	/varset SpeedBuff 62
	/if (${Me.Buff["Flight of Eagles"].ID})				/varset SpeedBuff 65
/return ${SpeedBuff}

Sub BuffedAttack1
	/declare AttackBuff int local 0
	|Slot 1 +ATK buffs
	/if (${Me.Buff["Spiritual Brawn"].ID})					/varset AttackBuff 10
	/if (${Me.Buff["Firefist"].ID})									/varset AttackBuff 10
	/if (${Me.Buff["Grim Aura"].ID})								/varset AttackBuff 10
	/if (${Me.Buff["Aura of Rage"].ID})							/varset AttackBuff 20
	/if (${Me.Buff["Nortlav's Vengeance"].ID})			/varset AttackBuff 20
	/if (${Me.Buff["Strength of Nature"].ID})				/varset AttackBuff 25
	/if (${Me.Buff["Spiritual Vigor"].ID})					/varset AttackBuff 40
	/if (${Me.Buff["Celestial Tranquility"].ID})		/varset AttackBuff 40	
	/if (${Me.Buff["Spiritual Vitality"].ID})				/varset AttackBuff 52
	/if (${Me.Buff["Strength of Tunare"].ID})				/varset AttackBuff 60
	/if (${Me.Buff["Spiritual Vim"].ID})						/varset AttackBuff 62
	/if (${Me.Buff["Strength of the Hunter"].ID})		/varset AttackBuff 75
	/if (${Me.Buff["Strength of the Forest Stalker"].ID})		/varset AttackBuff 90
	/if (${Me.Buff["Strength of the Forest Stalker Rk. II"].ID})		/varset AttackBuff 94
	/if (${Me.Buff["Strength of the Forest Stalker Rk. III"].ID})		/varset AttackBuff 96
/return ${AttackBuff}

|fixme: return buff name instead so can check how long to refresh
Sub BuffedAttack3
	/declare AttackBuff int local 0
	|Slot 3 +ATK buffs
	/if (${Me.Buff["Ferine Avatar"].ID})						/varset AttackBuff 10
	/if (${Me.Buff["Champion"].ID})									/varset AttackBuff 10
	/if (${Me.Buff["Ancient Feral Avatar"].ID})			/varset AttackBuff 10
	/if (${Me.Buff["Primal Avatar"].ID})						/varset AttackBuff 10
	/if (${Me.Buff["Avatar"].ID})										/varset AttackBuff 10
	/if (${Me.Buff["Form of the Hunter"].ID})				/varset AttackBuff 10
	/if (${Me.Buff["Feral Pack"].ID})								/varset AttackBuff 10
	/if (${Me.Buff["Feral Form"].ID})								/varset AttackBuff 10
	/if (${Me.Buff["Form of the Great Wolf"].ID})		/varset AttackBuff 10
	/if (${Me.Buff["Night's Dark Terror"].ID})			/varset AttackBuff 10
	/if (${Me.Buff["Carnage"].ID})									/varset AttackBuff 10
	/if (${Me.Buff["Greater Wolf Form"].ID})				/varset AttackBuff 10
	/if (${Me.Buff["Boon of the Garou"].ID})				/varset AttackBuff 10
	/if (${Me.Buff["Illusion Scaled Wolf"].ID})			/varset AttackBuff 10
	/if (${Me.Buff["Wolf Form"].ID})								/varset AttackBuff 10
	/if (${Me.Buff["Share Wolf Form"].ID})					/varset AttackBuff 10
	/if (${Me.Buff["Force of Nature"].ID})					/varset AttackBuff 10
	/if (${Me.Buff["Aura of Battle"].ID})						/varset AttackBuff 10
/return ${AttackBuff}

Sub BuffedAttack5
	/declare AttackBuff int local 0
	|Slot 5 +ATK buffs
	/if (${Me.Buff["Chromatic Chaos"].ID})				/varset AttackBuff 5
	/if (${Me.Buff["Frostreaver's Blessing"].ID})		/varset AttackBuff 10
	/if (${Me.Buff["Savage Guard"].ID})					/varset AttackBuff 25
	/if (${Me.Buff["Furious Might"].ID})				/varset AttackBuff 40
/return ${AttackBuff}

Sub BuffedManaRegen
	/declare ManaregenBuff string local
	|Slot 8 mana regen buffs
	/if (${Me.Buff["Soul Clasp"].ID})									/varset ManaregenBuff Soul Clasp
	/if (${Me.Buff["Lunar Whispers"].ID})							/varset ManaregenBuff Lunar Whispers
	/if (${Me.Buff["Aura of Eternity"].ID})						/varset ManaregenBuff Aura of Eternity
	/if (${Me.Buff["Koadic's Heightened Focus"].ID})	/varset ManaregenBuff Koadic's Heightened Focus
	/if (${Me.Buff["Aura of Taelosia"].ID})						/varset ManaregenBuff Aura of Taelosia
	/if (${Me.Buff["Maelin's Methodical Mind"].ID})		/varset ManaregenBuff Maelin's Methodical Mind
	/if (${Me.Buff["Reyfin's Random Musings"].ID})		/varset ManaregenBuff Reyfin's Random Musings
	/if (${Me.Buff["Chaotic Enlightenment"].ID})			/varset ManaregenBuff Chaotic Enlightenment
	|Buff dont stack with hp-regen component of this manaregen buff: fixme-solve stacking better
	/if (${Me.Buff["Taelosian Guard"].ID})			/varset ManaregenBuff Taelosian Guard
/return ${ManaregenBuff}

Sub BuffedManaPool
	/declare ManapoolBuff string local
	|Slot 4 mana pool buffs
	/if (${Me.Buff["Koadic's Expansive Mind"].ID})	/varset ManapoolBuff Koadic's Expansive Mind
	/if (${Me.Buff["Maelin's Meditation"].ID})			/varset ManapoolBuff Maelin's Meditation
	/if (${Me.Buff["Reyfin's Racing Thoughts"].ID})	/varset ManapoolBuff Reyfin's Racing Thoughts
	/if (${Me.Buff["Salik's Expansive Mind"].ID})		/varset ManapoolBuff Salik's Expansive Mind
/return ${ManapoolBuff}

Sub BuffedDS2
	/declare DSBuff string local
	|Slot 2 DS buffs
	/if (${Me.Buff["Riftwind's Protection"].ID})		/varset DSBuff Riftwind's Protection
	/if (${Me.Buff["Force of Nature"].ID})					/varset DSBuff Force of Nature
	/if (${Me.Buff["Call of Earth"].ID})						/varset DSBuff Call of Earth
	/if (${Me.Buff["Chromatic Chaos"].ID})					/varset DSBuff Chromatic Chaos
	/if (${Me.Buff["Tempest Guard"].ID})						/varset DSBuff Tempest Guard
	/if (${Me.Buff["O'Keils Flickering Flame"].ID})	/varset DSBuff O'Keils Flickering Flame
	/if (${Me.Buff["Fiery Might"].ID})							/varset DSBuff Fiery Might
	/if (${Me.Buff["Shield of the Eighth"].ID})			/varset DSBuff Shield of the Eighth
	/if (${Me.Buff["Call of the Rathe"].ID})				/varset DSBuff Call of the Rathe
	/if (${Me.Buff["Frostreaver's Blessing"].ID})		/varset DSBuff Frostreaver's Blessing
	/if (${Me.Buff["Guard of the Earth"].ID})				/varset DSBuff Guard of the Earth
	/if (${Me.Buff["Illusion Fire Elemental"].ID})	/varset DSBuff Illusion Fire Elemental
	/if (${Me.Buff["Scorching Skin"].ID})						/varset DSBuff Scorching Skin
	/if (${Me.Buff["Vengeful Guard"].ID})						/varset DSBuff Vengeful Guard
	/if (${Me.Buff["Hateful Guard"].ID})						/varset DSBuff Hateful Guard
	/if (${Me.Buff["Kilva's Skin of Flame"].ID})		/varset DSBuff Kilva's Skin of Flame
/return ${DSBuff}

Sub BuffedDS4
	/declare DSBuff string local
	|Slot 4 DS buffs
	/if (${Me.Buff["Thorny Shield"].ID})						/varset DSBuff Thorny Shield
	/if (${Me.Buff["Icicle Aura"].ID})							/varset DSBuff Icicle Aura
	/if (${Me.Buff["Shield of Pain"].ID})						/varset DSBuff Shield of Pain
	/if (${Me.Buff["Shield of Torment"].ID})				/varset DSBuff Shield of Torment
	/if (${Me.Buff["Frost Shield"].ID})							/varset DSBuff Frost Shield
	/if (${Me.Buff["Frost Guard"].ID})							/varset DSBuff Frost Guard
	/if (${Me.Buff["Storm Guard"].ID})							/varset DSBuff Storm Guard
/return ${DSBuff}

Sub BuffedResists
	/declare ResistBuff string local
	|Slot 1 resist buffs
	/if (${Me.Buff["Soul Energy"].ID})		/varset ResistBuff Soul Energy
	/if (${Me.Buff["Eternal Ward"].ID})		/varset ResistBuff Eternal Ward
	/if (${Me.Buff["Chaotic Ward"].ID})		/varset ResistBuff Chaotic Ward
/return ${ResistBuff}

Sub BuffedHP
	|Note: this is buffed HP from clickies.
	/declare HPBuff string local
	/if (${Me.Buff["Form of Endurance I"].ID})					/varset HPBuff Form of Endurance I
	/if (${Me.Buff["Form of Endurance"].ID})						/varset HPBuff Form of Endurance
	/if (${Me.Buff["Form of Endurance III"].ID})				/varset HPBuff Form of Endurance III
	/if (${Me.Buff["Elemental Conjunction"].ID})				/varset HPBuff Elemental Conjunction
	/if (${Me.Buff["Savage Wildcaller's Blessing"].ID})	/varset HPBuff Savage Wildcaller's Blessing
/return ${HPBuff}