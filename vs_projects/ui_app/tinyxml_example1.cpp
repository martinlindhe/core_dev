#include <iostream>
#include <string>

#undef TIXML_USE_STL
#include "tinyxml/tinyxml.h"
#include "lua/lua.hpp"

#include "CInterface.h"

// print all attributes of pElement.
// returns the number of attributes printed
int dump_attribs_to_stdout(TiXmlElement* pElement)
{
	if ( !pElement ) return 0;

	TiXmlAttribute* pAttrib=pElement->FirstAttribute();
	int i=0;
	int ival;
	double dval;

	while (pAttrib)
	{
		printf( "%s: value=[%s]", pAttrib->Name(), pAttrib->Value());

		if (pAttrib->QueryIntValue(&ival)==TIXML_SUCCESS)    printf( " int=%d", ival);
		if (pAttrib->QueryDoubleValue(&dval)==TIXML_SUCCESS) printf( " d=%1.1f", dval);
		printf( "\n" );
		i++;
		pAttrib=pAttrib->Next();
	}
	return i;
}

void Bind_to_UI(CInterface &ui, TiXmlNode *node, const char *attr, const char *val)
{
	string tagname = node->Value();
	if (strlen(attr)) {
		cout << "  set value of <" << tagname << "> property " << attr << " to " << val << endl;

		if (tagname == "window") {
			if (attr == "x") {
				//ui.window->SetX(val);
			}
		}

	} else {
		cout << "  set content of <" << tagname << "> property to " << val << endl;
	}

	
}


