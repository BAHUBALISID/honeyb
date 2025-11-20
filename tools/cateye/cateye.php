<?php
// CATEYE Web Reconnaissance Tool Integration

// Check if we're running within HoneyB
if (!defined('HONEYB_VERSION')) {
    die("This tool must be run from HoneyB main menu.\n");
}

// Load CATEYE functions
require_once __DIR__ . '/functions.php';

class CATEYE {
    private $target;
    private $protocol;
    
    public function __construct() {
        $this->show_banner();
        if ($this->get_target()) {
            $this->main_menu();
        }
    }
    
    private function show_banner() {
        system("clear");
        global $bold, $magenta, $lblue, $yellow, $white, $cln;
        
        echo $bold . $magenta . "       
      /\     /\                     ███████ ███████  ███████ ███████ ██  ██ ███████\n
     /  \~~~/. \                    ██   ██ ██   ██    ██    ██      ██  ██ ██  \n
    (    . .    )                   ██      ███████    ██    █████   ██████ █████\n
     \__\_v_/__/                    ██   ██ ██   ██    ██    ██        ██   ██  \n
        /   \                       ███████ ██   ██    ██    ███████   ██   ███████ \n
       (_____)     \n";
        echo $bold . $lblue . "\n        Advanced Web Reconnaissance Tool\n";
        echo $bold . $yellow . "        Integrated with HoneyB Security Suite\n";
        echo $bold . $white . "        Rebranded as CATEYE with Enhanced Features\n\n";
        echo $cln;
    }
    
    private function get_target() {
        userinput("Enter The Website You Want To Scan (or 'back' to return)");
        $this->target = trim(fgets(STDIN, 1024));
        
        if (strtolower($this->target) === 'back') {
            return false;
        }
        
        // Validate target
        if (empty($this->target) || strpos($this->target, ' ') !== false) {
            echo $bold . $red . "\n[!] Invalid target! Please enter a valid domain.\n" . $cln;
            sleep(2);
            return $this->get_target();
        }
        
        echo "\n";
        userinput("Enter 1 For HTTP OR Enter 2 For HTTPS");
        $protocol_choice = trim(fgets(STDIN, 1024));
        $this->protocol = ($protocol_choice == "2") ? "https://" : "http://";
        
        return true;
    }
    
    public function main_menu() {
        global $cln, $bold, $blue, $lblue, $yellow, $white, $magenta, $fgreen, $red;
        
        while(true) {
            system("clear");
            $this->show_banner();
            
            echo $bold . $blue . "
      +--------------------------------------------------------------+
      +                  CATEYE - Scan Selection                     +
      +--------------------------------------------------------------+

            $lblue Scanning Site : " . $fgreen . $this->protocol . $this->target . $blue . "
      \n\n";
            
            echo $yellow . " [0]  Basic Recon$white (Site Title, IP, CMS, Cloudflare, Robots.txt)$yellow \n";
            echo " [1]  Whois Lookup \n";
            echo " [2]  Geo-IP Lookup \n";
            echo " [3]  Grab Banners \n";
            echo " [4]  DNS Lookup \n";
            echo " [5]  Subnet Calculator \n";
            echo " [6]  NMAP Port Scan \n";
            echo " [7]  Subdomain Scanner \n";
            echo " [8]  Reverse IP Lookup & CMS Detection \n";
            echo " [9]  SQLi Scanner$white (Parameter-based SQL Injection)$yellow \n";
            echo " [10] Bloggers View$white (SEO and Site Information)$yellow \n";
            echo " [11] WordPress Scan \n";
            echo " [12] Crawler \n";
            echo " [13] MX Lookup \n";
            echo " [14] Advanced Link Crawler \n";
            echo " [15] Sensitive Information Scanner \n";
            echo "$magenta [A]  Scan For Everything \n";
            echo "$blue [B]  Scan Another Website \n";
            echo "$red [Q]  Back to HoneyB Main Menu \n\n" . $cln;

            userinput("Choose Any Scan OR Action");
            $scan = trim(fgets(STDIN, 1024));
            
            switch(strtolower($scan)) {
                case 'q':
                    return; // Return to HoneyB main menu
                case 'b':
                    if (!$this->get_target()) return;
                    break;
                case '0':
                    $this->basic_recon();
                    break;
                case '1':
                    $this->whois_lookup();
                    break;
                case '2':
                    $this->geoip_lookup();
                    break;
                case '3':
                    $this->grab_banners();
                    break;
                case '4':
                    $this->dns_lookup();
                    break;
                case '5':
                    $this->subnet_calculator();
                    break;
                case '6':
                    $this->nmap_scan();
                    break;
                case '7':
                    $this->subdomain_scan();
                    break;
                case '8':
                    $this->reverse_ip_lookup();
                    break;
                case '9':
                    $this->sqli_scan();
                    break;
                case '10':
                    $this->bloggers_view();
                    break;
                case '11':
                    $this->wordpress_scan();
                    break;
                case '12':
                    $this->crawler();
                    break;
                case '13':
                    $this->mx_lookup();
                    break;
                case '14':
                    $this->advanced_link_crawler();
                    break;
                case '15':
                    $this->sensitive_info_scan();
                    break;
                case 'a':
                    $this->full_scan();
                    break;
                default:
                    echo $bold . $red . "\n[!] Invalid option! Please try again.\n" . $cln;
                    sleep(2);
            }
            
            if (strtolower($scan) !== 'q' && strtolower($scan) !== 'b') {
                readline($bold . $yellow . "\nPress Enter to continue..." . $cln);
            }
        }
    }
    
