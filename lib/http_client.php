<?php
class HTTPClient {
    private $timeout;
    private $user_agent;
    private $follow_redirects;
    
    public function __construct($timeout = 30, $user_agent = null, $follow_redirects = true) {
        $this->timeout = $timeout;
        $this->user_agent = $user_agent ?: USER_AGENT;
        $this->follow_redirects = $follow_redirects;
    }
    
    public function get($url, $headers = []) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_FOLLOWLOCATION => $this->follow_redirects,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        return [
            'body' => $response,
            'http_code' => $http_code,
            'error' => $error
        ];
    }
    
    public function post($url, $data, $headers = []) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_FOLLOWLOCATION => $this->follow_redirects,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        return [
            'body' => $response,
            'http_code' => $http_code,
            'error' => $error
        ];
    }
    
    public function check_url($url) {
        $result = $this->get($url);
        return $result['http_code'] == 200;
    }
}
?>
