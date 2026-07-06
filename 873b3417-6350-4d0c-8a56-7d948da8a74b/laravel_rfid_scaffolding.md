# Laravel API Scaffolding for RFID Scans

Drop these into your existing Laravel project. Zero existing code is touched.

---

## 1. Migration

`database/migrations/xxxx_xx_xx_create_tag_reads_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_reads', function (Blueprint $table) {
            $table->id();
            $table->string('epc', 64)->index();
            $table->float('rssi');
            $table->unsignedTinyInteger('antenna')->default(0);
            $table->string('direction', 8)->default('0');
            $table->string('device_sn', 64)->nullable();
            $table->string('device_ip', 45)->nullable();
            // Timestamp from the device (ISO-8601 string from Python).
            // Stored separately from Laravel's created_at so you always have both.
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_reads');
    }
};
```

---

## 2. Model

`app/Models/TagRead.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagRead extends Model
{
    protected $fillable = [
        'epc',
        'rssi',
        'antenna',
        'direction',
        'device_sn',
        'device_ip',
        'scanned_at',
    ];

    protected $casts = [
        'rssi'       => 'float',
        'antenna'    => 'integer',
        'scanned_at' => 'datetime',
    ];
}
```

---

## 3. Controller

`app/Http/Controllers/RfidScanController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\TagRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RfidScanController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'epc'       => ['required', 'string', 'max:64'],
                'rssi'      => ['required', 'numeric'],
                'antenna'   => ['sometimes', 'integer'],
                'direction' => ['sometimes', 'string', 'max:8'],
                'device_sn' => ['sometimes', 'nullable', 'string', 'max:64'],
                'device_ip' => ['sometimes', 'nullable', 'string', 'max:45'],
                'timestamp' => ['sometimes', 'nullable', 'string'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error'   => 'Validation failed',
                'details' => $e->errors(),
            ], 422);
        }

        $tag = TagRead::create([
            'epc'        => $validated['epc'],
            'rssi'       => $validated['rssi'],
            'antenna'    => $validated['antenna'] ?? 0,
            'direction'  => $validated['direction'] ?? '0',
            'device_sn'  => $validated['device_sn'] ?? null,
            'device_ip'  => $validated['device_ip'] ?? null,
            // Cast the ISO-8601 string from Python — Carbon handles it natively.
            'scanned_at' => isset($validated['timestamp'])
                ? \Carbon\Carbon::parse($validated['timestamp'])->setTimezone(config('app.timezone'))
                : now(),
        ]);

        // TODO: broadcast(new TagReadCreated($tag))->toOthers();
        // Uncomment above once Reverb is configured (next step).

        return response()->json($tag, 201);
    }
}
```

---

## 4. Route

Add this line to `routes/api.php`:

```php
use App\Http\Controllers\RfidScanController;

Route::post('/rfid-scans', [RfidScanController::class, 'store']);
```

> [!IMPORTANT]
> Laravel 11 does not include `routes/api.php` by default. If it is missing, run:
> ```bash
> php artisan install:api
> ```
> That command creates the file and registers the `api` middleware group.

---

## 5. Run the migration

```bash
php artisan migrate
```

---

## 6. Verify with curl (before touching Python)

```bash
curl -X POST http://localhost:8000/api/rfid-scans \
  -H "Content-Type: application/json" \
  -d '{"epc":"E200123456789ABC","rssi":45.5,"antenna":1,"direction":"1","device_sn":"SN001","device_ip":"192.168.1.10","timestamp":"2026-07-06T14:00:00"}'
```

Expected response: `201 Created` with the saved row as JSON.

---

## Next: Real-time with Reverb + Echo (optional, additive)

Once the above is working, add this to the controller's `store()` method:

```php
use App\Events\TagReadCreated;

// After TagRead::create(...):
broadcast(new TagReadCreated($tag))->toOthers();
```

Create the event:
```bash
php artisan make:event TagReadCreated
```

```php
// app/Events/TagReadCreated.php
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TagReadCreated implements ShouldBroadcast
{
    public function __construct(public TagRead $tag) {}

    public function broadcastOn(): Channel
    {
        return new Channel('rfid-scans');
    }
}
```

On the Vue side (with `laravel-echo` + `pusher-js` installed):
```js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher
const echo = new Echo({ broadcaster: 'reverb', ... })

echo.channel('rfid-scans').listen('TagReadCreated', (e) => {
  // e.tag has all the fields — push to your reactive store
  tagStore.add(e.tag)
})
```

Install Reverb once and only once per project:
```bash
php artisan install:broadcasting
# Choose "reverb" when prompted
```
