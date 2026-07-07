@echo off
setlocal enabledelayedexpansion
for /f "tokens=* delims=" %%a in ('adb shell getprop ro.boot.serialno') do (
    set OUTPUT=%%a
)
echo !OUTPUT!

@echo off
setlocal

if not exist "factory" mkdir factory
echo ok

set DEVSN=!OUTPUT!

echo pls input(CODE):
set /p CODE=
echo your input code = : %CODE%

mkdir "factory\%DEVSN%"
echo %CODE% > "factory\%DEVSN%\edge.sys"

echo ok
pause



::echo pls input(DEVSN): 
::set /p DEVSN=
::echo your input sn = : %DEVSN%


