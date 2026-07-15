/**
 * Global Dashboard JS
 * Handles fetching API data and rendering the dashboard.
 */

let currentCountry = 'ID';
let map = null;
let charts = {};

document.addEventListener('DOMContentLoaded', () => {
    initMap();
    initCharts();
    
    document.getElementById('countrySelect').addEventListener('change', (e) => {
        currentCountry = e.target.value;
        loadDashboardData();
        loadNewsData();
    });

    // Initial load
    loadDashboardData();
    loadNewsData();
});

// -- MAP LOGIC --
function initMap() {
    map = L.map('weather-map').setView([20, 0], 2);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors © CARTO'
    }).addTo(map);
}

function updateMap(weatherData, coords, iso) {
    if (!map) return;
    
    // Clear previous layers
    map.eachLayer((layer) => {
        if (layer instanceof L.Circle || layer instanceof L.Marker) {
            map.removeLayer(layer);
        }
    });
    
    map.flyTo(coords, 4, { duration: 1.5 });
    
    // Draw weather zones based on risk
    if (weatherData) {
        // Rain zone
        if (weatherData.precipitation > 0) {
            L.circle(coords, {
                color: '#2563eb', fillColor: '#2563eb', fillOpacity: 0.2, radius: 150000 + (weatherData.precipitation * 10000)
            }).addTo(map).bindPopup('Rain Zone (' + weatherData.precipitation + 'mm)');
        }
        
        if (weatherData.storm_risk > 30 || weatherData.gusts > 25) {
            L.circle([coords[0] + 0.5, coords[1] + 0.5], {
                color: '#dc2626', fillColor: '#dc2626', fillOpacity: 0.4, radius: 100000 + (weatherData.gusts * 2000)
            }).addTo(map).bindPopup('Storm/Gust Warning (' + weatherData.gusts + 'kt)');
        }
    }
}

function getCoords(iso) {
    const centroids = {
        'ID': [-2.5, 118], 'DE': [51.16, 10.45], 'CN': [35.86, 104.19],
        'AU': [-25.27, 133.77], 'US': [37.09, -95.71]
    };
    return centroids[iso] || [0, 0];
}

// -- CHARTS LOGIC --
function initCharts() {
    const commonOpts = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };
    
    charts.currency = new Chart(document.getElementById('currencyChart'), {
        type: 'line',
        data: { labels: [], datasets: [{ label: 'Rate vs USD', data: [], borderColor: '#2563eb', tension: 0.3 }] },
        options: commonOpts
    });
    
    charts.gdp = new Chart(document.getElementById('gdpChart'), {
        type: 'bar',
        data: { labels: ['2023', '2024', '2025', '2026'], datasets: [{ label: 'GDP (T)', data: [], backgroundColor: '#10b981' }] },
        options: { ...commonOpts, plugins: { title: { display: true, text: 'GDP Trend' } } }
    });
    
    charts.inflation = new Chart(document.getElementById('inflationChart'), {
        type: 'line',
        data: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], datasets: [{ label: 'Inflation %', data: [], borderColor: '#d97706', tension: 0.1 }] },
        options: { ...commonOpts, plugins: { title: { display: true, text: 'Inflation Trend' } } }
    });
    
    charts.risk = new Chart(document.getElementById('riskTrendChart'), {
        type: 'line',
        data: { labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], datasets: [{ label: 'Risk Score', data: [], borderColor: '#dc2626', backgroundColor: 'rgba(220, 38, 38, 0.1)', fill: true, tension: 0.4 }] },
        options: { ...commonOpts, plugins: { title: { display: true, text: 'Composite Risk Trend' } } }
    });
}

function updateCharts(countryData) {
    // Mocking historical trend data based on current value for demonstration
    const curVal = countryData.exchange_rate;
    charts.currency.data.labels = ['1W', '2W', '3W', 'Now'];
    charts.currency.data.datasets[0].data = [curVal*1.02, curVal*0.99, curVal*1.01, curVal];
    charts.currency.update();
    
    const gdpVal = countryData.gdp / 1e12; // Trillions
    charts.gdp.data.datasets[0].data = [gdpVal*0.9, gdpVal*0.95, gdpVal*0.98, gdpVal];
    charts.gdp.update();
    
    const inf = countryData.inflation;
    charts.inflation.data.datasets[0].data = [inf-0.5, inf-0.2, inf+0.1, inf-0.1, inf+0.2, inf];
    charts.inflation.update();
    
    const risk = countryData.risk.total_risk;
    charts.risk.data.datasets[0].data = [risk-5, risk+2, risk-1, risk];
    charts.risk.update();
}

