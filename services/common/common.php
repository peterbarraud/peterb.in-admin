<?php
function make_json_array($result){$tempArray=array();$retval=array();while($row=$result->fetch_array(MYSQLI_ASSOC)){$tempArray=$row;array_push($retval,$tempArray);}return json_encode($retval);}function fwriteln($fh,$line){fwrite($fh,$line);fwrite($fh,"\n");}
function send_mail(){    $to = "admin@thecorrespondent.in";	$subject = "Test mail";	$message = "Hello! This is a simple email message.";	$from = "admin@thecorrespondent.in";	$headers = "From:" . $from;	mail($to,$subject,$message,$headers);}
function run_ws(){    $pattern = '/\.php\/(.+?)\/?$/';    preg_match($pattern, $_SERVER['REQUEST_URI'], $matches);    $function_parts = explode('/',$matches[1]);		$function_name = $function_parts[0];    array_shift($function_parts);    call_user_func_array($function_name,$function_parts);}function file_put_contents_start($filename){	file_put_contents($filename,'START' . "\n");}function file_put_contents_end($filename){	file_put_contents($filename,'END',FILE_APPEND);}
function getsimplequeryjson($query){require_once 'dbconnection.php';$ret_val=array();$mysqli=MySQLConnection::Open();if($result=$mysqli->query($query)){$ret_val=make_json_array($result);}MySQLConnection::Close($mysqli);return $ret_val;}
function castObject($instance, $className) {return unserialize(sprintf('O:%d:"%s"%s',strlen($className),$className,strstr(strstr(serialize($instance), '"'), ':')));}
?>