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
    public function test($id){
        $sql = 'select 1';
        $data['sql'] = $sql;
        $data['info'] = yield table('')->query($sql);
        return $data;
    }


    public function cache($key){
        $model = new TestModel();
        yield $model->test($key);
    }
}