// -- DATA FETCHING --
async function loadDashboardData() {
    const loader = document.getElementById('loader-overlay');
    if (loader) loader.style.display = 'flex';
    
    try {
        const res = await fetch('/api/countries');
        const data = await res.json();
        
        const countryData = data.find(c => c.iso_code === currentCountry);
        if (countryData) {
            updateMetrics(countryData);
            updateMap(countryData.weather, getCoords(currentCountry), currentCountry);
            updateCharts(countryData);
        }
    } catch (e) {
        console.error("Error loading dashboard data", e);
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

function updateMetrics(c) {
    document.getElementById('kpi-gdp').textContent = '$' + (c.gdp / 1e12).toFixed(2) + 'T';
    document.getElementById('kpi-inflation').textContent = c.inflation.toFixed(1) + '%';
    document.getElementById('kpi-population').textContent = (c.population / 1e6).toFixed(1) + 'M';
    
    const fx = c.exchange_rate;
    document.getElementById('kpi-currency-rate').textContent = fx < 100 ? fx.toFixed(2) : fx.toFixed(0);
    document.getElementById('kpi-currency-name').textContent = c.currency + ' / USD';
    
    document.getElementById('kpi-weather').textContent = c.weather.temp + '°C';
    document.getElementById('kpi-weather-desc').innerHTML = `<span style="font-size: 0.75rem; white-space: nowrap;"><i class="bi bi-wind"></i> ${c.weather.wind}kt | <i class="bi bi-cloud-rain"></i> ${c.weather.precipitation || 0}mm</span>`;
    document.getElementById('kpi-weather-extra').innerHTML = `<span style="font-size: 0.75rem; white-space: nowrap;"><i class="bi bi-tornado"></i> Gust: ${c.weather.gusts || 0}kt</span>`;
    
    // Change icon based on precipitation/storm risk
    const weatherIcon = document.querySelector('#icon-weather i');
    if (c.weather.storm_risk > 50 || c.weather.precipitation > 5) {
        weatherIcon.className = 'bi bi-cloud-lightning-rain';
        document.getElementById('icon-weather').className = 'info-box-icon bg-danger';
    } else if (c.weather.precipitation > 0) {
        weatherIcon.className = 'bi bi-cloud-rain';
        document.getElementById('icon-weather').className = 'info-box-icon bg-info';
    } else {
        weatherIcon.className = 'bi bi-cloud-sun';
        document.getElementById('icon-weather').className = 'info-box-icon bg-primary';
    }
    
    const risk = c.risk;
    document.getElementById('kpi-risk-val').textContent = risk.total_risk;
    const badge = document.getElementById('kpi-risk-class');
    badge.textContent = risk.class;
    
    let badgeColor = 'bg-secondary';
    if (risk.class === 'Low') badgeColor = 'bg-success';
    if (risk.class === 'Medium') badgeColor = 'bg-warning text-dark';
    if (risk.class === 'High') badgeColor = 'bg-danger';
    
    badge.className = 'badge ' + badgeColor;
    document.getElementById('icon-risk').className = 'info-box-icon ' + badgeColor;
}

async function loadNewsData() {
    const feed = document.getElementById('news-feed');
    feed.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
    
    try {
        const res = await fetch('/api/news?country=' + currentCountry);
        const articles = await res.json();
        
        if (articles.length === 0) {
            feed.innerHTML = '<div class="p-3 text-muted">Belum ada berita untuk negara ini.</div>';
            return;
        }
        
        let html = '';
        articles.forEach(a => {
            let badgeColor = 'bg-secondary';
            if (a.sentiment === 'Positive') badgeColor = 'bg-success';
            if (a.sentiment === 'Negative') badgeColor = 'bg-danger';
            
            html += `
                <div class="news-item px-3">
                    <a href="${a.url}" target="_blank" class="news-title">${a.title}</a>
                    <div class="news-desc">${a.description || ''}</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="bi bi-clock"></i> ${new Date(a.published_at).toLocaleDateString()} &middot; ${a.source}</small>
                        <span class="badge ${badgeColor} badge-sentiment">${a.sentiment} (${a.positive_pct}%)</span>
                    </div>
                </div>
            `;
        });
        feed.innerHTML = html;
        
    } catch (e) {
        console.error("Error loading news", e);
        feed.innerHTML = '<div class="p-3 text-danger">Gagal memuat berita.</div>';
    }
}
