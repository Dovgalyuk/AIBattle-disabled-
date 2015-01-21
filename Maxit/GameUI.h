#ifndef GAMEUI_H
#define GAMEUI_H

#include <QMap>
#include "BaseGameUi.h"

struct Cell
{
    int x,y;
    Cell() : x(0), y(0) {}
    Cell(const int i, const int j) : x(i), y(j) {}
    bool operator==(const Cell& nc) {return x == nc.x && y == nc.y;}
};

class GameUI : public BaseGameUi
{
	Q_OBJECT
public:
	GameUI(const QString& gameName, QSize fieldSize);

private:
    static const int fieldSize = 6;
	int field[fieldSize][fieldSize];

    virtual void move(int player, const QString &turn) {}; // not needed yet
    virtual void setField(const QString &field);
	virtual void drawField(); // Отрисовка поля

    int currentPlayer;
    Cell currentTurn;

private slots:
	void prepareGame();
};

#endif // GAMEUI_H
