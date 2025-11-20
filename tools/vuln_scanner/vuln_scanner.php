<?php
// Vulnerability Scanner Tool
class VulnerabilityScanner {
    private $target;
    private $protocol;
    private $results = [];
    
    public function __construct() {
        $this->show_banner();
        $this->get_target();
        $this->main_menu();
    }
    
    private function show_banner() {
        system("clear");
        global $bold, $red, $cln;
        
        echo $bold . $red . "
    +-----------------------------------+
    |      VULNERABILITY SCANNER        |
    +-----------------------------------+
        " . $cln . "\n\n";
    }
    
    private function get_target() {
        userinput("Enter target URL (e.g., example.com)");
        $this->target = trim(fgets(STDIN, 1024));
        
        echo "\n";
        userinput("Enter 1 For HTTP OR Enter 2 For HTTPS");
        $protocol_choice = trim(fgets(STDIN, 1024));
        $this->protocol = ($protocol_choice == "2") ? "https://" : "http://";
    }
    
    public function main_menu() {
        global $cln, $bold, $green, $blue, $yellow, $red;
        
        $vuln_tools = [
            '1' => 'SQL Injection Scanner',
            '2' => 'XSS Scanner',
            '3' => 'File Inclusion Checker',
            '4' => 'Command Injection Scanner',
            '5' => 'SSL/TLS Security Check',
            '6' => 'HTTP Security Headers Check',
            '7' => 'Full Vulnerability Scan',
            '0' => 'Back to Main Menu'
        ];
        
        while(true) {
            $this->show_banner();
            
            foreach($vuln_tools as $key => $tool) {
                echo $bold . $yellow . "    [$key] " . $green . $tool . $cln . "\n";
            }
            
            $choice = readline($bold . $green . "\n    Choose vulnerability scan type: " . $cln);
            
            switch($choice) {
                case '1':
                    $this->sql_injection_scan();
                    break;
                case '2':
                    $this->xss_scan();
                    break;
                case '3':
                    $this->file_inclusion_scan();
                    break;
                case '4':
                    $this->command_injection_scan();
                    break;
                case '5':
                    $this->ssl_tls_check();
                    break;
                case '6':
                    $this->security_headers_check();
                    break;
                case '7':
                    $this->full_vuln_scan();
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
    
    private function sql_injection_scan() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [SQLi] Scanning for SQL Injection vulnerabilities...\n" . $cln;
        
        $url = $this->protocol . $this->target;
        $html = readcontents($url);
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        
        $vulnerable_links = [];
        
        foreach($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos($href, '?') !== false) {
                echo $blue . "    Testing: " . $href . $cln . "\n";
                
                $test_url = (strpos($href, '://') !== false) ? $href . "'" : $url . "/" . $href . "'";
                $response = readcontents($test_url);
                
                $sql_errors = [
                    "You have an error in your SQL syntax",
                    "Warning: mysql_fetch_array()",
                    "Warning: mysql_num_rows()",
                    "Warning: mysql_query()",
                    "Unclosed quotation mark",
                    "SQL syntax.*MySQL",
                    "Warning: pg_query()",
                    "Warning: odbc_exec()",
                    "Microsoft OLE DB Provider for SQL Server",
                    "ODBC SQL Server Driver",
                    "Unclosed quotation mark after the character string"
                ];
                
                foreach($sql_errors as $error) {
                    if (strpos($response, $error) !== false) {
                        $vulnerable_links[] = $href;
                        echo $bold . $red . "    [!] Vulnerable: " . $href . $cln . "\n";
                        break;
                    }
                }
            }
        }
        
        if (count($vulnerable_links) > 0) {
            echo $bold . $red . "\n    [!] Found " . count($vulnerable_links) . " potentially vulnerable links.\n" . $cln;
        } else {
            echo $bold . $green . "\n    [*] No SQL injection vulnerabilities found.\n" . $cln;
        }
    }
    
    private function xss_scan() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [XSS] Scanning for Cross-Site Scripting vulnerabilities...\n" . $cln;
        
        $url = $this->protocol . $this->target;
        $html = readcontents($url);
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        $forms = $dom->getElementsByTagName('form');
        
        $vulnerable = [];
        
        // Test links with parameters
        foreach($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos($href, '?') !== false) {
                $vulnerable = array_merge($vulnerable, $this->test_xss_url($url, $href));
            }
        }
        
        // Test forms
        foreach($forms as $form) {
            $vulnerable = array_merge($vulnerable, $this->test_xss_form($url, $form));
        }
        
