<?php
function readcontents($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
    curl_setopt($ch, CURLOPT_TIMEOUT, REQUEST_TIMEOUT);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function userinput($prompt) {
    global $bold, $green, $cln;
    echo $bold . $green . "[?] " . $prompt . ": " . $cln;
}

function save_result($filename, $data) {
    if(!is_dir('results')) mkdir('results');
    file_put_contents("results/" . $filename, $data);
}

function generate_report($data, $format = 'txt') {
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "honeyb_scan_" . $timestamp . "." . $format;
    save_result($filename, $data);
    return $filename;
}
?>
