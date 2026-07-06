/* =========================================================
 * dashboard.js — Supply Chain Monitor v2.1-Map
 * ========================================================= */

/* =========================================================
 * dashboard.js — Supply Chain Monitor v2.2-FullStack
 * ========================================================= */

let SHIPMENTS = [];
let ACTIVE_COUNTRY = 'ID';
let activeId = null;
let filterRegion = 'all';
let filterTransport = 'all';
let filterRisk = 'all';
let searchQuery = '';
let sparkCharts = {};

/* ── DOM helpers ───────────────────────────────────────── */
const $ = id => document.getElementById(id);
const el = (tag, cls, html) => { const e = document.createElement(tag); if (cls) e.className = cls; if (html) e.innerHTML = html; return e; };

/* ── Clock ─────────────────────────────────────────────── */
function updateClock () {
    const now = new Date();
    const utc  = now.toISOString().substring(11, 19);
    const clock = $('utc-clock');
    if (clock) clock.textContent = utc + ' UTC';
    const sync  = $('last-sync');
    if (sync)  sync.textContent  = now.toTimeString().substring(0, 8);
}
setInterval(updateClock, 1000);
updateClock();

/* ── Chip helpers ──────────────────────────────────────── */
function riskBadge (level) {
    const map = { low: 'risk-low', medium: 'risk-med', high: 'risk-high' };
    const lbl = { low: 'LOW', medium: 'MED', high: 'HIGH' };
    return `<span class="risk ${map[level] || 'risk-low'}">${lbl[level] || 'LOW'}</span>`;
}

/* ── Load Dynamic Data from API ────────────────────────── */
async function loadDashboardData() {
    try {
        const response = await fetch('/api/countries');
        const data = await response.json();
        
        // Transform backend dynamic country data to shipment-like objects for the map & table
        SHIPMENTS = data.map((c, idx) => {
            const centroid = getCentroidCoords(c.iso_code);
            return {
                id: `SH-2026-${9020 + idx}`,
                container: `CNTR-${c.iso_code}-${1029 + idx}`,
                carrier: getCarrierForIso(c.iso_code),
                mode: c.iso_code === 'DE' ? 'Land' : 'Sea',
                modeIcon: c.iso_code === 'DE' ? 'bi-truck' : 'bi-water',
                origin: `${c.name} Port`,
                destination: 'Tanjung Priok (ID)',
                eta: '2026-07-15',
                region: c.region.toLowerCase(),
                risk: c.risk.class,
                riskScore: c.risk.total_risk,
                customsStatus: c.risk.total_risk > 50 ? 'In Review' : 'Cleared',
                customsChip: c.risk.total_risk > 50 ? 'chip-yellow' : 'chip-green',
                status: c.risk.total_risk > 55 ? 'Delayed' : (c.risk.total_risk > 35 ? 'In Transit' : 'Arrived'),
                statusChip: c.risk.total_risk > 55 ? 'chip-yellow' : (c.risk.total_risk > 35 ? 'chip-blue' : 'chip-green'),
                progress: c.risk.total_risk > 55 ? 45 : (c.risk.total_risk > 35 ? 75 : 100),
                weather: `${c.weather.temp}°C / ${c.weather.wind}kt`,
                coords: `${centroid[0].toFixed(2)}°N ${centroid[1].toFixed(2)}°E`,
                originCoord: centroid,
                destCoord: [-6.10, 106.88], // Default to Tanjung Priok
                currentCoord: [centroid[0] - (centroid[0] + 6.10) * 0.3, centroid[1] - (centroid[1] - 106.88) * 0.3],
                documents: [
                    { name: 'Bill of Lading', done: true },
                    { name: 'Commercial Invoice', done: true },
                    { name: 'Packing List', done: c.risk.total_risk < 50 },
                    { name: 'Cert. of Origin', done: true },
                    { name: 'Marine Insurance', done: c.risk.total_risk < 40 },
                    { name: 'Export Permit', done: true },
                    { name: 'Import Permit', done: c.risk.total_risk < 60 },
                ],
                risks: [
                    { label: 'Weather', pct: c.risk.weather_risk, color: '#2563EB' },
                    { label: 'Currency', pct: c.risk.currency_risk, color: '#D97706' },
                    { label: 'Inflation', pct: c.risk.inflation_risk, color: '#EA580C' },
                    { label: 'Political & News', pct: c.risk.news_sentiment_risk, color: '#16A34A' },
                    { label: 'Port Wait', pct: Math.round(c.risk.total_risk * 0.8), color: '#DC2626' }
                ],
                telemetry: [
                    { label: 'Temp (Cargo)', val: `${c.weather.temp}°C` },
                    { label: 'GDP (WB)', val: c.gdp ? `$${(c.gdp / 1e12).toFixed(2)}T` : 'N/A' },
                    { label: 'Inflation', val: `${c.inflation}%` }
                ],
                timeline: [
                    { label: 'Loaded — Port', time: '2026-07-02', state: 'done' },
                    { label: 'Sea Transit', time: '2026-07-06', state: 'curr' },
                    { label: 'Arrival Tanjung Priok', time: '2026-07-15', state: '' }
                ],
                iso: c.iso_code
            };
        });

        if (SHIPMENTS.length > 0) {
            if (!activeId) activeId = SHIPMENTS[0].id;
            updateCounts();
            renderTable();
            initMap();
            selectShipment(activeId);
            updateTopStats();
        }
    } catch (err) {
        console.error("Error fetching live country data:", err);
    }
}

