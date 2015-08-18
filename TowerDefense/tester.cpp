#include <sstream>
#include <iostream>
#include "execution.h"
#include <list>
#include <vector>
#include <map>
#include <cmath>
#include <algorithm>
#include <cstdlib>
#include <ctime>
#include <assert.h>

using namespace std;

// описания полей

const int fieldSize = 10; // размер поля
const int maxMoves = 555; // максимальное количество ходов

enum FieldType 
{
    EMPTY = 0, // Пустая клетка
    FIRST_PLAYER_TOWER = 1, // Башня первого игрока
    SECOND_PLAYER_TOWER = 2, // Башня второго игрока
    FIRST_PLAYER_LOCATION = 500, // Местоположение первого игрока
    SECOND_PLAYER_LOCATION = 900, // Местоположение второго игрока
    FIRST_PLAYER_CANNON_FIRST_TYPE = 200, // Пушка 1-ого уровня первого игрока
    FIRST_PLAYER_CANNON_SECOND_TYPE = 300, // Пушка 2-ого уровня первого игрока
    FIRST_PLAYER_CANNON_THIRD_TYPE = 400, // Пушка 3-ого уровня первого игрока
    SECOND_PLAYER_CANNON_FIRST_TYPE = 600, // Пушка 1-ого уровня второго игрока
    SECOND_PLAYER_CANNON_SECOND_TYPE = 700, // Пушка 2-ого уровня второго игрока
    SECOND_PLAYER_CANNON_THIRD_TYPE = 800, // Пушка 3-ого уровня второго игрока
    GOLD = 1000 // Золотая монетка
};


FieldType field[fieldSize][fieldSize];

// Данные для пушек

// структура "координата"
struct Point
{
    int x,y;

    Point() : x(0), y(0) {}

    Point(int x, int y)
    {
        this->x = x;
        this->y = y;
    }

    // Проверка корректности точки
    static bool isCorrectPoint(int x, int y)
    {
        return x >= 0 && x < fieldSize && y >= 0 && y < fieldSize;
    }
    
    bool operator==(const Point &p) const
    {
        return x == p.x && y == p.y;
    }

    bool operator<(const Point &p) const
    {
        if (y != p.y)
            return y < p.y;
        return x < p.x;
    }

    bool isNormalPoint() const
    {
        return x != -INT_MAX && y != -INT_MAX;
    }
};

// манхэттаннское расстояние
int dist(const Point &p1, const Point &p2)
{
    return abs(p1.x - p2.x) + abs(p1.y - p2.y);
}

bool dist(const Point &p1, const Point &p2, int radius)
{
    return dist(p1, p2) <= radius;
}

enum CannonDir {DR, DL, UL, UR}; // Down-Right, Down-Left, Up-Left, Up-Right

enum CannonType {FIRST, SECOND, THIRD, UNKNOWN}; // типы пушек (первая, вторая, третья)

const int cannonHealth[] = {9, 12, 5};
const int cannonCost[] = {3, 7, 12}; // стоимость пушек
const int cannonScore[] = {2, 4, 7}; // очки за пушки
const int cannonPower[] = {2, 3, 4};
const int cannonRange[] = {1, 2, 3};

// Структура пушек
struct Cannon
{
    CannonType type;
    int health;

    Cannon() : type(UNKNOWN), health(0) {}
    
    Cannon(CannonType cannonType)
    {
        assert(type < UNKNOWN);
        type = cannonType;
        health = cannonHealth[type];
    }

    int getRadius() const
    {
        assert(type < UNKNOWN);
        return cannonRange[type];
    }
    
    int getPower() const
    {
        assert(type < UNKNOWN);
        return cannonPower[type];
    }
};

// typedef для листа пушек
typedef map<Point, Cannon> CannonMap;

// #####

// Данные для игроков

struct Player
{
    int gold, score, towerHealth;
    Point position;
    Player() : gold(0), score(0), towerHealth(100), position(-1, -1) {}
};

