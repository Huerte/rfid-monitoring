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
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${tag.epc}</td>
            <td>${tag.ant}</td>
            <td>${tag.gpi}</td>
            <td>${tag.rssi}</td>
            <td>${tag.times}</td>
            <td>${tag.pc}</td>
            <td>${tag.first_time || '—'}</td>
            <td>${tag.sensor || '—'}</td>
            <td>${tag.created_at}</td>
        `;
        const tbody = document.getElementById('tag-body');
        tbody.prepend(row);
        
        while (tbody.children.length > 10) {
            tbody.removeChild(tbody.lastChild);
        }
    });


