#include <iostream>
#include <fstream>
#include <sstream>
#include "execution.h"

ExecutionResult runProcess(const std::string &exe, const std::string &input,
    std::string &output, int timeLimit, int memoryLimit)
{
    std::ofstream inputfile("input.txt");
    inputfile << input;
    inputfile.close();
    FILE* pipe = popen(std::string(("./timeout --detect-hangups --no-info-on-success -t ")
            + std::to_string(timeLimit/1000.0) + " -m " +
            std::to_string(memoryLimit) + " " + exe + " < input.txt > output.txt 2> result.txt").c_str(), "r");
    if (!pipe) return ER_IE;
    char buffer[128];
    std::string result = "";
    while(!feof(pipe))
    {
        if(fgets(buffer, 128, pipe) != NULL)
            result += buffer;
    }
    std::ifstream resultFile("result.txt");
    std::string ex_result;
    resultFile >> ex_result;
    if (ex_result != "FINISHED")
    {
        if ((ex_result == "TIMEOUT") || (ex_result == "HANGUP"))
            return ER_TL;
        if (ex_result == "MEM")
            return ER_ML;
    }
    resultFile.close();
    if (pclose(pipe) / 256 > 0)
        return ER_RE;
    std::ifstream inFile;
    inFile.open("output.txt");

    std::stringstream strStream;
    strStream << inFile.rdbuf();
    output = strStream.str();
    inFile.close();
    return ER_OK;
}

static std::string getResultString(ExecutionResult result)
{
    switch (result)
    {
    case ER_OK: return "OK";
    case ER_TL: return "TL";
    case ER_RE: return "RE";
    case ER_ML: return "ML";
    case ER_IE: return "IE";
    case ER_SV: return "SV";
    case ER_IM: return "IM";
    case ER_WIN: return "WIN";
    case ER_TIE: return "TIE";
    default:
        return "Invalid result code";
    }
}

void printLog(bool first, ExecutionResult result, const std::string &output)
{
    std::cout << getResultString(result) << " ";
    switch (result)
    {
    case ER_OK:
    case ER_IM:
        std::cout << (first ? 1 : 2) << "\n" << output << "\nEND_OF_OUTPUT";
        break;
    case ER_TL:
    case ER_RE:
    case ER_ML:
    case ER_SV:
    case ER_WIN:
        std::cout << (first ? 1 : 2);
        break;
    case ER_TIE:
    case ER_IE:
        break;
    default:
        break;
    }
    std::cout << "\n";
}


void printField(const std::string &output)
{
    std::cout << "FIELD\n" << output << "\nEND_OF_FIELD\n";
}

void printInput(bool first, const std::string &input)
{
    std::cout << "INPUT " << (first ? 1 : 2) << "\n" << input << "\nEND_OF_INPUT\n";
}

void printAnimation(const std::string &output)
{
    std::cout << output << "\n";
}

void printAnimationStart()
{
    std::cout << "ANIMATION\n";
}

void printAnimationEnd()
{
    std::cout << "END_OF_ANIMATION\n";
}

