-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20230902.f7a82ce698
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-01-06 07:46:34
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
-- 表的结构 `gaga_community_topic`
--

CREATE TABLE `gaga_community_topic` (
  `topic_id` bigint UNSIGNED NOT NULL,
  `topic_name` varchar(100) DEFAULT NULL COMMENT '话题',
  `status` tinyint DEFAULT '1' COMMENT '状态',
  `is_hot` tinyint DEFAULT '0' COMMENT '推荐',
  `category_id` int UNSIGNED DEFAULT '0' COMMENT '分类id',
  `is_del` tinyint DEFAULT '0',
  `pic` varchar(128) DEFAULT NULL COMMENT '图标',
  `count_use` int UNSIGNED DEFAULT '0' COMMENT '使用次数',
  `count_view` int UNSIGNED DEFAULT '0' COMMENT '浏览量',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sort` int UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='社区话题';

--
-- 转存表中的数据 `gaga_community_topic`
--

INSERT INTO `gaga_community_topic` (`topic_id`, `topic_name`, `status`, `is_hot`, `category_id`, `is_del`, `pic`, `count_use`, `count_view`, `create_time`, `sort`) VALUES
(36, '设计感家居好物', 1, 1, 64, 0, 'http://c22.com/uploads/def/20211110/9b809928e69f46eddc7eaceef8f40347.jpg', 0, 0, '2021-11-10 01:36:39', 0),
(37, '职场穿搭', 1, 1, 67, 0, 'http://c22.com/uploads/def/20211110/f8db48357bd42307d51ef60905ef8516.jpg', 0, 0, '2021-11-10 01:37:33', 0),
(38, '早餐吃什么', 1, 1, 66, 0, 'http://c22.com/uploads/def/20211110/6968c4026ff623ce73bed19612c21dfb.jpg', 0, 0, '2021-11-10 01:37:57', 0),
(39, '养猫那些事', 1, 0, 68, 0, 'http://c22.com/uploads/def/20211110/e7bcce709b1eaebe63350e56b34d56ae.jpg', 0, 0, '2021-11-10 01:38:16', 0);

--
-- 转储表的索引
--

--
-- 表的索引 `gaga_community_topic`
--
ALTER TABLE `gaga_community_topic`
  ADD PRIMARY KEY (`topic_id`) USING BTREE,
  ADD UNIQUE KEY `id` (`topic_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gaga_community_topic`
--
ALTER TABLE `gaga_community_topic`
  MODIFY `topic_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
