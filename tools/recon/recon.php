<?php
// Advanced Reconnaissance Tool
class AdvancedRecon {
    private $target;
    private $results = [];
    
    public function __construct() {
        $this->show_banner();
        $this->get_target();
        $this->main_menu();
    }
    
    private function show_banner() {
        system("clear");
        global $bold, $blue, $cln;
        
        echo $bold . $blue . "
    +-----------------------------------+
    |      ADVANCED RECONNAISSANCE      |
    +-----------------------------------+
        " . $cln . "\n\n";
    }
    
    private function get_target() {
        userinput("Enter target domain or IP");
        $this->target = trim(fgets(STDIN, 1024));
    }
    
    public function main_menu() {
        global $cln, $bold, $green, $blue, $yellow, $red;
        
        $recon_tools = [
            '1' => 'Subdomain Enumeration',
            '2' => 'Port Scanning',
            '3' => 'WHOIS Lookup',
            '4' => 'DNS Enumeration',
            '5' => 'Technology Detection',
            '6' => 'Email Harvesting',
            '7' => 'Cloud Infrastructure Recon',
            '8' => 'Full Reconnaissance Suite',
            '0' => 'Back to Main Menu'
        ];
        
        while(true) {
            $this->show_banner();
            
            foreach($recon_tools as $key => $tool) {
                echo $bold . $yellow . "    [$key] " . $green . $tool . $cln . "\n";
            }
            
            $choice = readline($bold . $green . "\n    Choose reconnaissance method: " . $cln);
            
            switch($choice) {
                case '1':
                    $this->subdomain_scan();
                    break;
                case '2':
                    $this->port_scan();
                    break;
                case '3':
                    $this->whois_lookup();
                    break;
                case '4':
                    $this->dns_enum();
                    break;
                case '5':
                    $this->tech_detection();
                    break;
                case '6':
                    $this->email_harvesting();
                    break;
                case '7':
                    $this->cloud_recon();
                    break;
                case '8':
                    $this->full_recon();
                    break;
                case '0':
                    return;
                default:
                    echo $bold . $red . "\n    Invalid option!\n" . $cln;
                    sleep(2);
            }
            
            if($choice != '0') {
                readline($bold . $yellow . "\n    Press Enter to continue..." . $cln);
            }
        }
    }
    
    private function subdomain_scan() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [SUBDOMAIN] Scanning for subdomains...\n" . $cln;
        
        // Check if wordlist exists
        $wordlist_path = WORDLIST_PATH . 'subdomains.txt';
        if (!file_exists($wordlist_path)) {
            echo $bold . $red . "    Subdomain wordlist not found at: $wordlist_path\n" . $cln;
            return;
        }
        
        $wordlist = file($wordlist_path, FILE_IGNORE_NEW_LINES);
        $found = [];
        
        $total = count($wordlist);
        $current = 0;
        
        foreach($wordlist as $sub) {
            $current++;
            $domain = $sub . '.' . $this->target;
            show_progress($current, $total, "    Scanning subdomains");
            
            $ip = gethostbyname($domain);
            
            if($ip != $domain) {
                echo $bold . $green . "\n    [+] Found: " . $domain . " -> " . $ip . $cln;
                $found[] = $domain;
            }
        }
        
