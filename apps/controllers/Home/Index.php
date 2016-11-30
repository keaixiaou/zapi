<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/15
 * Time: 下午3:58
 */

namespace controllers\Home;

use ZPHP\Controller\Apicontroller;
use ZPHP\Core\Db;
use ZPHP\Core\Log;

class Index extends Apicontroller{
    public function index(){
        return 'hello zpi!';
    }

    public function test($id=0){
        $id = !empty($id)?$id:$_REQUEST['id'];
        $data = yield Db::redis()->cache('admin_user_'.$id);
        $data = json_decode($data, true);
        return ['data'=>$data,'request'=>$_REQUEST];
    }


    public function main(){
        return 'hello zpi!';
    }

}