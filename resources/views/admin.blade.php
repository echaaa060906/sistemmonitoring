<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Supply Chain Risk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #343a40; --bg-color: #f4f6f9; }
        body { font-family: 'Roboto', sans-serif; background-color: var(--bg-color); display: flex; width: 100vw; height: 100vh; overflow: hidden; }
        #sidebar { width: 250px; background-color: var(--sidebar-bg); color: #fff; display: flex; flex-direction: column; flex-shrink: 0; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .sidebar-brand { padding: 1.25rem 1rem; font-size: 1.1rem; font-weight: 700; text-align: center; border-bottom: 1px solid #4f5962; background-color: #212529; }
        .sidebar-nav { padding: 0; list-style: none; margin-top: 1rem; }
        .sidebar-link { display: block; padding: 0.8rem 1.25rem; color: #c2c7d0; text-decoration: none; transition: 0.2s; }
        .sidebar-link i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar-link:hover, .sidebar-link.active { color: #fff; background-color: rgba(255,255,255,0.1); border-left: 4px solid var(--primary-color); }
        #main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        #topbar { background-color: #fff; padding: 1rem 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; z-index: 10; }
        .card { border: none; border-radius: 0.25rem; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); margin-bottom: 1.5rem; }
        .tab-pane { padding-top: 1.5rem; }
    </style>
</head>
<body>
    <aside id="sidebar">
        <div class="sidebar-brand"><i class="bi bi-globe-americas"></i> SCM Project</div>
        <ul class="sidebar-nav">
            <li><a href="/" class="sidebar-link"><i class="bi bi-speedometer2"></i> Global Dashboard</a></li>
            <li><a href="/map" class="sidebar-link"><i class="bi bi-map"></i> Peta Rute</a></li>
            <li><a href="/ports" class="sidebar-link"><i class="bi bi-geo-alt"></i> Port Locations</a></li>
            <li><a href="/comparison" class="sidebar-link"><i class="bi bi-intersect"></i> Comparison</a></li>
            <li><a href="/favorites" class="sidebar-link"><i class="bi bi-star"></i> Favorites List</a></li>
            <li><a href="/admin" class="sidebar-link active"><i class="bi bi-gear"></i> Admin Panel</a></li>
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
            <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#users" type="button">Users</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ports" type="button">Ports</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#articles" type="button">Articles</button></li>
            </ul>

            <div class="tab-content bg-white border border-top-0 p-4">
                
                <!-- Users Tab -->
                <div class="tab-pane fade show active" id="users" role="tabpanel">
                    <div class="d-flex justify-content-between mb-3">
                        <h6 class="fw-bold">User Management</h6>
                        <button class="btn btn-primary btn-sm" onclick="fetchUsers()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th><th>Actions</th></tr></thead>
                            <tbody id="tbody-users"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Ports Tab -->
                <div class="tab-pane fade" id="ports" role="tabpanel">
                    <div class="d-flex justify-content-between mb-3">
                        <h6 class="fw-bold">Port Datasets</h6>
                        <div>
                            <button class="btn btn-primary btn-sm" onclick="fetchPorts()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                            <button class="btn btn-success btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#portModal" onclick="clearForm('portForm')"><i class="bi bi-plus-lg"></i> Add Port</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light"><tr><th>ID</th><th>Code</th><th>Name</th><th>Country</th><th>Lat/Lon</th><th>Actions</th></tr></thead>
                            <tbody id="tbody-ports"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Articles Tab -->
                <div class="tab-pane fade" id="articles" role="tabpanel">
                    <div class="d-flex justify-content-between mb-3">
                        <h6 class="fw-bold">Analysis Articles</h6>
                        <div>
                            <button class="btn btn-primary btn-sm" onclick="fetchArticles()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                            <button class="btn btn-success btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#articleModal" onclick="clearForm('articleForm')"><i class="bi bi-plus-lg"></i> Add Article</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light"><tr><th>ID</th><th>Title</th><th>Author</th><th>Content Snippet</th><th>Actions</th></tr></thead>
                            <tbody id="tbody-articles"></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Port Modal -->
    <div class="modal fade" id="portModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="portForm" onsubmit="savePort(event)">
                    <div class="modal-header">
                        <h5 class="modal-title">Port details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="portId" name="id">
                        <div class="mb-3"><label>Name</label><input type="text" class="form-control" name="name" required></div>
                        <div class="mb-3"><label>Country (ISO)</label><input type="text" class="form-control" name="country" required></div>
                        <div class="mb-3"><label>Code</label><input type="text" class="form-control" name="code"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label>Latitude</label><input type="number" step="any" class="form-control" name="latitude" required></div>
                            <div class="col-md-6 mb-3"><label>Longitude</label><input type="number" step="any" class="form-control" name="longitude" required></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Port</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Article Modal -->
    <div class="modal fade" id="articleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="articleForm" onsubmit="saveArticle(event)">
                    <div class="modal-header">
                        <h5 class="modal-title">Article details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="articleId" name="id">
                        <div class="mb-3"><label>Title</label><input type="text" class="form-control" name="title" required></div>
                        <div class="mb-3"><label>Author</label><input type="text" class="form-control" name="author" value="{{ Auth::user()->name }}"></div>
                        <div class="mb-3"><label>Content</label><textarea class="form-control" name="content" rows="6" required></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Article</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';
        
        async function req(url, method = 'GET', body = null) {
            const opts = { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } };
            if(body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
            return await fetch(url, opts).then(r => r.json());
        }

        function clearForm(id) { document.getElementById(id).reset(); document.getElementById(id).elements['id'].value = ''; }

        // --- Users ---
        async function fetchUsers() {
            const users = await req('/api/admin/users');
            document.getElementById('tbody-users').innerHTML = users.map(u => `
                <tr>
                    <td>${u.id}</td><td>${u.name}</td><td>${u.email}</td><td>${u.created_at}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id})"><i class="bi bi-trash"></i></button></td>
                </tr>
            `).join('');
        }
        async function deleteUser(id) { if(confirm('Delete user?')) { await req(`/api/admin/users/${id}`, 'DELETE'); fetchUsers(); } }

        // --- Ports ---
        async function fetchPorts() {
            const ports = await req('/api/admin/ports');
            document.getElementById('tbody-ports').innerHTML = ports.map(p => `
                <tr>
                    <td>${p.id}</td><td>${p.code || '-'}</td><td>${p.name}</td><td>${p.country}</td><td>${p.latitude}, ${p.longitude}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="deletePort(${p.id})"><i class="bi bi-trash"></i></button></td>
                </tr>
            `).join('');
        }
        async function savePort(e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(e.target));
            await req('/api/admin/ports', 'POST', data);
            bootstrap.Modal.getInstance(document.getElementById('portModal')).hide();
            fetchPorts();
        }
        async function deletePort(id) { if(confirm('Delete port?')) { await req(`/api/admin/ports/${id}`, 'DELETE'); fetchPorts(); } }

        // --- Articles ---
        async function fetchArticles() {
            const articles = await req('/api/admin/articles');
            document.getElementById('tbody-articles').innerHTML = articles.map(a => `
                <tr>
                    <td>${a.id}</td><td>${a.title}</td><td>${a.author || '-'}</td><td>${a.content.substring(0, 50)}...</td>
                    <td><button class="btn btn-danger btn-sm" onclick="deleteArticle(${a.id})"><i class="bi bi-trash"></i></button></td>
                </tr>
            `).join('');
        }
        async function saveArticle(e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(e.target));
            await req('/api/admin/articles', 'POST', data);
            bootstrap.Modal.getInstance(document.getElementById('articleModal')).hide();
            fetchArticles();
        }
        async function deleteArticle(id) { if(confirm('Delete article?')) { await req(`/api/admin/articles/${id}`, 'DELETE'); fetchArticles(); } }

        // Initial loads
        document.addEventListener('DOMContentLoaded', () => { fetchUsers(); fetchPorts(); fetchArticles(); });
    </script>
</body>
</html>
