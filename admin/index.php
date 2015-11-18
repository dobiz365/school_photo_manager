<?php
session_start();
if(!isset($_SESSION['admin'])){
	header('Location: ../index.php');
	exit;
}
include('../modle.class.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"/>
<title><?php echo SCHOOL_NAME;?>图库程序 V<?php echo VERSION;?></title>
<link rel='stylesheet' type='text/css' href='css/index.css'/>
</head>
<body>
<?php

$pid='0';
if(isset($_GET['pid'])){
	if(is_numeric($_GET['pid'])){
		$pid=$_GET['pid'];
	}
}
$folder=new Folder($pid);
$folder->scan_dir();

$dirs=$folder->get_dir();
$files= $folder->get_file();
?>
<div class='navbar'>
<font color='yellow' >【 <?php echo SCHOOL_NAME; ?> 】</font>的照片列表 
</div>
<div class='curdir'>当前目录：
<?php 

	function parse_path($paths){
		$len=count($paths);
		if($len<=0) return '/';
		$p='<a href="index.php?pid=0">根目录</a>/';
		for($i=$len-1;$i>=0;$i--){
			$p.='<a href="index.php?pid='.$paths[$i][0].'">'.$paths[$i][1].'</a>/';
		}
		return $p;
	}
	
$paths=$folder->get_path();
echo parse_path($paths);
?> <button class='blue_button' id='new_folder'>新建文件夹</button>
<button class='blue_button' id='select_all'>全  选</button>
<button class='blue_button' id='select_none'>清除全选</button>
<button class='blue_button' id='delete_btn'>删除选中</button>
<button class='blue_button' id='rename_btn'>改名</button>
</div>
<div id='photobox'>
<?php
$len=count($dirs);
if($len>0){
	for($i=0;$i<$len;$i++){
		$dir=$dirs[$i];
?>
	<div class='dirclass'>
	<span class='check_box' data='0' dataid='<?php echo $dir['id'] ?>'>&#9744;</span>
	<a href="index.php?pid=<?php echo $dir['id'] ?>">
	<img class='image' src='images/folder.jpg'>
	</a>
	<div class='dirtitle'>
	<a href="index.php?pid=<?php echo $dir['id'] ?>"><?php echo $dir['name'] ?></a>
	</div>
	</div>
<?php
	}
}
$len=count($files);
if($len>0){
	for($i=0;$i<$len;$i++){
		$file=$files[$i];
?>
	<div class='fileclass'>
	<span class='check_box' data='0' dataid='<?php echo $file['id'] ?>'>&#9744;</span>
	<a href="">
	<img src="../<?php echo $file['prev_img'] ?>" class='image'>
	</a>
	<div class='dirtitle'>
	<a href=""><?php echo $file['name'] ?></a>
	</div>
	</div>
<?php
	}
}
?>
<div class='dirclass'>
	
	<label class='add_img' for="file_upload">+</label>
	<input type='file' name='file_upload' id='file_upload' multiple="multiple" style='position:absolute;visibility:hidden;'/>
	<div class='dirtitle'>
	<a href="">添加图片</a>
	</div>
	</div>
</div>
<script src='js/jquery.min.js'></script>
<script src='js/fileread_xhr_upload.js'></script>
<script src='js/admin_index.js'></script>
<script>
var path_id=<?php echo $pid;?>;
</script>
<div class='copyright'>
制作者：唐明，QQ:147885198，如有改进请联系我。
<button class='red_button' id='admin_logout'>退出管理</button>
</div>
</body>
</html>
