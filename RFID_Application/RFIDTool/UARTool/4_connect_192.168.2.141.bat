
@echo *** Welcomm Using Android Reader Screen Tools ***

SET reader_ip=192.168.2.188
adb disconnect
adb disconnect
adb connect %reader_ip%

scrcpy.exe
pause