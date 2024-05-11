
ALTER TABLE `gaga_user_brokerage` ADD `amount_received` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '到账金额' AFTER `number`;
ALTER TABLE `gaga_user_extract` ADD `number` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '提现的积分/gaga币数量' AFTER `alipay_code`;
ALTER TABLE `gaga_store_order` ADD `one_brokerage_gaga` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '一级返佣gaga币数量' AFTER `one_brokerage`;
ALTER TABLE `gaga_store_order` ADD `two_brokerage_gaga` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '二级返佣gaga币数量' AFTER `two_brokerage`;


