<?php

namespace App\Events;

use App\Models\TagRead;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TagScanned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TagRead $tagRead;

    public function __construct(TagRead $tagRead)
    {
        $this->tagRead = $tagRead;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('rfid-scans'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'tag.scanned';
    }

    public function broadcastWith(): array
    {
        return [
            'tagRead' => [
                'epc' => $this->tagRead->epc,
                'ant' => $this->tagRead->ant,
                'rssi' => $this->tagRead->rssi,
                'first_time_exact' => $this->tagRead->first_time,
                'created_at_time' => \Carbon\Carbon::parse($this->tagRead->created_at)->format('H:i:s'),
                'search_key' => strtolower($this->tagRead->epc . ' ' . $this->tagRead->ant . ' ' . $this->tagRead->rssi),
            ]
        ];
    }
}