        if (count($vulnerable) > 0) {
            echo $bold . $red . "\n    [!] Found " . count($vulnerable) . " potentially vulnerable points.\n" . $cln;
        } else {
            echo $bold . $green . "\n    [*] No XSS vulnerabilities found.\n" . $cln;
        }
    }
    
    private function test_xss_url($base_url, $url) {
        $vulnerable = [];
        $xss_payload = '<script>alert("XSS")</script>';
        
        // Parse URL and parameters
        $parsed = parse_url($url);
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $params);
            foreach ($params as $key => $value) {
                $test_url = str_replace($value, $xss_payload, $url);
                $response = readcontents($test_url);
                if (strpos($response, $xss_payload) !== false) {
                    $vulnerable[] = $test_url;
                }
            }
        }
        
        return $vulnerable;
    }
    
    private function test_xss_form($base_url, $form) {
        // This is a simplified check. In reality, form testing is more complex.
        $vulnerable = [];
        $action = $form->getAttribute('action');
        $method = $form->getAttribute('method') ?: 'get';
        
        // Check if form action has parameters
        if (strpos($action, '?') !== false) {
            $vulnerable = array_merge($vulnerable, $this->test_xss_url($base_url, $action));
        }
        
        return $vulnerable;
    }
    
    private function file_inclusion_scan() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [LFI/RFI] Scanning for File Inclusion vulnerabilities...\n" . $cln;
        
        // This would typically involve testing parameters that might be used for file inclusion
        echo $bold . $yellow . "    [*] File inclusion scan not yet implemented.\n" . $cln;
    }
    
    private function command_injection_scan() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [CMDi] Scanning for Command Injection vulnerabilities...\n" . $cln;
        
        // This would typically involve testing parameters that might be used for command execution
        echo $bold . $yellow . "    [*] Command injection scan not yet implemented.\n" . $cln;
    }
    
    private function ssl_tls_check() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [SSL/TLS] Checking SSL/TLS configuration...\n" . $cln;
        
        if ($this->protocol != 'https://') {
            echo $bold . $yellow . "    [*] Target is not using HTTPS. Skipping SSL check.\n" . $cln;
            return;
        }
        
        $stream = @stream_socket_client("ssl://{$this->target}:443", $errno, $errstr, 30);
        if ($stream) {
            $params = stream_context_get_params($stream);
            $certificate = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            
            echo $green . "    Certificate Subject: " . $certificate['name'] . $cln . "\n";
            echo $green . "    Valid From: " . date('Y-m-d', $certificate['validFrom_time_t']) . $cln . "\n";
            echo $green . "    Valid To: " . date('Y-m-d', $certificate['validTo_time_t']) . $cln . "\n";
            
            $days_remaining = ($certificate['validTo_time_t'] - time()) / (60 * 60 * 24);
            if ($days_remaining < 30) {
                echo $red . "    [!] Certificate expires in " . round($days_remaining) . " days.\n" . $cln;
            }
        } else {
            echo $red . "    [!] Could not retrieve SSL certificate.\n" . $cln;
        }
    }
    
    private function security_headers_check() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [HEADERS] Checking HTTP Security Headers...\n" . $cln;
        
        $url = $this->protocol . $this->target;
        $headers = get_headers($url, 1);
        
        $security_headers = [
            'Strict-Transport-Security' => 'Recommended',
            'Content-Security-Policy' => 'Recommended',
            'X-Content-Type-Options' => 'Recommended',
            'X-Frame-Options' => 'Recommended',
            'X-XSS-Protection' => 'Recommended',
            'Referrer-Policy' => 'Optional',
            'Feature-Policy' => 'Optional'
        ];
        
        foreach ($security_headers as $header => $importance) {
            if (isset($headers[$header])) {
                echo $green . "    [✓] $header: " . $headers[$header] . $cln . "\n";
            } else {
                if ($importance == 'Recommended') {
                    echo $red . "    [✗] $header: Missing (Recommended)" . $cln . "\n";
                } else {
                    echo $yellow . "    [~] $header: Missing (Optional)" . $cln . "\n";
                }
            }
        }
    }
    
    private function full_vuln_scan() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [FULL] Starting full vulnerability scan...\n" . $cln;
        
        $this->sql_injection_scan();
        $this->xss_scan();
        $this->file_inclusion_scan();
        $this->command_injection_scan();
        $this->ssl_tls_check();
        $this->security_headers_check();
        
        echo $bold . $green . "\n    [FULL] Vulnerability scan complete!\n" . $cln;
    }
}

// Start Vulnerability Scanner
new VulnerabilityScanner();
?>
