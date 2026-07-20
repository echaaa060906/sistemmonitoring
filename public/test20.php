<?php
$mysqli = new mysqli("localhost", "root", "", "scm_dashboard");
$res = $mysqli->query("SELECT * FROM news_cache WHERE country_code = 'AG'");
while($row = $res->fetch_assoc()) { print_r($row); }
