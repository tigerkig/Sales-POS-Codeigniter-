-- system_items_hiding --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER table phppos_items ADD `system_item` int(1) NOT NULL DEFAULT '0', ADD INDEX `deleted_system_item` (`deleted`,`system_item`);