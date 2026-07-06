<?php

namespace App\Console\Commands;

use App\Models\TagRead;
use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class ListenRfidStream extends Command
{
    protected $signature = 'rfid:listen {--host=127.0.0.1} {--port=1883} {--topic=rfid/tags}';

    protected $description = 'Listen to the MQTT stream for RFID reads and save them to the database';

    public function handle()
    {
        $server = $this->option('host');
        $port = (int) $this->option('port');
        $topic = $this->option('topic');
        $clientId = 'laravel_rfid_listener_' . uniqid();

        $this->info("Connecting to MQTT Broker at {$server}:{$port}...");

        try {
            $mqtt = new MqttClient($server, $port, $clientId);

            $settings = (new ConnectionSettings())
                ->setKeepAliveInterval(60)
                ->setConnectTimeout(10)
                ->setUseTls(false);

            $mqtt->connect($settings, useCleanSession: true);

            $this->info("Connected successfully! Listening on topic '{$topic}'...");

            $mqtt->subscribe($topic, function (string $topic, string $message) {
                $this->info("Received raw payload on [$topic]: " . $message);
                $this->processMessage($message);
            }, 0);

            $mqtt->loop(true);
            $mqtt->disconnect();

        } catch (\Exception $e) {
            $this->error("MQTT Connection Error: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function processMessage(string $message)
    {
        $payload = json_decode($message, true);

        if (!$payload) {
            $this->error("Invalid JSON payload.");
            return;
        }

        $tagList = [];
        
        if (isset($payload['data']['tagList']) && is_array($payload['data']['tagList'])) {
            $tagList = $payload['data']['tagList'];
        } elseif (isset($payload['tagList']) && is_array($payload['tagList'])) {
            $tagList = $payload['tagList'];
        } elseif (isset($payload['epc'])) {
            $tagList = [$payload];
        } else {
            $this->warn("No valid tags found in payload.");
            return;
        }

        $saved = 0;

        foreach ($tagList as $tag) {
            if (empty($tag['epc'])) continue;

            TagRead::create([
                'epc'        => $tag['epc'],
                'ant'        => (int)   ($tag['ant']       ?? 0),
                'gpi'        => (int)   ($tag['gpi']       ?? 0),
                'rssi'       => (float) ($tag['rssi']      ?? 0),
                'times'      => (int)   ($tag['times']     ?? 1),
                'pc'         => (string)($tag['pc']        ?? ''),
                'first_time' => (string)($tag['firstTime'] ?? ''),
                'sensor'     => (string)($tag['sensor']    ?? ''),
            ]);
            $saved++;
        }

        $this->info("Saved {$saved} tag(s) to the database.");
    }
}
