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
}
