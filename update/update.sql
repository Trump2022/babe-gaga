
ALTER TABLE `gaga_user_brokerage` ADD `amount_received` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '到账金额' AFTER `number`;
ALTER TABLE `gaga_user_extract` ADD `number` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '提现的积分/gaga币数量' AFTER `alipay_code`;


