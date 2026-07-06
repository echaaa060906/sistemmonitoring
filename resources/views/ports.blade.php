<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Port Locations - Supply Chain Risk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #f8f9fa;
            --sidebar-bg: #1e293b;
            --sidebar-color: #cbd5e1;
            --primary: #2563eb;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-color); display: flex; width: 100vw; height: 100vh; overflow: hidden; }
        #sidebar { width: 250px; background: var(--sidebar-bg); color: var(--sidebar-color); display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 1.5rem 1.25rem; font-size: 1.1rem; font-weight: 700; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-nav { padding: 1rem 0; list-style: none; margin: 0; }
        .sidebar-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; color: var(--sidebar-color); text-decoration: none; font-weight: 500; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.05); color: #fff; border-left: 3px solid var(--primary); }
        
        #main-content { flex: 1; display: flex; flex-direction: column; }
        #topbar { background: #fff; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
        
        #map-container { flex: 1; position: relative; }
        #port-map { width: 100%; height: 100%; }
        
        #search-panel { position: absolute; top: 20px; left: 20px; z-index: 1000; background: #fff; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 300px; }
    </style>
</head>
<body>
    <aside id="sidebar">
        <div class="sidebar-brand"><i class="bi bi-globe-americas"></i> RiskIntel</div>
        <ul class="sidebar-nav">
            <li><a href="/" class="sidebar-link"><i class="bi bi-grid-1x2"></i> Country Dashboard</a></li>
            <li><a href="/map" class="sidebar-link"><i class="bi bi-map"></i> Peta Rute</a></li>
            <li><a href="/ports" class="sidebar-link active"><i class="bi bi-geo-alt"></i> Port Locations</a></li>
            <li><a href="/comparison" class="sidebar-link"><i class="bi bi-arrow-left-right"></i> Comparison Engine</a></li>
            <li><a href="/favorites" class="sidebar-link"><i class="bi bi-star"></i> Watchlist</a></li>
            <li><a href="/admin" class="sidebar-link"><i class="bi bi-gear"></i> Admin Dashboard</a></li>
        </ul>
    </aside>

    <main id="main-content">
        <header id="topbar">
            <div>
                <h5 class="mb-0 fw-bold">Port Location Dashboard</h5>
                <small class="text-muted">Global Port Directory</small>
            </div>
        </header>

        <div id="map-container">
            <div id="search-panel">
                <h6 class="fw-bold mb-3">Search Port</h6>
                <div class="input-group mb-3">
                    <input type="text" id="port-search" class="form-control" placeholder="Search by name or country...">
                    <button class="btn btn-primary" id="btn-search"><i class="bi bi-search"></i></button>
                </div>
                <div id="search-results" style="max-height: 200px; overflow-y: auto;">
                    <div class="text-muted small">Enter query to search ports...</div>
                </div>
            </div>
            <div id="port-map"></div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('port-map').setView([20, 0], 3);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

        let markers = [];

        document.getElementById('btn-search').addEventListener('click', async () => {
            const query = document.getElementById('port-search').value;
            const res = document.getElementById('search-results');
            res.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div>';
            
            // Cleanup markers
            markers.forEach(m => map.removeLayer(m));
            markers = [];

            try {
                const response = await fetch('/api/ports?query=' + query);
                const ports = await response.json();
                
                if (ports.length === 0) {
                    res.innerHTML = '<div class="text-danger small">No ports found.</div>';
                    return;
                }

                let html = '<ul class="list-group list-group-flush small">';
                ports.forEach(p => {
                    html += `<li class="list-group-item p-1 border-0"><a href="#" onclick="flyToPort(${p.latitude}, ${p.longitude})" class="text-decoration-none">${p.name} (${p.country})</a></li>`;
                    
                    const marker = L.marker([p.latitude, p.longitude]).addTo(map)
                        .bindPopup(`<b>${p.name}</b><br>${p.country}`);
                    markers.push(marker);
                });
                html += '</ul>';
                res.innerHTML = html;

                if (ports.length > 0) {
                    map.flyTo([ports[0].latitude, ports[0].longitude], 5);
                }
            } catch(e) {
                res.innerHTML = '<div class="text-danger small">Error fetching ports.</div>';
            }
        });

        window.flyToPort = function(lat, lng) {
            map.flyTo([lat, lng], 10);
        }
        
        // Initial load for demo
        document.getElementById('port-search').value = "Tanjung";
        document.getElementById('btn-search').click();
    </script>
</body>
</html>
