SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_suppliers` ADD `company_name` VARCHAR( 255 ) NOT NULL AFTER `person_id` ;