<?
	if($action == 'upgrade') {
		errorACT('Kommer snart!');
	}
	$sections = array('cookies', 'openhours', 'radio', 'disclaimer', 'about', 'agree', 'contact', 'url', 'info-mms-help');
	$action_new = $action;
	if(in_array($action, $sections)) {
		if($action == 'agree')
			$action_new = 'register-accept';
	} else {
		errorACT('Felaktig sida.');
	}
	if($action == 'cookies' || $action == 'agree') $v = 1; else $v = 3;
	if(empty($id)) {
		if($v == 1)
			errorIACT(extOUT(gettxt($action_new)), $action, $v);
		else
			errorIACT(extOUT(gettxt($action_new), 'wht'), $action, $v, 'wht');
	} else if($id == 1)
		splashLACT(extOUT(gettxt($action_new), ''));
	else if($id == 2)
		popupLACT(extOUT(gettxt($action_new), ''));
	else if($id == 3)
		bigpopupACT(extOUT(gettxt($action_new), 'wht'));
?>