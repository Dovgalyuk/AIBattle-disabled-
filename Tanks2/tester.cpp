#include <sstream>
#include <iostream>
#include <list>
#include <algorithm>
#include <time.h>
#include "execution.h"

const int size = 15;
const int tanksNum = 4;
const int maxMoves = 200;

char field[size][size + 1] =
{
    "###############",
    "#.............#",
    "#....#####....#",
    "#.............#",
    "#...#.....#...#",
    "#...#.....#...#",
    "#......#......#",
    "####..###..####",
    "#......#......#",
    "#...#.....#...#",
    "#...#.....#...#",
    "#.............#",
    "#....#####....#",
    "#.............#",
    "###############"
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
const int T_PILL = 10;
const float T_SHOOT = 0.25;

const int SC_KILL = 20;
const int SC_CAPTURE = 5;
const int SC_FLAG = 50;
const int SC_PILL = 1;

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

const int initFlagC[2] = {size / 2, size / 2};
const int initFlagR[2] = {1, size - 2};
const Pos initTanks[4] = { Pos(1, 1, D_DOWN), Pos(1, size - 2, D_DOWN),
                          Pos(size - 2, 1, D_UP), Pos(size - 2, size - 2, D_UP) };

typedef std::list<Pos> PosList;

Pos tanks[tanksNum];
int energy[tanksNum];
int reload[tanksNum];
bool hasFlag[tanksNum];
Pos flags[2];
int score[2];
PosList shoots;
int pillTimeout;
std::string customData[2];
bool globalResult;

void printField(std::ostringstream &outs)
{
    for (int i = 0 ; i < size ; ++i)
        outs << field[i] << "\n";
}

void printTanks(std::ostringstream &outs)
{
    for (int i = 0 ; i < tanksNum ; ++i)
        outs << energy[i] << " " << tanks[i].dir << " " << reload[i] << " " << (hasFlag[i] ? 1 : 0) << "\n";
}

void printFlags(std::ostringstream &outs)
{
    for (int i = 0 ; i < 2 ; ++i)
        outs << flags[i].r << " " << flags[i].c << "\n";
}

void makeInput(int player, std::ostringstream &outs)
{
    outs << player << "\n";
    printField(outs);
    printTanks(outs);
    printFlags(outs);
    outs << customData[player - 1] << "\n";
    printInput(player == 1, outs.str());
}

void animation(const std::string &obj, float t1, float t2, int r1, int c1, int r2, int c2)
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
    printFlags(outs);
    outs << score[0] << " " << score[1] << "\n";
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
    case 'a':
    case 'b':
    case 'c':
    case 'd':
        // hit the tank
        energy[field[r][c] - 'a'] -= E_HIT;
        return false;
    case 'A':
    case 'B':
        return true;
    default:
        printLog(false, ER_IE, "Shooting at the invalid object");
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


char getTankSymbol(int t)
{
    return t + (hasFlag[t] ? 'a' : '1');
}


void moveShoots(float curTime)
{
    for (PosList::iterator sh = shoots.begin() ; sh != shoots.end() ; )
    {
        int r = sh->r + dr[sh->dir - 1];
        int c = sh->c + dc[sh->dir - 1];
        char obj = field[sh->r][sh->c];
        field[sh->r][sh->c] = '.';
        for (int f = 0 ; f < 2 ; ++f)
        {
            if (flags[f] == *sh)
                field[sh->r][sh->c] = 'A' + f;
        }
        if (shootField(r, c))
        {
            animation(std::string(1, obj), curTime, curTime + T_SHOOT, sh->r, sh->c, r, c);
            sh->r = r;
            sh->c = c;
            field[r][c] = obj;
            ++sh;
        }
        else
        {
            animation("x", curTime, curTime + T_SHOOT, r, c, r, c);
            sh = shoots.erase(sh);
        }
    }
}

int main(int argc, char **argv)
{
    if (argc != 3 && argc != 4)
    {
        std::cout << "Usage: tester <program1> <program2> [<seed>]\n";
        return 1;
    }

    if (argc >= 4)
        srand((int)atoi(argv[3]));
    else
        srand((int)time(NULL));

    const char *program1 = argv[1];
    const char *program2 = argv[2];

    // init field
    for (int i = 0 ; i < tanksNum ; ++i)
    {
        tanks[i] = initTanks[i];
        field[tanks[i].r][tanks[i].c] = getTankSymbol(i);
        energy[i] = E_MAX;
    }
    for (int i = 0 ; i < 2 ; ++i)
    {
        flags[i].r = initFlagR[i];
        flags[i].c = initFlagC[i];
        field[flags[i].r][flags[i].c] = 'A' + i;
    }

    // save field and score before the first move
    saveField();

    int deathTimeout = 5;
    for (int moves = 0 ; moves < maxMoves ; ++moves)
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
            bool im1 = false;
            bool im2 = false;
            {
                std::istringstream ins(output1);
                for (int i = 0 ; !im1 && i < 2 ; ++i)
                {
                    ins >> dir[i] >> shoot[i];
                    if (dir[i] < D_LEFT || dir[i] > D_DOWN
                        || shoot[i] < 0 || shoot[i] > 1)
                    {
                        printLog(true, ER_IM, output1);
                        im1 = true;
                    }
                }
                std::getline(ins, customData[0]);
                std::getline(ins, customData[0]);
            }
            {
                std::istringstream ins(output2);
                for (int i = 2 ; !im2 && i < 4 ; ++i)
                {
                    ins >> dir[i] >> shoot[i];
                    if (dir[i] < D_LEFT || dir[i] > D_DOWN
                        || shoot[i] < 0 || shoot[i] > 1)
                    {
                        printLog(false, ER_IM, output2);
                        im2 = true;
                    }
                }
                std::getline(ins, customData[1]);
                std::getline(ins, customData[1]);
            }
            if (im1 || im2)
            {
                globalResult = true;
                if (im1 && im2)
                {
                    printLog(false, ER_TIE, "");
                }
                break;
            }

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
                    bool move = false;
                    field[tanks[t].r][tanks[t].c] = '.';
                    int r = tanks[t].r + dr[tanks[t].dir - 1];
                    int c = tanks[t].c + dc[tanks[t].dir - 1];
                    int flag = 0;
                    switch (field[r][c])
                    {
                    case 'e':
                        // get the pill
                        energy[t] += E_MAX / 2;
                        if (energy[t] > E_MAX)
                            energy[t] = E_MAX;
                        field[r][c] = '.';
                        score[t / 2] += SC_PILL;
                        // fall through
                    case '.':
                        move = true;
                        break;
                    case '^':
                    case '>':
                    case 'v':
                    case '<':
                        animation("x", 0.5, 0.75, r, c, r, c);
                        // lose energy
                        field[r][c] = '.';
                        energy[t] -= E_HIT;
                        // erase shoot
                        shoots.erase(std::find(shoots.begin(), shoots.end(), Pos(r, c)));
                        move = true;
                        break;
                    case '#':
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                    case 'a':
                    case 'b':
                    case 'c':
                    case 'd':
                        // cannot move
                        break;
                    case 'A':
                    case 'B':
                        flag = field[r][c] - 'A';
                        if (flag != t / 2)
                        {
                            // capture the flag
                            hasFlag[t] = true;
                            flags[flag].r = -1;
                            flags[flag].c = -1;
                            // get flag
                            field[r][c] = '.';
                            // add score
                            score[t / 2] += SC_CAPTURE;
                        }
                        else
                        {
                            if (initFlagR[flag] == r && initFlagC[flag] == c
                                && hasFlag[t])
                            {
                                // get points for enenmy flag
                                score[t / 2] += SC_FLAG;
                                // send enemy flag back to base
                                flags[1 - flag].r = initFlagR[1 - flag];
                                flags[1 - flag].c = initFlagC[1 - flag];
                                hasFlag[t] = false;
                            }
                            else
                            {
                                // send own flag back to base
                                flags[flag].r = initFlagR[flag];
                                flags[flag].c = initFlagC[flag];
                            }
                        }
                        move = true;
                        break;
                    default:
                        printLog(false, ER_IE, "Invalid cell in front of tank");
                        exit(0);
                    }
                    if (move)
                    {
                        animation(std::string(1, getTankSymbol(t)) + getDirSymbol(tanks[t].dir),
                            0, 1, tanks[t].r, tanks[t].c, r, c);
                        // move the tank
                        tanks[t].r = r;
                        tanks[t].c = c;
                    }
                    field[tanks[t].r][tanks[t].c] = getTankSymbol(t);
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
                        animation("x", 1 - T_SHOOT, 1, r, c, r, c);
                    }
                    energy[i] -= E_SHOOT;
                    reload[i] = T_RELOAD;
                }
            }
            // draw flags back
            for (int i = 0 ; i < 2 ; ++i)
            {
                int r = flags[i].r;
                int c = flags[i].c;
                if (field[r][c] == '.')
                    field[r][c] = 'A' + i;
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
            for (int i = 0 ; i < tanksNum ; ++i)
                if (alive[i] && energy[i] <= 0)
                {
                    int r = tanks[i].r;
                    int c = tanks[i].c;
                    animation("x", 1 - T_SHOOT, 1, r, c, r, c);
                    field[r][c] = '.';
                    // put flag back
                    if (hasFlag[i])
                    {
                        int f = 1 - i / 2;
                        field[r][c] = 'A' + f;
                        flags[f].r = r;
                        flags[f].c = c;
                    }
                    else
                    {
                        // standing on flag
                        for (int f = 0 ; f < 2 ; ++f)
                        {
                            if (flags[f] == tanks[i])
                                field[r][c] = 'A' + f;
                        }
                    }
                    // count the score
                    score[1 - i / 2] += SC_KILL;
                    // respawn the tank
                    r = initTanks[i].r;
                    c = initTanks[i].c;
                    bool ok = false;
                    for (int k = 0 ; !ok && k < size ; ++k)
                    {
                        for (int rr = r - k ; !ok && rr <= r + k ; ++rr)
                            for (int cc = c - k ; !ok && cc <= c + k ; ++cc)
                                if (cc >= 0 && cc < size && rr >= 0 && rr < size
                                    && field[rr][cc] == '.')
                                {
                                    r = rr;
                                    c = cc;
                                    ok = true;
                                }
                    }
                    tanks[i].r = r;
                    tanks[i].c = c;
                    tanks[i].dir = initTanks[i].dir;
                    energy[i] = E_MAX;
                    hasFlag[i] = false;
                    reload[i] = 0;
                    field[r][c] = '1' + i;
                }

            printAnimationEnd();

            // save field and score after the correct move
            saveField();
        }
        else
        {
            if (result1 != ER_OK)
            {
                printLog(true, result1, "");
            }
            if (result2 != ER_OK)
            {
                printLog(false, result2, "");
            }
            if (result1 != ER_OK && result2 != ER_OK)
            {
                printLog(true, ER_TIE, "");
            }
            globalResult = true;
            break;
        }
    }

    if (!globalResult)
    {
        if (score[0] == score[1])
            printLog(true, ER_TIE, "");
        else
            printLog(score[0] > score[1], ER_WIN, "");
    }

    return 0;
}
