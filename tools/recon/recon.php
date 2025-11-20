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
        global $cln, $bold, $green, $blue, $yellow;
        
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
        
        $wordlist = file('data/wordlists/subdomains.txt', FILE_IGNORE_NEW_LINES);
        $found = [];
        
        foreach($wordlist as $sub) {
            $domain = $sub . '.' . $this->target;
            $ip = gethostbyname($domain);
            
            if($ip != $domain) {
                echo $bold . $green . "    [+] Found: " . $domain . " -> " . $ip . $cln . "\n";
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
        
        foreach($common_ports as $port) {
            $connection = @fsockopen($this->target, $port, $errno, $errstr, 1);
            
            if(is_resource($connection)) {
                echo $bold . $green . "    [+] Port " . $port . " is open\n" . $cln;
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
}

// Start Advanced Reconnaissance
new AdvancedRecon();
?>
