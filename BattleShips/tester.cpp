#include <sstream>
#include <iostream>
#include <numeric>
#include "execution.h"
#include "testlib.h"

const int size = 7;
const int ships = 7;

enum Cell
{
    Empty = 0,
    Miss = 1,
    Shot = 2,
    Killed = 3,
    Ship = 4
};

const int sizes[ships] = {3, 2, 2, 2, 1, 1, 1};

Cell fields[2][size][size];
int shipId[2][size][size];
int shipLen[2][ships];
const char *programs[2];
bool error = false;

int getPlayerScore(int player)
{
    return std::accumulate(shipLen[player], shipLen[player] + ships, 0);
}

void saveField()
{
    std::ostringstream outs;
    for (int player = 0 ; player < 2 ; ++player)
    {
        for (int i = 0 ; i < size ; ++i)
        {
            for (int j = 0 ; j < size ; ++j)
            {
                Cell f = fields[player][i][j];
                if (f == Shot && shipLen[player][shipId[player][i][j] - 1] == 0)
                    f = Killed;
                outs << f << " ";
            }
            outs << "\n";
        }
        outs << "\n";
    }
	//outs << player << " " << row << " " << col << "\n"; 
	outs << getPlayerScore(0) << " " << getPlayerScore(1) << "\n"; 

    printField(outs.str());
}

void initField(int player)
{
    ExecutionResult result = ER_OK;
    std::string output;
    char dir[] = {'H', 'V'};
    result = runProcess(programs[player], "1\n", output, 1000, 64000);
    if (result == ER_OK)
    {
        //std::istringstream ins(output);
        InStream ins(output);
        for (int i = 0 ; i < ships ; ++i)
        {
            int x, y;
            char v = 'H';

            try
            {
                ins >> ValueInBounds<int>(y, 1, 7) >> ValueInBounds<int>(x, 1, 7);
                if (sizes[i] != 1)
                    ins >> ValueInRange<char>(v, dir, 2);
            }
            catch (ReadCheckerException &exception)
            {
                result = ER_IM;

                std::ostringstream outs;
                outs    << std::string("invalid ships placement: ") << std::endl << output << std::endl
                        << exception.getReadResultText() << ": " << exception.what() << std::endl
                        << std::string("END") << std::endl;

                printLog(player == 0, result, outs.str());
                error = true;
                break;

            }

            int dx = 1, dy = 0;
            int szx = sizes[i];
            int szy = 1;
            if (v == 'V')
            {
                dx = 0;
                dy = 1;
                std::swap(szx, szy);
            }
            --x;
            --y;
            // check the ship
            for (int xx = x - 1 ; !error && xx <= x + szx ; ++xx)
            {
                for (int yy = y - 1 ; !error && yy <= y + szy ; ++yy)
                {
                    if (xx >= 0 && xx < 7 && yy >= 0 && yy < 7)
                    {
                        if (fields[player][yy][xx] != Empty)
                        {
                            result = ER_IM;
                            printLog(player == 0, result, "overlapping ships placement: " + output + "\nEND\n");
                            error = true;
                        }
                    }
                }
            }

            if (error)
                return;

            // place the ship
            shipLen[player][i] = sizes[i];
            for (int xx = x, yy = y, c = 0 ; c < sizes[i] 
                ; ++c, xx += dx, yy += dy)
            {
                if (xx >= 0 && xx < 7 && yy >= 0 && yy < 7)
                {
                    fields[player][yy][xx] = Ship;
                    shipId[player][yy][xx] = i + 1;
                }
                else
                {
                    result = ER_IM;
                    printLog(player == 0, result, "ship is outside the field: " + output + "\nEND\n");
                    error = true;
                }
            }
        }
    }
    else
    {
        error = true;
        printLog(player == 0, result, "");
    }
}

int main(int argc, char **argv)
{
    if (argc != 3)
    {
        std::cout << "Usage: BattleShips_tester <program1> <program2>\n";
        return 1;
    }
    programs[0] = argv[1];
    programs[1] = argv[2];

    // init fields
    initField(0);
    if (error)
        return 0;

    initField(1);
    if (error)
        return 0;

    // save field and score before the first move
    saveField();

    int player = 0;
    ExecutionResult result = ER_OK;
    for (int move = 0 ; move < 2 * size * size ; ++move)
    {
        std::ostringstream outs;
        outs << "2\n";
        for (int i = 0 ; i < size ; ++i)
        {
            for (int j = 0 ; j < size ; ++j)
            {
                Cell f = fields[1 - player][i][j];
                if (f == Ship)
                    f = Empty;
                else if (f == Shot && shipLen[1 - player][shipId[1 - player][i][j] - 1] == 0)
                    f = Killed;
                outs << f << " ";
            }
            outs << "\n";
        }
        printInput(player == 0, outs.str());
        std::string output;
        result = runProcess(programs[player], outs.str(), output, 1000, 64000);
        if (result == ER_OK)
        {e
            //std::istringstream ins(output);
            InStream ins(output);
            int row, col;
            try
            {
                ins >> ValueInBounds<int>(row, 1, 7) >> ValueInBounds<int>(col, 1, 7);
            }
            catch (ReadCheckerException &exception)
            {
                result = ER_IM;
                std::ostringstream outs;
                outs << output << std::endl << exception.getReadResultText() << ": " << exception.what() << std::endl;

                printLog(player == 0, result, outs.str());
                break;
            }

            --row;
            --col;
            if (    fields[1 - player][row][col] == Empty ||
                    fields[1 - player][row][col] == Ship)
            {
                printLog(player == 0, result, output);

                // process shot
                if (fields[1 - player][row][col] == Empty)
                {
                    fields[1 - player][row][col] = Miss;
                    player = 1 - player;
                }
                else
                {
                    fields[1 - player][row][col] = Shot;
                    int id = shipId[1 - player][row][col] - 1;
                    --shipLen[1 - player][id];
                    if (getPlayerScore(1 - player) == 0)
                    {
                        result = ER_WIN;
                    }
                }

                // print field
                saveField();

                // print animation?

                if (result == ER_WIN)
                {
                    printLog(player == 0, result, "");
                    break;
                }
            }
            else
            {
                result = ER_IM;
                printLog(player == 0, result, output);
                break;
            }
        }
        else
        {
            printLog(player == 0, result, "");
            break;
        }
    }

    return 0;
}
