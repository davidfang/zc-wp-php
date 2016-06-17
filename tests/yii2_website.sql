-- phpMyAdmin SQL Dump
-- version 4.4.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: 2015-07-21 20:15:42
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yii2_website`
--

-- --------------------------------------------------------

--
-- 表的结构 `xw_admin_info`
--

CREATE TABLE IF NOT EXISTS `xw_admin_info` (
  `id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '部门',
  `status` set('在职','劳务','离职') COLLATE utf8_unicode_ci DEFAULT '在职' COMMENT '状态',
  `in_time` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '入职时间',
  `id_number` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '身份证号',
  `sex` set('男','女') COLLATE utf8_unicode_ci DEFAULT '男' COMMENT '性别',
  `birthday` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '生日',
  `birthday_month` int(2) DEFAULT NULL COMMENT '生日月份',
  `age` int(2) DEFAULT NULL COMMENT '年龄',
  `mobile` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '联系电话',
  `created_at` int(13) DEFAULT NULL COMMENT '建立时间',
  `updated_at` int(13) DEFAULT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='管理人员信息表';

--
-- 转存表中的数据 `xw_admin_info`
--

INSERT INTO `xw_admin_info` (`id`, `city`, `department`, `status`, `in_time`, `id_number`, `sex`, `birthday`, `birthday_month`, `age`, `mobile`, `created_at`, `updated_at`) VALUES
(1, '上海', '董事会', '在职', '2015/01/01', '41020519821019', '男', '19850107', 1, 30, '13612345678', 2147483647, 2147483647),
(2, '北京', '市场', '在职', '20150203', '41020519821019', '男', '19850805', 8, 30, '13512345678', 2147483647, 2147483647),
(3, '苏州', '财务', '', '20150103', '410209185210252067', '男', '19861015', 10, 29, '15612345678', 1437475888, 1437475888);

-- --------------------------------------------------------

--
-- 表的结构 `xw_admin_user`
--

