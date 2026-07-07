
@echo *** Welcomm Using Android Reader Screen Tools ***

SET reader_ip=xxx.xxx.x.xxx
adb disconnect
adb disconnect
adb connect %reader_ip%

scrcpy.exe
pause