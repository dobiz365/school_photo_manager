<?php
/*
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