function getCentroidCoords(iso) {
    const coords = {
        'ID': [-6.20, 106.84],
        'DE': [52.52, 13.40],
        'CN': [39.90, 116.40],
        'AU': [-35.28, 149.13],
        'US': [38.90, -77.03],
    };
    return coords[iso] || [0, 0];
}

function getCarrierForIso(iso) {
    const carriers = { 'ID': 'Samudera Indonesia', 'DE': 'Hapag-Lloyd', 'CN': 'COSCO', 'AU': 'ANL', 'US': 'ONE Line' };
    return carriers[iso] || 'Global Carrier';
}

function updateTopStats() {
    const s = SHIPMENTS.find(x => x.id === activeId);
    if (!s) return;

    const set  = (id, val) => { const e = document.getElementById(id); if (e) e.textContent = val; };
    const html = (id, val) => { const e = document.getElementById(id); if (e) e.innerHTML  = val; };

    /* Marine Weather */
    set('stat-weather', s.weather || '—');

    /* Origin Country */
    set('stat-country', s.iso || s.origin || '—');
    html('stat-country-sub', `<i class="bi bi-geo-alt"></i> ${s.origin}`);

    /* GDP */
    const gdp = s.telemetry?.find(t => t.label === 'GDP (WB)')?.val || 'N/A';
    set('stat-gdp', gdp);

    /* Risk */
    const riskEl = document.getElementById('stat-risk');
    if (riskEl) {
        riskEl.textContent = s.riskScore + '%';
        riskEl.className   = 'stat-value ' + (s.riskScore > 60 ? 'text-red' : s.riskScore > 35 ? 'text-orange' : 'text-green');
    }
    set('stat-risk-sub', `${s.risk.charAt(0).toUpperCase() + s.risk.slice(1)} Risk`);
    /* Weather sub */
    set('stat-weather-sub', `${s.weather}`);
    /* Origin sub */
    set('stat-origin', s.origin);
}


/* ── Filtered dataset ──────────────────────────────────── */
function filteredShipments () {
    return SHIPMENTS.filter(s => {
        if (filterRegion !== 'all' && s.region !== filterRegion) return false;
        if (filterTransport !== 'all' && s.mode.toLowerCase() !== filterTransport) return false;
        if (filterRisk !== 'all' && s.risk !== filterRisk) return false;
        if (searchQuery) {
            const q = searchQuery.toLowerCase();
            if (!s.id.toLowerCase().includes(q) &&
                !s.origin.toLowerCase().includes(q) &&
                !s.destination.toLowerCase().includes(q) &&
                !s.carrier.toLowerCase().includes(q)) return false;
        }
        return true;
    });
}

/* ── Update sidebar counters ───────────────────────────── */
function updateCounts () {
    const all = SHIPMENTS;
    const set = (id, v) => { const e = $(id); if (e) e.textContent = v; };
    set('cnt-transit', all.filter(s => s.status === 'In Transit').length);
    set('cnt-delayed', all.filter(s => s.status === 'Delayed').length);
    set('cnt-alert',   all.filter(s => s.status === 'Alert').length);
    set('cnt-arrived', all.filter(s => s.status === 'Arrived').length);
}

