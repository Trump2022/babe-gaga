-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-01-15 06:51:01
-- 服务器版本： 5.7.41-log
-- PHP 版本： 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `gaga_fanxinghy`
--

-- --------------------------------------------------------

--
-- 表的结构 `gaga_store_order_info`
--

CREATE TABLE `gaga_store_order_info` (
  `id` int(10) NOT NULL,
  `oid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单ID',
  `user_name` varchar(30) NOT NULL DEFAULT '' COMMENT '上课人姓名',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `age` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '年龄',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别：0-未知，1-男，2-女',
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上课门店ID',
  `store_address` varchar(500) NOT NULL DEFAULT '' COMMENT '门店地址',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '记录添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程订单下单信息';

--
-- 转储表的索引
--

--
-- 表的索引 `gaga_store_order_info`
--
ALTER TABLE `gaga_store_order_info`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `oid` (`oid`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gaga_store_order_info`
--
ALTER TABLE `gaga_store_order_info`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
