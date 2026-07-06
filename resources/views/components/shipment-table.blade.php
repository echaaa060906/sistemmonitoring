<div id="table-panel">

    <div class="panel-header">
        <span class="panel-title">
            <i class="bi bi-box-seam"></i>
            Shipment Monitor
            <span class="chip chip-blue" id="table-count">9 entries</span>
        </span>
        <div style="display:flex;gap:.5rem;align-items:center">
            <button class="hd-icon-btn" title="Refresh data" onclick="renderTable()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button class="hd-icon-btn" title="Export">
                <i class="bi bi-download"></i>
            </button>
        </div>
    </div>

    <table class="t" id="shipment-table">
        <thead>
            <tr>
                <th style="width:24px"></th>
                <th>Shipment ID</th>
                <th>Container</th>
                <th>Route</th>
                <th>Carrier / Mode</th>
                <th>ETA</th>
                <th>Customs</th>
                <th>Risk</th>
                <th style="width:60px">Action</th>
            </tr>
        </thead>
        <tbody id="shipment-tbody">
            <!-- JS rendered -->
        </tbody>
    </table>
</div>
