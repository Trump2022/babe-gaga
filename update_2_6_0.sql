-- eb_system_config
INSERT INTO `eb_system_config` (`id`, `is_store`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(NULL, '0', 'reminder_deadline_second_card_time', 'text', 'number', 27, '', 0, '', 100, 0, '1', '次卡临期提醒', '次卡商品未核销临期提醒，单位（小时）', 0, 1);

-- 发货信息管理
INSERT INTO `eb_system_config` (`id`, `is_store`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(null, '0', 'order_shipping_open', 'radio', 'input', 7, '1=>开启\n0=>关闭', 1, '', 0, 0, '0', '小程序发货信息管理服务', '小程序有订单发货管理时，请打开此开关，否则会导致订单资金冻结', 0, 1);

-- 供应商财务设置
INSERT INTO `eb_system_config` (`id`, `is_store`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(NULL, 0, 'supplier_extract_min_price', 'text', 'input', 82, '', 1, '', 100, 0, '\"\"', '最低提现金额', '最低提现金额', 0, 1),
(NULL, 0, 'supplier_extract_max_price', 'text', 'input', 82, '', 1, '', 100, 0, '\"\"', '最高提现金额', '最高提现金额', 0, 1);

-- 小票打印设置
INSERT INTO `eb_system_config` (`id`, `is_store`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(NULL, 0, 'print_type', 'radio', 'input', 21, '1=>易联云\n2=>飞鹅云', 1, '', 0, 0, '\"1\"', '平台选择', '打印平台选择', 0, 1),
(NULL, 0, 'fey_user', 'text', 'input', 21, '', 1, '', 0, 0, '\"\"', '飞鹅云USER', '飞鹅云后台注册账号', 10, 1),
(NULL, 0, 'fey_ukey', 'text', 'input', 21, '', 1, '', 0, 0, '\"\"', '飞鹅云UYEK', '飞鹅云后台注册账号后生成的UKEY 【备注：这不是填打印机的KEY】', 7, 1),
(NULL, 0, 'fey_sn', 'text', 'input', 21, '', 1, '', 100, 0, '\"\"', '飞鹅云SN', '打印机标签上的编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API', 0, 1);

-- 小票打印门店设置
INSERT INTO `eb_system_config` (`id`, `is_store`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(NULL, 1, 'store_print_type', 'radio', 'input', 79, '1=>易联云\n2=>飞鹅云', 1, '', 0, 0, '\"1\"', '平台选择', '打印平台选择', 0, 1),
(NULL, 1, 'store_fey_user', 'text', 'input', 79, '', 1, '', 0, 0, '\"\"', '飞鹅云USER', '飞鹅云后台注册账号', 10, 1),
(NULL, 1, 'store_fey_ukey', 'text', 'input', 79, '', 1, '', 0, 0, '\"\"', '飞鹅云UYEK', '飞鹅云后台注册账号后生成的UKEY 【备注：这不是填打印机的KEY】', 7, 1),
(NULL, 1, 'store_fey_sn', 'text', 'input', 79, '', 1, '', 100, 0, '\"\"', '飞鹅云SN', '打印机标签上的编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API', 0, 1);

-- 积分抵扣上限类型
INSERT INTO `eb_system_config` (`id`, `is_store`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(NULL, 0, 'integral_max_type', 'radio', 'input', 11, '1=>最多抵扣积分\n2=>最高抵扣比例', 0, '', 100, 0, '\"1\"', '积分抵扣上限', '', 9, 1),
(NULL, 0, 'integral_max_rate', 'text', 'input', 11, '', 0, '', 100, 0, '\"0\"', '最高抵扣比例', '一笔订单最高可抵扣订单实付金额的百分比(0-100%),0不限制', 9, 1);

-- 用户协议配置
INSERT INTO `eb_system_config` (`id`, `is_store`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(NULL, 0, 'store_user_agreement', 'radio', 'input', 86, '1=>手动同意\n0=>自动同意', 0, '', 100, 0, '\"1\"', '用户协议', '授权登录用户协议1：需要用户手动同意0：表示默认自动同意', 9, 1),
(null, 0, 'routine_auth_type', 'checkbox', 'input', 86, '1=>微信授权\n2=>手机号登录', 1, '', 0, 0, '[\"1\",\"2\"]', '手机号获取方式', '小程序授权登录获取手机号的方式，微信授权和手机号验证码', 90, 1);

-- 修改存储配置
UPDATE `eb_system_config` SET `parameter` = '1=>本地存储\n2=>七牛云存储\n3=>阿里云OSS\n4=>腾讯COS\n5=>京东云存储\n6=>华为云存储\n7=>天翼云存储' WHERE `menu_name` = 'upload_type';
-- 存储配置
INSERT INTO `eb_system_config` (`id`, `is_store`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(NULL, 0, 'jd_accessKey', 'text', 'input', 17, '', 1, '', 100, 0, '\"\"', '京东云accessKey', '京东云accessKey', 0, 1),
(NULL, 0, 'jd_secretKey', 'text', 'input', 17, '', 1, '', 100, 0, '\"\"', '京东云secretKey', '京东云secretKey', 0, 1),
(NULL, 0, 'jd_storage_region', 'text', 'input', 17, '', 0, '', 100, 0, '\"\"', '京东云所属地域', '京东云所属地域', 0, 1),
(NULL, 0, 'hw_accessKey', 'text', 'input', 17, '', 1, '', 100, 0, '\"\"', '华为云accessKey', '华为云accessKey', 0, 1),
(NULL, 0, 'hw_secretKey', 'text', 'input', 17, '', 1, '', 100, 0, '\"\"', '华为云secretKey', '华为云secretKey', 0, 1),
(NULL, 0, 'ty_secretKey', 'text', 'input', 17, '', 1, '', 100, 0, '\"\"', '天翼云secretKey', '天翼云secretKey', 0, 1),
(NULL, 0, 'ty_accessKey', 'text', 'input', 17, '', 1, '', 100, 0, '\"\"', '天翼云accessKey', '天翼云accessKey', 0, 1);




INSERT INTO `eb_system_timer` (`name`, `mark`, `type`, `title`, `is_open`, `cycle`, `last_execution_time`, `update_execution_time`, `is_del`, `add_time`) VALUES
('次卡商品未核销短信提醒', 'reminder_unverified_remind', 1, '', 1, '30', 0, 0, 0, 1687242639);

-- 次卡提醒
INSERT INTO `eb_system_notification` (`id`, `mark`, `name`, `title`, `is_system`, `is_app`, `is_wechat`, `is_routine`, `is_sms`, `is_ent_wechat`, `system_title`, `system_text`, `app_id`, `wechat_id`, `routine_id`, `sms_id`, `ent_wechat_text`, `variable`, `url`, `type`, `add_time`) VALUES
(NULL, 'reminder_verification_status', '核销成功提醒', '购买虚拟商品给用户发送提醒', 1, 0, 0, 0, 1, 0, '核销成功提醒', '尊敬的顾客，您已成功核销商品： 商品名称：{store_name}; 核销日期：{write_time} 感谢您的支持！如有任何疑问，请联系我们的客服', 0, '0', '0', 947454, '', '', '', 1, 0),
(NULL, 'expiration_reminder', '过期提醒', '购买虚拟商品给用户发送提醒', 1, 0, 0, 0, 1, 0, '过期提醒', '尊敬的顾客，您购买的商品{store_name} 已过期，过期日期：{end_time} 感谢您的支持！如有任何疑问，请联系我们的客服。', 0, '0', '0', 947455, '', '', '', 1, 0),
(NULL, 'reminder_brink_death', '临期提醒', '账号异地登录给用户发送提醒', 1, 0, 0, 0, 1, 0, '过期提醒', '尊敬的顾客，您购买的商品{store_name} 购买日期：{pay_time} 有效期至：{end_time} 请您尽快使用，以免商品过期。如有任何疑问，请联系我们的客服。感谢您的支持！', 0, '0', '0', 947456, '', '', '', 1, 0),
(NULL, 'supplier_verify_success', '入驻审核通过通知', '供应商入驻审核通过提醒', 1, 0, 0, 0, 1, 0, '审核通过提醒', '您好，您{date}提交的{supplier}供应商入驻申请已通过，供应商账号：{phone}, 密码：{pwd}，请前往{site_name}商城查看。', 0, '0', '0', 947456, '', '', '', 1, 0),
(NULL, 'supplier_verify_fail', '入驻审核未通过通知', '供应商入驻未审核通过提醒', 1, 0, 0, 0, 1, 0, '审核未通过提醒', '您好，您{date}提交的{supplier}供应商入驻申请审核未通过，请前往{site_name}商城—供应商入驻页面查看。', 0, '0', '0', 947456, '', '', '', 1, 0);



-- 积分订单表
ALTER TABLE `eb_store_integral_order` ADD `trade_no` varchar(100) NOT NULL DEFAULT '' COMMENT '支付订单号' AFTER order_id;

-- 商品
ALTER TABLE `eb_store_product` ADD `video_open` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '商品视频是否开启' AFTER `soure_link`;
ALTER TABLE `eb_store_product`  ADD `show_type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '展示：0:所有端1:移动端2:收银台'  AFTER `stock`;
-- UPDATE `eb_store_product` set `type` = 0, `relation_id` =0 where `type` = 2;

-- 商品规格增加结算价
ALTER TABLE `eb_store_product_attr_value` ADD `settle_price` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算价' AFTER `price`;

-- 商品规格加字段
ALTER TABLE `eb_store_product_rule` ADD `type` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '商品所属：0：平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_store_product_rule` ADD `relation_id` INT(10) NOT NULL DEFAULT '0' COMMENT '关联门店、供应商ID'  AFTER `type`;

-- 运费模版加字段
ALTER TABLE `eb_shipping_templates` CHANGE `type` `group` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '计费方式1按件数,2按重量, 3按体积';
ALTER TABLE `eb_shipping_templates_free` CHANGE `type` `group` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '计费方式1按件数,2按重量, 3按体积';
ALTER TABLE `eb_shipping_templates_region` CHANGE `type` `group` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '计费方式1按件数,2按重量, 3按体积';
ALTER TABLE `eb_shipping_templates` ADD `type` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '商品所属：0：平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_shipping_templates` ADD `relation_id` INT(10) NOT NULL DEFAULT '0' COMMENT '关联门店、供应商ID'  AFTER `type`;

-- 商品评价表修改
ALTER TABLE `eb_store_product_reply` CHANGE `type` `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `oid`;
ALTER TABLE `eb_store_product_reply` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_store_product_reply` set `type` = 1 where `relation_id` > 0;

-- 商品评价回复表修改
ALTER TABLE `eb_store_product_reply_comment` ADD `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_store_product_reply_comment` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_store_product_reply_comment` set `type` = 1 where `relation_id` > 0;

-- 商品保障表
ALTER TABLE `eb_store_product_ensure` CHANGE `type` `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_store_product_ensure` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_store_product_ensure` set `type` = 0 where `type` = 1;

-- 商品参数表
ALTER TABLE `eb_store_product_specs` CHANGE `type` `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_store_product_specs` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_store_product_specs` set `type` = 0 where `type` = 1;

-- 商品单位表
ALTER TABLE `eb_store_product_unit` ADD `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_store_product_unit` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;

-- 角色表
ALTER TABLE `eb_system_role` CHANGE `type` `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_system_role` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_system_role` set `type` = 0 where `type` = 1;
UPDATE  `eb_system_role` set `type` = 1 where `type` = 2;

-- 标签分类表
ALTER TABLE `eb_category` CHANGE `type` `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_category` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_category` set `type` = 0 where `type` = 1;
UPDATE  `eb_category` set `type` = 1 where `type` = 2;

-- 商品关联标签表
ALTER TABLE `eb_store_product_label` CHANGE `type` `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_store_product_label` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_store_product_label` set `type` = 0 where `type` = 1;
UPDATE  `eb_store_product_label` set `type` = 1 where `type` = 2;

-- 用户标签表
ALTER TABLE `eb_user_label` CHANGE `type` `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_user_label` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_user_label` set `type` = 0 where `type` = 1;
UPDATE  `eb_user_label` set `type` = 1 where `type` = 2;

-- 用户关联标签表
ALTER TABLE `eb_user_label_relation` add `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：0平台1:门店2:供应商' AFTER `uid`;
ALTER TABLE `eb_user_label_relation` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_user_label_relation` set `type` = 1 where `relation_id` > 0;

-- 配送员表
ALTER TABLE `eb_delivery_service` CHANGE `type` `type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '类型：0平台1:门店2:供应商' AFTER `uid`;
ALTER TABLE `eb_delivery_service` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_delivery_service` set `type` = 0 where `type` = 1;
UPDATE  `eb_delivery_service` set `type` = 1 where `type` = 2;

-- 门店配置表
ALTER TABLE `eb_store_config` add `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '类型：1:门店2:供应商' AFTER `id`;
ALTER TABLE `eb_store_config` CHANGE `store_id` `relation_id` INT(11) NOT NULL DEFAULT '0' COMMENT '门店、供应商id' AFTER `type`;
UPDATE  `eb_store_config` set `type` = 1 where `relation_id` > 0;

-- 删除唯一索引
ALTER TABLE `eb_store_config` DROP INDEX `key`;
ALTER TABLE `eb_store_config` ADD INDEX `type` (`type`, `relation_id`);

-- 增加优惠券适用门店
ALTER TABLE `eb_store_coupon_issue` ADD `applicable_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '适用门店：0：仅平台1：所有2：部分';
ALTER TABLE `eb_store_coupon_issue` ADD `applicable_store_id` LONGTEXT NULL DEFAULT NULL COMMENT '适用门店ids' AFTER `applicable_type`;

-- ALTER TABLE `eb_store_coupon_user` ADD `applicable_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '适用门店：0：仅平台1：所有2：部分';
-- ALTER TABLE `eb_store_coupon_user` ADD `applicable_store_id` LONGTEXT NULL DEFAULT NULL COMMENT '适用门店ids' AFTER `applicable_type`;

-- 抽奖记录保存奖品信息
ALTER TABLE `eb_luck_lottery_record` ADD `prize_info` TEXT NULL DEFAULT NULL COMMENT '奖品信息' AFTER `type`;

-- 商城链接
INSERT INTO `eb_page_category` (`id`, `pid`, `type`, `name`, `sort`, `status`, `add_time`) VALUES
(null, 1, 'store_home', '门店主页', 96, 1, 1626831994);

-- 供应商入驻链接
INSERT INTO `eb_page_link` (`id`, `cate_id`, `type`, `name`, `url`, `param`, `example`, `status`, `sort`, `add_time`) VALUES
(null, 5, 1, '供应商入驻', '/pages/users/supplier/index', ' ', '/pages/users/supplier/index', 1, 0, 1626837579);


-- 用户申请门店、供应商
CREATE TABLE `eb_system_user_apply` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
 `type` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '申请类型1:门店2:供应商',
 `relation_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联商户',
 `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
 `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
 `system_name` varchar(30) NOT NULL DEFAULT '' COMMENT '商户名称',
 `name` varchar(30) NOT NULL DEFAULT '' COMMENT '联系人名称',
 `images` varchar(2000) NOT NULL DEFAULT '' COMMENT '多图',
 `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
 `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '处理状态0未处理，1通过 ，2未通过',
 `fail_msg` varchar(255) NOT NULL DEFAULT '' COMMENT '未通过原因',
 `is_del` tinyint(2) NOT NULL DEFAULT '0' COMMENT '删除状态 1删除 ，0未删除',
 `status_time` int(10) NOT NULL DEFAULT '0' COMMENT '审核时间',
 `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
 PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户申请表';

-- 供应商流水
-- 表的结构 `eb_supplier_flowing_water`
--
CREATE TABLE `eb_supplier_flowing_water` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
 `supplier_id` int(10) NOT NULL DEFAULT '0' COMMENT '供应商ID',
 `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
 `order_id` varchar(20) NOT NULL DEFAULT '' COMMENT '交易单号',
 `link_id` varchar(50) NOT NULL DEFAULT '' COMMENT '关联订单',
 `pm` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = 支出 1 = 获得',
 `number` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '明细',
 `type` varchar(50) NOT NULL DEFAULT '' COMMENT '交易类型(1:支付订单,2:退款订单)',
 `pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式',
 `pay_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付金额',
 `pay_postage` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付邮费',
 `total_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
 `remark` varchar(512) NOT NULL DEFAULT '' COMMENT '平台备注',
 `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商备注',
 `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
 `trade_time` int(11) NOT NULL DEFAULT '0' COMMENT '交易时间',
 `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供应商流水表';

-- 供应商交易
-- 表的结构 `eb_supplier_transactions`
--
CREATE TABLE `eb_supplier_transactions` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
 `supplier_id` int(10) NOT NULL DEFAULT '0' COMMENT '供应商ID',
 `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
 `order_id` varchar(20) NOT NULL DEFAULT '' COMMENT '交易单号',
 `link_id` varchar(50) NOT NULL DEFAULT '' COMMENT '关联订单',
 `pm` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = 支出 1 = 获得',
 `type` varchar(50) NOT NULL DEFAULT '' COMMENT '交易类型(1:支付订单,2:退款订单)',
 `pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式',
 `pay_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付金额',
 `pay_postage` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付邮费',
 `total_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
 `remark` varchar(512) NOT NULL DEFAULT '' COMMENT '平台备注',
 `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商备注',
 `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
 `trade_time` int(11) NOT NULL DEFAULT '0' COMMENT '交易时间',
 `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供应商交易表';

--
-- 表的结构 `eb_supplier_extract`
--
CREATE TABLE `eb_supplier_extract` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
`supplier_id` int(10) NOT NULL DEFAULT '0' COMMENT '供应商ID',
`extract_type` varchar(32) NOT NULL DEFAULT 'bank' COMMENT 'bank = 银行卡 alipay = 支付宝wx=微信',
`bank_code` varchar(32) NOT NULL DEFAULT '0' COMMENT '银行卡',
`bank_address` varchar(256) NOT NULL DEFAULT '' COMMENT '开户地址',
`alipay_account` varchar(64) NOT NULL DEFAULT '' COMMENT '支付宝账号',
`wechat` varchar(15) NOT NULL DEFAULT '' COMMENT '微信号',
`qrcode_url` varchar(255) NOT NULL DEFAULT '' COMMENT '二维码地址',
`extract_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '提现金额',
`mark` varchar(512) NOT NULL DEFAULT '',
`balance` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00',
`status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '-1 未通过 0 审核中 1 已提现',
`pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 未转账 1 已转账',
`supplier_mark` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商备注',
`admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
`fail_msg` varchar(128) NOT NULL DEFAULT '' COMMENT '无效原因',
`fail_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
`voucher_image` varchar(256) NOT NULL DEFAULT '' COMMENT '转账凭证',
`voucher_title` varchar(256) NOT NULL DEFAULT '' COMMENT '转账说明',
`add_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
PRIMARY KEY (`id`),
KEY `supplier_id` (`supplier_id`),
KEY `extract_type` (`extract_type`),
KEY `status` (`status`),
KEY `add_time` (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供应商提现表';

--
-- 修改表的结构 `eb_system_supplier`
--
ALTER TABLE `eb_system_supplier` ADD COLUMN  `bank_code` varchar(32) NOT NULL DEFAULT '0' COMMENT '银行卡' AFTER `detailed_address`;
ALTER TABLE `eb_system_supplier` ADD COLUMN  `bank_address` varchar(256) NOT NULL DEFAULT '' COMMENT '开户地址' AFTER `bank_code`;
ALTER TABLE `eb_system_supplier` ADD COLUMN  `alipay_account` varchar(64) NOT NULL DEFAULT '' COMMENT '支付宝账号' AFTER `bank_address`;
ALTER TABLE `eb_system_supplier` ADD COLUMN  `alipay_qrcode_url` varchar(255) NOT NULL DEFAULT '' COMMENT '支付宝二维码地址' AFTER `alipay_account`;
ALTER TABLE `eb_system_supplier` ADD COLUMN  `wechat` varchar(15) NOT NULL DEFAULT '' COMMENT '微信号' AFTER `alipay_qrcode_url`;
ALTER TABLE `eb_system_supplier` ADD COLUMN  `wechat_qrcode_url` varchar(255) NOT NULL DEFAULT '' COMMENT '二维码地址' AFTER `wechat`;

--
-- 修改表的结构 `eb_system_store`
--
ALTER TABLE `eb_system_store` ADD COLUMN  `default_delivery` tinyint(1) NOT NULL DEFAULT '1' COMMENT '默认设置门店配送方式 1=配送  2=自提' AFTER `is_store`;

--
-- 修改表的结构 `eb_system_attachment`
--
ALTER TABLE `eb_system_attachment` ADD COLUMN  `scan_token` varchar(32) NOT NULL DEFAULT '' COMMENT '扫码上传的token';

--
-- 修改表的结构 `eb_store_order_cart_info`
--
ALTER TABLE `eb_store_order_cart_info` ADD COLUMN  `is_advent_sms` tinyint(1) NOT NULL DEFAULT '0' COMMENT '临期提醒是否发送' AFTER `write_end`;
ALTER TABLE `eb_store_order_cart_info` ADD COLUMN  `is_expire_sms` tinyint(1) NOT NULL DEFAULT '0' COMMENT '过期提醒是否发送' AFTER `is_advent_sms`;

--
-- 修改表的结构 `eb_system_admin`
--
ALTER TABLE `eb_system_admin` ADD COLUMN  `is_way` tinyint(1) NOT NULL DEFAULT '0' COMMENT '图片上传方式 0=本地上传 1=网络图片 2=扫码上传' AFTER `status`;

--
-- 修改表的结构 `eb_system_store_staff`
--
ALTER TABLE `eb_system_store_staff` ADD COLUMN  `is_way` tinyint(1) NOT NULL DEFAULT '0' COMMENT '图片上传方式 0=本地上传 1=网络图片 2=扫码上传' AFTER `notify`;

--
-- 转存表中的数据 `eb_system_notification`
--
UPDATE  `eb_system_notification` set `is_wechat` = 0 where `mark` = 'bind_spread_uid';
UPDATE  `eb_system_notification` set `is_wechat` = 0 where `mark` = 'price_revision';
UPDATE  `eb_system_notification` set `is_wechat` = 0 where `mark` = 'order_pay_false';
UPDATE  `eb_system_notification` set `is_wechat` = 0, `is_sms` = 1 where `mark` = 'integral_accout';
UPDATE  `eb_system_notification` set `is_wechat` = 0, `is_sms` = 1 where `mark` = 'order_brokerage';
UPDATE  `eb_system_notification` set `is_wechat` = 0, `is_sms` = 1 where `mark` = 'bargain_success';
UPDATE  `eb_system_notification` set `is_wechat` = 0, `is_sms` = 1 where `mark` = 'order_user_groups_success';
UPDATE  `eb_system_notification` set `is_wechat` = 0, `is_sms` = 1 where `mark` = 'send_order_pink_fial';
UPDATE  `eb_system_notification` set `is_wechat` = 0, `is_sms` = 1 where `mark` = 'open_pink_success';
UPDATE  `eb_system_notification` set `is_wechat` = 0, `is_sms` = 1 where `mark` = 'user_balance_change';
UPDATE  `eb_system_notification` set `is_wechat` = 0, `is_sms` = 1 where `mark` = 'send_order_pink_clone';
UPDATE  `eb_system_notification` set `is_wechat` = 0, `is_sms` = 1 where `mark` = 'can_pink_success';
UPDATE  `eb_system_notification` set `is_wechat` = 0 where `mark` = 'admin_pay_success_code';
UPDATE  `eb_system_notification` set `is_wechat` = 0 where `mark` = 'send_order_apply_refund';
UPDATE  `eb_system_notification` set `is_wechat` = 0 where `mark` = 'kami_deliver_goods_code';
UPDATE  `eb_system_notification` set `is_wechat` = 0 where `mark` = 'order_deliver_success';

UPDATE  `eb_system_notification` set `is_sms` = 1 where `mark` = 'order_refund';
UPDATE  `eb_system_notification` set `is_sms` = 1 where `mark` = 'user_extract';
UPDATE  `eb_system_notification` set `is_sms` = 1 where `mark` = 'send_order_refund_no_status';

UPDATE  `eb_system_notification` set `name` = '卡密商品发货通知',`title` = '购买卡密商品给用户发送提醒' where `mark` = 'kami_deliver_goods_code';
--
-- 转存表中的数据 `eb_template_message`
--
DELETE FROM `eb_template_message` WHERE `tempkey` = 'OPENTM409880752';
DELETE FROM `eb_template_message` WHERE `tempkey` = 'OPENTM401202515';
DELETE FROM `eb_template_message` WHERE `tempkey` = 'OPENTM408199008';

--
-- 转存表中的数据 `eb_template_message`
--
INSERT INTO `eb_template_message` (`id`, `notification_id`, `type`, `tempkey`, `name`, `kid`, `content`, `example`, `tempid`, `add_time`, `status`) VALUES (NULL, '17', '1', '46232', '退货退款申请结果通知', '', '退款金额{{amount5.DATA}}\r\n订单编号{{character_string8.DATA}}\r\n处理结果{{thing6.DATA}}\r\n结果通知时间{{time9.DATA}}', '', '', '1515053049', '1');

UPDATE  `eb_template_message` set `tempkey` = 43216,`name` = '订单支付成功通知	', `content` = '订单号{{character_string2.DATA}}\r\n商品名称{{thing3.DATA}}\r\n支付金额{{amount5.DATA}}\r\n会员名称{{phrase18.DATA}}\r\n下单时间{{time4.DATA}}' where `tempkey` = 'OPENTM418062102';
UPDATE  `eb_template_message` set `tempkey` = 50439,`name` = '确认收货通知',`content` = '订单编号{{character_string7.DATA}}\r\n商品名称{{thing6.DATA}}\r\n收货时间{{time4.DATA}}' where `tempkey` = 'OPENTM418528119';
UPDATE  `eb_template_message` set `tempkey` = 48058,`name` = '退款成功通知',`notification_id` = '5', `content` = '退款金额{{amount2.DATA}}\r\n退款时间{{time4.DATA}}\r\n订单编号{{character_string5.DATA}}' where `tempkey` = 'OPENTM207284059';
UPDATE  `eb_template_message` set `tempkey` = 42934,`name` = '充值成功提醒',`content` = '充值时间{{time11.DATA}}\r\n充值金额{{amount5.DATA}}' where `tempkey` = 'OPENTM414089457';
UPDATE  `eb_template_message` set `tempkey` = 51729,`name` = '提现到账通知',`content` = '到账金额{{amount3.DATA}}\r\n提现时间{{time4.DATA}}' where `tempkey` = 'OPENTM405876306';
UPDATE  `eb_template_message` set `tempkey` = 42984,`name` = '订单发货通知',`content` = '订单编号{{character_string2.DATA}}\r\n商品名称{{thing4.DATA}}\r\n快递公司{{thing13.DATA}}\r\n快递单号{{character_string14.DATA}}\r\n发货时间{{time12.DATA}}' where `tempkey` = 'OPENTM415939287';