void XML_Element_to_UI(CInterface &ui, TiXmlNode *node)
{
	TiXmlNode *parent;
	string tagname = node->Value();

	parent = node->Parent();
	string parentname = parent->Value();

	cout << "<" << tagname << ">, ";
	cout << "parent is <" << parentname << ">" << endl;

	if ( node->Type() != node->ELEMENT) {
		cout << "this is not a element node, aborting!" << endl;
		return;
	}

	int windowId = 0;

	if (parentname == "ui" && tagname == "window") {

		//Skapa ett nytt window
		windowId = ui.AddWindow();
		cout << "window " << windowId << " created" << endl;
	}

	if (parentname == "ui" && tagname == "script" && !node->NoChildren()) {
		//cout << "  -- TODO: load lua script from " << node->FirstChild()->Value() << endl;
	}

	if (parentname == "window" && tagname == "title" && !node->NoChildren() ) {
		//if parent = window, set window title
		//cout << "  -- window title: " << node->FirstChild()->Value() << endl;

		windowId = ui.GetLastWindow();
		ui.Window( windowId )->SetTitle( node->FirstChild()->Value() );
	}

	if (parentname == "window" && tagname == "font" && !node->NoChildren() ) {
		//cout << "  -- window font: " << node->FirstChild()->Value() << endl;

		windowId = ui.GetLastWindow();
		ui.Window( windowId )->SetFont( node->FirstChild()->Value() );
	}


	if (parentname == "border" && tagname == "visible" && !node->NoChildren() ) {
		//fixme: vad är parent till <border>
		cout << "  -- border visibility: " << node->FirstChild()->Value() << endl;
	}

	if (parentname == "border" && tagname == "size" && !node->NoChildren() ) {
		//fixme: vad är parent till <border>
		cout << "  -- border size: " << node->FirstChild()->Value() << endl;

		int intval = 666;
		/*node->FirstChild()->Q
			QueryIntValue(&intval);
			*/
		//todo: konvertera Value() till int

		windowId = ui.GetLastWindow();
		ui.Window( windowId )->border.SetSize( intval );
	}

	if (parentname == "border" && tagname == "rounded" && !node->NoChildren() ) {
		//fixme: vad är parent till <border>
		cout << "  -- border rounded: " << node->FirstChild()->Value() << endl;
	}

	if (parentname == "border" && tagname == "color" && !node->NoChildren() ) {
		//fixme: vad är parent till <border>
		cout << "  -- border color: " << node->FirstChild()->Value() << endl;
	}



	TiXmlAttribute* pAttrib=node->ToElement()->FirstAttribute();

	while (pAttrib)
	{
		//cout << "  attr: " << pAttrib->Name() << " = " << pAttrib->Value() << endl;

		int intval = 0;
		
		pAttrib->QueryIntValue(&intval);

		string attrib = pAttrib->Name();

		if (tagname == "window") {
			CWindow *wnd = ui.Window( windowId );

			if (attrib == "x") {			
				wnd->SetX( intval );
			} else if (attrib == "y") {
				wnd->SetY( intval );
			} else if (attrib == "width") {
				wnd->SetWidth( intval );
			} else if (attrib == "height") {
				wnd->SetHeight( intval );
			} else {
				cout << "  -- Unknown window property: " << attrib << endl;
			}
		}
		//Bind_to_UI(ui, node, pAttrib->Name(), pAttrib->Value() );
		pAttrib = pAttrib->Next();
	}

	if (node->NoChildren() ) {
		cout << "node has no children!" << endl;
		return;
	}

	//loopar igenom detta elements alla childs
	TiXmlNode *child = node->FirstChild();

	while (child)
	{
		//cout << "child node of type " << child->Type() << endl;
		switch (child->Type())
		{
			case child->COMMENT:
				//Comment: Just ignore it
				break;

			case child->ELEMENT:
//				cout << "element" << endl;
				XML_Element_to_UI(ui, child );
				break;

			case child->TEXT:
				cout << "  text for <" << node->Value() << ">: " << child->Value() << endl;
//				Bind_to_UI(ui, node, "", child->Value() );
				break;

			default:
				cout << "todo: unhandled node type: " << child->Type() << endl;
		}

		child = child->NextSibling();
	}

	return;

/*
	if (s1 == "script") {
		//cout << "script tag" << endl;
	} else if (s1 == "window") {
		//cout << "window tag" << endl;
		//Window content sub-loop
		TiXmlNode *w_node = 0;
		w_node = node->FirstChildElement();
		while (w_node) {
				
			if ( w_node->FirstChild()->Type() == w_node->ELEMENT) {
				cout << "element name: " << w_node->Value() << endl;
				cout << "value: " << w_node->FirstChild()->Value() << endl;
				dump_attribs_to_stdout(w_node->FirstChild()->ToElement());
			} else {

				string key = w_node->Value();
				string param = w_node->FirstChild()->Value();

				//cout << key << " = " << param << endl;

				if (key == "title") {
//					cout << "setting title" << endl;
					wnd.SetTitle(param);
				} else if (key == "position") {
					if (param == "relative") {
						wnd.DisableAbsolutePositioning();
					} else {
						wnd.EnableAbsolutePositioning();
					}
				} else if (key == "font") {
					wnd.SetFont(param);
				} else {
					cout << "unknown property for <window> container: " << w_node->Value() << endl;
				}
			}

			w_node = w_node->NextSiblingElement();
		}
	} else {
		cout << "Unknown tag: " << node->Value() << endl;
	}
	dump_attribs_to_stdout(node->ToElement());

	*/
}


int main()
{

	CInterface ui;



	const char* pFilename = "ui_layout.xml";

	TiXmlDocument doc( pFilename );

	if (!doc.LoadFile())
	{
		cout << "Could not load test file '" << pFilename << "'. Error='" << doc.ErrorDesc() << "'. Exiting." << endl;
		exit( 1 );
	}

	cout << pFilename << " loaded." << endl;


	TiXmlNode *uiNode = doc.FirstChild("ui");	//Pekare på <ui> rot-elementet

	if (!uiNode) {
		cout << "XML Error: <ui> tag not found!" << endl;
		exit(1);
	}

	XML_Element_to_UI(ui, uiNode);

	ui.Window( 1 )->ShowSettings();

/*
	ui.ShowSettings();
	ui.Draw();
*/




	/*
		TiXmlElement* pElem;

		pElem=uiElement.FirstChild( "Messages" ).FirstChild().Element();
		for( pElem; pElem; pElem=pElem->NextSiblingElement())
		{
			const char *pKey=pElem->Value();
			const char *pText=pElem->GetText();
			if (pKey && pText) 
			{
				//m_messages[pKey]=pText;
			}
		}
*/
//	dump_attribs_to_stdout(current, 4);

//	printf("child node name: %s\n", node->G
	//printf("child node val: %s\n", node->Value() );



	doc.Clear();

	return 0;
}