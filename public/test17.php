<?php
$res = file_get_contents("http://127.0.0.1:8000/api/countries");
echo substr($res, 0, 500);
