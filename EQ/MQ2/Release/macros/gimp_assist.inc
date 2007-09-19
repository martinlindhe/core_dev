|must call Init_Assist before main loop

|remap /ta till /target istället för /tap
|

#Event PureBloodCure	"[MQ2] pbcure"
#Event RGCCure			"[MQ2] rgccure"

#Event TargetCured		"Your target has been cured.#*#"

#Event AutoRaidMA		"#1# has been delegated Raid Main Assist."
#Event AutoGroupMA		"#1# has been delegated Group Main Assist."


Sub Event_AutoRaidMA(string Line, string Name)
	/if (${Me.Name.NotEqual[${Name}]} && ${mcGlobalMA.NotEqual[${Name}]}) {
		/echo # Auto changing raid MA to: ${Name}
		/call Event_SetAssist "${Line}", "${Name}"
	}
/return

Sub Event_AutoGroupMA(string Line, string Name)
	/if (${Me.Name.NotEqual[${Name}]} && ${mcGlobalMA.NotEqual[${Name}]}) {
		/echo # Auto changing group MA to: ${Name}
		/call Event_SetAssist "${Line}", "${Name}"
	}
/return

Sub Init_Assist
	/declare mcGlobalMA				string outer		${Ini[INI_FILE, ${Me.Name}, MA, NOT_FOUND]}
	/declare gimp_assist_initialized int outer 1
	/echo gimp_assist.inc initialized
	/echo * MA is > ${mcGlobalMA} <

	|XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	|XXXX Adds aliases to the script
	|XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	/squelch /alias /ma /echo setassist
	/squelch /alias /assistma /assist ${mcGlobalMA}
	/squelch /alias /sa /echo setsassist
	/squelch /alias /assistsa /assist ${mcGlobalSA}	
	
	|moved to plugin: /assisttarget (MQ2Target.dll)
	/squelch /alias /assistt /assisttarget
	|moved to plugin: /targethp (MQ2Target.dll)
	/squelch /alias /targetbyhp /targethp

	|Misc commands that should me moved into separate file
	/squelch /alias /pbcure /echo pbcure
	/squelch /alias /rgccure /echo rgccure
	
	/squelch /alias /lootmycorpse /echo LootMyCorpse 
/return

#Event SetAssist	"[MQ2] setassist#1#"
Sub Event_SetAssist(string Line, string Name)
	/if (!${gimp_assist_initialized}) /call Init_Assist

	/if (${Name.Length}) {
		/if (${Name.Left[1].Equal[" "]}) /varset Name ${Name.Right[-1]}
	} else {
		/if (!${Target.ID}) /return
		/varset Name ${Target.CleanName}
	}

	/varset mcGlobalMA ${Name}
	/ini "INI_FILE" "${Me.Name}" "MA" "${mcGlobalMA}"
	/echo New MA is > ${mcGlobalMA} <
	/squelch /alias /assistma /assist ${mcGlobalMA}
/return


Sub Event_TargetCured
	/if (${Target.ID} && ${Target.Type.Equal["PC"]}) {
		|Dont know for sure who I actually cured
		/echo # Debug: ${Target.Name} (?) cured
	} else {
		/echo # Debug: Group member cured.
	}
/return
