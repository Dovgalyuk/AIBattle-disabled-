#include <sstream>
#include <iostream>
#include <list>
#include <algorithm>
#include <time.h>
#include "execution.h"

const int size = 11;
const int tanksNum = 4;

char field[size][size + 1] =
{
    "###########",
    "#....#....#",
    "#.........#",
    "#.........#",
    "#.........#",
    "##...#...##",
    "#.........#",
    "#.........#",
    "#.........#",
    "#....#....#",
    "###########"
};

enum Dir
{
    D_LEFT  = 1,
    D_UP    = 2,
    D_RIGHT = 3,
    D_DOWN  = 4
};

const int E_MAX = 100;
const int E_HIT = 20;
const int E_SHOOT = 5;
const int T_RELOAD = 5;
const int T_PILL = 15;
const float T_SHOOT = 0.25;

const int dr[4] = {0, -1, 0, 1};
const int dc[4] = {-1, 0, 1, 0};

struct Pos
{
    Pos() : r(0), c(0), dir(D_LEFT) {}
    Pos(int rr, int cc) : r(rr), c(cc), dir(D_LEFT) {}
    Pos(int rr, int cc, Dir dd) : r(rr), c(cc), dir(dd) {}

    int r, c;
    Dir dir;

    bool operator==(const Pos &p) const
    {
        return r == p.r && c == p.c;
    }
};

typedef std::list<Pos> PosList;

Pos tanks[tanksNum];
int energy[tanksNum];
int reload[tanksNum];
PosList shoots;
int pillTimeout;

void printField(std::ostringstream &outs)
{
    for (int i = 0 ; i < size ; ++i)
        outs << field[i] << "\n";
}

void printTanks(std::ostringstream &outs)
{
    for (int i = 0 ; i < tanksNum ; ++i)
        outs << energy[i] << " " << tanks[i].dir << " " << reload[i] << "\n";
}

void makeInput(int player, std::ostringstream &outs)
{
    printField(outs);
    outs << player << "\n";
    printTanks(outs);
}

void animation(char obj, float t1, float t2, int r1, int c1, int r2, int c2)
{
    std::ostringstream outs;
    outs << obj << " " << t1 << " " << t2 << " " << r1 << " " << c1 << " " << r2 << " " << c2;
    printAnimation(outs.str());
}

void saveField()
{
    std::ostringstream outs;
    printField(outs);
    printTanks(outs);
    printField(outs.str());
}

bool shootField(int r, int c)
{
    switch (field[r][c])
    {
    case 'e':
        // remove the pill
        field[r][c] = '.';
        return false;
    case '.':
        return true;
    case '^':
    case '>':
    case 'v':
    case '<':
        // kill shoot
        field[r][c] = '.';
        // erase shoot
        shoots.erase(std::find(shoots.begin(), shoots.end(), Pos(r, c)));
        return false;
    case '#':
        return false;
    case '1':
    case '2':
    case '3':
    case '4':
        // hit the tank
        energy[field[r][c] - '1'] -= E_HIT;
        return false;
    default:
        printLog(false, ER_IE, "");
        exit(0);
    }

    return false;
}

char getDirSymbol(Dir dir)
{
    switch (dir)
    {
    case D_LEFT:
        return '<';
    case D_RIGHT:
        return '>';
    case D_UP:
        return '^';
    case D_DOWN:
        return 'v';
    }
    return 'x';
}


void moveShoots(float curTime)
{
    for (PosList::iterator sh = shoots.begin() ; sh != shoots.end() ; )
    {
        int r = sh->r + dr[sh->dir - 1];
        int c = sh->c + dc[sh->dir - 1];
        char obj = field[sh->r][sh->c];
        field[sh->r][sh->c] = '.';
        if (shootField(r, c))
        {
            animation(obj, curTime, curTime + T_SHOOT, sh->r, sh->c, r, c);
            sh->r = r;
            sh->c = c;
            field[r][c] = obj;
            ++sh;
        }
        else
        {
            animation('x', curTime, curTime + T_SHOOT, r, c, r, c);
            sh = shoots.erase(sh);
        }
    }
}

