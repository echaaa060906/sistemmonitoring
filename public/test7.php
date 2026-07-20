<?php
$opts = [
    "http" => [
        "method" => "GET"
    ]
];
$context = stream_context_create($opts);
$response = file_get_contents("http://127.0.0.1:8000/api/country/AG", false, $context);
print_r($response);
