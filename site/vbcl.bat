call "%VS100COMNTOOLS%vsvars32.bat"
vbc.exe /imports:Microsoft.VisualBasic,System /out:.\executions_bin\%1.exe .\executions\%1