#include <sstream>
#include <iostream>
#include "execution.h"

int field[8][8] =
{
    {8, 7, 6, 5, 4, 3, 2, 1},
    {3, 8, 5, 2, 7, 4, 1, 6},
    {2, 5, 8, 3, 6, 1, 4, 7},
    {5, 6, 7, 8, 1, 2, 3, 4},
    {4, 3, 2, 1, 8, 7, 6, 5},
    {7, 4, 1, 6, 3, 8, 5, 2},
    {6, 1, 4, 7, 2, 5, 8, 3},
    {1, 2, 3, 4, 5, 6, 7, 8}
};

bool occ[8][8];

int row[2][8];
int col[2][8];
int color;
int moves;
bool first = true;

bool check(int dr, int dc, int mr, int mc)
{
//std::cout << "CHECK " << dr << " " << dc << "\n";
    int r = row[first ? 0 : 1][color - 1] + dr;
    int c = col[first ? 0 : 1][color - 1] + dc;
    while (r >= 0 && c >= 0 && r < 8 && c < 8 && !occ[r][c])
    {
//std::cout << r << " " << c << "\n";
        ++moves;
        if (r == mr && c == mc)
            return true;
        r += dr;
        c += dc;
    }
    return false;
}

void printPlayer(std::ostringstream &outs, int p, bool swap)
{
    for (int i = 0 ; i < 8 ; ++i)
        if (swap)
            outs << 8 - row[p][i] << " " << 8 - col[p][i] << " ";
        else
            outs << row[p][i] + 1 << " " << col[p][i] + 1 << " ";
    outs << "\n";
}

void saveField()
{
    std::ostringstream outs;
    for (int t = 0 ; t < 2 ; ++t)
        printPlayer(outs, t, false);

    printField(outs.str());
}

int main(int argc, char **argv)
{
    if (argc != 3)
    {
        std::cout << "Usage: tester <program1> <program2>\n";
        return 1;
    }
    const char *program1 = argv[1];
    const char *program2 = argv[2];

    // init field
    for (int i = 0 ; i < 8 ; ++i)
    {
        row[0][i] = 0;
        col[0][i] = 7 - i;
        row[1][i] = 7;
        col[1][i] = i;

        occ[0][i] = true;
        occ[7][i] = true;
    }
    color = 0;

    // save field and score before the first move
    saveField();

    ExecutionResult result = ER_OK;
    int lastMoved = -1;
    int movesCount = 0;
    for ( ; movesCount < 128 ; ++movesCount)
    {
        std::ostringstream outs;
        outs << color << "\n";
        printPlayer(outs, first ? 0 : 1, first);
        printPlayer(outs, first ? 1 : 0, first);

        printInput(first, outs.str());

        std::string output;
        result = runProcess(first ? program1 : program2, 
            outs.str(), output, 1000, 64000);
        if (result == ER_OK)
        {
            std::istringstream ins(output);
            int colorIn, r, c, rr, cc;
            ins >> colorIn >> r >> c;
            if (first)
            {
                rr = 8 - r;
                cc = 8 - c;
            }
            else
            {
                rr = r - 1;
                cc = c - 1;
            }
            if ((!color && colorIn >= 1 && colorIn <= 8) || colorIn == color)
            {
                color = colorIn;
                moves = 0;
                if (check(first ? 1 : -1, -1, rr, cc)
                    || check(first ? 1 : -1, 0, rr, cc)
                    || check(first ? 1 : -1, 1, rr, cc))
                {
                    lastMoved = first ? 1 : 2;
                    printLog(first, result, output);
                    occ[row[first ? 0 : 1][color - 1]][col[first ? 0 : 1][color - 1]] = false;
                    occ[rr][cc] = true;
                    row[first ? 0 : 1][color - 1] = rr;
                    col[first ? 0 : 1][color - 1] = cc;
                    color = field[rr][cc];

                    // save field and score after the correct move
                    saveField();

                    // check win
                    if ((first && rr == 7) || (!first && rr == 0))
                    {
                        printLog(first, ER_WIN, "");
                        break;
                    }

                    // get next player
                    first = !first;
                }
                else
                {
                    if (!moves)
                    {
                        if (row[first ? 0 : 1][color - 1] == rr
                            && col[first ? 0 : 1][color - 1] == cc)
                        {
                            printLog(first, result, output);
                            color = field[rr][cc];
                            saveField();
                            first = !first;
                        }
                        else
                        {
                            printLog(first, ER_IM, output);
                            break;
                        }
                    }
                    else
                    {
                        printLog(first, ER_IM, output);
                        break;
                    }
                }
            }
            else
            {
                printLog(first, ER_IM, output);
                break;
            }
        }
        else
        {
            printLog(first, result, "");
            break;
        }
    }

    if (movesCount >= 128)
    {
        printLog(lastMoved == 2, ER_WIN, "");
    }

    return 0;
}
