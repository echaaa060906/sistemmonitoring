<?php
$html = file_get_contents("c:\\xampp4\\htdocs\\sistemmonitoring\\resources\\views\\map.blade.php");
preg_match('/<script>(.*?)<\/script>/s', $html, $matches);
$js = $matches[1];

$testJs = <<<JS
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

$js

boot().catch(console.error);
JS;
file_put_contents("c:\\xampp4\\htdocs\\sistemmonitoring\\public\\test12.js", $testJs);
exec("node c:\\xampp4\\htdocs\\sistemmonitoring\\public\\test12.js 2>&1", $output);
print_r($output);
