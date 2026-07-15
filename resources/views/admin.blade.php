<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Supply Chain Risk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --bg-color: #f8f9fa; --sidebar-bg: #1e293b; --sidebar-color: #cbd5e1; --primary: #2563eb; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-color); display: flex; width: 100vw; height: 100vh; overflow: hidden; }
        #sidebar { width: 250px; background: var(--sidebar-bg); color: var(--sidebar-color); display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 1.5rem 1.25rem; font-size: 1.1rem; font-weight: 700; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-nav { padding: 1rem 0; list-style: none; margin: 0; }
        .sidebar-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; color: var(--sidebar-color); text-decoration: none; font-weight: 500; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.05); color: #fff; border-left: 3px solid var(--primary); }
        #main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        #topbar { background: #fff; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
        .card { border: none; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <aside id="sidebar">
        <div class="sidebar-brand"><i class="bi bi-globe-americas"></i> RiskIntel</div>
        <ul class="sidebar-nav">
            <li><a href="/" class="sidebar-link"><i class="bi bi-grid-1x2"></i> Country Dashboard</a></li>
            <li><a href="/ports" class="sidebar-link"><i class="bi bi-geo-alt"></i> Port Locations</a></li>
            <li><a href="/comparison" class="sidebar-link"><i class="bi bi-arrow-left-right"></i> Comparison Engine</a></li>
            <li><a href="/favorites" class="sidebar-link"><i class="bi bi-star"></i> Watchlist</a></li>
            <li><a href="/admin" class="sidebar-link active"><i class="bi bi-gear"></i> Admin Dashboard</a></li>
        </ul>
    </aside>

    <main id="main-content">
        <header id="topbar">
            <div><h5 class="mb-0 fw-bold">Admin Dashboard</h5><small class="text-muted">Manage System Data</small></div>
            
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
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card p-4 text-center h-100">
                        <i class="bi bi-people text-primary mb-3" style="font-size: 2rem;"></i>
                        <h6 class="fw-bold">User Management</h6>
                        <p class="text-muted small">Manage access and user roles.</p>
                        <button class="btn btn-outline-primary btn-sm mt-auto">Manage Users</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 text-center h-100">
                        <i class="bi bi-database text-success mb-3" style="font-size: 2rem;"></i>
                        <h6 class="fw-bold">Port Datasets</h6>
                        <p class="text-muted small">Update and upload World Port Index data.</p>
                        <button class="btn btn-outline-success btn-sm mt-auto">Manage Ports</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 text-center h-100">
                        <i class="bi bi-journal-text text-warning mb-3" style="font-size: 2rem;"></i>
                        <h6 class="fw-bold">Analysis Articles</h6>
                        <p class="text-muted small">Write and publish custom analysis articles.</p>
                        <button class="btn btn-outline-warning btn-sm mt-auto">Manage Articles</button>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4 p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-spellcheck"></i> Sentiment Lexicon Dictionary</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-success fw-bold">Positive Words</h6>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-success">growth</span> <span class="badge bg-success">increase</span> <span class="badge bg-success">profit</span> <span class="badge bg-success">stable</span> <span class="badge bg-success">improve</span>
                                <button class="badge bg-secondary border-0">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-danger fw-bold">Negative Words</h6>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-danger">war</span> <span class="badge bg-danger">crisis</span> <span class="badge bg-danger">inflation</span> <span class="badge bg-danger">delay</span> <span class="badge bg-danger">disaster</span>
                                <button class="badge bg-secondary border-0">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
