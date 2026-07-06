<header id="header">
    <div class="hd-left">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav">
            <a href="#">Supply Chain</a>
            <span class="sep">/</span>
            <span class="cur">Dashboard</span>
        </nav>

        <!-- Search -->
        <div class="hd-search">
            <i class="bi bi-search"></i>
            <input type="text" id="search-input" placeholder="Cari ID, pelabuhan, negara…">
        </div>

        <!-- Country Selector (Global Country Dashboard) -->
        <div class="hd-country-picker" style="margin-left:1rem">
            <select id="country-select" class="form-select form-select-sm" style="font-size:var(--f-xs);background-color:var(--c-bg-alt);border-color:var(--c-border);color:var(--c-primary);font-weight:600">
                <option value="ID">Indonesia</option>
                <option value="DE">Germany</option>
                <option value="CN">China</option>
                <option value="AU">Australia</option>
                <option value="US">United States</option>
            </select>
        </div>
    </div>

    <div class="hd-right">
        <div class="utc-clock" id="utc-clock">--:-- UTC</div>

        <a href="/map" class="hd-icon-btn" title="Peta Rute Pengiriman" style="text-decoration:none;color:var(--c-blue);font-weight:600;gap:4px;font-size:.72rem;padding:4px 8px;border:1px solid var(--c-border);">
            <i class="bi bi-map-fill" style="font-size:.85rem;"></i>
            Peta Rute
        </a>

        <button class="hd-icon-btn" title="Notifikasi">
            <i class="bi bi-bell"></i>
            <span class="notif-dot"></span>
        </button>


        <button class="hd-profile" title="Profil pengguna">
            <div class="hd-avatar">AD</div>
            <div style="text-align:left">
                <div class="hd-name">Admin</div>
                <div class="hd-role">Ops Manager</div>
            </div>
        </button>
    </div>
</header>
