#include <iostream>
#include <time.h>
#include <stdlib.h>

bool occ[8][8];

int row[2][8];
int col[2][8];
int color;

int mr[24], mc[24];
int moves;

void count(int dc)
{
    int r = row[0][color - 1] - 1 - 1;
    int c = col[0][color - 1] - 1 + dc;
    while (r >= 0 && c >= 0 && c < 8 && !occ[r][c])
    {
        mr[moves] = r;
        mc[moves] = c;
        ++moves;
        --r;
        c += dc;
    }
}

int main()
{
    srand((int)time(NULL));

    std::cin >> color;
    for (int i = 0 ; i < 8 ; ++i)
    {
        std::cin >> row[0][i] >> col[0][i];
        occ[row[0][i] - 1][col[0][i] - 1] = true;
    }
    for (int i = 0 ; i < 8 ; ++i)
    {
        std::cin >> row[1][i] >> col[1][i];
        occ[row[1][i] - 1][col[1][i] - 1] = true;
    }

    // выбор первой фишки
    if (!color)
        color = rand() % 8 + 1;

    // свои двигаются снизу вверх (от 8 к 1 линии)
    count(0);
    count(-1);
    count(1);

    std::cout << color << " ";
    if (moves)
    {
        int m = rand() % moves;
        std::cout << mr[m] + 1 << " " << mc[m] + 1 << "\n";
    }
    else
    {
        std::cout << row[0][color - 1] << " " << col[0][color - 1] << "\n";
    }

    return 0;
}
