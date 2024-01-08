-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20230902.f7a82ce698
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-01-06 07:38:32
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
-- 表的结构 `gaga_community_category`
--

CREATE TABLE `gaga_community_category` (
  `category_id` bigint UNSIGNED NOT NULL,
  `cate_name` varchar(50) DEFAULT NULL COMMENT '分类名',
  `pid` int DEFAULT NULL COMMENT '父级ID',
  `path` varchar(255) DEFAULT '/' COMMENT '路径 ',
  `is_show` tinyint DEFAULT '1' COMMENT '状态',
  `level` int DEFAULT '0' COMMENT '等级',
  `sort` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='社区分类';

--
-- 转存表中的数据 `gaga_community_category`
--

INSERT INTO `gaga_community_category` (`category_id`, `cate_name`, `pid`, `path`, `is_show`, `level`, `sort`) VALUES
(64, '家居', 0, '/', 1, 0, 0),
(65, '美妆', 0, '/', 1, 0, 0),
(66, '美食', 0, '/', 1, 0, 0),
(67, '穿搭', 0, '/', 1, 0, 0),
(68, '旅游', 0, '/', 1, 0, 0);

--
-- 转储表的索引
--

--
-- 表的索引 `gaga_community_category`
--
ALTER TABLE `gaga_community_category`
  ADD PRIMARY KEY (`category_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gaga_community_category`
--
ALTER TABLE `gaga_community_category`
  MODIFY `category_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
