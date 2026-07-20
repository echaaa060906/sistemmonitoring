const document = {
    getElementById: (id) => ({
        value: '',
        innerHTML: '',
        textContent: '',
        style: {},
        classList: { toggle: ()=>{} }
    }),
    querySelectorAll: () => [],
    querySelector: () => ({ style: {} })
};
const window = { addEventListener: (e, cb) => cb() };
const L = {
    map: () => ({ addTo: ()=>({bindPopup:()=>({on:()=>({setIcon:()=>({setLatLng:()=>({setPopupContent:()=>({setStyle:()=>({setLatLngs:()=>{}})})})})})})})})}),
    tileLayer: () => ({ addTo: ()=>{} }),
    marker: () => ({ addTo: ()=>({bindPopup:()=>({on:()=>()})}) }),
    divIcon: () => {},
    circleMarker: () => ({ addTo: ()=>{} }),
    polyline: () => ({ addTo: ()=>{} }),
};
const fetch = async () => ({ json: async () => ([{iso_code:'ID', name:'Indonesia'}]) });
const alert = console.log;
const bootstrap = { Tab: class { show(){} } };


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
                const opt = `<option value="${c.iso_code}">${c.name}</option>`;
                originSel.innerHTML += opt;
                destSel.innerHTML += opt;
            });
            destSel.innerHTML += `<option value="IDTPP">Tanjung Priok, ID</option>`;
            CENTROIDS["IDTPP"] = DEST;
            window.allCountriesData = data;
            
            // Only create default dummy shipments for hardcoded centroids
            const defaultISOs = Object.keys(CENTROIDS).filter(k => k !== 'IDTPP');
            shipments = defaultISOs.map((iso, i) => {
                const c = data.find(x => x.iso_code === iso) || {name: iso};
                const orig = CENTROIDS[iso];
                const prog = Math.floor(Math.random() * 60) + 20; // 20-80%
                const stat = prog > 70 ? 'In Transit' : (prog < 40 ? 'Delayed' : 'In Transit');
                const t = prog / 100;
                const cur = [orig[0]+(DEST[0]-orig[0])*t*0.75, orig[1]+(DEST[1]-orig[1])*t*0.75];
                return { id:`SH-2026-${9020+i}`, container:`CNTR-${iso}-${1029+i}`,
                    carrier:({ID:'Samudera Indonesia',DE:'Hapag-Lloyd',CN:'COSCO',AU:'ANL',US:'ONE Line'}[iso]||'Global Carrier'),
                    mode:iso==='DE'?'Land (Truck)':'Sea (Vessel)',
                    origin:c.name, eta:'2026-07-15', status:stat, risk:'Medium',
                    risk_score: 45, progress:prog, origin_coord:orig, current_coord:cur };
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
    async function startCustomSim() {
        const o = document.getElementById('sim-origin').value;
        const d = document.getElementById('sim-dest').value;
        if(!o || !d || o === d) return alert("Pilih asal dan tujuan yang berbeda!");
    
        const simId = 'SIM-' + Math.floor(Math.random()*10000);
        
        let oName = o; let dName = d;
        if(window.allCountriesData) {
            const co = window.allCountriesData.find(x => x.iso_code === o);
            if(co) oName = co.name;
            const cd = window.allCountriesData.find(x => x.iso_code === d);
            if(cd) dName = cd.name;
        }
        if (d === 'IDTPP') dName = 'Tanjung Priok, ID';
        
        // Fetch dynamic coordinates if not cached in CENTROIDS
        let cOrigin = CENTROIDS[o];
        if (!cOrigin) {
            try {
                const r = await fetch('/api/country/' + o);
                const dta = await r.json();
                if (dta && dta.coords) cOrigin = dta.coords;
            } catch(e) { console.error('Fetch error', e); }
        }
        
        let cDest = CENTROIDS[d];
        if (!cDest && d !== 'IDTPP') {
            try {
                const r = await fetch('/api/country/' + d);
                const dta = await r.json();
                if (dta && dta.coords) cDest = dta.coords;
            } catch(e) { console.error('Fetch error', e); }
        }
        
        // Fallbacks just in case
        if (!cOrigin) cOrigin = [0, 0];
        if (!cDest) cDest = DEST;
    
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
    

boot().catch(console.error);