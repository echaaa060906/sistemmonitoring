<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Country Comparison - Supply Chain Risk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        
        #main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        #topbar { background: #fff; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
        
        .comp-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1.5rem; height: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .metric-row { display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid #f1f5f9; }
        .metric-row:last-child { border-bottom: none; }
        .metric-label { font-weight: 600; color: #64748b; font-size: 0.85rem; text-transform: uppercase; }
        .metric-val { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
        
        .vs-badge { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); background: var(--primary); color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: 700; border: 4px solid var(--bg-color); z-index: 10; }
    </style>
</head>
<body>
    <aside id="sidebar">
        <div class="sidebar-brand"><i class="bi bi-globe-americas"></i> RiskIntel</div>
        <ul class="sidebar-nav">
            <li><a href="/" class="sidebar-link"><i class="bi bi-grid-1x2"></i> Country Dashboard</a></li>
            <li><a href="/ports" class="sidebar-link"><i class="bi bi-geo-alt"></i> Port Locations</a></li>
            <li><a href="/comparison" class="sidebar-link active"><i class="bi bi-arrow-left-right"></i> Comparison Engine</a></li>
            <li><a href="/favorites" class="sidebar-link"><i class="bi bi-star"></i> Watchlist</a></li>
            <li><a href="/admin" class="sidebar-link"><i class="bi bi-gear"></i> Admin Dashboard</a></li>
        </ul>
    </aside>

    <main id="main-content">
        <header id="topbar">
            <div>
                <h5 class="mb-0 fw-bold">Country Comparison Engine</h5>
                <small class="text-muted">Compare Supply Chain Risks</small>
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

        <div class="container-fluid p-4">
            
            <div class="row mb-4 align-items-center">
                <div class="col-md-5">
                    <select id="countryA" class="form-select form-select-lg">
                        <option value="DE" selected>Germany</option>
                        <option value="ID">Indonesia</option>
                        <option value="CN">China</option>
                        <option value="AU">Australia</option>
                        <option value="US">United States</option>
                    </select>
                </div>
                <div class="col-md-2 text-center">
                    <button class="btn btn-primary" onclick="compare()"><i class="bi bi-arrow-repeat"></i> Compare</button>
                </div>
                <div class="col-md-5">
                    <select id="countryB" class="form-select form-select-lg">
                        <option value="AU" selected>Australia</option>
                        <option value="ID">Indonesia</option>
                        <option value="CN">China</option>
                        <option value="DE">Germany</option>
                        <option value="US">United States</option>
                    </select>
                </div>
            </div>

            <div id="loader" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-primary"></div>
            </div>

            <div class="position-relative" id="compare-results">
                <div class="vs-badge">VS</div>
                <div class="row g-0">
                    <div class="col-md-6 pe-3">
                        <div class="comp-card" id="cardA">
                            <h3 class="text-center mb-4" id="nameA">Germany</h3>
                            <div class="metric-row"><span class="metric-label">GDP (World Bank)</span><span class="metric-val" id="gdpA">--</span></div>
                            <div class="metric-row"><span class="metric-label">Inflation Rate</span><span class="metric-val" id="infA">--</span></div>
                            <div class="metric-row"><span class="metric-label">Currency vs USD</span><span class="metric-val" id="curA">--</span></div>
                            <div class="metric-row"><span class="metric-label">Current Weather</span><span class="metric-val" id="weaA">--</span></div>
                            <div class="metric-row"><span class="metric-label">Risk Score</span><span class="metric-val" id="riskA">--</span></div>
                        </div>
                    </div>
                    <div class="col-md-6 ps-3">
                        <div class="comp-card" id="cardB">
                            <h3 class="text-center mb-4" id="nameB">Australia</h3>
                            <div class="metric-row"><span class="metric-label">GDP (World Bank)</span><span class="metric-val" id="gdpB">--</span></div>
                            <div class="metric-row"><span class="metric-label">Inflation Rate</span><span class="metric-val" id="infB">--</span></div>
                            <div class="metric-row"><span class="metric-label">Currency vs USD</span><span class="metric-val" id="curB">--</span></div>
                            <div class="metric-row"><span class="metric-label">Current Weather</span><span class="metric-val" id="weaB">--</span></div>
                            <div class="metric-row"><span class="metric-label">Risk Score</span><span class="metric-val" id="riskB">--</span></div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let countriesData = [];

        async function fetchCountries() {
            const res = await fetch('/api/countries');
            countriesData = await res.json();
            compare();
        }

        function compare() {
            if (countriesData.length === 0) return;
            const ca = document.getElementById('countryA').value;
            const cb = document.getElementById('countryB').value;

            const dataA = countriesData.find(c => c.iso_code === ca);
            const dataB = countriesData.find(c => c.iso_code === cb);

            if(dataA) fillCard('A', dataA);
            if(dataB) fillCard('B', dataB);
        }

        function fillCard(side, c) {
            document.getElementById('name' + side).textContent = c.name;
            document.getElementById('gdp' + side).textContent = '$' + (c.gdp / 1e12).toFixed(2) + 'T';
            document.getElementById('inf' + side).textContent = c.inflation.toFixed(1) + '%';
            document.getElementById('cur' + side).textContent = c.exchange_rate.toFixed(2) + ' ' + c.currency;
            document.getElementById('wea' + side).textContent = c.weather.temp + '°C, ' + c.weather.wind + 'kt';
            
            const riskEl = document.getElementById('risk' + side);
            riskEl.textContent = c.risk.total_risk + ' (' + c.risk.class + ')';
            
            // Highlight color
            if (c.risk.class === 'Low') riskEl.style.color = '#16a34a';
            else if (c.risk.class === 'High') riskEl.style.color = '#dc2626';
            else riskEl.style.color = '#d97706';
        }

        fetchCountries();
    </script>
</body>
</html>
