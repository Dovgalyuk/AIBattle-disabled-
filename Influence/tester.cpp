#include <sstream>
#include <ctime>
#include <iostream>
#include <vector>
#include <map>
#include <set>
#include <algorithm>
#include <cstring>
#include "execution.h"

std::vector<int>graph[50];
std::pair<int, int> map_[50];
int num_st[50];
std::vector<std::pair<int, int> > edges;
std::vector<int>verts;
bool was[50];
int n;

void saveField(int n)
{
	std::ostringstream outs;
    outs << n << "\n";
    for (int i = 0; i < n; i++)
    {
        outs << graph[i].size() << " ";
        for (int j = 0; j < int(graph[i].size()); j++)
        {
            outs << graph[i][j] << " ";
        }
        outs << "\n";
    }
    for (int i = 0; i < n; ++i)
    {
        if (map_[i].second != 0)
            outs << i << " " << map_[i].second << " " << map_[i].first << "\n";
    }
	printField(outs.str());
}



void genGraph(int v, int amount)
{
    int step = rand() % (amount / 3 - num_st[v]) + 1;
    was[v] = true;
    for (int i = 0; i < step; i++)
    {
        std::vector<int> tmp;
        for (int i = 0; i < n; i++)
        {
            bool tf = true;
            for (int j = 0; j < int(graph[v].size()); j++)
            {

                if (graph[v][j] == i)
                {
                    tf = false;
                    break;
                }
            }
            if (tf)
            {
                tmp.push_back(i);
            }
        }
        int x = tmp[rand() % tmp.size()];
        graph[v].push_back(x);
        graph[x].push_back(v);
        num_st[v]++;
        num_st[x]++;
        if (!was[x])
        {
            genGraph(x, amount);
        }
    }
}

void genMap(int v, int c)
{
    was[v] = true;
    for (int i = 0; i < int(graph[v].size()); i++)
    {
        if (!was[graph[v][i]])
        {
            edges.push_back(std::make_pair(v, graph[v][i]));
            verts.push_back(graph[v][i]);
            if (map_[graph[v][i]].second == c)
            {
                genMap(graph[v][i], c);
            }
        }
    }
}

void was_cl()
{
    for (int i = 0; i < 50; i++)
    {
        was[i] = false;
    }
}

