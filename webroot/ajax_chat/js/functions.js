function display_timestamp(stamp)
{
	//todo: timezone conversion stuff
	if (stamp == undefined) var curr = new Date();
	else var curr = new Date(stamp*1000) // from timestamp
	//return curr.toString();
	return curr.formatDate('D jS M H:i');
}