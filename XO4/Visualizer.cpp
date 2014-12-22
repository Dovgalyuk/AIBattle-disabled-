#include <QtGui/QApplication>
#include "GameUI.h"

int main(int argc, char *argv[])
{
	QApplication a(argc, argv);
	
	GameUI w(QString("XO4"), QSize(256, 256));
	w.show();

	return a.exec();
}
