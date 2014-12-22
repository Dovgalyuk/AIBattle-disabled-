#ifndef GAMEUI_H
#define GAMEUI_H

#include "BaseGameUi.h"

struct Point 
{
    int row;
    int col;
    Point() : row(0), col(0) {}
    Point(int objectRow, int objectCol) 
        : row(objectRow), col(objectCol) 
    {}
};

struct TankState
{
    int energy;
    int direction;
    int cooldown;

    TankState() 
        : energy(0), direction(0), cooldown(0) 
    {}
    TankState(int tankEnergy, int tankDirection, int tankCooldown) 
        : energy(tankEnergy), direction(tankDirection), cooldown(tankCooldown) 
    {}
};

struct AnimationState
{
    char object;
    double startAnimationTime;
    double endAnimationTime;
    Point startPoint;
    Point endPoint;
    AnimationState() : 
        object(' '),
        startAnimationTime(0),
        endAnimationTime(0),
        startPoint(),
        endPoint()
        {}
    AnimationState(char objectName, double startTime, double endTime, Point start, Point end) :
        object(objectName),
        startAnimationTime(startTime),
        endAnimationTime(endTime),
        startPoint(start),
        endPoint(end)
        {}
};

class GameUI : public BaseGameUi
{
	Q_OBJECT

public:
	GameUI(const QString& gameName, QSize fieldSize);
private:
    bool firstTurn;
    int fieldSize;
    QStringList field;
    QVector<QStringList> fieldHistory;
    QVector<TankState> tanks, prevTanks;
    QVector<AnimationState> animatedObjects;
    QImage firstTank;
    QImage secondTank;
    QImage wall;
    QImage bullet;
    QImage energy;
    QImage fire;

    virtual void move(int player, const QString &turn) {} // not needed yet
    virtual void setField(const QString &fieldString);
	virtual void drawField(); // Отрисовка поля
    virtual void setAnimation(const QString &animation);

    QImage getImageByCell(char cell);
    void drawImageByCell(char cell, double left, double top, int cellWidth, int cellHeight, QPainter& p);
    void drawTank(char cell, double left, double top, int cellWidth, int cellHeight, bool prev, QPainter& p);
private slots:
	void prepareGame();
};

#endif // GAMEUI_H
