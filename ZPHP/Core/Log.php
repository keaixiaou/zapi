<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/14
 * Time: 下午2:14
 */


namespace ZPHP\Core;

use ZPHP\ZPHP;

abstract class Log {
    const TRACE   = 0;
    const INFO    = 1;
    const NOTICE  = 2;
    const WARN    = 3;
    const ERROR   = 4;
    private static $log;

    protected static $level_str = array(
        'TRACE',
        'INFO',
        'NOTICE',
        'WARN',
        'ERROR',
    );


    //写日志
    static public function write($msg, $level=self::ERROR){
        $level_str = self::$level_str[$level];
        $message = date('Y-m-d H:i:s')." {$level_str}-".$msg."\n";
        self::$log[] = $message;
        if(count(self::$log)>=10){
            $str = implode("", self::$log);
            $file_path = ZPHP::getRootPath().'/log/app';
            if(!is_dir($file_path)){
                mkdir($file_path, 0755, true);
            }
            $file_name = $file_path.'/'.date('Y-m-d').'.log';

            error_log($str, 3, $file_name);
            self::$log = [];
        }

    }
}


