<?php
// Reporting & Analytics Tool
class ReportGenerator {
    private $reports = [];
    
    public function __construct() {
        $this->show_banner();
        $this->main_menu();
    }
    
    private function show_banner() {
        system("clear");
        global $bold, $cyan, $cln;
        
        echo $bold . $cyan . "
    +-----------------------------------+
    |       REPORTING & ANALYTICS       |
    +-----------------------------------+
        " . $cln . "\n\n";
    }
    
    public function main_menu() {
        global $cln, $bold, $green, $blue, $yellow, $red;
        
        $report_tools = [
            '1' => 'Generate Security Report',
            '2' => 'View Scan History',
            '3' => 'Export Results',
            '4' => 'Analytics Dashboard',
            '0' => 'Back to Main Menu'
        ];
        
        while(true) {
            $this->show_banner();
            
            foreach($report_tools as $key => $tool) {
                echo $bold . $yellow . "    [$key] " . $green . $tool . $cln . "\n";
            }
            
            $choice = readline($bold . $green . "\n    Choose report option: " . $cln);
            
            switch($choice) {
                case '1':
                    $this->generate_report();
                    break;
                case '2':
                    $this->view_history();
                    break;
                case '3':
                    $this->export_results();
                    break;
                case '4':
                    $this->analytics_dashboard();
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
    
    private function generate_report() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [REPORT] Generating security report...\n" . $cln;
        
        userinput("Enter target name");
        $target = trim(fgets(STDIN, 1024));
        
        userinput("Enter scan type");
        $scan_type = trim(fgets(STDIN, 1024));
        
        userinput("Enter findings");
        $findings = trim(fgets(STDIN, 1024));
        
        $report = [
            'target' => $target,
            'scan_type' => $scan_type,
            'findings' => $findings,
            'date' => date('Y-m-d H:i:s'),
            'report_id' => uniqid()
        ];
        
        $this->reports[] = $report;
        
        $filename = $this->save_report($report);
        
        echo $bold . $green . "\n    Report generated: " . $filename . $cln . "\n";
    }
    
    private function save_report($report) {
        $reports_dir = 'reports';
        if (!is_dir($reports_dir)) {
            mkdir($reports_dir, 0755, true);
        }
        
        $filename = $reports_dir . '/report_' . $report['report_id'] . '.json';
        file_put_contents($filename, json_encode($report, JSON_PRETTY_PRINT));
        
        return $filename;
    }
    
    private function view_history() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [HISTORY] Scan history...\n" . $cln;
        
        $reports_dir = 'reports';
        if (!is_dir($reports_dir)) {
            echo $bold . $red . "    No reports found.\n" . $cln;
            return;
        }
        
        $files = glob($reports_dir . '/*.json');
        if (count($files) === 0) {
            echo $bold . $red . "    No reports found.\n" . $cln;
            return;
        }
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $report = json_decode($content, true);
            
            echo $green . "    Report ID: " . $report['report_id'] . $cln . "\n";
            echo $green . "    Target: " . $report['target'] . $cln . "\n";
            echo $green . "    Scan Type: " . $report['scan_type'] . $cln . "\n";
            echo $green . "    Date: " . $report['date'] . $cln . "\n";
            echo "    ---\n";
        }
    }
    
    private function export_results() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [EXPORT] Exporting results...\n" . $cln;
        
        userinput("Enter report ID to export");
        $report_id = trim(fgets(STDIN, 1024));
        
        $filename = 'reports/report_' . $report_id . '.json';
        if (!file_exists($filename)) {
            echo $bold . $red . "    Report not found.\n" . $cln;
            return;
        }
        
        userinput("Enter export format (txt/json/html)");
        $format = trim(fgets(STDIN, 1024));
        
        $content = file_get_contents($filename);
        $report = json_decode($content, true);
        
        $export_filename = 'exports/report_' . $report_id . '.' . $format;
        if (!is_dir('exports')) {
            mkdir('exports', 0755, true);
        }
        
        switch ($format) {
            case 'txt':
                $export_content = $this->format_txt($report);
                break;
            case 'json':
                $export_content = $content;
                break;
            case 'html':
                $export_content = $this->format_html($report);
                break;
            default:
                echo $bold . $red . "    Unsupported format.\n" . $cln;
                return;
        }
        
        file_put_contents($export_filename, $export_content);
        echo $bold . $green . "    Exported to: " . $export_filename . $cln . "\n";
    }
    
    private function format_txt($report) {
        $content = "Security Scan Report\n";
        $content .= "===================\n\n";
        $content .= "Target: " . $report['target'] . "\n";
        $content .= "Scan Type: " . $report['scan_type'] . "\n";
        $content .= "Date: " . $report['date'] . "\n";
        $content .= "Findings:\n" . $report['findings'] . "\n";
        
        return $content;
    }
    
    private function format_html($report) {
        $content = "<html>\n<head>\n<title>Security Scan Report</title>\n</head>\n<body>\n";
        $content .= "<h1>Security Scan Report</h1>\n";
        $content .= "<p><strong>Target:</strong> " . $report['target'] . "</p>\n";
        $content .= "<p><strong>Scan Type:</strong> " . $report['scan_type'] . "</p>\n";
        $content .= "<p><strong>Date:</strong> " . $report['date'] . "</p>\n";
        $content .= "<h2>Findings</h2>\n";
        $content .= "<pre>" . $report['findings'] . "</pre>\n";
        $content .= "</body>\n</html>";
        
        return $content;
    }
    
    private function analytics_dashboard() {
        global $bold, $blue, $green, $cln;
        
        echo $bold . $blue . "\n    [ANALYTICS] Analytics dashboard...\n" . $cln;
        
        $reports_dir = 'reports';
        if (!is_dir($reports_dir)) {
            echo $bold . $red . "    No reports found.\n" . $cln;
            return;
        }
        
        $files = glob($reports_dir . '/*.json');
        $total_reports = count($files);
        
        echo $green . "    Total Reports: " . $total_reports . $cln . "\n";
        
        $scan_types = [];
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $report = json_decode($content, true);
            $scan_type = $report['scan_type'];
            
            if (!isset($scan_types[$scan_type])) {
                $scan_types[$scan_type] = 0;
            }
            $scan_types[$scan_type]++;
        }
        
        echo $green . "    Scan Types:\n" . $cln;
        foreach ($scan_types as $type => $count) {
            echo "      " . $type . ": " . $count . "\n";
        }
    }
}

// Start Report Generator
new ReportGenerator();
?>
