<?php

namespace App\Console\Commands;

use App\Events\TagScanned;
use App\Models\TagRead;
use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;

class ListenRfidMqtt extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Maminaw sa RFID tag reads gikan sa MQTT broker';

    public function handle()
    {
        $host = '127.0.0.1';
        $port = 1883;
        $topic = 'rfid/tagsfd71b6a';


        $client = new MqttClient($host, $port, 'rfid-listener');
        $client->connect();

        $this->info('Konected na sa MQTT broker.....');

        $client->subscribe($topic, function (string $topic, string $message) {
            $payload = json_decode($message, true);
            $tagList = $payload['data']['tagList'] ?? [];

            foreach ($tagList as $tag) {
                $epc = $tag['epc'] ?? null;
                if (!$epc) {
                    continue;
                }
                $read = TagRead::create([
                    'epc'        => $epc,
                    'ant'        => (int)($tag['ant'] ?? 0),
                    'gpi'        => (int)($tag['gpi'] ?? 0),
                    'rssi'       => (float)($tag['rssi'] ?? 0),
                    'times'      => (int)($tag['times'] ?? 1),
                    'pc'         => (string)($tag['pc'] ?? ''),
                    'first_time' => (string)($tag['firstTime'] ?? ''),
                    'sensor'     => (string)($tag['sensor'] ?? ''),]);
                    
                // push the data to the frontend immediately
                TagScanned::dispatch($read);

                $this->line("Tag saved: {$epc}");
            }

        }, 0);

        $client->loop(true);

    }
}
