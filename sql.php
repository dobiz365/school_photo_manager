<?php
/*
#2015-11-18 �㽭ʡ�²��س���Сѧ ���� QQ��147885198
#��Ƭ����������ݱ�ṹ
#ʹ�÷�����
#		1.���Դ�phpmysqladmin��ҳ���������SQL������ƹ�ȥ����һ�顣
#		2.����������������mysql��Ȼ�����������������һ�顣
#�Ե�Ƭ�̣����ݱ�ṹ������ɡ�

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