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
        'HomeIndex' => controller\Home\Index::class,
        'user' => controller\Home\User::class,
    ],

];