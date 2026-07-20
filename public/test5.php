<?php
$response = file_get_contents("https://restcountries.com/v3.1/all");
echo substr($response, 0, 500);
