#!/bin/bash
g++ -x c++ -L ./libs -I ./includes ./testers/$1 -o ./testers_bin/$1 -lexecution
