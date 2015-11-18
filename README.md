#学校图库管理程序(PHP版)
#school_photo_manager

2015-11-12 浙江省新昌县城西小学 唐明 QQ：147885198
* 模型类 modle 是文件夹的抽象表达
* 创建:	$folder=new Folder(上级文件夹ID);     若不提供id,则默认是根目录。
* 方法：
	
		scan_dir()		列出内部的所有文件夹和文件，并存放在类内部的$dir和$file数组中
		get_dir()		返回$dir数据
		get_file()		返回$file数据
		get_id()		返回当前文件夹id
		set_id()		设置当前文件夹id
		delete_self()	删除自己
		add_dir($name)	添加文件夹（参数：文件夹名）
		add_img($name,$src,$prev_img,$size,$width,$height)
			添加照片（参数：$name显示名字,$src照片路径,$prev_img预览路径,$size照片大小,$width宽度,$height高度）
		get_path()  	返回当前文件夹的上级文件夹列表数组
		rename_by_id($id,$type,$new_name)
			文件夹或照片改名（参数：$id,$type类型＝0文件 ＝1文件夹,$new_name新名字）

* 为了加快数据库的查询速度，本程序将文件和文件夹分两个表进行存储，导致在编程比较繁琐。

#scan_dir.php功能
* 功能：照片数据迁移程序
* 本程序可以将已有的照片数据整体迁移到数据库中，并按照原有的目录结构进行排列，也可以将照片挂到某个目录下面，进行增量添加。

* 使用方法：

1.设置好scan_dir.php中的参数，$src_path表示源文件目录，$pid表示要挂在数据库中的哪个目录下，0表示根目录。

2.环境变量path中添加php的路径。保证可以在scan_dir.php文件夹中可以运行PHP程序。

3.在scan_dir.php文件夹中打开CMD窗口，并运行php scan_dir.php

4.等待程序运行完成。
	因为照片数据可能较多，无法在短时间内转移完成，所以不能在网页上运行PHP程序（有脚本运行时间限制）。在命令行上运行则没有这样的限制。

	本程序支持png、jpg、gif三种格式图片的迁移，并会在程序中进行过滤。而且会过滤small文件夹。使用时请注意。
