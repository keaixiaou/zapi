<?php
use ZPHP\ZPHP;

define("ROOTPATH", dirname(__DIR__));
define('DEBUG',false);
require  ROOTPATH.'/ZPHP/ZPHP.php';

ZPHP::run(ROOTPATH);
//$server_pid = file_get_contents('/tmp/zphp_master.pid');
//$res = posix_kill($server_pid, SIGTERM);
//var_dump($res);