/* ── Render table ──────────────────────────────────────── */
function renderTable () {
    const tbody  = $('shipment-tbody');
    if (!tbody) return;
    const list   = filteredShipments();
    const count  = $('table-count');
    if (count) count.textContent = list.length + ' entries';
    tbody.innerHTML = '';

    list.forEach(s => {
        /* --- main data row --- */
        const tr = el('tr', 'data-row' + (s.id === activeId ? ' active' : ''));
        tr.dataset.id = s.id;
        tr.innerHTML = `
            <td><i class="bi bi-chevron-right chev" id="chev-${s.id}"></i></td>
            <td class="mono">${s.id}</td>
            <td class="mono">${s.container}</td>
            <td>
                <span style="font-size:.73rem;font-weight:600">${s.origin}</span>
                <span style="color:var(--c-muted);font-size:.65rem;margin:0 3px">→</span>
                <span style="font-size:.73rem;font-weight:600">${s.destination}</span>
            </td>
            <td>
                <span style="font-size:.73rem;font-weight:500">${s.carrier}</span>
                <span style="color:var(--c-muted);font-size:.65rem;margin-left:4px">
                    <i class="bi ${s.modeIcon}"></i> ${s.mode}
                </span>
            </td>
            <td class="mono" style="font-size:.72rem">${s.eta}</td>
            <td><span class="chip ${s.customsChip}">${s.customsStatus}</span></td>
            <td>${riskBadge(s.risk)}</td>
            <td>
                <button class="hd-icon-btn btn-detail" data-id="${s.id}" title="Expand detail" style="font-size:.75rem">
                    <i class="bi bi-layout-sidebar-reverse"></i>
                </button>
            </td>
        `;

        /* --- expanded row --- */
        const trExp = el('tr', 'expanded-row');
        trExp.id = 'exp-' + s.id;
        trExp.style.display = 'none';
        const tdExp = el('td');
        tdExp.colSpan = 9;

        const timeline_html = s.timeline.map(t =>
            `<li class="${t.state}">
                <div class="tl-title">${t.label}</div>
                <div class="tl-time">${t.time}</div>
            </li>`).join('');

        const telemetry_html = s.telemetry.map(t =>
            `<tr><td>${t.label}</td><td>${t.val}</td></tr>`).join('');

        const docs_done  = s.documents.filter(d => d.done).length;
        const docs_total = s.documents.length;

        tdExp.innerHTML = `
            <div class="expand-content">
                <div>
                    <div class="expand-section-title"><i class="bi bi-map" style="margin-right:4px"></i>Transit Timeline</div>
                    <ul class="tl">${timeline_html}</ul>
                </div>
                <div>
                    <div class="expand-section-title"><i class="bi bi-thermometer-half" style="margin-right:4px"></i>Live Telemetry</div>
                    <table class="log-tbl">${telemetry_html}</table>
                </div>
                <div>
                    <div class="expand-section-title"><i class="bi bi-file-earmark-check" style="margin-right:4px"></i>Dokumen (${docs_done}/${docs_total})</div>
                    <div style="font-size:.68rem;color:var(--c-muted)">
                        ${s.documents.map(d =>
                            `<div style="padding:2px 0;display:flex;align-items:center;gap:6px">
                                <i class="bi ${d.done ? 'bi-check-circle-fill text-green' : 'bi-circle text-muted'}"></i>
                                ${d.name}
                            </div>`
                        ).join('')}
                    </div>
                </div>
            </div>`;
        trExp.appendChild(tdExp);

        tbody.appendChild(tr);
        tbody.appendChild(trExp);

        /* click row → select shipment */
        tr.addEventListener('click', (e) => {
            if (e.target.closest('.btn-detail')) return;
            selectShipment(s.id);
        });

        /* click detail button → expand row */
        tr.querySelector('.btn-detail').addEventListener('click', () => {
            toggleExpand(s.id);
        });
    });
}

/* ── Toggle expand row ─────────────────────────────────── */
function toggleExpand (id) {
    const expRow  = $('exp-' + id);
    const chevron = $('chev-' + id);
    if (!expRow) return;
    const isOpen = expRow.style.display !== 'none';
    // close all others first
    document.querySelectorAll('.expanded-row').forEach(r => { r.style.display = 'none'; });
    document.querySelectorAll('.chev').forEach(c => c.classList.remove('open'));
    if (!isOpen) {
        expRow.style.display = '';
        if (chevron) chevron.classList.add('open');
    }
}

