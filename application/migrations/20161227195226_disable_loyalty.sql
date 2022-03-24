-- disable_loyalty --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_item_kits` ADD `disable_loyalty` int(1) NOT NULL DEFAULT '0' AFTER `change_cost_price`;