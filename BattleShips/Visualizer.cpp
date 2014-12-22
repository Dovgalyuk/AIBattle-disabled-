#include <QtGui/QApplication>
#include "GameUI.h"

int main(int argc, char *argv[])
{
	QApplication a(argc, argv);
	
	GameUI w("BattleShips", QSize(450, 210));
	w.show();

	return a.exec();
}
