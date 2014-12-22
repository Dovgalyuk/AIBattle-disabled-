#include <iostream>
#include <string>
#include <vector>
#include <algorithm>

typedef unsigned long long Mask;


const int SZ = 6;

int a[SZ][SZ];
int player;
int rowcol;

int cost(Mask m1, Mask m2)
{
    int res = 0;
    Mask m = 1;
    for (int i = 0 ; i < SZ ; ++i)
        for (int j = 0 ; j < SZ ; ++j, m <<= 1)
        {
            if (m1 & m)
                res += a[i][j];
            else if (m2 & m)
                res -= a[i][j];
        }
    return res;
}

int cost2(int pl, Mask mask)
{
    Mask m = 1;
    int cnt[SZ];
    for (int i = 0 ; i < SZ ; ++i)
        cnt[i] = 0;
    int v[SZ][SZ];
    for (int i = 0 ; i < SZ ; ++i)
        for (int j = 0 ; j < SZ ; ++j, m <<= 1)
        {
            if (a[i][j] && (m & mask) == 0)
            {
                int k = pl == 0 ? j : i;
                v[k][cnt[k]++] = a[i][j];
            }
        }

    int res = 0;
    for (int i = 0 ; i < SZ ; ++i)
    {
        std::sort(&v[i][0], &v[i][cnt[i]]);
        /*for (int j = 0 ; j < cnt[i] ; ++j)
            res += v[i][j] * (j < (cnt[i] + 1) / 2 ? 1 : -1);*/
        if (cnt[i] % 2)
            res += 1;//v[i][0];
    }
    
    return res;
}

long long find(int depth, int pl, int rowcol, int prev, Mask m1, Mask m2, int *move = NULL)
{
        long long t = cost(m1, m2);
    if (depth >= 8)
    {
        long long t2 = cost2(pl, m1 | m2);
#ifdef DDD
        std::cout << "D" << std::string(depth, ' ') << "P:" << pl << " RC:" << rowcol << " RES:" << t << "\n";
#endif
        return t2+t*5;
    }

    bool found = false;
    long long res = 0;
    long long min = 1000000000000000000LL;
    int mv = -1;
    int from = 0;
    int to = SZ;
    int maxI = -1;
    if (pl != player - 1)
    {
        for (int i = 0 ; i < SZ ; ++i)
        {
            int row = pl == 0 ? rowcol : i;
            int col = pl == 0 ? i : rowcol;
            Mask m = 1LL << (col + row * SZ);
            int rowM = pl == 0 ? rowcol : maxI;
            int colM = pl == 0 ? maxI : rowcol;
            if (a[row][col] && !((m1 | m2) & m)
                && (maxI < 0 || a[rowM][colM] < a[row][col]))
            {
                maxI = i;
            }
        }
    }
    /*else
    {
        if (t < -10)
            return -50;
    }*/
    if (maxI >= 0)
    {
        from = maxI;
        to = maxI + 1;
    }
    for (int i = from ; i < to ; ++i)
    {
        int row = pl == 0 ? rowcol : i;
        int col = pl == 0 ? i : rowcol;
        //if (!greed || max < a[row][col])
        {
            Mask m = 1LL << (col + row * SZ);
            if (a[row][col] && !((m1 | m2) & m))
            {
                long long r1 = find(depth + 1, 1 - pl, i, rowcol, m2, m1 | m);
                if (r1 < min)
                {
                    mv = i;
                    min = r1;
                }
                //res -= r1;
                found = true;
            }
        }
    }
    if (move && mv >= 0)
        *move = mv;

    if (!found)
    {
        if (prev == -1)
        {
            long long t = cost(m1, m2);
#ifdef DDD
            std::cout << "P" << std::string(depth, ' ') << "P:" << pl << " RC:" << rowcol << " RES:" << t << "\n";
#endif
            return t;
        }
        res = -find(depth + 1, 1 - pl, prev, -1, m2, m1);
#ifdef DDD
        std::cout << "S" << std::string(depth, ' ') << "P:" << pl << " RC:" << rowcol << " RES:" << res << "\n";
#endif
    }
    else
    {
        res = -min;
#ifdef DDD
        std::cout << "A" << std::string(depth, ' ') << "P:" << pl << " RC:" << rowcol << " RES:" << res << " M:" << mv << "\n";
#endif
    }
    return res;
}


int main()
{
    for (int i = 0 ; i < 6 ; ++i)
        for (int j = 0 ; j < 6 ; ++j)
            std::cin >> a[i][j];

    std::cin >> player >> rowcol;

    int move = -1;
    find(0, player - 1, rowcol - 1, -1, 0, 0, &move);

    std::cout << move + 1 << "\n";

    return 0;
}
