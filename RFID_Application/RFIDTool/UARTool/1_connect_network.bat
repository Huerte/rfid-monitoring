
@echo *** Welcomm Using Android Reader Screen Tools ***

SET reader_ip=0.0.0.0
adb disconnect
adb disconnect
adb connect %reader_ip%
adb shell ifconfig eth0
screen.exe
pause
