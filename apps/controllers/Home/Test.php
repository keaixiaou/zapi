<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 2016/10/27
 * Time: 上午9:55
 */

namespace controllers\Home;

use service\TestService;
use ZPHP\Controller\Controller;
use ZPHP\Coroutine\Http\HttpClientCoroutine;
use ZPHP\Core\Db;

class Test extends Controller{
    /**
     * service 封装方法
     * @return mixed
     */
    public function service(){
        //使用1-封装在service层,需要yield
        $testservice = new TestService();
        $vo = yield $testservice->test(1);
        $data['vo'] = $vo;
        return $data;
    }


    /**
     * table 使用方法
     * @return mixed
     */
    public function table(){
        $user = yield table('admin_user')->where(['id' => 2])->find();
        $res['user'] = $user;
        return $res;
    }

    /**
     * 异步http client使用方法
     * @return array
     */
    public function httpClient(){
        $httpClient = new HttpClientCoroutine();
        $data = yield $httpClient->request('http://speak.test.com/');
        return ['html'=>$data];
    }

    /**
     * cache的写法
     */
    public function cache(){
        //使用2 - 写缓存
        yield Db::redis()->cache('abcd1',1111);
        // 读缓存
        $data = yield Db::redis()->cache('abcd1');
        $res['cache'] = $data;
    }


    public function abcd(){
        return ['test'=>2];
    }
}