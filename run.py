import subprocess
import json
from pathlib import Path
import webbrowser

BASE_PATH = Path(__file__).resolve().parent

JOB_FILES = BASE_PATH / 'running_commands.json'

jobs = [
    {'cwd': BASE_PATH / 'backend', 'cmd': 'php artisan serve --host=0.0.0.0 --port=8000'},
    {'cwd': BASE_PATH / 'backend', 'cmd': 'php artisan mqtt:listen'},
    {'cwd': BASE_PATH / 'backend', 'cmd': 'php artisan reverb:start'},
    {'cwd': BASE_PATH / 'backend', 'cmd': 'php artisan queue:work'},
    {'cwd': BASE_PATH / 'backend', 'cmd': 'npm run dev'},
]

running = []

for job in jobs:
    p = subprocess.Popen(
        ["powershell", "-NoExit", "-Command", job["cmd"]],
        cwd=str(job["cwd"]),
        creationflags=subprocess.CREATE_NEW_CONSOLE
    )

    running.append({
        "pid": p.pid,
        "cwd": str(job["cwd"]),
        "cmd": job["cmd"]
    })

with open(JOB_FILES, "w") as f:
    json.dump(running, f, indent=4)

import time
time.sleep(7)
webbrowser.open("http://127.0.0.1:8000/")

print('NICE')