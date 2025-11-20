<?php
// API Security Scanner Tool
class APIScanner {
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
        global $bold, $cyan, $cln;
        
        echo $bold . $cyan . "
    +-----------------------------------+
    |         API SECURITY SCANNER      |
    +-----------------------------------+
        " . $cln . "\n\n";
    }
    
    private function get_target() {
        userinput("Enter target domain (e.g., api.example.com)");
        $this->target = trim(fgets(STDIN, 1024));
        
        echo "\n";
        userinput("Enter 1 For HTTP OR Enter 2 For HTTPS");
        $protocol_choice = trim(fgets(STDIN, 1024));
        $this->protocol = ($protocol_choice == "2") ? "https://" : "http://";
    }
    
    public function main_menu() {
        global $cln, $bold, $green, $blue, $yellow, $red;
        
        $api_tools = [
            '1' => 'API Endpoint Discovery',
            '2' => 'Authentication Testing',
            '3' => 'Input Validation Testing',
            '4' => 'HTTP Method Testing',
            '5' => 'Rate Limiting Testing',
            '6' => 'Sensitive Data Exposure',
            '7' => 'Full API Security Scan',
            '0' => 'Back to Main Menu'
        ];
        
        while(true) {
            $this->show_banner();
            
            foreach($api_tools as $key => $tool) {
                echo $bold . $yellow . "    [$key] " . $green . $tool . $cln . "\n";
            }
            
            $choice = readline($bold . $green . "\n    Choose API scan type: " . $cln);
            
            switch($choice) {
                case '1':
                    $this->endpoint_discovery();
                    break;
                case '2':
                    $this->auth_testing();
                    break;
                case '3':
                    $this->input_validation_testing();
                    break;
                case '4':
                    $this->http_method_testing();
                    break;
                case '5':
                    $this->rate_limiting_testing();
                    break;
                case '6':
                    $this->sensitive_data_testing();
                    break;
                case '7':
                    $this->full_api_scan();
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
    
    private function endpoint_discovery() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [ENDPOINTS] Discovering API endpoints...\n" . $cln;
        
        $common_endpoints = [
            '/api/v1/users',
            '/api/v1/products',
            '/api/v1/admin',
            '/api/v1/config',
            '/api/v1/tokens',
            '/api/v1/auth',
            '/graphql',
            '/rest/v1',
            '/soap',
            '/xmlrpc'
        ];
        
        $found_endpoints = [];
        
        foreach ($common_endpoints as $endpoint) {
            $url = $this->protocol . $this->target . $endpoint;
            $response = readcontents($url);
            
            if ($response && !strpos($response, '404 Not Found')) {
                $found_endpoints[] = $endpoint;
                echo $green . "    [+] Found: " . $endpoint . $cln . "\n";
            }
        }
        
        if (count($found_endpoints) > 0) {
            echo $bold . $green . "\n    Found " . count($found_endpoints) . " API endpoints.\n" . $cln;
        } else {
            echo $bold . $red . "\n    No common API endpoints found.\n" . $cln;
        }
    }
    
    private function auth_testing() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [AUTH] Testing API authentication...\n" . $cln;
        
        // Test for common authentication endpoints
        $auth_endpoints = [
            '/api/v1/auth',
            '/api/v1/login',
            '/api/v1/token',
            '/oauth/token'
        ];
        
        foreach ($auth_endpoints as $endpoint) {
            $url = $this->protocol . $this->target . $endpoint;
            if (check_url($url)) {
                echo $green . "    [+] Authentication endpoint: " . $endpoint . $cln . "\n";
                
                // Test with empty credentials
                $response = readcontents($url);
                if (strpos($response, 'token') !== false || strpos($response, 'access') !== false) {
                    echo $yellow . "    [!] Endpoint may be returning tokens.\n" . $cln;
                }
            }
        }
    }
    
    private function input_validation_testing() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [INPUT] Testing input validation...\n" . $cln;
        
        // This would involve sending various malicious inputs to API endpoints
        echo $bold . $yellow . "    [*] Input validation testing not yet implemented.\n" . $cln;
    }
    
    private function http_method_testing() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [HTTP] Testing HTTP methods...\n" . $cln;
        
        $endpoints = ['/api/v1/users', '/api/v1/products'];
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];
        
        foreach ($endpoints as $endpoint) {
            $url = $this->protocol . $this->target . $endpoint;
            echo $blue . "    Testing: " . $endpoint . $cln . "\n";
            
            foreach ($methods as $method) {
                $response = $this->test_http_method($url, $method);
                if ($response['http_code'] != 404 && $response['http_code'] != 405) {
                    echo $green . "      " . $method . ": " . $response['http_code'] . $cln . "\n";
                }
            }
        }
    }
    
    private function test_http_method($url, $method) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => 10
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return ['http_code' => $http_code, 'body' => $response];
    }
    
    private function rate_limiting_testing() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [RATE] Testing rate limiting...\n" . $cln;
        
        $url = $this->protocol . $this->target . '/api/v1/users';
        $responses = [];
        
        for ($i = 0; $i < 10; $i++) {
            $response = readcontents($url);
            $responses[] = $response;
        }
        
        // Check if responses change (e.g., rate limit exceeded)
        $unique_responses = array_unique($responses);
        if (count($unique_responses) > 1) {
            echo $green . "    [+] Rate limiting may be in effect.\n" . $cln;
        } else {
            echo $yellow . "    [-] No rate limiting detected.\n" . $cln;
        }
    }
    
    private function sensitive_data_testing() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [DATA] Checking for sensitive data exposure...\n" . $cln;
        
        $endpoints = ['/api/v1/users', '/api/v1/admin'];
        
        foreach ($endpoints as $endpoint) {
            $url = $this->protocol . $this->target . $endpoint;
            $response = readcontents($url);
            
            $sensitive_patterns = [
                '/password/i',
                '/token/i',
                '/secret/i',
                '/key/i',
                '/credit.card/i',
                '/ssn/i'
            ];
            
            foreach ($sensitive_patterns as $pattern) {
                if (preg_match($pattern, $response)) {
                    echo $red . "    [!] Sensitive data pattern found in: " . $endpoint . $cln . "\n";
                    break;
                }
            }
        }
    }
    
    private function full_api_scan() {
        global $bold, $blue, $green, $red, $cln;
        
        echo $bold . $blue . "\n    [FULL] Starting full API security scan...\n" . $cln;
        
        $this->endpoint_discovery();
        $this->auth_testing();
        $this->input_validation_testing();
        $this->http_method_testing();
        $this->rate_limiting_testing();
        $this->sensitive_data_testing();
        
        echo $bold . $green . "\n    [FULL] API security scan complete!\n" . $cln;
    }
}

// Start API Security Scanner
new APIScanner();
?>
