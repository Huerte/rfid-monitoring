//

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

window.Echo.channel('rfid-scans')
    .listen('.tag.scanned', (event) => {
        const tag = event.tagRead;
        const tbody = document.getElementById('tag-body');

        // Check if this EPC already has a row in the table
        const existingRow = Array.from(tbody.querySelectorAll('tr:not(.empty-state)')).find(r =>
            r.dataset.search && r.dataset.search.startsWith(tag.epc.toLowerCase())
        );

        if (existingRow) {
            // Just update the count cell in place (column index 3 = Count)
            existingRow.cells[3].textContent = tag.count;
            return;
        }

        const emptyState = tbody.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }

        tbody.querySelectorAll('tr.selected').forEach(r => r.classList.remove('selected'));

        Array.from(tbody.querySelectorAll('tr:not(.empty-state)')).forEach((r) => {
            const numCell = r.querySelector('.c-num');
            if (numCell) {
                const currentNum = parseInt(numCell.textContent || '0');
                if (!isNaN(currentNum)) {
                    numCell.textContent = currentNum + 1;
                }
            }
        });

        const row = document.createElement('tr');
        row.className = 'selected';
        row.setAttribute('data-search', tag.search_key);
        row.setAttribute('onclick', 'selectRow(this)');
        row.setAttribute('ondblclick', 'copyEpc(this)');
        row.setAttribute('title', 'Double-click to copy EPC');

        row.innerHTML = `
            <td class="c-num">1</td>
            <td>${tag.epc}</td>
            <td>${tag.first_time_exact || '—'}</td>
            <td class="center">${tag.ant}</td>
            <td class="right">${tag.rssi} dBm</td>
            <td>${tag.created_at_time}</td>
        `;

        tbody.prepend(row);

        while (tbody.children.length > 10) {
            tbody.removeChild(tbody.lastChild);
        }
    });


