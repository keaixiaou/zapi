
# sphp-根据zphp改造专门用来做http服务的轻量级框架

##运行demo

	本框架只支持http模式：
	运行：
	cd webroot;
	php main.php start|stop|restart|reload
	访问IP:PORT


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
###mysql
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


