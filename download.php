<?php
$url = "https://raw.githubusercontent.com/mledoze/countries/master/countries.json";
$json = file_get_contents($url);
file_put_contents(__DIR__ . '/storage/app/countries.json', $json);
echo "Downloaded countries.json\n";
