<div class="right-panel-section" id="analytics-panel">
    <div class="right-section-title">
        <span>Analytics Sparklines</span>
        <i class="bi bi-graph-up text-primary"></i>
    </div>
    
    <div class="analytics-grid">
        <!-- Sparkline 1: Shipment Volume -->
        <div class="analytics-chart-card">
            <span class="analytics-chart-title">Shipment Vol</span>
            <span class="analytics-chart-val" id="chart-val-shipments">1,482 Vol</span>
            <div class="chart-canvas-wrapper">
                <canvas id="sparkline-shipments"></canvas>
            </div>
        </div>

        <!-- Sparkline 2: Route Temperature -->
        <div class="analytics-chart-card">
            <span class="analytics-chart-title">Route Temp</span>
            <span class="analytics-chart-val" id="chart-val-temp">22.4°C Avg</span>
            <div class="chart-canvas-wrapper">
                <canvas id="sparkline-temp"></canvas>
            </div>
        </div>

        <!-- Sparkline 3: Currency Index -->
        <div class="analytics-chart-card">
            <span class="analytics-chart-title">Exchange Index</span>
            <span class="analytics-chart-val" id="chart-val-exchange">1.042 Delta</span>
            <div class="chart-canvas-wrapper">
                <canvas id="sparkline-exchange"></canvas>
            </div>
        </div>

        <!-- Sparkline 4: World Bank GDP -->
        <div class="analytics-chart-card">
            <span class="analytics-chart-title">GDP Growth</span>
            <span class="analytics-chart-val" id="chart-val-gdp">5.2% Annual</span>
            <div class="chart-canvas-wrapper">
                <canvas id="sparkline-gdp"></canvas>
            </div>
        </div>

        <!-- Sparkline 5: Inflation Rate -->
        <div class="analytics-chart-card">
            <span class="analytics-chart-title">Inflation Index</span>
            <span class="analytics-chart-val" id="chart-val-inflation">3.4% Index</span>
            <div class="chart-canvas-wrapper">
                <canvas id="sparkline-inflation"></canvas>
            </div>
        </div>

        <!-- Sparkline 6: Port Congestion -->
        <div class="analytics-chart-card">
            <span class="analytics-chart-title">Port Wait Time</span>
            <span class="analytics-chart-val" id="chart-val-congestion">2.1 Days</span>
            <div class="chart-canvas-wrapper">
                <canvas id="sparkline-congestion"></canvas>
            </div>
        </div>
    </div>
</div>
