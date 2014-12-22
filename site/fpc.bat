fpc.exe -Mdelphi -FE.\executions_bin\ .\executions\%1 
SET TMP=%ERRORLEVEL%
del .\executions_bin\*.o
exit %TMP%
