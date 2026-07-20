<?php
$html = file_get_contents("c:\\xampp4\\htdocs\\sistemmonitoring\\resources\\views\\map.blade.php");
preg_match('/<script>(.*?)<\/script>/s', $html, $matches);
if (isset($matches[1])) {
    file_put_contents("c:\\xampp4\\htdocs\\sistemmonitoring\\public\\test_script.js", $matches[1]);
    exec("node -c c:\\xampp4\\htdocs\\sistemmonitoring\\public\\test_script.js 2>&1", $output, $return_var);
    echo "Return Code: $return_var\n";
    print_r($output);
} else {
    echo "No script found";
}
