<?php
session_start();

include('../modle.class.php');
if(isset($_SESSION['admin'])){
	if(isset($_POST['folder_name']) && isset($_POST['path_id']) && is_numeric($_POST['path_id'])){	
		$folder=new Folder($_POST['path_id']);
		$ret=$folder->add_dir($_POST['folder_name']);
		echo json_encode($ret);
	}else{
		echo '{"code":101,"info":"没有文件夹名！","data":""}';
	}
}else{
	echo '{"code":102,"info":"没有登录，请登录！","data":""}';
}
?>