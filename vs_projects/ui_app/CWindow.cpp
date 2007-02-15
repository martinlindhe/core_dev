#include "CWindow.h"

CWindow::CWindow()
{
	//CWindow constructor
	//printf("constructing CWindow()\n");
	this->title = "Untitled";
	this->x = 0;
	this->y = 0;
	this->w = 0;
	this->h = 0;
	this->absolute_position = true; //x & y is absolute positioning
}

CWindow::~CWindow()
{
	//CWindow destructor
	//printf("destructing CWindow()\n");
}


void CWindow::SetTitle(const string &t) {	this->title = t; }
void CWindow::SetFont(const string &f) {	this->font = f; }
void CWindow::SetWidth(int w) {				this->w = w; }
void CWindow::SetHeight(int h) {			this->h = h; }
void CWindow::SetX(int x) {					this->x = x; }
void CWindow::SetY(int y) {					this->y = y; }

string CWindow::GetTitle() {				return this->title; }
string CWindow::GetFont() {					return this->font; }
int CWindow::GetWidth() {					return this->w; }
int CWindow::GetHeight() {					return this->h; }
int CWindow::GetX() {						return this->x; }
int CWindow::GetY() {						return this->y; }

void CWindow::EnableAbsolutePositioning() {	this->absolute_position = true; }
void CWindow::DisableAbsolutePositioning() {	this->absolute_position = false; }

void CWindow::ShowSettings()
{
	cout << "--[Window Settings]--" << endl;
	cout << "Position: " << this->x << "x" << this->y << endl;
	cout << "Absolute: " << this->absolute_position << endl;
	cout << "Size    : " << this->w << "x" << this->h << endl;
	cout << "Title   : " << this->title << endl;
	cout << "Font    : " << this->font << endl;

	this->border.ShowSettings();
}

//Draws the window on top of screen
void CWindow::Draw()
{
	if (!this->w || !this->h) return;

	//Drawing window, then draws the border
	cout << "Drawing window" << endl;
	this->border.Draw();
}