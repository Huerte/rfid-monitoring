import json
import subprocess
from pathlib import Path

BASE_PATH = Path(__file__).resolve().parent
PID_FILE = BASE_PATH / "running_commands.json"

if not PID_FILE.exists():
    raise SystemExit

with PID_FILE.open("r", encoding="utf-8") as f:
    jobs = json.load(f)

for job in jobs:
    pid = job["pid"]
    cmd = job.get("cmd", "Unknown Command")

    try:
        subprocess.run(
            ["taskkill", "/PID", str(pid), "/F", "/T"],
            check=True,
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )

    except subprocess.CalledProcessError as e:
        print(e)

PID_FILE.unlink()

print('Goods na')