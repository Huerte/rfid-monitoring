@echo off
setlocal enabledelayedexpansion
for /f "tokens=* delims=" %%a in ('adb shell getprop ro.boot.serialno') do (
    set OUTPUT=%%a
)
echo !OUTPUT!

set DEVSN=!OUTPUT!

adb push factory/%DEVSN%/edge.sys sdcard/Android/
echo ok

pause