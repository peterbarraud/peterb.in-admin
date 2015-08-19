<?php

class TypeFactory {
	public static function GetType($typeid)
	{
		$retval = null;
		$select_sql = "SELECT ty.id,ty.name,ty.icon from pbtype ty where ty.id = $typeid";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			$retval = new PostType($result->fetch_assoc()['id']);
		}
		else {
			glogg('SQL Error:' . $GLOBALS['mysqli']->error);
			glogg($select_sql,FILE_APPEND);
		}
		return $retval;
	}

	public static function GetTypes()
	{
		$retval = array();
		$select_sql = "SELECT ty.id,ty.name,ty.icon from pbtype ty";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push ($retval,new PostType($row['id']));
			}
		}
		else {
			glogg('SQL Error:' . $GLOBALS['mysqli']->error);
			glogg($select_sql,FILE_APPEND);
		}
		return $retval;
	}
	public static function GetTypesByPost($postid)
	{
		$retval = array();
		$select_sql = "SELECT ty.id from pbposttype posttype join pbtype ty on ty.id = posttype.typeid where posttype.postid = $postid;";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push ($retval,new PostType($row['id']));
			}
		}
		else {
			glogg('SQL Error:' . $GLOBALS['mysqli']->error);
			glogg($select_sql,FILE_APPEND);
		}
		return $retval;
	}
	public static function NewType()
	{
	}
}

class PostType {
	//row is null if we are creating a new Post
	//let's keep the mysqli object mandatory for post create tasks list save and publish
	public function __construct($typeid=null) {
		if ($typeid != null) {
			$select_sql = "select id,name,icon from pbtype where id = $typeid;";
			if ($result = $GLOBALS['mysqli']->query($select_sql)) {
				$row = $result->fetch_assoc();
				$this->ID = $row['id'];
				$this->Name = $row['name'];
				$this->Icon = $row['icon'];
			}
			else {
				glogg('SQL Error:' . $GLOBALS['mysqli']->error);
				glogg($select_sql,FILE_APPEND);
			}
		}
	}

	//debug method to see all values
	function __toString() {
		$ret_val = 'ID: ' . $this->ID . "\n";
		$ret_val .= 'Name: ' . $this->Name . "\n";
		$ret_val .= 'Icon: ' . $this->Icon . "\n";
		return $ret_val;
	}
	
	//public properties
	public $ID = -1;
	public $Name = '';
	public $Icon = '';
	
}


?>