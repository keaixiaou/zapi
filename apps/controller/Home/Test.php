<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 2016/10/27
 * Time: 上午9:55
 */

namespace controller\Home;

use service\TestService;
use ZPHP\Controller\Controller;
use ZPHP\Core\App;
use ZPHP\Core\Factory;
use ZPHP\Core\Log;
use ZPHP\Coroutine\Http\HttpClientCoroutine;
use ZPHP\Core\Db;
use ZPHP\Redis\Redis;

class Test extends Controller{
//    public $isApi = true;
//这个和下面的init函数效果一样
    protected function init(){
        $this->isApi = true;
        return true;
    }

    public function index($abcd='abcd'){
        $data['list'] = yield App::service('Test')->test(1);
        $data['request'] = $this->input->request;
        return $data;
    }
    /**
     * service 封装方法
     */
    public function service(){
        //使用1-封装在service层,需要yield
        $testservice = new TestService();
        $vo = yield $testservice->test(1);
        $data['vo'] = $vo;
        return $data;
    }


    public function mysqlquery(){
        $data = yield Db::table('')->query("update `user` set nickname='admin1' where id =1 ; update `user` set nickname ='keaixiaou2' where id =2;");
        return $data;
    }

    /**
     * table 使用方法
     */
    public function mysql(){
        $user = yield table('user')->where(['id' => 1])->find();
        $res['user'] = $user;
        return $res;
    }

    /**
     * 异步http client使用方法
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
//        yield Db::redis()->cache('abcd1',1111);
        // 读缓存
        $data = yield Db::redis()->decr('abcd1');
        $res['cache'] = $data;
        return $res;
    }


    public function mongo(){
        $pipline = [
            [
                '$group' =>
                    [   '_id' => '$num',
                        'sum' => ['$sum' => 1],
                        'all' => ['$sum'=>'$num']
                    ],

            ],
            [
                '$sort' => [
                    'sum' => 1
                ]
            ]


        ];
//        $data = yield Db::collection('hello')->aggregate($pipline);
        $key = ['num'=>1];
        $initial = ['all'=>0,'no'=>0,'finish'=>0];

        $reduce = "function(obj, prev){prev.all=prev.all+obj.num}";
//         $group = yield Db::collection('hello')->where(['like'=>['lte',5]])->group($key, $initial, $reduce);
        // $aggregate = yield Db::collection('hello')->where(['])->setInc('num');
//        $finddata = yield Db::collection('test')->where(['likes'=>100])->find();
//        $getdata = yield Db::collection('test')->where(['likes'=>100])->get();
//        $count = yield Db::collection('hello')->where(['like'=>['elt',5]])->count();
        $data = yield Db::collection('log')->find();
        return json_encode($data);
//        Log::write('mongo end!');
        $this->assign('data', $data);
        $this->setTemplate('home');
        $this->display('index');
    }

    public function http(){
        $client = new HttpClientCoroutine();
        $url = 'http://www.baidu.com/';
        $postData = [];
        $data = yield $client->request($url, $postData);
        return $data;
    }

    public function memcacheset(){
        $data = yield Db::memcached()->cache('mystr', 'abcd');
        return $data;
    }
    public function memcachedelete(){
        $data = yield Db::memcached()->cache('mystr', null);
        return $data;
    }
    public function memcacheget(){
        $data = yield Db::memcached()->cache('mystr');
        return $data;
    }
}