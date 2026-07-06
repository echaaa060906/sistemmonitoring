<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Rute & Simulasi - Supply Chain Risk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-bg: #343a40;
            --bg-color: #f4f6f9;
        }

        body { font-family: 'Roboto', sans-serif; background-color: var(--bg-color); display: flex; width: 100vw; height: 100vh; overflow: hidden; }
        
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

        #main-content { flex: 1; display: flex; flex-direction: column; }
        #topbar { background: #fff; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
        
        #map-container { flex: 1; position: relative; }
        #world-map { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
        
        #sim-panel { position: absolute; top: 20px; left: 20px; z-index: 1000; background: #fff; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 340px; }
        
        .route-card {
            margin-bottom: .5rem; background: #fff;
            border: 1px solid #dee2e6; border-radius: 8px; padding: .7rem .85rem;
            cursor: pointer; transition: all .18s; position: relative; overflow: hidden;
        }
        .route-card::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0;
            width: 4px; border-radius: 0 2px 2px 0; background: #3B82F6;
        }
        .route-card.status-delayed::before { background: #EAB308; }
        .route-card.status-alert::before   { background: #EF4444; }
        .route-card.status-arrived::before { background: #22C55E; }
        .route-card:hover { background: #f8f9fa; border-color: #3B82F6; transform: translateX(2px); }
        .route-card.active { background: #e0f2fe; border-color: #3B82F6; }
        
        .rc-id    { font-family: monospace; font-size: .75rem; color: #6c757d; margin-bottom: 3px; }
        .rc-route { font-size: .85rem; font-weight: 600; color: #212529; display: flex; align-items: center; gap: 4px; margin-bottom: 5px; }
        .rc-meta  { display: flex; justify-content: space-between; align-items: center; }
        .rc-carrier { font-size: .75rem; color: #6c757d; }
        .rc-badge { font-size: .7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; }
        
        .badge-transit { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-delayed { background: #fef9c3; color: #ca8a04; border: 1px solid #fde047; }
        .badge-alert   { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-arrived { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        
        #info-panel {
            position: absolute; top: 20px; right: 20px; width: 320px;
            background: #fff;
            border: 1px solid #e2e8f0; border-radius: 10px; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .ip-header { padding: 1rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .ip-title { font-size: 1rem; font-weight: 700; color: #212529; }
        .ip-close { background: none; border: none; color: #6c757d; cursor: pointer; font-size: 1.2rem; }
        .ip-close:hover { color: #dc3545; }
        .ip-body  { padding: 1rem; }
        .ip-row   { display: flex; justify-content: space-between; align-items: flex-start; padding: 6px 0; border-bottom: 1px solid #f8f9fa; }
        .ip-row:last-child { border-bottom: none; }
        .ip-lbl   { font-size: .8rem; color: #6c757d; }
        .ip-val   { font-size: .85rem; font-weight: 600; color: #212529; text-align: right; max-width: 60%; }
        .ip-val.mono { font-family: monospace; }
        
        .progress-track { height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-top: 12px; }
        .progress-fill  { height: 100%; border-radius: 3px; transition: width .5s; }
        
        #stats-overlay {
            position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 10px; z-index: 999;
        }
        .stat-pill {
            background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0; border-radius: 20px;
            padding: 8px 16px; display: flex; align-items: center; gap: 8px; font-size: .85rem;
        }
        .stat-pill-num { font-family: monospace; font-weight: 700; color: #212529; }
        .stat-pill-lbl { color: #6c757d; }
        
        .map-popup { padding: .5rem; min-width: 180px; }
        .mp-id    { font-family: monospace; font-size: .75rem; color: #6c757d; margin-bottom: 4px; }
        .mp-route { font-size: .9rem; font-weight: 600; color: #212529; margin-bottom: 6px; }
        .mp-meta  { display: flex; justify-content: space-between; align-items: center; font-size: .75rem; color: #6c757d; }
        
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(249,115,22, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(249,115,22, 0); } 100% { box-shadow: 0 0 0 0 rgba(249,115,22, 0); } }
    </style>
</head>
<body>
    <aside id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-globe-americas"></i> SCM Project
        </div>
        <ul class="sidebar-nav">
            <li><a href="/" class="sidebar-link"><i class="bi bi-speedometer2"></i> Global Dashboard</a></li>
            <li><a href="/map" class="sidebar-link active"><i class="bi bi-map"></i> Peta Rute</a></li>
            <li><a href="/ports" class="sidebar-link"><i class="bi bi-geo-alt"></i> Port Locations</a></li>
            <li><a href="/comparison" class="sidebar-link"><i class="bi bi-intersect"></i> Comparison</a></li>
            <li><a href="/favorites" class="sidebar-link"><i class="bi bi-star"></i> Favorites List</a></li>
            <li><a href="/admin" class="sidebar-link"><i class="bi bi-gear"></i> Admin Panel</a></li>
        </ul>
    </aside>

    <main id="main-content">
        <header id="topbar">
            <div>
                <h4 class="mb-0 text-dark">Peta Rute & Simulasi</h4>
                <small class="text-muted">Real-time supply chain monitoring · Hub: Tanjung Priok, Indonesia</small>
            </div>
        </header>

        <div id="map-container">
            <!-- Left Panel -->
            <div id="sim-panel">
                <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="sim-tab" data-bs-toggle="tab" data-bs-target="#sim-pane" type="button" role="tab">Simulasi Manual</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rute-tab" data-bs-toggle="tab" data-bs-target="#rute-pane" type="button" role="tab">Daftar Rute Aktif</button>
                  </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                  <!-- Simulation Tab -->
                  <div class="tab-pane fade show active" id="sim-pane" role="tabpanel" tabindex="0">
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted mb-1">Negara Asal</label>
                          <select id="sim-origin" class="form-select"><option value="">Pilih Asal</option></select>
                      </div>
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted mb-1">Negara Tujuan</label>
                          <select id="sim-dest" class="form-select"><option value="">Pilih Tujuan</option></select>
                      </div>
                      <button class="btn btn-primary w-100 fw-bold" onclick="startCustomSim()">Mulai Simulasi</button>
                  </div>
                  <!-- Active Routes Tab -->
                  <div class="tab-pane fade" id="rute-pane" role="tabpanel" tabindex="0">
                      <div id="route-list" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                          <div class="text-center p-3"><div class="spinner-border text-primary spinner-border-sm"></div></div>
                      </div>
                  </div>
                </div>
            </div>
            
            <!-- Info Panel -->
            <div id="info-panel" style="display:none;">
                <div class="ip-header">
                    <span class="ip-title" id="ip-title">—</span>
                    <button class="ip-close" onclick="closeInfoPanel()"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="ip-body" id="ip-body"></div>
            </div>
            
            <!-- Statistics Bottom -->
            <div id="stats-overlay">
                <div class="stat-pill"><span class="badge" style="background:#3B82F6">&nbsp;</span><span class="stat-pill-num" id="cnt-transit">0</span><span class="stat-pill-lbl">In Transit</span></div>
                <div class="stat-pill"><span class="badge" style="background:#EAB308">&nbsp;</span><span class="stat-pill-num" id="cnt-delayed">0</span><span class="stat-pill-lbl">Delayed</span></div>
                <div class="stat-pill"><span class="badge" style="background:#EF4444">&nbsp;</span><span class="stat-pill-num" id="cnt-alert">0</span><span class="stat-pill-lbl">Alert</span></div>
                <div class="stat-pill"><span class="badge" style="background:#22C55E">&nbsp;</span><span class="stat-pill-num" id="cnt-arrived">0</span><span class="stat-pill-lbl">Arrived</span></div>
            </div>
            
            <div id="world-map"></div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
    const STATUS_COLOR = { 'In Transit':'#3B82F6', 'Delayed':'#EAB308', 'Alert':'#EF4444', 'Arrived':'#22C55E' };
    const STATUS_CLASS = { 'In Transit':'transit', 'Delayed':'delayed', 'Alert':'alert', 'Arrived':'arrived' };
    const BADGE_CLASS  = { 'In Transit':'badge-transit', 'Delayed':'badge-delayed', 'Alert':'badge-alert', 'Arrived':'badge-arrived' };
    const CENTROIDS    = { 'ID':[-2.5,117.0],'CN':[35.9,104.2],'DE':[51.2,10.4],'AU':[-25.3,133.8],'US':[37.1,-95.7],'JP':[36.2,138.3],'SG':[1.35,103.8],'IN':[20.6,78.9],'KR':[35.9,127.8] };
    const DEST         = [-6.104, 106.881];
    
    let map=null, markers={}, polylines={}, shipments=[], activeId=null;
    
    function initMap() {
        map = L.map('world-map', { center:[5,80], zoom:3, zoomControl:true, attributionControl:false, minZoom:2, maxZoom:12 });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        // Hub marker
        L.marker(DEST, { icon: L.divIcon({ className:'', html:`<div style="width:14px;height:14px;border-radius:50%;background:#F97316;border:3px solid #fff;box-shadow:0 0 0 4px rgba(249,115,22,.3);"></div>`, iconSize:[14,14], iconAnchor:[7,7] }) })
          .addTo(map).bindPopup(`<div class="map-popup"><div class="mp-id">HUB · DESTINATION</div><div class="mp-route">🏭 Tanjung Priok, Jakarta</div><div class="mp-meta"><span>Indonesia</span><span style="color:#F97316;font-weight:700;">IMPORT HUB</span></div></div>`, {closeButton:false});
    }
    
    function makeIcon(status, isActive) {
        const color=STATUS_COLOR[status]||'#6c757d', size=isActive?16:11;
        const glow = isActive ? `box-shadow:0 0 0 5px ${color}40,0 2px 8px rgba(0,0,0,.5)` : 'box-shadow:0 2px 6px rgba(0,0,0,.4)';
        return L.divIcon({ className:'', html:`<div style="width:${size}px;height:${size}px;border-radius:50%;background:${color};border:2px solid #fff;${glow};transition:all .2s;"></div>`, iconSize:[size,size], iconAnchor:[size/2,size/2] });
    }
    
    function addToMap(s) {
        if (!s.origin_coord) return;
        const color=STATUS_COLOR[s.status]||'#6c757d', isActive=s.id===activeId, curr=s.current_coord||s.origin_coord;
        const dest = s.dest_coord || DEST;
        const poly = L.polyline([s.origin_coord, curr, dest], { color, weight:isActive?2.5:1.5, opacity:isActive?0.85:0.35, dashArray:'6 5' }).addTo(map);
        polylines[s.id] = poly;
        L.circleMarker(s.origin_coord, { radius:4, color, fillColor:color, fillOpacity:0.5, weight:1, opacity:0.4 }).addTo(map);
        const marker = L.marker(curr, { icon:makeIcon(s.status, isActive) }).addTo(map)
            .bindPopup(`<div class="map-popup"><div class="mp-id">${s.id} · ${s.container}</div><div class="mp-route">${s.origin} → ${s.dest_name || 'Tanjung Priok'}</div><div class="mp-meta"><span>${s.carrier}</span><span style="color:${color};font-weight:700;">${s.status}</span></div></div>`, {closeButton:false, offset:[0,-8]});
        marker.on('click', ()=>selectShipment(s.id));
        markers[s.id] = marker;
    }
    
    function selectShipment(id) {
        activeId=id;
        const s=shipments.find(x=>x.id===id);
        if(!s) return;
        document.querySelectorAll('.route-card').forEach(c=>c.classList.toggle('active',c.dataset.id===id));
        shipments.forEach(sh=>{
            const m=markers[sh.id]; if(m) m.setIcon(makeIcon(sh.status, sh.id===id));
            const p=polylines[sh.id]; if(p) p.setStyle({weight:sh.id===id?2.5:1.5, opacity:sh.id===id?0.9:0.3});
        });
        const curr=s.current_coord||s.origin_coord;
        if(curr) map.flyTo(curr, Math.max(map.getZoom(),4), {duration:1});
        showInfoPanel(s);
    }
    
    function showInfoPanel(s) {
        const color=STATUS_COLOR[s.status]||'#6c757d';
        document.getElementById('ip-title').textContent=s.id;
        document.getElementById('ip-title').style.color=color;
        document.getElementById('ip-body').innerHTML=`
            <div class="ip-row"><span class="ip-lbl">Container</span><span class="ip-val mono">${s.container}</span></div>
            <div class="ip-row"><span class="ip-lbl">Asal</span><span class="ip-val">${s.origin}</span></div>
            <div class="ip-row"><span class="ip-lbl">Tujuan</span><span class="ip-val">${s.dest_name || 'Tanjung Priok, ID'}</span></div>
            <div class="ip-row"><span class="ip-lbl">Carrier</span><span class="ip-val">${s.carrier}</span></div>
            <div class="ip-row"><span class="ip-lbl">Mode</span><span class="ip-val">${s.mode}</span></div>
            <div class="ip-row"><span class="ip-lbl">ETA</span><span class="ip-val mono">${s.eta}</span></div>
            <div class="ip-row"><span class="ip-lbl">Status</span><span class="ip-val" style="color:${color};font-weight:700;">${s.status}</span></div>
            <div class="ip-row"><span class="ip-lbl">Risiko</span><span class="ip-val" style="color:${s.risk_score>60?'#EF4444':s.risk_score>35?'#EAB308':'#22C55E'}">${s.risk_score}% (${s.risk})</span></div>
            <div class="ip-row"><span class="ip-lbl">Progress</span><span class="ip-val mono">${s.progress}%</span></div>
            <div class="progress-track"><div class="progress-fill" style="width:${s.progress}%;background:${color};"></div></div>`;
        document.getElementById('info-panel').style.display='block';
    }
    
    function closeInfoPanel() { document.getElementById('info-panel').style.display='none'; }
    
    function renderRouteList() {
        document.getElementById('route-list').innerHTML = shipments.map(s=>`
            <div class="route-card status-${STATUS_CLASS[s.status]||'transit'}" data-id="${s.id}" onclick="selectShipment('${s.id}')">
                <div class="rc-id">${s.id} · ${s.container}</div>
                <div class="rc-route">${s.origin} <i class="bi bi-arrow-right" style="font-size:.65rem;color:#6c757d;"></i> ${s.dest_name ? s.dest_name.split(',')[0] : 'Priok'}</div>
                <div class="rc-meta"><span class="rc-carrier">${s.carrier}</span><span class="rc-badge ${BADGE_CLASS[s.status]||'badge-transit'}">${s.status}</span></div>
            </div>`).join('');
        document.getElementById('cnt-transit').textContent=shipments.filter(s=>s.status==='In Transit').length;
        document.getElementById('cnt-delayed').textContent=shipments.filter(s=>s.status==='Delayed').length;
        document.getElementById('cnt-alert').textContent  =shipments.filter(s=>s.status==='Alert').length;
        document.getElementById('cnt-arrived').textContent=shipments.filter(s=>s.status==='Arrived').length;
    }
    
    async function boot() {
        initMap();
        try {
            const res  = await fetch('/api/countries');
            const data = await res.json();
            
            // Populate custom sim dropdowns
            const originSel = document.getElementById('sim-origin');
            const destSel = document.getElementById('sim-dest');
            data.forEach(c => {
                if(CENTROIDS[c.iso_code]) {
                    const opt = `<option value="${c.iso_code}">${c.name}</option>`;
                    originSel.innerHTML += opt;
                    destSel.innerHTML += opt;
                }
            });
            destSel.innerHTML += `<option value="IDTPP">Tanjung Priok, ID</option>`;
            CENTROIDS["IDTPP"] = DEST;
            window.allCountriesData = data;
            
            shipments = data.map((c,i)=>{
                const orig=CENTROIDS[c.iso_code]||[0,0];
                const prog=c.risk.total_risk>55?45:(c.risk.total_risk>35?72:100);
                const stat=c.risk.total_risk>55?'Delayed':(c.risk.total_risk>35?'In Transit':'Arrived');
                const t=prog/100;
                const cur=[orig[0]+(DEST[0]-orig[0])*t*0.75, orig[1]+(DEST[1]-orig[1])*t*0.75];
                return { id:`SH-2026-${9020+i}`, container:`CNTR-${c.iso_code}-${1029+i}`,
                    carrier:({ID:'Samudera Indonesia',DE:'Hapag-Lloyd',CN:'COSCO',AU:'ANL',US:'ONE Line'}[c.iso_code]||'Global Carrier'),
                    mode:c.iso_code==='DE'?'Land (Truck)':'Sea (Vessel)',
                    origin:c.name, eta:'2026-07-15', status:stat, risk:c.risk.class,
                    risk_score:c.risk.total_risk, progress:prog, origin_coord:orig, current_coord:cur };
            });
            renderRouteList();
            shipments.forEach(s=>addToMap(s));
            if(shipments.length>0) { activeId=shipments[0].id; selectShipment(activeId); }
            const bounds=[...shipments.map(s=>s.origin_coord).filter(Boolean), DEST];
            if(bounds.length>1) map.fitBounds(bounds,{padding:[60,60]});
        } catch(err) {
            console.error('Error loading data:',err);
            document.getElementById('route-list').innerHTML='<div style="padding:1rem;font-size:.8rem;color:#dc3545;text-align:center;"><i class="bi bi-exclamation-circle"></i> Gagal memuat data</div>';
        }
        setTimeout(()=>map&&map.invalidateSize(),300);
        setTimeout(()=>map&&map.invalidateSize(),900);
    }
    
    let simInterval = null;
    function startCustomSim() {
        const o = document.getElementById('sim-origin').value;
        const d = document.getElementById('sim-dest').value;
        if(!o || !d || o === d) return alert("Pilih asal dan tujuan yang berbeda!");
    
        const cOrigin = CENTROIDS[o];
        const cDest = CENTROIDS[d];
        const simId = 'SIM-' + Math.floor(Math.random()*10000);
        
        let oName = o; let dName = d;
        if(window.allCountriesData) {
            const co = window.allCountriesData.find(x => x.iso_code === o);
            if(co) oName = co.name;
            const cd = window.allCountriesData.find(x => x.iso_code === d);
            if(cd) dName = cd.name;
        }
        if (d === 'IDTPP') dName = 'Tanjung Priok, ID';
    
        const s = { 
            id: simId, container: `CUST-${o}-${d}`, carrier: 'Simulator Line', mode: 'Express',
            origin: oName, dest_name: dName, eta: 'Real-time', status: 'In Transit', risk: 'Low', risk_score: 10,
            progress: 0, origin_coord: cOrigin, current_coord: cOrigin, dest_coord: cDest
        };
    
        shipments.unshift(s);
        
        const color = '#F97316';
        const poly = L.polyline([cOrigin, cOrigin, cDest], { color, weight:2.5, opacity:0.85, dashArray:'4 6' }).addTo(map);
        polylines[simId] = poly;
        
        const iconHtml = `<div style="width:18px;height:18px;border-radius:50%;background:${color};border:2px solid #fff;box-shadow:0 0 10px ${color},0 2px 8px rgba(0,0,0,.5);animation:pulse 1s infinite;"></div>`;
        const marker = L.marker(cOrigin, { icon: L.divIcon({ className:'', html:iconHtml, iconSize:[18,18], iconAnchor:[9,9] }) }).addTo(map)
            .bindPopup(`<div class="map-popup"><div class="mp-id">${simId}</div><div class="mp-route">${oName} → ${dName}</div><div class="mp-meta"><span style="color:${color};font-weight:700;">Simulating...</span></div></div>`, {closeButton:false, offset:[0,-8]});
        markers[simId] = marker;
        marker.on('click', ()=>selectShipment(simId));
    
        renderRouteList();
        
        const ruteTab = new bootstrap.Tab(document.getElementById('rute-tab'));
        ruteTab.show();
        selectShipment(simId);
    
        let t = 0;
        const step = 0.005; 
        if(simInterval) clearInterval(simInterval);
        
        simInterval = setInterval(() => {
            t += step;
            if (t >= 1) {
                t = 1;
                clearInterval(simInterval);
                s.status = 'Arrived';
                s.progress = 100;
                renderRouteList();
                marker.setPopupContent(`<div class="map-popup"><div class="mp-id">${simId}</div><div class="mp-route">${oName} → ${dName}</div><div class="mp-meta"><span style="color:#22C55E;font-weight:700;">Arrived</span></div></div>`);
            }
            s.progress = Math.floor(t * 100);
            
            const lat = cOrigin[0] + (cDest[0] - cOrigin[0]) * t;
            const lng = cOrigin[1] + (cDest[1] - cOrigin[1]) * t;
            s.current_coord = [lat, lng];
    
            marker.setLatLng([lat, lng]);
            poly.setLatLngs([cOrigin, [lat, lng], cDest]);
            
            if(activeId === simId && document.getElementById('info-panel').style.display !== 'none') {
                document.querySelector('#info-panel .progress-fill').style.width = `${s.progress}%`;
                document.querySelectorAll('#info-panel .ip-val.mono')[2].textContent = `${s.progress}%`; 
                if(t===1) {
                    document.querySelectorAll('#info-panel .ip-val')[6].textContent = 'Arrived';
                    document.querySelectorAll('#info-panel .ip-val')[6].style.color = '#22C55E';
                    document.querySelector('#info-panel .progress-fill').style.background = '#22C55E';
                }
            }
        }, 50);
    }
    
    window.addEventListener('load', boot);
    </script>
</body>
</html>
