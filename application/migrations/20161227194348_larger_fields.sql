-- larger_fields --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_register_log` CHANGE `notes` `notes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `phppos_expenses` CHANGE `expense_note` `expense_note` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;