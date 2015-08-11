call "%VS100COMNTOOLS%vsvars32.bat"
cl.exe /MD /TP /Ox /Dsystem=do_not_use_system_pause .\executions\%1 /Fe.\executions_bin\ /Fo.\executions_bin\ > ./compilelogs/%1.txt 2>&1
SET TMP=%ERRORLEVEL%
del .\executions_bin\*.obj
exit %TMP%
