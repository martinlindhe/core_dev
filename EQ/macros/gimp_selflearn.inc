#Event ZoneIndoors "You can only cast this spell in the outdoors.#*#"
Sub Event_ZoneIndoors(string line)
	/declare chk string local ${Ini[ZONEINFO_FILE, ZoneInfo, ${Zone.ShortName}_Outdoors, FALSE]}
	/if (${chk.Equal[???]}) {
		/echo # Debug: Updating Zoneinfo for ${Zone.ShortName}: Indoor zone!
		/ini "ZONEINFO_FILE" "ZoneInfo" "${Zone.ShortName}_Outdoors" "0"
	}
/return

#Event ImmuneCharm "This NPC cannot be charmed."
Sub Event_ImmuneCharm(string line)
	/declare chk string local ${Ini[ZONEINFO_FILE, MobTypes_${Zone.ShortName}, ${Target.CleanName}_ImmuneCharm, FALSE]}
	/if (${chk.Equal[FALSE]} && ${Target.Type.Equal[NPC]}) {
		/echo # Debug: Storing information that ${Target.CleanName} cannot be charmed
		/ini "ZONEINFO_FILE" "MobTypes_${Zone.ShortName}" "${Target.CleanName}_ImmuneCharm" "1"
	}
/return

#Event MobNotAnimal "This spell only works on animals."
Sub Event_MobNotAnimal(string line)
	/declare chk string local ${Ini[ZONEINFO_FILE, MobTypes_${Zone.ShortName}, ${Target.CleanName}_Animal, FALSE]}
	/if (${chk.Equal[FALSE]} && ${Target.Type.Equal[NPC]}) {
		/echo # Debug: Storing information that ${Target.CleanName} is not an animal
		/ini "ZONEINFO_FILE" "MobTypes_${Zone.ShortName}" "${Target.CleanName}_Animal" "0"
	}
/return

#Event MobImmuneSnare "Your target is immune to changes in its run speed."
Sub Event_MobImmuneSnare(string line)
	/declare chk string local ${Ini[ZONEINFO_FILE, MobTypes_${Zone.ShortName}, ${Target.CleanName}_ImmuneSnare, FALSE]}
	/if (${chk.Equal[FALSE]} && ${Target.Type.Equal[NPC]}) {
		/echo # Debug: Storing information that ${Target.CleanName} is immune to snare
		/ini "ZONEINFO_FILE" "MobTypes_${Zone.ShortName}" "${Target.CleanName}_ImmuneSnare" "1"
	}
/return
