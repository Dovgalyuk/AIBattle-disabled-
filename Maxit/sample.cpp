#include <iostream>
#include <time.h>

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
    do
    {
        if (player == 1)
            j = rand() % 6;
        else
            i = rand() % 6;
    }
    while (!a[i][j]);

    std::cout << (player == 1 ? j : i) + 1;

    return 0;
}