    private function basic_recon() {
        global $cln, $bold, $lblue, $green, $blue, $yellow;
        
        $reallink = $this->protocol . $this->target;
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n";
        echo $bold . $yellow . "[S] Scan Type : BASIC SCAN" . $cln;
        echo "\n\n";
        
        echo $bold . $lblue . "[iNFO] Site Title: " . $green;
        echo getTitle($reallink);
        echo $cln;
        
        $wip = gethostbyname($this->target);
        echo $lblue . $bold . "\n[iNFO] IP address: " . $green . $wip . "\n" . $cln;
        
        echo $bold . $lblue . "[iNFO] Web Server: ";
        WEBserver($reallink);
        echo "\n";
        
        echo $bold . $lblue . "[iNFO] CMS: \e[92m" . advanced_CMSdetect($reallink) . $cln;
        echo $lblue . $bold . "\n[iNFO] Cloudflare: ";
        cloudflaredetect($lwwww);
        
        echo $lblue . $bold . "[iNFO] Robots File:$cln ";
        robotsdottxt($reallink);
        echo "\n";
    }
    
    private function whois_lookup() {
        global $cln, $bold, $lblue, $green, $blue, $yellow;
        
        $reallink = $this->protocol . $this->target;
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n";
        echo $bold . $yellow . "[S] Scan Type : WHOIS Lookup" . $cln;
        echo $bold . $lblue . "\n[~] Whois Lookup Result: \n\n" . $cln;
        
        $urlwhois = "http://api.hackertarget.com/whois/?q=" . $lwwww;
        $resultwhois = file_get_contents($urlwhois);
        echo $bold . $green . $resultwhois . $cln;
    }
    
    private function geoip_lookup() {
        global $cln, $bold, $lblue, $green, $blue, $yellow;
        
        $reallink = $this->protocol . $this->target;
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n";
        echo $bold . $yellow . "[S] Scan Type : GEO-IP Lookup" . $cln;
        echo "\n\n";
        
        $urlgip = "http://api.hackertarget.com/geoip/?q=" . $lwwww;
        $resultgip = readcontents($urlgip);
        $geoips = explode("\n", $resultgip);
        
        foreach ($geoips as $geoip) {
            echo $bold . $lblue . "[GEO-IP]$green $geoip \n";
        }
    }
    
    private function grab_banners() {
        global $cln, $bold, $lblue, $green, $blue, $yellow;
        
        $reallink = $this->protocol . $this->target;
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n";
        echo $bold . $yellow . "[S] Scan Type : Banner Grabbing" . $cln;
        echo "\n\n";
        
        gethttpheader($reallink);
    }
    
    private function dns_lookup() {
        global $cln, $bold, $lblue, $green, $blue, $yellow;
        
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m " . $this->protocol . $this->target . "\n";
        echo $bold . $yellow . "[S] Scan Type : DNS Lookup" . $cln;
        echo "\n\n";
        
        $urldlup = "http://api.hackertarget.com/dnslookup/?q=" . $lwwww;
        $resultdlup = readcontents($urldlup);
        echo $bold . $green . $resultdlup . $cln;
    }
    
