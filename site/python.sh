#!/bin/bash
echo "#!/usr/bin/python3" > executions_bin/$1
cat executions/$1$EXTENSION >> executions_bin/$1
chmod a+x executions_bin/$1
