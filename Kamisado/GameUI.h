#ifndef GAMEUI_H
#define GAMEUI_H

#include <QColor>
#include "BaseGameUi.h"

const QColor colors[8] = {QColor(80, 40, 0), Qt::green, Qt::red, Qt::yellow, Qt::magenta, Qt::cyan, Qt::blue, QColor(255, 148, 0)};
const int ColorField[8][8] =
{
    {8, 7, 6, 5, 4, 3, 2, 1},
    {3, 8, 5, 2, 7, 4, 1, 6},
    {2, 5, 8, 3, 6, 1, 4, 7},
    {5, 6, 7, 8, 1, 2, 3, 4},
    {4, 3, 2, 1, 8, 7, 6, 5},
    {7, 4, 1, 6, 3, 8, 5, 2},
    {6, 1, 4, 7, 2, 5, 8, 3},
    {1, 2, 3, 4, 5, 6, 7, 8}
};

struct node {int r,c; node(int _r = 0, int _c = 0) {r = _r; c = _c;}};

class GameUI : public BaseGameUi
{
	Q_OBJECT

public:
	GameUI(const QString& gameName, QSize fieldSize);

private:
    static const int fieldSize = 8;
	bool field[fieldSize][fieldSize];

    QVector<node> whiteFigures;
    QVector<node> blackFigures;

    virtual void move(int player, const QString &turn) {} // not needed yet
    virtual void setField(const QString &field);
	virtual void drawField(); // Отрисовка поля

    void drawFigure(QPainter& p, const int i, const int j, const QColor brushColor, const QColor baseColor);
    unsigned char getWinColors(int player);

private slots:
	void prepareGame();
};

#endif // GAMEUI_H
