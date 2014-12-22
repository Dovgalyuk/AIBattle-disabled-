#include <sstream>
#include <iostream>
#include "execution.h"

const int size = 4;

int field[size][size];
int score[2];

void diag1(int r, int c)
{
    if (r > 1 || c > 1)
        return;
    int xo = field[r][c];
    if (!xo)
        return;
    for (int i = 1 ; i < 3 ; ++i)
    {
        if (field[r + i][c + i] != xo)
            return;
    }
    ++score[xo - 1];
}

void diag2(int r, int c)
{
    if (r > 1 || c <= 1)
        return;
    int xo = field[r][c];
    if (!xo)
        return;
    for (int i = 1 ; i < 3 ; ++i)
    {
        if (field[r + i][c - i] != xo)
            return;
    }
    ++score[xo - 1];
}

void horz(int r, int c)
{
    if (c > 1)
        return;
    int xo = field[r][c];
    if (!xo)
        return;
    for (int i = 1 ; i < 3 ; ++i)
    {
        if (field[r][c + i] != xo)
            return;
    }
    ++score[xo - 1];
}

void vert(int r, int c)
{
    if (r > 1)
        return;
    int xo = field[r][c];
    if (!xo)
        return;
    for (int i = 1 ; i < 3 ; ++i)
    {
        if (field[r + i][c] != xo)
            return;
    }
    ++score[xo - 1];
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

    ExecutionResult result = ER_OK;
    for (int move = 0 ; move < size * size ; ++move)
    {
        bool first = move % 2 == 0;
        std::ostringstream outs;
		outs << !first + 1 << "\n"; 
        for (int i = 0 ; i < size ; ++i)
        {
            for (int j = 0 ; j < size ; ++j)
            {
                outs << field[i][j] << " ";
            }
            outs << "\n";
        }
        std::string output;
        result = runProcess(first ? program1 : program2, 
            outs.str(), output, 1000, 64000);
        if (result == ER_OK)
        {
            std::istringstream ins(output);
            int x, y;
            ins >> y >> x;
            if (x >= 1 && x <= size && y >= 1 && y <= size
                && !field[y-1][x-1])
            {
                printLog(first, result, output);

                int xo = first ? 1 : 2;
                field[y-1][x-1] = xo;
                score[0] = score[1] = 0;
                for (int i = 0 ; i < size ; ++i)
                {
                    for (int j = 0 ; j < size ; ++j)
                    {
                        horz(i, j);
                        vert(i, j);
                        diag1(i, j);
                        diag2(i, j);
                    }
                }

                std::ostringstream outs;
                for (int i = 0 ; i < size ; ++i)
                {
                    for (int j = 0 ; j < size ; ++j)
                        outs << field[i][j] << " ";
                    outs << "\n";
                }
                outs << score[0] << " " << score[1] << "\n";
                printField(outs.str());
            }
            else
            {
                result = ER_IM;
                printLog(first, result, output);
                break;
            }
        }
        else
        {
            printLog(first, result, "");
            break;
        }
    }

    if (result == ER_OK)
    {
        result = ER_TIE;
        if (score[0] != score[1])
            result = ER_WIN;
        printLog(score[0] > score[1], result, "");
    }

    return 0;
}
