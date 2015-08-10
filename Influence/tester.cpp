#include <sstream>
#include <ctime>
#include <iostream>
#include <vector>
#include <map>
#include <set>
#include <algorithm>
#include <cstring>
#include "execution.h"

typedef std::vector<int> adjGraphV;
typedef std::pair<int, int> cityInfo;
typedef std::vector<std::pair<int, int> > roads;
typedef std::vector<int> cities;

const int MAX_CITIES = 50;
const int MAX_MOVES = 500;

adjGraphV graph[MAX_CITIES];
cityInfo map[MAX_CITIES];
int num_st[MAX_CITIES];
roads edges;
cities verts;
bool was[MAX_CITIES];
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
        if (map[i].second != 0)
            outs << i << " " << map[i].second << " " << map[i].first << "\n";
    }

	printField(outs.str());
}

void genGraph(int v, int amount)
{
    int step = rand() % (amount / 3 - num_st[v]) + 1;
    was[v] = true;

    for (int k = 0; k < step; k++)
    {
        cities tmp;
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

void genVisibilityZone(int v, int c)
{
    was[v] = true;

    for (int i = 0; i < int(graph[v].size()); i++)
    {
        if (!was[graph[v][i]])
        {
            edges.push_back(std::make_pair(v, graph[v][i]));
            verts.push_back(graph[v][i]);
            if (map[graph[v][i]].second == c)
            {
                genVisibilityZone(graph[v][i], c);
            }
        }
    }
}

void wasCl()
{
    for (int i = 0; i < MAX_CITIES; i++)
    {
        was[i] = false;
    }
}

void printGraph(std::ostream &outs, int num_player)
{
    edges.clear();
    verts.clear();
    wasCl();

    int beg;
    for (int i = 0; i < n; i++)
    {
        if (map[i].second == num_player && !was[i])
        {
            verts.push_back(i);
            genVisibilityZone(i, num_player);
        }
    }

    outs << verts.size() << " " << edges.size() << "\n";
    for (int i = 0; i < int(edges.size()); i++)
    {
        outs << edges[i].first << " " << edges[i].second << "\n";
    }

    for (int i = 0; i < int(verts.size()); i++)
    {
        outs << verts[i] << " " << map[verts[i]].first << " " << map[verts[i]].second << "\n";
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

    n = rand() % 30 + 10; // Number of cities
    genGraph(0, n); // Generating map
    for (int i = 0; i < n; ++i)
    {
        std::unique(graph[i].begin(), graph[i].end());
    }
    int first_beg = rand() % n; // First's capital
    int second_beg = rand() % n; // Second's capital

    while (first_beg == second_beg)
    {    
        second_beg = rand() % n;
    }

    map[first_beg] = std::make_pair(1, 1);
    map[second_beg] = std::make_pair(1, 2);

    ExecutionResult result = ER_OK;
    for (int move = 0 ; move < MAX_MOVES ; ++move)
    {
        saveField(n); // Print field
        bool first = move % 2 == 0;
        int num_player = move % 2 + 1;
        std::ostringstream outs;
        outs << num_player << "\n"; 

        int counter = 0; // How many free scores player has to distribute
        for (int i = 0; i < n; i++)
        {
            if (map[i].second == num_player)
            {
                counter++;
            }
        }

        outs << "Phase 1\n";
        outs << counter << "\n";
        printGraph(outs, num_player);

        printInput(first, outs.str());
        std::string output;

        result = runProcess(first ? program1 : program2, outs.str(), output, 1000, 64000);
        if (result == ER_OK)
        {
            std::istringstream ins(output);
            int k, v, num, tmp = 0;
            roads list;

            ins >> k;
            printLog(first, result, output);
            if (k > int(verts.size()))
            {
                result = ER_IM;
                return 0;
            }

            for (int i = 0; i < k; i++) // Distributing scores
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
                if (!t || map[v].second != num_player || num <= 0)
                {
                    result = ER_IM;
                    return 0;
                }
                list.push_back(std::make_pair(v, num));
                tmp += num;
            }
            // Check if player distributed all scores
            if (tmp != counter)
            {
                result = ER_IM;
                return 0;
            }
            for (int i = 0; i < k; i++)
            {
                map[list[i].first].first += list[i].second;
            }

            std::ostringstream outs;
            outs << num_player << "\n";
            outs << "Phase 2\n";
            printGraph(outs, num_player);

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
                if (map[u].second != num_player || map[v].second == num_player || !t || map[u].first <= 1)
                {
                    result = ER_IM;
                    return 0;
                }                
                if ((map[u].first - 1) * (rand() % 6 + 1) > map[v].first * (rand() % 6 + 1))
                {
                    map[v] = std::make_pair(std::max(1, map[u].first - 1 - map[v].first), num_player);

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
                    map[v].first = std::max(1, map[v].first - map[u].first);
                }

                map[u].first = 1;
                std::string output;
                outs.str("");
                outs.clear();
                outs << num_player << "\n";
                outs << "Phase 2\n";
                printGraph(outs, num_player);

                printInput(first, outs.str());
                result = runProcess(first ? program1 : program2, outs.str(), output, 1000, 64000);
                printLog(first, result, output);
                if (result == ER_OK)
                {
                    insPhase2.clear();
                    insPhase2.str(output);
                    insPhase2 >> s;
                }
                else
                {
                    printLog(first, result, "");
                    return 0;
                }
            }
            bool isWinning = true;
            for (int i = 0; i < n; i++)
            {
                if (map[i].second != num_player && map[i].second != 0)
                {
                    isWinning = false;
                    break;
                }
            }
            if (isWinning)
            {
                result = ER_WIN;
                printLog(first, result, output);
                break;
            }
        }
        else
        {
            printLog(first, result, "");
            break;
        }
    }

    if (result == ER_OK)
    {
        result = ER_TIE;
        printLog(false, result, "");
    }

    return 0;
}
