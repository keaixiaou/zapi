<?php
namespace ZPHP\Coroutine\Http;
use ZPHP\Core\Log;

/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16-9-2
 * Time: 下午2:54
 */
class Client
{

    protected $swooleHttpClient;
    protected $data;


    protected function initHttpClient($url, $postData, $callback){
        $this->data['callback'] = $callback;
        $this->data['url'] = $url;
        $this->data['postdata'] = $postData;
    }


    public function request($url, $postData, $callback){
        $this->initHttpClient($url, $postData, $callback);
        $this->getHttpClient();
    }

    /**
     * 获取一个http客户端
     * @param $base_url
     * @param $callBack
     */
    public function getHttpClient()
    {

        $parseUrl = parse_url($this->data['url']);
        if(empty($parseUrl['host'])){
            throw new \Exception("输入地址有误");
        }
        $this->data['host'] = $parseUrl['host'];
        $this->data['ssl'] = $parseUrl['scheme']=='https'?true:false;
        if($this->data['ssl']==true){
            $this->data['port'] = 443;
        }else {
            $this->data['port'] = empty($parseUrl['port']) ? 80 : $parseUrl['port'];
        }
        $this->data['path'] = $parseUrl['path'];

        $data = $this->data;
        swoole_async_dns_lookup($this->data['host'], function ($host, $ip) use (&$data) {
            if(empty($ip)){
                throw new \Exception("找不到该域名");
            }
            $client = new \swoole_http_client($ip, $data['port'], $data['ssl']);
            $this->myCurl($client);
        });
    }


    /**
     * http 请求的过程
     */
    public function myCurl($swoolehttpclient){
        if(!empty($this->data['postdata'])) {
            $swoolehttpclient->post($this->data['path'], $this->data['postdata'],
                function ($swoolehttpclient) {
                    call_user_func_array($this->data['callback'], ['data'=>$swoolehttpclient->body]);
                });
        }else{
            $swoolehttpclient->get($this->data['path'], function ($swoolehttpclient) {
                call_user_func_array($this->data['callback'], ['data'=>$swoolehttpclient->body]);
            });
        }
    }

}