/* ── Select shipment → update panel ───────────────────── */
function selectShipment (id) {
    activeId = id;
    const s  = SHIPMENTS.find(x => x.id === id);
    if (!s) return;

    /* highlight active row */
    document.querySelectorAll('.data-row').forEach(r => r.classList.toggle('active', r.dataset.id === id));

    /* update detail pane */
    const set = (elId, val) => { const e = $(elId); if (e) e.textContent = val; };
    const html = (elId, val) => { const e = $(elId); if (e) e.innerHTML = val; };

    set('d-id',          s.id);
    set('d-container',   s.container);
    set('d-carrier',     s.carrier);
    set('d-mode',        `${s.mode} (${s.modeIcon.replace('bi-', '').replace('-', ' ')})`);
    set('d-origin',      s.origin);
    set('d-destination', s.destination);
    set('d-eta',         s.eta);
    set('d-progress-pct', s.progress + '%');
    html('d-status',     `<span class="chip ${s.statusChip}">${s.status}</span>`);
    html('d-risk-score', `<span class="${s.riskScore > 60 ? 'text-red' : s.riskScore > 35 ? 'text-orange' : 'text-green'}" style="font-family:var(--f-mono);font-weight:700">${s.riskScore}% — ${s.risk.charAt(0).toUpperCase() + s.risk.slice(1)}</span>`);

    const bar = $('d-progress-bar');
    if (bar) {
        bar.style.width = s.progress + '%';
        bar.className = 'progress-fill ' + (s.progress >= 100 ? 'bg-green' : s.progress >= 60 ? 'bg-blue' : 'bg-yellow');
    }

    /* checklist */
    const cl = $('d-checklist');
    if (cl) {
        cl.innerHTML = s.documents.map(d => `
            <div class="cl-item">
                <span class="cl-item-name">
                    <i class="bi ${d.done ? 'bi-check-circle-fill text-green' : 'bi-circle text-muted'}"></i>
                    ${d.name}
                </span>
                <span class="cl-status ${d.done ? 'text-green' : 'text-muted'}">${d.done ? 'OK' : '—'}</span>
            </div>`).join('');
    }

    /* risk bars */
    const rb = $('risk-bars');
    if (rb) {
        rb.innerHTML = s.risks.map(r => `
            <div class="risk-bar-item">
                <div class="risk-bar-header">
                    <span class="risk-bar-name">${r.label}</span>
                    <span class="risk-bar-pct" style="color:${r.color}">${r.pct}%</span>
                </div>
                <div class="risk-track">
                    <div class="risk-fill" style="width:${r.pct}%;background:${r.color}"></div>
                </div>
            </div>`).join('');
    }

    /* news feed */
    const nf = $('news-feed');
    if (nf) {
        nf.innerHTML = '<div style="font-size:0.7rem;color:var(--c-muted);padding:1rem;text-align:center"><i class="bi bi-arrow-clockwise spin"></i> Loading trade intelligence...</div>';
        
        // Fetch news dynamically for the country of the selected shipment
        fetch(`/api/news?country=${s.iso || 'ID'}`)
            .then(res => res.json())
            .then(newsData => {
                if (newsData.length === 0) {
                    nf.innerHTML = '<div style="font-size:0.7rem;color:var(--c-muted);padding:1rem;text-align:center">No active news alerts for this trade lane.</div>';
                    return;
                }
                nf.innerHTML = newsData.map(n => `
                    <div class="news-card" style="border-left: 3px solid ${n.sentiment === 'Positive' ? '#16A34A' : (n.sentiment === 'Negative' ? '#DC2626' : '#64748B')}">
                        <div class="news-meta">
                            <span class="news-cat" style="font-weight:700">${n.source}</span>
                            <span class="news-time">${n.sentiment} (${n.positive_pct}% Pos)</span>
                        </div>
                        <div class="news-headline" style="font-weight:600;font-size:0.75rem;margin:4px 0">${n.title}</div>
                        <div class="news-desc" style="font-size:0.68rem;color:var(--c-muted);line-height:1.3">${n.description || ''}</div>
                        <div class="news-footer" style="margin-top:5px;font-size:0.6rem;color:var(--c-muted)">
                            <span>Pos matched: ${n.matched_pos.join(', ') || 'none'}</span> | 
                            <span>Neg matched: ${n.matched_neg.join(', ') || 'none'}</span>
                        </div>
                    </div>`).join('');
            })
            .catch(err => {
                nf.innerHTML = '<div style="font-size:0.7rem;color:var(--c-red);padding:1rem;text-align:center">Gagal memuat berita.</div>';
            });
    }

    /* update map */
    updateMapActive(id);

    /* update top stats */
    updateTopStats();

    /* switch to detail tab */
    switchTab('detail');
}

