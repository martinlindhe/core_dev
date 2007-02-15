#include <iostream>

using namespace std;

//UI Border class
//CWindow has this as child
class CBorder
{
private:
	bool visible, rounded;
	int size, color;

public:
	CBorder::CBorder();
	CBorder::~CBorder();

	void SetSize(int);
	void SetColor(int);

	int GetSize();
	int GetColor();

	void ShowSettings();
	void Draw();
};
