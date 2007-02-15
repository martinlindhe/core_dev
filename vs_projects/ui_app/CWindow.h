#include <iostream>
#include <string>

#include "CBorder.h"

using namespace std;

//UI Window class
class CWindow
{
private:
	int x, y, w, h;
	bool absolute_position;
	string title;
	string font;//font class.??

public:
	CBorder border;

	CWindow::CWindow();
	CWindow::~CWindow();

	void SetTitle(const string &title);
	void SetFont(const string &font);
	void SetWidth(int w);
	void SetHeight(int h);
	void SetX(int x);
	void SetY(int y);

	string GetTitle();
	string GetFont();
	int GetWidth();
	int GetHeight();
	int GetX();
	int GetY();

	void EnableAbsolutePositioning();
	void DisableAbsolutePositioning();

	void ShowSettings();

	void Draw();
};
