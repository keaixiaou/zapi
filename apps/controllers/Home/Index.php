<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/15
 * Time: 下午3:58
 */

namespace controllers\Home;

use service\TestService;
use ZPHP\Controller\Apicontroller;
use ZPHP\Core\Log;
use ZPHP\Db\Db;

class Index {
    public function index(){
//        $data = Db::table()->query('select* from admin_user');
//        echo json_encode($data);
//        $a = DB::table()->query('select*from admin_user where id =1');
//        $model  = table('admin_user')->where(['id'=>1])->find();
        echo 'hello world!';
//        echo json_encode($model);
//        unset($model);
    }
    public function abcd(){
        echo json_encode(['test'=>2]);
    }
}