<?php

namespace izosa\page;

use voku\helper\HtmlDomParser;

/**
 * Page Downloader and Parcer
 * @author izosa@msn.com
 */
class Page {

    private $url;
    private $html;
    private $statusCode;
    private $dom = null;
    private $handler;
    private $proxy;
    private $filename;
    private $useragent;


    public static $default_useragent = [
        'os_type' => 'Windows',
        'device_type' => 'Desktop'
    ];

    public function __construct($url,$proxy = '', $useragent = []){
        $this->url = $url;
        $this->setProxy($proxy);
        $this->setUserAgent([]);
    }

    /**
     * Load Page from existing file
     * @param $filename
     * @return Page
     * @throws \Exception
     */
    public static function loadFromFile($filename){
        if(file_exists($filename)){
            $object = new self('');
            $object->html = file_get_contents($filename);
            $object->setFile($filename);
            return $object;
        } else {
            throw  new \Exception('File not exist');
        }
    }

    /**
     * Set Proxy
     * @param $proxy
     */
    public function setProxy($proxy){
        if(is_array($proxy)){
            $this->proxy = $proxy[array_rand($proxy)];
        } else {
            $this->proxy = $proxy;
        }
    }

    /**
     * Set User Agent
     * @doc https://github.com/joecampo/random-user-agent
     * @param string $useragent
     * @throws \Exception
     */
    public function setUserAgent(array $useragent = []){

        if(empty($useragent)){
            $useragent = self::$default_useragent;
        }

        if(is_array($useragent)){
            $this->useragent =\Campo\UserAgent::random($useragent);
        } else {
            throw  new \Exception('UserAgent must be array');
        }
    }

    /**
     * Download Page
     */
    public function download(){

        //init
        $this->handler = curl_init();
        curl_setopt($this->handler, CURLOPT_URL, $this->url);

        // proxy
        if(!empty($this->proxy))
        {
            curl_setopt($this->handler, CURLOPT_HTTPPROXYTUNNEL, 0);
            curl_setopt($this->handler, CURLOPT_PROXY, $this->proxy);
        }
        //user agent
        curl_setopt($this->handler, CURLOPT_USERAGENT, $this->useragent);

        // options
        curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST,'GET');
        curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->handler, CURLOPT_MAXREDIRS, 100);
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handler, CURLOPT_CONNECTTIMEOUT, 5);
        $this->html = curl_exec($this->handler);
        $this->statusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);
        curl_close($this->handler);
    }

    /**
     * Log Page Result
     */
    public function log(){
        $log = new Log();
        $log->url = $this->url;
        $log->file = $this->filename;
        $log->status = $this->statusCode;
        $log->proxy = $this->proxy;
        $log->useragent = $this->useragent;
        $log->save();
    }

    /**
     * Save page in file
     * @param $path
     * @return bool
     */
    public function save($filename){
        $handler = fopen($filename, 'w+');
        $fileSize = fwrite($handler, $this->html);
        fclose($handler);
        $this->file = $filename;
        return $fileSize > 0;
    }

    /**
     * Find DOM Element with Simple DOM Library
     * @param $query
     * @param null $index
     * @return array|\voku\helper\SimpleHtmlDomNode|\voku\helper\SimpleHtmlDomNode[]|null
     */
    public function find($query, $index = null){
        if(is_null($this->dom)){
            $this->dom = HtmlDomParser::str_get_html($this->html);
        }
        return $this->dom->find($query,$index);
    }


    /**
     * Get file
     * @return mixed
     */
    public function getFile(){
        return $this->filename;
    }

    /**
     * Set filename
     * @param $filename
     * @return mixed
     */
    public function setFile($filename){
        return $this->filename = $filename;
    }

    /**
     * Get Status Code
     * @return mixed
     */
    public function getStatusCode(){
        return $this->statusCode;
    }

    /**
     * Get Content
     * @return mixed
     */
    public function getContent(){
        return $this->html;
    }

    /**
     * Get URL
     * @return mixed
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     * Get User Agent
     * @return mixed
     */
    public function getUserAgent(){
        return $this->useragent;
    }

}