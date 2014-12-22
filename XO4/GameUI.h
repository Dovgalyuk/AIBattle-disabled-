#ifndef GAMEUI_H
#define GAMEUI_H

#include "BaseGameUi.h"

class GameUI : public BaseGameUi
{
	Q_OBJECT

private:
    static const int fieldSize = 4;
	int field[fieldSize][fieldSize];

    virtual void move(int player, const QString &turn) {}
    virtual void setField(const QString &field);
    virtual void drawField(); // Отрисовка поля
public:
	GameUI(const QString& gameName, QSize fieldSize);
};

#endif // GAMEUI_H
