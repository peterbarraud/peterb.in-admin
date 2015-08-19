<?php
//create Post objects ONLY via the factory
class PostFactory {
	public static function GetPost($postid)
	{
		$retval = null;
		$select_sql = "select id from pbpost where id = '$postid';";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			$row = $result->fetch_assoc();
			$retval = new Post($row['id']);
		}
		return $retval;
		
		
	}
	public static function NewPost()
	{
		$retval = new Post();
		return $retval;
	}
	//returns Posts object
	public static function GetAllPosts()
	{
		$retval = array();
		$select_sql = "SELECT post.id FROM pbpost post order by post.modifieddate desc";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($retval,new Post($row['id']));
			}
		}
		return $retval;
	}
	
	public static function GetRelatedPosts($postid)
	{
		$retval = array();
		//only get posts that have a blog (text) because now we're allowing people to publish only title and subtitle
		//a sort of a tweet things - we want these to show up in the index page but not as related posts
		$select_sql = "SELECT post.id FROM pbpost post where id <> $postid and blog is not null order by post.modifieddate desc";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				$post = new Post($row['id']);
				if ($post->CanPublish) {
					array_push($retval,$post);
				}
			}
		}
		else {
			glogg($GLOBALS['mysqli']->error);
			glogg($select_sql);
		}
		return $retval;
	}
	public static function GetBlogsByCategory($categoryid)
	{
		$retval = array();
		$select_sql = "SELECT post.id FROM pbpost post join pbpostcategory postcat on post.id = postcat.postid where postcat.categoryid = $categoryid order by post.modifieddate desc";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($retval,new Post($row['id']));
			}
		}
		else {
			glogg('something happened:' . $GLOBALS['mysqli']->error);
			glogg($insert_sql);
		}
		return $retval;
	}
	public static function GetCanPublishBlogsByCategory($categoryid)
	{
		$categoryBlogs = PostFactory::GetBlogsByCategory($categoryid);
		$retval = array();
		foreach ($categoryBlogs as $blog) {
			if ($blog->CanPublish) {
				array_push($retval,$blog);
			}
		}
		return $retval;
	}

	public static function GetAllCanPublishBlogs()
	{
		$blogPosts = PostFactory::GetAllPosts();
		$retval = array();
		foreach ($blogPosts as $blog) {
			if ($blog->CanPublish && $blog->ReadyForPublish) {
				array_push($retval,$blog);
			}
		}
		return $retval;
	}
	
	
	public static function GetCategoryBlogsByState($categoryid,$state)
	{
		$retval = array();
		//get posts by category
		$posts = PostFactory::GetBlogsByCategory($categoryid);
		//then filter
		foreach ($posts as $post) {
			if ($state == 'notpublished') {
				array_push($retval,$post);
			}
			else if ($state == 'unpublished') {
				array_push($retval,$post);
			}
			else if ($state == 'outdated') {
				array_push($retval,$post);
			}
			else if ($state == 'canpublish') {
				//it is ready to publish if
				array_push($retval,$post);
			}
		}
		return $retval;
	}
	
	public static function GetBlogsByState($state)		//state should be bit flags
	{
		$retval = array();
		//get all posts
		$posts = PostFactory::GetAllPosts();
		//then filter
		//unpublished
		foreach ($posts as $post) {
			if ($state == 'unpublished') {
				if (isset($post->UnpublishDate) && $post->UnpublishDate > $post->PublishDate) {
					array_push($retval,$post);
				}
			}
		//notpublished
			else if ($state == 'notpublished') {
				if (isset($post->PublishDate) == FALSE) {
					array_push($retval,$post);
				}
			}
		//outdated
			else if ($state == 'outdated') {
				if (isset($post->PublishDate) && $post->ModifiedDate > $post->PublishDate) {
					array_push($retval,$post);
				}
			}
		}
		return $retval;
	}
	//this is different from can publish blogs
	//these are blogs that are already published and need to be added to the index pages
	public static function GetPublishedBlogs()
	{
		$retval = array();
		$select_sql = "select id from pbpost where (publishdate is not null and publishdate >= modifieddate) or (unpublishdate is not null and publishdate > unpublishdate) order by modifieddate desc";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($retval,new Post($row['id']));
			}
		}
		return $retval;
		
	}
	
	public static function GetPublishedBlogsByCategory($categoryid)
	{
		$retval = array();
		$select_sql = "SELECT post.id FROM pbpost post join pbpostcategory postcat on post.id = postcat.postid where postcat.categoryid = $categoryid and (post.publishdate is not null and post.publishdate >= post.modifieddate) or (post.unpublishdate is not null and post.publishdate > post.unpublishdate) order by post.modifieddate desc";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($retval,new Post($row['id']));
			}
		}
		return $retval;
		
	}
}

