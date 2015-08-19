<?php

class CategoryFactory {
	public static function HasPosts($categoryid)
	{
		$retval = FALSE;
		$select_sql = "select count(post.id) as postcount from pbpostcategory postcat join pbpost post on postcat.postid = post.id and postcat.categoryid = $categoryid";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			$row = $result->fetch_assoc();
			if ($row['postcount'] > 0) {
				$retval = TRUE;
			}
		}
		else {
			glogg('SQL Error:' . $GLOBALS['mysqli']->error);
			glogg($select_sql,FILE_APPEND);
		}
		
		return $retval;
	}
	public static function GetCategory($categoryid)
	{
		$retval = null;
		$select_sql = "SELECT cat.id from pbcategory cat where cat.id = $categoryid";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			$row = $result->fetch_assoc();
			$retval = new Category($row['id']);
		}
		else {
			glogg('SQL Error:' . $GLOBALS['mysqli']->error);
			glogg($select_sql,FILE_APPEND);
		}
		return $retval;
	}
	
	public static function GetPublishedCategories()
	{
		$retval = array();
		$select_sql = "select cat.id from pbpost post, pbcategory cat, pbpostcategory postcat where post.id = postcat.postid and cat.id = postcat.categoryid and (post.publishdate is not null or post.publishdate > post.unpublishdate) group by cat.id";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push ($retval,new Category($row['id']));
			}
		}
		else {
			glogg('SQL Error:' . $GLOBALS['mysqli']->error);
			glogg($select_sql,FILE_APPEND);
		}
		return $retval;
	}
	public static function GetCategories()
	{
		$retval = array();
		$select_sql = "SELECT * from pbcategory";

		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push ($retval,new Category($row['id']));
			}
		}
		else {
			glogg('SQL Error:' . $GLOBALS['mysqli']->error);
			glogg($select_sql,FILE_APPEND);
		}
		return $retval;
	}
	public static function GetCategoriesByPost($postid)
	{
		$retval = array();
		$select_sql = "SELECT cat.id from pbpostcategory postcat join pbcategory cat on cat.id = postcat.categoryid where postcat.postid = $postid;";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push ($retval,new Category($row['id']));
			}
		}
		else {
			glogg('SQL Error:' . $GLOBALS['mysqli']->error);
			glogg($select_sql,FILE_APPEND);
		}
		return $retval;
	}
	public static function NewCategory()
	{
	}
}

class Category {
	//row is null if we are creating a new Post
	//let's keep the mysqli object mandatory for post create tasks list save and publish
	public function __construct($id = null) {
		if ($id != null) {
			$select_sql = "SELECT * from pbcategory where id = $id";
			if ($result = $GLOBALS['mysqli']->query($select_sql)) {
				$row = $result->fetch_assoc();
				$this->ID = $row['id'];
				$this->Name = $row['name'];
				$this->Dscription = $row['description'];
				$this->Color = $row['color'];
				$this->PostFolder = $row['postfolder'];
				$this->Url = $row['url'];
				$this->Title = $row['title'];
				$this->BackgroundTitle = $row['backgroundtitle'];
				$this->CarouselTitle = $row['carouseltitle'];
				$this->CarouselSubTitle = $row['carouselsubtitle'];
				$this->BackgroundTitleColor = $row['backgroundtitlecolor'];
			}
		}
	}

	//debug method to see all values
	//this code here (using get_object_vars) is begging for a parent class and implement toString in the parent?
	function __toString() {
		$retval = '';
		$object_vars = get_object_vars ($this);
		while ( list ($key, $value) = each ($object_vars) ) {	//nice hash iterator code
			$print_val = $value;
			if (isset($print_val)) {
				$print_val = is_array ($print_val) ? sizeof($print_val) : $print_val;
			
			}
			else {
				$print_val = 'NULL';
			}
			$retval .= $key . '>> ' . $print_val . "\n";
		}
		return $retval;
	}
	public function Publish()
	{
		$publishedBlogs = PostFactory::GetPublishedBlogsByCategory($this->ID);
		$numofblogs = sizeof($publishedBlogs);
		if ($numofblogs) {
			$post_rows = '';
			for ($x=0; $x<$numofblogs; $x++) {
				$post_row = '<div class=row>';
				$post_row .=  Post::blog_snippet($publishedBlogs[$x++]);
				if ($x<$numofblogs) {
					$post_row .=  Post::blog_snippet($publishedBlogs[$x++]);
					if ($x<$numofblogs) {
						$post_row .=  Post::blog_snippet($publishedBlogs[$x]);
					}
				}
				$post_row .= '</div>' . "\n";
				$post_rows .= $post_row;
			}
			file_put_contents('category-blog.log',$post_rows);
			require_once 'pbglobal.php';
			$globalData = GlobalFactory::GetGlobalData();
			$file_contents_string = file_get_contents($globalData->TemplateFolder . $globalData->CategoryTemplateName);
			//not sure what this does:
			//$file_contents_string = str_replace("<headline-placeholders>",$post_rows,$file_contents_string);
			//now let's clean out the template stuff
			$file_contents_string = str_replace('{{category_title}}',$this->Title,$file_contents_string);
			//not sure what this does:
			//$file_contents_string = str_replace('<category_background_title>',$this->BackgroundTitle,$file_contents_string);
			$file_contents_string = str_replace('<category_color>',$this->Color,$file_contents_string);
			$file_contents_string = str_replace('{{category_carousel_title}}',$this->CarouselTitle,$file_contents_string);
			$file_contents_string = str_replace('{{category_carousel_subtitle}}',$this->CarouselSubTitle,$file_contents_string);
			//not sure what this does:
			//$file_contents_string = str_replace('<category_url>',$this->Url,$file_contents_string);
			$file_contents_string = str_replace('{{post_rows}}',$post_rows,$file_contents_string);
			
			file_put_contents($this->PostFolder . $globalData->IndexFileName , $file_contents_string);
		}
	}
	
	public static function getcategorydropdown()
	{
		$retval = '';
		$categories = CategoryFactory::GetCategories();
		foreach ($categories as $category) {
			if (sizeof(PostFactory::GetCanPublishBlogsByCategory($category->ID))) {
				$retval .= '<li><a href="' . $category->Url . '">' . $category->Dscription . '</a></li>';
			}
		}
		return $retval;
	}
	
	//making this public for now - used by the main index.html
	
	//public properties
	public $ID = -1;
	public $Name = '';
	public $Dscription = '';
	public $Color = '';	//might want to think about rgb or whatever
	public $PostFolder = '';
	public $Url = '';
	public $Title = '';
	public $BackgroundTitle = '';
	public $BackgroundTitleColor = '';
	public $CarouselTitle = '';
	public $CarouselSubTitle = '';
	
}
?>
