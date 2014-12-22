#include <iostream>
#include <time.h>
#include <vector>
#include <string>
#include <queue>

std::vector<std::string> field;

struct Pos
{
    Pos() : r(0), c(0) {}
    Pos(int rr, int cc) : r(rr), c(cc) {}

    int r, c;

    bool operator==(const Pos &p) const
    {
        return r == p.r && c == p.c;
    }
};

int size;
int row[4];
int col[4];
int dir[4];
int energy[4];
int reload[4];
int hasFlag[4];
int rowF[2];
int colF[2];
int player;

const int dr[4] = {0, -1, 0, 1};
const int dc[4] = {-1, 0, 1, 0};
int initFlagR;
int initFlagC;

bool map[100][100];

typedef std::deque<Pos> Q;

int getTargetDir(Q &q, int sr, int sc)
{
    for (int i = 0 ; i < size ; ++i)
        for (int j = 0 ; j < size ; ++j)
            map[i][j] = false;
    for (Q::iterator i = q.begin() ; i != q.end() ; ++i)
        map[i->r][i->c] = true;
    int d = -1;
    while (!q.empty() && d < 0)
    {
        Pos p = q.front();
        q.pop_front();
        for (int i = 0 ; i < 4 ; ++i)
        {
            int rr = p.r + dr[i];
            int cc = p.c + dc[i];
            if (rr == sr && cc == sc)
            {
                d = i ^ 2;
                break;
            }
            if (rr >= 0 && rr < size && cc >= 0 && cc < size
                && field[rr][cc] != '#' && !map[rr][cc])
            {
                q.push_back(Pos(rr, cc));
                map[rr][cc] = true;
            }
        }
    }
    return d + 1;
}

int getTargetDir(int r, int c, int sr, int sc)
{
    Q q;
    q.push_back(Pos(r, c));
    return getTargetDir(q, sr, sc);
}

void moveTank(int t)
{
    if (energy[t] > 0)
    {
        int shoot = 0;
        int d = -1;
        if (!hasFlag[t] && rowF[2 - player] >= 0)
        {
            d = getTargetDir(rowF[2 - player], colF[2 - player], row[t], col[t]);
        }
        else if (rowF[player - 1] >= 0 
            && rowF[player - 1] != initFlagR
            && colF[player - 1] != initFlagC)
        {
            d = getTargetDir(rowF[player - 1], colF[player - 1], row[t], col[t]);
        }
        else if (hasFlag[t] && rowF[player - 1] >= 0)
        {
            d = getTargetDir(rowF[player - 1], colF[player - 1], row[t], col[t]);
        }
        else
        {
            Q q;
            for (int i = 0 ; i < size ; ++i)
                for (int j = 0 ; j < size ; ++j)
                    if (field[i][j] == 'e')
                        q.push_back(Pos(i, j));
            if (!q.empty())
                d = getTargetDir(q, row[t], col[t]);
        }

        if (d < 0 && rand() % 50 <= 30)
        {
            do
                d = rand() % 4;
            while (field[row[t] + dr[d]][col[t] + dc[d]] == '#');
            ++d;
        }

        if (reload[t] == 0)
        {
            char first = player == 1 ? '3' : '1';
            char last = player == 1 ? '4' : '2';
            int dd;
            for (dd = d >= 0 ? d - 1 : 0 ; dd <= (d >= 0 ? d - 1 : 3) ; ++dd)
            {
                int r = row[t] + dr[dd];
                int c = col[t] + dc[dd];
                while (r >= 0 && c >= 0 && r < size && c < size)
                {
                    char f = field[r][c];
                    if ((f >= first && f <= last)
                        || (f >= first + 'a' - '1' && f <= last + 'a' - '1'))
                    {
                        shoot = 1;
                        break;
                    }
                    if (f != '.' && f != 'A' && f != 'B'
                        && f != '<' && f != '>' && f != '^' && f != 'v')
                        break;
                    r += dr[dd];
                    c += dc[dd];
                }
                if (shoot)
                    break;
            }
            if (shoot)
            {
                d = dd + 1;
            }
        }

        if (d > 0)
            dir[t] = d;

        std::cout << dir[t] << " " << shoot << "\n";
    }
    else
    {
        std::cout << "1 1\n";
    }
}

int main()
{
    srand((int)clock());

    std::cin >> player;

    std::string s;
    // line-feed
    std::getline(std::cin, s);
    // first line
    std::getline(std::cin, s);
    size = (int)s.size();
    field.push_back(s);
    for (int i = 1 ; i < size ; ++i)
    {
        std::getline(std::cin, s);
        field.push_back(s);
    }

    for (int i = 0 ; i < size ; ++i)
    {
        for (int j = 0 ; j < size ; ++j)
        {
            if (field[i][j] >= '1' && field[i][j] <= '4')
            {
                row[field[i][j] - '1'] = i;
                col[field[i][j] - '1'] = j;
            }
            else if (field[i][j] >= 'a' && field[i][j] <= 'd')
            {
                row[field[i][j] - 'a'] = i;
                col[field[i][j] - 'a'] = j;
            }
        }
    }

    for (int i = 0 ; i < 4 ; ++i)
        std::cin >> energy[i] >> dir[i] >> reload[i] >> hasFlag[i];

    for (int i = 0 ; i < 2 ; ++i)
        std::cin >> rowF[i] >> colF[i];

    initFlagR = player == 1 ? 1 : size - 2;
    initFlagC = size / 2;

    moveTank(player == 1 ? 0 : 2);
    moveTank(player == 1 ? 1 : 3);

    // custom data
    std::cout << rand() << "\n";

    return 0;
}
