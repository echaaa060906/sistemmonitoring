<div class="panel-card" id="world-map-wrapper">
    <div class="panel-card-header">
        <h2 class="panel-card-title">
            <i class="bi bi-map"></i> Global Logistics Map Network
        </h2>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary" style="font-family: var(--font-mono); font-size: 0.65rem;">LANE MONITOR ACTIVE</span>
        </div>
    </div>
    <div class="position-relative">
        <!-- Leaflet Map Container -->
        <div id="world-map"></div>
        
        <!-- Custom Map Legend -->
        <div class="map-legend">
            <div class="fw-bold mb-1 border-bottom pb-1" style="font-size: 0.65rem; color: var(--color-primary);">STATUS LEGEND</div>
            <div class="legend-item">
                <div class="legend-color bg-success"></div>
                <span>Normal</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-warning"></div>
                <span>Delay</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-critical"></div>
                <span>Port Congestion</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-danger"></div>
                <span>Critical / Weather</span>
            </div>
        </div>
    </div>
</div>
