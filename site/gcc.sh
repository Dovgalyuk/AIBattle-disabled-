#!/bin/bash
g++ -x c++ ./executions/$1 -o ./executions_bin/$1 > ./compilelogs/$1.txt 2>&1
d=$(cat ./compilelogs/$1.txt)
if test -n "$d"; then
    exit 1
else
    exit 0
fi
