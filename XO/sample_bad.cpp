#include <iostream>
#include <ctime>
#include <cstdlib>
#include <vector>
#include <windows.h>
#include <string>

// тестирование разных нарушений безопасности

struct cell
{
    int i,j;
    cell(int i, int j)
    {
        this->i = i;
        this->j = j;
    }
};

const int n = 3;
int main()
{
#if 0
    // create new process
    PROCESS_INFORMATION pi;
    STARTUPINFO si;
    SECURITY_ATTRIBUTES sa;
    ZeroMemory(&sa, sizeof(sa));
    sa.nLength = sizeof(sa);
    sa.bInheritHandle = TRUE;
    sa.lpSecurityDescriptor = NULL;
    ZeroMemory(&pi, sizeof(pi));
    ZeroMemory(&si, sizeof(si));
    si.cb = sizeof(si);
    CreateProcess("c:/windows/system32/calc.exe", NULL, NULL, NULL, TRUE, CREATE_NEW_CONSOLE,
        NULL, NULL, &si, &pi);
    //Sleep(500);
    //
#endif
#if 0
    // time limit
    Sleep(1000);
#endif
#if 0
    // time limit 2
    for (int i = 0 ; i < 100000000 ; ++i)
        srand((unsigned int)time(0));
#endif
#if 0
    // memory limit
    char *c = new char[64000000];
#endif
#if 0
    // memory limit in static requires initialization
    static char c[256000000];
    memset(c, -1, sizeof(c));
#endif

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
