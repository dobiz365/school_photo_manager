<?php
/*
2015-11-12 浙江省新昌县城西小学 唐明 QQ：147885198
模型类

*/
include('config.php');

class Folder{
	private  $id=0;
	private  $dir=array();
	private  $file=array();
	private  $conn;
	
	function __construct($p_id){
		$this->id=$p_id;
		$s='mysql:host='.DB_HOST.';dbname='.DB_NAME;
		$this->conn= new PDO($s,DB_USER,DB_PWD);
		$this->conn->exec("set names utf8");
	}
	//列出本目录文件列表
	function scan_dir(){
		$sql='select * from m_dir where flag=0 and parent_id='.$this->id;
		$query=$this->conn->query($sql);
		$this->dir=$query->fetchAll(PDO::FETCH_ASSOC);
		$sql='select * from m_file where flag=0 and parent_id='.$this->id;
		$query=$this->conn->query($sql);
		$this->file=$query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_dir(){
		return $this->dir;
	}
	
	function get_file(){
		return $this->file;
	}
	
	function get_id(){
		return $this->id;
	}
	
	function set_id($id){
		$this->id=$id;
	}
	//删除本目录
	function delete_self(){
		return self::delete_dir($this->conn,$this->id);
	}
	//目录中添加下级目录
	function add_dir($name){
		return self::add_dir_byid($this->conn,$this->id,$name,'');
	}
	//目录中添加图片
	function add_img($name,$src,$prev_img,$size,$width,$height){
		return self::add_img_byid($this->conn,$this->id,$name,$src,$prev_img,$size,$width,$height);
	}
	
	function get_path(){
		$paths=array();
		$i=0;
		$parent_id=$this->id;
		do{
			$sql='select id,name,parent_id from m_dir where id='.$parent_id;
			$query=$this->conn->query($sql);
			$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row){
				$parent_id=$row['parent_id'];
				$paths[]=array($row['id'],$row['name']);
			}
			$i++;
			if($i>100) break;			
		}while($parent_id!=0);
		return $paths;
	}
	

	
	static function filter_sql($str){
		return str_replace("'",'"',$str);
	}
	/*静态函数 真实删除目录
		参数：$conn  	数据库连接
			  $id		待删除的目录id
		注意：将会递归删除下级目标和文件夹
	*/
	static function delete_dir($conn,$id){
		if($id=='' || !is_numeric($id) || $id<=0){
			return array('code'=>100,'info'=>'根目录不能删除！','data'=>'');
		}
		//删除目录下所有文件
		$sql='update m_file set flag=1 where parent_id='.$id;
		$conn->exec($sql);
		$sql='select id from m_dir where parent_id='.$id;
		$query=$conn->query($sql);
		$dirs=$query->fetchAll(PDO::FETCH_ASSOC);
		$flag=true;
		foreach($dirs as $dir){
			self::delete_dir($conn,$dir['id']);
		}
		$sql='update m_dir set flag=1 where id='.$id;
		if($conn->exec($sql)>0){
			return array('code'=>200,'info'=>'删除成功！','data'=>'');
		}else{
			return array('code'=>101,'info'=>'删除失败！','data'=>'');
		}
	}
	/*静态函数 真实删除目录
		参数：$conn  	数据库连接
			  $id		待删除的目录id
		约束：只能删除空目录
	*/
	static function real_delete_dir($conn,$id){		
		if($id=='' || !is_numeric($id) || $id<=0){
			return array('code'=>100,'info'=>'根目录不能删除！','data'=>'');
		}
		$sql='select count(id) as cnt from m_dir where parent_id='.$id;
		$query=$conn->query($sql);
		$row=$query->fetch(PDO::FETCH_ASSOC);
		if($row['cnt']>0){
			return array('code'=>101,'info'=>'目录不为空，里面还有目录！','data'=>'');
		}
		$sql='select count(id) as cnt from m_file where parent_id='.$id;
		$query=$conn->query($sql);
		$row=$query->fetch(PDO::FETCH_ASSOC);
		if($row['cnt']>0){
			return array('code'=>102,'info'=>'目录不为空，里面还有图片！','data'=>'');
		}
		$sql='delete from m_dir where id='.$id;
		if($conn->exec($sql)>0){
			return array('code'=>200,'info'=>'删除目录成功！','data'=>'');
		}else{
			return array('code'=>103,'info'=>'写入数据库失败，无法删除！','data'=>'');
		}
	}
	/*静态函数 虚拟删除图片
	参数：$conn		数据库连接
		  $ids		待删除的文件ID列表，以逗号分隔
	一次可以删除多个文件
	*/
	function delete_img($ids){
		if($ids==''){
			return array('code'=>106,'info'=>'ID参数不能为空！','data'=>'');
		}
		$sql="update m_file set flag=1 where parent_id=".$this->id." and id in ({$ids})";
		
		if($this->conn->exec($sql)>0){
			return array('code'=>200,'info'=>'删除图片成功！','data'=>'');
		}else{
			return array('code'=>107,'info'=>'删除图片失败！','data'=>'');
		}
	}
	/*静态函数 真实删除图片
	参数：$conn		数据库连接
		  $ids		待删除的文件ID列表，以逗号分隔
	一次可以删除多个文件
	*/
	static function real_delete_img($conn,$ids){
		if($ids==''){
			return array('code'=>106,'info'=>'ID参数不能为空！','data'=>'');
		}
		$sql="select src,prev_img from m_file where id in ({$ids})";
		$query=$conn->query($sql);
		$rows=$query->fetchAll(PDO::FETCH_ASSOC);
		for($i=0,$len=count($rows);$i<$len;$i++){
			$f=$rows[$i];
			if(file_exists($f['src'])){
				unlink($f['src']);
			}
			if(file_exists($f['prev_img'])){
				unlink($f['prev_img']);
			}
		}
		$sql="delete from m_file where id in ({$ids})";
		if($conn->exec($sql)>0){
			return array('code'=>200,'info'=>'删除图片成功！','data'=>'');
		}else{
			return array('code'=>107,'info'=>'删除图片失败！','data'=>'');
		}
	}
	
