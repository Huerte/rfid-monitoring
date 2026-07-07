# HOW TO RUN 🏃‍♂️‍➡️

## RFID Reader Setup

Get you PC ip address
```bash
ipconfig
```

Open the RFID android application
[RFID APP PATH](RFID_Application/RFIDTool/RfidTool.exe)

What to do;
- open `System Settings`
- find `Output Mode`, then switch on the `HTTP`. Confirm afterward.
- go back ot `System Settings` then find `Configure Host`
- find `HTTP Url` configure it in using this template `http://<your-pc-ip-address>:8000/api/rfid-scans`
- confirm, then restart the rfid reader

## Web based Setup

### Go to backend dir
```bash
cd backend
```

### Then run this
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### View raw data output
```bash
http://127.0.0.1:8000/api/rfid-scans
```