// #####

// Общие объявления 

CannonMap cannons[2]; // 0 - первый игрок, 1 - второй игрок, в данном массиве храним пушки игрока
Player players[2]; // тут хранится информация о игроках 

// функции

// получить здоровье пушки по координате
int getCannonHealth(int player, int x, int y)
{
    CannonMap playerCannons = cannons[player];
    
    CannonMap::iterator it = playerCannons.find(Point(x, y));
    if (it != playerCannons.end())
        return it->second.health;
    else 
        return -1;
}

// Получить однозначное представление клетки (в случае пушек необходимо подавать координаты!)
int convertFieldType(FieldType fieldType, int x, int y)
{
    switch (fieldType)
    {
    case EMPTY:
    case FIRST_PLAYER_LOCATION:
    case SECOND_PLAYER_LOCATION:
    case GOLD:
        return fieldType;
    case FIRST_PLAYER_TOWER:
        return players[0].towerHealth;
    case SECOND_PLAYER_TOWER:
        return players[1].towerHealth;
    case FIRST_PLAYER_CANNON_FIRST_TYPE:
    case FIRST_PLAYER_CANNON_SECOND_TYPE:
    case FIRST_PLAYER_CANNON_THIRD_TYPE:
        return fieldType + getCannonHealth(0, x, y);
    case SECOND_PLAYER_CANNON_FIRST_TYPE:
    case SECOND_PLAYER_CANNON_SECOND_TYPE:
    case SECOND_PLAYER_CANNON_THIRD_TYPE:
        return fieldType + getCannonHealth(1, x, y);
    }

    return 0;
}

void initField()
{
    for (int i = 0; i < fieldSize; ++i)
        for (int j = 0; j < fieldSize; ++j)
            field[i][j] = EMPTY;

    field[0][0] = FIRST_PLAYER_LOCATION;
    field[9][9] = SECOND_PLAYER_LOCATION;
    field[2][2] = FIRST_PLAYER_TOWER;
    field[7][7] = SECOND_PLAYER_TOWER;

    players[0].position = Point(0, 0);
    players[1].position = Point(9, 9);
}

void getField(std::ostringstream &outs)
{
    for (int i = 0; i < fieldSize; ++i)
    {
        for (int j = 0; j < fieldSize; ++j)
            outs << convertFieldType(field[i][j], j, i) << " ";

        outs << "\n";
    }
}

//Движение игрока
ExecutionResult checkMovement(bool firstPlayer, istringstream &ins, std::string &result)
{
    char movement;
    ins >> movement;

    int dx = -2, dy = -2;
    int playerIndex = firstPlayer == true ? 0 : 1;

    switch (movement)
    {
    case 'U':
        dy = -1;
        dx = 0;
        break;
    case 'L':
        dy = 0;
        dx = -1;
        break;
    case 'R':
        dy = 0;
        dx = 1;
        break;
    case 'D':
        dy = 1;
        dx = 0;
        break;
    }

    ostringstream outs;
    outs << "M " << movement << std::endl;
    result = outs.str();

    if (dy != -2 && dx != -2)
    {
        // у нас есть осмысленное направление движения игрока
        int y = players[playerIndex].position.y;
        int x = players[playerIndex].position.x;

        if (Point::isCorrectPoint(x + dx, y + dy))
        {
            // у нас корректный ход с точки зрения границ массива (золото тоже можно брать)
            if (field[y + dy][x + dx] == EMPTY || field[y + dy][x + dx] == GOLD)
            {
                FieldType currentPlayer = field[y][x];
                FieldType nextTurn = field[y + dy][x + dx];
                field[y][x] = EMPTY;
                field[y + dy][x + dx] = currentPlayer;

                players[playerIndex].position.y = y + dy;
                players[playerIndex].position.x = x + dx;

                if (nextTurn == GOLD)
                {
                    players[playerIndex].gold++;
                    players[playerIndex].score++;
                }

                return ER_OK;
            } 
        }
    }
    return ER_IM;
}

