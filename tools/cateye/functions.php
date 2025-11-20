<?php
// CATEYE Functions - Integrated from original CATEYE tool

function getTitle($url){
   $data = readcontents($url);
   $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;
   return $title;
}

function WEBserver($url){
   global $cln, $bold, $fgreen, $red;
   $ch = curl_init($url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_HEADER, 1);
   curl_setopt($ch, CURLOPT_NOBODY, 1);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_TIMEOUT, 10);
   $result = curl_exec($ch);
   $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
   $header = substr($result, 0, $header_size);
   
   if (preg_match('/Server: (.*)/i', $header, $matches)) {
        $server = trim($matches[1]);
        echo $bold . $fgreen . $server . $cln;
   } else {
        echo $bold . $red . "Could Not Detect!" . $cln;
   }
   curl_close($ch);
}

function advanced_CMSdetect($url){
   $content = readcontents($url);
   $cms = "Could Not Detect";
   
   // WordPress
   if (strpos($content, 'wp-content') !== false || strpos($content, 'wp-includes') !== false) {
        $cms = "WordPress";
   }
   // Joomla
   elseif (strpos($content, 'joomla') !== false || strpos($content, '/media/jui/') !== false) {
        $cms = "Joomla";
   }
   // Drupal
   elseif (strpos($content, 'drupal') !== false || strpos($content, '/sites/default/') !== false) {
        $cms = "Drupal";
   }
   // Magento
   elseif (strpos($content, 'magento') !== false || strpos($content, '/skin/frontend/') !== false) {
        $cms = "Magento";
   }
   
   return $cms;
}

function cloudflaredetect($domain){
   global $cln, $bold, $fgreen, $red;
   $ns = dns_get_record($domain, DNS_NS);
   $cf = $bold . $red . "Not Detected" . $cln;
   
   foreach($ns as $n) {
        if (strpos($n['target'], 'cloudflare') !== false) {
            $cf = $bold . $fgreen . "Detected" . $cln;
            break;
        }
   }
   echo $cf . "\n";
}

function robotsdottxt($url){
   global $cln, $bold, $fgreen, $red;
   $robot = $url . "/robots.txt";
   $robotcontent = readcontents($robot);
   
   if ($robotcontent && !strpos($robotcontent, '404 Not Found')) {
        echo $bold . $fgreen . "Found - " . $robot . $cln;
        
        // Display interesting robots.txt entries
        $lines = explode("\n", $robotcontent);
        foreach ($lines as $line) {
            if (strpos($line, 'Disallow:') !== false || strpos($line, 'Allow:') !== false) {
                echo "    " . trim($line) . "\n";
            }
        }
   } else {
        echo $bold . $red . "Not Found" . $cln;
   }
}

function gethttpheader($url){
   $ch = curl_init($url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_HEADER, 1);
   curl_setopt($ch, CURLOPT_NOBODY, 1);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_TIMEOUT, 10);
   $result = curl_exec($ch);
   $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
   $header = substr($result, 0, $header_size);
   echo $header;
   curl_close($ch);
}

function MXlookup($domain){
   $result = dns_get_record($domain, DNS_MX);
   $output = "";
   
   if (count($result) > 0) {
        foreach ($result as $mx) {
            $output .= "MX Record: " . $mx['target'] . " (Priority: " . $mx['pri'] . ")\n";
        }
   } else {
        $output = "No MX records found";
   }
   
   return $output;
}
?>