/* ── Tab switching ─────────────────────────────────────── */
function switchTab (name) {
    document.querySelectorAll('.p-tab').forEach(b => b.classList.toggle('active', b.dataset.tab === name));
    document.querySelectorAll('.pane').forEach(p => p.classList.toggle('active', p.id === 'pane-' + name));
}

/* ── Sparkline charts ──────────────────────────────────── */
function renderSparklines () {
    const grid = $('spark-grid');
    if (!grid) return;
    grid.innerHTML = '';

    const currentShipment = SHIPMENTS.find(s => s.id === activeId) || { riskScore: 30, progress: 50 };
    
    // Generate dynamic charts based on active country telemetry
    const SPARKLINES = [
        { label: 'Shipment Vol', val: '847 TEU', color: '#2563EB',  data: [62, 75, 68, 80, 74, 91, 85, 94, 88, 100] },
        { label: 'WCI Index',    val: '$3,248',  color: '#16A34A',  data: [55, 60, 58, 65, 70, 68, 72, 75, 70, 78] },
        { label: 'Exchange Rate',val: currentShipment.iso || 'USD', color: '#EA580C',  data: [70, 72, 68, 74, 76, 72, 78, 74, 80, 77] },
        { label: 'GDP Growth',   val: currentShipment.telemetry.find(t => t.label === 'GDP (WB)')?.val || 'N/A', color: '#7E22CE',  data: [30, 32, 34, 33, 35, 36, 34, 38, 37, 40] },
        { label: 'Inflation',    val: currentShipment.telemetry.find(t => t.label === 'Inflation')?.val || 'N/A', color: '#D97706',  data: [40, 42, 45, 43, 48, 46, 50, 48, 52, 55] },
        { label: 'Risk Trend',   val: `${currentShipment.riskScore}%`, color: '#DC2626',  data: [30, 35, 32, 38, 40, currentShipment.riskScore - 10, currentShipment.riskScore - 5, currentShipment.riskScore] },
    ];

    SPARKLINES.forEach((sp, i) => {
        const card = el('div', 'spark-card');
        const cid  = 'spark-' + i;
        card.innerHTML = `
            <div class="spark-label">${sp.label}</div>
            <div class="spark-val">${sp.val}</div>
            <div class="spark-wrap"><canvas id="${cid}"></canvas></div>`;
        grid.appendChild(card);

        requestAnimationFrame(() => {
            const ctx = document.getElementById(cid);
            if (!ctx) return;
            if (sparkCharts[cid]) { sparkCharts[cid].destroy(); }
            sparkCharts[cid] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: sp.data.map((_, j) => j),
                    datasets: [{
                        data: sp.data, borderColor: sp.color, borderWidth: 1.5,
                        tension: 0.4, pointRadius: 0, fill: true,
                        backgroundColor: sp.color.replace(')', ',.08)').replace('rgb', 'rgba').replace('#', '').length > 10
                            ? sp.color + '14'
                            : sp.color + '14'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, animation: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: { x: { display: false }, y: { display: false } }
                }
            });
        });
    });
}

/* ── Bind filters ──────────────────────────────────────── */
function bindFilters () {
    ['filter-region', 'filter-transport', 'filter-risk'].forEach(groupId => {
        const group = $(groupId);
        if (!group) return;
        /* Support both old (.sb-btn-filter) and new (.sb-btn) class names */
        group.querySelectorAll('.sb-btn, .sb-btn-filter').forEach(btn => {
            btn.addEventListener('click', () => {
                group.querySelectorAll('.sb-btn, .sb-btn-filter').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                if (groupId === 'filter-region')    filterRegion    = btn.dataset.val;
                if (groupId === 'filter-transport') filterTransport = btn.dataset.val;
                if (groupId === 'filter-risk')      filterRisk      = btn.dataset.val;
                renderTable();
                updateCounts();
            });
        });
    });

    const searchEl = $('search-input');
    if (searchEl) {
        searchEl.addEventListener('input', () => {
            searchQuery = searchEl.value.trim();
            renderTable();
        });
    }
}