bool cannonPossibleToBuild(Point player, Point cannon)
{
    return abs(player.x - cannon.x) <= 1 && abs(player.y - cannon.y) <= 1 
        && (player.x != cannon.x || player.y != cannon.y); 
}

// строительство башни
ExecutionResult cannonBuilding(bool firstPlayer, istringstream &ins, std::string &result)
{
    CannonType cannonType = UNKNOWN;
    int cx = -2, cy = -2, cannon = UNKNOWN;

    ins >> cx >> cy >> cannon;
    cannonType = (CannonType)(cannon - 1);

    ostringstream outs;
    outs << "B " << cx << " " << cy << " " << cannon << std::endl; 

    result = outs.str();

    int playerIndex = firstPlayer == true ? 0 : 1;
    int y = players[playerIndex].position.y;
    int x = players[playerIndex].position.x;
    
    if (Point::isCorrectPoint(cx, cy) && cannonPossibleToBuild(players[playerIndex].position, Point(cx, cy)))
    {
        // проверка на то, что пользователь ввел корректное значение
        if (field[cy][cx] == EMPTY)
        {
            // башню можно построить, так как место пустое
            int cost = cannonCost[cannonType];
            if (players[playerIndex].gold >= cost)
            {
                // у игрока есть деньги на строительство
                players[playerIndex].gold -= cost;
                
                cannons[playerIndex].insert(make_pair(Point(cx, cy), Cannon(cannonType)));
                players[playerIndex].score += cannonScore[cannonType];

                FieldType cannonOnField = EMPTY;

                if (firstPlayer)
                {
                    switch (cannonType)
                    {
                    case FIRST:
                        cannonOnField = FIRST_PLAYER_CANNON_FIRST_TYPE;
                        break;
                    case SECOND:
                        cannonOnField = FIRST_PLAYER_CANNON_SECOND_TYPE;
                        break;
                    case THIRD:
                        cannonOnField = FIRST_PLAYER_CANNON_THIRD_TYPE;
                        break;
                    }
                }
                else
                {
                    switch (cannonType)
                    {
                    case FIRST:
                        cannonOnField = SECOND_PLAYER_CANNON_FIRST_TYPE;
                        break;
                    case SECOND:
                        cannonOnField = SECOND_PLAYER_CANNON_SECOND_TYPE;
                        break;
                    case THIRD:
                        cannonOnField = SECOND_PLAYER_CANNON_THIRD_TYPE;
                        break;
                    }
                }
                field[cy][cx] = cannonOnField;

                return ER_OK;
            } 
        } 
    } 
    return ER_IM;
}

// Выполнение хода игрока
ExecutionResult playerMove(bool firstPlayer, const char* program, std::string &result)
{
    ostringstream outs;
    string output;

    int player = firstPlayer == true ? 0 : 1;
    int enemy = 1 - player;

    outs << player + 1 << " " << players[player].gold << " " << players[enemy].gold  << "\n";
    getField(outs);

    printInput(firstPlayer, outs.str());

    //cout << outs.str() << endl;

    ExecutionResult execResult = runProcess(program, outs.str(), output, 1000, 64000); 
        
    if (execResult == ER_OK)
    {
        istringstream ins(output);
        char mode;

        ins >> mode;
        switch (mode)
        {
        case 'S':
            // ничего не делаем
            result = "S";
            return ER_OK;
        case 'M':
            // движение игрока
            return checkMovement(firstPlayer, ins, result);
        case 'B':
            // строительство башен
            return cannonBuilding(firstPlayer, ins, result);
        default:
            // какая-то неправильная команда - ничего не делаем
            result = mode;
            return ER_IM;
        }
    }

    result = output;
    return execResult;
}

bool checkCycleDR(int dx, int value)
{
    return dx < value; 
}

const int cannonCheckDX[] = {1, -1, -1, 1};
const int cannonCheckDY[] = {1, 1, -1, -1};

