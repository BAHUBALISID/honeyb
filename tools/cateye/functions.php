<?php
// Functions for CATEYE Web Scanner - HoneyB Integrated Version

function getTitle($url) {
    global $cln;
    $data = readcontents($url);
    $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;
    return $title ?: $cln . "Could not retrieve title";
}

function userinput($message) {
    global $bold, $lblue, $fgreen, $cln;
    echo $bold . $lblue . "[?] " . $message . ": " . $fgreen;
}

function WEBserver($url) {
    global $bold, $fgreen, $red, $cln;
    
    stream_context_set_default([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]);
    
    $wsheaders = get_headers($url, 1);
    if (is_array($wsheaders['Server'])) {
        $ws = $wsheaders['Server'][0];
    } else {
        $ws = $wsheaders['Server'];
    }
    
    if ($ws == "") {
        echo $bold . $red . "Could Not Detect" . $cln;
    } else {
        echo $bold . $fgreen . $ws . $cln;
    }
}

function cloudflaredetect($domain) {
    global $bold, $fgreen, $red, $cln;
    
    $ns = dns_get_record($domain, DNS_NS);
    $cf = false;
    
    foreach($ns as $n) {
        if (strpos($n['target'], 'cloudflare') !== false) {
            $cf = true;
            break;
        }
    }
    
    if ($cf) {
        echo $bold . $red . "Detected\n" . $cln;
    } else {
        echo $bold . $fgreen . "Not Detected\n" . $cln;
    }
}

function CMSdetect($reallink) {
    $cmssc = readcontents($reallink);
    if (strpos($cmssc, '/wp-content/') !== false) {
        $tcms = "WordPress";
    } else {
        if (strpos($cmssc, 'Joomla') !== false) {
            $tcms = "Joomla";
        } else {
            $drpurl = $reallink . "/misc/drupal.js";
            $drpsc = readcontents($drpurl);
            if (strpos($drpsc, 'Drupal') !== false) {
                $tcms = "Drupal";
            } else {
                if (strpos($cmssc, '/skin/frontend/') !== false) {
                    $tcms = "Magento";
                } else {
                    if (strpos($cmssc, 'content="WordPress') !== false) {
                        $tcms = "WordPress";
                    } else {
                        $tcms = "Could Not Detect";
                    }
                }
            }
        }
    }
    return $tcms;
}

function advanced_CMSdetect($reallink) {
    global $cln;
    $cmssc = readcontents($reallink);
    $cms_signatures = [
        'WordPress' => ['/wp-content/', 'content="WordPress', 'wp-includes/', 'wp-json/'],
        'Joomla' => ['/joomla/', 'Joomla!', 'content="Joomla'],
        'Drupal' => ['Drupal', 'drupal.js'],
        'Magento' => ['/skin/frontend/', 'Magento'],
        'Shopify' => ['shopify', 'cdn.shopify.com'],
        'Wix' => ['wix.com', 'wix-domain.net'],
        'Squarespace' => ['squarespace', 'static.squarespace.com'],
        'Blogger' => ['blogger.com', 'blogspot.com'],
        'Ghost' => ['ghost.org', 'content="Ghost'],
        'TYPO3' => ['typo3', 'TYPO3'],
        'PrestaShop' => ['prestashop', 'PrestaShop'],
        'OpenCart' => ['opencart', 'OpenCart'],
        'WooCommerce' => ['woocommerce', 'WooCommerce'],
        'BigCommerce' => ['bigcommerce', 'bigcommerce.com'],
        'Moodle' => ['moodle', 'Moodle'],
        'MediaWiki' => ['mediawiki', 'MediaWiki'],
        'phpBB' => ['phpbb', 'phpBB'],
    ];
    
    foreach ($cms_signatures as $cms => $signatures) {
        foreach ($signatures as $signature) {
            if (strpos($cmssc, $signature) !== false) {
                return $cms;
            }
        }
    }
    
    return "Could Not Detect";
}

