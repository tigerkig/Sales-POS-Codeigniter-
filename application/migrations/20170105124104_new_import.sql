-- new_import --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_app_files` ADD `expires` TIMESTAMP NULL DEFAULT NULL AFTER `timestamp`;