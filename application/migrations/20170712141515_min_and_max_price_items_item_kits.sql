-- min_and_max_price_items_item_kits --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_items` ADD `max_edit_price` decimal(23,10) NULL DEFAULT NULL,ADD `min_edit_price` decimal(23,10) NULL DEFAULT NULL;

ALTER TABLE `phppos_item_kits` ADD `max_edit_price` decimal(23,10) NULL DEFAULT NULL,ADD `min_edit_price` decimal(23,10) NULL DEFAULT NULL;