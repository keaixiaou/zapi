<?php

define("ROOTPATH", dirname(__DIR__));
define("APPPATH", ROOTPATH.'/apps');

require ROOTPATH.'/vendor/autoload.php';
use ZPHP\ZPHP;

define('DEBUG', true);

ZPHP::run(ROOTPATH);