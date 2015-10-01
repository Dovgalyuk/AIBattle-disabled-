#include "testlib.h"

int main()
{
    InStream in("b 1");
    char p;
    char r[] = {'a', 'b', 'c'};
    int k;
    int r1[] = {0, 1, 2, 3, 4, 5, 6, 7, 8, 9};


    try
    {
        in >> ValueInRange<char>(p, r, 3) >> ValueInRange<int>(k, r1, 10);
        std::cout << p << " " << k << std::endl;
    }
    catch (ReadCheckerException ex)
    {
        std::cerr << ex.getReadResultText() << ": " << ex.what() << std::endl;
    }

    return 0;
}

