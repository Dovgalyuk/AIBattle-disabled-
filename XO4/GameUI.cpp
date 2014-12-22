#include "GameUI.h"

GameUI::GameUI(const QString& gameName, QSize fieldSize)
	: BaseGameUi(gameName, fieldSize, NULL, true)
{
	drawField();
}

void GameUI::drawField()
{
	
	QPixmap fieldPixmap(fieldBaseSize);
	// Отрисовка поля
	QPainter p;
	p.begin(&fieldPixmap);

	p.fillRect(fieldPixmap.rect(), p.background());
	p.setBrush(QBrush(Qt::black));

	int fieldSizeWidth = fieldPixmap.width(); 
	int fieldSizeHeight = fieldPixmap.height();
    int cellWidth = fieldSizeWidth / fieldSize;
    int cellHeight = fieldSizeHeight / fieldSize;
    for (int i = 1 ; i < fieldSize ; ++i)
    {
    	p.drawLine(cellWidth * i, 0, cellWidth * i, fieldSizeHeight);
    	p.drawLine(0, cellHeight * i, fieldSizeWidth, cellHeight * i);
    }
	p.setBrush(QBrush(Qt::NoBrush));

	// Oтрисовка значений на поле
    int w = (int)(cellWidth * 0.4);
    int h = (int)(cellHeight * 0.4);
	for (int i = 0 ; i < fieldSize ; ++i)
		for (int j = 0 ; j < fieldSize ; ++j)
        {
            QPoint center(cellWidth * j + cellWidth / 2, cellHeight * i + cellHeight / 2);
			if (field[i][j] == 1)
            {
            	p.setPen(QPen(Qt::red));
	            p.drawLine(center.x() - w, center.y() - h, center.x() + w, center.y() + h);
	            p.drawLine(center.x() - w, center.y() + h, center.x() + w, center.y() - h);
            }
			else if (field[i][j] == 2)
            {
            	p.setPen(QPen(Qt::blue));
	            p.drawEllipse(center, w, h);
            }
        }
	p.end();
	// Запоминаем pixmap
	fieldLabel->setPixmap(fieldPixmap);
}

void GameUI::setField(const QString &field)
{
    QString f(field);
	QTextStream stream(&f);
    for (int i = 0 ; i < fieldSize ; ++i)
        for (int j = 0 ; j < fieldSize ; ++j)
            stream >> this->field[i][j];

    // then goes the current cell and the score
    int score1, score2;
    stream >> score1 >> score2;
    setScore(score1, score2);
}

