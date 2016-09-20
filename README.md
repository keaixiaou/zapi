
# zhttp-根据zphp改造专门用来做http服务的轻量级框架
(当前只用来做app接口用)
## 注意事项
	1.框架最新加入协程+mysql连接池，非阻塞的mysql查询大大提高了框架应对请求的吞吐量
	2.php版本需要7.0+
	3.swoole版本1.8.*
	
##运行demo

	本框架只支持http模式：
	运行：
	cd webroot;
	php main.php start|stop|restart|reload
	访问IP:PORT
	建议：
		如果是静态文件，可以直接用nginx代理
		如果是动态请求，最好使用nginx做代理转发
		
##ab测试-本机裸跑输出
![本机裸跑输出](https://raw.githubusercontent.com/keaixiaou/pic/master/test2.jpg)

##ab测试-本机查询mysql，一个work进程，4个链接mysql连接池
![本机查询mysql](https://raw.githubusercontent.com/keaixiaou/base/master/swoole3.jpeg)

###目录结构
![目录结构](https://raw.githubusercontent.com/keaixiaou/pic/master/test1.jpg)


####apps -  mvc框架的controllers和service
####		^	service 通常的调用服务层
####config - 配置文件
####library - 对应的全局函数,每个work进程启动的时候会加载这个方法



###路由
根据pathinfo访问对应得controller，如ip:port/home/index/index则会访问home目录下的IndexController的index方法；如果不指定pathinfo则访问home目录下的IndexController的index方法

###Cache

```
return array(
    'cache'=>array(
        'adapter' => 'Redis',
        'pconnect' => true,
        'host' => '127.0.0.1',
        'port' => 6379,
        'timeout' => 5,
        'prefix' => 'zchat'
    )
);
```
只要在config目录下配置cache文件，即可在业务里调用缓存方法,如：

```
//读取缓存
echo cache('abcd');

//写入缓存
cache('abcd',1111,3600);
```

##数据库
###mysql(已经是异步非阻塞)
在config下配置mysql的配置文件，即可在业务中使用,你可以使用以下方法查询数据

```
 		$data = Db::table()->query('select* from admin_user');
        $a = DB::table()->query('select*from admin_user where id =1');
        $userinfo = table('admin_user')->where(['id'=>1])->find();
```


###mongo
在config下配置mongo的配置文件，即可在业务中使用，如下

```
$data = Db::collection('stu_quest_score')->findOne(['iStuId'=>26753]);


```


