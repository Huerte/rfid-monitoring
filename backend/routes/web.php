<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $mockData = collect([
        (object) ['epc' => 'E200001B0514022416007421', 'ant' => 1, 'rssi' => -45, 'first_time' => now()->subMinutes(5)->format('Y-m-d H:i:s'),  'created_at' => now()->subMinutes(5)->format('Y-m-d H:i:s')],
        (object) ['epc' => 'E200001B0514022416007422', 'ant' => 2, 'rssi' => -50, 'first_time' => now()->subMinutes(10)->format('Y-m-d H:i:s'), 'created_at' => now()->subMinutes(10)->format('Y-m-d H:i:s')],
        (object) ['epc' => 'E200001B0514022416007423', 'ant' => 1, 'rssi' => -40, 'first_time' => now()->subMinutes(2)->format('Y-m-d H:i:s'),  'created_at' => now()->subMinutes(2)->format('Y-m-d H:i:s')],
        (object) ['epc' => 'E200001B0514022416007424', 'ant' => 3, 'rssi' => -60, 'first_time' => now()->subMinutes(1)->format('Y-m-d H:i:s'),  'created_at' => now()->subMinutes(1)->format('Y-m-d H:i:s')],
        (object) ['epc' => 'E200001B0514022416007425', 'ant' => 4, 'rssi' => -55, 'first_time' => now()->subSeconds(30)->format('Y-m-d H:i:s'), 'created_at' => now()->subSeconds(30)->format('Y-m-d H:i:s')],
        (object) ['epc' => 'E200001B0514022416007426', 'ant' => 2, 'rssi' => -42, 'first_time' => now()->subMinutes(8)->format('Y-m-d H:i:s'),  'created_at' => now()->subMinutes(8)->format('Y-m-d H:i:s')],
        (object) ['epc' => 'E200001B0514022416007427', 'ant' => 1, 'rssi' => -58, 'first_time' => now()->subMinutes(3)->format('Y-m-d H:i:s'),  'created_at' => now()->subMinutes(3)->format('Y-m-d H:i:s')],
        (object) ['epc' => 'E200001B0514022416007428', 'ant' => 3, 'rssi' => -48, 'first_time' => now()->subSeconds(45)->format('Y-m-d H:i:s'), 'created_at' => now()->subSeconds(45)->format('Y-m-d H:i:s')],
    ]);

    return view('rfid-live', ['existing' => $mockData]);
});
