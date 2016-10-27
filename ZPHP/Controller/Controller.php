<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午5:16
 */

namespace ZPHP\Controller;

class Controller {
    /**
     * @var 请求参数
     */
    public $post;
    public $cookie;
    public $get;
    public $header;
    public $server;
    public $files;

    /**
     * @var $response
     */
    public $response;
    public $method;



    /**
     * api接口请求总入口
     *
    */
    public function coroutineApiStart(){
        $result = yield call_user_func([$this, $this->method]);
        $result = json_encode($result);
        $this->response->end($result);
        $this->destroy();
    }

    /**
     * 异常处理
     */
    public function onExceptionHandle(\Exception $e){
        $msg = DEBUG===true?$e->getMessage():'服务器升空了!';
        $this->response->end(json_encode(['code'=>500,'msg'=>$msg]));
        $this->destroy();
    }

    /**
     * 系统异常错误处理
     * @param $message
     */
    public function onSystemException($message){
        $message = DEBUG===true?'系统出现了异常:'.$message:'服务器稍微出现了点问题!';
        $this->response->end(json_encode(['code'=>500,'msg'=>$message]));
        $this->destroy();
    }

    protected function destroy(){
        unset($this->response);
    }
}