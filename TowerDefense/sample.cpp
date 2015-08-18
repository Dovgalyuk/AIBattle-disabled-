
#include <ctime>
#include <cstdlib>
#include <iostream>
#include <vector>

using namespace std;

const int fieldSize = 10;
int field[fieldSize][fieldSize];
const int build_dx[] = {1, -1, 0, 0, 1, 1, -1, -1}; 
const int build_dy[] = {0, 0, 1, -1, 1, -1, 1, -1}; 
const int move_dx[] = {-1, 0, 0, 1};
const int move_dy[] = {0, -1, 1, 0};

bool good(int x, int y) 
{
    return (x >= 0 && x < fieldSize && y >= 0 && y < fieldSize);
}

char getMove(int x, int y)
{
    if (y == 0 && x == -1)
        return 'L';
    else if (y == 0 && x == 1)
        return 'R';
    else if (y == -1 && x == 0)
        return 'U';
    else if (y == 1 && x == 0)
        return 'D';
}

int main()
{
    srand((unsigned)time(NULL));
    int money = 0, score = 0, x = -1, y = -1, player;

    cin >> player >> money >> score;

    for (int i = 0; i < fieldSize; ++i)
        for (int j = 0; j < fieldSize; ++j)
        {
            cin >> field[i][j];

            if ((player == 1 && field[i][j] == 500) || (player == 2 && field[i][j] == 900)) {
                y = i;
                x = j;
            }
        }

    vector<pair<int, int>> possibleBuilding;

    // если есть возможность построить башню - пытаемся строить
    if (money >= 7)
    {
        for (int i = 0; i < 8; ++i)
            if (good(x + build_dx[i], y + build_dy[i]) && field[y + build_dy[i]][x + build_dx[i]] == 0)
                possibleBuilding.push_back(make_pair(x + build_dx[i], y + build_dy[i]));
    
    }

    if (!possibleBuilding.empty())
    {
        int index = rand() % possibleBuilding.size();
        cout <<  "B " << possibleBuilding[index].first << " " << possibleBuilding[index].second << " " << 2 << endl;
    }
    else
    {
        // строить нечего - пытаемся поймать монетки
        vector<pair<int, int>> possibleMovement;

        for (int i = 0; i < 4; ++i)
            if (good(x + move_dx[i], y + move_dy[i]) && 
                (field[y + move_dy[i]][x + move_dx[i]] == 0 || field[y + move_dy[i]][x + move_dx[i]] == 1000))
                    possibleMovement.push_back(make_pair(move_dx[i], move_dy[i]));

        if (possibleMovement.size() > 0)
        {
            // Есть куда идти
            for (int i = 0; i < possibleMovement.size(); ++i)
                if (field[y + possibleMovement[i].second][x + possibleMovement[i].first] == 1000)
                {
                    // сперва собираем монетки
                    cout << "M " << getMove(possibleMovement[i].first, possibleMovement[i].second) << endl;
                    return 0;
                }

            // монеток нет - идем куда сможем
            int index = rand() % possibleMovement.size();

            cout << "M " << getMove(possibleMovement[index].first, possibleMovement[index].second) << endl;
        }
        else
        {
            cout << "S" << endl;
        }
    }
    
    return 0;
}
