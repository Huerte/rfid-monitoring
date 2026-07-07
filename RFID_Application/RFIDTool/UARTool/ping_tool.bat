@echo.
@echo.

@echo off
echo ******************************* 欢迎使用甘霖PING工具 *******************************
@echo.
echo 执行中，请稍后...
echo ping日期：%date% > ping_log.txt
echo ping时间：%time% >> ping_log.txt
echo.>>ping_log.txt
echo 具体数据：>>ping_log.txt
@echo on

@echo -------------------------------------------------------
for /L %%i in (1,1,250) do ping -n 1 -w 60 192.168.0.%%i | find "回复" >> ping_log.txt
@echo -------------------------------------------------------

@echo off
echo ------------------------------------------------------- >> ping_log.txt
echo.
echo 执行结束，请双击打开ping_log.txt查看。
echo.
echo.
echo ******************************* 欢迎使用甘霖PING工具 *******************************
@echo on


pause
pause