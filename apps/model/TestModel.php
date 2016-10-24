<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 2016/10/24
 * Time: 下午4:33
 */

namespace model;


use ZPHP\Core\Db;

class TestModel{
    public function test($key){
        $data = yield Db::redis()->cache($key);
        return $data;
    }
}