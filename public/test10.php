<?php
$response = file_get_contents("https://api.worldbank.org/v2/country/AU/indicator/FP.CPI.TOTL.ZG?format=json&per_page=5");
print_r(json_decode($response, true));
