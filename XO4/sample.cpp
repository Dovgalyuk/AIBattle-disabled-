#include <iostream>
#include <ctime>
#include <cstdlib>
#include <vector>

// В качестве теста - бот, страдающий абсолютно рандомным выбором

struct cell
{
    int i,j;
    cell(int i, int j)
    {
        this->i = i;
        this->j = j;
    }
};

const int n = 4;
int main()
{   
    srand((unsigned int)time(0));

    int player = 0;
    std::vector<cell> positions; 
    int board[n][n];

    std::cin >> player;

    for (int i=0; i < n; ++i)
        for (int j=0; j < n; ++j)
        {
            std::cin >> board[i][j];
            if (board[i][j] == 0) positions.push_back(cell(i, j)) ;
        }

    cell answer = positions[rand() % positions.size()];
    std::cout << answer.i + 1 << " " << answer.j + 1 << std::endl;

    return 0;
}
