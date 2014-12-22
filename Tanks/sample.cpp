#include <iostream>
#include <time.h>
#include <vector>
#include <string>

std::vector<std::string> field;

int size;
int row[4];
int col[4];
int dir[4];
int energy[4];
int reload[4];
int player;

const int dr[4] = {0, -1, 0, 1};
const int dc[4] = {-1, 0, 1, 0};

void moveTank(int t)
{
    if (energy[t] > 0)
    {
        if (rand() % 50 <= 40)
        {
            int d;
            do
                d = rand() % 4;
            while (field[row[t] + dr[d]][col[t] + dc[d]] == '#');
            dir[t] = d + 1;
        }

        std::cout << dir[t];
        if (rand() % 100 <= 50)
            std::cout << " 1\n";
        else
            std::cout << " 0\n";
    }
    else
    {
        std::cout << "1 1\n";
    }
}

int main()
{
    srand((int)clock());

    std::string s;
    std::getline(std::cin, s);
    size = (int)s.size();
    field.push_back(s);
    for (int i = 1 ; i < size ; ++i)
    {
        std::getline(std::cin, s);
        field.push_back(s);
    }

    for (int i = 0 ; i < size ; ++i)
        for (int j = 0 ; j < size ; ++j)
            if (field[i][j] >= '1' && field[i][j] <= '4')
            {
                row[field[i][j] - '1'] = i;
                col[field[i][j] - '1'] = j;
            }

    std::cin >> player;

    for (int i = 0 ; i < 4 ; ++i)
        std::cin >> energy[i] >> dir[i] >> reload[i];

    moveTank(player == 1 ? 0 : 2);
    moveTank(player == 1 ? 1 : 3);

    return 0;
}
