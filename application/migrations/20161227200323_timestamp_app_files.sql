-- timestamp_app_files --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_app_files` ADD `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;