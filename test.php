<?php
require './asyntask.class.php';

$task = new AsynTask();

//添加单次任务
$name = '单次任务';
$cmd = 'php abc.php';
$params = array(
	'params'=>1
);
$task->add_once_task($name,$cmd,$params);

//添加定时任务
$name = '定时任务';
$cmd = 'php abc.php';
$params = array(
	'params'=>1
);
$duetime = strtotime('2017-03-15');
$task->add_time_task($name,$cmd,$params,$duetime);


//添加长时任务
$name = '长时任务';
$cmd = 'php abc.php';
$params = array(
	'params'=>1
);
$duetime = strtotime('2017-03-15');
$task->add_long_task($name,$cmd,$params,$duetime);



//添加循环任务
$name = '循环任务';
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

//获取下一条待执行的任务
$task->get_task();