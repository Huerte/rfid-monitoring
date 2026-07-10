<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Tag Reader</title>
    @vite(['resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f5f5f5;
            font-size: 13px;
            color: #1a1a1a;
        }

        .app {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
        }

        /* ══ Title bar ══ */
        .titlebar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 14px;
            height: 38px;
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            flex-shrink: 0;
        }

        .titlebar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .titlebar-title {
            font-size: 13px;
            font-weight: 600;
            color: #111;
        }

        .tag-count-badge {
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 2px 9px;
            font-size: 11px;
            color: #555;
            font-weight: 500;
        }

        /* ══ Toolbar ══ */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            background: #fafafa;
            border-bottom: 1px solid #e5e5e5;
            flex-shrink: 0;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            max-width: 320px;
        }

        .search-wrap svg {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            pointer-events: none;
        }

        .search-wrap input {
            width: 100%;
            height: 26px;
            padding: 0 8px 0 28px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            font-size: 12px;
            font-family: inherit;
            background: #fff;
            outline: none;
            color: #111;
        }
        .search-wrap input:focus {
            border-color: #4a90d8;
            box-shadow: 0 0 0 2px rgba(74,144,216,.15);
        }

        .toolbar-sep { width: 1px; height: 18px; background: #ddd; margin: 0 4px; }

        .toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            height: 26px;
            font-size: 12px;
            font-family: inherit;
            background: #fff;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            cursor: pointer;
            color: #333;
            white-space: nowrap;
        }
        .toolbar-btn:hover { background: #eef4ff; border-color: #4a90d8; color: #1a5fb8; }

        .record-count {
            margin-left: auto;
            font-size: 11px;
            color: #888;
            white-space: nowrap;
        }
        .record-count b { color: #333; }

        /* ══ Grid ══ */
        .grid-wrap {
            flex: 1;
            overflow: auto;
            background: #fff;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        col.c-num   { width: 46px; }
        col.c-epc   {width: 350px; }
        col.c-first { }
        col.c-ant   { width: 120px; }
        col.c-rssi  { width: 120px; }
        col.c-saved { width: 300px; }

        thead { position: sticky; top: 0; z-index: 10; }

        thead th {
            background: #f7f7f7;
            border-bottom: 2px solid #e0e0e0;
            border-right: 1px solid #e8e8e8;
            padding: 7px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #444;
            text-align: left;
            white-space: nowrap;
            user-select: none;
            cursor: pointer;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        thead th:last-child { border-right: none; }
        thead th:hover { background: #eef4ff; color: #1a5fb8; }
        thead th.c-num {
            text-align: center;
            background: #f0f0f0;
            cursor: default;
            color: #888;
        }
        thead th.c-num:hover { background: #f0f0f0; color: #888; }

        .sort-icon { font-size: 9px; color: #ccc; margin-left: 4px; vertical-align: middle; }
        thead th.asc  .sort-icon,
        thead th.desc .sort-icon { color: #4a90d8; }

        /* ══ Rows ══ */
        tbody tr { cursor: pointer; transition: background .08s; }

        tbody tr:nth-child(even) td { background: #fafafa; }
        tbody tr:nth-child(odd)  td { background: #ffffff; }

        tbody tr:hover td { background: #eef4ff !important; }

        tbody tr.selected td {
            background: #1a6fc4 !important;
            color: #fff !important;
            border-color: transparent !important;
        }

        tbody td {
            border-bottom: 1px solid #efefef;
            border-right: 1px solid #f3f3f3;
            padding: 6px 10px;
            font-size: 12px;
            color: #222;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        tbody td:last-child { border-right: none; }

        td.c-num {
            text-align: center;
            color: #bbb;
            background: #f8f8f8 !important;
            border-right: 1px solid #e8e8e8;
            font-size: 11px;
        }
        tbody tr.selected td.c-num { background: #145ea8 !important; color: rgba(255,255,255,.6) !important; }

        td.center { text-align: center; }
        td.right  { text-align: right; }

        /* ══ Status bar ══ */
        .statusbar {
            display: flex;
            align-items: center;
            gap: 12px;
            height: 24px;
            padding: 0 14px;
            background: #f0f0f0;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
            flex-shrink: 0;
        }
        .statusbar .sep { color: #ccc; }
        .statusbar .hint { margin-left: auto; color: #bbb; }
        .statusbar #selected-info b { color: #1a6fc4; }

        /* ══ Toast ══ */
        #toast {
            position: fixed;
            bottom: 34px;
            left: 50%;
            transform: translateX(-50%) translateY(6px);
            background: #1a1a1a;
            color: #fff;
            font-size: 11px;
            padding: 5px 16px;
            border-radius: 5px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s, transform .18s;
            z-index: 999;
            white-space: nowrap;
        }
        #toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

        /* ══ Empty state ══ */
        .empty-state td {
            text-align: center;
            color: #bbb !important;
            font-style: italic;
            padding: 40px 0 !important;
            background: #fff !important;
            font-size: 12px;
        }

        tr.hidden { display: none; }
    </style>
</head>
<body>

@php $total = $existing->count(); @endphp

<div class="app">

    <!-- Title bar -->
    <div class="titlebar">
        <div class="titlebar-left">
            <span class="titlebar-title">RFID Tag Reader</span>
            <span class="tag-count-badge">{{ $total }} tags</span>
        </div>
    </div>

    <!-- Toolbar / search -->
    <div class="toolbar">
        <div class="search-wrap">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="9" r="7"/><line x1="14.65" y1="14.65" x2="19" y2="19"/>
            </svg>
            <input id="filterInput" type="text" placeholder="Search EPC, antenna, RSSI…" oninput="filterTable(this.value)">
        </div>

        <div class="toolbar-sep"></div>

        <button class="toolbar-btn" onclick="exportCSV()">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 16h12M10 4v9M6 9l4 4 4-4"/>
            </svg>
            Export CSV
        </button>

        <button class="toolbar-btn" onclick="location.reload()">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 12a8 8 0 1 0 1.5-4.8"/><polyline points="1 6 4 12 10 9"/>
            </svg>
            Refresh
        </button>

        <span class="record-count">
            Showing <b id="visibleCount">{{ $total }}</b> of {{ $total }}
        </span>
    </div>

    <!-- Grid -->
    <div class="grid-wrap">
        <table id="tag-table">
            <colgroup>
                <col class="c-num">
                <col class="c-epc">
                <col class="c-first">
                <col class="c-ant">
                <col class="c-rssi">
                <col class="c-saved">
            </colgroup>
            <thead>
                <tr>
                    <th class="c-num">#</th>
                    <th class="sortable" onclick="sortTable(1, this)">Tag ID (EPC)</th>
                    <th class="sortable" onclick="sortTable(4, this)">First Detected</th>
                    <th class="sortable center" onclick="sortTable(2, this)">Antenna</th>
                    <th class="sortable right" onclick="sortTable(3, this)">Signal (RSSI)</th>
                    <th class="sortable" onclick="sortTable(5, this)">Saved At</th>
                </tr>
            </thead>
            <tbody id="tag-body">
                @forelse($existing as $i => $tag)
                    @php
                        $searchKey = strtolower($tag->epc . ' ' . $tag->ant . ' ' . $tag->rssi);
                    @endphp
                    <tr class="{{ $i === 0 ? 'selected' : '' }}"
                        data-search="{{ $searchKey }}"
                        onclick="selectRow(this)"
                        ondblclick="copyEpc(this)"
                        title="Double-click to copy EPC">
                        <td class="c-num">{{ $i + 1 }}</td>
                        <td>{{ $tag->epc }}</td>
                        <td>{{ $tag->first_time }}</td>
                        <td class="center">{{ $tag->ant }}</td>
                        <td class="right">{{ $tag->rssi }} dBm</td>
                        <td>{{ \Carbon\Carbon::parse($tag->created_at)->format('H:i:s') }}</td>
                    </tr>
                @empty
                    <tr class="empty-state">
                        <td colspan="6">No tag records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Status bar -->
    <div class="statusbar">
        <span>{{ $total }} records</span>
        <span class="sep">|</span>
        <span id="selected-info">No row selected</span>
        <span class="hint">Double-click to copy EPC</span>
    </div>

    <div id="toast"></div>
</div>

<script>
    const tbody = document.getElementById('tag-body');

    /* ── Select ── */
    function selectRow(row) {
        if (row.classList.contains('empty-state')) return;
        tbody.querySelectorAll('tr.selected').forEach(r => r.classList.remove('selected'));
        row.classList.add('selected');
        document.getElementById('selected-info').innerHTML =
            'Selected: <b>' + row.cells[1].textContent.trim() + '</b>';
        row.scrollIntoView({ block: 'nearest' });
    }

    /* ── Copy EPC ── */
    function copyEpc(row) {
        if (row.classList.contains('empty-state')) return;
        const epc = row.cells[1].textContent.trim();
        navigator.clipboard.writeText(epc).then(() => toast('✓ Copied: ' + epc));
    }

    /* ── Toast ── */
    let toastT;
    function toast(msg) {
        const el = document.getElementById('toast');
        el.textContent = msg;
        el.classList.add('show');
        clearTimeout(toastT);
        toastT = setTimeout(() => el.classList.remove('show'), 2200);
    }

    /* ── Filter ── */
    function filterTable(q) {
        q = q.toLowerCase().trim();
        let vis = 0;
        tbody.querySelectorAll('tr:not(.empty-state)').forEach(r => {
            const match = !q || r.dataset.search.includes(q);
            r.classList.toggle('hidden', !match);
            if (match) vis++;
        });
        document.getElementById('visibleCount').textContent = vis;
    }

    /* ── Sort ── */
    let sortState = { col: -1, dir: 'asc' };
    function sortTable(col, th) {
        const dir = (sortState.col === col && sortState.dir === 'asc') ? 'desc' : 'asc';
        sortState = { col, dir };

        document.querySelectorAll('thead th.sortable').forEach(h => {
            h.classList.remove('asc', 'desc');
            h.querySelector('.sort-icon').textContent = '▲▼';
        });
        th.classList.add(dir);
        th.querySelector('.sort-icon').textContent = dir === 'asc' ? '▲' : '▼';

        const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-state):not(.hidden)'));
        rows.sort((a, b) => {
            const va = a.cells[col]?.textContent.trim() ?? '';
            const vb = b.cells[col]?.textContent.trim() ?? '';
            const na = parseFloat(va), nb = parseFloat(vb);
            const cmp = (!isNaN(na) && !isNaN(nb)) ? na - nb : va.localeCompare(vb);
            return dir === 'asc' ? cmp : -cmp;
        });
        rows.forEach((r, i) => { tbody.appendChild(r); r.cells[0].textContent = i + 1; });
    }

    /* ── Export CSV ── */
    function exportCSV() {
        const headers = ['#', 'Tag ID (EPC)', 'First Detected', 'Antenna', 'Signal (RSSI)', 'Saved At'];
        const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-state):not(.hidden)'))
            .map(r => Array.from(r.cells).map(c => `"${c.textContent.trim().replace(/"/g,'""')}"`).join(','));
        const csv = [headers.join(','), ...rows].join('\n');
        const a = Object.assign(document.createElement('a'), {
            href: URL.createObjectURL(new Blob([csv], { type: 'text/csv' })),
            download: 'rfid-tags.csv'
        });
        a.click();
    }
</script>

</body>
</html>
