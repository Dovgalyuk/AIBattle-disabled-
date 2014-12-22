#include <sstream>
#include <iostream>
#include "execution.h"

const int size = 3;

int field[size][size];

bool diag1(int xo)
{
    for (int i = 0 ; i < size ; ++i)
    {
        if (field[i][i] != xo)
            return false;
    }
    return true;
}

bool diag2(int xo)
{
    for (int i = 0 ; i < size ; ++i)
    {
        if (field[i][size - i - 1] != xo)
            return false;
    }
    return true;
}

bool horz(int xo, int i)
{
    for (int j = 0 ; j < size ; ++j)
        if (field[i][j] != xo)
            return false;
    return true;
}

bool vert(int xo, int i)
{
    for (int j = 0 ; j < size ; ++j)
        if (field[j][i] != xo)
            return false;
    return true;
}

void saveField()
{
	std::ostringstream outs;
	for (int i = 0 ; i < size ; ++i)
	{
		for (int j = 0 ; j < size ; ++j)
			outs << field[i][j] << " ";
		outs << "\n";
	}
	printField(outs.str());
}

int main(int argc, char **argv)
{
    if (argc != 3)
    {
        std::cout << "Usage: XO_tester <program1> <program2>\n";
        return 1;
    }
    const char *program1 = argv[1];
    const char *program2 = argv[2];

    //initLog(program1, program2);

	// save field and score before the first move
	saveField();
	
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
				
				saveField();
				
				// check win
                if (diag1(xo) || diag2(xo) || horz(xo, y - 1) || vert(xo, x - 1))
                {
                    result = ER_WIN;
                    printLog(first, result, output);
                    break;
                }
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
        printLog(false, result, "");
    }

    return 0;
}
