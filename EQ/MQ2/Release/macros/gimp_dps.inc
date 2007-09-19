|must call Init_DPS before main loop of program
|exports data to HUD:
|	 mcHUDlastDS - last ammount of DS a mob was hit for

|DPS counter events:
#Event DamageNuke		"#1# hit #2# for #3# points of non-melee damage."
#Event DamageUnknown "#1# was hit by non-melee for #2# points of damage."
#Event DamageDoT		"#*#has taken #1# damage from your #2#"
#Event Heal				"You have healed #1# for #2# points of damage."
#Event DamageDS			"#1# was hit by non-melee for #2# points of damage."
#Event HealedMe			"#1# has healed you for #2# points of damage."

#Event HoTonMe			"You have been healed for #1# hit points by your heal over time spell(s)."
Sub Event_HoTonMe(string line, string value)
	/echo HoT: ${value}
/return


|you make melee damage (addverbated form):
#Event DamageMelee		"#*#crush #*# for #1#"
#Event DamageMelee		"#*#punch #*# for #1#"
#Event DamageMelee		"#*#pierce #*# for #1#"
#Event DamageMelee		"#*#slash #*# for #1#"
#Event DamageMelee		"#*#backstab #*# for #1#"
#Event DamageMelee		"#*#bash #*# for #1#"
#Event DamageMelee		"#*#kick #*# for #1#"
#Event DamageMelee		"#*#strike #*# for #1#"

|caster dps,...
|#Event DamageMelee		"#*#hit #*# for #1#"

|Uncomment the below lines to count arrow damage, to make dps numbers more fair for pullers, these are commented out
|#Event DamageMelee		"hit #*# for #1#"
|#Event DamageMelee		"You hit #*# for #1# point of damage."
|#Event DamageMelee		"You hit #*# for #1# points of damage."


|you take damage (addverbated form):
|"mob hits for xx"
#Event DamageMeleeTaken "#1# hits for #2#"
#Event DamageMeleeTaken "#1# bashes for #2#"
#Event DamageMeleeTaken "#1# slashes for #2#"
#Event DamageMeleeTaken "#1# smashes for #2#"
#Event DamageMeleeTaken "#1# kicks for #2#"
#Event DamageMeleeTaken "#1# pierces for #2#"
#Event DamageMeleeTaken "#1# bites for #2#"
#Event DamageMeleeTaken "#1# claws for #2#"

Sub Event_DamageMeleeTaken(string Line, string Name, int Value)
	|/echo # Was hit for ${Value} by ${Name}
	/varcalc totDmgTaken ${totDmgTaken}+${Value}
/return

#Event DPS_XP			"You gain experience#*#"
#Event DPS_XP			"You gain party experience#*#"
#Event DPS_XP			"You gained raid experience#*#"

|EQ IS FPS!
#Event KillShot "You have slain #1#!"
Sub Event_KillShot(string Line, string Name)
	/echo # Kill Shot: ${Name}
	/popup Kill Shot: ${Name}
/return

Sub Init_DPS
	/declare topNuke	int outer ${Ini[INI_FILE, ${Me.Name}, TopNuke, NOT_FOUND]}
	/declare topDoT		int outer ${Ini[INI_FILE, ${Me.Name}, TopDoT, NOT_FOUND]}
	/declare topMelee	int outer ${Ini[INI_FILE, ${Me.Name}, TopMelee, NOT_FOUND]}
	/declare topHeal	int outer ${Ini[INI_FILE, ${Me.Name}, TopHeal, NOT_FOUND]}
	/declare topDPS		float outer ${Ini[INI_FILE, ${Me.Name}, TopDPS, NOT_FOUND]}
	/declare mcHUDlastDS	int outer	0

	/declare gimp_dps_fightstarted int outer 0
	/declare gimp_dps_fight_time int outer 0
	/declare gimp_dps_current_dps float outer 0
	/declare gimp_dps_hits	int outer 0
	/declare gimp_dps_last_hit int outer 0

	|Will count how much i healed until mob is dead
	/declare totHeal		int outer 0
	/declare totHealedMe	int outer 0
	/declare totDmg			int outer 0
	/declare totDmgUnknown int outer 0
	/declare totDmgTaken int outer 0

	/echo gimp_dps.inc initialized
	/echo * Best nuke: ${topNuke}, dot: ${topDoT}, melee: ${topMelee}, heal: ${topHeal}, dps: ${topDPS}
/return


Sub CalcDPS(int value)

	/if (${totDmg} == 0) {
		/echo [${Time}] Fight started
		/popup FIGHT STARTED
		/varset gimp_dps_fightstarted ${Macro.RunTime}
		/varset gimp_dps_hits 0
	}
	/varcalc totDmg ${totDmg}+${value}
	/varcalc gimp_dps_hits ${gimp_dps_hits}+1

	/varcalc gimp_dps_fight_time ${Macro.RunTime}-${gimp_dps_fightstarted}
	/varset gimp_dps_last_hit ${Macro.RunTime}

	/if (${gimp_dps_fight_time}) /varcalc gimp_dps_current_dps ${totDmg}/${gimp_dps_fight_time}
