<?php
/*
#2015-11-18 浙江省新昌县城西小学 唐明 QQ：147885198
#照片管理程序数据表结构
#使用方法：
#		1.可以打开phpmysqladmin网页，把下面的SQL命令都复制过去运行一遍。
#		2.可以在命令行运行mysql，然后复制下面的命令运行一遍。
#稍等片刻，数据表结构建立完成。

create database image_lib;
use image_lib;

create table if not exists m_dir (
	id int unsigned not null auto_increment primary key,
	name varchar(50) not null default '',
	size int not null default 0,
	cdate datetime,
	icon varchar(100) not null default '',
	parent_id int unsigned not null default 0,
	flag tinyint unsigned not null default 0
	
) engine=InnoDB default charset=utf8;

create table if not exists m_file (
	id int unsigned not null auto_increment primary key,
	name varchar(50) not null default '',
	size int not null default 0,
	cdate datetime,
	prev_img varchar(100) not null default '',
	src varchar(100) not null default '',
	width int not null default 0,
	height int not null default 0,
	parent_id int unsigned not null default 0,
	flag tinyint unsigned not null default 0
) engine=InnoDB default charset=utf8;

alter table m_dir add index(parent_id);
alter table m_file add index(parent_id);
*/
?>