/* ── Bind tabs ─────────────────────────────────────────── */
function bindTabs () {
    document.querySelectorAll('.p-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            switchTab(btn.dataset.tab);
            if (btn.dataset.tab === 'analytics') { renderSparklines(); }
        });
    });
}

/* ── Init ──────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    bindFilters();
    bindTabs();
    
    // Load dynamic data from API
    loadDashboardData();

    // Bind Country Selector (Global Country Dashboard)
    const countrySel = $('country-select');
    if (countrySel) {
        countrySel.addEventListener('change', async (e) => {
            const code = e.target.value;
            ACTIVE_COUNTRY = code;
            
            // Re-fetch news for the selected country
            const newsRes = await fetch(`/api/news?country=${code}`);
            const newsData = await newsRes.json();
            
            // Find active shipment corresponding to this country and switch
            const matchingShipment = SHIPMENTS.find(s => s.iso === code);
            if (matchingShipment) {
                selectShipment(matchingShipment.id);
            }

            // Render live news with sentiment analysis in Risk pane
            const nf = $('news-feed');
            if (nf) {
                nf.innerHTML = newsData.map(n => `
                    <div class="news-card" style="border-left: 3px solid ${n.sentiment === 'Positive' ? '#16A34A' : (n.sentiment === 'Negative' ? '#DC2626' : '#64748B')}">
                        <div class="news-meta">
                            <span class="news-cat">${n.source}</span>
                            <span class="news-time">${n.sentiment} (${n.positive_pct}% Pos)</span>
                        </div>
                        <div class="news-headline" style="font-weight:600;font-size:0.75rem">${n.title}</div>
                        <div class="news-desc" style="font-size:0.65rem;color:var(--c-muted);margin-top:3px">${n.description || ''}</div>
                        <div class="news-footer" style="margin-top:5px;font-size:0.6rem">
                            <span>Pos matched: ${n.matched_pos.join(', ') || 'none'}</span><br>
                            <span>Neg matched: ${n.matched_neg.join(', ') || 'none'}</span>
                        </div>
                    </div>`).join('');
            }
        });
    }
});

/* ── Leaflet Map ─────────────────────────────────────────────
   Strategy: set explicit px height on #world-map via JS right
   before calling L.map(), then invalidateSize() repeatedly.
   ─────────────────────────────────────────────────────────── */
let map        = null;
let mapMarkers = {};
let mapLines   = {};

const STATUS_COLOR = {
    'In Transit': '#2563EB',
    'Delayed':    '#D97706',
    'Alert':      '#DC2626',
    'Arrived':    '#16A34A',
};

function makeIcon (status, isActive) {
    const color = STATUS_COLOR[status] || '#64748B';
    const size  = isActive ? 15 : 10;
    const glow  = isActive
        ? `box-shadow:0 0 0 5px ${color}35,0 2px 6px rgba(0,0,0,.35)`
        : 'box-shadow:0 1px 4px rgba(0,0,0,.3)';
    return L.divIcon({
        className: '',
        html: `<div style="width:${size}px;height:${size}px;border-radius:50%;
                    background:${color};border:2px solid #fff;${glow};
                    transition:all .15s;"></div>`,
        iconSize:   [size, size],
        iconAnchor: [size / 2, size / 2],
    });
}

