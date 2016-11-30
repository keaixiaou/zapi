<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 2016/11/29
 * Time: 下午2:14
 */

namespace controllers\Home;

use ZPHP\Controller\Apicontroller;
use ZPHP\Core\Db;

class User extends Apicontroller{
    public function getDetail(){
        $user = yield Db::table('user')->where(['id'=>1])->find();
        return $user;
    }
}