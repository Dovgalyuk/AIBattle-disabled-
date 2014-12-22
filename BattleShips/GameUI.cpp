#include <QColor.h>
#include <QPen.h>
#include "GameUI.h"

GameUI::GameUI(const QString& gameName, QSize fieldSize)
	: BaseGameUi(gameName, fieldSize, NULL, true)
{
	prepareGame();
	drawField();
}

void GameUI::drawField()
{
	QPixmap fieldPixmap(fieldBaseSize);
    
	// Отрисовка поля
	QPainter p;
	p.begin(&fieldPixmap);

    int cellsCount = fieldSize * 2 + 1;
	int fieldWidth = fieldPixmap.width() / cellsCount * fieldSize; 
	int fieldHeight = fieldPixmap.height();
    int cellWidth = fieldWidth / fieldSize;
    int cellHeight = fieldHeight / fieldSize;
    int fieldOffset = fieldWidth + cellWidth;

    QColor sea(64, 255, 255);
    p.fillRect(0, 0, fieldWidth, fieldHeight, sea);
    p.fillRect(fieldOffset, 0, fieldWidth, fieldHeight, sea);

    QColor ship(192, 192, 192);
    QColor shipDead(0, 0, 128);
    p.setPen(QPen(QBrush(Qt::red), 2));
    for (int player = 0 ; player < 2 ; ++player)
        for (int i = 0 ; i < fieldSize ; ++i)
            for (int j = 0 ; j < fieldSize ; ++j)
            {
                int x = fieldOffset * player + j * cellWidth;
                int y = i * cellHeight;
                switch (fields[player][i][j])
                {
                case Ship:
                case Shot:
                    p.fillRect(x, y, cellWidth, cellHeight, ship);
                    break;
                case Killed:
                    p.fillRect(x, y, cellWidth, cellHeight, shipDead);
                    break;
                }
                switch (fields[player][i][j])
                {
                case Shot:
                case Killed:
                case Miss:
                    p.drawLine(x, y, x + cellWidth, y + cellHeight);
                    p.drawLine(x, y + cellHeight, x + cellWidth, y);
                    break;
                }
            }
    
    p.setPen(QPen(QBrush(Qt::black), 1));
    for (int i = 0 ; i <= fieldSize ; ++i)
    {
	    p.drawLine(cellWidth * i, 0, cellWidth * i, fieldHeight);
	    p.drawLine(0, cellHeight * i, fieldWidth, cellHeight * i);
	    p.drawLine(fieldOffset + cellWidth * i, 0, fieldOffset + cellWidth * i, fieldHeight);
	    p.drawLine(fieldOffset, cellHeight * i, fieldOffset + fieldWidth, cellHeight * i);
    }

    p.end();

	// Запоминаем pixmap
	fieldLabel->setPixmap(fieldPixmap);
}


void GameUI::setField(const QString &field)
{
    QString f(field);
	QTextStream stream(&f);
    for (int player = 0 ; player < 2 ; ++player)
        for (int i = 0 ; i < fieldSize ; ++i)
            for (int j = 0 ; j < fieldSize ; ++j)
            {
                int v;
                stream >> v;
                fields[player][i][j] = (Cell)v;
            }

    // then goes the score
    int score1, score2;
    stream >> score1 >> score2;
   
    setScore(score1, score2);
}

void GameUI::prepareGame()
{
    for (int player = 0 ; player < 2 ; ++player)
        for (int i = 0 ; i < fieldSize ; ++i)
            for (int j = 0 ; j < fieldSize ; ++j)
                fields[player][i][j] = Empty;
}
