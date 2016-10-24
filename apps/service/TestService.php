<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/17
 * Time: 下午1:48
 */

namespace service;

use model\TestModel;
use ZPHP\Core\Db;

class TestService{
    public function test(){
        $sql = 'select * from admin_user where id=1';
        $data['sql'] = $sql;
        $data['info'] = yield table('admin_user')->where(['id'=>1])->find();
        return $data;
    }

    public function test2(){
        $sql = 'select * from admin_user where id=2';
        $data['sql'] = $sql;
        $data['info'] = yield table('admin_user')->where(['id'=>1])->find();
        return $data;
    }


    public function cache($key){
        $model = new TestModel();
        yield $model->test($key);
    }
}