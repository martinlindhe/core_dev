|Modified by Harden to cast: Guard of the Earth
|todo: redo to pass spell name as parameter to function
|todo: show X players skipped (cos of oor, not in zone etc)
|fix: använd ledig buffslot för Guard of the Earth om det finns, som i buffrequest
|todo: vänta till spell is ready innan den kastar

|add hotkey: /echo castgote
|add: function that cast a specified group buff on each group in raid (target closest member from each group)

|add feature: skriv till gimp_gote.ini username=timestamp, så man kan auto refresha delar av raiden när det tillkommit fler folk utan att behöva rebuffa alla

#Event CastGotEOnAll		"#*#[MQ2] castgote#*#"
Sub Event_CastGotEOnAll
	/declare i int local 0
	/declare oldSpell7 string local ${Me.Gem[7]}
	/declare CntPets			int local 0
	/declare CntPetsBuffed		int local 0
	/declare CntPlayersBuffed	int local 0

	/if (${Raid.Members}) {
		/echo Casting Guard of the Earth on all Raid Members	

		/for i 1 to ${Raid.Members}
			/if ( ${Raid.Member[${i}].Class.Name.Equal["Bard"]} || ${Raid.Member[${i}].Class.Name.Equal["Beastlord"]} || ${Raid.Member[${i}].Class.Name.Equal["Berserker"]} || ${Raid.Member[${i}].Class.Name.Equal["Cleric"]} || ${Raid.Member[${i}].Class.Name.Equal["Druid"]} || ${Raid.Member[${i}].Class.Name.Equal["Magician"]} || ${Raid.Member[${i}].Class.Name.Equal["Monk"]} || ${Raid.Member[${i}].Class.Name.Equal["Necromancer"]} || ${Raid.Member[${i}].Class.Name.Equal["Paladin"]} || ${Raid.Member[${i}].Class.Name.Equal["Rogue"]} || ${Raid.Member[${i}].Class.Name.Equal["Warrior"]} || ${Raid.Member[${i}].Class.Name.Equal["Wizard"]} ) {
				/if (${Spawn[pc ${Raid.Member[${i}].Name}].ID}) {
					/if (${Raid.Member[${i}].Distance} < 150 ) {
						|/echo Buffing ${Raid.Member[${i}].Name} (${Raid.Member[${i}].Class.ShortName})
						/target pc ${Raid.Member[${i}].Name}
						/call Cast "Guard of the Earth" gem7
						/varcalc CntPlayersBuffed ${CntPlayersBuffed}+1
						/delay 3s
					} else {
						/echo ${Raid.Member[${i}].Name} (${Raid.Member[${i}].Class.ShortName}) is OOR, ${Raid.Member[${i}].Distance} distance
					}
				} else {
					/echo ${Raid.Member[${i}].Name} (${Raid.Member[${i}].Class.ShortName}) not in zone
				}
			}
			/if (${Raid.Member[${i}].Class.Name.NotEqual["Necromancer"]} && ${Raid.Member[${i}].Class.Name.NotEqual["Wizard"]} && ${Raid.Member[${i}].Pet.ID} && ${Raid.Member[${i}].Pet.Level} > 28) {
				/if (${Raid.Member[${i}].Pet.Distance} < 150) {
					/echo Buffing ${Raid.Member[${i}].Pet.CleanName} (${Raid.Member[${i}].Name}'s pet)
					/target pet ${Raid.Member[${i}].Pet.Name}
					/call Cast "Guard of the Earth" gem7
					/varcalc CntPetsBuffed ${CntPetsBuffed}+1
					/delay 3s
				}
				/varcalc CntPets ${CntPets}+1
			}
		/next i
		
		/echo ${CntPlayersBuffed}/${Raid.Members} players buffed, ${CntPetsBuffed}/${CntPets} pets
	} else {

		/if (${Group.Members}) {
			/echo Casting Guard of the Earth on all group members
	
			/for i 0 to 5
				/if (${Group.Member[${i}].Class.Name.Equal["Bard"]} || ${Group.Member[${i}].Class.Name.Equal["Beastlord"]} || ${Group.Member[${i}].Class.Name.Equal["Berserker"]} || ${Group.Member[${i}].Class.Name.Equal["Cleric"]} || ${Group.Member[${i}].Class.Name.Equal["Druid"]} || ${Group.Member[${i}].Class.Name.Equal["Magician"]} || ${Group.Member[${i}].Class.Name.Equal["Monk"]} || ${Group.Member[${i}].Class.Name.Equal["Necromancer"]} || ${Group.Member[${i}].Class.Name.Equal["Paladin"]} || ${Group.Member[${i}].Class.Name.Equal["Rogue"]} || ${Group.Member[${i}].Class.Name.Equal["Warrior"]} || ${Group.Member[${i}].Class.Name.Equal["Wizard"]} ) {
					| person is in zone
					/if (${Spawn[pc ${Group.Member[${i}].Name}].ID}) {
						/if (${Group.Member[${i}].Distance} < 150) {
							|/echo Buffing ${Group.Member[${i}].Name} (${Group.Member[${i}].Class.ShortName})
							/target pc ${Group.Member[${i}].Name}
							/call Cast "Guard of the Earth" gem7
							/delay 3s
						} else {
							/echo ${Group.Member[${i}].Name} (${Group.Member[${i}].Class.ShortName}) is OOR, ${Group.Member[${i}].Distance} distance
						}
						
					} else {
						/echo ${Group.Member[${i}].Name} (${Group.Member[${i}].Class.ShortName}) not in zone
					}
				}
				/if (${Group.Member[${i}].Class.Name.NotEqual["Necromancer"]} && ${Group.Member[${i}].Class.Name.NotEqual["Wizard"]} && ${Group.Member[${i}].Pet.ID} && ${Group.Member[${i}].Pet.Level} > 28) {
					/if (${Group.Member[${i}].Pet.Distance} < 150) {
						/echo Buffing ${Group.Member[${i}].Pet.CleanName} (${Group.Member[${i}].Name}'s pet)
						/target pet ${Group.Member[${i}].Pet.Name}
						/call Cast "Guard of the Earth" gem7
						/delay 3s
					}
				}
			/next i
			/echo Done casting Guard of the Earth
		}
	}

	/if (${Me.Gem[7].Name.NotEqual["${oldSpell7}"]}) {
		/delay 2s
		/echo # Restoring old spell gem
		/memspell 7 ${oldSpell7}
	}

/return