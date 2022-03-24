-- larger_invoice_item_description --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";

ALTER TABLE `phppos_sales_items` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;