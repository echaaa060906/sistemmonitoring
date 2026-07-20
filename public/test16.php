<?php
$html = file_get_contents("c:\\xampp4\\htdocs\\sistemmonitoring\\resources\\views\\map.blade.php");
preg_match('/<script>\s*const STATUS_COLOR(.*?)<\/script>/s', $html, $matches);
$js = "const STATUS_COLOR" . $matches[1];

$testJs = <<<JS
const document = {
    getElementById: (id) => ({
        value: '',
        innerHTML: '',
        textContent: '',
        style: {},
        classList: { toggle: ()=>{} }
    }),
    querySelectorAll: () => [{textContent:'', style:{}}, {textContent:'', style:{}}, {textContent:'', style:{}}, {textContent:'', style:{}}, {textContent:'', style:{}}, {textContent:'', style:{}}, {textContent:'', style:{}}],
    querySelector: () => ({ style: {} })
};
const window = { addEventListener: (e, cb) => { try { cb() } catch(err){ console.log("WINDOW ERR", err); } } };
const L = {
    map: () => ({ addTo: ()=>({bindPopup:()=>({on:()=>({setIcon:()=>({setLatLng:()=>({setPopupContent:()=>({setStyle:()=>({setLatLngs:()=>{}})})})})})})})}), fitBounds: ()=>{} }),
    tileLayer: () => ({ addTo: ()=>{} }),
    marker: () => ({ addTo: ()=>({bindPopup:()=>({on:()=>()})}) }),
    divIcon: () => {},
    circleMarker: () => ({ addTo: ()=>{} }),
    polyline: () => ({ addTo: ()=>{} }),
};
const fetch = async () => ({ json: async () => ([{iso_code:'ID', name:'Indonesia', risk:{total_risk:40, class:'Medium'}}, {iso_code:'US', name:'USA', risk:{total_risk:20, class:'Low'}}]) });
const alert = console.log;
const bootstrap = { Tab: class { show(){} } };

$js

boot().catch(err => console.log("BOOT ERR", err));
JS;
file_put_contents("c:\\xampp4\\htdocs\\sistemmonitoring\\public\\test16.js", $testJs);
exec("node c:\\xampp4\\htdocs\\sistemmonitoring\\public\\test16.js 2>&1", $output);
print_r($output);
