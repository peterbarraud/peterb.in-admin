<?php
//create Response objects ONLY via the factory
class ResponseFactory {
	public static function GetResponsesByPost($postid)
	{
		$retval = array();
		$select_sql = "SELECT id FROM pbuserresponse where postid = $postid order by responsedate desc";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($retval,new UserResponse($row['id']));
			}
		}
		return $retval;
	}	
	public static function GetIsOKResponsesByPost($postid)
	{
		$retval = array();
		//need to fix the query when we have implemented curation
		$select_sql = "SELECT id FROM pbuserresponse where postid = $postid order by responsedate desc";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($retval,new UserResponse($row['id']));
			}
		}
		return $retval;
	}	
}

class UserResponse {
	public function __construct($id = null) {
		//if row is null then get an empty post object
		if ($id != null) {
			$select_sql = "SELECT * from pbuserresponse where id = $id";
			if ($result = $GLOBALS['mysqli']->query($select_sql)) {
				$row = $result->fetch_assoc();
				$this->ID = $row['id'];
				$this->PostID = $row['postid'];
				$this->Response = $row['response'];
				$this->ResponseDate = $row['responsedate'];
				$this->Name = $row['name'];
				$this->Email = $row['email'];
				$this->IsOK = isset($row['isok']) ? TRUE : FALSE;
				$this->Replies = ReplyFactory::GetRepliesByResponse($this->ID);
			}
		}
	}
	public function Save()
	{
		
		if ($this->ID < 1) {	//add new
			$insert_sql = function() {
				$col_list = '';
				$val_list = '';
				if (!empty($this->PostID)) {
					$col_list .= 'postid,';
					$val_list .= "'$this->PostID',";
				}
				if (!empty($this->Response)) {
					$col_list .= 'response,';
					$val_list .= "'$this->Response',";
				}
				if (!empty($this->Name)) {
					$col_list .= 'name,';
					$val_list .= "'$this->Name',";
				}
				if (!empty($this->Email)) {
					$col_list .= 'email,';
					$val_list .= "'$this->Email',";
				}
				$col_list .= 'responsedate';
				$val_list .= 'now()';
				$retval = 'insert into pbuserresponse (' . $col_list . ') values (' . $val_list . ');';
				return $retval;
			};
			if ($GLOBALS['mysqli']->query($insert_sql())) {
				$this->ID = $GLOBALS['mysqli']->insert_id;
			}
			else {
				glogg('something happened:' . $GLOBALS['mysqli']->error);
				glogg($insert_sql);
			}
		}
		else {	//update isok
			if ($this->IsOK == TRUE) {
				$update_sql = "update pbuserresponse set isok = '' where id = $this->ID;";
				if ($GLOBALS['mysqli']->query($update_sql())) {
				}
				else {
					glogg('something happened--:' . $GLOBALS['mysqli']->error);
					glogg($update_sql());
				}
			}
		
		}
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
	public $PostID = 0;
	public $Response = "";
	public $Name = "";
	public $Email = "";
	public $IsOK = FALSE;
	public $ResponseDate = "";
	public $Replies = array();
}

?>