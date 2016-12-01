<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 2016/12/1
 * Time: 下午1:46
 */



return [
    'service'=>[
        'Test' => service\Test::class,
    ],
    'model'=>[
        'Test' => model\Test::class,
    ],
    'controller' => [
        'index' => controllers\Home\Index::class,
        'user' => controllers\Home\User::class,
    ],

];