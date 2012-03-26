--
-- @author Kirill "Nemoden" K
-- schema for tests (MySQL)
--

create database if not exists `test` default character set = utf8 default collate = utf8_general_ci;

use `test`;

create table if not exists `people` (
  `id` int(11) unsigned not null auto_increment,
  `name` varchar(255) not null default '',
  `address` text,
  `birthday` date not null default '0000-00-00', -- test dates < 1970
  `created_at` datetime not null default '0000-00-00 00:00:00', -- test dates
  `updated_at` timestamp not null,
  `age` tinyint(4) not null, -- test field setter
  `gender` set('M', 'F') not null, -- test set
  PRIMARY KEY `id` (`id`),
  KEY `birthday` (`birthday`),
  KEY `created_at` (`created_at`),
  KEY `gender` (`gender`),
  KEY `gender_age` (`gender`, `age`)
) ENGINE=InnoDB CHARSET=utf8 COMMENT='People table for test cases';

create table if not exists `users` (
  `id` int(11) unsigned not null auto_increment,
  `name` varchar(64) not null default '',
  `email` varchar(64) not null default '',
  `password` varchar(64) not null default '', -- test field setter
  `created_at` datetime not null default '0000-00-00 00:00:00',
  `updated_at` timestamp not null,
  `email_confirmation_code` varchar(64) not null default '', -- test field setter
  PRIMARY KEY `id` (`id`),
  KEY `email_confirmation_code` (`email_confirmation_code`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`)) ENGINE=InnoDB CHARSET=utf8 COMMENT='Users';
