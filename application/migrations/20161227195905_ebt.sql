-- ebt --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_item_kits` ADD `is_ebt_item` int(1) NOT NULL DEFAULT '0' AFTER `override_default_tax`;