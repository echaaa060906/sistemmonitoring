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
            --bg-color: #f4f6f9;
            --sidebar-bg: #343a40;
            --primary-color: #007bff;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-color); display: flex; width: 100vw; height: 100vh; overflow: hidden; margin: 0; }
        
        /* Sidebar (Standard Admin Style) */
        #sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            color: #fff;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar-brand {
            padding: 1.25rem 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            text-align: center;
            border-bottom: 1px solid #4f5962;
            background-color: #212529;
        }
        .sidebar-nav { padding: 0; list-style: none; margin-top: 1rem; }
        .sidebar-link {
            display: block;
            padding: 0.8rem 1.25rem;
            color: #c2c7d0;
            text-decoration: none;
            transition: 0.2s;
        }
        .sidebar-link i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar-link:hover, .sidebar-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
            border-left: 4px solid var(--primary-color);
        }
        
        #main-content { flex: 1; display: flex; flex-direction: column; }
        #topbar { background: #fff; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
        
        #map-container { flex: 1; position: relative; }
        #port-map { width: 100%; height: 100%; }
        
        #search-panel { position: absolute; top: 20px; left: 20px; z-index: 1000; background: #fff; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 300px; }
    </style>
</head>
<body>
    <aside id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-globe-americas"></i> SCM Project
        </div>
        <ul class="sidebar-nav">
            <li><a href="/" class="sidebar-link"><i class="bi bi-speedometer2"></i> Global Dashboard</a></li>
            <li><a href="/map" class="sidebar-link"><i class="bi bi-map"></i> Peta Rute</a></li>
            <li><a href="/ports" class="sidebar-link active"><i class="bi bi-geo-alt"></i> Port Locations</a></li>
            <li><a href="/comparison" class="sidebar-link"><i class="bi bi-intersect"></i> Comparison</a></li>
            <li><a href="/favorites" class="sidebar-link"><i class="bi bi-star"></i> Favorites List</a></li>
            <li><a href="/admin" class="sidebar-link"><i class="bi bi-gear"></i> Admin Panel</a></li>
        </ul>
    </aside>

    <main id="main-content">
        <header id="topbar">
            <div>
                <h5 class="mb-0 fw-bold">Port Location Dashboard</h5>
                <small class="text-muted">Global Port Directory</small>
            </div>
            
            <div class="d-flex align-items-center">
                @auth
                <div class="dropdown ms-3">
                    <button class="btn btn-outline-secondary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right text-danger"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const map = L.map('port-map').setView([20, 0], 3);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

        let markers = [];

        async function loadPorts(query = '') {
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
                        .bindPopup(`<b>${p.name}</b><br>${p.country}<br><div id="marine-${p.id || p.name.replace(/\s+/g,'')}"><small class="text-primary"><i class="spinner-border spinner-border-sm"></i> Loading marine data...</small></div>`);
                    
                    marker.on('popupopen', async function() {
                        const divId = `marine-${p.id || p.name.replace(/\s+/g,'')}`;
                        const div = document.getElementById(divId);
                        if (div && !div.dataset.loaded) {
                            try {
                                const mRes = await fetch(`/api/marine?lat=${p.latitude}&lon=${p.longitude}`);
                                const mData = await mRes.json();
                                div.innerHTML = `
                                    <hr class="my-1">
                                    <div class="small">
                                        <i class="bi bi-water text-info"></i> Wave Height: <b>${mData.wave_height} m</b><br>
                                        <i class="bi bi-compass text-secondary"></i> Current: <b>${mData.ocean_current_velocity} km/h</b>
                                    </div>
                                `;
                                div.dataset.loaded = 'true';
                            } catch (e) {
                                div.innerHTML = '<span class="text-danger small">Failed to load marine data</span>';
                            }
                        }
                    });
                    
                    markers.push(marker);
                });
                html += '</ul>';
                res.innerHTML = html;
            } catch (error) {
                res.innerHTML = '<div class="text-danger small">Error loading data.</div>';
            }
        }

        document.getElementById('btn-search').addEventListener('click', () => {
            loadPorts(document.getElementById('port-search').value);
        });

        // Load all ports on startup
        loadPorts();

        window.flyToPort = function(lat, lng) {
            map.flyTo([lat, lng], 10);
        }
    </script>
</body>
</html>