int main(int argc, char **argv)
{
    if (argc < 3)
    {
        std::cout << "Usage: InfluenceTester <program1> <program2> [seed]\n";
        return 1;
    }
    const char *program1 = argv[1];
    const char *program2 = argv[2];
    //Random initialization
    if (argc >= 4)
        srand(atoi(argv[3]));
    else
        srand((unsigned int)time(NULL));

    n = rand() % 30 + 10;
    genGraph(0, n);
    for (int i = 0; i < n; ++i)
    {
        std::unique(graph[i].begin(), graph[i].end());
    }
    int first_beg = rand() % n;
    int second_beg = rand() % n;

    while (first_beg == second_beg)
    {    
        second_beg = rand() % n;
    }

    map_[first_beg] = std::make_pair(1, 1);
    map_[second_beg] = std::make_pair(1, 2);

    ExecutionResult result = ER_OK;
    for (int move = 0 ; move < 500 ; ++move)
    {
        saveField(n);
        bool first = move % 2 == 0;
        int num_player = move % 2 + 1;
        std::ostringstream outs;
        outs << num_player << "\n"; 
        int counter = 0;
        for (int i = 0; i < n; i++)
        {
            if (map_[i].second == num_player)
            {
                counter++;
            }
        }
        outs << "Phase 1\n";
        outs << counter << "\n";
        edges.clear();
        verts.clear();
        was_cl();
        int beg;
        for (int i = 0; i < n; i++)
        {
            if (map_[i].second == num_player && !was[i])
            {
                verts.push_back(i);
                genMap(i, num_player);
            }
        }
        outs << verts.size() << " " << edges.size() << "\n";
        for (int i = 0; i < int(edges.size()); i++)
        {
            outs << edges[i].first << " " << edges[i].second << "\n";
        }
        for (int i = 0; i < int(verts.size()); i++)
        {
            outs << verts[i] << " " << map_[verts[i]].first << " " << map_[verts[i]].second << "\n";
        }
        printInput(first, outs.str());
        std::string output;
        result = runProcess(first ? program1 : program2, outs.str(), output, 1000, 64000);
        if (result == ER_OK)
        {
            std::istringstream ins(output);
            int k, v, num, tmp = 0;
            std::vector<std::pair<int, int> > list;
            ins >> k;
            printLog(first, result, output);
            if (k > int(verts.size()))
            {
                result = ER_IM;
                return 0;
            }
            for (int i = 0; i < k; i++)
            {
                ins >> v >> num;
                bool t = false;
                for (int j = 0; j < int(verts.size()); j++)
                {
                    if (verts[j] == v)
                    {
                        t = true;
                        break;
                    }
                }
                if (!t || map_[v].second != num_player || num <= 0)
                {
                    result = ER_IM;
                    return 0;
                }
                list.push_back(std::make_pair(v, num));
                tmp += num;
            }
            //check
            if (tmp != counter)
            {
                result = ER_IM;
                return 0;
            }
            for (int i = 0; i < k; i++)
            {
                map_[list[i].first].first += list[i].second;
            }
            std::ostringstream outs;
            outs << num_player << "\n";
            outs << "Phase 2\n";
            edges.clear();
            verts.clear();
            was_cl();
            int beg;
            for (int i = 0; i < n; i++)
            {
                if (map_[i].second == num_player && !was[i])
                {
                    verts.push_back(i);
                    genMap(i, num_player);
                }
            }
            outs << verts.size() << " " << edges.size() << "\n";
            for (int i = 0; i < int(edges.size()); i++)
            {
                outs << edges[i].first << " " << edges[i].second << "\n";
            }
            for (int i = 0; i < int(verts.size()); i++)
            {
                outs << verts[i] << " " << map_[verts[i]].first << " " << map_[verts[i]].second << "\n";
            }
            std::string output;
            printInput(first, outs.str());
            result = runProcess(first ? program1 : program2, outs.str(), output, 1000, 64000);
            printLog(first, result, output);
            std::istringstream insPhase2(output);
            int u;
            std::string s;
            insPhase2 >> s;
            while (s != "END_CONQUEST")
            {
                insPhase2 >> u >> v;
                //check
                bool t = false;
                for (int i = 0; i < int(graph[u].size()); i++)
                {
                    if (graph[u][i] == v)
                    {
                        t = true;
                        break;
                    }
                }
                if (map_[u].second != num_player || map_[v].second == num_player || !t || map_[u].first <= 1)
                {
                    result = ER_IM;
                    return 0;
                }                
                if ((map_[u].first - 1) * (rand() % 6 + 1) > map_[v].first * (rand() % 6 + 1))
                {
                    map_[v] = std::make_pair(std::max(1, map_[u].first - 1 - map_[v].first), num_player);

                    for (int i = 0; i < int(graph[v].size()); i++) 
                    {

                        if (!was[graph[v][i]]) 
                        {
                            edges.push_back(std::make_pair(v, graph[v][i]));
                            verts.push_back(graph[v][i]);
                        }
                    } 
                } 
                else 
                {
                    map_[v].first = std::max(1, map_[v].first - map_[u].first);
                }
                map_[u].first = 1;
                std::string output;
                outs = std::ostringstream();
                outs << num_player << "\n";
                outs << "Phase 2\n";
                edges.clear();
                verts.clear();
                was_cl();
                int beg;
                for (int i = 0; i < n; i++)
                {
                    if (map_[i].second == num_player && !was[i])
                    {
                        verts.push_back(i);
                        genMap(i, num_player);
                    }
                }
                outs << verts.size() << " " << edges.size() << "\n";
                for (int i = 0; i < int(edges.size()); i++)
                {
                    outs << edges[i].first << " " << edges[i].second << "\n";
                }
                for (int i = 0; i < int(verts.size()); i++)
                {
                    outs << verts[i] << " " << map_[verts[i]].first << " " << map_[verts[i]].second << "\n";
                }
                printInput(first, outs.str());
                result = runProcess(first ? program1 : program2, outs.str(), output, 1000, 64000);
                printLog(first, result, output);
                if (result == ER_OK)
                {
                    insPhase2 = std::istringstream(output);
                    insPhase2 >> s;
                }
                else
                {
                    printLog(first, result, "");
                    return 0;
                }
            }
            for (int i = 0; i < n; i++)
            {
                if (map_[i].second != num_player && map_[i].second != 0)
                    goto go;
            }
            result = ER_WIN;
            printLog(first, result, output);
            break;
        }
        else
        {
            printLog(first, result, "");
            break;
        }
        go:;
    }

    if (result == ER_OK)
    {
        result = ER_TIE;
        printLog(false, result, "");
    }

    return 0;
}
