<nav id="sidebar">
    <!-- Brand -->
    <div class="sb-brand">
        <div class="sb-brand-icon"><i class="bi bi-globe-asia-australia"></i></div>
        <div>
            <div class="sb-brand-text">SupplyChain</div>
            <div class="sb-brand-sub">Global Monitor</div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="sb-section">
        <a href="#" class="sb-link active" id="nav-dashboard">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="#" class="sb-link" id="nav-shipments">
            <i class="bi bi-box-seam"></i> Shipments
        </a>
        <a href="#" class="sb-link" id="nav-ports">
            <i class="bi bi-geo-alt"></i> Port Status
        </a>
        <a href="#" class="sb-link" id="nav-risk">
            <i class="bi bi-shield-check"></i> Risk Analysis
        </a>
        <a href="/comparison" class="sb-link" id="nav-compare">
            <i class="bi bi-arrow-left-right"></i> Comparison
        </a>
        <a href="/map" class="sb-link" id="nav-map">
            <i class="bi bi-map"></i> Peta Rute
        </a>
        <a href="#" class="sb-link" id="nav-docs">
            <i class="bi bi-file-earmark-text"></i> Customs
        </a>
        <a href="#" class="sb-link" id="nav-settings">
            <i class="bi bi-gear"></i> Settings
        </a>
    </div>

    <hr class="sb-divider">

    <!-- Overview counts -->
    <div class="sb-section">
        <div class="sb-section-label">Overview</div>
        <ul class="sb-metrics">
            <li class="sb-metric">
                <span class="sb-metric-label"><i class="bi bi-circle-fill text-blue" style="font-size:.5rem"></i> Active</span>
                <span class="sb-metric-val text-blue" id="sb-count-active">4</span>
            </li>
            <li class="sb-metric">
                <span class="sb-metric-label"><i class="bi bi-circle-fill text-yellow" style="font-size:.5rem"></i> Delayed</span>
                <span class="sb-metric-val text-yellow" id="sb-count-delayed">2</span>
            </li>
            <li class="sb-metric">
                <span class="sb-metric-label"><i class="bi bi-circle-fill text-red" style="font-size:.5rem"></i> Alert</span>
                <span class="sb-metric-val text-red" id="sb-count-alert">1</span>
            </li>
            <li class="sb-metric">
                <span class="sb-metric-label"><i class="bi bi-circle-fill text-green" style="font-size:.5rem"></i> Arrived</span>
                <span class="sb-metric-val text-green" id="sb-count-arrived">2</span>
            </li>
        </ul>
    </div>

    <hr class="sb-divider">

    <!-- Filters -->
    <div class="sb-section">
        <div class="sb-filter-row">
            <div class="sb-filter-label">Region</div>
            <div class="sb-filter-btns" id="filter-region">
                <button class="sb-btn-filter active" data-val="all">All</button>
                <button class="sb-btn-filter" data-val="asia">Asia</button>
                <button class="sb-btn-filter" data-val="europe">EU</button>
                <button class="sb-btn-filter" data-val="america">AM</button>
            </div>
        </div>
        <div class="sb-filter-row">
            <div class="sb-filter-label">Transport</div>
            <div class="sb-filter-btns" id="filter-transport">
                <button class="sb-btn-filter active" data-val="all">All</button>
                <button class="sb-btn-filter" data-val="sea">Sea</button>
                <button class="sb-btn-filter" data-val="air">Air</button>
                <button class="sb-btn-filter" data-val="land">Land</button>
            </div>
        </div>
        <div class="sb-filter-row">
            <div class="sb-filter-label">Risk</div>
            <div class="sb-filter-btns" id="filter-risk">
                <button class="sb-btn-filter active" data-val="all">All</button>
                <button class="sb-btn-filter" data-val="low">Low</button>
                <button class="sb-btn-filter" data-val="medium">Med</button>
                <button class="sb-btn-filter" data-val="high">High</button>
            </div>
        </div>
    </div>
</nav>