/return

|todo: skriv om dps calc: spara tid vid varje "senaste" hit, räkna dps från första till sissta hit

Sub Event_DPS_XP
	/if (${totDmg} && ${gimp_dps_fight_time}) {
		/declare gimp_dps_hits_per_sec float local ${Math.Calc[${gimp_dps_hits}/${gimp_dps_fight_time}]}
		/echo - Dealt ${totDmg} damage over ${gimp_dps_fight_time}s in ${gimp_dps_hits} hits (${gimp_dps_current_dps} DPS, ${gimp_dps_hits_per_sec} hits per sec)
		
		/if (${gimp_dps_current_dps} > ${topDPS}) {
			/if (${gimp_dps_fight_time} > 20 && (${gimp_dps_hits} > 5)) {
				|Only log dps highscores that is taken over some time (20 sec), to make result more accurate
				/echo # New DPS highscore: ${gimp_dps_current_dps} (old was ${topDPS})
				/varset topDPS ${gimp_dps_current_dps}
				/ini "INI_FILE" "${Me.Name}" "TopDPS" "${topDPS}"
			}
		}
	}
	/if (${totDmgUnknown}) /echo - ${totDmgUnknown} unknown damage was made (DS & such)
	/if (${totHeal} && ${totHealedMe}) {
		/echo - Healed ${totHeal} HP, was healed for ${totHealedMe} HP
	} else {
		/if (${totHeal}) /echo - Healed ${totHeal} HP
		/if (${totHealedMe}) /echo - Was healed for ${totHealedMe} HP
	}
	/if (${totDmgTaken}) /echo - Took ${totDmgTaken} points of melee damage (fixme: spell dmg)
	/echo -----------------------------------
	/varset totHeal 0
	/varset totHealedMe 0
	/varset totDmg 0
	/varset totDmgUnknown 0
	/varset totDmgTaken 0
/return

Sub Event_Heal(string line, string name, int value)
	/if (${Me.Name.Equal[${name}]}) /varcalc totHealedMe ${totHealedMe}+${value}
	/if (${value} > 1000) /echo Heal: ${name} -> ${value} HP
	/varcalc totHeal ${totHeal}+${value}
	/if (${value} <= ${topHeal}) /return
	/echo Heal highschore: ${value} (old was ${topHeal})
	/varset topHeal ${value}
	/ini "INI_FILE" "${Me.Name}" "TopHeal" "${topHeal}"
/return

Sub Event_DamageMelee(string line, int value)
	/call CalcDPS ${value}
	/if (${value} <= ${topMelee}) /return
	/echo Melee highschore: ${value} (old was ${topMelee})
	/varset topMelee ${value}
	/ini "INI_FILE" "${Me.Name}" "TopMelee" "${topMelee}"
/return

Sub Event_DamageNuke(string line, string name, string mob, string value)
	/if (${Me.Name.Equal[${name}]} && ${Me.Name.NotEqual[${mob}]}) {
		/call CalcDPS ${value}
		/if (${value} >= 1000) /echo Nuke: ${value}
		/if (${value} <= ${topNuke}) /return
		/echo Nuke highschore: ${value} (old was ${topNuke})
		/varset topNuke ${value}
		/ini "INI_FILE" "${Me.Name}" "TopNuke" "${topNuke}"
	}
/return

Sub Event_DamageDoT(string line, int value)
	/call CalcDPS ${value}
	/if ((${value} > 100) && !${Raid.Members}) /echo DoT: ${value}
	/if (${value} <= ${topDoT}) /return
	/echo DoT highschore: ${value} (old was ${topDoT})
	/varset topDoT ${value}
	/ini "INI_FILE" "${Me.Name}" "TopDoT" "${topDoT}"
/return

|This event triggers from Damage Shield, probably more
Sub Event_DamageUnknown(string line, string name, string value)
	/if (${Spawn[${name}].Type.Equal["NPC"]}) /varcalc totDmgUnknown ${totDmgUnknown}+${value}
/return

Sub Event_HealedMe(string line, string name, int value)
	/if (${value} > 300) /echo ${name} healed ME for ${value}
	/varcalc totHealedMe ${totHealedMe}+${value}
/return



|This will work for most ppl, can't really make a better fix,ignores cleric reverse DS, this is to report my own DS
Sub Event_DamageDS(string line, string name, int value)
	/if ((${value} != 34) && (${value} != 45)) /varset mcHUDlastDS ${value}
/return