CREATE TABLE IF NOT EXISTS `xw_admin_user` (
  `id` int(11) NOT NULL,
  `fromusername` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `userphoto` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'default.jpg',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `xw_admin_user`
--

INSERT INTO `xw_admin_user` (`id`, `fromusername`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `userphoto`, `created_at`, `updated_at`) VALUES
(1, NULL, 'admin', 'LSvYNsKja1651tBuAxY_PDd6zqwrZmkk', '$2y$13$bk2PADWe5/UlAW5/dTYhN.p.JlGipAdc6Sry.gZaDhALIJTxrVM.K', NULL, 'example1@abc.com', 10, 'nophoto.jpg', 0, 0),
(2, NULL, 'demo', 'zlnLab8GTqV1G5U02JcwRt6iYI_4oaEy', '$2y$13$JLSbF4rnRbS4unJgwPS0FOFD8JkGegaYV9XA9GvLoPDyZ0aiJfJI6', NULL, 'example2@abc.com', 10, 'nophoto.jpg', 0, 0),
(3, NULL, '老板', '', '$2y$13$PYoWzxHLbWwC7IBfwAErm.SD8rj7afbHChMx4wdvELAP0L2YDOwyC', NULL, '', 10, 'default.jpg', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `xw_adm_user`
--

CREATE TABLE IF NOT EXISTS `xw_adm_user` (
  `id` int(11) NOT NULL,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `userphoto` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `xw_adm_user`
--

INSERT INTO `xw_adm_user` (`id`, `username`, `password`, `userphoto`) VALUES
(1, 'admin', '$2y$13$sUelwYRFRszQHZtvzrB2Iuz.km68287HygljZI9fpdhgUtA9EyMGS', 'default.jpg'),
(2, 'demo', '$2y$13$Oj2xyoYdoMf5ij7PmwaTdOVK0ayt0HLNlrBIb53D1ul8lmYEkUeP6', 'default.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `xw_auth_assignment`
--

CREATE TABLE IF NOT EXISTS `xw_auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `xw_auth_assignment`
--

INSERT INTO `xw_auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('管理员', '2', 1436868814);

-- --------------------------------------------------------

--
-- 表的结构 `xw_auth_item`
--

CREATE TABLE IF NOT EXISTS `xw_auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `xw_auth_item`
--

INSERT INTO `xw_auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('admininfo/index', 2, '用户列表', NULL, NULL, 1437391893, 1437480807),
('conf', 2, '系统设置', NULL, NULL, 1436867969, 1436867969),
('rbac', 2, '权限管理', NULL, NULL, 1436869060, 1436869060),
('rbac/permissions', 2, '资源管理', NULL, NULL, 1436865051, 1436865491),
('rbac/roles', 2, '角色管理', NULL, NULL, 1436866405, 1436866405),
('sys/menu', 2, '菜单管理', NULL, NULL, 1436868018, 1436868018),
('user/add', 2, '用户', NULL, NULL, 1436860839, 1436860839),
('user/index', 2, '用户管理', NULL, NULL, 1436866278, 1436866278),
('管理员', 1, '管理员', NULL, NULL, 1436865294, 1436865294);

-- --------------------------------------------------------

--
-- 表的结构 `xw_auth_item_child`
--

CREATE TABLE IF NOT EXISTS `xw_auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `xw_auth_item_child`
--

INSERT INTO `xw_auth_item_child` (`parent`, `child`) VALUES
('user/add', 'admininfo/index'),
('管理员', 'conf'),
('conf', 'rbac'),
('管理员', 'rbac'),
('rbac', 'rbac/permissions'),
('rbac', 'rbac/roles'),
('管理员', 'rbac/roles'),
('conf', 'sys/menu'),
('管理员', 'sys/menu'),
('管理员', 'user/index');

-- --------------------------------------------------------

--
-- 表的结构 `xw_auth_rule`
--

CREATE TABLE IF NOT EXISTS `xw_auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `xw_menu`
--

CREATE TABLE IF NOT EXISTS `xw_menu` (
  `id` int(11) NOT NULL,
  `menuname` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `parentid` smallint(6) NOT NULL DEFAULT '0',
  `route` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `menuicon` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'icon-book',
  `level` smallint(6) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `xw_menu`
--

INSERT INTO `xw_menu` (`id`, `menuname`, `parentid`, `route`, `menuicon`, `level`) VALUES
(1, '设置', 0, 'conf', 'icon-cog', 1),
(2, '菜单管理', 1, 'sys/menu', 'icon-book', 3),
(3, '用户管理', 1, 'user/index', 'icon-book', 3),
(4, '权限管理', 1, 'rbac', 'icon-book', 2),
(5, '角色管理', 4, 'rbac/roles', 'icon-book', 3),
(6, '用户', 0, 'user/add', 'icon-book', 1),
(7, '资源管理', 4, 'rbac/permissions', 'icon-book', 3),
(8, '用户列表', 6, 'admininfo/index', 'icon-book', 2);

-- --------------------------------------------------------

--
-- 表的结构 `xw_migration`
--

CREATE TABLE IF NOT EXISTS `xw_migration` (
  `version` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `xw_migration`
--

INSERT INTO `xw_migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1437032995),
('m130524_201442_init', 1437375051),
('m140506_102106_rbac_init', 1437032998),
('m141022_124022_create_menutable', 1437032998),
('m141101_015745_createtable_admin_user', 1437032999),
('m150720_065701_admin_user', 1437376122);

-- --------------------------------------------------------

--
-- 表的结构 `xw_user`
--

CREATE TABLE IF NOT EXISTS `xw_user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `xw_admin_info`
--
ALTER TABLE `xw_admin_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xw_admin_user`
--
ALTER TABLE `xw_admin_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xw_adm_user`
--
ALTER TABLE `xw_adm_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xw_auth_assignment`
--
ALTER TABLE `xw_auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`);

--
-- Indexes for table `xw_auth_item`
--
ALTER TABLE `xw_auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

--
-- Indexes for table `xw_auth_item_child`
--
ALTER TABLE `xw_auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Indexes for table `xw_auth_rule`
--
ALTER TABLE `xw_auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `xw_menu`
--
ALTER TABLE `xw_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xw_migration`
--
ALTER TABLE `xw_migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `xw_user`
--
ALTER TABLE `xw_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `xw_admin_user`
--
ALTER TABLE `xw_admin_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `xw_adm_user`
--
ALTER TABLE `xw_adm_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `xw_menu`
--
ALTER TABLE `xw_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `xw_user`
--
ALTER TABLE `xw_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 限制导出的表
--

--
-- 限制表 `xw_auth_assignment`
--
ALTER TABLE `xw_auth_assignment`
  ADD CONSTRAINT `xw_auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `xw_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `xw_auth_item`
--
ALTER TABLE `xw_auth_item`
  ADD CONSTRAINT `xw_auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `xw_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- 限制表 `xw_auth_item_child`
--
ALTER TABLE `xw_auth_item_child`
  ADD CONSTRAINT `xw_auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `xw_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `xw_auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `xw_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
