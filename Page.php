<?php

namespace izosa\page;

use voku\helper\HtmlDomParser;

/**
 * Page Parcer and Downloader
 * @author izosa@msn.com
 */
class Page {

    private $url;
    private $html;
    private $dom = null;
    private $handler;
    private $useragent;
    
    public function __construct($url, $init = true, $proxy = []){

        $this->url = $url;
        if($init) $this->download($proxy);
    }
    
    public function download($proxy = []){
        
        $this->useragent =\Campo\UserAgent::random([
            'os_type' => 'Windows',
            'device_type' => 'Desktop'
        ]);
        
        $this->handler = curl_init();
        curl_setopt($this->handler, CURLOPT_URL, $this->url);
        
        // proxy 
        if(!empty($proxy))
        {
            curl_setopt($this->handler, CURLOPT_HTTPPROXYTUNNEL, 0);
            curl_setopt($this->handler, CURLOPT_PROXY, $proxy['url']);
        }
        
        curl_setopt($this->handler, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST,'GET');
        curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->handler, CURLOPT_MAXREDIRS, 100);
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handler, CURLOPT_CONNECTTIMEOUT, 5);
        $this->html = curl_exec($this->handler);
        curl_close($this->handler);
    }

    public function save($path){
        $handler = fopen($path.DIRECTORY_SEPARATOR.str_replace("/", "|", $this->url), 'w+');
        $fileSize = fwrite($handler, $this->html);
        fclose($handler);
        return $fileSize > 0;
    }
    
    public function find($query, $index = null){
        if(is_null($this->dom)){
            $this->dom = HtmlDomParser::str_get_html($this->html);
        }
        $this->dom->find($query,$index);
    }
    
    public function getContent(){
        return $this->html;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getUserAgent(){
        return $this->useragent;
    }

}