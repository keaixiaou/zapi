
# zapi-根据zphp改造专门用来做http接口服务的轻量级异步非阻塞框架
(当前只用来做app接口用)

### 开发交流群:138897359

##	优势
	1.框架基于swoole开发，并且一些IO操作底层已经封装为异步，性能极其强悍。
	2.框架底层已经封装好异步，内置mysql、redis连接池，只需要在调用的时候在前面加yield，近乎同步的写法，却是异步的调用，并且无需关注底层实现，连接数超等问题，使用非常简单。
	
	
## 注意事项

	1.框架最新加入协程+mysql连接池，非阻塞的mysql查询大大提高了框架应对请求的吞吐量
	2.php版本需要7.0+
	3.swoole版本1.8.*
	4.如果用到异步redis，需要安装hiredis，安装教程:http://wiki.swoole.com/wiki/page/p-redis.html

##安装依赖包
	composer install
	1.没有安装composer的先安装composer
	2.不会composer或者不喜欢composer的可以直接去我另一个资源库下载框架依赖,地址：https://github.com/keaixiaou/zphp
	
##运行zapi

	本框架只支持http模式：
	运行：
	cd 到根目录
	php webroot/main.php start|stop|restart|reload
	访问IP:PORT

## 



###目录结构

![目录结构](https://raw.githubusercontent.com/keaixiaou/base/master/zapidir.jpeg)



##apps -  mvc框架的controllers和service

####			service 通常的调用服务层
####	config - 配置文件
####	library - 对应的全局函数,每个work进程启动的时候会加载这个方法

​		

## 路由

​	根据pathinfo访问对应得controller，如ip:port/home/index/index则会访问home目录下的IndexController的index方法；如果不指定pathinfo则访问home目录下的IndexController的index方法

##service
```
service层：
		$sql = 'select * from admin_user where id=1';
        $data['sql'] = $sql;
        $data['info'] = yield table('admin_user')->where(['id'=>1])->find();
        return $data;
controller层:
		//使用1-封装在service层,controller层也得写yield
        $testservice = new TestService();
        $data = yield $testservice->test();
        return $data;
```


## 

###Cache-redis(已经是异步非阻塞)

配置:

```
return [
    'redis'=>[
        'ip' => 'localhost',
        'port' => 6379,
        'select' => 0,
        'password' => '',
        'asyn_max_count' => 10,
    ]
];
```

使用:

```
$data = yield Db::redis()->cache('abcd'); //读取缓存

$res = yield Db::
```

只要在config目录下配置cache文件，即可在业务里调用缓存方法,如：

## 



##数据库



##mysql(已经是异步非阻塞)

在config下配置mysql的配置文件，即可在业务中使用,你可以使用以下方法查询数据

```
比如是一张test表，里面有字段:id，content
$data = yield Db::table()->query('select* from test');
query方法查询出来的结果:
{
    "client_id": 1,
    "result": [
        {
            "id": "1",
            "content": "222333"
        }
    ],
    "affected_rows": 0,
    "insert_id": 0
}

如果query执行失败则里面的result为false

$userinfo = yield table('test')->where(['id'=>1])->find();
find 方法查询出来的结果：
 {
    "id": "1",
    "content": "222333"
}

$userinfo = yield table('test')->where(['id'=>1])->get();
get 方法查询出来的结果:
[
    {
        "id": "1",
        "content": "222333"
    }
]

$insertId = yield Db::table('test')->add(['content'=>'333']);
add 方法得到的结果是：2（主键ID)


$res = yield Db::table('test')->save(['content'=>'333']);
save方法得到的结果是:0（修改的行数）

以上add,get,find,save 如果执行失败则返回false

```


###http client（已经是异步非阻塞）

```
$httpClient = new HttpClientCoroutine();
$data = yield $httpClient->request('http://speak.test.com/');//get请求
$data = yield $httpClient->request('http://speak.test.com/',['a'=>1]);//post请求
```

###框架全部封装好.怎么样，这异步用起来是不是很简单^_^


###mongo(还是同步阻塞的)
在config下配置mongo的配置文件，即可在业务中使用，如下

```
$data = Db::collection('stu_quest_score')->findOne(['iStuId'=>26753]);
```





##ab测试-本机裸跑输出

![本机裸跑输出](https://raw.githubusercontent.com/keaixiaou/base/master/%E8%A3%B8%E8%B7%91%E6%B5%8B%E8%AF%95.png)

##abredis测试，2个work
![redis测试](https://raw.githubusercontent.com/keaixiaou/base/master/api%E6%B5%8B%E8%AF%95.png)

##ab测试-本机(mac air)查询mysql，4个work进程，每个work10个链接mysql连接池
![本机查询mysql](https://raw.githubusercontent.com/keaixiaou/base/master/mysql.png)






