call "%VS100COMNTOOLS%vsvars32.bat"
cl.exe  /TP /MD /I.\includes\ .\testers\%1 /Fo.\testers_bin\ /Fe.\testers_bin\ /link .\libs\Execution.lib
SET TMP=%ERRORLEVEL%
del .\testers_bin\*.obj
exit %TMP%
