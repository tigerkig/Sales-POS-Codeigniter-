SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE  `phppos_customers` ADD  `company_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER  `account_number`;