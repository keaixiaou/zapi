
# zapi-用来做http接口服务的轻量级异步框架

### (请关注zapi的同学也关注zhttp，zhttp功能更强大，而且兼容zapi)
### 开发交流群:384013097
最新框架依赖环境docker镜像已经发布
(内置mongodb扩展、memcached扩展、swoole扩展1.9包括异步redis)

```
//拉取方法
docker pull keaixiaou/zhttp:1.0
```

## 使用手册（待完善）
https://www.gitbook.com/book/keaixiaou/zhttp

## 协议
MIT license

##	优势
	1.框架基于swoole开发，并且一些IO操作底层已经封装为异步，性能极其强悍。
	2.框架底层已经封装好异步，内置mysql、redis连接池，只需要在调用的时候在前面加yield，近乎同步的写法，却是异步的调用，并且无需关注底层实现，连接数超等问题，使用非常简单。
	
	
## 注意事项
	1.框架最新加入协程+mysql连接池，非阻塞的mysql查询大大提高了框架应对请求的吞吐量
	2.php版本需要7.0+
	3.swoole版本1.8.*
	4.如果用到异步redis，需要安装hiredis，安装教程:http://wiki.swoole.com/wiki/page/p-redis.html

## 安装依赖包
	composer install
	1.没有安装composer的先安装composer
	2.不会composer或者不喜欢composer的可以直接去我另一个资源库下载框架依赖,地址：https://github.com/keaixiaou/zphp
	
## 运行zapi
	本框架只支持http模式：
	运行：
	cd 到根目录
	php webroot/main.php start|stop|restart|reload|status
	访问IP:PORT

### 服务监控图

![服务监控](https://raw.githubusercontent.com/keaixiaou/base/master/status.jpeg)

### 本机裸跑输出

![本机裸跑输出](https://raw.githubusercontent.com/keaixiaou/base/master/mongo.jpeg)



## ab测试（配置：MacBook Air 8G内存，双核，I5）
### 本机裸跑输出

![本机裸跑输出](https://raw.githubusercontent.com/keaixiaou/base/master/%E8%A3%B8%E8%B7%91%E6%B5%8B%E8%AF%95.png)

### redis测试，2个work
![redis测试](https://raw.githubusercontent.com/keaixiaou/base/master/api%E6%B5%8B%E8%AF%95.png)

### mysql测试，4个work进程，每个work10个链接mysql连接池
![本机查询mysql](https://raw.githubusercontent.com/keaixiaou/base/master/mysql.png)