int main(int argc, char **argv)
{
    if (argc != 3)
    {
        std::cout << "Usage: tester <program1> <program2>\n";
        return 1;
    }

    srand((int)5557/*time(NULL)*/);

    const char *program1 = argv[1];
    const char *program2 = argv[2];

    // init field
    tanks[0].r = 1;
    tanks[0].c = 1;
    tanks[0].dir = D_DOWN;
    tanks[1].r = size - 2;
    tanks[1].c = size - 2;
    tanks[1].dir = D_UP;
    tanks[2].r = size - 2;
    tanks[2].c = 1;
    tanks[2].dir = D_RIGHT;
    tanks[3].r = 1;
    tanks[3].c = size - 2;
    tanks[3].dir = D_LEFT;
    for (int i = 0 ; i < tanksNum ; ++i)
    {
        field[tanks[i].r][tanks[i].c] = '1' + i;
        energy[i] = E_MAX;
    }

    // save field and score before the first move
    saveField();

    int moves = 0;
    int deathTimeout = 5;
    const int max_moves = 100;
    for ( ; moves < max_moves ; ++moves)
    {
        std::ostringstream outs1, outs2;
        makeInput(1, outs1);
        makeInput(2, outs2);

        std::string output1, output2;
        ExecutionResult result1 = runProcess(program1, outs1.str(), output1, 1000, 64000);
        ExecutionResult result2 = runProcess(program2, outs2.str(), output2, 1000, 64000);
        if (result1 == ER_OK && result2 == ER_OK)
        {
            int dir[tanksNum], shoot[tanksNum];
            bool alive[tanksNum];
            bool im = false;
            {
                std::istringstream ins(output1);
                for (int i = 0 ; !im && i < 2 ; ++i)
                {
                    ins >> dir[i] >> shoot[i];
                    if (dir[i] < D_LEFT || dir[i] > D_DOWN
                        || shoot[i] < 0 || shoot[i] > 1)
                    {
                        printLog(true, ER_IM, output1);
                        im = true;
                    }
                }
            }
            {
                std::istringstream ins(output2);
                for (int i = 2 ; !im && i < 4 ; ++i)
                {
                    ins >> dir[i] >> shoot[i];
                    if (dir[i] < D_LEFT || dir[i] > D_DOWN
                        || shoot[i] < 0 || shoot[i] > 1)
                    {
                        printLog(false, ER_IM, output2);
                        im = true;
                    }
                }
            }
            if (im)
                break;

            printLog(true, ER_OK, "FIRST:\n" + output1 + "\nSECOND:\n" + output2);
            // update dirs
            for (int i = 0 ; i < tanksNum ; ++i)
            {
                tanks[i].dir = (Dir)dir[i];
                alive[i] = energy[i] > 0;
                --energy[i];
            }
            // update positions
            printAnimationStart();

            // move shoots
            moveShoots(0);
            moveShoots(T_SHOOT);

            int order[tanksNum] = {0, 1, 2, 3};
            for (int i = 0 ; i < tanksNum ; ++i)
                std::swap(order[i], order[rand() % tanksNum]);
            // move tanks
            for (int i = 0 ; i < tanksNum ; ++i)
            {
                int t = order[i];
                if (alive[t])
                {
                    field[tanks[t].r][tanks[t].c] = '.';
                    int r = tanks[t].r + dr[tanks[t].dir - 1];
                    int c = tanks[t].c + dc[tanks[t].dir - 1];
                    switch (field[r][c])
                    {
                    case 'e':
                        // get the pill
                        energy[t] += E_MAX / 2;
                        if (energy[t] > E_MAX)
                            energy[t] = E_MAX;
                        field[r][c] = '.';
                        // fall through
                    case '.':
                        animation('1' + t, 0, 1, tanks[t].r, tanks[t].c, r, c);
                        // move the tank
                        tanks[t].r = r;
                        tanks[t].c = c;
                        break;
                    case '^':
                    case '>':
                    case 'v':
                    case '<':
                        animation('x', 0.5, 0.75, r, c, r, c);
                        // lose energy
                        field[r][c] = '.';
                        energy[t] -= E_HIT;
                        // erase shoot
                        shoots.erase(std::find(shoots.begin(), shoots.end(), Pos(r, c)));
                        animation('1' + t, 0, 1, tanks[t].r, tanks[t].c, r, c);
                        // move tank
                        tanks[t].r = r;
                        tanks[t].c = c;
                        break;
                    case '#':
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                        // cannot move
                        break;
                    default:
                        printLog(false, ER_IE, "");
                        break;
                    }
                    field[tanks[t].r][tanks[t].c] = '1' + t;
                }
            }
            // move shoots again
            moveShoots(2 * T_SHOOT);
            moveShoots(3 * T_SHOOT);
            // add new shoots
            for (int i = 0 ; i < tanksNum ; ++i)
                if (reload[i])
                    --reload[i];
            for (int i = 0 ; i < tanksNum ; ++i)
            {
                if (alive[i] && shoot[i] && !reload[i])
                {
                    int r = tanks[i].r + dr[tanks[i].dir - 1];
                    int c = tanks[i].c + dc[tanks[i].dir - 1];
                    if (shootField(r, c))
                    {
                        shoots.push_back(Pos(r, c, tanks[i].dir));
                        field[r][c] = getDirSymbol(tanks[i].dir);
                    }
                    else
                    {
                        animation('x', 1 - T_SHOOT, 1, r, c, r, c);
                    }
                    energy[i] -= E_SHOOT;
                    reload[i] = T_RELOAD;
                }
            }
            // add new pills
            if (!pillTimeout)
            {
                int tries = size;
                int r, c;
                do
                {
                    r = rand() % size;
                    c = rand() % size;
                }
                while (--tries && field[r][c] != '.');
                if (tries)
                {
                    field[r][c] = 'e';
                    pillTimeout = T_PILL;
                }
            }
            else
            {
                --pillTimeout;
            }

            // check aliveness
            bool live = false;
            for (int i = 0 ; i < tanksNum ; ++i)
                if (alive[i] && energy[i] <= 0)
                {
                    int r = tanks[i].r;
                    int c = tanks[i].c;
                    animation('x', 1 - T_SHOOT, 1, r, c, r, c);
                    field[r][c] = '.';
                }
                else if (energy[i] > 0)
                    live = true;

            printAnimationEnd();

            // save field and score after the correct move
            saveField();

            // todo count moves when one player's tanks are dead
            if (!live)
            {
                printLog(true, ER_TIE, "");
                break;
            }

            int first = (energy[0] > 0) + (energy[1] > 0);
            int second = (energy[2] > 0) + (energy[3] > 0);
            if (!first || !second)
            {
                if (deathTimeout == 0)
                {
                    printLog(first > second, ER_WIN, "");
                    break;
                }
                else
                {
                    --deathTimeout;
                }
            }
        }
        else
        {
            if (result1 != ER_OK)
                printLog(true, result1, "");
            else
                printLog(false, result2, "");
            break;
        }
    }

    // calculate score after the last move
    if (moves >= max_moves)
    {
        int first = (energy[0] > 0) + (energy[1] > 0);
        int second = (energy[2] > 0) + (energy[3] > 0);
        if (first == second)
            printLog(true, ER_TIE, "");
        else
            printLog(first > second, ER_WIN, "");
    }

    return 0;
}
