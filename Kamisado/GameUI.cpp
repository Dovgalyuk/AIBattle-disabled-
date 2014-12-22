#include "GameUI.h"

GameUI::GameUI(const QString& gameName, QSize fieldSize)
	: BaseGameUi(gameName, fieldSize)
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
    p.setPen(QPen(Qt::black));

	int fieldWidth = fieldPixmap.width(); 
	int fieldHeight = fieldPixmap.height();
    int cellWidth = fieldWidth / fieldSize;
    int cellHeight = fieldHeight / fieldSize;
    for (int i = 1 ; i < fieldSize ; ++i)
    {
	    p.drawLine(cellWidth * i, 0, cellWidth * i, fieldHeight);
	    p.drawLine(0, cellHeight * i, fieldWidth, cellHeight * i);
    }

    unsigned char col1 = getWinColors(1);
    unsigned char col2 = getWinColors(2);

    for (int i = 0; i < fieldSize; ++i)
        for (int j = 0; j < fieldSize; ++j)
        {
            p.fillRect(j * cellWidth, i * cellHeight, cellWidth - 1, cellHeight - 1, colors[ColorField[i][j] - 1]);
            /*
            if (col1 & (1 << (ColorField[i][j] - 1)))
            {
                p.fillRect(j * cellWidth, i * cellHeight, cellWidth * 0.2, cellHeight * 0.2, Qt::white);
            }
            if (col2 & (1 << (ColorField[i][j] - 1)))
            {
                p.fillRect(j * cellWidth + 0.8 * cellWidth, i * cellHeight, cellWidth * 0.2, cellHeight * 0.2, Qt::black);
            }
            */
        }

    // Отрисовка белых
    for (int i = 0; i < whiteFigures.size(); ++i) 
        drawFigure(p, whiteFigures[i].c - 1, whiteFigures[i].r - 1, colors[i], Qt::white);

    // Отрисовка черных
    for (int i = 0; i < blackFigures.size(); ++i)
        drawFigure(p, blackFigures[i].c - 1, blackFigures[i].r - 1, colors[i], Qt::black);

	p.end();
	// Запоминаем pixmap
	fieldLabel->setPixmap(fieldPixmap);
}


void GameUI::setField(const QString &field)
{
    QString f(field);
	QTextStream stream(&f);
    
    prepareGame();

    for (int i = 0; i < fieldSize; ++i)
    {
        int k,f;
        stream >> k >> f;
        whiteFigures.push_back(node(k, f));
        this->field[k - 1][f - 1] = true;
    }

    for (int i = 0; i < fieldSize; ++i)
    {
        int k,f;
        stream >> k >> f;
        blackFigures.push_back(node(k, f));
        this->field[k - 1][f - 1] = true;
    }
}

void GameUI::prepareGame()
{
    whiteFigures.clear();
    blackFigures.clear();
    for (int i = 0 ; i < fieldSize ; ++i)
        for (int j = 0 ; j < fieldSize ; ++j)
            field[i][j] = false;
}

void GameUI::drawFigure(QPainter& p, const int i, const int j, const QColor brushColor, const QColor baseColor)
{
    p.setRenderHint(QPainter::Antialiasing, true);
    p.setBrush(QBrush(brushColor/*.darker(140)*/));
    p.setPen(QPen(baseColor, 4));
    int fieldWidth = fieldBaseSize.width(); 
	int fieldHeight = fieldBaseSize.height();
    const int w = fieldWidth / fieldSize;
    const int h = fieldHeight / fieldSize;
    p.drawEllipse((int)((i + 0.15) * w), (int)((j + 0.15) * h), (int)(w * 0.7), (int)(h * 0.7)); 
}

unsigned char GameUI::getWinColors(int player)
{
    if (whiteFigures.empty() || blackFigures.empty())
        return 0;

    unsigned char res = 0;
    int dr = player == 1 ? 1 : -1;
    for (int i = 0 ; i < 8 ; ++i)
    {
        node figure = player == 1 ? whiteFigures[i] : blackFigures[i];
        int tr = figure.r - 1;
        int tc = figure.c - 1;
        bool ok = false;
        for (int dc = -1 ; !ok && dc <= 1 ; ++dc)
        {
            int r = tr + dr;
            int c = tc + dc;
            while (!ok && r >= 0 && r < 8 && c >= 0 && c < 8 && !field[r][c])
            {
                if ((player == 1 && r == 7) || (player == 2 && r == 0))
                    ok = true;
                r += dr;
                c += dc;
            }
        }
        if (ok)
        {
            res |= 1 << i;
        }
    }
    return res;
}
