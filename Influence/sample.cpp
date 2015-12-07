#include <iostream>
#include <ctime>
#include <cstdio>
#include <stdio.h>     
#include <math.h>
#include <algorithm>
#include <vector>
#include <cstring>
#include <string>
#include <map>
#include <set>
#include <cmath>


using namespace std;

int n, m, phase, player;
vector<int> graph[50], verts;
pair<int, int> map_[50];
string s;

int main()
{
    cin >> player >> s >> phase;
    srand((unsigned int)time(0));
    for (int i = 0; i < 50; i++)
    {
        map_[i].second = -1;
    }
    if (phase == 1)
    {
        int points;
        cin >> points;
        int n, m, u, v, k, tp;
        map<int, int> ans;
        cin >> n >> m;
        for (int i = 0; i < m; i++)
        {
            cin >> u >> v;
            graph[u].push_back(v);
            graph[v].push_back(u);
        }
        for (int i = 0; i < n; i++)
        {
            cin >> u >> k >> tp;
            verts.push_back(u);
            map_[u] = make_pair(k, tp);
        }
        while (points > 0)
        {
            v = verts[rand() % verts.size()];
            if (map_[v].second == player)
            {
                int want = rand() % points + 1;
                points -= want;
                if (!ans.count(v))
                {
                    ans[v] = want;
                } else {
                    ans[v] += want;
                }
            }
        }
        cout << ans.size() << "\n";
        for (map<int, int> :: iterator it = ans.begin(); it != ans.end(); it++)
        {
            cout << it->first << " " << it->second << "\n";
        }
    } 
    else
    { 
        int n, m, u, v, k, tp;
        cin >> n >> m;
        for (int i = 0; i < m; i++)
        {
            cin >> u >> v;
            graph[u].push_back(v);
            graph[v].push_back(u);
        }
        for (int i = 0; i < n; i++)
        {
            cin >> u >> k >> tp;
            map_[u] = make_pair(k, tp);
        }
        for (int i = 0; i < n; i++)
        {
            if (map_[i].second == player && map_[i].first > 1)
            {
                for (int j = 0; j < graph[i].size(); j++)
                {
                    if ((map_[graph[i][j]].second != player) && (rand() % 2 == 0))
                    {
                        cout << "CONTINUE_CONQUEST\n" << i << " " << graph[i][j];
                        return 0;
                    }
                }
            }
        }
        cout << "END_CONQUEST";
    }
    return 0;
}