    private function subnet_calculator() {
        global $cln, $bold, $lblue, $green, $blue, $yellow;
        
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m " . $this->protocol . $this->target . "\n";
        echo $bold . $yellow . "[S] Scan Type : Subnet Calculator" . $cln;
        echo "\n\n";
        
        $urlscal = "http://api.hackertarget.com/subnetcalc/?q=" . $lwwww;
        $resultscal = readcontents($urlscal);
        echo $bold . $green . $resultscal . $cln;
    }
    
    private function nmap_scan() {
        global $cln, $bold, $lblue, $green, $blue, $yellow;
        
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m " . $this->protocol . $this->target . "\n";
        echo $bold . $yellow . "[S] Scan Type : NMAP Port Scan" . $cln;
        echo "\n\n";
        
        $urlnmap = "http://api.hackertarget.com/nmap/?q=" . $lwwww;
        $resultnmap = readcontents($urlnmap);
        echo $bold . $green . $resultnmap . $cln;
    }
    
    private function subdomain_scan() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $fgreen;
        
        $reallink = $this->protocol . $this->target;
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n";
        echo $bold . $yellow . "[S] Scan Type : Subdomain Scanner" . $cln;
        
        $urlsd = "http://api.hackertarget.com/hostsearch/?q=" . $lwwww;
        $resultsd = readcontents($urlsd);
        $subdomains = trim($resultsd, "\n");
        $subdomains = explode("\n", $subdomains);
        
        // Remove the first line if it's a header
        if (strpos($subdomains[0], 'API') !== false) {
            unset($subdomains[0]);
        }
        
        $sdcount = count($subdomains);
        
        echo "\n" . $blue . $bold . "[i] Total Subdomains Found : " . $green . $sdcount . "\n\n" . $cln;
        
