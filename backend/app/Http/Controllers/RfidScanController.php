<?php

namespace App\Http\Controllers;

use App\Models\TagRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RfidScanController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $tagList = $request->input('data.tagList', []);
        $deviceSn = $request->input('sn');

        $saved = 0;
        foreach ($tagList as $tag) {
            $epc = $tag['epc'] ?? null;
            if (!$epc) {
                continue;
            }

            TagRead::create([
                'epc' => $epc,
                'ant' => (int)($tag['ant'] ?? 0),
                'gpi' => (int)($tag['gpi'] ?? 0),
                'rssi' => (float)($tag['rssi'] ?? 0),
                'times' => (int)($tag['times'] ?? 1),
                'pc' => (string)($tag['pc'] ?? ''),
                'first_time' => (string)($tag['firstTime'] ?? ''),
                'sensor' => (string)($tag['sensor'] ?? ''),
            ]);

            $saved++;
        }

        Log::info('RFID scan received', [
            'device' => $deviceSn,
            'tag_count' => $saved,
            'tags' => array_column($tagList, 'epc'),
        ]);

        return response()->json([
            'status' => ['code' => 200, 'text' => 'Request successful!'],
        ]);
    }

    public function index(): JsonResponse
    {
        $reads = TagRead::latest()->take(10)->get();

        return response()->json($reads);
    }
}
