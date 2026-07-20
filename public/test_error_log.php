<?php
$error = $_GET['e'] ?? 'No error';
file_put_contents("c:\\xampp4\\htdocs\\sistemmonitoring\\public\\map_error.log", $error . "\n", FILE_APPEND);
echo "OK";
