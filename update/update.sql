
ALTER TABLE `gaga_user_brokerage` ADD `amount_received` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '到账金额' AFTER `number`;
