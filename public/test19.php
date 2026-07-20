<?php
$query = '"supply chain" OR logistics OR economy "Antigua"';
$apiKey = 'f5cb9d4408d5684c850e44b9111ed413';
$url = "https://gnews.io/api/v4/search?q=" . urlencode($query) . "&lang=en&max=10&apikey=$apiKey";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);
echo "Response: $response\n";
