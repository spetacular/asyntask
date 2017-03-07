#!/bin/bash
start_asyn_task(){
	result=`ps aux | grep -i "cron_asyn_task.php" | grep -v "grep" | wc -l`
	if [ $result -ge 1 ]
	   then
			echo "script is running"
	   else
			/usr/bin/php cron_asyn_task.php &
	fi
}
start_asyn_task