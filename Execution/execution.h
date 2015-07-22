#ifndef EXECUTION_H
#define EXECUTION_H

#include <string>
#ifndef _WIN32
#include <iostream>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/wait.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/resource.h>
#endif

enum ExecutionResult
{
    ER_OK, // Корректное завершение
    ER_TL, // Превышено время выполнения
    ER_RE, // Ошибка времени выполнения
    ER_ML, // Превышен допустимый объем памяти
    ER_IE, // Внутренняя ошибка (остальные виды ошибок)
    ER_SV, // Security violation
    // Дополнительные коды - не используются в функции runProcess
    ER_IM, // Некорректный ход
    ER_WIN, // Победитель
    ER_TIE, // Ничья
};

/*! Executes the specified process with limitations.
  \param exe Executable to run         
  \param input Input data                
  \param output Output data               
  \param timeLimit Time Limit in milliseconds
  \param memoryLimit Memory Limit in kilobytes  
*/
ExecutionResult runProcess(const std::string &exe, const std::string &input,
    std::string &output, int timeLimit, int memoryLimit);

void printLog(bool first, ExecutionResult result, const std::string &output);
void printField(const std::string &output);
void printInput(bool first, const std::string &input);
void printAnimation(const std::string &output);
void printAnimationStart();
void printAnimationEnd();

#endif
