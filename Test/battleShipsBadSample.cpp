#include <iostream>

int main()
{
    int mode = 0;
    std::cin >> mode;
    if (mode == 1)
        std::cout   << "2 5 V\n"
                    << "1 7 V\n"
                    << "3 1 H\n"
                    << "6 3 H\n"
                    << "1 3\n"
                    << "5 1\n"
                    << "6 6\n";
    if (mode == 2)
        std::cout << "6 8" << std::endl;
    return 0;
}

