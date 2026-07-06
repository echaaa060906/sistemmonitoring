<div class="right-panel-section" id="risk-panel">
    <div class="right-section-title">
        <span>Risk & Operations Analysis</span>
        <i class="bi bi-shield-alert text-primary"></i>
    </div>
    
    <!-- Weather Risk -->
    <div class="risk-item">
        <div class="risk-header">
            <span class="risk-name">Weather Risk</span>
            <span class="risk-percentage text-success" id="risk-val-weather">15%</span>
        </div>
        <div class="risk-progress-bar">
            <div class="risk-progress-fill bg-success" style="width: 15%;" id="risk-bar-weather"></div>
        </div>
    </div>

    <!-- Currency Risk -->
    <div class="risk-item">
        <div class="risk-header">
            <span class="risk-name">Currency Fluctuation Risk</span>
            <span class="risk-percentage text-warning" id="risk-val-currency">42%</span>
        </div>
        <div class="risk-progress-bar">
            <div class="risk-progress-fill bg-warning" style="width: 42%;" id="risk-bar-currency"></div>
        </div>
    </div>

    <!-- Inflation Risk -->
    <div class="risk-item">
        <div class="risk-header">
            <span class="risk-name">Inflation Risk (GDP Delta)</span>
            <span class="risk-percentage text-success" id="risk-val-inflation">28%</span>
        </div>
        <div class="risk-progress-bar">
            <div class="risk-progress-fill bg-success" style="width: 28%;" id="risk-bar-inflation"></div>
        </div>
    </div>

    <!-- Political / Customs Risk -->
    <div class="risk-item">
        <div class="risk-header">
            <span class="risk-name">Political & Border Risk</span>
            <span class="risk-percentage text-success" id="risk-val-political">10%</span>
        </div>
        <div class="risk-progress-bar">
            <div class="risk-progress-fill bg-success" style="width: 10%;" id="risk-bar-political"></div>
        </div>
    </div>

    <!-- Port Congestion Risk -->
    <div class="risk-item">
        <div class="risk-header">
            <span class="risk-name">Port Congestion Risk</span>
            <span class="risk-percentage text-warning" id="risk-val-congestion">32%</span>
        </div>
        <div class="risk-progress-bar">
            <div class="risk-progress-fill bg-warning" style="width: 32%;" id="risk-bar-congestion"></div>
        </div>
    </div>

    <hr class="my-2" style="border-color: var(--border-color);">

    <!-- Overall Risk Score -->
    <div class="risk-item mt-2">
        <div class="risk-header font-semibold" style="font-size: 0.8rem;">
            <span>Overall Composite Risk Index</span>
            <span class="risk-percentage text-warning" id="risk-val-overall">48%</span>
        </div>
        <div class="risk-progress-bar" style="height: 10px;">
            <div class="risk-progress-fill bg-warning" style="width: 48%;" id="risk-bar-overall"></div>
        </div>
        <div class="text-muted mt-1" style="font-size: 0.65rem; line-height: 1.25;">
            *Skor ini dihitung berdasarkan bobot cuaca rute laut, volatilitas mata uang port, dan waktu tunggu antrean pelabuhan secara real-time.
        </div>
    </div>
</div>
