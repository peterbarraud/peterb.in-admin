<?php

	require_once 'Slim/Slim.php';
	use Slim\Slim;
	Slim::registerAutoloader();
	$app = new Slim();

	$app->get('/selectalltable/:tablename','selectalltable');
	$app->get('/getTableList','getTableList');
	$app->post('/executequery','executequery');

	$app->run();
	
function selectalltable($tablename)
{
	require_once 'common/common.php';
	echo getsimplequeryjson('SELECT * from ' . $tablename . ';');
}

function executequery()
{
	require_once 'common/dbconnection.php';
	require_once 'common/common.php';
	$app = Slim::getInstance();
	$ret_val = array();
	$mysqli = MySQLConnection::Open();
	//before finding out how many queries to execute, let's clean out the post
	//first let's trim out the string
	$postquery = trim($app->request->post('query'));
	//next we'll remove any trailing semi-colon (;) - this causes the explode to think there's one extra blank item at the end of the array
	//in fact the trim function removes trailing and prefixing commas
	$postquery = trim($postquery, ";");	
	$queries = explode (';',$postquery);
	//now let's clean up the
	foreach ($queries as $query) {
		$queryresult = new QueryResult($query,$mysqli);
		array_push($ret_val,$queryresult);
	}
	MySQLConnection::Close($mysqli);
	echo json_encode($ret_val);
}

function getTableList()
{
	require_once 'common/dbconnection.php';
	require_once 'common/common.php';
	$ret_val = array();
	$results = array();
	$mysqli=MySQLConnection::Open();
	if($result=$mysqli->query('show tables')){
		while ($row = $result->fetch_array()){
			array_push($ret_val,$row[0]);
		}
	}
	MySQLConnection::Close($mysqli);
	echo json_encode($ret_val);
}


class QueryResult {
	public function __construct($query,$mysqli) {
		if (!empty($query)) {
			//an amazing hack to handle the ../ error that seems to be coming from the .htaccess
			$this->Query = str_replace('dotdotfrontslash','../',$query);
			if($result = $mysqli->query($this->Query)){
				$this->Success = 1;
				if ($result === TRUE) {
					//fire a blank query
					$result = $mysqli->query("SELECT 'No results' AS Result");
				}
				$this->Result = make_json_array($result);
			}
			else {
				$this->Success = 0;
				$this->Error = $mysqli->error;
			}
		}
		//we will not respond to empty queries
	}
	//public properties
	public $Success = 0;
	public $Error = '';
	public $Result = null;
	public $Query = '';
}


?>