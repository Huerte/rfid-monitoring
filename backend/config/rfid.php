<?php

return [
    'host' => env('RFID_HOST', '127.0.0.1'),
    'port' => env('RFID_PORT', 1883),
    'topic' => env('RFID_TOPIC', 'rfid/tagsfd71b6a'),
    'client_id' => env('RFID_CLIENT_ID', 'rfid-listener'),
    'cooldown_seconds' => env('RFID_COOLDOWN_SECONDS', 10),
];