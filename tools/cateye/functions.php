<?php
// CATEYE Functions - Integrated from original CATEYE tool

function getTitle($url){
   $data = readcontents($url);
   $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;
   return $title;
}

function userinput($message) {
   global $cln, $bold, $lblue, $fgreen;
   echo $bold . $lblue . "[?] " . $message . ": " . $fgreen;
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
   // Shopify
   elseif (strpos($content, 'shopify') !== false) {
        $cms = "Shopify";
   }
   // Wix
   elseif (strpos($content, 'wix.com') !== false) {
        $cms = "Wix";
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

function extract_social_links($sourcecode) {
   global $bold, $lblue, $fgreen, $red, $blue, $magenta, $orange, $white, $green, $grey, $cyan;
   $fb_link_count = 0;
   $twitter_link_count = 0;
   $insta_link_count = 0;
   $yt_link_count = 0;
   $gp_link_count = 0;
   $pint_link_count = 0;
   $github_link_count = 0;
   $total_social_link_count = 0;

   $social_links_array = array(
        'facebook' => array(),
        'twitter' => array(),
        'instagram' => array(),
        'youtube' => array(),
        'google_p' => array(),
        'pinterest' => array(),
        'github' => array()
   );

   $sm_dom = new DOMDocument;
   @$sm_dom->loadHTML($sourcecode);
   $links = $sm_dom->getElementsByTagName('a');
   foreach ($links as $link) {
        $href = $link->getAttribute('href');
        if (strpos($href, "facebook.com/") !== false) {
            $total_social_link_count++;
            $fb_link_count++;
            array_push($social_links_array['facebook'], $href);
        } elseif (strpos($href, "twitter.com/") !== false) {
            $total_social_link_count++;
            $twitter_link_count++;
            array_push($social_links_array['twitter'], $href);
        } elseif (strpos($href, "instagram.com/") !== false) {
            $total_social_link_count++;
            $insta_link_count++;
            array_push($social_links_array['instagram'], $href);
        } elseif (strpos($href, "youtube.com/") !== false) {
            $total_social_link_count++;
            $yt_link_count++;
            array_push($social_links_array['youtube'], $href);
        } elseif (strpos($href, "plus.google.com/") !== false) {
            $total_social_link_count++;
            $gp_link_count++;
            array_push($social_links_array['google_p'], $href);
        } elseif (strpos($href, "github.com/") !== false) {
            $total_social_link_count++;
            $github_link_count++;
            array_push($social_links_array['github'], $href);
        } elseif (strpos($href, "pinterest.com/") !== false) {
            $total_social_link_count++;
            $pint_link_count++;
            array_push($social_links_array['pinterest'], $href);
        }
   }
   
   if ($total_social_link_count == 0) {
        echo $bold . $red . "[!] No Social Link Found In Source Code. \n\e[0m";
   } elseif ($total_social_link_count == "1") {
        echo $bold . $lblue . "[i] " . $fgreen . $total_social_link_count . $lblue . " Social Link Was Gathered From Source Code \n\n";
        display_social_links($social_links_array);
   } else {
        echo $bold . $lblue . "[i] " . $fgreen . $total_social_link_count . $lblue . " Social Links Were Gathered From Source Code \n\n";
        display_social_links($social_links_array);
   }
}

function display_social_links($social_links_array) {
   global $bold, $blue, $cyan, $magenta, $red, $orange, $grey, $white;
   
   foreach ($social_links_array['facebook'] as $link) {
        echo $bold . $blue . "[ facebook  ] " . $white . $link . "\n";
   }
   foreach ($social_links_array['twitter'] as $link) {
        echo $bold . $cyan . "[  twitter  ] " . $white . $link . "\n";
   }
   foreach ($social_links_array['instagram'] as $link) {
        echo $bold . $magenta . "[ instagram ] " . $white . $link . "\n";
   }
   foreach ($social_links_array['youtube'] as $link) {
        echo $bold . $red . "[  youtube  ] " . $white . $link . "\n";
   }
   foreach ($social_links_array['google_p'] as $link) {
        echo $bold . $orange . "[  google+  ] " . $white . $link . "\n";
   }
   foreach ($social_links_array['pinterest'] as $link) {
        echo $bold . $red . "[ pinterest ] " . $white . $link . "\n";
   }
   foreach ($social_links_array['github'] as $link) {
        echo $bold . $grey . "[  github   ] " . $white . $link . "\n";
   }
   echo "\n";
}

function extractLINKS($reallink) {
   global $bold, $lblue, $fgreen;
   $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
   );
   $ip = str_replace("https://", "", $reallink);
   $lwwww = str_replace("www.", "", $ip);
   $elsc = file_get_contents($reallink, false, stream_context_create($arrContextOptions));
   $eldom = new DOMDocument;
   @$eldom->loadHTML($elsc);
   $elinks = $eldom->getElementsByTagName('a');
   $elinks_count = 0;
   foreach ($elinks as $ec) {
        $elinks_count++;
   }
   echo $bold . $lblue . "[i] Number Of Links Found In Source Code : " . $fgreen . $elinks_count . "\n";
   userinput("Display Links ? (Y/N) ");
   $bv_show_links = trim(fgets(STDIN, 1024));
   if ($bv_show_links == "y" or $bv_show_links == "Y") {
        foreach ($elinks as $elink) {
            $elhref = $elink->getAttribute('href');
            if (strpos($elhref, $lwwww) !== false) {
                echo "\n\e[92m\e[1m*\e[0m\e[1m $elhref";
            } else {
                echo "\n\e[38;5;208m\e[1m*\e[0m\e[1m $elhref";
            }
        }
        echo "\n";
   }
}

function readcontents($urltoread) {
   $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
   );
   $filecntns = @file_get_contents($urltoread, false, stream_context_create($arrContextOptions));
   return $filecntns;
}

