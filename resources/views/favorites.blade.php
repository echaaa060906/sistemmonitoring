<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watchlist - Global Supply Chain Risk Intelligence Platform</title>
    
    <!-- Standard Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
            <li><a href="/" class="sidebar-link"><i class="bi bi-speedometer2"></i> Global Dashboard</a></li>
            <li><a href="/map" class="sidebar-link"><i class="bi bi-map"></i> Peta Rute</a></li>
            <li><a href="/ports" class="sidebar-link"><i class="bi bi-geo-alt"></i> Port Locations</a></li>
            <li><a href="/comparison" class="sidebar-link"><i class="bi bi-intersect"></i> Comparison</a></li>
            <li><a href="/favorites" class="sidebar-link active"><i class="bi bi-star"></i> Favorites List</a></li>
            <li><a href="/admin" class="sidebar-link"><i class="bi bi-gear"></i> Admin Panel</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Header -->
        <header id="topbar">
            <div>
                <h5 class="mb-0 fw-bold">Favorite Monitoring List</h5>
                <small class="text-muted">Your saved countries</small>
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

        <!-- Content Body -->
        <div class="container-fluid p-4">
            @if(count($favorites) > 0)
                <div class="row">
                    @foreach($favorites as $fav)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 p-3 shadow-sm border-0" style="border-top: 4px solid var(--primary-color) !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="fw-bold mb-0">{{ $fav->name }}</h5>
                                <span class="badge bg-secondary">{{ $fav->iso_code }}</span>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <small class="text-muted d-block">Region</small>
                                <span class="fw-medium">{{ $fav->region }}</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Currency</small>
                                <span class="fw-medium">{{ $fav->currency_code }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block">GDP</small>
                                <span class="fw-medium text-success">${{ number_format($fav->gdp / 1000000000, 2) }} B</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="card p-5 text-center shadow-sm">
                    <i class="bi bi-star-fill text-warning mb-3" style="font-size: 4rem;"></i>
                    <h4 class="fw-bold">Your Watchlist is Empty</h4>
                    <p class="text-muted mb-4">Go to the Global Dashboard to add countries to your watchlist.</p>
                    <div>
                        <a href="/" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
