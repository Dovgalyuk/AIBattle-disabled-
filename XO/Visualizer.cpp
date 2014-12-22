#include <QtGui/QApplication>
#include "GameUI.h"

int main(int argc, char *argv[])
{
	QApplication a(argc, argv);
	
	GameUI w(QString("XO"), QSize(180, 180));
	w.show();

	return a.exec();
}