function MXlookup($site) {
   $Mxlkp = dns_get_record($site, DNS_MX);
   if (!empty($Mxlkp)) {
        $mxrcrd = $Mxlkp[0]['target'];
        $mxip = gethostbyname($mxrcrd);
        $mx = gethostbyaddr($mxip);
        $mxresult = "\e[1m\e[36mIP      :\e[32m " . $mxip . "\n\e[36mHOSTNAME:\e[32m " . $mx;
   } else {
        $mxresult = "\e[91mNo MX records found";
   }
   return $mxresult;
}

function bv_get_alexa_rank($url) {
   $xml = @simplexml_load_file("http://data.alexa.com/data?cli=10&url=" . $url);
   if (isset($xml->SD)) {
        return $xml->SD->POPULARITY->attributes()->TEXT;
   }
   return "N/A";
}

function bv_moz_info($url) {
   global $bold, $red, $fgreen, $lblue, $blue;
   if (file_exists("config.php")) {
        require("config.php");
        if (isset($accessID) && isset($secretKey) && 
            !empty($accessID) && !empty($secretKey) && 
            strpos($accessID, " ") === false && strpos($secretKey, " ") === false) {
            
            $expires = time() + 300;
            $SignInStr = $accessID . "\n" . $expires;
            $binarySignature = hash_hmac('sha1', $SignInStr, $secretKey, true);
            $SafeSignature = urlencode(base64_encode($binarySignature));
            $objURL = $url;
            $flags = "103079231492";
            $reqUrl = "http://lsapi.seomoz.com/linkscape/url-metrics/" . urlencode($objURL) . "?Cols=" . $flags . "&AccessID=" . $accessID . "&Expires=" . $expires . "&Signature=" . $SafeSignature;
            $opts = array(
                CURLOPT_RETURNTRANSFER => true
            );
            $curlhandle = curl_init($reqUrl);
            curl_setopt_array($curlhandle, $opts);
            $content = curl_exec($curlhandle);
            curl_close($curlhandle);
            $resObj = json_decode($content);
            if ($resObj) {
                echo $bold . $lblue . "[i] Moz Rank : " . $fgreen . ($resObj->{'umrp'} ?? 'N/A') . "\n";
                echo $bold . $lblue . "[i] Domain Authority : " . $fgreen . ($resObj->{'pda'} ?? 'N/A') . "\n";
                echo $bold . $lblue . "[i] Page Authority : " . $fgreen . ($resObj->{'upa'} ?? 'N/A') . "\n";
            } else {
                echo $bold . $red . "[!] Failed to retrieve MOZ data\n";
            }
        } else {
            echo $bold . $red . "\n[!] Some Results Will Be Omitted (Please Put Valid MOZ API Keys in config.php file)\n\n";
        }
   } else {
        echo $bold . $red . "\n[!] Config file not found. MOZ data will be omitted.\n\n";
   }
}

