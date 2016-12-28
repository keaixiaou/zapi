<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/15
 * Time: 下午3:58
 */

namespace controller\Home;

use ZPHP\Controller\Controller;
use ZPHP\Core\Db;
use ZPHP\Core\Log;

class Index extends Controller{
    protected function init(){
        $this->isApi = true;
        return true;
    }

    public function index(){
        return 'hello zpi!';
    }

    public function test($id=0){
        $id = !empty($id)?$id:$this->input->get('id');
        $data = yield Db::redis()->decr('abcd1');
//        $data = json_decode($data, true);
        return ['data'=>$data,'request'=>$this->input->request(),'id'=>$id];
    }

    public function user($id, $name=''){
        $user = yield Db::table('user')->where(['id'=>$id])->find();
        return ['user'=>$user,'id'=> $id,'name'=>$name];
    }

    public function main(){
        return 'hello zpi!';
    }

}