function robotsdottxt($reallink) {
    global $bold, $fgreen, $red, $blue, $cln;
    
    $rbturl = $reallink . "/robots.txt";
    $rbthandle = curl_init($rbturl);
    curl_setopt($rbthandle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($rbthandle, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($rbthandle, CURLOPT_TIMEOUT, 10);
    $rbtresponse = curl_exec($rbthandle);
    $rbthttpCode = curl_getinfo($rbthandle, CURLINFO_HTTP_CODE);
    curl_close($rbthandle);
    
    if ($rbthttpCode == 200) {
        $rbtcontent = readcontents($rbturl);
        if ($rbtcontent == "") {
            echo $bold . $red . "Found But Empty!" . $cln;
        } else {
            echo $bold . $fgreen . "Found" . $cln . "\n";
            echo $blue . "\n-------------[ contents ]----------------" . $cln . "\n";
            echo $rbtcontent;
            echo $blue . "\n-----------[end of contents]-------------" . $cln;
        }
    } else {
        echo $bold . $red . "Could NOT Find robots.txt!" . $cln . "\n";
    }
}

function gethttpheader($reallink) {
    global $bold, $fgreen, $cln;
    
    $hdr = get_headers($reallink);
    foreach ($hdr as $shdr) {
        echo "\n" . $bold . $fgreen . "[i]" . $cln . "  $shdr";
    }
    echo "\n";
}

function extract_social_links($sourcecode) {
    global $bold, $lblue, $fgreen, $red, $blue, $magenta, $yellow, $white, $green, $cyan, $cln;
    
    $social_links_array = [
        'facebook' => [],
        'twitter' => [],
        'instagram' => [],
        'youtube' => [],
        'google_p' => [],
        'pinterest' => [],
        'github' => [],
        'linkedin' => []
    ];

    $sm_dom = new DOMDocument;
    @$sm_dom->loadHTML($sourcecode);
    $links = $sm_dom->getElementsByTagName('a');
    
    foreach ($links as $link) {
        $href = $link->getAttribute('href');
        if (strpos($href, "facebook.com/") !== false) {
            $social_links_array['facebook'][] = $href;
        } elseif (strpos($href, "twitter.com/") !== false) {
            $social_links_array['twitter'][] = $href;
        } elseif (strpos($href, "instagram.com/") !== false) {
            $social_links_array['instagram'][] = $href;
        } elseif (strpos($href, "youtube.com/") !== false) {
            $social_links_array['youtube'][] = $href;
        } elseif (strpos($href, "plus.google.com/") !== false) {
            $social_links_array['google_p'][] = $href;
        } elseif (strpos($href, "github.com/") !== false) {
            $social_links_array['github'][] = $href;
        } elseif (strpos($href, "pinterest.com/") !== false) {
            $social_links_array['pinterest'][] = $href;
        } elseif (strpos($href, "linkedin.com/") !== false) {
            $social_links_array['linkedin'][] = $href;
        }
    }
    
    $total_count = 0;
    foreach ($social_links_array as $platform => $links) {
        $total_count += count($links);
    }
    
    if ($total_count == 0) {
        echo $bold . $red . "[!] No Social Links Found In Source Code.\n" . $cln;
    } else {
        echo $bold . $lblue . "[i] " . $fgreen . $total_count . $lblue . " Social Link(s) Found\n\n" . $cln;
        display_social_links($social_links_array);
    }
}

function display_social_links($social_links_array) {
    global $bold, $blue, $cyan, $magenta, $red, $yellow, $white, $green, $cln;
    
    $colors = [
        'facebook' => $blue,
        'twitter' => $cyan,
        'instagram' => $magenta,
        'youtube' => $red,
        'google_p' => $yellow,
        'pinterest' => $red,
        'github' => $white,
        'linkedin' => $blue
    ];
    
    $labels = [
        'facebook' => 'facebook ',
        'twitter' => ' twitter ',
        'instagram' => 'instagram',
        'youtube' => ' youtube ',
        'google_p' => ' google+ ',
        'pinterest' => 'pinterest',
        'github' => '  github  ',
        'linkedin' => ' linkedin '
    ];
    
    foreach ($social_links_array as $platform => $links) {
        foreach ($links as $link) {
            echo $bold . $colors[$platform] . "[" . $labels[$platform] . "] " . $white . $link . $cln . "\n";
        }
    }
    echo "\n";
}

function extractLINKS($reallink) {
    global $bold, $lblue, $fgreen, $yellow, $cln;
    
    $arrContextOptions = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
    ];
    
    $ip = str_replace(["https://", "http://"], "", $reallink);
    $lwwww = str_replace("www.", "", $ip);
    $elsc = file_get_contents($reallink, false, stream_context_create($arrContextOptions));
    $eldom = new DOMDocument;
    @$eldom->loadHTML($elsc);
    $elinks = $eldom->getElementsByTagName('a');
    $elinks_count = $elinks->length;
    
    echo $bold . $lblue . "[i] Number Of Links Found In Source Code: " . $fgreen . $elinks_count . $cln . "\n";
    
    userinput("Display Links? (Y/N)");
    $bv_show_links = trim(fgets(STDIN, 1024));
    
    if (strtolower($bv_show_links) == "y") {
        foreach ($elinks as $elink) {
            $elhref = $elink->getAttribute('href');
            if (strpos($elhref, $lwwww) !== false || strpos($elhref, 'http') === false) {
                echo $bold . $fgreen . "[*] " . $cln . $elhref . "\n";
            } else {
                echo $bold . $yellow . "[*] " . $cln . $elhref . "\n";
            }
        }
        echo "\n";
    }
}

function readcontents($urltoread) {
    $arrContextOptions = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
    ];
    
    $filecntns = @file_get_contents($urltoread, false, stream_context_create($arrContextOptions));
    return $filecntns ?: "";
}

