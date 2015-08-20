<?php
  //VERY IMPORTANT
  //these services will NEVER error out.
  //at the service we will stop any errors and send back a good json but packaged with error information
	require_once 'Slim/Slim.php';
	use Slim\Slim;
	Slim::registerAutoloader();
	$app = new Slim();
	//a single rest API is self-sufficient - so how about the db connection is made at the API level
	//this connection object - held inside a global variable or something of that sort is then available to every method, object that is invokved from the API
	//this ensures that a single connection is opened for the entire duration of the API but no more
	//we can then also (brilliant, this one) make full use of db transactions - we can do a full commit / rollback of everything that happened for the duration of the API
	
	$app->get('/loginuser/:username/:password',function($username,$password) {
		$ret_val = array();
		$user = new User($username,$password);
		if ($user->IsValidUser) {
			$ret_val['success'] = 1;
			$ret_val['redirect'] = 'manageblog.php';

		}
		else {
			$ret_val['success'] = 0;
			$ret_val['redirect'] = null;

		}
		echo json_encode($ret_val);	
	});
	


	//TODO
	$app->get('/getblogtypelist/',function(){
		require_once 'common/dbconnection.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		require_once 'posttype.php';
		$retval = TypeFactory::GetTypes();
		MySQLConnection::Close($GLOBALS['mysqli']);
		echo json_encode($retval);
	});
	
	$app->get('/getcategorylist/',function () {
		require_once 'common/dbconnection.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		require_once 'category.php';
		$retval = CategoryFactory::GetCategories();
		MySQLConnection::Close($GLOBALS['mysqli']);
		echo json_encode($retval);
	});

	$app->get('/getpublishedcategorylist/',function () {
		require_once 'common/dbconnection.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		require_once 'category.php';
		$retval = CategoryFactory::GetPublishedCategories();
		MySQLConnection::Close($GLOBALS['mysqli']);
		cors();
		echo json_encode($retval);
	});

	$app->get('/getcategoryblogs/:categoryid',function ($categoryid) {
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		$retval = PostFactory::GetBlogsByCategory($categoryid);
		MySQLConnection::Close($GLOBALS['mysqli']);
		echo json_encode($retval);
	});

	$app->get('/getblogsbyblogstate/:state',function($state){
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		$retval = PostFactory::GetBlogsByState($state);
		MySQLConnection::Close($GLOBALS['mysqli']);
		echo json_encode($retval);
	
	});

	//POST APIs --start
	$app->get('/getpost/:postid',function($postid) {
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		if ($postid > 0) {  //get a new post object
  		$retval = PostFactory::GetPost($postid);
		}
		else {
		  $retval = PostFactory::NewPost();
	  }
		MySQLConnection::Close($GLOBALS['mysqli']);
  	echo json_encode($retval);
	});
	
	$app->get('/getallposts',function() {
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		$retval = PostFactory::GetAllPosts();
		MySQLConnection::Close($GLOBALS['mysqli']);
  	echo json_encode($retval);
	});

	$app->get('/deletepost/:postid',function ($postid){
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		$blogPost = PostFactory::GetPost($postid);
		$blogPost->Delete();
		$ret_val['deletedpostid'] = $blogPost->ID;
		$ret_val['success'] = 'Blog post deleted';
		MySQLConnection::Close($GLOBALS['mysqli']);
		echo json_encode($ret_val);	
	});
	
	$app->post('/savepost',function() use ($app) {
		require_once 'common/dbconnection.php';
		require_once 'common/common.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
	  $blogObject = castObject(json_decode($app->request->post('blogObject')),'Post');
	  $blogObject->Save();
	  $retval = array();
	  $retval['savedblogid'] = $blogObject->ID;
		MySQLConnection::Close($GLOBALS['mysqli']);
	  echo json_encode($retval);	
	});
	
	//is this required - let's leave it for now
	$app->get('/publishpost/:postid',function ($postid){
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		$categoriesToPublish = array();
		$blogPost = PostFactory::GetPost($postid);
		$blogPost->Publish();
		//we also have to re-publish the global
		(new PbGlobal())->Publish();
		//re-publish the categories to which this post belongs
		foreach ($blogPost->Categories as $category) {
			$category->Publish();
		}
		MySQLConnection::Close($GLOBALS['mysqli']);
		$ret_val['success'] = 1;
		$ret_val['message'] = "Post published successfully";
		echo json_encode($ret_val);
	});
	$app->post('/setreadyforpublish/',function() use ($app){
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		//first turn off auto-commit
		//$GLOBALS['mysqli']->autocommit(FALSE);
		$postid = $app->request->post('postid');
		$state = $app->request->post('state');
		$blogPost = PostFactory::GetPost($postid);
		$blogPost->SetReadyForPublish($state);
		//then manually commit
		//$GLOBALS['mysqli']->commit();
		//or rollback
		//$GLOBALS['mysqli']->rollback();
		$ret_val['postid'] = $blogPost->ID;
		$ret_val['success'] = 'Set ready for publish';
		MySQLConnection::Close($GLOBALS['mysqli']);
		echo json_encode($ret_val);	
	});
	//POST APIs --end



	$app->get('/publishall/:categoryid',function($categoryid){
		//for now everything all and all gets published
		//this is early days so we dont have enough to publish by category
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		
		//we're changed this around
		//first get the blog that we should be publishing right now - readyforpublish
		
		$blogsToPublish = array();
		if ($categoryid == -1) {
			$blogsToPublish = PostFactory::GetAllPublishableBlogs();
		}
		else {
			$blogs = null;
			if (is_numeric($categoryid)) {
				$blogs = PostFactory::GetBlogsByCategory($categoryid);
			}
			else {
				$blogs = PostFactory::GetBlogsByState($categoryid);
			}
			foreach ($blogs as $post) {
				if ($post->CanPublish && $post->ReadyForPublish) {
					array_push($blogsToPublish,$post);
				}
			}
		
		}
		$numberofblogspublished = 0;
		//go ahead only if at least there is at least one blog to publish - else NOTHING has changed
		if (sizeof($blogsToPublish) > 0) {
			//we're going to create an array of categories that we want to publish
			//these are categories of blogs that we are publishing right now
			$categoriesToPublish = array();
			foreach ($blogsToPublish as $blogToPublish) {
				foreach ($blogToPublish->Categories as $category) {
					$categoriesToPublish[$category->ID] = $category;
				}
				$numberofblogspublished += 1;
				$blogToPublish->Publish();			
			}
			//now let's publish the categories that we're changed (means blogs in them just got published)
			foreach ($categoriesToPublish as $categoryToPublish) {
				$categoryToPublish->Publish();
			}
			//and finally the main index.html
			(new PbGlobal())->Publish();
		}
		MySQLConnection::Close($GLOBALS['mysqli']);
		$ret_val['success'] = 1;
		$ret_val['numberofblogspublished'] = $numberofblogspublished;
		echo json_encode($ret_val);
	});
	
	$app->get('/unpublishpost/:postid',function(){
		$ret_val['success'] = 1;
		echo json_encode($ret_val);
		//TODO
	});
	
	$app->post('/addresponse/:postid',function($postid) use ($app){
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		//first turn off auto-commit
		//$GLOBALS['mysqli']->autocommit(FALSE);
		//get a post object
		$blogPost = PostFactory::GetPost($postid);
		//the get a new user respose object
		//this is to ensure that you cannot create a response without a blog post - sweet
		$userresponse = $blogPost->NewResponse();
		$userresponse->PostID = $postid;
		$userresponse->Response = $app->request->post('usercomments');
		if ($app->request->post('username')) {
			$userresponse->Name = $app->request->post('username');
		}
		else {
			$userresponse->Name = 'anonymous';
		}
		if ($app->request->post('email')) {
			$userresponse->Email = $app->request->post('email');
		}
		else {
			$userresponse->Email = 'anonymous@gave.noemail';
		}
		
		//and finally save
		$userresponse->Save();
		//then manually commit
		//$GLOBALS['mysqli']->commit();
		//or rollback
		//$GLOBALS['mysqli']->rollback();
		MySQLConnection::Close($GLOBALS['mysqli']);
		//$ret_val['newcommentid'] = $postid;
		$ret_val['success'] = 'Thanks for your comments. You should see up it pretty soon.';
		//allow cors
		cors();
		echo json_encode($ret_val);	
	
	
	});
	$app->get('/getuserresponses/:postid',function($postid){
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		$userResponses = ResponseFactory::GetIsOKResponsesByPost($postid);
		MySQLConnection::Close($GLOBALS['mysqli']);
		//allow cors
		cors();
		echo json_encode($userResponses);	
	});
	$app->get('/getrelatedposts/:postid',function($postid){
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		$relatedPosts = PostFactory::GetRelatedPosts($postid);
		MySQLConnection::Close($GLOBALS['mysqli']);
		//allow cors
		cors();
		echo json_encode($relatedPosts);	
	});

	$app->get('/getrecentposts/',function(){
		require_once 'common/dbconnection.php';
		require_once 'category.php';
		require_once 'post.php';
		require_once 'posttype.php';
		require_once 'userresponse.php';
		require_once 'userreply.php';
		$GLOBALS['mysqli'] = MySQLConnection::Open();
		$relatedPosts = PostFactory::GetRecentPosts();
		MySQLConnection::Close($GLOBALS['mysqli']);
		//allow cors
		cors();
		echo json_encode($relatedPosts);	
	});
	
	
	$app->run();

class User {
	public function __construct($username,$password) {
		$this->UserName = $username;
		$this->Password = $password;
		if ($username == 'gapeterb' && $password == 'danielb') {
			$this->IsValidUser = TRUE;
		}
	}
	//TODO
	public function Save()
	{
	}
	
	public $UserName = '';
	public $IsValidUser = FALSE;
	private $Password = '';

}

//utility function to log

//file_append is optional
//file name is optional - creates a file name depending on the caller
//you might want to create a function explicitly if, for example, you want to create multiple logs from the same function
function logg($whatyouwanttolog,$file_append=0,$filename=null)
{
	$trace = debug_backtrace();
	$log_file_name = $filename;
	if (!isset($filename)) {
		//if this is a class function then log file name is class-name.function-name, if this is not class then log file name is function-name
		$log_file_name = isset($trace[1]['class']) ? $trace[1]['class'] . '_' . $trace[1]['function'] : $trace[1]['function'];
		$log_file_name .= '.log';
	}
	file_put_contents($log_file_name,$whatyouwanttolog . "\n", $file_append);
}
//global log
function glogg($whatyouwanttologg)
{
	file_put_contents('pbblog.log',$whatyouwanttologg . "\n",FILE_APPEND);
}

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
