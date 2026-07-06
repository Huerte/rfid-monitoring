<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Raw API</title>
    <style>
        :root {
            color-scheme: dark;
            background: #0b0d10;
            color: #e9eef5;
            font-family: Inter, system-ui, sans-serif;
            line-height: 1.5;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
            background: radial-gradient(circle at top, rgba(64, 224, 255, 0.12), transparent 36%),
                        linear-gradient(180deg, #0b0d10 0%, #07090c 100%);
        }

        .page {
            width: min(100%, 980px);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 20px;
            background: rgba(10, 12, 16, .88);
            box-shadow: 0 24px 80px rgba(0, 0, 0, .35);
            overflow: hidden;
        }

        header {
            padding: 24px 28px;
            background: rgba(255, 255, 255, .04);
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        h1 {
            margin: 0;
            font-size: 1.35rem;
            letter-spacing: -0.02em;
        }

        p {
            margin: 8px 0 0;
            color: #b7c4d1;
            font-size: .95rem;
        }

        .body {
            padding: 24px 28px;
        }

        .meta {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            margin-bottom: 18px;
            align-items: center;
        }

        .meta .badge {
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(64, 224, 255, .25);
            background: rgba(64, 224, 255, .08);
            color: #8ff3ff;
            font-size: .85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .meta .url {
            color: #cfd8e6;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
            font-size: .95rem;
            overflow-wrap: anywhere;
        }

        .status {
            color: #a7b8c9;
            font-size: .88rem;
            margin-bottom: 18px;
        }

        pre {
            margin: 0;
            min-height: 320px;
            padding: 18px;
            border-radius: 18px;
            background: rgba(14, 17, 22, .96);
            color: #c8e7ff;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
            font-size: .9rem;
            line-height: 1.65;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
            border: 1px solid rgba(255, 255, 255, .06);
        }

        .footer {
            margin-top: 20px;
            color: #7b8a99;
            font-size: .85rem;
        }
    </style>
</head>
<body>
    <div class="page">
        <header>
            <h1>RFID Raw API</h1>
            <p>Fetching raw JSON from <code>/api/rfid-scans</code> automatically every second.</p>
        </header>

        <section class="body">
            <div class="meta">
                <div class="url"><strong>API:</strong> /api/rfid-scans</div>
                <div class="badge">Auto refresh</div>
            </div>
            <div class="status" id="status">Loading latest scan data…</div>
            <pre id="output">Waiting for data...</pre>
            <div class="footer">Last updated: <span id="updated">—</span></div>
        </section>
    </div>

    <script>
        const output = document.getElementById('output');
        const status = document.getElementById('status');
        const updated = document.getElementById('updated');

        async function refreshData() {
            try {
                const response = await fetch('/api/rfid-scans', { cache: 'no-store' });
                if (!response.ok) {
                    throw new Error(response.status + ' ' + response.statusText);
                }

                const data = await response.json();
                const formatted = JSON.stringify(data, null, 2);
                output.textContent = formatted;
                status.textContent = 'Latest raw API result loaded successfully.';
                updated.textContent = new Date().toLocaleTimeString('en-US', { hour12: false });
            } catch (error) {
                output.textContent = String(error);
                status.textContent = 'Unable to fetch data. Retrying…';
                updated.textContent = new Date().toLocaleTimeString('en-US', { hour12: false });
            }
        }

        refreshData();
        setInterval(refreshData, 1000);
    </script>
</body>
</html>
