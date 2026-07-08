# HOW TO RUN🏃‍♂️‍➡️🏃‍♀️‍➡️🏃‍➡️
<img width="498" height="333" alt="image" src="https://github.com/user-attachments/assets/f2cb4208-eba7-471b-8ccb-d6bc46589375" />

## Prerequisites

- **PHP**
- **Node.js**
- **Composer**
- **MQTT Broker (Mosquitto - https://mosquitto.org/download/)**
- **RFID Reader**

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
```bash
cd backend
```

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
```bash
php artisan reverb:start
```

### Terminal 4: Background Queue (Processes events)
```bash
php artisan queue:work
```

### Terminal 5: Frontend Build (Optional, for UI development)
```bash
npm run dev
```

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