class Post {
	//row is null if we are creating a new Post
	//let's keep the mysqli object mandatory for post create tasks list save and publish
	//since a post is made up of some data (wrt lists) let's give the caller the option not to get this info
	//but this is a dangerous issue - if a caller chooses not 
	public function __construct($id = null,$getlists=TRUE) {
		//if row is null then get an empty post object
		if ($id != null) {
			$select_sql = "SELECT * from pbpost where id = $id";
			if ($result = $GLOBALS['mysqli']->query($select_sql)) {
				$row = $result->fetch_assoc();
				$this->ID = $row['id'];
				$this->Title = $row['title'];
				$this->SubTitle = $row['subtitle'];
				$this->PageName = $row['pagename'];
				$this->Blog = $row['blog'];
				$this->ReadyForPublish = isset($row['readyforpublish']) ? TRUE : FALSE;
				$this->CreateDate = $row['createdate'];
				$this->ModifiedDate = $row['modifieddate'];
				$this->PublishDate = $row['publishdate'];
				$this->UnpublishDate = $row['unpublishdate'];
				$this->Categories = CategoryFactory::GetCategoriesByPost($this->ID);
				$this->Types = TypeFactory::GetTypesByPost($this->ID);
				
				if ((!empty($this->Title) || !isset($this->Title)) && (!empty($this->Blog) || !isset($this->Blog))) {
					$this->CanDelete = TRUE;
				}
				//we're going to republish all every single time
				//but why
				//the related post column for previously published will then have refs to newer links
				//but first check if readyforpublish is true
				//first check that a blog has a title and subtitle and or post
				if (!empty($this->Title) && (!empty($this->SubTitle) || !empty($this->Blog))) {
					//we're not going to use the ready flag to check if it can be published
					//the ready flag is only used in a batch publish - IMPORTANT
					$this->CanPublish = TRUE;
				}
				
				//can delete if it is not published
				//or if the unpublish date is greater than publish date
				if (!isset($this->PublishDate) || $this->UnpublishDate > $this->PublishDate || sizeof($this->Categories) == 0) {
					$this->CanDelete = TRUE;
				}
				
				//can unpublish if its been published - that's all nothing about being outdated
				if (isset($this->PublishDate)) {
					$this->CanUnpublish = TRUE;
				}
			}
			else {
				//TODO: error in sql execute
			}
		}
		else {
			$this->Categories = array();	//TODO: I dont think this is required
		}
		require_once 'pbglobal.php';
		$globalData = GlobalFactory::GetGlobalData();
		$this->TemplateFolder = $globalData->TemplateFolder;
		$this->TemplateName = $globalData->PostTemplateName;
		$this->PostFolder = $globalData->PostFolder;
		$this->PostUrl = $globalData->PostUrl;
			
	}
	public function Save()
	{
		if ($this->ID < 1) {	//add new
			$insert_sql = function() {
				$col_list = '';
				$val_list = '';
				if (!empty($this->Title)) {
					$col_list .= 'title,';
					$val_list .= "'$this->Title',";
				}
				if (!empty($this->SubTitle)) {
					$col_list .= 'subtitle,';
					$val_list .= "'$this->SubTitle',";
				}
				if (!empty($this->Blog)) {
					$col_list .= 'blog,';
					$val_list .= "'$this->Blog',";
				}
				if (!empty($this->PageName)) {
					$col_list .= 'pagename,';
					$val_list .= "'$this->PageName',";
				}
				if ($this->ReadyForPublish) {
					//but also let's make sure that the title and the subtitle or the blog are there
					if (isset($this->Title) && (isset($this->SubTitle) || isset($this->Blog))) {
						$col_list .= 'readyforpublish,';
						$val_list .= "'',";
					}
				}
				$col_list .= 'createdate,modifieddate';
				$val_list .= 'now(),now()';
				$retval = 'insert into pbpost (' . $col_list . ') values (' . $val_list . ');';
				return $retval;
			};
			if ($GLOBALS['mysqli']->query($insert_sql())) {
				$this->ID = $GLOBALS['mysqli']->insert_id;
				Post::assignpostcategories($this);
				Post::assignposttypes($this);
			}
			else {
				glogg('something happened:' . $GLOBALS['mysqli']->error);
				glogg($insert_sql);
			}
		}
		else {	//update
			//beautiful anonymous function usage
			$update_sql = function() {
				$retval = 'update pbpost set ';
				$retval .= empty($this->Title) ? "title = null, " : "title = '$this->Title', ";
				$retval .= empty($this->SubTitle) ? "subtitle = null, " : "subtitle = '$this->SubTitle', ";
				$retval .= empty($this->Blog) ? "blog = null, " : "blog = '$this->Blog', ";
				$retval .= empty($this->PageName) ? "pagename = null, " : "subtitle = '$this->PageName', ";
				$retval .= empty($this->SubTitle) ? "subtitle = null, " : "subtitle = '$this->SubTitle', ";
				if (empty($this->PublishDate)) {
					$retval .= "publishdate = null, ";
				}
				else {
					if ($this->PublishDate == 'now()') {
						$retval .= "publishdate = now(), ";
					}
					else {
						$retval .= "publishdate = '$this->PublishDate', ";
					}
				}
				if (empty($this->UnpublishDate)) {
					$retval .= "unpublishdate = null, ";
				}
				else {
					if ($this->UnpublishDate == 'now()') {
						$retval .= "unpublishdate = now(), ";
					}
					else {
						$retval .= "unpublishdate = '$this->PublishDate', ";
					}
				}
				$retval .= $this->ReadyForPublish == TRUE ? "readyforpublish = '', " : "readyforpublish = null, ";

				$retval .= "modifieddate = now() where id = $this->ID";
				return $retval;
			};
			if ($GLOBALS['mysqli']->query($update_sql())) {
				Post::assignpostcategories($this);
				Post::assignposttypes($this);
			}
			else {
				glogg('something happened--:' . $GLOBALS['mysqli']->error);
				glogg($update_sql());
			}
		}
	}
	
