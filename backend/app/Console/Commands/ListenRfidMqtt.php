<?php

namespace App\Console\Commands;

use App\Events\TagScanned;
use App\Models\TagRead;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use PhpMqtt\Client\MqttClient;

class ListenRfidMqtt extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Maminaw sa RFID tag reads gikan sa MQTT broker';

    public function handle()
    {
        $host = config('rfid.host');
        $port = (int) config('rfid.port');
        $topic = config('rfid.topic');
        $clientId = config('rfid.client_id', 'rfid-listener');

        $client = new MqttClient($host, $port, $clientId);
        $client->connect();

        $this->info('Konected na sa MQTT broker.....');

        $client->subscribe($topic, function (string $topic, string $message){
            $payload = json_decode($message, true);
            $tagList = $payload['data']['tagList'] ?? [];

            foreach ($tagList as $tag) {
                $epc = $tag['epc'] ?? null;
                if (!$epc) {
                    continue;
                }

                $read = TagRead::firstOrCreate(
                    ['epc' => $epc],
                    [
                        'ant'        => (int)($tag['ant'] ?? 0),
                        'rssi'       => (float)($tag['rssi'] ?? 0),
                        'first_time' => (string) Carbon::now()->getPreciseTimestamp(3),
                        'count'      => 1,
                    ]
                );

                if ($read->wasRecentlyCreated) {
                    $this->line("Tag saved: {$epc}");
                } else {
                    $read->count += 1;
                    $read->ant = (int)($tag['ant'] ?? $read->ant);
                    $read->rssi = ('float')($tag['rssi'] ?? $read->rssi);
                    $read->save();

                    $this->line("Tag count updated: {$epc} -> {$read->count}");
                }

                TagScanned::dispatch($read);
            }

        }, 0);

        $client->loop(true);
    }
}
