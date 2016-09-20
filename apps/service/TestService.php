<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/17
 * Time: 下午1:48
 */

namespace service;

class TestService{
    public function test(){
        return 'select * from admin_user where id=1';
    }
}