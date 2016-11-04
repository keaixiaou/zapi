<?php

define("ROOTPATH", dirname(__DIR__));

require ROOTPATH.'/vendor/autoload.php';
use ZPHP\ZPHP;

define('DEBUG', true);

ZPHP::run(ROOTPATH);