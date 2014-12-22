#include <QtGui/QApplication>
#include "GameUI.h"

int main(int argc, char *argv[])
{
	QApplication a(argc, argv);
	
	GameUI w(QString("Kamisado"), QSize(1.5*192, 1.5*192));
	w.show();

	return a.exec();
}
