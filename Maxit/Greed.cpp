#include <iostream>
#include <time.h>
#include <stdlib.h>

int main()
{
    srand((int)time(NULL));

    int a[6][6];
    int player;
    int rowcol;

    for (int i = 0 ; i < 6 ; ++i)
        for (int j = 0 ; j < 6 ; ++j)
            std::cin >> a[i][j];

    std::cin >> player >> rowcol;

    int i = rowcol - 1;
    int j = rowcol - 1;
    int im = 0, jm = 0;
    int max = -1;
    for (int t = 0 ; t < 6 ; ++t)
    {
        if (player == 1)
            j = t;
        else
            i = t;
        if (max == -1 || a[i][j] > a[im][jm])
        {
            im = i;
            jm = j;
            max = t;
        }
    }

    std::cout << max + 1;

    return 0;
}
