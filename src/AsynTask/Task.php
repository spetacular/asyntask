<?php
namespace AsynTask;
require_once(__DIR__.'/function.php');
require_once(__DIR__.'/Mysql/DataModel.class.php');
class Task
{
	const TYPE_ONCE = "once";//单次执行
	const TYPE_TIME = "time";//定时执行
	const TYPE_LOOP = "loop";//循环执行
	const TYPE_LONG = "long";//长时间执行
	private $taskmodel = NULL;
	public function __construct(){
		$this->taskmodel = new \AsynTask\Mysql\DataModel();
	}

	public function add_once_task($name,$cmd,$params){
		$task = array(
			'available'=>1,
			'type'=>self::TYPE_ONCE,
			'name'=>$name,
			'cmd' => $cmd,
			'params'=> encode_params($params),
			'lastrun' => 0,
			'nextrun' => 0,
			'day' => 0,
			'hour' => 0,
			'minute' => 0
		);
		return $this->taskmodel->Insert($task);
	}

	public function add_time_task($name,$cmd,$params,$duetime){
		$task = array(
			'available'=>1,
			'type'=>self::TYPE_TIME,
			'name'=>$name,
			'cmd' => $cmd,
			'params'=> encode_params($params),
			'lastrun' => 0,
			'nextrun' =>$duetime,
			'day' => 0,
			'hour' => 0,
			'minute' => 0
		);
		return $this->taskmodel->Insert($task);
	}

	/**
	 * 增加循环任务。
	 * 每天执行：day 1 hour 0 minute 0 每天零点执行
	 * 每小时执行：day 0 hour 1 minute 5 每小时的5分执行
	 * 每隔若干分钟执行：day 0 hour 0 minute 5 每隔5分钟执行
	 * @param $name
	 * @param $cmd
	 * @param $params
	 * @param $timeOptions
	 * @return mixed
	 */
	public function add_loop_task($name,$cmd,$params,$timeOptions){
		$nextrun = task_get_loop_nextrun($timeOptions);

		$task = array(
			'available'=>1,
			'type'=>self::TYPE_LOOP,
			'name'=>$name,
			'cmd' => $cmd,
			'params'=> encode_params($params),
			'lastrun' => 0,
			'nextrun' => $nextrun
		);
		$task = array_merge($task,$timeOptions);
		return $this->taskmodel->Insert($task);
	}


	/**
	 * 增加长时间执行任务。长时间任务与其它任务的区别是：因为执行时间较长，为了不阻塞，需要使用重定向（> /dev/null 2>&1 &），程序立即返回。
	 * @param $name
	 * @param $cmd
	 * @param $params
	 * @param $duetime
	 * @return mixed
	 */
	public function add_long_task($name,$cmd,$params,$duetime = 0){
		$task = array(
			'available'=>1,
			'type'=> self::TYPE_LONG,
			'name'=>$name,
			'cmd' => $cmd,
			'params'=> encode_params($params),
			'lastrun' => 0,
			'nextrun' => $duetime,
			'day' => 0,
			'hour' => 0,
			'minute' => 0
		);
		return $this->taskmodel->Insert($task);
	}


	public function get_task(){
		$task = $this->taskmodel->Get_Task();
		if(!in_array($task['type'],array(self::TYPE_ONCE,self::TYPE_TIME,self::TYPE_LOOP,self::TYPE_LONG))){
			return false;
		}
		return $task;
	}


	public function save_message($id,$data){
		return $this->taskmodel->Save($id,$data);
	}
}