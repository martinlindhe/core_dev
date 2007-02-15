|requires you to call Init_XP before program main loop

#Event XP			"#*#You gain experience#*#"
#Event XP			"#*#You gain party experience#*#"
#Event XP			"#*#You gained raid experience#*#"

Sub Init_XP
	/if (!${gimp_xptrack_loaded}) {
		/declare gimp_xptrack_loaded	int		outer	1
		/declare CurrentLevel			int		outer	${Me.Level}
		/declare CurrentXP				int		outer	${Me.Exp}
		/declare CurrentAA				int		outer	${Me.AAExp}
		/declare CurrentLDRXP			int		outer	${Me.GroupLeaderExp}
		/declare mobcount				int		outer	0
		/echo gimp_xptrack.inc initialized
		/echo * lv${Me.Level}, exp ${Me.Exp}/330 (${Me.PctExp}%)
	}
/return

Sub Event_XP
	/declare GainedXP		int local
	/declare GainedXPPct	float local
	/declare GainedAA		int local
	/declare GainedAAPct	float local
	/declare GainedLDRXP	int local
	/declare GainedLDRXPPct	float local
	/declare tmp			float local

	/if (${Me.Level} > ${CurrentLevel}) {
		/echo Ding level ${Me.Level}!
	} else {
		/varcalc GainedXP ${Me.Exp}-${CurrentXP}
		/varcalc GainedAA ${Me.AAExp}-${CurrentAA}
		/varcalc GainedLDRXP ${Me.GroupLeaderExp}-${CurrentLDRXP}
		
		/if (${GainedXP} > 0) {
			/varcalc GainedXPPct (${GainedXP}/330)*100
			|fixme: cross check that the -1.0 is working correctly for normal xp track (does for aa xp)
			/varcalc tmp ((330-${CurrentXP})/${GainedXP})-1.0
			/echo - Gained ${GainedXPPct}% XP, ${tmp} to ding (${Math.Calc[100-${Me.PctExp}]}% left)
		}
		/if (${GainedAA} < 0) {
			/echo Ping AA point!
			/varcalc GainedAA ${GainedAA}+330
			|hur kollar jag antal AA points jag har? totalt alltså inte unspent ($Me.AAPoints}
			|måste patcha koden, se http://www.macroquest2.com/phpBB2/viewtopic.php?t=10990
		}
		/if (${GainedAA} != 0) {
			/varcalc GainedAAPct (${GainedAA}/330)*100
			/varcalc tmp ((330-${CurrentAA})/${GainedAA})-1.0
			/if (${tmp} >= 0) {
				/echo - Gained ${GainedAAPct}% AA, ${tmp} to ping (${Math.Calc[100-${Me.PctAAExp}]}% left)
			} else {
				/echo - # PING !!!!! (${Math.Calc[100-${Me.PctAAExp}]}% left to next ping)
			}
		}
		/if (${GainedLDRXP} > 0) {
			/varcalc GainedLDRXPPct (${GainedLDRXP}/1000)*100
			/varcalc tmp (1000-${CurrentLDRXP})/${GainedLDRXP}
			/echo - Gained ${GainedLDRXPPct}% LDR AA, ${tmp} to ding [${Me.GroupLeaderExp.Int}/1000]
		}
	}
	/varset CurrentLevel	${Me.Level}
	/varset CurrentXP		${Me.Exp}
	/varset CurrentAA		${Me.AAExp}
	/varset CurrentLDRXP	${Me.GroupLeaderExp}
	/varcalc mobcount ${mobcount}+1
	/echo [${Time}] ${mobcount} mobs in ${Math.Calc[${Macro.RunTime}/60]} min (${Math.Calc[(${Macro.RunTime}/${mobcount})/60]} avg)
/return