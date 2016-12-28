<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 2016/11/28
 * Time: 下午1:55
 */


return [
    'route'=>[
        'GET' => [
            '/testmodel/{key}' => function($key){
                return \ZPHP\Core\App::model('test')->test($key);
            },
            '/testindex' => function(){return 111;},
        ],
        'POST' => [

            '/testinfo/{id}' => function($id){
                return $id;
            },
        ],
        'ANY' => [
            '/' => 'Index\main',
            '/user/{name}/no/{id}' => function($id, $name){
                $data = yield \ZPHP\Core\App::model('test')->getUserDetail($id, $name);
                return ['data'=>$data];
            },

            '/user/{id}' => function($id){
                return \ZPHP\Core\App::model('test')->getUserById($id);
            }
        ],
    ],
];