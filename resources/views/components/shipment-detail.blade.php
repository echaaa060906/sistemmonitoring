<aside id="workspace-right">
    <!-- Tab nav -->
    <div class="panel-tabs">
        <button class="p-tab active" data-tab="detail"><i class="bi bi-info-circle"></i> Detail</button>
        <button class="p-tab" data-tab="risk"><i class="bi bi-shield"></i> Risk</button>
        <button class="p-tab" data-tab="analytics"><i class="bi bi-bar-chart"></i> Charts</button>
    </div>

    <div class="panel-scroll">

        <!-- ── PANE: Detail ─────────────────────────────── -->
        <div class="pane active" id="pane-detail">

            <!-- Shipment Info -->
            <div class="detail-sec">
                <div class="detail-sec-title">
                    Shipment Detail
                    <span class="chip chip-blue" id="d-status">—</span>
                </div>
                <div class="detail-row"><span class="lbl">Shipment ID</span>     <span class="val mono" id="d-id">—</span></div>
                <div class="detail-row"><span class="lbl">Container</span>        <span class="val mono" id="d-container">—</span></div>
                <div class="detail-row"><span class="lbl">Carrier</span>          <span class="val" id="d-carrier">—</span></div>
                <div class="detail-row"><span class="lbl">Mode</span>             <span class="val" id="d-mode">—</span></div>
                <div class="detail-row"><span class="lbl">Origin</span>           <span class="val" id="d-origin">—</span></div>
                <div class="detail-row"><span class="lbl">Destination</span>      <span class="val" id="d-destination">—</span></div>
                <div class="detail-row"><span class="lbl">ETA</span>              <span class="val mono" id="d-eta">—</span></div>
                <div class="detail-row"><span class="lbl">Risk Score</span>       <span class="val" id="d-risk-score">—</span></div>

                <!-- Transit progress -->
                <div class="progress-wrap">
                    <div class="progress-header">
                        <span style="font-size:.65rem;color:var(--c-muted)">Transit Progress</span>
                        <span id="d-progress-pct" style="font-family:var(--f-mono);font-size:.7rem;font-weight:700">—</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill bg-blue" id="d-progress-bar" style="width:0%"></div>
                    </div>
                </div>
            </div>

            <!-- Customs checklist -->
            <div class="detail-sec">
                <div class="detail-sec-title">Customs Documents</div>
                <div id="d-checklist">
                    <!-- JS rendered -->
                </div>
            </div>
        </div>

        <!-- ── PANE: Risk & News ────────────────────────── -->
        <div class="pane" id="pane-risk">

            <!-- Risk bars -->
            <div class="detail-sec">
                <div class="detail-sec-title">Risk Analysis</div>
                <div id="risk-bars">
                    <!-- JS rendered -->
                </div>
            </div>

            <!-- News -->
            <div class="detail-sec">
                <div class="detail-sec-title">Trade Intelligence</div>
                <div id="news-feed">
                    <!-- JS rendered -->
                </div>
            </div>
        </div>

        <!-- ── PANE: Analytics ──────────────────────────── -->
        <div class="pane" id="pane-analytics">
            <div class="detail-sec">
                <div class="detail-sec-title">Trend Analytics</div>
                <div class="spark-grid" id="spark-grid">
                    <!-- JS rendered -->
                </div>
            </div>
        </div>

    </div><!-- /panel-scroll -->
</aside>
