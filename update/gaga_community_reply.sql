-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20230902.f7a82ce698
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-01-06 07:46:56
-- 服务器版本： 8.0.33
-- PHP 版本： 8.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `c22`
--

-- --------------------------------------------------------

--
-- 表的结构 `gaga_community_reply`
--

CREATE TABLE `gaga_community_reply` (
  `reply_id` bigint UNSIGNED NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_estonian_ci DEFAULT NULL COMMENT '评论内容',
  `pid` int UNSIGNED DEFAULT '0' COMMENT '回复id',
  `uid` int UNSIGNED DEFAULT '0' COMMENT '发言人',
  `re_uid` int UNSIGNED DEFAULT '0' COMMENT '回复人',
  `count_start` int UNSIGNED DEFAULT '0' COMMENT '点赞数',
  `count_reply` int UNSIGNED DEFAULT '0' COMMENT '评论数',
  `status` tinyint DEFAULT '1' COMMENT '状态 ',
  `community_id` int UNSIGNED DEFAULT '0' COMMENT '文章id',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_del` tinyint DEFAULT '0',
  `refusal` varchar(255) DEFAULT NULL COMMENT '拒绝原因'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='社区评论';

--
-- 转储表的索引
--

--
-- 表的索引 `gaga_community_reply`
--
ALTER TABLE `gaga_community_reply`
  ADD PRIMARY KEY (`reply_id`) USING BTREE,
  ADD UNIQUE KEY `id` (`reply_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gaga_community_reply`
--
ALTER TABLE `gaga_community_reply`
  MODIFY `reply_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
