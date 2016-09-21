<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/5
 * Time: 下午2:14
 */

$res = SeasLog::setBasePath('/tmp/seaslog');
var_dump($res);
echo SeasLog::getBasePath();
Seaslog::setLogger('app');
//die;
//$res = SeasLog::log(SEASLOG_ERROR,'this is a first seaslog');
$log = Seaslog::analyzerDetail(SEASLOG_ERROR);
var_dump($log);