	static function add_dir_byid($conn,$parent_id,$name,$icon){
		$name=self::filter_sql($name);
		$sql="insert into m_dir (id,name,size,cdate,icon,parent_id,flag) values(null,'{$name}',0,now(),'',{$parent_id},0)";
		if($conn->exec($sql)>0){
			$id=$conn->lastInsertId();
			return array('code'=>200,'info'=>'添加目录成功！','data'=>$id);
		}else{
			return array('code'=>104,'info'=>'数据库错误，添加目录失败！','data'=>'');
		}
	}
	
	static function add_img_byid($conn,$parent_id,$name,$src,$prev_img,$size,$width,$height){
		$name=self::filter_sql($name);
		$sql="insert into m_file (id,name,size,cdate,prev_img,src,flag,parent_id,width,height) values (null,'{$name}',{$size},now(),'{$prev_img}','{$src}',0,{$parent_id},{$width},{$height})";
		echo $sql;
		//echo $sql;
		if($conn->exec($sql)>0){
			return array('code'=>200,'info'=>'添加图片成功！','data'=>'');
		}else{
			return array('code'=>105,'info'=>'数据库错误，添加图片失败！','data'=>'');
		}		
	}
	
	function rename_by_id($id,$type,$new_name){
		return self::rename($this->conn,$id,$type,$new_name);
	}
	
	static function rename($conn,$id,$type,$new_name){
		$new_name=self::filter_sql($new_name);
		if($type==0){
			$sql="update m_file set name='$new_name' where id=$id";
		}else{
			$sql="update m_dir set name='$new_name' where id=$id";
		}
		if($conn->exec($sql)>0){
			return array('code'=>200,'info'=>'改名成功！','data'=>'');
		}else{
			return array('code'=>100,'info'=>'改名失败！','data'=>'');
		}
	}
}
?>