function initMap () {
    if (typeof L === 'undefined') {
        console.error('Leaflet (L) not loaded — map cannot init');
        return;
    }

    const mapEl = document.getElementById('world-map');
    if (!mapEl) {
        console.error('#world-map element not found');
        return;
    }

    /* ── Force explicit pixel height before Leaflet touches the div ── */
    const MAP_H = 200;
    mapEl.style.height = MAP_H + 'px';
    mapEl.style.width  = '100%';

    /* ── Destroy previous instance if re-initialising ── */
    if (map) {
        map.remove();
        map        = null;
        mapMarkers = {};
        mapLines   = {};
    }

    /* ── Create map ── */
    map = L.map('world-map', {
        center:             [20, 110],
        zoom:               3,
        zoomControl:        true,
        attributionControl: false,
        scrollWheelZoom:    true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 10,
        minZoom: 1,
    }).addTo(map);

    /* ── Destination hub (Tanjung Priok) ── */
    const hubIcon = L.divIcon({
        className: '',
        html: `<div style="width:12px;height:12px;border-radius:50%;
                    background:#F97316;border:2px solid #fff;
                    box-shadow:0 0 0 4px rgba(249,115,22,.25),0 2px 6px rgba(0,0,0,.3);">
               </div>`,
        iconSize:   [12, 12],
        iconAnchor: [6, 6],
    });
    L.marker([-6.104, 106.881], { icon: hubIcon })
        .addTo(map)
        .bindPopup(`<div class="map-popup">
            <div class="map-popup-id">DESTINATION HUB</div>
            <div class="map-popup-route">🏭 Tanjung Priok — Jakarta</div>
            <div class="map-popup-meta"><span>Indonesia</span><span style="color:#F97316;font-weight:700;">IMPORT HUB</span></div>
        </div>`, { closeButton: false });

    /* ── Add all shipments ── */
    SHIPMENTS.forEach(s => addShipmentToMap(s));

    /* ── Update active-count badge ── */
    const badge = document.getElementById('map-count');
    if (badge) badge.textContent = SHIPMENTS.length;

    /* ── Fix tiles: call invalidateSize after layout settles ── */
    [50, 200, 500, 1000].forEach(ms =>
        setTimeout(() => { if (map) map.invalidateSize({ debounceMoveend: true }); }, ms)
    );

    /* ── Fit map to all markers after a short delay ── */
    setTimeout(() => {
        if (!map || !SHIPMENTS.length) return;
        const latlngs = SHIPMENTS.map(s => s.currentCoord).filter(Boolean);
        latlngs.push([-6.104, 106.881]);
        if (latlngs.length > 1) map.fitBounds(latlngs, { padding: [30, 30], maxZoom: 5 });
    }, 600);
}

function addShipmentToMap (s) {
    if (!map) return;
    const orig = s.originCoord   || null;
    const curr = s.currentCoord  || orig;
    const dest = s.destCoord     || [-6.104, 106.881];
    if (!curr) return;

    const color    = STATUS_COLOR[s.status] || '#64748B';
    const isActive = s.id === activeId;

    /* Dashed polyline: origin → current → dest */
    if (orig) {
        const line = L.polyline([orig, curr, dest], {
            color,
            weight:    isActive ? 2.5 : 1.5,
            opacity:   isActive ? 0.85 : 0.4,
            dashArray: '6 5',
        }).addTo(map);
        mapLines[s.id] = line;

        /* Small dot at origin */
        L.circleMarker(orig, {
            radius: 3, color, fillColor: color,
            fillOpacity: 0.5, weight: 1, opacity: 0.5,
        }).addTo(map);
    }

    /* Current-position marker */
    const marker = L.marker(curr, { icon: makeIcon(s.status, isActive) })
        .addTo(map)
        .bindPopup(`<div class="map-popup">
            <div class="map-popup-id">${s.id} · ${s.container}</div>
            <div class="map-popup-route">${s.origin} → ${s.destination}</div>
            <div class="map-popup-meta">
                <span>${s.carrier} · ${s.mode}</span>
                <span class="risk risk-${s.risk}">${s.riskScore}%</span>
            </div>
        </div>`, { closeButton: false, offset: [0, -7] });

    marker.on('click', () => { selectShipment(s.id); });
    mapMarkers[s.id] = marker;
}

function updateMapActive (id) {
    SHIPMENTS.forEach(s => {
        const m = mapMarkers[s.id];
        const l = mapLines[s.id];
        const active = s.id === id;
        if (m) m.setIcon(makeIcon(s.status, active));
        if (l) l.setStyle({ opacity: active ? 0.9 : 0.35, weight: active ? 2.5 : 1.5 });
    });
    const s = SHIPMENTS.find(x => x.id === id);
    if (s && map && s.currentCoord) {
        map.flyTo(s.currentCoord, Math.max(map.getZoom(), 4), { duration: 0.7 });
    }
}