        foreach ($subdomains as $subdomain) {
            if (!empty(trim($subdomain))) {
                $parts = explode(',', $subdomain);
                if (count($parts) >= 2) {
                    echo $bold . $lblue . "[+] Subdomain: $fgreen" . trim($parts[0]) . $cln . "\n";
                    echo $bold . $lblue . "[-] IP: $fgreen" . trim($parts[1]) . $cln . "\n\n";
                }
            }
        }
    }
    
    private function reverse_ip_lookup() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $fgreen, $red;
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m " . $this->protocol . $this->target . "\n";
        echo $bold . $yellow . "[S] Scan Type : Reverse IP Lookup & CMS Detection" . $cln;
        echo "\n";
        
        $ip = gethostbyname($this->target);
        echo $bold . $lblue . "[i] Target IP: " . $fgreen . $ip . $cln . "\n";
        
        // Using hackertarget for reverse IP lookup
        $url = "http://api.hackertarget.com/reverseiplookup/?q=" . $ip;
        $result = readcontents($url);
        
        if (strpos($result, 'error') !== false || strpos($result, 'No records') !== false) {
            echo $bold . $red . "[!] No sites found on this IP address.\n" . $cln;
            return;
        }
        
        $sites = explode("\n", trim($result));
        $site_count = count($sites);
        
        echo $bold . $lblue . "[i] Total Sites Found On This Server: " . $fgreen . $site_count . $cln . "\n\n";
        
        // Ask if user wants CMS detection
        userinput("Do you want to detect CMS for each site? (Y/N)");
        $detect_cms = strtolower(trim(fgets(STDIN, 1024)));
        
        $count = 0;
        foreach ($sites as $site) {
            if (!empty(trim($site))) {
                $count++;
                echo $bold . $lblue . "\n[$count] Site: " . $fgreen . $site . $cln . "\n";
                echo $bold . $lblue . "    IP: " . $fgreen . gethostbyname($site) . $cln . "\n";
                
                if ($detect_cms == 'y' || $detect_cms == 'yes') {
                    $cms_url = "http://" . $site;
                    echo $bold . $lblue . "    CMS: " . $green . advanced_CMSdetect($cms_url) . $cln . "\n";
                }
                
                // Limit display to prevent too much output
                if ($count >= 20) {
                    echo $bold . $yellow . "\n[i] Displaying first 20 sites only...\n" . $cln;
                    break;
                }
            }
        }
    }
    
    private function sqli_scan() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $fgreen, $red;
        
        $reallink = $this->protocol . $this->target;
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n";
        echo $bold . $yellow . "[S] Scan Type : SQL Injection Scanner" . $cln;
        echo "\n\n";
        
        $html = readcontents($reallink);
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        $vlnk = 0;
        $vulnerable_count = 0;
        
        foreach ($links as $link) {
            $lol = $link->getAttribute('href');
            if (strpos($lol, '?') !== false) {
                echo $lblue . $bold . "\n[ LINK ] " . $fgreen . $lol . "\n" . $cln;
                echo $blue . $bold . "[ SQLi ] ";
                
                $sqllist = "' OR '1'='1'; --";
                if (strpos($lol, '://') !== false) {
                    $sqlurl = $lol . $sqllist;
                } else {
                    $sqlurl = $reallink . "/" . $lol . $sqllist;
                }
                
                $sqlsc = @readcontents($sqlurl);
                $sqlvn = $bold . $red . "Not Vulnerable";
                
                $sqlerrors = [
                    "You have an error in your SQL syntax",
                    "Warning: mysql",
                    "Unclosed quotation mark",
                    "SQL syntax.*MySQL",
                    "Warning: pg_",
                    "Microsoft OLE DB Provider",
                    "ODBC Driver",
                    "PostgreSQL.*ERROR"
                ];
                
                foreach ($sqlerrors as $error) {
                    if (strpos($sqlsc, $error) !== false) {
                        $sqlvn = $green . $bold . "Potentially Vulnerable!";
                        $vulnerable_count++;
                        break;
                    }
                }
                echo $sqlvn . $cln;
                echo "\n";
                $vlnk++;
            }
        }
        
        echo "\n" . $blue . $bold . "[+] URL(s) With Parameter(s): " . $green . $vlnk . $cln;
        echo "\n" . $blue . $bold . "[+] Potentially Vulnerable: " . ($vulnerable_count > 0 ? $red . $vulnerable_count : $green . $vulnerable_count) . $cln . "\n";
    }
    
    private function bloggers_view() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $fgreen;
        
        $reallink = $this->protocol . $this->target;
        $srccd = readcontents($reallink);
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln\t" . $lblue . $bold . "[+] BLOGGERS VIEW [+] \n\n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n\n" . $cln;
        
        $test_url = $reallink;
        $handle = curl_init($test_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        $tu_response = curl_exec($handle);
        $test_url_http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        
        echo $lblue . $bold . "[i] HTTP Response Code : " . $fgreen . $test_url_http_code . $cln . "\n";
        echo $lblue . $bold . "[i] Site Title: " . $fgreen . getTitle($reallink) . $cln . "\n";
        echo $lblue . $bold . "[i] CMS: " . $fgreen . advanced_CMSdetect($reallink) . $cln . "\n";
        echo $lblue . $bold . "[i] Alexa Global Rank : " . $fgreen . bv_get_alexa_rank($lwwww) . $cln . "\n";
        
        bv_moz_info($lwwww);
        extract_social_links($srccd);
        extractLINKS($reallink);
    }
    
    private function wordpress_scan() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $fgreen, $red;
        
        echo "\n$cln" . $lblue . $bold . "[+] WordPress Scanner \n\n" . $cln;
        
        userinput("Enter WordPress directory (press Enter for root)");
        $wp_dir = trim(fgets(STDIN, 1024));
        
        if (empty($wp_dir)) {
            $reallink = $this->protocol . $this->target;
        } else {
            $reallink = $this->protocol . $this->target . $wp_dir;
        }
        
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n\n" . $cln;
        
        $srccd = readcontents($reallink);
        
        // Check if WordPress
        if (strpos($srccd, 'wp-content') === false && strpos($srccd, 'wp-includes') === false) {
            echo $bold . $red . "[!] WordPress not detected on this site.\n" . $cln;
            return;
        }
        
        echo $bold . $green . "[+] WordPress installation detected!\n\n" . $cln;
        
        // Basic WordPress checks
        echo $bold . $yellow . "Basic WordPress Checks:\n" . $cln;
        echo "====================\n\n";
        
        // Check readme.html
        $readme_url = $reallink . "/readme.html";
        if (check_url_exists($readme_url)) {
            echo $green . "[+] Readme file found: " . $readme_url . $cln . "\n";
        } else {
            echo $red . "[-] Readme file not found\n" . $cln;
        }
        
        // Check license.txt
        $license_url = $reallink . "/license.txt";
        if (check_url_exists($license_url)) {
            echo $green . "[+] License file found: " . $license_url . $cln . "\n";
        } else {
            echo $red . "[-] License file not found\n" . $cln;
        }
        
        // Check wp-admin directory
        $admin_url = $reallink . "/wp-admin/";
        if (check_url_exists($admin_url)) {
            echo $green . "[+] Admin directory accessible: " . $admin_url . $cln . "\n";
        }
        
        // Check XML-RPC
        $xmlrpc_url = $reallink . "/xmlrpc.php";
        $xmlrpc_content = @readcontents($xmlrpc_url);
        if ($xmlrpc_content && strpos($xmlrpc_content, 'XML-RPC') !== false) {
            echo $green . "[+] XML-RPC interface enabled: " . $xmlrpc_url . $cln . "\n";
        }
        
        echo $bold . $yellow . "\nNote: For comprehensive WordPress scanning, use dedicated WordPress security tools.\n" . $cln;
    }
    
    private function crawler() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $fgreen;
        
        echo "\n$cln" . $lblue . $bold . "[+] Website Crawler \n\n" . $cln;
        echo $blue . $bold . "[i] Scanning Site:\e[92m " . $this->protocol . $this->target . "\n\n" . $cln;
        
        echo $bold . $yellow . "Crawler Types:\n" . $cln;
        echo "1. Basic Crawler (Fast, shows only 200 responses)\n";
        echo "2. Advanced Crawler (Shows all responses except 404)\n\n";
        
        userinput("Choose crawler type (1 or 2)");
        $crawler_type = trim(fgets(STDIN, 1024));
        
        $common_paths = [
            'admin', 'administrator', 'wp-admin', 'login', 'admin.php',
            'backend', 'phpmyadmin', 'cpanel', 'webmail', 'ftp',
            'backup', 'backups', 'old', 'test', 'dev', 'staging',
            'robots.txt', 'sitemap.xml', '.git', '.env', 'config.php',
            'readme.html', 'license.txt'
        ];
        
        echo $bold . $blue . "\n[i] Scanning " . count($common_paths) . " common paths...\n\n" . $cln;
        
        $found_paths = [];
        
        foreach ($common_paths as $path) {
            $url = $this->protocol . $this->target . '/' . $path;
            $headers = @get_headers($url);
            
            if ($headers) {
                $http_code = substr($headers[0], 9, 3);
                
                if ($crawler_type == '1') {
                    // Basic crawler - show only 200 responses
                    if ($http_code == '200') {
                        echo $green . "[+] " . $url . " (HTTP " . $http_code . ")\n" . $cln;
                        $found_paths[] = $path;
                    }
                } else {
                    // Advanced crawler - show all except 404
                    if ($http_code != '404') {
                        $color = ($http_code == '200') ? $green : $yellow;
                        echo $color . "[+] " . $url . " (HTTP " . $http_code . ")\n" . $cln;
                        if ($http_code == '200') {
                            $found_paths[] = $path;
                        }
                    }
                }
            }
        }
        
        echo $bold . $blue . "\n[i] Found " . count($found_paths) . " accessible paths.\n" . $cln;
    }
    
    private function mx_lookup() {
        global $cln, $bold, $lblue, $green, $blue, $yellow;
        
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m " . $this->protocol . $this->target . "\n";
        echo $bold . $yellow . "[S] Scan Type : MX Lookup" . $cln;
        echo "\n\n";
        
        echo MXlookup($lwwww) . "\n";
    }
    
    private function advanced_link_crawler() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $fgreen, $magenta;
        
        $reallink = $this->protocol . $this->target;
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n";
        echo $bold . $yellow . "[S] Scan Type : Advanced Link Crawler" . $cln;
        echo "\n\n";
        
        $html = readcontents($reallink);
        $link_categories = advanced_link_crawl($html, $reallink);
        
        $internalLinks = $link_categories['internal'];
        $externalLinks = $link_categories['external'];
        $resourceLinks = $link_categories['resources'];
        
        // Display results
        echo $bold . $lblue . "[i] Internal Links Found: " . $green . count($internalLinks) . $cln . "\n";
        echo $bold . $lblue . "[i] External Links Found: " . $green . count($externalLinks) . $cln . "\n";
        echo $bold . $lblue . "[i] Resource Links Found: " . $green . count($resourceLinks) . $cln . "\n\n";
        
        // Show all internal links
        if (count($internalLinks) > 0) {
            echo $bold . $yellow . "Internal Links:\n" . $cln;
            foreach (array_slice($internalLinks, 0, 20) as $link) { // Show first 20 only
                echo $green . $link . $cln . "\n";
            }
            if (count($internalLinks) > 20) {
                echo $bold . $yellow . "... and " . (count($internalLinks) - 20) . " more internal links\n" . $cln;
            }
            echo "\n";
        }
        
        // Show all external links
        if (count($externalLinks) > 0) {
            echo $bold . $yellow . "External Links:\n" . $cln;
            foreach (array_slice($externalLinks, 0, 15) as $link) { // Show first 15 only
                echo $blue . $link . $cln . "\n";
            }
            if (count($externalLinks) > 15) {
                echo $bold . $yellow . "... and " . (count($externalLinks) - 15) . " more external links\n" . $cln;
            }
            echo "\n";
        }
        
        // Show all resource links
        if (count($resourceLinks) > 0) {
            echo $bold . $yellow . "Resource Links:\n" . $cln;
            foreach (array_slice($resourceLinks, 0, 10) as $link) { // Show first 10 only
                echo $magenta . $link . $cln . "\n";
            }
            if (count($resourceLinks) > 10) {
                echo $bold . $yellow . "... and " . (count($resourceLinks) - 10) . " more resource links\n" . $cln;
            }
            echo "\n";
        }
    }
    
    private function sensitive_info_scan() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $fgreen, $red;
        
        $reallink = $this->protocol . $this->target;
        
        echo "\n$cln" . $lblue . $bold . "[+] Scanning Begins ... \n";
        echo $blue . $bold . "[i] Scanning Site:\e[92m $reallink \n";
        echo $bold . $yellow . "[S] Scan Type : Sensitive Information Scanner" . $cln;
        echo "\n\n";
        
        sensitive_info_scan($reallink, $this->protocol, $this->target);
    }
    
    private function full_scan() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $red;
        
        $reallink = $this->protocol . $this->target;
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . $lblue . "[+] Full Comprehensive Scan Started ... \n";
        echo $blue . "[i] Scanning Site:\e[92m $reallink \n\n" . $cln;
        
        // Basic Information
        echo $bold . $lblue . "BASIC INFORMATION\n";
        echo "=================\n" . $cln;
        
        echo $blue . "[+] Site Title: " . $green . getTitle($reallink) . $cln . "\n";
        
        $wip = gethostbyname($this->target);
        echo $blue . "[+] IP address: " . $green . $wip . $cln . "\n";
        
        echo $blue . "[+] Web Server: ";
        WEBserver($reallink);
        echo $blue . "[+] CMS: " . $green . advanced_CMSdetect($reallink) . $cln . "\n";
        
        echo $blue . "[+] Cloudflare: ";
        cloudflaredetect($lwwww);
        
        // WHOIS
        echo $bold . $lblue . "\nWHOIS INFORMATION\n";
        echo "=================\n" . $cln;
        
        $urlwhois = "http://api.hackertarget.com/whois/?q=" . $lwwww;
        $resultwhois = file_get_contents($urlwhois);
        echo $green . $resultwhois . $cln . "\n";
        
        // Subdomains
        echo $bold . $lblue . "SUBDOMAIN SCAN\n";
        echo "==============\n" . $cln;
        
        $urlsd = "http://api.hackertarget.com/hostsearch/?q=" . $lwwww;
        $resultsd = readcontents($urlsd);
        $subdomains = array_filter(explode("\n", trim($resultsd)));
        
        // Remove API header if present
        if (strpos($subdomains[0], 'API') !== false) {
            array_shift($subdomains);
        }
        
        $sdcount = count($subdomains);
        echo $blue . "[+] Total Subdomains Found: " . $green . $sdcount . $cln . "\n";
        
        if ($sdcount > 0) {
            foreach (array_slice($subdomains, 0, 10) as $subdomain) { // Show first 10 only
                $parts = explode(',', $subdomain);
                if (count($parts) >= 2) {
                    echo "    " . trim($parts[0]) . " -> " . trim($parts[1]) . "\n";
                }
            }
            if ($sdcount > 10) {
                echo $yellow . "    ... and " . ($sdcount - 10) . " more subdomains\n" . $cln;
            }
        }
        
        echo $bold . $green . "\n[+] Full scan completed!\n" . $cln;
    }
}

// Start CATEYE only if this file is executed directly for testing
if (basename(__FILE__) == basename($_SERVER['PHP_SELF']) && php_sapi_name() === "cli") {
    new CATEYE();
}
?>
