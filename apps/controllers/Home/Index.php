<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/15
 * Time: 下午3:58
 */

namespace controllers\Home;

use ZPHP\Controller\Apicontroller;

class Index extends Apicontroller{
    public function index(){
        return ['data'=>'hello zapi'];
    }
}