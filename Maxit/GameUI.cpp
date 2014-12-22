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

	p.fillRect(fieldPixmap.rect(), p.background());
	p.setBrush(QBrush(Qt::black));

	int fieldWidth = fieldPixmap.width(); 
	int fieldHeight = fieldPixmap.height();
    int cellWidth = fieldWidth / fieldSize;
    int cellHeight = fieldHeight / fieldSize;
    for (int i = 1 ; i < fieldSize ; ++i)
    {
	    p.drawLine(cellWidth * i, 0, cellWidth * i, fieldHeight);
	    p.drawLine(0, cellHeight * i, fieldWidth, cellHeight * i);
    }

    QFont font("Courier New", 24);
    p.setFont(font);

    bool isFirst = true;
    for (int i = 0; i < fieldSize; ++i)
    {
        for (int j = 0; j < fieldSize; j++)
            if (field[i][j] < 0)
            {
                isFirst = false;
                break;
            }
        if (!isFirst) break;
    }

    for (int i = 0 ; i < fieldSize ; ++i)
    {
        for (int j = 0 ; j < fieldSize ; ++j)
        {
            if (field[i][j] != 0)
            {
                p.setPen(QPen(Qt::black));
                QTextStream str;
                QString s = QString::number(abs(field[i][j]));

                if (field[i][j] < 0)
                {
                    currentTurn = Cell(i,j);
                    if (currentPlayer == 1) 
                       p.setPen(QPen(Qt::red));
                    else
                    if (currentPlayer == 2) 
                       p.setPen(QPen(Qt::blue));
                }

                
                QColor color = QColor(200, 255, 160);
                
                if (isFirst)
                {
                    if (currentTurn.x == i)
                        p.fillRect(j*cellHeight, i*cellWidth, cellWidth - 1, cellHeight - 1, color);
                }
                else
                {
                    if (!isFirst && currentPlayer == 1 && currentTurn.y == j)
                    p.fillRect(j*cellHeight, i*cellWidth, cellWidth - 1, cellHeight - 1, color);
                    else
                    if (currentPlayer == 2 && currentTurn.x == i)
                        p.fillRect(j*cellHeight, i*cellWidth, cellWidth - 1, cellHeight - 1, color);
                }
                
                p.drawText(j * cellWidth, i * cellHeight, cellWidth, cellHeight, Qt::AlignCenter, s);
            }
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
    int player, r, c, score1, score2;
    stream >> player >> r >> c >> score1 >> score2;
   
    currentPlayer = player;
    currentTurn = Cell(r - 1, c - 1);

    setScore(score1, score2);
}

void GameUI::prepareGame()
{
    for (int i = 0 ; i < fieldSize ; ++i)
        for (int j = 0 ; j < fieldSize ; ++j)
            field[i][j] = 0;
}

/*
void GameUI::move(int player, const QString &turn)
{
    /
    if (player == 1) 
        currentTurn.y = turn.toInt() - 1;
    else
        currentTurn.x = turn.toInt() - 1;
}
*/