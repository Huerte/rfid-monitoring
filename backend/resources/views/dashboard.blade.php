<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Live Monitor | Marathon System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0B0D10;
            --surface:   #131720;
            --border:    #273140;
            --text:      #E9EEF5;
            --muted:     #5A6B82;
            --cyan:      #40E0FF;
            --green:     #00C48C;
            --red:       #FF4757;
            --warning:   #FFB300;
            --radius:    8px;
        }

        html, body {
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            line-height: 1.5;
        }

        /* ── Layout ── */
        .shell {
            display: grid;
            grid-template-rows: auto 1fr;
            min-height: 100vh;
            padding: 0 32px 32px;
            max-width: 1280px;
            margin: 0 auto;
        }

        /* ── Header ── */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 32px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #1a2940 0%, #0e1620 100%);
            border: 1px solid var(--cyan);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cyan);
        }

        .brand-icon svg { width: 18px; height: 18px; }

        h1 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .brand-sub {
            font-size: 0.75rem;
            color: var(--muted);
            font-weight: 400;
        }

        /* ── Status pill ── */
        .status-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--muted);
            transition: color 200ms ease-out, border-color 200ms ease-out;
        }

        .status-pill.live { color: var(--green); border-color: rgba(0, 196, 140, 0.3); }
        .status-pill.error { color: var(--red); border-color: rgba(255, 71, 87, 0.3); }

        .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        .status-pill.live .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* ── Stats bar ── */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 20px;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text);
            font-family: 'JetBrains Mono', monospace;
            line-height: 1.2;
        }

        .stat-value.cyan  { color: var(--cyan); }
        .stat-value.green { color: var(--green); }

        /* ── Toolbar ── */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .section-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: var(--radius);
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            transition: color 200ms ease-out, border-color 200ms ease-out, background 200ms ease-out;
            font-family: inherit;
        }

        .btn:hover { color: var(--text); border-color: #3d5166; background: #1a2232; }
        .btn.primary { background: rgba(64, 224, 255, 0.08); border-color: rgba(64, 224, 255, 0.3); color: var(--cyan); }
        .btn.primary:hover { background: rgba(64, 224, 255, 0.15); border-color: var(--cyan); }

        .last-updated {
            font-size: 0.75rem;
            color: var(--muted);
            font-family: 'JetBrains Mono', monospace;
        }

        /* ── Table ── */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--muted);
            background: rgba(0,0,0,0.25);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 150ms ease-out;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(64, 224, 255, 0.03); }

        tbody tr.new-row {
            animation: row-in 400ms ease-out;
        }

        @keyframes row-in {
            from { opacity: 0; background: rgba(64, 224, 255, 0.08); }
            to   { opacity: 1; background: transparent; }
        }

        td {
            padding: 12px 16px;
            font-size: 0.8125rem;
            color: var(--text);
            white-space: nowrap;
        }

        .td-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8125rem;
            color: var(--cyan);
            letter-spacing: 0.03em;
        }

        .td-muted { color: var(--muted); }

        /* RSSI badge */
        .rssi-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .rssi-badge.good    { background: rgba(0,196,140,0.12);  color: var(--green); }
        .rssi-badge.mid     { background: rgba(255,179,0,0.12);  color: var(--warning); }
        .rssi-badge.weak    { background: rgba(255,71,87,0.12);  color: var(--red); }

        /* ── Empty state ── */
        .empty-state {
            padding: 64px 24px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .empty-state svg { color: var(--muted); opacity: 0.4; }

        .empty-title {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--muted);
        }

        .empty-sub {
            font-size: 0.8125rem;
            color: #3a4d63;
        }

        /* ── Loading skeleton ── */
        .skeleton-row td {
            padding: 14px 16px;
        }

        .skeleton-cell {
            height: 14px;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--border) 25%, #2d3e52 50%, var(--border) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
        }

        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ── Footer ── */
        footer {
            margin-top: 24px;
            font-size: 0.75rem;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        @media (max-width: 768px) {
            .shell { padding: 0 16px 24px; }
            .stat-value { font-size: 1.5rem; }
            td, thead th { padding: 10px 12px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
<div class="shell">
    <header>
        <div class="brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                    <path d="M12 18h.01"/>
                    <path d="M8 6h8M8 10h8M8 14h4"/>
                </svg>
            </div>
            <div>
                <h1>RFID Live Monitor</h1>
                <div class="brand-sub">Marathon System &mdash; Tag Read Stream</div>
            </div>
        </div>
        <div id="status-pill" class="status-pill">
            <span class="pulse-dot"></span>
            <span id="status-text">Connecting...</span>
        </div>
    </header>

    <main>
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-label">Total Reads</div>
                <div class="stat-value cyan" id="stat-total">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Unique Tags</div>
                <div class="stat-value green" id="stat-unique">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Avg RSSI</div>
                <div class="stat-value" id="stat-rssi">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Poll Interval</div>
                <div class="stat-value" style="font-size:1.25rem; color: var(--muted)">3s</div>
            </div>
        </div>

        <div class="toolbar">
            <span class="section-title">Recent Tag Reads</span>
            <div class="toolbar-right">
                <span class="last-updated" id="last-updated">Never</span>
                <button class="btn primary" id="btn-pause" onclick="togglePause()">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                    Pause
                </button>
                <button class="btn" onclick="fetchNow()">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16"/></svg>
                    Refresh
                </button>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>EPC Tag</th>
                        <th>RSSI</th>
                        <th>Antenna</th>
                        <th>Times</th>
                        <th>PC</th>
                        <th>First Seen</th>
                        <th>Recorded At</th>
                    </tr>
                </thead>
                <tbody id="tag-tbody">
                    <!-- skeleton -->
                    <tr class="skeleton-row"><td><div class="skeleton-cell" style="width:24px"></div></td><td><div class="skeleton-cell" style="width:160px"></div></td><td><div class="skeleton-cell" style="width:60px"></div></td><td><div class="skeleton-cell" style="width:40px"></div></td><td><div class="skeleton-cell" style="width:40px"></div></td><td><div class="skeleton-cell" style="width:50px"></div></td><td><div class="skeleton-cell" style="width:140px"></div></td><td><div class="skeleton-cell" style="width:160px"></div></td></tr>
                    <tr class="skeleton-row"><td><div class="skeleton-cell" style="width:24px"></div></td><td><div class="skeleton-cell" style="width:140px"></div></td><td><div class="skeleton-cell" style="width:60px"></div></td><td><div class="skeleton-cell" style="width:40px"></div></td><td><div class="skeleton-cell" style="width:40px"></div></td><td><div class="skeleton-cell" style="width:50px"></div></td><td><div class="skeleton-cell" style="width:140px"></div></td><td><div class="skeleton-cell" style="width:160px"></div></td></tr>
                    <tr class="skeleton-row"><td><div class="skeleton-cell" style="width:24px"></div></td><td><div class="skeleton-cell" style="width:120px"></div></td><td><div class="skeleton-cell" style="width:60px"></div></td><td><div class="skeleton-cell" style="width:40px"></div></td><td><div class="skeleton-cell" style="width:40px"></div></td><td><div class="skeleton-cell" style="width:50px"></div></td><td><div class="skeleton-cell" style="width:140px"></div></td><td><div class="skeleton-cell" style="width:160px"></div></td></tr>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <span>Polling <code style="background:var(--surface);padding:2px 6px;border-radius:4px;font-size:0.7rem">GET /api/rfid-scans</code> every 3 seconds</span>
        <span id="footer-count" style="color: var(--muted)"></span>
    </footer>
</div>

<script>
    const POLL_MS   = 3000;
    const API_URL   = '/api/rfid-scans';

    let paused      = false;
    let pollTimer   = null;
    let knownIds    = new Set();
    let lastData    = [];

    // ── DOM refs ──
    const tbody        = document.getElementById('tag-tbody');
    const statusPill   = document.getElementById('status-pill');
    const statusText   = document.getElementById('status-text');
    const lastUpdated  = document.getElementById('last-updated');
    const footerCount  = document.getElementById('footer-count');
    const statTotal    = document.getElementById('stat-total');
    const statUnique   = document.getElementById('stat-unique');
    const statRssi     = document.getElementById('stat-rssi');
    const btnPause     = document.getElementById('btn-pause');

    function setStatus(state) {
        statusPill.className = 'status-pill ' + state;
        if (state === 'live')  statusText.textContent = 'Live';
        if (state === 'error') statusText.textContent = 'Error';
        if (state === '')      statusText.textContent = 'Connecting...';
    }

    function rssiClass(v) {
        if (v <= 0) return ''; // negative dBm — not applicable here
        if (v >= 60) return 'good';
        if (v >= 40) return 'mid';
        return 'weak';
    }

    function fmtDate(str) {
        if (!str) return '—';
        const d = new Date(str);
        if (isNaN(d)) return str;
        return d.toLocaleString('en-PH', { hour12: false, year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }

    function renderRows(data) {
        if (!data.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="5" y="2" width="14" height="20" rx="2"/>
                                <path d="M12 18h.01"/>
                            </svg>
                            <div class="empty-title">No tag reads yet</div>
                            <div class="empty-sub">Waiting for the RFID reader to POST data to /api/rfid-scans</div>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        const newIds = new Set(data.map(r => r.id));

        tbody.innerHTML = data.map((row, i) => {
            const isNew = !knownIds.has(row.id);
            const rc    = rssiClass(row.rssi);
            return `<tr class="${isNew ? 'new-row' : ''}">
                <td class="td-muted">${i + 1}</td>
                <td><span class="td-mono">${row.epc ?? '—'}</span></td>
                <td>
                    <span class="rssi-badge ${rc}">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z"/></svg>
                        ${row.rssi ?? '—'}
                    </span>
                </td>
                <td class="td-muted">ANT ${row.ant ?? '—'}</td>
                <td class="td-muted">${row.times ?? 1}</td>
                <td><span class="td-mono" style="color:var(--muted);font-size:0.75rem">${row.pc ?? '—'}</span></td>
                <td class="td-muted">${row.first_time ?? '—'}</td>
                <td class="td-muted">${fmtDate(row.created_at)}</td>
            </tr>`;
        }).join('');

        knownIds = newIds;
    }

    function updateStats(data) {
        statTotal.textContent  = data.length;
        const unique = new Set(data.map(r => r.epc)).size;
        statUnique.textContent = unique;

        const validRssi = data.map(r => parseFloat(r.rssi)).filter(v => !isNaN(v));
        if (validRssi.length) {
            const avg = (validRssi.reduce((a, b) => a + b, 0) / validRssi.length).toFixed(1);
            statRssi.textContent = avg;
        } else {
            statRssi.textContent = '—';
        }
    }

    async function fetchNow() {
        try {
            const res  = await fetch(API_URL, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();

            // Normalise — handle both raw array and { data: [...] }
            const rows = Array.isArray(data) ? data : (data.data ?? []);

            renderRows(rows);
            updateStats(rows);
            setStatus('live');

            const now = new Date().toLocaleTimeString('en-PH', { hour12: false });
            lastUpdated.textContent = `Updated ${now}`;
            footerCount.textContent = `${rows.length} records shown`;
            lastData = rows;
        } catch (err) {
            setStatus('error');
            statusText.textContent = `Error: ${err.message}`;
        }
    }

    function schedulePoll() {
        clearTimeout(pollTimer);
        if (!paused) pollTimer = setTimeout(async () => { await fetchNow(); schedulePoll(); }, POLL_MS);
    }

    function togglePause() {
        paused = !paused;
        if (paused) {
            btnPause.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg> Resume`;
            btnPause.className = 'btn';
            clearTimeout(pollTimer);
            statusPill.className = 'status-pill';
            statusText.textContent = 'Paused';
        } else {
            btnPause.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg> Pause`;
            btnPause.className = 'btn primary';
            fetchNow().then(schedulePoll);
        }
    }

    // Boot
    fetchNow().then(schedulePoll);
</script>
</body>
</html>
