<?php
namespace App;
class HttpRequest
{
    private false|\CurlHandle $ch;
    private string $baseUrl;
    private array $headers;

    private string $cookie_string = '';

    public function __construct(string $baseUrl = '')
    {
        $this->ch = curl_init();
        $this->baseUrl = $baseUrl;
        $this->headers = [];
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HEADER, true);

    }

    public function setCookie(string $cookie_file_path):static
    {
        if(is_file($cookie_file_path)){
            curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookie_file_path);
           // curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie_file_path);
        }else{
            exit('File path does not exist or is not readable: '.$cookie_file_path);
        }
        return $this;
    }

    public function setBaseURL(string $url):void
    {
        if(filter_var($url, FILTER_VALIDATE_URL)) {
            $this->baseUrl = $url;
        }else{
            exit('Invalid URL');
        }

    }

    public function setHeader($header): static
    {
        $this->headers[] = $header;
        return $this;
    }


    public function setUserAgent(string $userAgent):void
    {
        curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
    }

    public function get($endpoint, $params = []):\stdClass
    {
        $url = $this->baseUrl . $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HTTPGET, true);
        if($this->cookie_string){
            curl_setopt($this->ch, CURLOPT_COOKIE, $this->cookie_string);
        }

        return $this->execute();
    }

    public function post($endpoint, $data, $json = false): \stdClass
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($this->ch, CURLOPT_POST, true);

        if ($json) {
            $this->setHeader('Content-Type: application/json');
            $data = json_encode($data);
        }


        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        return $this->execute();
    }




    public function put($endpoint, $data, $json = false): \stdClass
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        if ($json) {
            $this->setHeader('Content-Type: application/json');
            $data = json_encode($data);
        }

        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        return $this->execute();
    }

    public function delete($endpoint): \stdClass
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->execute();
    }

    private function execute(): \stdClass
    {
        if (!empty($this->headers)) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        $response = curl_exec($this->ch);
        $headerSize = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
        $statusCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        $responseHeaders = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $obj = new \stdClass();
        $obj->status  = $statusCode;
        $obj->headers = $responseHeaders;
        $obj->body = $body;
        return $obj;
    }

    private function parseHeaders($headerContent):array
    {
        $headers = [];
        foreach (explode("\r\n", $headerContent) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                $arr = explode(': ', $line, 2);
                if(count($arr) > 1) {
                    list($key, $value) = $arr;
                }
                if (isset($key) && isset($value)) {
                    $headers[$key] = $value;
                }
            }
        }
        return $headers;
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }
}
