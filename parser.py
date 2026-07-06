import json
from dataclasses import dataclass, field
from datetime import datetime
from typing import Optional


_DEVICE_TS_FMT = "%Y-%m-%d %H:%M:%S"


def _parse_device_time(raw_ts: str) -> datetime:
    # Date portion is wrong (epoch) but HH:MM:SS is accurate.
    # Falls back to PC time only if the field is missing or malformed.
    try:
        return datetime.strptime(raw_ts.strip(), _DEVICE_TS_FMT)
    except (ValueError, AttributeError):
        return datetime.now()


@dataclass
class TagRead:
    epc: str
    rssi: float
    antenna: int
    direction: str
    first_time: int
    last_time: int
    times: int
    device_sn: str
    device_ip: str
    received_at: datetime = field(default_factory=datetime.now)


@dataclass
class ProbePayload:
    device_sn: str
    device_ip: str
    tags: list[TagRead]
    raw_timestamp: str


def parse_message(topic: str, raw: str) -> Optional[ProbePayload]:
    try:
        payload = json.loads(raw)
    except json.JSONDecodeError:
        return None

    if payload.get("method") != "device_report_probe_data":
        return None

    data = payload.get("data", {})
    tag_list = data.get("tagList", [])

    if not tag_list:
        return None

    sn = payload.get("sn", "unknown")
    ip = data.get("ip", "unknown")
    # Device RTC keeps accurate HH:MM:SS but date is stuck at epoch — that's fine
    # since we only display the time portion. Fall back to PC clock if missing.
    received_at = _parse_device_time(payload.get("timestamp", ""))

    tags = [
        TagRead(
            epc        = t.get("epc", ""),
            rssi       = float(t.get("rssi", 0.0)),
            antenna    = int(t.get("ant", 0)),
            direction  = str(t.get("direction", "0")),
            first_time = int(t.get("firstTime", 0)),
            last_time  = int(t.get("lastTime", 0)),
            times      = int(t.get("times", 0)),
            device_sn  = sn,
            device_ip  = ip,
            received_at= received_at,
        )
        for t in tag_list
        if t.get("epc")
    ]

    return ProbePayload(
        device_sn     = sn,
        device_ip     = ip,
        tags          = tags,
        raw_timestamp = payload.get("timestamp", ""),
    )
