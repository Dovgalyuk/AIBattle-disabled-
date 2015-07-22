#include <iostream>
#include <time.h>
#include <stdlib.h>

int main()
{
    srand((int)time(NULL));

    int a[7][7];
    int m;
    std::cin >> m;
    if (m == 1)
    {
        std::cout << "2 5 V\n"
                  << "1 7 V\n"
                  << "3 1 H\n"
                  << "6 3 H\n"
                  << "1 3\n"
                  << "5 1\n"
                  << "6 6\n";
    }
    else
    {
        for (int i = 0 ; i < 7 ; ++i)
            for (int j = 0 ; j < 7 ; ++j)
                std::cin >> a[i][j];

        int i, j;
        do
        {
            j = rand() % 7;
            i = rand() % 7;
        }
        while (a[i][j]);

        std::cout << i + 1 << " " << j + 1 << "\n";
    }

    return 0;
}
