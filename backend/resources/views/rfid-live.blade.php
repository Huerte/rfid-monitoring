<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live RFID Scans</title>
    @vite(['resources/js/app.js'])
</head>
<body>
    <h1>Live Tag Scans</h1>
    <table border="1" cellpadding="5" cellspacing="0" id="tag-table">
        <thead>
            <tr>
                <th>Tag ID (EPC)</th>
                <th>Antenna Port</th>
                <th>Signal Strength (RSSI)</th>
                <th>First Detected</th>
                <th>Saved At</th>
            </tr>
        </thead>
        <tbody id="tag-body">
            @foreach($existing as $tag)
                <tr>
                    <td>{{ $tag->epc }}</td>
                    <td>{{ $tag->ant }}</td>
                    <td>{{ $tag->rssi }}</td>
                    <td>{{ $tag->first_time }}</td>
                    <td>{{ $tag->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

