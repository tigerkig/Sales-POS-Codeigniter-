-- limited_discounts_for_items_and_item_kits --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_items` ADD `max_discount_percent` decimal(15,3) NULL DEFAULT NULL;
ALTER TABLE `phppos_item_kits` ADD `max_discount_percent` decimal(15,3) NULL DEFAULT NULL;