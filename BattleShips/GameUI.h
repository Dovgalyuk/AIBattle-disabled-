#ifndef GAMEUI_H
#define GAMEUI_H

#include <QMap.h>
#include "BaseGameUi.h"

enum Cell
{
    Empty = 0,
    Miss = 1,
    Shot = 2,
    Killed = 3,
    Ship = 4
};

class GameUI : public BaseGameUi
{
	Q_OBJECT
public:
	GameUI(const QString& gameName, QSize fieldSize);

private:
    static const int fieldSize = 7;
	Cell fields[2][fieldSize][fieldSize];

    // not needed yet
    virtual void move(int player, const QString &turn) {}
    virtual void setField(const QString &field);
    // Отрисовка поля
	virtual void drawField();

private slots:
	void prepareGame();
};

#endif // GAMEUI_H
