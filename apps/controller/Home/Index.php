<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/15
 * Time: 下午3:58
 */

namespace controller\Home;

use ZPHP\Controller\Controller;
use ZPHP\Core\App;
use ZPHP\Core\Db;
use ZPHP\Core\Factory;
use ZPHP\Core\Log;

class Index extends Controller{
    public $isApi = true;

    /**
     * @method POST
     * @description  首页接口
     * @param int $a  用户编号 optional
     * @param int $b 用户名称
     * @return String $c 用户性别
     * @return Int $a 用户编号
     * @return Array $list 列表
     * @return _Int $c 昵称
     * @return _object $d 用户对象
     * @return __object $f 对象f
     * @return ___object $g 对象g
     * @return ____int $f 对象f
     * @return int $id 数据
     * @return Array $data 列表
     * @return _Int $c 昵称
     * @return _object $d 用户对象
     * @return __object $f 对象f
     * @return ___object $g 对象g
     * @return ____object $g 对象g
     * @return _____int $g 对象g
     * @return _____object $g 对象g
     * @return ______string $k 对象K
     */
    public function index(){
        return 'hello zpi!';
    }



}