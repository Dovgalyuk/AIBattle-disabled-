#include <iostream>
#include <fstream>
//#include <map>

//std::ofstream debug("debug.log");

#ifdef V2
typedef int ResType;
#else
typedef long long ResType;
#endif

struct Board
{
    int a[4][4];

    int getId() const
    {
        int r = 0;
        for (int i = 0 ; i < 4 ; ++i)
            for (int j = 0 ; j < 4 ; ++j)
                r = r * 3 + a[i][j];

        return r;
    }

    int getId2() const
    {
        int r = 0;
        for (int i = 0 ; i < 4 ; ++i)
            for (int j = 0 ; j < 4 ; ++j)
                r = r * 3 + a[j][i];

        return r;
    }

    int getId3() const
    {
        int r = 0;
        for (int i = 3 ; i >= 0 ; --i)
            for (int j = 0 ; j < 4 ; ++j)
                r = r * 3 + a[i][j];

        return r;
    }

    int getId4() const
    {
        int r = 0;
        for (int i = 3 ; i >= 0 ; --i)
            for (int j = 3 ; j >= 0 ; --j)
                r = r * 3 + a[i][j];

        return r;
    }

    int getId5() const
    {
        int r = 0;
        for (int i = 0 ; i < 4 ; ++i)
            for (int j = 3 ; j >= 0 ; --j)
                r = r * 3 + a[i][j];

        return r;
    }

    int getId6() const
    {
        int r = 0;
        for (int i = 3 ; i >= 0 ; --i)
            for (int j = 3 ; j >= 0 ; --j)
                r = r * 3 + a[j][i];

        return r;
    }

    int getId7() const
    {
        int r = 0;
        for (int i = 3 ; i >= 0 ; --i)
            for (int j = 0 ; j < 4 ; ++j)
                r = r * 3 + a[j][i];

        return r;
    }

    int getId8() const
    {
        int r = 0;
        for (int i = 0 ; i < 4 ; ++i)
            for (int j = 3 ; j >= 0 ; --j)
                r = r * 3 + a[j][i];

        return r;
    }

};

//typedef std::pair<int, Board> Key;
struct Pos
{
    Pos() : x(0xff), y(0xff) {}
    Pos(ResType r, int xx, int yy)
        : res(r), x(xx), y(yy)
    {
    }

    ResType res;
    unsigned char x, y;
};

//typedef std::map<Key, Pos> Positions;

std::istream &operator>>(std::istream &stream, Board &board)
{
    for (int i = 0 ; i < 4 ; ++i)
        for (int j = 0 ; j < 4 ; ++j)
        {
            int t;
            stream >> t;
            board.a[i][j] = t;
        }

    return stream;
}

std::ostream &operator<<(std::ostream &stream, const Board &board)
{
    for (int i = 0 ; i < 4 ; ++i)
    {
        for (int j = 0 ; j < 4 ; ++j)
            stream << (int)board.a[i][j];
        stream << "\n";
    }

    return stream;
}

bool operator<(const Board &b1, const Board &b2)
{
    for (int i = 0 ; i < 4 ; ++i)
        for (int j = 0 ; j < 4 ; ++j)
            if (b1.a[i][j] != b2.a[i][j])
                return b1.a[i][j] < b2.a[i][j];

    return false;
}

//Positions positions;
Board board;
Pos positions[43046721];
long long calls, calcs, ends;

bool findPos(int player, Pos &pos)
{
    /*
    Positions::iterator it = positions.find(Key(player, board));
    if (it != positions.end())
    {
        pos = it->second;
        return true;
    }

    return false;*/
    pos = positions[board.getId()];
    if (pos.x < 0xff)
        return true;
    /*pos = positions[player - 1][board.getId2()];
    if (pos.x < 0xff)
    {
        std::swap(pos.x, pos.y);
        return true;
    }*/
    /*pos = positions[player - 1][board.getId3()];
    if (pos.x < 0xff)
    {
        pos.y = 3 - pos.y;
        return true;
    }
    pos = positions[player - 1][board.getId4()];
    if (pos.x < 0xff)
    {
        pos.x = 3 - pos.x;
        pos.y = 3 - pos.y;
        return true;
    }*/
    return false;
}

bool check(int i, int j, int ii, int jj)
{
    return !(ii < 0 || ii >= 4 || jj < 0 || jj >= 4
        || board.a[ii][jj] != board.a[i][j]);
}

