#ifndef GAMEUI_H
#define GAMEUI_H

#include <qmap.h>
#include "BaseGameUi.h"

struct fieldState
{
	// 0 - пустое поле
	// 1 - первый игрок
	// 2 - второй игрок
	int field[3][3];
	fieldState()
	{
		for (int i = 0; i < 3; i++)
			for (int j = 0; j < 3; j++)
				field[i][j] = 0;
	}
};

class GameUI : public BaseGameUi
{
	Q_OBJECT

private:
	fieldState currentGameField;

	virtual void move(int player, const QString &turn);
    virtual void setField(const QString &field) {} // not needed
    virtual void drawField(); // Отрисовка поля

	void drawX(const int i, const int j, const int player, QPainter& p);
	void drawO(const int i, const int j, const int player, QPainter& p);

private slots:
	void prepareGame();

public:
	GameUI(const QString& gameName, QSize fieldSize);
};

#endif // GAMEUI_H
