/*
SQLyog Community v11.51 (64 bit)
MySQL - 10.1.36-MariaDB : Database - tntl
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tntl` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `tntl`;

/*Table structure for table `comments` */

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `content` text,
  `created` double DEFAULT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Data for the table `comments` */

insert  into `comments`(`no`,`video_id`,`member_id`,`content`,`created`) values (3,1,4,'aaa',1557169127),(4,1,4,'aaa',1557169133),(5,1,4,'test',1557169169),(7,1,4,'Great Video. Thanks',1557332558),(8,1,4,'new comment',1557338684),(9,1,4,'new1',1557338704),(10,2,4,'Hello',1557338708),(11,2,4,'Yes',1557338709),(12,4,4,'Great',1557338712);

/*Table structure for table `followers` */

DROP TABLE IF EXISTS `followers`;

CREATE TABLE `followers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `to_follower_id` int(11) DEFAULT NULL,
  `time` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `likes_unique_index` (`member_id`,`to_follower_id`),
  KEY `member` (`member_id`),
  KEY `to_follower_id` (`to_follower_id`),
  KEY `member_and_to_follow_index` (`member_id`,`to_follower_id`),
  KEY `id_and_to_follow_index` (`id`,`to_follower_id`),
  KEY `id_and_member_index` (`id`,`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

/*Data for the table `followers` */

insert  into `followers`(`id`,`member_id`,`to_follower_id`,`time`) values (21,4,4,1557330207);

/*Table structure for table `members` */

DROP TABLE IF EXISTS `members`;

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(90) DEFAULT NULL,
  `facebook_id` varchar(64) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `first_name` varchar(25) DEFAULT NULL,
  `last_name` varchar(25) DEFAULT NULL,
  `full_name` varchar(50) DEFAULT NULL,
  `invites` int(11) NOT NULL DEFAULT '0',
  `videos_submitted` int(11) NOT NULL DEFAULT '0',
  `games_played` int(11) NOT NULL DEFAULT '0',
  `gamecenter_playerid` varchar(64) DEFAULT '',
  `is_using_gamecenter` int(1) NOT NULL DEFAULT '0',
  `achievements` int(2) NOT NULL DEFAULT '0',
  `approved` enum('Not Approved','Approved','Blocked') NOT NULL DEFAULT 'Approved',
  `times_opened_app` int(11) NOT NULL DEFAULT '0',
  `last_login_ip` varchar(50) DEFAULT NULL,
  `last_login_date` varchar(20) DEFAULT NULL,
  `register_ip` varchar(50) DEFAULT NULL,
  `register_date` varchar(20) DEFAULT NULL,
  `is_iphone` int(1) NOT NULL DEFAULT '1',
  `iphone_token` varchar(64) DEFAULT NULL,
  `register_key` varchar(20) DEFAULT NULL,
  `picture` text,
  `password` text,
  `description` text,
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `facebook_id` (`facebook_id`),
  KEY `member_id` (`member_id`),
  KEY `achievements` (`achievements`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `members` */

insert  into `members`(`member_id`,`username`,`facebook_id`,`email`,`first_name`,`last_name`,`full_name`,`invites`,`videos_submitted`,`games_played`,`gamecenter_playerid`,`is_using_gamecenter`,`achievements`,`approved`,`times_opened_app`,`last_login_ip`,`last_login_date`,`register_ip`,`register_date`,`is_iphone`,`iphone_token`,`register_key`,`picture`,`password`,`description`) values (4,'jin',NULL,'qqq@qqq.com',NULL,NULL,'Yu',0,0,0,'',0,0,'Approved',0,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,'343b1c4a3ea721b2d640fc8700db0f36','test test test');

/*Table structure for table `video_follow_like` */

DROP TABLE IF EXISTS `video_follow_like`;

CREATE TABLE `video_follow_like` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

/*Data for the table `video_follow_like` */

insert  into `video_follow_like`(`no`,`video_id`,`member_id`,`value`) values (44,1,4,1);

/*Table structure for table `video_follow_unlike` */

DROP TABLE IF EXISTS `video_follow_unlike`;

CREATE TABLE `video_follow_unlike` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

/*Data for the table `video_follow_unlike` */

insert  into `video_follow_unlike`(`no`,`video_id`,`member_id`,`value`) values (23,2,4,1),(28,3,4,1),(29,4,4,1);

/*Table structure for table `videos` */

DROP TABLE IF EXISTS `videos`;

CREATE TABLE `videos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `difficulty` enum('easy','medium','hard','any') NOT NULL DEFAULT 'easy',
  `youtube_url` varchar(150) DEFAULT NULL,
  `youtube_id` varchar(20) DEFAULT NULL,
  `video_title` varchar(45) DEFAULT NULL,
  `times_played` int(11) NOT NULL DEFAULT '0',
  `rating` int(11) NOT NULL DEFAULT '0',
  `status` enum('Pending','Approved','Not Approved') NOT NULL DEFAULT 'Pending',
  `added_date` int(11) NOT NULL DEFAULT '0',
  `random_order` int(11) NOT NULL DEFAULT '0',
  `file` text,
  `created` timestamp NULL DEFAULT NULL,
  `lk_count` int(11) DEFAULT NULL,
  `ulk_count` int(11) DEFAULT NULL,
  `view_count` int(11) DEFAULT NULL,
  `report_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `youtube_id` (`youtube_id`),
  KEY `difficulty` (`difficulty`),
  KEY `member_id` (`member_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `videos` */

insert  into `videos`(`id`,`member_id`,`difficulty`,`youtube_url`,`youtube_id`,`video_title`,`times_played`,`rating`,`status`,`added_date`,`random_order`,`file`,`created`,`lk_count`,`ulk_count`,`view_count`,`report_count`) values (1,4,'easy',NULL,NULL,'Test Video',0,0,'Pending',0,0,'/assets/videos/1.mp4','2019-04-24 22:14:16',1,-2,69,0),(2,4,'easy',NULL,NULL,'Video1',0,0,'Pending',0,0,'/assets/videos/1.mp4','2019-04-23 22:14:49',0,1,3,0),(3,4,'easy',NULL,NULL,'Video2',0,0,'Pending',0,0,'/assets/videos/1.mp4','2019-04-12 22:15:02',0,1,3,0),(4,4,'easy',NULL,NULL,'Video3',0,0,'Pending',0,0,'/assets/videos/1.mp4','2019-04-15 22:15:11',0,1,1,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
