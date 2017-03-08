# PHP异步任务队列管理器asyntask
asyntask是一个轻量级异步任务队列管理器，支持实时，定时，长时和周期任务。

## 项目由来

本项目最初用于通知推送。例如用户发布评论，需要推送一条push给原作者。而到苹果的服务器的请求时间较长，如果等待苹果服务器的返回结果，则整个发布评论的接口的响应时间就太长了。因为推送push早1秒晚1秒对用户基本没影响，所以当用户发布评论时，只要数据到数据库，即可返回。与此同时创建一条异步任务，在1秒内给用户推送push。这样既保证了接口的响应速度，又不影响用户体验。该项目已经在线上环境运行1年多，执行了累计8千万条命令，运行稳定。

## 优点

* 异步执行
* 集成管理后台，可视化操作
* 代码集成，可编程

## 缺点

并非真正实时，秒级误差。

# 安装
## 下载源码

直接使用：

git clone https://github.com/spetacular/asyntask.git
命令下载到本地。

也可以点击 https://github.com/spetacular/asyntask/archive/master.zip 下载最新内容的压缩包，然后解压。
## 通过 composer 来安装

   在你的 composer 项目中的 composer.json 文件中，添加这部分：
```
   {
       "require": {
           "davidyan/asyntask": ">=1.0"
       }
   }
```

然后执行`composer install`。调用示例如下：
```
include './vendor/autoload.php';
$task = new AsynTask\Task();

//添加单次任务
$name = '单次任务';
$cmd = 'php abcd.php';
$params = array(
	'params'=>1
);
$task->add_once_task($name,$cmd,$params);
```

# 配置
1.asyntask的数据默认存储在Mysql数据库里，因此需要更改config.php里的配置：

```
	'DB_HOST'=>'127.0.0.1',
	'DB_NAME' => 'asyntask',
	'DB_USER' => 'root',
	'DB_PWD' => '',
	'DB_PORT' => '3306',
	'DB_CHARSET' => 'utf8mb4',
```

2.导入数据表
将resource文件夹里的db.sql导入数据库中。

3.配置健康检查脚本
run.sh定期检查异步任务的运行状况，如果挂了，cron_asyn_task.php脚本。
```
chmod +x run.sh
```
然后配置CronTab。运行`crontab -e`，然后添加一行：
```
* * * * *  path-to/run.sh  > /dev/null 2>&1
```
# 使用方式
##管理后台
自带管理后台，可以轻松添加、编辑、删除、搜索任务。代码在[https://github.com/spetacular/asynadmin](https://github.com/spetacular/asynadmin)，请自行部署。
[![管理后台截图](https://github.com/spetacular/asynadmin/raw/master/asynadmin.jpeg)](https://github.com/spetacular/asynadmin/raw/master/asynadmin.jpeg)
##编程方式
可以集成到项目中，完整使用示例建`test.php`。
例如添加周期任务：
```
$name = '周期任务';
$cmd = 'php abc.php';
$params = array(
	'params'=>1
);
$timeOptions = array(
	'day'=>1,
	'hour'=>2,
	'minute'=>3
);
$task->add_loop_task($name,$cmd,$params,$timeOptions);
```
## 周期任务示例

每天执行：day 1 hour 0 minute 0 每天零点执行

每小时执行：day 0 hour 1 minute 5 每小时的5分执行

每隔若干分钟执行：day 0 hour 0 minute 5 每隔5分钟执行

# asyntask
A lightweight asynchronous queue manager, supporting real-time, timing, long-term, periodic tasks.
