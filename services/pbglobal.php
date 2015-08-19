<?php
class GlobalFactory {
	public static function GetGlobalData()
	{
		$retval = null;
		$select_sql = "SELECT id FROM pbglobal";
		$GLOBALS['mysqli']->query($select_sql);
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			$retval = new PbGlobal($result->fetch_assoc()['id']);
		}
		else {
			glogg('something happened:' . $GLOBALS['mysqli']->error);
			glogg($insert_sql);
		}
		return $retval;
	}	
}

class PbGlobal {
	public function __construct() {
		$select_sql = "SELECT * from pbglobal";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			$row = $result->fetch_assoc();
			$this->TemplateFolder = $row['templatefolder'];
			$this->PostTemplateName = $row['posttemplatename'];
			$this->PostFolder = $row['postfolder'];
			$this->PostUrl = $row['posturl'];
			$this->CategoryTemplateName = $row['categorytemplatename'];
			$this->IndexTemplateName = $row['indextemplatename'];
			$this->IndexFolder = $row['indexfolder'];
			$this->IndexFileName = $row['indexfilename'];
			$this->LightCarousel = $row['lightcarousel'];
			$this->DarkCarousel = $row['darkcarousel'];
		}
		else {
			glogg('something happened:' . $GLOBALS['mysqli']->error);
			glogg($insert_sql);
		}
	}
	public function Publish()
	{
		$publishedBlogs = PostFactory::GetPublishedBlogs();
		//but only if there is at least one post in the category
		$numofblogs = sizeof($publishedBlogs);
		$post_rows = '';
		for ($x=0; $x<$numofblogs; $x++) {
			$post_row = '<div class=row>';
			$blogPost = $publishedBlogs[$x++];
			$post_row .=  Post::blog_snippet($blogPost);
			if ($x<$numofblogs) {
				$blogPost = $publishedBlogs[$x++];
				$post_row .=  Post::blog_snippet($blogPost);
				if ($x<$numofblogs) {
					$blogPost = $publishedBlogs[$x];
					$post_row .=  Post::blog_snippet($blogPost);
				}
			}
			$post_row .= '</div>' . "\n";
			$post_rows .= $post_row;
		}
		
		$file_contents_string = file_get_contents($this->TemplateFolder . $this->IndexTemplateName);
		$file_contents_string = str_replace('{{post_rows}}',$post_rows,$file_contents_string);
		file_put_contents($this->IndexFolder . $this->IndexFileName , $file_contents_string);
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
	public $TemplateFolder = '';
	public $PostTemplateName = '';
	public $PostFolder = '';
	public $PostUrl = '';
	public $CategoryTemplateName = '';
	public $IndexTemplateName = '';
	public $IndexFolder = '';
	public $IndexFileName = '';
	public $LightCarousel = '';
	public $DarkCarousel = '';
}

?>
