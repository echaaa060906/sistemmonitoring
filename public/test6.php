<?php
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: SCM-Monitoring/1.0\r\n"
    ]
];
$context = stream_context_create($opts);
$response = file_get_contents("https://nominatim.openstreetmap.org/search?country=Antigua+and+Barbuda&format=json&limit=1", false, $context);
print_r($response);
