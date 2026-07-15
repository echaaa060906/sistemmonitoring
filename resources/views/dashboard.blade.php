<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk Intelligence Platform</title>
    
    <!-- Standard Bootstrap 5 (Sangat Cocok untuk Tugas Kuliah) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    
    <!-- Standard Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-bg: #343a40;
            --bg-color: #f4f6f9;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color);
            overflow-x: hidden;
        }

        /* Layout */
        #wrapper { display: flex; width: 100vw; height: 100vh; overflow: hidden; }
        
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

        /* Main Content */
        #main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        
        /* Header */
        #topbar {
            background-color: #fff;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.25rem;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.125);
            padding: 1rem 1.25rem;
            font-weight: 700;
            color: #495057;
        }

        /* KPI / Metric Boxes */
        .info-box {
            display: flex;
            background: #fff;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border-radius: 0.25rem;
            padding: 0.35rem;
            min-height: 70px;
            margin-bottom: 1.5rem;
        }
        .info-box-icon {
            border-radius: 0.25rem;
            align-items: center;
            display: flex;
            font-size: 1.5rem;
            justify-content: center;
            text-align: center;
            width: 50px;
            color: #fff;
        }
        .info-box-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 8px;
            flex: 1;
            overflow: hidden;
        }
        .info-box-text { text-transform: uppercase; font-size: 0.7rem; color: #6c757d; font-weight: 700; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
        .info-box-number { font-weight: 700; font-size: 1.1rem; color: #343a40; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
        
        .bg-info { background-color: #17a2b8 !important; }
        .bg-success { background-color: #28a745 !important; }
        .bg-warning { background-color: #ffc107 !important; }
        .bg-danger { background-color: #dc3545 !important; }
        .bg-primary { background-color: #007bff !important; }
        .bg-secondary { background-color: #6c757d !important; }

        /* Maps & Charts */
        #weather-map { height: 300px; width: 100%; border-radius: 0 0 0.25rem 0.25rem; }
        .chart-container { position: relative; height: 250px; width: 100%; padding: 1rem; }

        /* News List */
        .news-list { max-height: 400px; overflow-y: auto; padding: 0 1rem; }
        .news-item { border-bottom: 1px solid #dee2e6; padding: 1rem 0; }
        .news-item:last-child { border-bottom: none; }
        .news-title { font-weight: 600; font-size: 1rem; color: #007bff; margin-bottom: 0.25rem; display: block; text-decoration: none; }
        .news-title:hover { text-decoration: underline; }
        .news-desc { font-size: 0.85rem; color: #6c757d; margin-bottom: 0.5rem; }
        .badge-sentiment { font-size: 0.75rem; padding: 0.35em 0.65em; }

        /* Loader Overlay */
        #loader-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000; display: none; flex-direction: column;
            align-items: center; justify-content: center;
        }
    </style>
</head>
<body>

<div id="wrapper">
    <!-- Sidebar -->
    <aside id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-globe-americas"></i> SCM Project
        </div>
        <ul class="sidebar-nav">
            <li><a href="/" class="sidebar-link active"><i class="bi bi-speedometer2"></i> Global Dashboard</a></li>
            <li><a href="/map" class="sidebar-link"><i class="bi bi-map"></i> Peta Rute</a></li>
            <li><a href="/ports" class="sidebar-link"><i class="bi bi-geo-alt"></i> Port Locations</a></li>
            <li><a href="/comparison" class="sidebar-link"><i class="bi bi-intersect"></i> Comparison</a></li>
            <li><a href="/favorites" class="sidebar-link"><i class="bi bi-star"></i> Favorites List</a></li>
            <li><a href="/admin" class="sidebar-link"><i class="bi bi-gear"></i> Admin Panel</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Header -->
        <header id="topbar">
            <div>
                <h4 class="mb-0 text-dark">Global Country Dashboard</h4>
                <small class="text-muted">Supply Chain Risk Monitoring</small>
            </div>
            
            <div class="d-flex align-items-center">
                <label for="countrySelect" class="me-2 fw-bold text-secondary">Pilih Negara:</label>
                <select id="countrySelect" class="form-select shadow-sm" style="width: 200px;">
                    <option value="ID">Indonesia (ID)</option>
                    <option value="DE">Germany (DE)</option>
                    <option value="CN">China (CN)</option>
                    <option value="AU">Australia (AU)</option>
                    <option value="US">United States (US)</option>
                </select>

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

        <!-- Content Body -->
        <div class="container-fluid p-4 position-relative">
            
            <div id="loader-overlay">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 fw-bold text-secondary">Memuat Data...</div>
            </div>

            <!-- KPI Row -->
            <div class="row">
                <!-- Risk Score -->
                <div class="col-sm-6 col-md-4 col-lg-4 col-xl-2">
                    <div class="info-box">
                        <span class="info-box-icon" id="icon-risk"><i class="bi bi-shield-exclamation"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Risk</span>
                            <span class="info-box-number" id="kpi-risk-val">--</span>
                            <span class="badge" id="kpi-risk-class">--</span>
                        </div>
                    </div>
                </div>
                <!-- GDP -->
                <div class="col-sm-6 col-md-4 col-lg-4 col-xl-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="bi bi-bank"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">GDP</span>
                            <span class="info-box-number" id="kpi-gdp">--</span>
                        </div>
                    </div>
                </div>
                <!-- Inflation -->
                <div class="col-sm-6 col-md-4 col-lg-4 col-xl-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning text-dark"><i class="bi bi-graph-up-arrow"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Inflation</span>
                            <span class="info-box-number" id="kpi-inflation">--</span>
                        </div>
                    </div>
                </div>
                <!-- Population -->
                <div class="col-sm-6 col-md-4 col-lg-4 col-xl-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="bi bi-people"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Population</span>
                            <span class="info-box-number" id="kpi-population">--</span>
                        </div>
                    </div>
                </div>
                <!-- Currency -->
                <div class="col-sm-6 col-md-4 col-lg-4 col-xl-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="bi bi-cash-coin"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text" id="kpi-currency-name">Currency</span>
                            <span class="info-box-number" id="kpi-currency-rate">--</span>
                        </div>
                    </div>
                </div>
                <!-- Weather -->
                <div class="col-sm-6 col-md-4 col-lg-4 col-xl-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary" id="icon-weather"><i class="bi bi-cloud-sun"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Weather</span>
                            <span class="info-box-number" id="kpi-weather">--</span>
                            <small id="kpi-weather-desc" class="text-muted text-truncate d-block">--</small>
                            <small id="kpi-weather-extra" class="text-danger fw-bold text-truncate d-block">--</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maps & Analytics Row -->
            <div class="row">
                <!-- Weather Map -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><i class="bi bi-map text-primary me-2"></i> Global Weather Monitoring</div>
                        <div class="card-body p-0">
                            <div id="weather-map"></div>
                        </div>
                        <div class="card-footer bg-white text-center small text-muted">
                            <span class="me-3"><i class="bi bi-circle-fill text-primary"></i> Rain Zone</span>
                            <span class="me-3"><i class="bi bi-circle-fill text-danger"></i> Storm Risk</span>
                        </div>
                    </div>
                </div>
                
                <!-- Currency Impact Dashboard -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><i class="bi bi-currency-exchange text-success me-2"></i> Currency Impact Dashboard</div>
                        <div class="card-body p-0">
                            <div class="chart-container">
                                <canvas id="currencyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News & Trend Analytics Row -->
            <div class="row">
                <!-- News Intelligence -->
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-header"><i class="bi bi-newspaper text-info me-2"></i> News Intelligence (Sentiment Analysis)</div>
                        <div class="card-body p-0">
                            <div class="news-list" id="news-feed">
                                <!-- News items will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Trend Analytics -->
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header"><i class="bi bi-bar-chart-fill text-warning me-2"></i> Data Visualization Dashboard</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-2">
                                        <div class="chart-container" style="height: 180px;"><canvas id="gdpChart"></canvas></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-2">
                                        <div class="chart-container" style="height: 180px;"><canvas id="inflationChart"></canvas></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="border rounded p-2">
                                        <div class="chart-container" style="height: 200px;"><canvas id="riskTrendChart"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/global_dashboard.js') }}"></script>

</body>
</html>
