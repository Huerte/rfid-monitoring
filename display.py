from collections import deque
from datetime import datetime
from threading import Lock

from rich.console import Console
from rich.layout import Layout
from rich.live import Live
from rich.panel import Panel
from rich.table import Table
from rich.text import Text
from rich import box

import config
from parser import TagRead

console = Console()

_lock      = Lock()
_rows: deque[TagRead] = deque(maxlen=config.DISPLAY_ROWS)
_seen_epcs: set[str] = set()  # tracks first-seen EPCs; duplicates are dropped
_stats: dict = {
    "total_reads":  0,
    "unique_epcs":  set(),
    "last_heartbeat": "—",
    "device_sn":    "—",
    "device_ip":    "—",
    "started_at":   datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
}


def record_heartbeat(sn: str, ip: str, device_ts: str = "") -> None:
    with _lock:
        if device_ts:
            # Extract just HH:MM:SS — date portion is epoch but time is accurate
            _stats["last_heartbeat"] = device_ts.split(" ")[-1] if " " in device_ts else device_ts
        else:
            _stats["last_heartbeat"] = datetime.now().strftime("%H:%M:%S")
        _stats["device_sn"] = sn
        _stats["device_ip"] = ip


def record_tags(tags: list[TagRead]) -> None:
    with _lock:
        for tag in tags:
            if tag.epc in _seen_epcs:
                continue  # already recorded first detection, drop repeat
            _seen_epcs.add(tag.epc)
            _rows.append(tag)
            _stats["total_reads"] += 1
            _stats["unique_epcs"].add(tag.epc)
            _stats["device_sn"] = tag.device_sn
            _stats["device_ip"] = tag.device_ip


def _rssi_color(rssi: float) -> str:
    if rssi >= 50:
        return "green"
    if rssi >= 35:
        return "yellow"
    return "red"


def _build_header() -> Panel:
    with _lock:
        sn  = _stats["device_sn"]
        ip  = _stats["device_ip"]
        hb  = _stats["last_heartbeat"]
        tot = _stats["total_reads"]
        uniq = len(_stats["unique_epcs"])
        started = _stats["started_at"]

    grid = Table.grid(expand=True, padding=(0, 2))
    grid.add_column(justify="left")
    grid.add_column(justify="left")
    grid.add_column(justify="right")

    grid.add_row(
        f"[bold cyan]Device SN:[/]  [white]{sn}[/]",
        f"[bold cyan]IP:[/]  [white]{ip}[/]",
        f"[dim]Session started: {started}[/]",
    )
    grid.add_row(
        f"[bold cyan]Total Reads:[/] [white]{tot}[/]",
        f"[bold cyan]Unique Tags:[/] [white]{uniq}[/]",
        f"[dim]Last heartbeat: {hb}[/]",
    )
    return Panel(grid, title="[bold]RFID CLI Monitor[/]", border_style="cyan")


def _build_table() -> Table:
    table = Table(
        box=box.SIMPLE_HEAD,
        show_header=True,
        header_style="bold cyan",
        expand=True,
    )
    table.add_column("Time",       width=10)
    table.add_column("EPC",        min_width=24)
    table.add_column("RSSI (dBm)", width=12, justify="right")
    table.add_column("Antenna",    width=9,  justify="center")
    table.add_column("Direction",  width=10, justify="center")
    table.add_column("Device SN",  width=12)

    with _lock:
        rows = list(_rows)

    for tag in reversed(rows):
        rssi_color = _rssi_color(tag.rssi)
        table.add_row(
            tag.received_at.strftime("%H:%M:%S"),
            f"[bold white]{tag.epc}[/]",
            f"[{rssi_color}]{tag.rssi:.1f}[/]",
            str(tag.antenna),
            tag.direction,
            tag.device_sn,
        )

    return table


def build_display() -> Layout:
    layout = Layout()
    layout.split_column(
        Layout(_build_header(), name="header", size=5),
        Layout(Panel(_build_table(), border_style="dim"), name="body"),
    )
    return layout


def make_live() -> Live:
    return Live(
        build_display(),
        refresh_per_second=10,
        screen=True,
    )
