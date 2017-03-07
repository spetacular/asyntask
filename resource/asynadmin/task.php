<?php
$act = $_GET['act'];
if (!in_array($act, ['query', 'update'])) {
	echo json_encode(['msg' => 'Error']);
	exit;
}
$dbConfig = include('./config.php');
$dbString = sprintf('mysql:host=%s;dbname=%s;charset=%s', $dbConfig['DB_HOST'], $dbConfig['DB_NAME'], $dbConfig['DB_CHARSET']);
try {
	$db = new PDO($dbString, $dbConfig['DB_USER'], $dbConfig['DB_PWD']);
} catch (PDOException $ex) {
	exit('DB Config Is Not Correctly Configed! Message:' . $ex->getMessage());
}
$act($db);
function query($db) {
	$task_type = isset($_GET['task_type']) ? $_GET['task_type'] : '';
	$fields = array('page', 'rows', 'task_type', '_search', 'searchField', 'searchString', 'searchOper');
	foreach ($fields as $key) {
		$params[$key] = isset($_GET[$key]) ? $_GET[$key] : '';
	}

	$offset = ($params['page'] - 1) * $params['rows'];
	$limit = "{$offset} , {$params['rows']}";


	$order = 'id desc';


	$where_str = '';
	$where = [];
	if (isset($task_type) && in_array($task_type, array('once', 'loop', 'time', 'long'))) {

		$where_str .= "where `type` = '$task_type'";

	} else {
		$where_str .= 'where 1=1';
	}
	if ($params['_search'] == true) {
		if ($params['searchOper'] == 'eq') {
			$where_str .= " and `{$params['searchField']}`= '{$params['searchString']}'";
		}
		if ($params['searchOper'] == 'cn') {
			$where_str .= " and `{$params['searchField']}` like '%{$params['searchString']}%'";
		}

		// $where[$params['searchField']] = $params['searchString'];
	}
// var_dump($where_str);
	$stmt = $db->prepare("SELECT * FROM asyntask {$where_str} order by id desc limit {$limit}");
	$stmt->execute();

	$totalNum = $db->query("select count(*) from asyntask {$where_str}")->fetchColumn();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$totalPage = ceil($totalNum / $params['rows']);
	$data = array(
		'records' => $totalNum,
		'page' => $params['page'],
		'total' => $totalPage,
		'rows' => $result,
	);

	echo json_encode($data);
}


/**
 * 管理后台专用,用于修改crontab
 */
function update($db) {
	$fields = array('id', 'available', 'type', 'name', 'cmd', 'params', 'lastrun', 'nextrun', 'day', 'hour', 'minute');

	foreach ($fields as $key) {
		$params[$key] = isset($_POST[$key]) ? $_POST[$key] : '';
	}

	$params['nextrun'] = strtotime($params['nextrun']);
	$oper = $_POST['oper'];
	if ($oper == 'del') {//删除
		$stmt = $db->prepare("DELETE FROM asyntask WHERE id=:id");
		$stmt->bindValue(':id', $params['id'], PDO::PARAM_STR);
		$stmt->execute();
		$affected_rows = $stmt->rowCount();
		if ($affected_rows != 1) {
			return false;
		}
		return true;
	} else if ($oper == 'edit') {
		$stmt = $db->prepare("UPDATE asyntask SET available=:available,type=:type,name=:name,cmd=:cmd,params=:params,lastrun=:lastrun,nextrun=:nextrun,day=:day,hour=:hour,minute=:minute WHERE id={$params['id']}");
		unset($params['id']);
		$stmt->execute($params);
		$affected_rows = $stmt->rowCount();
		if ($affected_rows != 1) {
			return false;
		}
		return true;
	} else if ($oper == 'add') {
		unset($params['id']);
		try {
			$stmt = $db->prepare("INSERT INTO asyntask(available, type, name, cmd, params, lastrun, nextrun, day, hour, minute) VALUES(:available, :type, :name, :cmd, :params, :lastrun, :nextrun, :day, :hour, :minute)");
			$stmt->execute($params);
			return $stmt->rowCount() == 1;
		} catch (PDOException $ex) {
			return false;
		}
	}
	echo json_encode(['msg' => 'ok']);
}