bool isPointContainsCannon(const Point &point, int enemy)
{
    // 0 -- первый игрок, 1 -- второй
    int x = point.x, y = point.y;
    bool result = false;
    switch (enemy)
    {
    case 0:
        result = (field[y][x] == FIRST_PLAYER_CANNON_FIRST_TYPE || 
                 field[y][x] == FIRST_PLAYER_CANNON_SECOND_TYPE ||
                 field[y][x] == FIRST_PLAYER_CANNON_THIRD_TYPE);
        break;
    case 1:
        result = (field[y][x] == SECOND_PLAYER_CANNON_FIRST_TYPE || 
                 field[y][x] == SECOND_PLAYER_CANNON_SECOND_TYPE ||
                 field[y][x] == SECOND_PLAYER_CANNON_THIRD_TYPE);
        break;
    }

    return result;
}

Point checkNearbyCannons(Point cannon, int enemy, int radius)
{
    Point damagedCannonPoint(-INT_MAX, -INT_MAX);
    int dx = 0, dy = -radius;

    //cout << "cannon: (" << cannon.x << ", " << cannon.y << ") - currentRadius: " << radius << endl;

    for (int dir = 0; dir < 4; ++dir)
    {
        for (int i = 0; i < radius; ++i)
        {
            //cout << "dx: " << dx << ", dy: " << dy << ", i: " << i << ", dir: " << dir << ", enemy: " << enemy << endl;
            //cout << "newPoint (" << cannon.x + dx << ", " << cannon.y + dy << ") is " << field[cannon.y + dy][cannon.x + dx] << endl; 
            
            //cout << "correct (" << cannon.x + dx << ", " << cannon.y + dy << ") = " << Point::isCorrectPoint(cannon.x + dx, cannon.y + dy) << endl;
            //cout << "Containing: " << isPointContainsCannon(Point(cannon.x + dx, cannon.y + dy), enemy) << endl;
            
            if (Point::isCorrectPoint(cannon.x + dx, cannon.y + dy) && 
                isPointContainsCannon(Point(cannon.x + dx, cannon.y + dy), enemy))
            {
                damagedCannonPoint = Point(cannon.x + dx, cannon.y + dy);
                //cout << "GOTCHA" << endl;
                return damagedCannonPoint;
            }

            dx += cannonCheckDX[dir];
            dy += cannonCheckDY[dir];
        }
    }

    return damagedCannonPoint;
}



// стрельба пушек по всем
void cannonShooting(std::string &animation)
{
    ostringstream animationStream;

    // стрельба пушек
    for (int player = 0; player < 2; ++player)
    {
        int enemy = 1 - player;
        
        for (CannonMap::iterator it = cannons[player].begin(); it != cannons[player].end(); ++it)
        {
            // Достает ли пушка до вражеской башни
            Point tower(player == 0 ? 7 : 2, player == 0 ? 7 : 2);
            Point cannon = it->first;

            if (dist(cannon, tower, it->second.getRadius()))
            {
                // Достаем до башни
                players[enemy].towerHealth -= it->second.getPower();
                animationStream << cannon.x << " " << cannon.y << " " 
                    << tower.x << " " << tower.y << "\n"; 
                //cout << cannon.x << " " << cannon.y << " SHOOTS TOWER " << tower.x << " " << tower.y << endl;
            }
            else
            {
                Point nearbyPoint(-INT_MAX, -INT_MAX);
                // пытаемся попасть по пушкам
                for (int i = 1; i <= it->second.getRadius(); ++i) // берем с внутреннего радиуса
                {
                    nearbyPoint = checkNearbyCannons(cannon, enemy, i);
                    if (nearbyPoint.isNormalPoint())
                        break;
                }

                // Проверка того, нашли какую пушку повреждать
                if (nearbyPoint.isNormalPoint())
                {
                    cannons[enemy][nearbyPoint].health -= it->second.getPower();
                    animationStream << cannon.x << " " << cannon.y << " "
                        << nearbyPoint.x << " " << nearbyPoint.y << "\n";
                    //cout << cannon.x << " " << cannon.y << " SHOOTS CANNON " << nearbyPoint.x << " " << nearbyPoint.y << endl;
                }
            }
        }
    }

    animation = animationStream.str();

    // очистка мертвых пушек
    for (int player = 0; player < 2; ++player)
    {
        for (CannonMap::iterator it = cannons[player].begin(); it != cannons[player].end(); )
        {
            if (it->second.health <= 0)
            {
                field[it->first.y][it->first.x] = EMPTY;
                it = cannons[player].erase(it);
            }
            else
            {
                ++it;
            }
        }
    }
}

