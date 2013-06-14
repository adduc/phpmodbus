@echo off
call ../config.bat

for %%f in (test.*.php) do %php% -q "%%f" > "output/%%f.html"

cd output
for %%f in (*.html) do %diff% "%%f" ../ref/"%%f"
cd ..
pause

@echo on