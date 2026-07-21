# HOW TO RUN🏃‍♂️‍➡️🏃‍♀️‍➡️🏃‍➡️
<p align="center">
  <img width="640" height="428" alt="michael-scott-wink" src="https://github.com/user-attachments/assets/ec82685f-27ae-4989-8d88-c5bc3ea3ffda" />
</p>

## Prerequisites

- PHP
- Node.js
- Composer
- MQTT Broker (Mosquitto - https://mosquitto.org/download/)
- RFID Reader

---

## Step 1: RFID Reader Setup

### Local Network (USB + MQTT)

1. Get your PC's IP address:
   ```bash
   ipconfig
   ```
   Look for "IPv4 Address"

2. Open the RFID Android application:
   - Open: [RfidTool](RFID_Application\RFIDTool\RfidTool.exe)

3. In the app, go to **System Settings**:
   - Find `Output Mode` -> toggle **MQTT** on
   - Find `Configure Host` -> set **MQTT URL** to:
     ```
     tcp://<YOUR_PC_IP>:1883
     ```
   - Example: `tcp://192.168.1.100:1883`

4. Restart the RFID reader

### WiFi Setup

To run everything wireless:

1. Ensure both your laptop and RFID reader are on the **same WiFi network**
2. Configure your Mosquitto broker to listen on all interfaces:
   - Open `C:\Program Files\mosquitto\mosquitto.conf`
   - Add these lines:
     ```
     listener 1883 0.0.0.0
     allow_anonymous true
     ```
   - Restart Mosquitto service
3. Open Windows Firewall and allow port 1883:
   ```powershell
   New-NetFirewallRule -DisplayName "MQTT 1883" -Direction Inbound -LocalPort 1883 -Protocol TCP -Action Allow
   ```
4. In RFID reader settings, set MQTT URL to your laptop IP (e.g., `tcp://<YOUR_PC_IP>:1883`)

---

## Step 2: Backend Setup

### Go to backend directory
=======
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
>>>>>>> 557081a (chore: no changes LOL)
```bash
cd backend
```

<<<<<<< HEAD
### Install dependencies
```bash
composer install
npm install
```

### Copy environment file
```bash
copy .env.example .env
```

### Generate application key
```bash
php artisan key:generate
```

### Setup database
```bash
php artisan migrate
```

---

## Step 3: Run the System

Open **4 separate terminals** in the `backend` directory and run these commands:

### Terminal 1: Web Server
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
**Output:** Server running at `http://127.0.0.1:8000`

### Terminal 2: MQTT Listener (Receives RFID data)
```bash
php artisan mqtt:listen
```
**Expected output:** `Konected na sa MQTT broker.....`

Make sure your MQTT broker is running. If you don't see this message, check:
- Mosquitto is running: `Services` -> search "Mosquitto Broker"
- RFID reader is configured with correct IP and port
- Firewall allows port 1883

### Terminal 3: WebSocket Server (Real-time updates)
=======
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
>>>>>>> 557081a (chore: no changes LOL)
```bash
php artisan reverb:start
```

<<<<<<< HEAD
### Terminal 4: Background Queue (Processes events)
=======
**Terminal 4:** Start a background queue worker
>>>>>>> 557081a (chore: no changes LOL)
```bash
php artisan queue:work
```

<<<<<<< HEAD
### Terminal 5: Frontend Build (Optional, for UI development)
=======
**Terminal 5:** Compile frontend for real-time UI
>>>>>>> 557081a (chore: no changes LOL)
```bash
npm run dev
```

<<<<<<< HEAD
---

## Step 4: View the API

Open your browser and go to:
```
http://127.0.0.1:8000/
```

You will see the **raw API JSON output** with auto-refresh every second.

**Raw API endpoint:**
```
http://127.0.0.1:8000/api/rfid-scans
```

---

## Troubleshooting

### "mqtt:listen is not responding"

**Check 1:** Is Mosquitto running?
```bash
tasklist | findstr mosquitto
```
If not found, start it:
```bash
Services -> Right-click "Mosquitto Broker" -> Start
```

**Check 2:** Can you reach the broker?
```bash
telnet 127.0.0.1 1883
```
If this fails, Mosquitto is not listening on that port.

**Check 3:** Is the RFID reader sending data?
- Check RFID reader logs or web interface
- Verify MQTT URL is set to `tcp://<YOUR_PC_IP>:1883`
- Restart the RFID reader after changing settings

**Check 4:** Is the topic correct?
The code listens to: `rfid/tagsfd71b6a`

If your reader publishes to a different topic, update it in:
```
backend/app/Console/Commands/ListenRfidMqtt.php (line 17)
```

### "No data appears in browser"

1. Verify Terminal 2 shows: `Konected na sa MQTT broker.....`
2. Scan an RFID tag near the reader
3. Check Terminal 2 for: `Tag saved: <EPC_VALUE>`
4. Refresh browser and check the JSON output

### "Connection refused" on port 1883

Your Mosquitto broker is not running or listening on the correct port.

**Windows users:**
```powershell
# Start Mosquitto
Start-Service "Mosquitto Broker"

# Verify it's running
Get-Service "Mosquitto Broker" | Select-Object Status
```

### WiFi connection issues

If using WiFi and the RFID reader can't reach the broker:

1. **Verify network:** Both devices on same WiFi:
   ```bash
   ipconfig
   ```

2. **Update mosquitto.conf:**
   ```
   listener 1883 0.0.0.0
   allow_anonymous true
   ```

3. **Restart Mosquitto** and allow firewall:
   ```powershell
   New-NetFirewallRule -DisplayName "MQTT 1883" -Direction Inbound -LocalPort 1883 -Protocol TCP -Action Allow
   ```

4. **Update RFID reader** to use your laptop's **WiFi IP** (not `127.0.0.1`)

---

## Environment Variables

Edit `backend/.env` to customize:

```env
MQTT_HOST=127.0.0.1      # Change if using remote broker
MQTT_PORT=1883           # Standard MQTT port
APP_DEBUG=true           # Set to false in production
DB_CONNECTION=sqlite     # Uses local SQLite database
```

---

## Command Reference

| Command | Purpose |
|---------|---------|
| `php artisan mqtt:listen` | Listen for RFID scans |
| `php artisan reverb:start` | Start WebSocket server |
| `php artisan queue:work` | Process background jobs |
| `php artisan serve` | Start web server |
| `npm run dev` | Build frontend with auto-reload |
| `php artisan migrate` | Run database migrations |

---

## Screenshots
<img width="1433" height="705" alt="image" src="https://github.com/user-attachments/assets/dedc303b-75ed-4252-bb05-8d8fac6e237f" />
<img width="1798" height="525" alt="image" src="https://github.com/user-attachments/assets/db8e6787-4e37-4366-b02b-44f4ea328591" />

## You crossed the finish line 🏁

<p align="center">
  <img width="498" height="331" alt="cheers-fireworks" src="https://github.com/user-attachments/assets/c42dc4b4-2325-4d7a-9f3e-37dffac74307" />
</p>
=======
### View live dashboard
```text
http://127.0.0.1:8000/live
```

### Screenshots
>>>>>>> 557081a (chore: no changes LOL)
