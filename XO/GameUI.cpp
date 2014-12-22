#include "GameUI.h"

GameUI::GameUI(const QString& gameName, QSize fieldSize)
	: BaseGameUi(gameName, fieldSize)
{
	prepareGame();
	drawField();
}

void GameUI::drawX(const int i, const int j, const int player, QPainter& p)
{
	QPixmap* fieldPixmap = getFieldPixmap();
	int newX = fieldPixmap->height()/6 + fieldPixmap->height()/3*j;
	int newY = fieldPixmap->height()/6 + fieldPixmap->height()/3*i;
	QPoint centerPoint(newX, newY);
	
	QPoint p1(centerPoint.x() - 15, centerPoint.y() - 15);
	QPoint p2(centerPoint.x() + 15, centerPoint.y() + 15);
	QPoint p3(centerPoint.x() + 15, centerPoint.y() - 15);
	QPoint p4(centerPoint.x() - 15, centerPoint.y() + 15);

	p.begin(fieldPixmap);

	p.setPen(QPen(player == 1 ? Qt::red : Qt::blue));
	p.drawLine(p1, p2);
	p.drawLine(p3, p4);
}

void GameUI::drawO(const int i, const int j, const int player, QPainter& p)
{
	QPixmap* fieldPixmap = getFieldPixmap();
	int newX = fieldPixmap->height()/6 + fieldPixmap->height()/3*j;
	int newY = fieldPixmap->height()/6 + fieldPixmap->height()/3*i;
	QPoint centerPoint(newX, newY);

	p.begin(fieldPixmap);

	p.setPen(QPen(player == 1 ? Qt::red : Qt::blue));
	p.drawEllipse(centerPoint, 15, 15);
}

void GameUI::prepareGame()
{
	currentGameField = fieldState();
}

void GameUI::drawField()
{
	
	QPixmap fieldPixmap(fieldBaseSize);
	// Отрисовка поля
	QPainter p;
	p.begin(&fieldPixmap);

	p.fillRect(fieldPixmap.rect(), p.background());
	p.setBrush(QBrush(Qt::black));

	int fieldSizeWidht = fieldPixmap.width(); 
	int fieldSizeHeight = fieldPixmap.height();

	p.drawLine(fieldSizeWidht/3, 0, fieldSizeWidht/3, fieldSizeHeight);
	p.drawLine(2*fieldSizeWidht/3, 0, 2*fieldSizeWidht/3, fieldSizeHeight);
	p.drawLine(0, fieldSizeHeight/3, fieldSizeWidht, fieldSizeHeight/3);
	p.drawLine(0, 2*fieldSizeHeight/3, fieldSizeWidht, 2*fieldSizeHeight/3);

	p.setBrush(QBrush(Qt::NoBrush));

	// Oтрисовка значений на поле

	for (int i = 0; i < 3; ++i)
		for (int j = 0; j < 3; ++j)
			if (currentGameField.field[i][j] == 1)
				drawX(i, j, 1, p);
			else 
			if (currentGameField.field[i][j] == 2)
				drawO(i, j, 2, p);
	p.end();
	// Запоминаем pixmap
	fieldLabel->setPixmap(fieldPixmap);
}

void GameUI::move(int player, const QString &turn)
{
	int i, j;
    QString t(turn);
	QTextStream logStream(&t);
	
	logStream >> i >> j;

	currentGameField.field[i - 1][j - 1] = player;
}