function MXlookup($site) {
    global $bold, $cyan, $fgreen, $red, $cln;
    
    $Mxlkp = dns_get_record($site, DNS_MX);
    if (!empty($Mxlkp)) {
        $mxrcrd = $Mxlkp[0]['target'];
        $mxip = gethostbyname($mxrcrd);
        $mx = gethostbyaddr($mxip);
        $mxresult = $bold . $cyan . "IP      :" . $fgreen . " " . $mxip . "\n" . 
                   $bold . $cyan . "HOSTNAME:" . $fgreen . " " . $mx . $cln;
    } else {
        $mxresult = $bold . $red . "No MX records found" . $cln;
    }
    return $mxresult;
}

function bv_get_alexa_rank($url) {
    global $cln;
    $xml = @simplexml_load_file("http://data.alexa.com/data?cli=10&url=" . $url);
    if (isset($xml->SD)) {
        return $xml->SD->POPULARITY->attributes()->TEXT;
    }
    return "N/A";
}

function bv_moz_info($url) {
    global $bold, $red, $fgreen, $lblue, $cln;
    
    // For HoneyB integration, we'll use a simplified version
    // In a real implementation, you would use MOZ API keys
    echo $bold . $lblue . "[i] Moz Rank: " . $fgreen . "N/A (API Key Required)\n" . $cln;
    echo $bold . $lblue . "[i] Domain Authority: " . $fgreen . "N/A (API Key Required)\n" . $cln;
    echo $bold . $lblue . "[i] Page Authority: " . $fgreen . "N/A (API Key Required)\n" . $cln;
}

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
        if (empty($href) || $href == '#') continue;
        
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
    if (parse_url($url, PHP_URL_SCHEME) != '') {
        return $url;
    }
    
    // Parse base URL and convert to arrays
    $base_parts = parse_url($base);
    
    // If relative URL starts with //
    if (strpos($url, '//') === 0) {
        return $base_parts['scheme'] . ':' . $url;
    }
    
    // If relative URL has no path
    if ($url[0] == '/') {
        $path = $url;
    } else {
        // Parse base path
        $base_path = isset($base_parts['path']) ? $base_parts['path'] : '/';
        
        // Strip current directory and parent directory references
        $base_path = preg_replace('#/[^/]*$#', '', $base_path);
        
        // Build absolute path
        $path = $base_path . '/' . $url;
        
        // Resolve . and .. in path
        $path = resolve_path($path);
    }
    
    // Build absolute URL
    $abs_url = $base_parts['scheme'] . '://' . $base_parts['host'] . $path;
    
    return $abs_url;
}

function resolve_path($path) {
    $parts = explode('/', $path);
    $result = [];
    
    foreach ($parts as $part) {
        if ($part == '' || $part == '.') {
            continue;
        }
        if ($part == '..') {
            array_pop($result);
        } else {
            $result[] = $part;
        }
    }
    
    return '/' . implode('/', $result);
}

// New function for sensitive information scanning
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
    
    // Credit card patterns (for educational purposes only)
    $cc_patterns = [
        '/\b4[0-9]{12}(?:[0-9]{3})?\b/' => 'Visa',
        '/\b5[1-5][0-9]{14}\b/' => 'MasterCard',
        '/\b3[47][0-9]{13}\b/' => 'American Express',
        '/\b6(?:011|5[0-9]{2})[0-9]{12}\b/' => 'Discover'
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

// Function to get page load time
function get_page_load_time($url) {
    $start = microtime(true);
    $content = readcontents($url);
    $end = microtime(true);
    
    return round(($end - $start) * 1000, 2); // Convert to milliseconds
}

// Function to check security headers
function check_security_headers($url) {
    global $bold, $green, $yellow, $red, $cln;
    
    $headers = get_headers($url, 1);
    $security_headers = [
        'Strict-Transport-Security' => 'HSTS Header',
        'Content-Security-Policy' => 'CSP Header',
        'X-Content-Type-Options' => 'MIME Sniffing Protection',
        'X-Frame-Options' => 'Clickjacking Protection',
        'X-XSS-Protection' => 'XSS Protection',
        'Referrer-Policy' => 'Referrer Policy'
    ];
    
    $found_headers = [];
    
    foreach ($security_headers as $header => $description) {
        if (isset($headers[$header])) {
            $found_headers[] = $header;
            echo $bold . $green . "    [+] " . $description . ": Present\n" . $cln;
        } else {
            echo $bold . $yellow . "    [-] " . $description . ": Missing\n" . $cln;
        }
    }
    
    return $found_headers;
}
?>
