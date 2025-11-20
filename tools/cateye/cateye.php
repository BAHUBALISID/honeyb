<?php
// CATEYE Web Reconnaissance Tool Integration
require_once 'tools/cateye/functions.php';

class CATEYE {
    private $target;
    private $protocol;
    
    public function __construct() {
        $this->show_banner();
        $this->get_target();
        $this->main_menu();
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
        userinput("Enter The Website You Want To Scan");
        $this->target = trim(fgets(STDIN, 1024));
        
        if ($this->target == 'back') {
            return false;
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
                    return;
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
                case '7':
                    $this->subdomain_scan();
                    break;
                case 'a':
                    $this->full_scan();
                    break;
                default:
                    echo $bold . $red . "\n[!] Scan option not yet implemented in HoneyB integration\n" . $cln;
            }
            
            if (!in_array(strtolower($scan), ['q', 'b'])) {
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
        unset($subdomains['0']);
        $sdcount = count($subdomains);
        
        echo "\n" . $blue . $bold . "[i] Total Subdomains Found : " . $green . $sdcount . "\n\n" . $cln;
        
        foreach ($subdomains as $subdomain) {
            echo $bold . $lblue . "[+] Subdomain: $fgreen" . (str_replace(",", "\n\e[36m[-] IP: $fgreen", $subdomain));
            echo "\n\n" . $cln;
        }
    }
    
    private function full_scan() {
        global $cln, $bold, $lblue, $green, $blue, $yellow, $red;
        
        $reallink = $this->protocol . $this->target;
        $lwwww = str_replace("www.", "", $this->target);
        
        echo "\n$cln" . "$lblue" . "[+] Full Scan Started ... \n";
        echo "$blue" . "[i] Scanning Site:\e[92m $reallink \n\n";
        
        // Basic Info
        echo $bold . $lblue . "BASIC INFORMATION\n";
        echo "=================\n" . $cln;
        
        echo $blue . "[+] Site Title: " . $green . getTitle($reallink) . $cln . "\n";
        
        $wip = gethostbyname($this->target);
        echo $blue . "[+] IP address: " . $green . $wip . $cln . "\n";
        
        echo $blue . "[+] Web Server: ";
        WEBserver($reallink);
        echo $blue . "[+] CMS: " . $green . advanced_CMSdetect($reallink) . $cln . "\n";
        
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
        $subdomains = trim($resultsd, "\n");
        $subdomains = explode("\n", $subdomains);
        unset($subdomains['0']);
        $sdcount = count($subdomains);
        
        echo $blue . "[+] Total Subdomains Found: " . $green . $sdcount . $cln . "\n";
        
        foreach ($subdomains as $subdomain) {
            echo $green . "    " . str_replace(",", " -> ", $subdomain) . $cln . "\n";
        }
        
        echo $bold . $green . "\n[+] Full scan completed!\n" . $cln;
    }
}

// Check if we have the required functions
if (!function_exists('getTitle')) {
    echo "Error: CATEYE functions not found. Please check the installation.\n";
    exit;
}

// Start CATEYE
new CATEYE();
?>
