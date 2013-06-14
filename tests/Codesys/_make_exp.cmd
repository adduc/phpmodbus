rem Create Codesys EXP file

rem Build cmd file
set CODESYS="c:\Program Files (x86)\WAGO Software\CoDeSys V2.3\codesys.exe"
set PROJECT=test
del %PROJECT%.EXP
echo file open %PROJECT%.pro >> codesys_cmd_file.cmd
echo project export %PROJECT%.EXP >> codesys_cmd_file.cmd
rem echo file saveas %PROJECT%.lib internallib >> codesys_cmd_file.cmd
echo file close >> codesys_cmd_file.cmd
echo file quit >> codesys_cmd_file.cmd
%CODESYS% /noinfo /cmd codesys_cmd_file.cmd 

rem Clean all when finished
del codesys_cmd_file.cmd