// Проверка окончания игры
bool isGameOver()
{
    return players[0].towerHealth <= 0 || players[1].towerHealth <= 0;
}

// спаун монеток
void spawnGold()
{
    vector<Point> v;
    for (int i = 0 ; i < fieldSize ; ++i)
        for (int j = 0 ; j < fieldSize ; ++j)
            if (field[i][j] == EMPTY)
                v.push_back(Point(j, i));

    if (!v.empty())
    {
        size_t p = rand() % v.size();
        field[v[p].y][v[p].x] = GOLD;
    }
}

void debugInformation()
{
    cout << "DEBUG" << endl;
    cout << "Towers:" << endl;
    
    const char *p[] = {"First:", "Second:"};

    for (int i = 0; i < 2; ++i)
    {
        cout << p[i] << endl;
        for (CannonMap::iterator it = cannons[i].begin(); it != cannons[i].end(); ++it)
        {
            cout << "   " << it->first.x << " " << it->first.y << " -> " 
                << it->second.health << " " << it->second.getRadius() << endl;
        }
    }

    cout << "END_DEBUG" << endl;
}

int main(int argc, char **argv)
{
    if (argc != 3 && argc != 4)
    {
        std::cout << "Usage: TW_tester <program1> <program2> [<seed>]\n";
        return 1;
    }

    const char *program1 = argv[1]; 
    const char *program2 = argv[2];

    if (argc >= 4)
        srand((int)atoi(argv[3]));
    else
        srand((int)time(NULL));

    // инициализируем поле
    initField();

    std::string animation;

    int moves = 0;

    for (; moves < maxMoves; ++moves)
    {
        animation.clear();

        ostringstream outs;
        getField(outs);
        outs << players[0].gold << " " << players[0].score << "\n";
        outs << players[1].gold << " " << players[1].score << "\n";
        printField(outs.str());
        

        // ходы игроков
        std::string output1, output2;

        ExecutionResult exec1 = playerMove(true, program1, output1);

        printLog(true, exec1, output1);
        if (exec1 != ER_OK)
            return 0;

        ExecutionResult exec2 = playerMove(false, program2, output2);

        printLog(false, exec2, output2);
        if (exec2 != ER_OK)
            return 0;

        // стрельба пушек
        
        cannonShooting(animation);

        //debugInformation();

        printAnimationStart();
        printAnimation(animation);
        printAnimationEnd();

        // провека окончания игры
        if (!isGameOver())
        {
            if (moves % 3 == 0)
                spawnGold(); // спавним монетки 
        }
        else
        {
            // пишем кто у нас выиграл, а кто проиграл
            if (players[0].towerHealth <= 0 && players[1].towerHealth <= 0)
                printLog(true, ER_TIE, "");
            else if (players[0].towerHealth <= 0)
                printLog(false, ER_WIN, "");
            else
                printLog(true, ER_WIN, "");

            return 0;
        }       
    }

    // подумать про подсчет количества очков

    if (players[0].score > players[1].score)
        printLog(true, ER_WIN, std::string(players[0].score + " - " + players[1].score));
    else if (players[1].score > players[0].score)
        printLog(false, ER_WIN, std::string(players[0].score + " - " + players[1].score));
    else
        printLog(true, ER_TIE, "");

    return 0;
}
