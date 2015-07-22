#include "execution.h"

ExecutionResult runProcess(const std::string &exe, const std::string &input,
                           std::string &output, int timeLimit, int memoryLimit)
{
    //Running process
    int outputPipe[2];
    int inputPipe[2];
    pipe(outputPipe);
    pipe(inputPipe);
    int pid = fork();
    if (pid == 0)
    {
        //I am child
        close(outputPipe[0]); //Close reading end of output
        close(inputPipe[1]);
        
        dup2(outputPipe[1], 1); //Send stdout to tester
        //dup2(outputPipe[1], 2); //Send stderr to tester
        
        dup2(inputPipe[0], 0); //Receive stdin from tester
        
        //close(outputPipe[1]);
        //close(inputPipe[0]);
        
        rlimit rmemlimit; // Memory Limit
        rmemlimit.rlim_cur = memoryLimit * 1024;
        rmemlimit.rlim_max = memoryLimit * 1024;
        setrlimit(RLIMIT_AS, &rmemlimit);

        rlimit rtimelimit; //Time limit
        rtimelimit.rlim_cur = timeLimit / 1000;
        rtimelimit.rlim_max = timeLimit / 1000;
        setrlimit(RLIMIT_CPU, &rtimelimit);

        char the_path[256];
        getcwd(the_path, 255);
        std::string work_dir = std::string(the_path);
        execlp(std::string("./" + exe).c_str(), NULL);
        exit(0);
    }
    else if (pid < 0)
    {
        //Failed to fork
        close(inputPipe[0]);
        close(inputPipe[1]);
        close(outputPipe[0]);
        close(outputPipe[1]);
        return ER_IE;
    }
    else
    {    
        close(inputPipe[0]);
        //close(inputPipe[1]);
        //close(outputPipe[0]);
        close(outputPipe[1]);

        char inBuffer[1024];
        int copied = 0, inputLength = input.length();
        std::size_t length;
        while (copied < inputLength)
        {
            length = input.copy(inBuffer, 1024, copied);
            copied += 1024;
            write(inputPipe[1], inBuffer, length);
        }
        char zeroBuffer[1] = {'\0'};
        write(inputPipe[1], zeroBuffer, 1);
        close(inputPipe[1]); //Close writing end of input
        int status;
        int timeout_pid = fork();
        if (timeout_pid == 0)
        {
            usleep(timeLimit * 1000);
            exit(0);
        }
        int status_new;
        int exited_pid = wait(&status_new);
        if (exited_pid == pid)
        {
            kill(timeout_pid, SIGKILL);
        }
        else
        {
            kill(pid, SIGKILL);
            status_new = 2;
        }
        wait(NULL);
        status /= 256;
        char buffer[1024];
        int z;
        while ((z = read(outputPipe[0], buffer, sizeof(buffer))) != 0)
        {
            for (int i = 0; i < z; ++i)
                output += buffer[i];
        }
        if (status_new == 0)
            return ER_OK;
        if (status_new == 1)
            return ER_RE;
        if (status_new == 2)
            return ER_TL;
        return ER_IE;
    }
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

