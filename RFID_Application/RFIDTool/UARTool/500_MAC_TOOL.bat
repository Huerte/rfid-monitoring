adb root
adb remount rw
adb push ganlin.sh /etc/
adb shell chmod 0777 /etc/ganlin.sh
pause