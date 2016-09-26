<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/21
 * Time: 下午6:54
 */


namespace ZPHP\Coroutine\Http;


use ZPHP\Coroutine\Base\ICoroutineBase;

class HttpClientCoroutine implements ICoroutineBase{
    /**
     * @var Client
     */
    public $client;
    public $url;
    public $postData;
    protected $result;

    public function __construct(){
        $this->client = new Client();
    }


    public function request($url, $postData=[]){
        $this->url = $url;
        $this->postData = $postData;
        yield $this;
    }


    /**
     * @param callable $callback
     */
    function send(callable $callback){
        $this->client->request($this->url, $this->postData, $callback);
    }



    function getResult(){
        return $this->result;
    }

}