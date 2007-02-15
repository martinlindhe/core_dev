#define SPAWN_INI	gimp_spawncheck.ini

Sub Init_Spawncheck
	/declare gimp_spawncheck_loaded		int		outer	1
	/declare SpawnCheckTimer			timer	outer	0
/return

Sub CheckSpawns
	/if (!${gimp_spawncheck_loaded}) /call Init_Spawncheck
	/if (${SpawnCheckTimer} || ${Ini[SPAWN_INI, ${Zone.ShortName},1,NOTFOUND].Equal[NOTFOUND]}) /return

	/declare nValues		int local 1
	/declare i				int local 0
	/declare name			string local
	/declare FoundSpawn		int local 0
	
	:CounterLoop
	/if (${Ini[SPAWN_INI, ${Zone.ShortName},${nValues},NOTFOUND].Equal[NOTFOUND]}) {
		/varcalc nValues ${nValues}-1
		|/echo # done lookin
		/goto :LookForSpawns
	}
	/varcalc nValues ${nValues}+1
	/doevents
	/goto :CounterLoop 

	:LookForSpawns
	/if (!${nValues}) /return
	|/echo # Debug: Looking for spawns in ${Zone.ShortName} ...

	/for i 1 to ${nValues}
		/varset name ${Ini[SPAWN_INI, ${Zone.ShortName}, ${i}]}
		|/echo # Debug: Looking for: ${name}
		/if (${Spawn[npc ${name}].ID}) {
			/echo # SpawnCheck: ${Spawn[${name}].CleanName} is up [${Time.Time24}]
			/popup # SpawnCheck: ${Spawn[${name}].CleanName} is up [${Time.Time24}]
			/varset FoundSpawn 1
		}
	/next i
	/varset SpawnCheckTimer 3m
/return