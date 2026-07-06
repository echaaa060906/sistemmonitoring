<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watchlist - Supply Chain Risk</title>
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
            <li><a href="/favorites" class="sidebar-link active"><i class="bi bi-star"></i> Watchlist</a></li>
            <li><a href="/admin" class="sidebar-link"><i class="bi bi-gear"></i> Admin Dashboard</a></li>
        </ul>
    </aside>

    <main id="main-content">
        <header id="topbar">
            <div><h5 class="mb-0 fw-bold">Favorite Monitoring List</h5><small class="text-muted">Your saved countries</small></div>
        </header>
        <div class="container-fluid p-4">
            <div class="card p-4 text-center">
                <i class="bi bi-star text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Your Watchlist is Empty</h5>
                <p class="text-muted">Go to the Global Dashboard to add countries to your watchlist.</p>
                <div><a href="/" class="btn btn-primary mt-2">Go to Dashboard</a></div>
            </div>
        </div>
    </main>
</body>
</html>
