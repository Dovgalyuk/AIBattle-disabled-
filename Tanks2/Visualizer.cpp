#include <QtGui/QApplication>
#include "GameUI.h"

int main(int argc, char *argv[])
{
	QApplication a(argc, argv);
	
    QStringList arg = a.arguments();

    GameUI w(QString("Tanks Battle v2"), QSize(32*15, 32*15));
    if (arg.size() == 2)
        w.setPreLog(arg[1]);

	w.show();

	return a.exec();
}
