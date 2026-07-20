<?php
$html = file_get_contents("c:\\xampp4\\htdocs\\sistemmonitoring\\resources\\views\\map.blade.php");
preg_match('/<script>\s*const STATUS_COLOR(.*?)<\/script>/s', $html, $matches);
if(isset($matches[1])) {
    $js = "const STATUS_COLOR" . $matches[1];
    file_put_contents("c:\\xampp4\\htdocs\\sistemmonitoring\\public\\real_script.js", $js);
    exec("node -c c:\\xampp4\\htdocs\\sistemmonitoring\\public\\real_script.js 2>&1", $out, $ret);
    echo "Exit: $ret\n";
    print_r($out);
}
