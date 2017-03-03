<?php
private function asyn_shell($get_task_func){
		set_time_limit(0);
		//取出任务
		$TaskModel = D('Hbtask');
		do{
			$data = call_user_func($get_task_func);
			var_dump($data);
			//如果没有要处理的任务，就等待些时间吧
			if(empty($data)){
				echo "无等待任务，休息1秒\n";
				sleep(1);
				continue;
			}

			//拼装命令
			$command = $data['cmd'];
			$cmdArgs = 'task_id/'.$data['id'];

			$cmd = "/usr/bin/php ".CRON_RUN_PATH." ".$command .'/'.$cmdArgs." > /dev/null 2>&1 &";

			//执行命令
			$output = '';
			$return_var = '';
			exec ( $cmd, $output , $return_var);

			//纪录log
			$output = implode("\n",$output);
			$file = '/tmp/task.log';
			$message = '['.date('Y-m-d H:i:s').']['.$cmd.']['.$return_var.']'.$output."\n";
			$TaskModel->save_task($data['id'],array('ret'=>$message));
			echo $message;
			file_put_contents($file, $message, FILE_APPEND);

			if($return_var != 0){
				exit;
			}
		}while(true);
	}