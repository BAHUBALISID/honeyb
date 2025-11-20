<?php
// Network Tools
class NetworkTools {
    private $target;
    
    public function __construct() {
        $this->show_banner();
        $this->get_target();
        $this->main_menu();
    }
    
    private function show_banner() {
        system("clear");
        global $bold, $magenta, $cln;
        
        echo $bold . $magenta . "
    +-----------------------------------+
    |           NETWORK TOOLS           |
    +-----------------------------------+
        " . $cln . "\n\n";
    }
    
    private function get_target() {
        userinput("Enter target IP or domain");
        $this->target = trim(fgets(STDIN, 1024));
    }
    
    public function main_menu() {
        global $cln, $bold, $green, $blue, $yellow, $red;
        
        $network_tools = [
            '1' => 'Ping',
            '2' => 'Traceroute',
            '3' => 'DNS Lookup',
            '4' => 'Reverse DNS Lookup',
            '5' => 'Port Scanner (Advanced)',
            '6' => 'WHOIS Lookup',
            '7' => 'Subnet Calculator',
            '8' => 'Network Mapper',
            '0' => 'Back to Main Menu'
        ];
        
        while(true) {
            $this->show_banner();
            
            foreach($network_tools as $key => $tool) {
                echo $bold . $yellow . "    [$key] " . $green . $tool . $cln . "\n";
            }
            
            $choice = readline($bold . $green . "\n    Choose network tool: " . $cln);
            
            switch($choice) {
                case '1':
                    $this->ping();
                    break;
                case '2':
                    $this->traceroute();
                    break;
                case '3':
                    $this->dns_lookup();
                    break;
                case '4':
                    $this->reverse_dns();
                    break;
                case '5':
                    $this->port_scanner();
                    break;
                case '6':
                    $this->whois_lookup();
                    break;
                case '7':
                    $this->subnet_calculator();
                    break;
                case '8':
                    $this->network_mapper();
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
    
    private function ping() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [PING] Pinging " . $this->target . "...\n" . $cln;
        
        $command = "ping -c 4 " . escapeshellarg($this->target);
        system($command);
    }
    
    private function traceroute() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [TRACEROUTE] Tracing route to " . $this->target . "...\n" . $cln;
        
        $command = "traceroute " . escapeshellarg($this->target);
        system($command);
    }
    
    private function dns_lookup() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [DNS] Performing DNS lookup...\n" . $cln;
        
        $records = dns_get_record($this->target, DNS_ALL);
        foreach ($records as $record) {
            echo $green . "    " . $record['type'] . ": " . $record['host'] . " => " . (isset($record['ip']) ? $record['ip'] : (isset($record['target']) ? $record['target'] : '')) . $cln . "\n";
        }
    }
    
    private function reverse_dns() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [RDNS] Performing reverse DNS lookup...\n" . $cln;
        
        $ip = gethostbyname($this->target);
        $hostname = gethostbyaddr($ip);
        
        echo $green . "    IP: " . $ip . $cln . "\n";
        echo $green . "    Hostname: " . $hostname . $cln . "\n";
    }
    
    private function port_scanner() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [PORT SCAN] Scanning ports on " . $this->target . "...\n" . $cln;
        
        $common_ports = [
            21, 22, 23, 25, 53, 80, 110, 443, 993, 995, 
            1433, 3306, 3389, 5432, 5900, 8080, 8443
        ];
        
        $open_ports = [];
        
        foreach ($common_ports as $port) {
            $connection = @fsockopen($this->target, $port, $errno, $errstr, 1);
            if (is_resource($connection)) {
                echo $green . "    [+] Port " . $port . " is open\n" . $cln;
                $open_ports[] = $port;
                fclose($connection);
            }
        }
        
        if (count($open_ports) > 0) {
            echo $bold . $green . "\n    Found " . count($open_ports) . " open ports.\n" . $cln;
        } else {
            echo $bold . $red . "\n    No open ports found.\n" . $cln;
        }
    }
    
    private function whois_lookup() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [WHOIS] Performing WHOIS lookup...\n" . $cln;
        
        $whois_data = readcontents("http://api.hackertarget.com/whois/?q=" . $this->target);
        echo $green . $whois_data . $cln;
    }
    
    private function subnet_calculator() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [SUBNET] Subnet calculator...\n" . $cln;
        
        // This would typically calculate subnets based on IP and mask
        echo $bold . $yellow . "    [*] Subnet calculator not yet implemented.\n" . $cln;
    }
    
    private function network_mapper() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [NMAP] Network mapping...\n" . $cln;
        
        // This would use nmap if available
        if ($this->is_command_available('nmap')) {
            $command = "nmap -sP " . escapeshellarg($this->target);
            system($command);
        } else {
            echo $bold . $red . "    nmap is not available on this system.\n" . $cln;
        }
    }
    
    private function is_command_available($command) {
        $output = [];
        $return_code = 0;
        exec("which $command", $output, $return_code);
        return $return_code === 0;
    }
}

// Start Network Tools
new NetworkTools();
?>