	public static function assignposttypes($blog)
	{
		if ($blog->Types) {
			$delete_sql = "DELETE FROM pbposttype WHERE postid = $blog->ID;";
			if ($GLOBALS['mysqli']->query($delete_sql)) {
				foreach ($blog->Types as $type) {
					$insert_sql = "INSERT INTO pbposttype (postid,typeid) values ($blog->ID,$type->ID);";
					if ($GLOBALS['mysqli']->query($insert_sql) == FALSE) {
						glogg('something happened:' . $GLOBALS['mysqli']->error);
						glogg($insert_sql);
					}
				}					
			}
			else {
				glogg('something happened:' . $GLOBALS['mysqli']->error);
				glogg($insert_sql);
			}		
		}
	}
	public static function assignpostcategories($blog)
	{
	
		if ($blog->Categories) {
			$delete_sql = "DELETE FROM pbpostcategory WHERE postid = $blog->ID;";
			if ($GLOBALS['mysqli']->query($delete_sql)) {
				foreach ($blog->Categories as $category) {
					$insert_sql = "INSERT INTO pbpostcategory (postid,categoryid) values ($blog->ID,$category->ID);";
					if ($GLOBALS['mysqli']->query($insert_sql) == FALSE) {
						glogg('something happened:' . $GLOBALS['mysqli']->error);
						glogg($insert_sql);
					}
				}					
			}
			else {
				glogg('something happened:' . $GLOBALS['mysqli']->error);
				glogg($insert_sql);
			}
		}
	}
	
	
	
	public function AddCategory($categoryid)
	{
		array_push($this->Categories,CategoryFactory::GetCategory($categoryid));
	}
	public function RemoveCategory($categoryid)
	{
		$temparray = array();
		foreach ($this->Categories as $category) {
			if ($category->ID != $categoryid) {
				array_push($temparray,$category);
			}
		}
		unset ($this->Categories);
		$this->Categories = $temparray;
		unset ($temparray);
	}

