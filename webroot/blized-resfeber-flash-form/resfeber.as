formFlygHotell._visible = true;
formEvenemang._visible = false;
formHotell._visible = false;

radioButtonListener = new Object();
radioButtonListener.click = function (evt) {

	if (evt.target.selection.data == 'Flyg')
	{
		formFlygHotell._visible = true;
		formEvenemang._visible = false;
		formHotell._visible = false;
	} else if (evt.target.selection.data == 'Hotell')
	{
		formFlygHotell._visible = false;
		formEvenemang._visible = false;
		formHotell._visible = true;
	} else if (evt.target.selection.data == 'Evenemang')
	{
		formFlygHotell._visible = false;
		formEvenemang._visible = true;
		formHotell._visible = false;
	} else if (evt.target.selection.data == 'FlygHotell')
	{
		formFlygHotell._visible = true;
		formEvenemang._visible = false;
		formHotell._visible = false;
	}
}
radioGroup.addEventListener("click", radioButtonListener);


_level0.formFlygHotell.Knapp.clickHandler = function() {
	if (_level0.radioGroup.selection.data == 'FlygHotell') {
		trace('flyg & hotell - click');
		
		var formurl = 'http://www.resfeber.se/se/destination/cgi-bin/freetext_search.cgi?partner=msn&section=product&next_page=http://www.resfeber.se/se/flight/cgi-bin/pre_search.cgi&btn_date.x=yes&combo_id=10';
    
		//detta behövs kanske urlencodas: ??
		//<input name="next_page" value="http://www.resfeber.se/se/flight/cgi-bin/pre_search.cgi" type="hidden">	

	} else {
		trace("flyg - click");

		var formurl = 'http://www.resfeber.se/se/flight/cgi-bin/pre_freetext_search.cgi?partner=msn&section=flight&source=cmp_msn_firstpage_060616&next_page=http://www.resfeber.se/se/flight/cgi-bin/pre_search.cgi&combo_id=1';
		
		//<input name="next_page" value="http://www.resfeber.se/se/flight/cgi-bin/pre_search.cgi" type="hidden">
	}
	
	trace('url: ' + formurl);
}

_level0.formHotell.Knapp.clickHandler = function() {
	trace("hotell - click");
}

_level0.formEvenemang.Knapp.clickHandler = function() {
	trace("evenemang - click");
	
	//Läs in data från följande element:
	//Stad - dropdown
	//Kategori - dropdown
	//KategoriText - textfält
	
	//trace('stad: ' + _level0.formEvenemang.Stad.selectedItem.label);
	//trace('kategori: ' + _level0.formEvenemang.Kategori.selectedItem.label);
	//trace('kategoriText: ' + _level0.formEvenemang.KategoriText.text);

	var formurl = 'http://www.resfeber.se/se/event/cgi-bin/pre_search.cgi?partner=msn&combo_id=4&source=cmp_msn_firstpage_060616&adults=1';
	
	var dh_dest_id = 0;
	switch (_level0.formEvenemang.Stad.selectedItem.label) {	
		case "Amsterdam": dh_dest_id = 310005; break;
		case "Barcelona": dh_dest_id = 340002; break;
		case "Edinburgh": dh_dest_id = 440074; break;
		case "Las Vegas": dh_dest_id = 128038; break;
		case "London":		dh_dest_id = 440002; break;
		case "Madrid":		dh_dest_id = 340001; break;
		case "Manchester":dh_dest_id = 440003; break;
		case "Milano":		dh_dest_id = 390016; break;
		case "New York City": dh_dest_id = 131001; break;
		case "Paris":			dh_dest_id = 330001; break;
		case "Prag":			dh_dest_id = 420012; break;
		case "Rom":				dh_dest_id = 390077; break;
		case "Stratford Upon Avon": dh_dest_id = 440297; break;
		case "Torino":		dh_dest_id = 390013; break;
		case "Wien":			dh_dest_id = 430023; break;
		default: trace('UNKNOWN STAD: ' + _level0.formEvenemang.Stad.selectedItem.label); return;
	}
	formurl += '&dh_dest_id=' + dh_dest_id;
	
	var event_category = 0;
	switch (_level0.formEvenemang.Kategori.selectedItem.label) {
		case "Båtturer": event_category = 25; break;
		case "Formel 1": event_category = 8; break;
		case "Fotboll":	 event_category = 6; break;
		case "Kabaréer": event_category = 9; break;
		case "Klassiska konserter": event_category = 21; break;
		case "Musikaler":	event_category = 5; break;
		case "Opera": event_category = 10; break;
		case "Pjäser": event_category = 7; break;
		case "Tågpass": event_category = 18; break;
		case "Övrigt": event_category = 4; break;
		default: trace('UNKNOWN KATEGORI: ' + _level0.formEvenemang.Kategori.selectedItem.label); return;
	}
	formurl += '&event_category=' + event_category;

	if (_level0.formEvenemang.KategoriText.text != '... eller skriv fritext här') {
		//todo: urlencode!
		formurl += '&freetext=' + _level0.formEvenemang.KategoriText.text;
	}

	trace('url: ' + formurl);

	getURL(formurl, "_top");	

}