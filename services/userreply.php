<?php
//create Response objects ONLY via the factory
class ReplyFactory {
	public static function GetRepliesByResponse($responseid)
	{
		$retval = array();
		$select_sql = "SELECT id FROM pbuserreply where responseid = $responseid order by replydate desc";
		if ($result = $GLOBALS['mysqli']->query($select_sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($retval,new Reply($row['id']));
			}
		}
		return $retval;
	}	
}

class Reply {
	//let's keep the mysqli object mandatory for post create tasks list save and publish
	public function __construct($id = null) {
		//if row is null then get an empty post object
		if ($id != null) {
			$select_sql = "SELECT * from pbuserresponse where id = $id";
			if ($result = $GLOBALS['mysqli']->query($select_sql)) {
				$row = $result->fetch_assoc();
				$this->ID = $row['id'];
				$this->Reply = $row['reply'];
				$this->Name = $row['name'];
				$this->Email = $row['email'];
				$this->ReplyDate = $row['replydate'];
				$this->IsOK = isset($row['isok']) ? TRUE : FALSE;
			}
		}
	}
	public function SaveReply()
	{
	}	
	public function __toString()
	{
		$ret_val = 'ID: ' . $this->ID;
		$ret_val .= 'Response: ' . $this->Reply . "\n";
		$ret_val .= 'ResponseDate: ' . $this->ReplyDate . "\n";
		$ret_val .= 'User name: ' . $this->Name . "\n";
		$ret_val .= 'email: ' . $this->Email . "\n";
		$ret_val .= 'Is OK: ' . $this->IsOK . "\n";
		return $ret_val;
	}
	//public properties
	public $ID = 0;
	public $Reply = "";
	public $Name = "";
	public $Email = "";
	public $IsOK = FALSE;
	public $ReplyDate = "";
}

?>