<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TagRead extends Model
{
    protected $fillable = [
        'epc',
        'ant',
        'gpi',
        'rssi',
        'times',
        'pc',
        'first_time',
        'sensor',
    ];

    protected $casts = [
        'rssi' => 'float',
        'ant'  => 'integer',
        'gpi'  => 'integer',
        'times' => 'integer',
    ];

    // converts the millisecond timestamp from the reader to a readable datetime
    public function getFirstTimeAttribute(string $value): string
    {
        if (!$value) {
            return '—';
        }

        return Carbon::createFromTimestampMs((int) $value)
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d H:i:s');
    }
}
