#include <iostream>
#include <string>

#include "CWindow.h"

using namespace std;

//UI Interface class
class CInterface
{
private:
	int width, height;
	
	//number of windows in this interface
	int cnt_windows;

public:
	CWindow *windows;

	CInterface::CInterface();
	CInterface::~CInterface();

	void SetWidth(int w);
	void SetHeight(int h);

	int GetWidth();
	int GetHeight();

	int AddWindow();			//returns the windowId of the created window
	CWindow *Window(int id);
	int GetLastWindow();		//returns the windowId of the last created window

	void ShowSettings();

	void Draw();
};
