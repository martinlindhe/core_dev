| exposes a number of HUD variables, call CheckBuffs
|
|
|fixme: om man har paladin i gruppen ska symbol of jeran listas som missing


#Event JoinedGroup "You have joined the group."
#Event JoinedGroup "You have been removed from the group."
#Event JoinedGroup "Your raid was disbanded."
Sub Event_JoinedGroup(string Line)
	/if (${Raid.Members}) {
		/varset mcHUDbuffmode RL ${Raid.Leader}, GL${Raid.Member[${Me}].Group} ${Group.Leader} (raid:${Raid.Members}, zone:${SpawnCount[pc]})
	} else {
		/varset mcHUDbuffmode UNGROUPED (zone:${SpawnCount[pc]})
		/if (${Group.Members}) /varset mcHUDbuffmode GL: ${Group.Leader.Name} (group:${Math.Calc[${Group.Members}+1].Int}, zone:${SpawnCount[pc]})
	}
/return