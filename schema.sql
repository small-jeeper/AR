create database if not exists `test` default character set = utf-8 default collate = utf-8;

use `test`;

create table if not exists `users` (
  `id` int(11) unsigned not null auto_increment,
  `name` varchar(64) not null default '',
  `email` varchar(64) not null default '',
  `password` varchar(64) not null default '',
  `created_at` datetime not null default '0000-00-00 00:00:00',
  `updated_at` varchar(64) timestamp not null;
  `email_confirmation_code` varchar(64) not null default '',
  PRIMARY KEY `id` (`id`),
  KEY `email_confirmation_code` (`email_confirmation_code`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`)) ENGINE=InnoDB COMMENT='Users';
