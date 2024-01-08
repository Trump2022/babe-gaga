-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20230902.f7a82ce698
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-01-06 07:47:44
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
-- 表的结构 `gaga_community`
--

CREATE TABLE `gaga_community` (
  `community_id` int UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `image` varchar(1000) DEFAULT NULL COMMENT '图片',
  `category_id` int UNSIGNED DEFAULT '0',
  `topic_id` int UNSIGNED DEFAULT '0' COMMENT '话题',
  `uid` int UNSIGNED DEFAULT '0' COMMENT '用户',
  `count_start` int UNSIGNED DEFAULT '0' COMMENT '点赞数',
  `count_reply` int UNSIGNED DEFAULT '0' COMMENT '评论数',
  `count_share` int UNSIGNED DEFAULT '0' COMMENT '分享数',
  `status` tinyint DEFAULT '0' COMMENT '审核状态',
  `is_show` tinyint DEFAULT '0' COMMENT '显示状态',
  `start` tinyint(1) DEFAULT '1' COMMENT '星级排序',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_del` tinyint(1) DEFAULT '0',
  `content` varchar(1000) DEFAULT NULL,
  `refusal` varchar(255) DEFAULT NULL COMMENT '拒绝理由',
  `is_hot` tinyint DEFAULT '0' COMMENT '是否推荐',
  `order_id` int UNSIGNED DEFAULT '0' COMMENT '关联订单ID',
  `is_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 图文 2 视频',
  `video_link` varchar(255) DEFAULT NULL COMMENT '视频链接',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `status_time` timestamp NULL DEFAULT NULL COMMENT '审核时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='社区图文表信息';

--
-- 转储表的索引
--

--
-- 表的索引 `gaga_community`
--
ALTER TABLE `gaga_community`
  ADD PRIMARY KEY (`community_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gaga_community`
--
ALTER TABLE `gaga_community`
  MODIFY `community_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
