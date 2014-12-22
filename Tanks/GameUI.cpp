#include "GameUI.h"
#include <vector>

GameUI::GameUI(const QString& gameName, QSize fieldSize)
	: BaseGameUi(gameName, fieldSize)
{
	prepareGame();
    GameUI::fieldSize = 11;
    tanks.resize(4);

    wall.load(":/images/Wall.png");
    firstTank.load(":/images/Tank1.png");
    secondTank.load(":/images/Tank2.png");
    bullet.load(":/images/Bullet.png");
    energy.load(":/images/Energy.png");
    fire.load(":/images/Fire.png");

    drawField();
    QWidget::move(100, 100);

}

void GameUI::drawField()
{
	QPixmap fieldPixmap(fieldBaseSize);
	// Отрисовка поля
	QPainter p;
	p.begin(&fieldPixmap);

	p.fillRect(fieldPixmap.rect(), Qt::black);
	p.setBrush(QBrush(Qt::black));
    p.setPen(QPen(Qt::black));
    
    if (field.size())
        fieldSize = field[0].length();

	int fieldWidth = fieldPixmap.width(); 
	int fieldHeight = fieldPixmap.height();
    int cellWidth = fieldWidth / fieldSize;
    int cellHeight = fieldHeight / fieldSize;

    p.setPen(QPen(Qt::red));

    int atc = getAnimationTimerCount();
    int ati = animationTimerInterval;
    if (field.size() > 0)
    {
        for (int i = 0; i < fieldSize; ++i)
            for (int j = 0; j < fieldSize; ++j)
            {
                if (fieldHistory[0][i][j] == fieldHistory[1][i][j]
                    || atc >= ati)
                {
                    char cell = field[i][j].toAscii();
                    if (cell != '.')
                         drawImageByCell(cell, j * cellWidth, i * cellHeight, cellWidth, cellHeight, p);
                    if (cell >= '1' && cell <= '4')
                        drawTank(cell, j * cellWidth, i * cellHeight, cellWidth, cellHeight, false, p);
                }
            }
    }

    if (!firstTurn && atc < ati)
    {
        
        double animationTime = (double)atc / ati;
        for (int i = 0 ; i < animatedObjects.size() ; ++i)
        {
            
            if  (animationTime >= animatedObjects[i].startAnimationTime
                 && animationTime <= animatedObjects[i].endAnimationTime)
            {
                // Рисуем анимацию
                double dt = (animationTime - animatedObjects[i].startAnimationTime)
                    / (animatedObjects[i].endAnimationTime - animatedObjects[i].startAnimationTime);
                double dx = (animatedObjects[i].endPoint.col - animatedObjects[i].startPoint.col) * dt;
                double dy = (animatedObjects[i].endPoint.row - animatedObjects[i].startPoint.row) * dt;
                double tx = (animatedObjects[i].startPoint.col + dx) * cellWidth;
                double ty = (animatedObjects[i].startPoint.row + dy) * cellHeight;

                drawImageByCell(animatedObjects[i].object, tx, ty, cellWidth, cellHeight, p);
                
                // Рисуем здоровье у танка
                if (animatedObjects[i].object >= '1' && animatedObjects[i].object <= '4')
                    drawTank(animatedObjects[i].object, tx, ty, cellWidth, cellHeight, true, p);
            }
        }
    }
	p.end();
	// Запоминаем pixmap
	fieldLabel->setPixmap(fieldPixmap);
}


void GameUI::setField(const QString &fieldString)
{
    QString f(fieldString);

    if (fieldHistory.size() == 2)
    {
        firstTurn = false;
        std::swap(fieldHistory[0], fieldHistory[1]);
    }

    field.clear();
    prevTanks = tanks;
    tanks.resize(4);
   
    QTextStream stream(&f);
    QString line = stream.readLine();

    GameUI::fieldSize = line.length();
    field.push_back(line);

    for (int i = 1; i < GameUI::fieldSize; ++i)
        field.push_back(stream.readLine());

    if (fieldHistory.size() == 0)
        for (int i = 0; i < 2; ++i)
            fieldHistory.push_back(field);
    else
        fieldHistory[1] = field;

    for (int i = 0; i < tanks.size(); ++i)
    {
        QString splitSource = stream.readLine();
        QStringList split = splitSource.split(QChar(' '));
        tanks[i] = TankState(split[0].toInt(), split[1].toInt(), split[2].toInt());
    }

}

void GameUI::prepareGame()
{
    firstTurn = true;
    fieldHistory.clear();
}

void GameUI::setAnimation(const QString &animation)
{
    animatedObjects.clear();

    QStringList split = animation.split(QChar('\n'));
    split.pop_back();
    
    for (int i = 0; i < split.size(); ++i)
    {
        QString source = split[i];
        QStringList animationSplit = source.split(QChar(' '));
        animatedObjects.push_back(AnimationState(
                    animationSplit[0][0].toAscii(),
                    animationSplit[1].toDouble(),
                    animationSplit[2].toDouble(),
                    Point(animationSplit[3].toInt(), animationSplit[4].toInt()),
                    Point(animationSplit[5].toInt(), animationSplit[6].toInt())
                ));
    }
}

QImage GameUI::getImageByCell(char cell)
{
    switch (cell)
    {
        case '#':
            return wall;
        case '1':
        case '2':
        case '3':
        case '4':
            {
                const QImage &image = (cell == '1' || cell == '2') ? firstTank : secondTank;
                switch (tanks[cell - '0' - 1].direction)
                {
                    case 1:
                        return image.transformed(QTransform().rotate(-90));
                    case 3:
                        return image.transformed(QTransform().rotate(90));
                    case 4:
                        return image.mirrored(true);
                }
                return image;
            }
        case '^':
        case 'v':
        case '>':
        case '<':
            {
                const QImage &image = bullet;
                switch (cell)
                {
                    case 'v':
                        return image.mirrored(true);
                    case '>':
                        return image.transformed(QTransform().rotate(90));
                    case '<':
                        return image.transformed(QTransform().rotate(-90));
                }
                return image;
            }
        case 'e':
            return energy;
        case 'x':
            return fire;
    }

    return QImage();
}

void GameUI::drawImageByCell(char cell, double left, double top, int cellWidth, int cellHeight, QPainter& p)
{
    QImage image = getImageByCell(cell);
    QRect target(left, top, cellWidth, cellHeight);
    QRect source(image.rect());
    p.drawImage(target, image, source);
}

void GameUI::drawTank(char cell, double left, double top, int cellWidth, int cellHeight, bool prev, QPainter& p)
{
    QPen energyPen(Qt::green, 3);
    p.setPen(energyPen);

    double barSize = cellWidth * ((prev ? prevTanks : tanks)[cell - '1'].energy/100.0);
    p.drawLine(left, top - 1, left + barSize, top - 1);

    QPen reloadPen(Qt::blue, 3);
    p.setPen(reloadPen);

    if (tanks[cell - '1'].cooldown > 0)
    {
        barSize = cellHeight * ((prev ? prevTanks : tanks)[cell - '1'].cooldown / 5.0);
        p.drawLine(left, top - 1, left, top - 1 + barSize);
    }

    p.setPen(Qt::black);
}
