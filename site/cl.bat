call "%VS100COMNTOOLS%vsvars32.bat"
cl.exe /MD /TP /Ox .\executions\%1 /Fe.\executions_bin\ /Fo.\executions_bin\
SET TMP=%ERRORLEVEL%
del .\executions_bin\*.obj
exit %TMP%
