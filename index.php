<?php
/*
2015-11-18 浙江省新昌县城西小学 唐明 QQ：147885198
图库程序主文件，本图库以MVC模式构建。其中view使用smarty模板来驱动。所以主文件非常简捷，逻辑非常清楚。

*/
session_start();

include('modle.class.php');

require 'smarty/Smarty.class.php';
$smarty = new Smarty;

$smarty->setTemplateDir(TEMPLATES_PATH);

$pid='0';
if(isset($_GET['pid'])){
	if(is_numeric($_GET['pid'])){
		$pid=$_GET['pid'];
	}
}
$folder=new Folder($pid);
$folder->scan_dir();

$dir=$folder->get_dir();
$file= $folder->get_file();

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
$path=parse_path($paths);

$smarty->assign("dirs",$dir);
$smarty->assign('dir_count',count($dir));
$smarty->assign('file_count',count($file));
$smarty->assign("files",$file);
$smarty->assign('school_name',SCHOOL_NAME);
$smarty->assign('version',VERSION);
$smarty->assign('templates_path',TEMPLATES_PATH);
$smarty->assign('path',$path);
$smarty->assign('path_id',$pid);
$smarty->display('index.html');

?>
