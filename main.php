<?php
// HoneyB - Comprehensive Security Tool Suite
error_reporting(0);

// Load configuration and libraries
require_once 'config/config.php';
require_once 'config/colors.php';
require_once 'lib/common_functions.php';
require_once 'lib/http_client.php';
require_once 'lib/utils.php';

class HoneyB {
    private $tools = [];
    private $current_tool = '';
    
    public function __construct() {
        $this->load_tools();
        $this->show_banner();
        $this->main_menu();
    }
    
    private function load_tools() {
        $this->tools = [
            '1' => ['name' => 'CATEYE Web Recon', 'path' => 'tools/cateye/cateye.php'],
            '2' => ['name' => 'Advanced Reconnaissance', 'path' => 'tools/recon/recon.php'],
            '3' => ['name' => 'Vulnerability Scanner', 'path' => 'tools/vuln_scanner/vuln_scanner.php'],
            '4' => ['name' => 'API Security Scanner', 'path' => 'tools/api_security/api_scanner.php'],
            '5' => ['name' => 'Network Tools', 'path' => 'tools/network/network_tools.php'],
            '6' => ['name' => 'Reporting & Analytics', 'path' => 'tools/reporting/report_generator.php'],
            '7' => ['name' => 'Update HoneyB', 'path' => 'update'],
            '8' => ['name' => 'Fix Dependencies', 'path' => 'fix'],
            '0' => ['name' => 'Exit', 'path' => 'exit']
        ];
    }
    
    private function show_banner() {
        system("clear");
        include 'banner.php';
    }
    
    public function main_menu() {
        global $cln, $bold, $green, $blue, $yellow, $red;
        
        while(true) {
            $this->show_banner();
            
            echo $bold . $blue . "\n    +-----------------------------------+\n";
            echo "    |        HONEYB MAIN MENU          |\n";
            echo "    +-----------------------------------+\n\n" . $cln;
            
            foreach($this->tools as $key => $tool) {
                echo $bold . $yellow . "    [$key] " . $green . $tool['name'] . $cln . "\n";
            }
            
            echo $bold . $blue . "\n    +-----------------------------------+\n" . $cln;
            
            $choice = readline($bold . $green . "\n    Choose an option: " . $cln);
            
            if($choice == '0') {
                echo $bold . $yellow . "\n    Thank you for using HoneyB!\n" . $cln;
                exit;
            }
            
            if(isset($this->tools[$choice])) {
                $this->launch_tool($this->tools[$choice]);
            } else {
                echo $bold . $red . "\n    Invalid option! Please try again.\n" . $cln;
                sleep(2);
            }
        }
    }
    
    private function launch_tool($tool) {
        if($tool['path'] == 'update') {
            $this->update_honeyb();
        } elseif($tool['path'] == 'fix') {
            $this->fix_dependencies();
        } else {
            if(file_exists($tool['path'])) {
                require_once $tool['path'];
            } else {
                echo $bold . $red . "\n    Tool not found: " . $tool['path'] . $cln . "\n";
                sleep(2);
            }
        }
    }
    
    private function update_honeyb() {
        global $cln, $bold, $green;
        
        echo $bold . $yellow . "\n    [UPDATE] Updating HoneyB...\n" . $cln;
        system("git pull origin master");
        echo $bold . $green . "\n    [UPDATE] Update completed!\n" . $cln;
        sleep(2);
    }
    
    private function fix_dependencies() {
        global $cln, $bold, $green, $red;
        
        echo $bold . $yellow . "\n    [FIX] Checking dependencies...\n" . $cln;
        
        $required_extensions = ['curl', 'dom', 'json', 'simplexml'];
        $missing = [];
        
        foreach($required_extensions as $ext) {
            if(!extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }
        
        if(empty($missing)) {
            echo $bold . $green . "\n    [FIX] All dependencies are installed!\n" . $cln;
        } else {
            echo $bold . $red . "\n    [FIX] Missing extensions: " . implode(', ', $missing) . $cln . "\n";
            echo $bold . $yellow . "\n    Install them using: sudo apt-get install php-" . implode(' php-', $missing) . $cln . "\n";
        }
        
        sleep(3);
    }
}

// Start HoneyB
new HoneyB();
?>