// Additional functions for HoneyB integration
function advanced_link_crawl($html, $base_url) {
   $dom = new DOMDocument();
   @$dom->loadHTML($html);
   $links = $dom->getElementsByTagName('a');
   
   $internalLinks = [];
   $externalLinks = [];
   $resourceLinks = [];
   
   // Extract base domain for comparison
   $base_domain = parse_url($base_url, PHP_URL_HOST);
   
   foreach ($links as $link) {
        $href = $link->getAttribute('href');
        if (empty($href)) continue;
        
        // Resolve relative URLs
        $absolute_url = resolve_url($href, $base_url);
        
        // Categorize links
        $link_domain = parse_url($absolute_url, PHP_URL_HOST);
        if ($link_domain === $base_domain) {
            $internalLinks[] = $absolute_url;
        } elseif (strpos($absolute_url, 'http') === 0) {
            $externalLinks[] = $absolute_url;
        } else {
            $resourceLinks[] = $absolute_url;
        }
   }
   
   return [
        'internal' => array_unique($internalLinks),
        'external' => array_unique($externalLinks),
        'resources' => array_unique($resourceLinks)
   ];
}

function resolve_url($url, $base) {
   // Return if already absolute URL
   if (parse_url($url, PHP_URL_SCHEME) != '') return $url;
   
   // Parse base URL and convert to arrays
   $base_parts = parse_url($base);
   
   // If relative URL has no path
   if ($url[0] == '/') {
        $path = $url;
   } else {
        // Parse base path
        $base_path = isset($base_parts['path']) ? $base_parts['path'] : '';
        
        // Strip current directory and parent directory references
        $base_path = preg_replace('#/[^/]*$#', '', $base_path);
        
        // Build absolute path
        $path = $base_path . '/' . $url;
   }
   
   // Build absolute URL
   $abs_url = $base_parts['scheme'] . '://' . $base_parts['host'] . $path;
   
   return $abs_url;
}

function sensitive_info_scan($reallink, $protocol, $domain) {
   global $bold, $blue, $green, $red, $yellow, $cln;
   
   echo $bold . $blue . "\n    [SENSITIVE] Scanning for sensitive information...\n" . $cln;
   
   $content = readcontents($reallink);
   $found_items = [];
   
   // Email addresses
   preg_match_all('/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i', $content, $email_matches);
   $emails = array_unique($email_matches[0]);
   
   // API keys patterns
   $api_key_patterns = [
        '/[\'\"]([0-9a-zA-Z]{32,64})[\'\"]/' => 'Generic API Key',
        '/sk_live_[0-9a-zA-Z]{24}/' => 'Stripe Secret Key',
        '/rk_live_[0-9a-zA-Z]{24}/' => 'Stripe Restricted Key',
        '/AKIA[0-9A-Z]{16}/' => 'AWS Access Key',
        '/EAACEdEose0cBA[0-9A-Za-z]+/' => 'Facebook Access Token',
        '/ghp_[0-9a-zA-Z]{36}/' => 'GitHub Personal Token',
        '/xox[pborsa]-[0-9]{12}-[0-9]{12}-[0-9a-zA-Z]{24}/' => 'Slack Token'
   ];
   
   // Scan for emails
   if (!empty($emails)) {
        $found_items['emails'] = $emails;
        echo $bold . $yellow . "    [!] Found " . count($emails) . " email address(es):\n" . $cln;
        foreach ($emails as $email) {
            echo "        " . $email . "\n";
        }
   }
   
   // Scan for API keys
   foreach ($api_key_patterns as $pattern => $type) {
        preg_match_all($pattern, $content, $matches);
        if (!empty($matches[0])) {
            $found_items[$type] = $matches[0];
            echo $bold . $red . "    [!] Potential " . $type . " found:\n" . $cln;
            foreach (array_slice($matches[0], 0, 5) as $key) { // Show first 5 only
                echo "        " . $key . "\n";
            }
        }
   }
   
   // Check for common sensitive files
   $sensitive_files = [
        '/.env',
        '/config.php',
        '/.git/config',
        '/backup.zip',
        '/database.sql',
        '/.htpasswd',
        '/wp-config.php'
   ];
   
   $found_files = [];
   foreach ($sensitive_files as $file) {
        $test_url = $reallink . $file;
        if (check_url_exists($test_url)) {
            $found_files[] = $file;
            echo $bold . $red . "    [!] Sensitive file accessible: " . $file . $cln . "\n";
        }
   }
   
   if (empty($found_items) && empty($found_files)) {
        echo $bold . $green . "    [*] No obvious sensitive information found.\n" . $cln;
   } else {
        echo $bold . $yellow . "\n    [!] Scan completed. Review findings above.\n" . $cln;
   }
}

function check_url_exists($url) {
   $headers = @get_headers($url);
   if ($headers && strpos($headers[0], '200')) {
        return true;
   }
   return false;
}
?>
