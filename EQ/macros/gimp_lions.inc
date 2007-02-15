|todo: redo to pass spell name as parameter to function
|todo: show X players skipped (cos of oor, not in zone etc)
|fix: använd ledig buffslot för lions om det finns, som i buffrequest
|todo: vänta till spell is ready innan den kastar

|add hotkey: /echo castlions
|add: function that cast a specified group buff on each group in raid (target closest member from each group)

|add feature: skriv till gimp_lions.ini username=timestamp, så man kan auto refresha delar av raiden när det tillkommit fler folk utan att behöva rebuffa alla

#Event CastLionsOnMelees		"#*#[MQ2] castlions#*#"
Sub Event_CastLionsOnMelees
	/declare i int local 0
	/declare oldSpell1 string local ${Me.Gem[1]}
	/declare CntPets			int local 0
	/declare CntPetsBuffed		int local 0
	/declare CntPlayersBuffed	int local 0
	
	|/declare BuffName "Lion's Strength"
	/declare BuffName "Mammoth's Strength"
	

	/if (${Raid.Members}) {
		/echo Casting ${BuffName} on all melee classes in raid	

		/for i 1 to ${Raid.Members}
			/if ( ${Raid.Member[${i}].Class.Name.Equal["Bard"]} || ${Raid.Member[${i}].Class.Name.Equal["Beastlord"]} || ${Raid.Member[${i}].Class.Name.Equal["Berserker"]} || ${Raid.Member[${i}].Class.Name.Equal["Monk"]} || ${Raid.Member[${i}].Class.Name.Equal["Paladin"]} || ${Raid.Member[${i}].Class.Name.Equal["Ranger"]} || ${Raid.Member[${i}].Class.Name.Equal["Rogue"]} || ${Raid.Member[${i}].Class.Name.Equal["Shadow Knight"]} || ${Raid.Member[${i}].Class.Name.Equal["Warrior"]} ) {
				/if (${Spawn[pc ${Raid.Member[${i}].Name}].ID}) {
					/if (${Raid.Member[${i}].Distance} < 100 ) {
						|/echo Buffing ${Raid.Member[${i}].Name} (${Raid.Member[${i}].Class.ShortName})
						/target pc ${Raid.Member[${i}].Name}
						/call Cast ${BuffName} gem1
						/varcalc CntPlayersBuffed ${CntPlayersBuffed}+1
						/delay 3s
					} else {
						/echo ${Raid.Member[${i}].Name} (${Raid.Member[${i}].Class.ShortName}) is OOR, ${Raid.Member[${i}].Distance} distance
					}
				} else {
					/echo ${Raid.Member[${i}].Name} (${Raid.Member[${i}].Class.ShortName}) not in zone
				}
			}
			/if (${Raid.Member[${i}].Class.Name.NotEqual["Wizard"]} && ${Raid.Member[${i}].Pet.ID} && ${Raid.Member[${i}].Pet.Level} > 28) {
				/if (${Raid.Member[${i}].Pet.Distance} < 100) {
					/echo Buffing ${Raid.Member[${i}].Pet.CleanName} (${Raid.Member[${i}].Name}'s pet)
					/target pet ${Raid.Member[${i}].Pet.Name}
					/call Cast ${BuffName} gem1
					/varcalc CntPetsBuffed ${CntPetsBuffed}+1
					/delay 3s
				}
				/varcalc CntPets ${CntPets}+1
			}
		/next i
		
		/echo ${CntPlayersBuffed}/${Raid.Members} players buffed, ${CntPetsBuffed}/${CntPets} pets
	} else {

		/if (${Group.Members}) {
			/echo Casting ${BuffName} on all melee classes in group
	
			/for i 0 to 5
				/if (${Group.Member[${i}].Class.Name.Equal["Bard"]} || ${Group.Member[${i}].Class.Name.Equal["Beastlord"]} || ${Group.Member[${i}].Class.Name.Equal["Berserker"]} || ${Group.Member[${i}].Class.Name.Equal["Monk"]} || ${Group.Member[${i}].Class.Name.Equal["Paladin"]} || ${Group.Member[${i}].Class.Name.Equal["Ranger"]} || ${Group.Member[${i}].Class.Name.Equal["Rogue"]} || ${Group.Member[${i}].Class.Name.Equal["Shadow Knight"]} || ${Group.Member[${i}].Class.Name.Equal["Warrior"]} ) {
					| person is in zone
					/if (${Spawn[pc ${Group.Member[${i}].Name}].ID}) {
						/if (${Group.Member[${i}].Distance} < 100) {
							|/echo Buffing ${Group.Member[${i}].Name} (${Group.Member[${i}].Class.ShortName})
							/target pc ${Group.Member[${i}].Name}
							/call Cast ${BuffName} gem1
							/delay 3s
						} else {
							/echo ${Group.Member[${i}].Name} (${Group.Member[${i}].Class.ShortName}) is OOR, ${Group.Member[${i}].Distance} distance
						}
						
					} else {
						/echo ${Group.Member[${i}].Name} (${Group.Member[${i}].Class.ShortName}) not in zone
					}
				}
				/if (${Group.Member[${i}].Class.Name.NotEqual["Wizard"]} && ${Group.Member[${i}].Pet.ID} && ${Group.Member[${i}].Pet.Level} > 28) {
					/if (${Group.Member[${i}].Pet.Distance} < 100) {
						/echo Buffing ${Group.Member[${i}].Pet.CleanName} (${Group.Member[${i}].Name}'s pet)
						/target pet ${Group.Member[${i}].Pet.Name}
						/call Cast ${BuffName} gem1
						/delay 3s
					}
				}
			/next i
			/echo Done casting ${BuffName} on group
		}
	}

	/if (${Me.Gem[1].Name.NotEqual["${oldSpell1}"]}) {
		/delay 2s
		/echo # Restoring old spell gem
		/memspell 1 ${oldSpell1}
	}

/return