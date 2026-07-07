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
- find `Output Mode`, then switch on the `MQTT`. Confirm afterward.
- go back ot `System Settings` then find `Configure Host`
- find `HTTP Url` configure it in using this template `tcp://<your-pc-ip-address>:1883`
- confirm, then restart the rfid reader

## Web based Setup

### Go to backend dir
```bash
cd backend
```

### Initial Setup
1. Install dependencies:
   ```bash
   composer install
   npm install
   ```
2. Copy environment settings:
   ```bash
   copy .env.example .env
   ```
3. Generate application key:
   ```bash
   php artisan key:generate
   ```
4. Run migrations:
   ```bash
   php artisan migrate
   ```

### Running the App (Open 4 terminals in `backend` dir)

**Terminal 1:** Run web server
```bash
php artisan serve
```

**Terminal 2:** Listen to RFID scans via MQTT
```bash
php artisan mqtt:listen
```

**Terminal 3:** Start WebSocket server
```bash
php artisan reverb:start
```

**Terminal 4:** Start a background queue worker
```bash
php artisan queue:work
```

**Terminal 5:** Compile frontend for real-time UI
```bash
npm run dev
```

### View live dashboard
```text
http://127.0.0.1:8000/live
```

### Screenshots
