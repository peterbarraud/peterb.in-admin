<?php
  //VERY IMPORTANT
  //these services will NEVER error out.
  //at the service we will stop any errors and send back a good json but packaged with error information
	require_once 'Slim/Slim.php';	
	require_once 'dataobjectserver/common/logger.php';
	use Slim\Slim;
	Slim::registerAutoloader();
	$app = new Slim();
	//a single rest API is self-sufficient - so how about the db connection is made at the API level
	//this connection object - held inside a global variable or something of that sort is then available to every method, object that is invokved from the API
	//this ensures that a single connection is opened for the entire duration of the API but no more
	//we can then also (brilliant, this one) make full use of db transactions - we can do a full commit / rollback of everything that happened for the duration of the API
	
	
	$app->get('/validateuser/:username/:password',function($username,$password) {
		require_once 'dataobjectserver/application.php';
		$application = Application::getinstance();
		$classAttrs = array();
		$classAttrs['password'] = $password;
		$classAttrs['username'] = $username;
		$user = $application->GetObjectsByClassNameAndAttributes('appuser',$classAttrs);
		$ret_val = array();
		if ($user->Length == 1) {
			$ret_val['success'] = 1;
			$ret_val['error'] = '';
		}
		else {
			$ret_val['success'] = 0;
			$ret_val['error'] = 'Invalid user name and / or password.';
		}
		echo json_encode($ret_val);
	});
	
	$app->get('/gettypelist/',function(){
		require_once 'dataobjectserver/application.php';
		$application = Application::getinstance();
		$blogtypes = $application->GetObjectsByClassName('blogtype');
		echo json_encode($blogtypes);
	});
	
	$app->get('/getcategorylist/',function () {
		require_once 'dataobjectserver/application.php';
		$application = Application::getinstance();
		$blogcategories = $application->GetObjectsByClassName('blogcategory');
		echo json_encode($blogcategories);
	});
	
	$app->get('/getpost/:postid',function($postid) {
		require_once 'dataobjectserver/application.php';
		$application = Application::getinstance();
		$blog = $application->GetObjectById('blog',$postid,1);
		echo json_encode($blog);
	});
	$app->post('/savepost',function() use ($app) {
		require_once 'dataobjectserver/application.php';		
		$application = Application::getinstance();
		//cast the json object to a well formed php object based on the data object model
		$blogObject = $application->GetObjectForJSON(json_decode($app->request->post('blogObject')),'blog');
		if (!$blogObject->id) {
			$blogObject->createdate = 'now()';
		}
		$blogObject->modifieddate = 'now()';
		$blogObject->Save();
		$ret_val['savedblogid'] = $blogObject->id;
		echo json_encode($ret_val);
	});
	
	
	
	$app->run();


function cors() {

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    //echo "You have CORS!";
}
?>
