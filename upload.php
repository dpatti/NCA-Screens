<?php 
/**
 * Upload - Handles uploading files
 *
 * Takes an upload request from jquery.filedrop and stores the incoming
 * files on the server for later viewing.
 *
 * @author Andrew Tomaka
 * @version 1
 **/

$conf = json_decode(file_get_contents('database.conf'));

define('UPLOADS','uploads/');
$extensions = array('jpg','jpeg','png','gif','bmp');

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	error('request method error');
}

if(array_key_exists('image', $_FILES)) {
	$file = $_FILES['image'];

	$original = $file['name'];
	$extension = explode('.',$original);
	$extension = array_pop($extension);
	$extension = strtolower($extension);

	//if(!in_array(strtolower(array_pop(explode('.',$file['name'])),$extensions))) {
	if(!in_array($extension,$extensions)) {
		error('file extension error.');
	}

	$db = mysqli_init();
	$db->real_connect($conf->hostname, $conf->username, $conf->password, $conf->database);
	$query = "INSERT INTO screens (extension, original) VALUES('$extension','$original')";
	$db->query($query);
	$newId = $db->insert_id;

	if(move_uploaded_file($file['tmp_name'], UPLOADS . $newId . '.' . $extension)) {
		echo json_encode(array('type'=>'success','status'=>'Uploaded successfully','file'=>'http://screens.p5dev.com/' . UPLOADS . $newId . '.' . $extension));
		exit;
	}
}
error('unknown error');


function error($message) {
	echo json_encode(array('type'=>'error','status'=>$message));
	exit;
}
?>