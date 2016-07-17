<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/16
 * Time: 下午2:49
 */

namespace ZPHP\Core;

class Swoole {
    public static $instance ;
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new Swoole;
        }
        return self::$instance;
    }


    /**
     * 输出一条错误信息，并结束程序的运行
     * @param $msg
     * @param $content
     * @return string
     */
    static function info($msg, $content='')
    {
        if (DEBUG !==true )
        {
            $content = '';
        }

//        if(self::$echo_html) {
            $info = <<<HTMLS
            <html>
            <head>
            <title>application error</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <style type="text/css">
            *{
                font-family:		Consolas, Courier New, Courier, monospace;
                font-size:			14px;
            }
            body {
                background-color:	#fff;
                margin:				40px;
                color:				#000;
            }

            #content  {
            border:				#999 1px solid;
            background-color:	#fff;
            padding:			20px 20px 12px 20px;
            line-height:160%;
            }

            h1 {
            font-weight:		normal;
            font-size:			14px;
            color:				#990000;
            margin: 			0 0 4px 0;
            }
            </style>
            </head>
            <body>
                <div id="content">
                    <h1>$msg</h1>
                    <p>$content</p><pre>
HTMLS;
//        }else {
//            $info = "$msg: $content\n";
//        }
        if (DEBUG!==true)
        {
            return $info;
        }

        $trace = debug_backtrace();
        $info .= str_repeat('-', 100) . "\n";
        foreach ($trace as $k => $t)
        {
            if (empty($t['line']))
            {
                $t['line'] = 0;
            }
            if (empty($t['class']))
            {
                $t['class'] = '';
            }
            if (empty($t['type']))
            {
                $t['type'] = '';
            }
            if (empty($t['file']))
            {
                $t['file'] = 'unknow';
            }
            $info .= "#$k line:{$t['line']} call:{$t['class']}{$t['type']}{$t['function']}\tfile:{$t['file']}\n";
        }
        $info .= str_repeat('-', 100) . "\n";
//        if (self::$echo_html)
//        {
            $info .= '</pre></div></body></html>';
//        }
        return $info;
    }
}