        echo $bold . $blue . "\n    [SUBDOMAIN] Found " . count($found) . " subdomains\n" . $cln;
        $this->results['subdomains'] = $found;
    }
    
    private function port_scan() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [PORT SCAN] Scanning common ports...\n" . $cln;
        
        $common_ports = [21, 22, 23, 25, 53, 80, 110, 443, 993, 995, 8080, 8443];
        $open_ports = [];
        
        $total = count($common_ports);
        $current = 0;
        
        foreach($common_ports as $port) {
            $current++;
            show_progress($current, $total, "    Scanning ports");
            
            $connection = @fsockopen($this->target, $port, $errno, $errstr, 1);
            
            if(is_resource($connection)) {
                echo $bold . $green . "\n    [+] Port " . $port . " is open\n" . $cln;
                $open_ports[] = $port;
                fclose($connection);
            }
        }
        
        echo $bold . $blue . "\n    [PORT SCAN] Found " . count($open_ports) . " open ports\n" . $cln;
        $this->results['open_ports'] = $open_ports;
    }
    
    private function whois_lookup() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [WHOIS] Looking up WHOIS information...\n" . $cln;
        
        $whois_data = readcontents("http://api.hackertarget.com/whois/?q=" . $this->target);
        echo $bold . $green . $whois_data . $cln . "\n";
        
        $this->results['whois'] = $whois_data;
    }
    
    private function dns_enum() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [DNS] Enumerating DNS records...\n" . $cln;
        
        $dns_types = [DNS_A, DNS_NS, DNS_CNAME, DNS_MX, DNS_TXT];
        $dns_names = ['A', 'NS', 'CNAME', 'MX', 'TXT'];
        
        for ($i = 0; $i < count($dns_types); $i++) {
            $records = @dns_get_record($this->target, $dns_types[$i]);
            if ($records) {
                echo $bold . $green . "    " . $dns_names[$i] . " Records:\n" . $cln;
                foreach ($records as $record) {
                    echo "      " . print_r($record, true) . "\n";
                }
            }
        }
    }
    
    private function tech_detection() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [TECH] Detecting technologies...\n" . $cln;
        
        $url = "http://" . $this->target;
        $content = readcontents($url);
        
        $technologies = [];
        
        // Check for WordPress
        if (strpos($content, 'wp-content') !== false) {
            $technologies[] = 'WordPress';
        }
        
        // Check for Joomla
        if (strpos($content, 'joomla') !== false) {
            $technologies[] = 'Joomla';
        }
        
        // Check for Drupal
        if (strpos($content, 'Drupal') !== false) {
            $technologies[] = 'Drupal';
        }
        
        // Check for specific scripts
        if (strpos($content, 'jquery') !== false) {
            $technologies[] = 'jQuery';
        }
        
        if (strpos($content, 'bootstrap') !== false) {
            $technologies[] = 'Bootstrap';
        }
        
        if (count($technologies) > 0) {
            echo $bold . $green . "    Detected: " . implode(', ', $technologies) . $cln . "\n";
        } else {
            echo $bold . $red . "    No known technologies detected." . $cln . "\n";
        }
    }
    
    private function email_harvesting() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [EMAIL] Harvesting emails...\n" . $cln;
        
        $url = "http://" . $this->target;
        $content = readcontents($url);
        
        preg_match_all('/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i', $content, $matches);
        
        $emails = array_unique($matches[0]);
        
        if (count($emails) > 0) {
            foreach ($emails as $email) {
                echo $bold . $green . "    " . $email . $cln . "\n";
            }
        } else {
            echo $bold . $red . "    No emails found." . $cln . "\n";
        }
    }
    
    private function cloud_recon() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [CLOUD] Checking for cloud infrastructure...\n" . $cln;
        
        // Check for AWS S3 buckets
        $s3_url = "http://" . $this->target . ".s3.amazonaws.com";
        if (check_url($s3_url)) {
            echo $bold . $green . "    AWS S3 bucket found: " . $s3_url . $cln . "\n";
        }
        
        // Check for Azure
        $azure_url = "http://" . $this->target . ".blob.core.windows.net";
        if (check_url($azure_url)) {
            echo $bold . $green . "    Azure Blob Storage found: " . $azure_url . $cln . "\n";
        }
        
        // Check for Google Cloud
        $gcloud_url = "http://" . $this->target . ".storage.googleapis.com";
        if (check_url($gcloud_url)) {
            echo $bold . $green . "    Google Cloud Storage found: " . $gcloud_url . $cln . "\n";
        }
    }
    
    private function full_recon() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [FULL] Starting full reconnaissance...\n" . $cln;
        
        $this->subdomain_scan();
        $this->port_scan();
        $this->whois_lookup();
        $this->dns_enum();
        $this->tech_detection();
        $this->email_harvesting();
        $this->cloud_recon();
        
        echo $bold . $green . "\n    [FULL] Reconnaissance complete!\n" . $cln;
    }
}

// Check for required function
if (!function_exists('check_url')) {
    function check_url($url) {
        $headers = @get_headers($url);
        if ($headers && strpos($headers[0], '200')) {
            return true;
        }
        return false;
    }
}

// Start Advanced Reconnaissance
new AdvancedRecon();
?>
