import json
import logging
import time
from typing import Any

import paho.mqtt.client as mqtt

import config
import display
from parser import parse_message

logging.basicConfig(
    filename="monitor.log",
    level=logging.ERROR,
    format="%(asctime)s [%(levelname)s] %(message)s"
)


def on_connect(client: mqtt.Client, userdata: Any, flags: dict, rc: int) -> None:
    if rc == 0:
        client.subscribe(config.MQTT_TOPIC)
    else:
        logging.error(f"Failed to connect to MQTT broker. Return code: {rc}")


def on_message(client: mqtt.Client, userdata: Any, msg: mqtt.MQTTMessage) -> None:
    """Only mutate shared state here. Never touch Rich rendering from this thread."""
    try:
        payload_str = msg.payload.decode("utf-8")

        if "device_report_heartbeat_data" in payload_str:
            data = json.loads(payload_str)
            sn = data.get("sn", "unknown")
            ip = data.get("data", {}).get("ip", "unknown")
            ts = data.get("timestamp", "")
            display.record_heartbeat(sn, ip, ts)
            return

        parsed = parse_message(msg.topic, payload_str)
        if not parsed or not parsed.tags:
            return

        display.record_tags(parsed.tags)

    except Exception as e:
        logging.error(f"Error processing message: {e}", exc_info=True)


def main() -> None:
    client = mqtt.Client(client_id=config.MQTT_CLIENT_ID)
    client.on_connect = on_connect
    client.on_message = on_message

    try:
        client.connect(config.MQTT_BROKER, config.MQTT_PORT, 60)
    except Exception as e:
        print(f"Failed to connect to MQTT broker at {config.MQTT_BROKER}:{config.MQTT_PORT}: {e}")
        return

    client.loop_start()

    try:
        live = display.make_live()
        with live:
            while True:
                live.update(display.build_display())
                time.sleep(0.05)
    except KeyboardInterrupt:
        pass
    finally:
        client.loop_stop()
        client.disconnect()
        print("Monitor stopped.")


if __name__ == "__main__":
    main()

