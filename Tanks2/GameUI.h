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
    int player;
    int energy;
    int direction;
    int cooldown;
    bool hasFlag;

    TankState() 
        : player(0), energy(0), direction(0), cooldown(0), hasFlag(false)
    {}
    TankState(int pl, int tankEnergy, int tankDirection, int tankCooldown, bool tankHasFlag) 
        : player(pl), energy(tankEnergy), direction(tankDirection),
          cooldown(tankCooldown), hasFlag(tankHasFlag)
    {}
};

struct AnimationState
{
    QString object;
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
    AnimationState(const QString &objectName, double startTime, double endTime, Point start, Point end) :
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
	GameUI(const QString &gameName, QSize fieldSize);
private:
    bool firstTurn;
    int fieldSize;
    QStringList field;
    QVector<QStringList> fieldHistory;
    QVector<TankState> tanks, prevTanks;
    QVector<AnimationState> animatedObjects;
    Point flags[2];
    QImage firstTank;
    QImage secondTank;
    QImage wall;
    QImage bullet;
    QImage energy;
    QImage fire;
    QImage firstFlag;
    QImage secondFlag;

    virtual void move(int player, const QString &turn) {} // not needed yet
    virtual void setField(const QString &fieldString);
	virtual void drawField(); // Отрисовка поля
    virtual void setAnimation(const QString &animation);
    QImage getTankImage(const TankState &tank, int dir) const;
    static int getDirection(char dir);

    QImage getImageByCell(const QString &cell);
    void drawImageByCell(const QString &cell, double left, double top, int cellWidth, int cellHeight, QPainter& p);
    void drawTank(const TankState &tank, double left, double top, int cellWidth, int cellHeight, QPainter& p);
private slots:
	void prepareGame();
};

#endif // GAMEUI_H
