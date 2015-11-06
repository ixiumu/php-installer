-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 10 月 02 日 04:20
-- 服务器版本: 5.5.16
-- PHP 版本: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `test`
--

-- --------------------------------------------------------

--
-- 表的结构 `empty_admin`
--

CREATE TABLE `empty_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(30) DEFAULT NULL COMMENT '后台管理员用户名',
  `admin_pass` char(64) DEFAULT NULL COMMENT '管理员密码',
  `admin_login` int(11) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `admin_myname` varchar(100) DEFAULT NULL COMMENT '管理员名字',
  `admin_email` varchar(100) DEFAULT NULL COMMENT '管理员邮箱',
  `admin_oldip` varchar(20) NOT NULL,
  `admin_ip` varchar(20) DEFAULT NULL COMMENT '登录ip',
  `admin_rsdate` int(11) NOT NULL COMMENT '注册时间',
  `admin_olddate` int(11) NOT NULL,
  `admin_ok` int(11) DEFAULT '0' COMMENT '0不锁，1锁定',
  `admin_date` int(11) DEFAULT NULL COMMENT '登录日期',
  `admin_type` int(11) DEFAULT '1' COMMENT '管理员类型,0为超级，1为普通',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台管理员表' AUTO_INCREMENT=3 ;

--
-- 表的结构 `empty_special`
--

CREATE TABLE `empty_special` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `special_title` varchar(200) DEFAULT NULL,
  `special_keywords` varchar(200) DEFAULT NULL,
  `special_description` text,
  `special_template` varchar(100) DEFAULT NULL,
  `special_addtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的结构 `empty_user`
--

CREATE TABLE `empty_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) DEFAULT NULL,
  `user_pass` char(64) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_ok` int(11) DEFAULT '0',
  `user_myname` varchar(100) DEFAULT NULL,
  `user_sign` text,
  `user_tel` varchar(100) DEFAULT NULL,
  `user_sex` int(11) DEFAULT '1',
  `user_login` int(11) DEFAULT '0',
  `user_qq` varchar(100) DEFAULT NULL,
  `user_rsdate` int(11) NOT NULL,
  `user_reason` text,
  `user_cpname` varchar(100) DEFAULT NULL,
  `user_cpaddress` varchar(100) DEFAULT NULL,
  `user_cpfax` varchar(100) DEFAULT NULL,
  `user_cpcode` varchar(100) DEFAULT NULL,
  `user_cpwebsite` varchar(100) DEFAULT NULL,
  `user_oldip` varchar(20) NOT NULL,
  `user_ip` varchar(20) NOT NULL,
  `user_olddate` int(11) NOT NULL,
  `user_date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='前台用户表' AUTO_INCREMENT=5 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
