<?php
class Utils {
    public static function format_size($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    public static function extract_domain($url) {
        $parsed = parse_url($url);
        return isset($parsed['host']) ? $parsed['host'] : $url;
    }
    
    public static function generate_random_string($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_string = '';
        
        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $random_string;
    }
    
    public static function is_running_on_linux() {
        return strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN';
    }
    
    public static function create_directory($path) {
        if (!is_dir($path)) {
            return mkdir($path, 0755, true);
        }
        return true;
    }
}
?>
