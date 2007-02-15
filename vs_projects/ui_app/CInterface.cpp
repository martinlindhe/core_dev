#include "CInterface.h"

CInterface::CInterface()
{
	//CInterface constructor
	this->width = 0;
	this->height = 0;

	this->cnt_windows = 0;

	this->windows = new CWindow[100];	//todo: allokera nya CWindows vid AddWindow() istället
}

CInterface::~CInterface()
{
	//CInterface deconstructor

	//todo: frigör alla this->windows
}

void CInterface::SetWidth(int w) {	this->width = w; }
void CInterface::SetHeight(int h) {	this->height = h; }

int CInterface::GetWidth() {		return this->width; }
int CInterface::GetHeight() {		return this->height; }

void CInterface::ShowSettings()
{
	cout << "--[CInterface settings]--" << endl;
	cout << "Windows    : " << this->windows << endl;
	cout << endl;
}

void CInterface::Draw()
{
	cout << " drawing UI" << endl;
}

/* Add a Window to the UI class, return the windowId */
int CInterface::AddWindow()
{
	//todo: lock cnt_windows for thread safety

	CWindow *wnd = new CWindow;

	this->cnt_windows++;
	cout << "creating window " << this->cnt_windows << endl;

	//todo: skapa ett window i arrayen this->windows[ this->cnt_windows ]
	this->windows[ this->cnt_windows ].ShowSettings();

	return this->cnt_windows;
}

/* */
CWindow *CInterface::Window(int id)
{
//	CWindow wnd;
//	if (!this->windows[id]) return 0;

//	cout << "  -- accessing window " << id << endl;
	return &this->windows[id];

//	return wnd;
}

int CInterface::GetLastWindow()
{
	return this->cnt_windows;
}