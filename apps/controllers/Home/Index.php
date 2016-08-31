<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/15
 * Time: 下午3:58
 */

namespace controllers\Home;

use service\TestService;
use ZPHP\Cache\Factory;
use ZPHP\Cache\ICache;
use ZPHP\Controller\Apicontroller;
use ZPHP\Core\Config;
use ZPHP\Core\Log;
use ZPHP\Db\Db;
use ZPHP\Db\Mongo;
use ZPHP\Manager\Redis;

class Index {
    public function index(){
        echo 'index';
//        var_dump($config);
//        $redis = Factory::getInstance();
//        $redis->set('abcd','hello world');
//        var_dump($redis->set('abcd','1234',3600));
//        echo $redis->set('abcd',1111,3600);
        echo cache('abcd');
//        $data = Db::table()->query('select* from admin_user');
//        echo json_encode($data);
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
        $data = ['data'=>'abcd'];
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