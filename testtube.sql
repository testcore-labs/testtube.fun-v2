-- qzip

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `testtube` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */;
USE `testtube`;

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `video` varchar(24) NOT NULL,
  `text` varchar(500) NOT NULL,
  `isReply` int(11) DEFAULT NULL,
  `date` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `ratings`;
CREATE TABLE `ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating` int(11) NOT NULL DEFAULT 0 COMMENT '0 = like, 1 = dislike',
  `user` int(11) NOT NULL,
  `video` varchar(128) NOT NULL,
  `date` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `strikes`;
CREATE TABLE `strikes` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `admin` int(11) NOT NULL,
  `note` varchar(1000) NOT NULL,
  `date` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `date` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=196615 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `password` varchar(512) NOT NULL,
  `bio` varchar(250) DEFAULT NULL,
  `admin` int(11) NOT NULL DEFAULT 0,
  `verified` int(11) NOT NULL DEFAULT 0,
  `custom` mediumtext DEFAULT NULL,
  `js` mediumtext DEFAULT NULL,
  `avatar` varchar(2500) NOT NULL DEFAULT '/assets/img/avatar.png',
  `banner` varchar(2500) NOT NULL DEFAULT '/assets/img/banner.png',
  `date` int(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `videos`;
CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `watch` varchar(128) NOT NULL COMMENT 'Here goes the value!',
  `title` varchar(100) NOT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `featured` int(11) NOT NULL DEFAULT 0,
  `custom` varchar(2500) DEFAULT NULL,
  `privacy` int(11) NOT NULL DEFAULT 0 COMMENT '0 = public, 1 = unlisted, 2 = private',
  `duration` varchar(123) NOT NULL DEFAULT '0:00',
  `file` varchar(500) DEFAULT NULL,
  `thumbnail` longblob NOT NULL DEFAULT '\'/assets/img/thumbnail.png\'',
  `date` int(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `views`;
CREATE TABLE `views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(512) NOT NULL,
  `video` varchar(128) NOT NULL,
  `date` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


-- 2023-04-08 13:34:28
