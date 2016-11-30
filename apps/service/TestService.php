<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/17
 * Time: 下午1:48
 */

namespace service;

use model\TestModel;
use ZPHP\Core\App;
use ZPHP\Core\Db;

class TestService{


    public function test($key){
        $data = yield App::getModel('test')->test($key);
        return $data;

    }


    public function cache($key){
        $model = new TestModel();
        yield $model->test($key);
    }
}