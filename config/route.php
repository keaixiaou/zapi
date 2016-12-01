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
            '/testindex' => function(){return 111;},
        ],
        'POST' => [

            '/test/{id}' => function($id){
                return $id;
            },
        ],
        'ANY' => [
            '/' => 'Index\main',
            '/user/{name}/no/{id}' => function($id, $name){
                return \ZPHP\Core\App::getController('index')->user($id, $name);
            },
            '/controller/{id}'=>function($id){
                return \ZPHP\Core\App::getController('index')->index($id);
            },

            '/testc/{id}' =>function($id){
                return \ZPHP\Core\App::getController('test')->index($id);
            }
        ],
    ],
];