	public function AddType($typeid)
	{
		array_push($this->Types,TypeFactory::GetType($typeid));
	}
	public function RemoveType($typeid)
	{
		$temparray = array();
		foreach ($this->Types as $posttype) {
			if ($posttype->ID != $typeid) {
				array_push($temparray,$posttype);
			}
		}
		unset ($this->Types);
		$this->Types = $temparray;
		unset ($temparray);
	}

	
	public function NewResponse()
	{
		//this will be a direct method where the comment will be added to the object Comments array but will be saved directly.
		//this is because this is nothing to do with creating / editing a blog, per se
		//we will also need a curated field in the pbpostcomments table
		//post - comment is a one-to-many relationship so we probably don't need a table in the middle
		//pbComments:
		//id
		//postid - foreign key to pbpost
		//comment (text)
		//curated = TRUE / FALSE
		return new UserResponse($this->ID);
	}
	
	//TODO
	public function Delete()
	{
		//how about using cascading delete in mySQL???
		$delete_sql = 'delete FROM `pbpostcategory` WHERE postid = ' . $this->ID;
		$GLOBALS['mysqli']->query($delete_sql);
		$delete_sql = 'delete FROM `pbposttype` WHERE postid = ' . $this->ID;
		$GLOBALS['mysqli']->query($delete_sql);
		$delete_sql = 'delete from pbpost where id = ' . $this->ID;
		$GLOBALS['mysqli']->query($delete_sql);
	}
	
	public function Publish()
	{
		$file_contents_string = file_get_contents($this->TemplateFolder . $this->TemplateName);
		$file_contents_string = str_replace("<blog-post-title>",$this->Title,$file_contents_string);
		$file_contents_string = str_replace("<blog-post-post>",$this->Blog,$file_contents_string);
		//create the category folder if it doesn't exist
		//so this check is always required - probably not very expensive tho
		//later when we have a manage category UI, where we can create categories, we probably won't need this

		if (!isset($this->SubTitle)) {
			$this->SubTitle = substr(strip_tags($this->Blog),0,50);
		}
		Post::setpagename($this);
		$category_dropdown = Category::getcategorydropdown();
		$file_contents_string = str_replace('<category-dropdown>',$category_dropdown,$file_contents_string);
		$file_contents_string = str_replace('<post_id>',$this->ID,$file_contents_string);
		file_put_contents($this->PostFolder . $this->PageName,$file_contents_string);
		//finally set the publish date back to now
		$this->PublishDate = 'now()';
		$this->ReadyForPublish = FALSE;
		$this->Save();
	}
	
	public static function setpagename($blogPost)
	{
		if (!isset($blogPost->PageName)) {
			$blogPost->PageName = preg_replace('/[\s\`\~\!\@\#\$\%\^\&\*\(\)\+\=\{\[\}\]\:\;\'\"\,\<\.\>\/\?]/','_',$blogPost->Title);
		}
		
		if (substr($blogPost->PageName,strlen($blogPost->PageName)-5,strlen($blogPost->PageName)) != '.html') {
			$blogPost->PageName .= '.html';			
		}
	}
	
	
	public static function blog_snippet($blogpost)
	{
		$ret_val = '<div class=col-lg-4>';
		$ret_val .= '<h3>' . $blogpost->Title . '</h3>';
		$ret_val .= '<div>' . $blogpost->SubTitle . '</div>';
		
		if (isset($blogpost->Blog)) {
			Post::setpagename($blogpost);
			$ret_val .= '<p><a class="btn btn-default" href=' . $blogpost->PostUrl . $blogpost->PageName . ' role=button>Read more</a></p>';
		}
		$ret_val .= '</div>';
		return $ret_val;
	}
	
	function __toString() {
		$retval = '';
		$object_vars = get_object_vars ($this);
		while ( list ($key, $value) = each ($object_vars) ) {	//nice hash iterator code
			$print_val = $value;
			if (isset($print_val)) {
				$print_val = is_array ($value) ? sizeof($value) : $value;
			
			}
			else {
				$print_val = 'NULL';
			}
			$retval .= $key . '>> ' . $print_val . "\n";
		}
		return $retval;
	}

	//public properties
	public $ID = 0;
	public $Title = "";
	public $SubTitle = "";
	public $PageName = "";
	public $Blog = "";
	public $ReadyForPublish = FALSE;
	public $CreateDate = "";
	public $ModifiedDate = "";
	public $PublishDate = "";
	public $UnpublishDate = "";
	public $CanDelete = FALSE;
	public $CanPublish = FALSE;
	public $CanUnpublish = FALSE;
	public $TemplateFolder = '';
	public $TemplateName = '';
	public $PostFolder = '';
	public $PostUrl = '';
	public $Categories = array();
	public $Types = array();
}

?>