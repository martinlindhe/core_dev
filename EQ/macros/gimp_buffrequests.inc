|gimp_buffrequests.inc - handles buff requests in tells & group chat - by gimp
|
|--------------------------
|Uses these INI settings:
|
|[Playername]
|DontAutoBuff=1						turns off gimp_buffrequests.inc
|BuffGuild=NameofGuild				name of guild to buff, comment out this to use your own guild (default)
|DontBuffIfSpellNotMemorized=1		dont buff if requested spell is not memorized
|DontBuffGroupMembers=1				turn off behavior to accept buff requests from group/raid members even if they are not guilded
|DontBuffWhenBusy=1					wont buff when you are invis, moving, /afk, casting spells etc
|
|[Playername_AllowedPlayers]
|Otherplayer=1						allows buff requests from these players even tho they are not in your guild
|Someguy=1
|
|Reads recognized buffrequests from INI like this:
|
|[Classname_RecognizedBuffs]
|conv=Conviction					spell nickname=Real Spell or AA Name
|
|If Classname_RecognizedBuffs section is not found, it will look for
|[Playername_RecognizedBuffs]
|conv=Conviction					....
|
|Default class recognized buffs are provided in krust.ini.samples for a couple of classes (incomplete)
|
|--------------------------
|Uses these /-commands:
|
|/buffrequests						toggle this module ON/OFF on the fly.

|todo: if a instant-cast AA is requested, like shared camouflage, then wait 1-3 random seconds before casting

|If no spell slots are free, it will default to using this slot for buffing (old spell will be restored when done)
#Define DEFAULT_SPELL_SLOT		8

#Event ToggleBuffRequests		"[MQ2] buffrequests"
Sub Event_ToggleBuffRequests
	/if (!${gimp_buffrequests_loaded}) /call Init_BuffRequests
	/varset gimp_buffrequests_activated ${If[${gimp_buffrequests_activated},0,1]}
	/echo gimp_buffrequests.inc is ${If[${gimp_buffrequests_activated},ACTIVATED,DEACTIVATED]}
/return

Sub Init_BuffRequests

	/declare gimp_buffrequests_loaded				int		outer	1
	/declare gimp_buffrequests_activated			int		outer	1
	/declare gimp_buffrequests_allowedguild			string	outer
	/declare gimp_buffrequests_onlymemmedspells		int		outer	0
	/declare gimp_buffrequests_dontbuffgroupmembers	int		outer	0
	/declare gimp_buffrequests_dontbuffwhenbusy		int		outer	0

	/if ( ${Me.Class.Name.Equal["Bard"]} || ${Me.Class.Name.Equal["Berserker"]} || ${Me.Class.Name.Equal["Monk"]} || ${Me.Class.Name.Equal["Rogue"]} || ${Me.Class.Name.Equal["Warrior"]}  ) {
		|/echo gimp_buffrequests.inc disabled for melee classes
		/varset gimp_buffrequests_activated 0
		/return
	}

	/if (${Ini[INI_FILE, ${Me.Name}, DontAutoBuff, NOT_FOUND]}) {
		/echo gimp_buffrequests.inc disabled by INI
		/varset gimp_buffrequests_activated 0
		/return
	}

	/varset gimp_buffrequests_onlymemmedspells ${Ini[INI_FILE, ${Me.Name}, DontBuffIfSpellNotMemorized, NOT_FOUND]}
	/varset	gimp_buffrequests_dontbuffgroupmembers ${Ini[INI_FILE, ${Me.Name}, DontBuffGroupMembers, NOT_FOUND]}
	/varset	gimp_buffrequests_dontbuffwhenbusy ${Ini[INI_FILE, ${Me.Name}, DontBuffWhenBusy, NOT_FOUND]}

	/declare str string local
	/varset str 	${Ini[INI_FILE, ${Me.Name}, BuffGuild,]}

	/varset gimp_buffrequests_allowedguild ${Me.Guild}
	/if (${str.NotEqual[NULL]}) /varset gimp_buffrequests_allowedguild ${str}

	/squelch /alias /buffrequests /echo buffrequests

	/echo gimp_buffrequests.inc initialized
	/echo * Accepting <${gimp_buffrequests_allowedguild}> members
	/if (!${gimp_buffrequests_dontbuffgroupmembers}) /echo * # Buffing nonguilded group/raid members
