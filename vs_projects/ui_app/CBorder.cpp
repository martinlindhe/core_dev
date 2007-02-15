#include "CBorder.h"

CBorder::CBorder()
{
	//CBorder constructor
	//cout << "CBorder constructed" << endl;
	this->size = 1;
	this->color = 0xFFFFFF;
}
CBorder::~CBorder()
{
	//CBorder deconstructor
}

void CBorder::SetSize(int size) {	this->size = size; }
void CBorder::SetColor(int c) {		this->color = c; }

int CBorder::GetSize() {			return this->size; }
int CBorder::GetColor() {			return this->color; }

void CBorder::ShowSettings()
{
	cout << "--[Border Settings]--" << endl;
	cout << "Size    : " << this->size << endl;
	cout << "Color   : " << this->color << endl;
}

void CBorder::Draw()
{
	if (!this->size) return;

	//Drawing border
	cout << "Drawing border" << endl;
}