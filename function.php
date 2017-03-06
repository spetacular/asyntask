<?php
function task_get_nextrun($task){
	switch ($task['type']){
		case 'loop':
			$timeOptions = array(
				'day' => $task['day'],
				'hour' => $task['hour'],
				'minute' => $task['minute']
			);
			$lastrun = time();
			$nextrun = task_get_loop_nextrun($timeOptions,$lastrun);
			return array(
					'lastrun' => $lastrun,
					'nextrun' => $nextrun,
					'available' => 1,

				);

			break;
		case 'once':
		case 'time':
		case 'long':
		default:
			return array(
				'lastrun' => time(),
				'nextrun' => 0,
				'available' => 0,


			);
	}
}


function task_get_loop_nextrun($timeOptions,$lastrun = 0){
	$day = isset($timeOptions['day']) ? $timeOptions['day'] : 0;
	$hour = isset($timeOptions['hour']) ? $timeOptions['hour'] : 0;
	$minute = isset($timeOptions['minute']) ? $timeOptions['minute'] : 0;

	if($lastrun == 0){//首次执行时间
		if($day == 1){//每天执行：day 1 hour 0 minute 0 每天零点执行
			$nextrun = strtotime(date("Y-m-d $hour:$minute:0",time()));
		}else if($day == 0 && $hour == 1){//每小时执行：day 0 hour 1 minute 5 每小时的5分执行
			$nextrun = strtotime(date("Y-m-d H:$minute:0",time()));
		}else if($day == 0 && $hour == 0){
			$nextrun = time() + $minute * 60;
		}else{
			$nextrun = time();
		}
	}else{//下次执行时间
		if($day == 1){//每天执行：day 1 hour 0 minute 0 每天零点执行
			$nextrun = strtotime(date("Y-m-d $hour:$minute:0",time())) + 86400;
		}else if($day == 0 && $hour == 1){//每小时执行：day 0 hour 1 minute 5 每小时的5分执行
			$nextrun = strtotime(date("Y-m-d H:$minute:0",time())) + 3600;
		}else if($day == 0 && $hour == 0){
			$nextrun = time() + $minute * 60;
		}else{
			$nextrun = time();
		}
	}

	return $nextrun;
}

function encode_params($params){
	return json_encode($params);
}

function decode_params($params){
	return json_decode($params,true);
}