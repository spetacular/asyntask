<?php
namespace AsynTask\Mysql;

class TaskModel {
	private $db = NULL;
	private $table = 'asyntask';
	public function __construct(){
		if($this->db == NULL){
			$dbConfig = include('./config.php');
			$dbString = sprintf('mysql:host=%s;dbname=%s;charset=%s',$dbConfig['DB_HOST'],$dbConfig['DB_NAME'],$dbConfig['DB_CHARSET']);
			try{
				$this->db = new \PDO($dbString, $dbConfig['DB_USER'], $dbConfig['DB_PWD']);
			} catch(\PDOException $ex) {
				exit('DB Config Is Not Correctly Configed! Message:'.$ex->getMessage());
			}
		}
	}

	public function Insert($data){
		try {
			$stmt = $this->db->prepare("INSERT INTO {$this->table}(available, type, name, cmd, params, lastrun, nextrun, day, hour, minute) VALUES(:available, :type, :name, :cmd, :params, :lastrun, :nextrun, :day, :hour, :minute)");
			$stmt->execute($data);
			return $stmt->rowCount() == 1;
		} catch(\PDOException $ex) {
			return false;
		}

	}

	public function Save($id,$data){
		$stmt = $this->db->prepare("UPDATE {$this->table} SET ret=:ret WHERE id=$id");
		$stmt->execute($data);
		$affected_rows = $stmt->rowCount();
		if($affected_rows != 1){
			return false;
		}
		return true;
	}

	public function Get_Task(){
		try {
			$this->db->beginTransaction();

			$stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE available=1 AND nextrun<=? limit 1");

			$stmt->execute(array(time()));
			$task = $stmt->fetch(\PDO::FETCH_ASSOC);

			if(!$task){
				return false;
			}
			$update = task_get_nextrun($task);

			$stmt = $this->db->prepare("UPDATE {$this->table} SET lastrun=:lastrun, nextrun=:nextrun , available=:available WHERE id={$task['id']}");
			$stmt->execute($update);
			$affected_rows = $stmt->rowCount();
			if($affected_rows != 1){
				return false;
			}

			$this->db->commit();
			return array(
				'id'=> $task['id'],
				'type'=> $task['type'],
				'name' => $task['name'],
				'cmd'=> $task['cmd'],
				'params'=>decode_params($task['params'])
			);
		} catch(\PDOException $ex) {
			$this->db->rollBack();
			return false;
		}
	}
}