/return

#Event HandleBuffRequest		"#1# tells you, '#2#'"
Sub Event_HandleBuffRequest(string FullText, string Player, string Text)
	/if (${Spawn[PC ${Player}].Type.NotEqual["PC"]}) /return
	/call Event_HandleBuffRequestGroup "${FullText}" "${Player}" "${Text}"
/return

|Handle incomming command
#Event HandleBuffRequestGroup	"#1# tells the group, '#2#'"
Sub Event_HandleBuffRequestGroup(string FullText, string Player, string Text)

	/if (!${gimp_buffrequests_loaded}) /call Init_BuffRequests
	/if (!${gimp_buffrequests_activated}) /return

	/if (!${Spawn[pc ${Player}].ID} || ${Spawn[pc ${Player}].GM} || ${Spawn[pc ${Player}].Type.NotEqual[PC]} || ${Me.Invis} || ${Me.Feigning} || ${Me.Moving} || ${Me.AFK} || ${Cursor.ID} || ${Me.Casting.ID} || ${Window[SpellBookWnd].Open} || ${Window[BigBankWnd].Open} || ${Window[LootWnd].Open} || ${Window[TradeWnd].Open} || ${Window[MerchantWnd].Open} || ${Window[TradeskillWnd].Open} || ${Window[BarterWnd].Open} || !${InvSlot[chest].Item.ID}) /return

	/declare CheckStr			string	local
	/declare AllowedBuff		int		local	NOT_FOUND
	/declare RequestedBuff		string	local
	/declare i					int local 0
	/declare OldSpell			string	local
	/declare SpellSlot			int local 0
	
	|Checking if player is in specified guild, or in white-list
	/if (${Spawn[pc ${Player}].Guild.NotEqual[${gimp_buffrequests_allowedguild}]}) {
		/varset AllowedBuff ${Ini[INI_FILE, ${Me.Name}_AllowedPlayers, ${Player}, NOT_FOUND]}
		|/echo allowedbuff: ${AllowedBuff}
		
		/if (${AllowedBuff} == NOT_FOUND) {
			|If in group + buff group members option is on:
			/if (!${gimp_buffrequests_dontbuffgroupmembers} && (${Group.Member[${Player}]} || ${Raid.Member[${Player}].ID})) {
				|/echo # Debug: Allowing buffrequest from unguilded group/raid member
			} else {
				|/echo # Debug: Ignoring request '${Text}' from ${Player}
				/return
			}
		}
		|/echo # Debug: ${Player} is whitelisted (${AllowedBuff})
	}
	
	/varset RequestedBuff ${Text}

	|Drop long sentences before we attempt to process them
	/if (${RequestedBuff.Length} > 28) /return

	|Ignore strings containing , since they mess up ini read functions
	/if (${RequestedBuff.Find[,]} || ${RequestedBuff.Find[<]} || ${RequestedBuff.Find[>]}) /return
	
	|Is player in range? ${Spell[name].Range} just works if spell already is memmed
	/if (${Spawn[pc ${Player}].Distance} > 100) /return

	|fixme: loop till string dont change
	/for i 1 to 3
	/if (${RequestedBuff.Right[50].Equal["Sorry, I am A.F.K. (Away From Keyboard) right now."]})	/varset RequestedBuff ${RequestedBuff.Left[-50]}

	/if (${RequestedBuff.Right[2].Equal[" a"]})						/varset RequestedBuff ${RequestedBuff.Left[-2]}
	/if (${RequestedBuff.Right[3].Equal[" an"]})					/varset RequestedBuff ${RequestedBuff.Left[-3]}

	/if (${RequestedBuff.Left[1].Equal[" "]})							/varset RequestedBuff ${RequestedBuff.Right[-1]}
	/if (${RequestedBuff.Right[1].Equal[" "]})						/varset RequestedBuff ${RequestedBuff.Left[-1]}

	/if (${RequestedBuff.Right[3].Equal["-=)"]})					/varset RequestedBuff ${RequestedBuff.Left[-3]}
	/if (${RequestedBuff.Right[2].Equal["=)"]})						/varset RequestedBuff ${RequestedBuff.Left[-2]}
	/if (${RequestedBuff.Right[2].Equal[":)"]})						/varset RequestedBuff ${RequestedBuff.Left[-2]}
	/if (${RequestedBuff.Right[2].Equal[";)"]})						/varset RequestedBuff ${RequestedBuff.Left[-2]}
	/if (${RequestedBuff.Right[2].Equal[":D"]})						/varset RequestedBuff ${RequestedBuff.Left[-2]}
	/if (${RequestedBuff.Right[2].Equal[":P"]})						/varset RequestedBuff ${RequestedBuff.Left[-2]}
	/if (${RequestedBuff.Right[2].Equal["=P"]})						/varset RequestedBuff ${RequestedBuff.Left[-2]}

	/if (${RequestedBuff.Right[1].Equal[")"]})						/varset RequestedBuff ${RequestedBuff.Left[-1]}
	/if (${RequestedBuff.Right[1].Equal["?"]})						/varset RequestedBuff ${RequestedBuff.Left[-1]}
	/if (${RequestedBuff.Right[1].Equal["!"]})						/varset RequestedBuff ${RequestedBuff.Left[-1]}
	/if (${RequestedBuff.Right[1].Equal[":"]})						/varset RequestedBuff ${RequestedBuff.Left[-1]}
	/if (${RequestedBuff.Right[1].Equal["."]})						/varset RequestedBuff ${RequestedBuff.Left[-1]}

	/if (${RequestedBuff.Left[11].Equal["could i get"]})		/varset RequestedBuff ${RequestedBuff.Right[-11]}
	/if (${RequestedBuff.Left[10].Equal["can i have"]})			/varset RequestedBuff ${RequestedBuff.Right[-10]}
	/if (${RequestedBuff.Left[ 9].Equal["can i get"]})			/varset RequestedBuff ${RequestedBuff.Right[-9]}
	/if (${RequestedBuff.Left[ 7].Equal["can you"]})				/varset RequestedBuff ${RequestedBuff.Right[-7]}
	/if (${RequestedBuff.Left[ 5].Equal["may i"]})					/varset RequestedBuff ${RequestedBuff.Right[-5]}
	/if (${RequestedBuff.Left[ 4].Equal["need"]})						/varset RequestedBuff ${RequestedBuff.Right[-4]}
	/if (${RequestedBuff.Left[ 3].Equal["and"]})						/varset RequestedBuff ${RequestedBuff.Right[-3]}
	/if (${RequestedBuff.Left[ 3].Equal["plz"]})						/varset RequestedBuff ${RequestedBuff.Right[-3]}

	/if (${RequestedBuff.Right[4].Equal["buff"]})						/varset RequestedBuff ${RequestedBuff.Left[-4]}
	/if (${RequestedBuff.Right[3].Equal["hun"]})						/varset RequestedBuff ${RequestedBuff.Left[-3]}
	/if (${RequestedBuff.Right[3].Equal["wmp"]})						/varset RequestedBuff ${RequestedBuff.Left[-3]}
	/if (${RequestedBuff.Right[3].Equal["thx"]})						/varset RequestedBuff ${RequestedBuff.Left[-3]}

	/if (${RequestedBuff.Right[13].Equal["if time permits"]})	/varset RequestedBuff ${RequestedBuff.Left[-15]}
	/if (${RequestedBuff.Right[13].Equal["got dispelled"]})	/varset RequestedBuff ${RequestedBuff.Left[-13]}
	/if (${RequestedBuff.Right[13].Equal["before engage"]})	/varset RequestedBuff ${RequestedBuff.Left[-13]}
	/if (${RequestedBuff.Right[13].Equal["when possible"]})	/varset RequestedBuff ${RequestedBuff.Left[-13]}
	/if (${RequestedBuff.Right[12].Equal["when you can"]})		/varset RequestedBuff ${RequestedBuff.Left[-12]}
	/if (${RequestedBuff.Right[11].Equal["if possible"]})		/varset RequestedBuff ${RequestedBuff.Left[-11]}
	/if (${RequestedBuff.Right[10].Equal["when u can"]})		/varset RequestedBuff ${RequestedBuff.Left[-10]}
	/if (${RequestedBuff.Right[9].Equal["when poss"]})		/varset RequestedBuff ${RequestedBuff.Left[-9]}
	/if (${RequestedBuff.Right[7].Equal["if mana"]})			/varset RequestedBuff ${RequestedBuff.Left[-7]}
	/if (${RequestedBuff.Right[7].Equal["if poss"]})			/varset RequestedBuff ${RequestedBuff.Left[-7]}
	/if (${RequestedBuff.Right[2].Equal["me"]})					/varset RequestedBuff ${RequestedBuff.Left[-2]}
	/if (${RequestedBuff.Right[2].Equal["ty"]})					/varset RequestedBuff ${RequestedBuff.Left[-2]}
	/if (${RequestedBuff.Right[2].Equal["m8"]})					/varset RequestedBuff ${RequestedBuff.Left[-2]}
	/if (${RequestedBuff.Right[3].Equal["plz"]})				/varset RequestedBuff ${RequestedBuff.Left[-3]}
	/if (${RequestedBuff.Right[3].Equal["pls"]})				/varset RequestedBuff ${RequestedBuff.Left[-3]}
	/if (${RequestedBuff.Right[3].Equal["pzl"]})				/varset RequestedBuff ${RequestedBuff.Left[-3]}
	/if (${RequestedBuff.Right[3].Equal["bud"]})				/varset RequestedBuff ${RequestedBuff.Left[-3]}
	/if (${RequestedBuff.Right[4].Equal["mate"]})				/varset RequestedBuff ${RequestedBuff.Left[-4]}
	/if (${RequestedBuff.Right[6].Equal["please"]})			/varset RequestedBuff ${RequestedBuff.Left[-6]}
	/if (${RequestedBuff.Right[6].Equal["thanks"]})			/varset RequestedBuff ${RequestedBuff.Left[-6]}
	/next i
	
	|too short text
	/if (${RequestedBuff.Length} < 1) /return
	
	|Process request
	|/echo # Debug: Looking for buff: ${RequestedBuff}
	|First look in [PlayerName_RecognizedBuffs] 
	/varset CheckStr ${Ini[INI_FILE, ${Me.Name}_RecognizedBuffs, ${RequestedBuff}, NOT_FOUND]}
	/if (${CheckStr.Equal[NOT_FOUND]}) {
		|/echo # Debug: [${Me.Name}_RecognizedBuffs] not found, checking [${Me.Class}_RecognizedBuffs]

		|If not found, look in [ClassName_RecognizedBuffs]
		/varset CheckStr ${Ini[INI_FILE, ${Me.Class}_RecognizedBuffs, ${RequestedBuff}, NOT_FOUND]}
		/if (${CheckStr.Equal[NOT_FOUND]}) {
			|/echo # Debug: [${Me.Class}_RecognizedBuffs] not found, buffreq parsed to: ${RequestedBuff}
			/return
		}
	}

	/if (!${Me.Book[${CheckStr}]}) {
			
		/if (!${Me.Book[${CheckStr}]} && !${Me.AltAbilityReady[${CheckStr}]}) {
			/echo # Error: The spell name ${CheckStr} does not exist in your spell book! 
			/return
		}

		/if (!${Me.AltAbilityReady[${CheckStr}]}) {
			/echo # Error: You dont have the spell ${CheckStr} or AA isn't ready!
			/return
		}
	}
		
	|XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	|XXXX Time to check if the player is busy with something, then cancel
	/if (${gimp_buffrequests_dontbuffwhenbusy} && (${Me.Invis} || ${Me.Feigning} || ${Me.Moving} || ${Me.AFK} || ${Cursor.ID} || ${Me.Casting.ID} || ${Window[SpellBookWnd].Open} || ${Window[BigBankWnd].Open} || ${Window[LootWnd].Open} || ${Window[TradeWnd].Open} || ${Window[MerchantWnd].Open} || ${Window[TradeskillWnd].Open} || ${Window[BarterWnd].Open} || !${InvSlot[chest].Item.ID})) {
		/echo # Debug: ${Player} asked for buff but i am busy with something!
		/return
	}
		
	|Do a random few secs delay so the buffs dont get cast "too" fast
	/delay ${Math.Calc[${Math.Rand[2]}+0.5]}s
		
	|Wait till player stops moving
	:MoveLoop
	/if (${Me.Moving}) {
		/doevents
		/goto :MoveLoop
	}

	/echo # Buffing ${Player} with '${CheckStr}' (${Text})

	/if (${Me.AltAbilityReady[${CheckStr}]}) {
		/if (${Me.Sitting}) {
			/stand
			/delay 10
		}
		/echo # Activating AA: ${CheckStr}
		/docommand /aa act ${CheckStr}
		/return
	}
		
	/declare was_sitting int local 0
	/if (${Me.Sitting}) /varset was_sitting 1

	|If spell is not memorized in any spell gem slot, find first free slot, else put in DEFAULT_SPELL_SLOT
	/if (!${Me.Gem["${CheckStr}"]}) {
		|I was asked for buff ${CheckStr} but it is not memorized/AA not ready
		/if (${gimp_buffrequests_onlymemmedspells} && !${Me.AltAbilityReady[${CheckStr}]}) /return

		/declare cnt int local 0

		|Checking if i have 9th spell slot AA?
		/varset cnt 8
		/if (${Me.AltAbility["Mnemonic Retention"]}) /varset cnt 9

		/for i 1 to ${cnt}
			/if (!${Me.Gem[${i}].ID}) /varset SpellSlot ${i}
		/next i
			
		|Find first free spell slot, if none is free, fall back to DEFAULT_SPELL_SLOT
		/if (!${SpellSlot}) /varset SpellSlot DEFAULT_SPELL_SLOT

		/varset OldSpell ${Me.Gem[${SpellSlot}]}
		|/echo # Debug: Memorizing ${CheckStr} in slot ${SpellSlot}
		/memspell ${SpellSlot} "${CheckStr}"
	}

	/delay 10s ${Me.SpellReady[${CheckStr}]}

	/target pc "${Player}"
	/call Cast "${CheckStr}"
	/echo # Done buffing ${Player}

	|Restore old spell gem
	/if (${SpellSlot} && ${OldSpell.NotEqual[NULL]}) {
		/delay 2s
		/echo # Restoring old spell ${OldSpell} in slot ${SpellSlot}
		/memspell ${SpellSlot} "${OldSpell}"
		/delay 2s
	}

	/if (${was_sitting}) {
		|/echo # Debug: Was sitting when I got buff request, sitting again.
		/delay ${Math.Calc[${Math.Rand[5]}+1]}s
		/if (${Me.Standing} && ${Me.MaxMana} && !${Me.Moving} !${Me.Casting.ID}) /sit
	}

/return