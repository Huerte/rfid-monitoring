
@echo *** Welcomm Using Android Reader Screen Tools ***

adb install -t -r reader-update.apk
adb shell am start -W -n com.realopeniot.goods_inventory_pda/com.realopeniot.goods_inventory_pda.activity.LoginActivity
pause