int getSum()
{
    int res = 0;
    for (int i = 0 ; i < 4 ; ++i)
    {
        for (int j = 0 ; j < 4 ; ++j)
            if (board.a[i][j] > 0)
            {
                int di[4] = {0, 1, 1, 1};
                int dj[4] = {1, 0, 1, -1};
                for (int k = 0 ; k < 4 ; ++k)
                {
                    bool ok = true;
                    for (int d = 1 ; d <= 2 ; ++d)
                    {
                        int ii = i + di[k] * d;
                        int jj = j + dj[k] * d;
                        if (ii < 0 || ii >= 4 || jj < 0 || jj >= 4
                            || board.a[ii][jj] != board.a[i][j])
                        {
                            ok = false;
                            break;
                        }
                    }
                    if (ok)
                    {
                        if (board.a[i][j] == 1)
                            ++res;
                        else
                            --res;
                    }
                }
            }
    }

    return res;
}

int addSum(int i, int j)
{
    int res = 0;
    {
        int di[8] = {0, 1, 0, -1, 1, -1, 1, -1};
        int dj[8] = {1, 0, -1, 0, 1, -1, -1, 1};
        for (int k = 0 ; k < 8 ; ++k)
        {
            if (check(i, j, i + di[k], j + dj[k]) && check(i, j, i + di[k] * 2, j + dj[k] * 2))
            {
                if (board.a[i][j] == 1)
                    ++res;
                else
                    --res;
            }
        }
    }

    {
        int di[4] = {0, 1, 1, 1};
        int dj[4] = {1, 0, 1, -1};
        for (int k = 0 ; k < 4 ; ++k)
        {
            if (check(i, j, i + di[k], j + dj[k]) && check(i, j, i - di[k], j - dj[k]))
            {
                if (board.a[i][j] == 1)
                    ++res;
                else
                    --res;
            }
        }
    }

    return res;
}

ResType f(int player, int depth, int sum, int &x, int &y)
{
    ++calls;

    Pos pos;
    if (findPos(player, pos))
    {
        x = pos.x;
        y = pos.y;
        return pos.res;
    }

    ++calcs;

    x = 0;
    y = 0;

    bool end = true;
    int bestx, besty;
    ResType res = 0;
#ifdef V2
    ResType rr = 100;
#else
    ResType rr = 1000000000000LL;
#endif

    if (depth > 0)
    {
        for (int i = 0 ; i < 4 ; ++i)
        {
            for (int j = 0 ; j < 4 ; ++j)
            {
                if (board.a[i][j] == 0)
                {
                    end = false;

                    int x1, y1;
                    board.a[i][j] = player;
                    ResType r = f(3 - player, depth - 1, sum + addSum(i, j), x1, y1);
                    board.a[i][j] = 0;
#ifndef V2
                    res += r;
#endif
                    if (r < rr)
                    {
#ifdef V2
                        res = r;
#endif
                        rr = r;
                        bestx = j;
                        besty = i;
                    }
                }
            }
        }
    }

    if (end)
    {
        ++ends;
        res = player == 1 ? sum : -sum;
    }
    else
    {
        res = -res;
        x = bestx;
        y = besty;
    }

    //positions.insert(std::make_pair(Key(player, board), Pos(res, x, y)));
    positions[board.getId()] = Pos(res, x, y);
    positions[board.getId2()] = Pos(res, y, x);
    positions[board.getId3()] = Pos(res, x, 3 - y);
    positions[board.getId4()] = Pos(res, 3 - x, 3 - y);
    positions[board.getId5()] = Pos(res, 3 - x, y);
    positions[board.getId6()] = Pos(res, 3 - y, 3 - x);
    positions[board.getId7()] = Pos(res, 3 - y, x);
    positions[board.getId8()] = Pos(res, y, 3 - x);

    //debug << "Added pl=" << player << " r=" << res << " x=" << x << " y=" << y << "\n" << board << "\n";

    return res;
}

int main()
{
    int x, y, player;
    std::cin >> player >> board;
    f(player, 16, getSum(), x, y);

    std::cout << y + 1 << " " << x + 1;

    //debug << "Total calls: " << calls << "\nNew states: " << calcs << "\nEnd states: " << ends << "\n";

    return 0;
}
