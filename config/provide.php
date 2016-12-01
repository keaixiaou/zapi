<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 2016/12/1
 * Time: 下午1:46
 */



return [
    'service'=>[
        'test' => service\TestService::class,
    ],
    'model'=>[
        'test' => model\TestModel::class,
    ],
    'controller' => [
        'index' => controllers\Home\Index::class,
        'user' => controllers\Home\User::class,
    ],

];