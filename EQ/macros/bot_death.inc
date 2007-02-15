|auto memorize 1 spell set upon death
#define SPELL_SET_ON_DEATH bot-set

#Event YouHaveDied		"You have been slain by#*#"
#Event YouHaveDied		"You died.#*#"

Sub Event_YouHaveDied
	/echo [${Time}] You died at ${Me.Y}, ${Me.X}, ${Me.Z}!

	/delay 15s
	/varset AbortLoop 1

	/if (${Group.Member[${i}].Class.NotEqual["Warrior"]} && ${Group.Member[${i}].Class.NotEqual["Monk"]} && ${Group.Member[${i}].Class.NotEqual["Rogue"]} && ${Group.Member[${i}].Class.NotEqual["Berserker"]}) {
		|auto loads a spell set after death
		/loadspell SPELL_SET_ON_DEATH
	}
/return
