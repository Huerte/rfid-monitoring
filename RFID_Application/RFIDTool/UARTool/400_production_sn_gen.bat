@echo off
setlocal enabledelayedexpansion
for /f "tokens=* delims=" %%a in ('adb shell getprop ro.boot.serialno') do (
    set OUTPUT=%%a
)
echo !OUTPUT!

@echo off
setlocal

set DEVSN=!OUTPUT!

setlocal

if not exist "produced" mkdir produced

set DEVSN=!OUTPUT!

mkdir "produced\%DEVSN%"

echo ok

pause