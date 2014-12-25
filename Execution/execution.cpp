#include <windows.h>
#include <iostream>
#include "execution.h"

static void shutdownProcess(const PROCESS_INFORMATION &pi, HANDLE hChildStdinRd, HANDLE hChildStdinWr, HANDLE hChildStdoutRd, HANDLE hChildStdoutWr)
{
    //CloseHandle(hChildStdinWr);
    CloseHandle(hChildStdinRd);
    CloseHandle(hChildStdoutRd);
    //CloseHandle(hChildStdoutWr);
    CloseHandle(pi.hProcess);
    CloseHandle(pi.hThread);
}

ExecutionResult runProcess(const std::string &exe, const std::string &input,
    std::string &output, int timeLimit, int memoryLimit)
{
    // должна запускать программу, ограничивать ее время и память,
    // а потом возвращать код ошибки и вывод
    PROCESS_INFORMATION pi;
    STARTUPINFO si;
    SECURITY_ATTRIBUTES sa;

    ZeroMemory(&pi, sizeof(pi));
    ZeroMemory(&si, sizeof(si));
    ZeroMemory(&sa, sizeof(sa));

    sa.nLength = sizeof(sa);
    sa.bInheritHandle = TRUE;
    sa.lpSecurityDescriptor = NULL;

    HANDLE hChildStdinRd, hChildStdinWr, hChildStdoutRd, hChildStdoutWr;

    // Create a pipe for the child process's STDOUT.
    if (!CreatePipe(&hChildStdoutRd, &hChildStdoutWr, &sa, 0))
    {
        output = "Cannot create a pipe for the child process's STDOUT! Reason:\n"  + GetLastError();
        return ER_IE;
    }

    // Create a pipe for the child process's STDIN.
    if (!CreatePipe(&hChildStdinRd, &hChildStdinWr, &sa, 0))
    {
        CloseHandle(hChildStdoutRd);
        CloseHandle(hChildStdoutWr);
        output = "Cannot create a pipe for the child process's STDIN! Reason:\n"  + GetLastError();
        return ER_IE;
    }

    // Start the process
    si.cb = sizeof(si);
    si.dwFlags = STARTF_USESTDHANDLES;
    si.hStdError = hChildStdoutWr;
    si.hStdOutput = hChildStdoutWr;
    si.hStdInput = hChildStdinRd;

    if (!CreateProcess(exe.c_str(), NULL, NULL, NULL, true, CREATE_NO_WINDOW, NULL, NULL, &si, &pi))
    {
        //std::cout << exe << std::endl; Для теста!
        output = "CreateProcess failed!";
        CloseHandle(hChildStdinWr);
        CloseHandle(hChildStdinRd);
        CloseHandle(hChildStdoutRd);
        CloseHandle(hChildStdoutWr);
        return ER_IE;
    }
    else
    {
        DWORD written;
        BOOL fSuccess;
        // Write terminating zero to make EOF
        fSuccess = WriteFile(hChildStdinWr, input.c_str(), input.length() + 1, &written, NULL);
        CloseHandle(hChildStdoutWr);
        CloseHandle(hChildStdinWr);
        if (!fSuccess || written <= input.length())
        {
            output = "WriteFile failed!";
            shutdownProcess(pi, hChildStdinRd, hChildStdinWr, hChildStdoutRd, hChildStdoutWr);
            return ER_IE;
        }

        DWORD processStatus = WaitForSingleObject(pi.hProcess, timeLimit);
        if (processStatus == WAIT_TIMEOUT)
        {
            // время вышло, сообщаем об этом
            output = "processStatus: TIMEOUT!";
            TerminateProcess(pi.hProcess, 0);
            shutdownProcess(pi, hChildStdinRd, hChildStdinWr, hChildStdoutRd, hChildStdoutWr);
            return ER_TL;
        }
        else if (processStatus == WAIT_FAILED)
        {
            // произошла непонятная ошибка
            output = "WaitForSingleObject failed!";
            TerminateProcess(pi.hProcess, 0);
            shutdownProcess(pi, hChildStdinRd, hChildStdinWr, hChildStdoutRd, hChildStdoutWr);
            return ER_IE;
        }
        else if (processStatus == WAIT_OBJECT_0)
        {
            DWORD exitCode = 0;
            if (!GetExitCodeProcess(pi.hProcess, &exitCode))
            {
                output = "Cannot get process exit code.";
                shutdownProcess(pi, hChildStdinRd, hChildStdinWr, hChildStdoutRd, hChildStdoutWr);
                return ER_IE;
            }
            if (exitCode != 0)
            {
                output = "Program terminated with non-zero exit code";
                shutdownProcess(pi, hChildStdinRd, hChildStdinWr, hChildStdoutRd, hChildStdoutWr);
                return ER_RE;
            }
            // программа работает корректно
            for (;;)
            {
                char buffer[128];
                DWORD read;
                BOOL readSuccess = ReadFile(hChildStdoutRd, buffer, sizeof(buffer) - 1, &read, NULL);

                if (!readSuccess || read == 0)
                    break;
                buffer[read] = 0;
                output += buffer;
            }
            shutdownProcess(pi, hChildStdinRd, hChildStdinWr, hChildStdoutRd, hChildStdoutWr);
            if (output.find("END_OF_OUTPUT") != std::string::npos
                || output.find("END_OF_INPUT") != std::string::npos
                || output.find("END_OF_ANIMATION") != std::string::npos
                || output.find("END_OF_FIELD") != std::string::npos)
            {
                return ER_SV;
            }
            // посылаем что всё окей
            return ER_OK;
        }
    }

    return ER_IE;
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
    // формирует строку для вывода в лог 
    // и выводит ее в стандартный поток вывода
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
        break;
    case ER_IE:
        std::cout << " " << output << "\n";
        break;
    default:
        break;
    }
    std::cout << "\n";
}


void printField(const std::string &output)
{
    // формирует строку для вывода в лог 
    // и выводит ее в стандартный поток вывода
    std::cout << "FIELD\n" << output << "\nEND_OF_FIELD\n";
}

void printInput(bool first, const std::string &input)
{
    // формирует строку для вывода в лог 
    // и выводит ее в стандартный поток вывода
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

