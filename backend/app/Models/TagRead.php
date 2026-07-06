<?php

namespace App\Models;

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
        'rssi'  => 'float',
        'ant'   => 'integer',
        'gpi'   => 'integer',
        'times' => 'integer',
    ];
}
