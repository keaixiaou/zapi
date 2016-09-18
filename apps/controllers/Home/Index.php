<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/15
 * Time: 下午3:58
 */

namespace controllers\Home;

use service\TestService;
use socket\Controller;
use ZPHP\Cache\Factory;
use ZPHP\Cache\ICache;
use ZPHP\Controller\Apicontroller;
use ZPHP\Core\Config;
use ZPHP\Core\Log;
use ZPHP\Core\Db;
use ZPHP\Db\Mongo;
use ZPHP\Manager\Redis;
use ZPHP\Model\Model;

class Index extends Controller{
    public function index(){


        $sql= 'select * from admin_user where id=1';
        $data = Db::table()->query($sql);
        $res = (yield $data);
        $res['aaa'] = 111;
        return json_encode($res);
//        echo json_encode($res);
//        $result = json_encode($res);
//        Log::write('response:'.json_encode($this->response).';result:'.$result);
//        $this->response->end($result);


        //        var_dump($config);
//        $redis = Factory::getInstance();
//        $redis->set('abcd','hello world');
//        var_dump($redis->set('abcd','1234',3600));
//        echo $redis->set('abcd',1111,3600);
//        $data = Db::collection('stu_quest_score')->findOne(['iStuId'=>26753]);
//        echo json_encode($data);
//        echo cache('abcd');
//        $mysqlpool = Db::getInstance()->getMysqlPool();
//        $model = new Model($mysqlpool);

//            ->query('select * from admin_user where id =1');
//        $res = yield $data;
//        echo json_encode($res);
//        $data = Db::table()->query('select * from admin_user');
//       $data = $model->query('select * from admin_user');
//        $res = yield $data;
//            ->query('select* from admin_user');
//        $this->httpClient->end(json_encode($res));
//        echo json_encode($res);
//        $a = DB::table()->query('select*from admin_user where id =1');
//        $model  = table('admin_user')->where(['id'=>1])->find();
//        echo json_encode($model);
//        $model = table('admin_user')->where(['id']);
//        echo 'hello world!';
//        $config = array('server' => '192.168.5.6',
//         'port'   => '50000' ,
//         'options' => array('connect' => true) ,
//         'db_name'=> 'ChineseBasicTest' ,
//         'username'=> '' ,
//         'password'=> '',
//            'dsn'=>'mongodb://192.168.5.6:50000;dbname=ChineseBasicTest',
//        );
//        $mongo = new Mongo();
//        $mongo->connect($config);
//        $mongo->setDBName($config['db_name']);
//        $mongo->selectCollection('course');
//        $data = $mongo->findOne(['b'=>3]);
//        $swoole_mysql = new \Swoole\Coroutine\MySQL();
//        $swoole_mysql->connect(['host' => '120.27.143.217',
//            'user' => 'jeekzx',
//            'password' => '7f331f',
//            'database' => 'jeekzx',
//        ]);
//        $swoole_mysql->setDefer();
//        $res = $swoole_mysql->query('show tables');
//        echo json_encode($res);


//        unset($model);
    }


    public function abcd(){
        echo json_encode(['test'=>2]);
    }
}