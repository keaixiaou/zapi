<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/15
 * Time: 上午10:48
 */
$http = new \swoole_http_server("127.0.0.1", 9501);
$http->on('request', function ($request, $response) {
    $response->end(json_encode(['a'=>1]));
});
$http->start();