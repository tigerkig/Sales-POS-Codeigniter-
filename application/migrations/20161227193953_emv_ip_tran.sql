-- emv_ip_tran --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_registers` ADD `iptran_device_id` VARCHAR(255) NULL DEFAULT NULL AFTER `name`;
ALTER TABLE `phppos_registers` ADD `emv_terminal_id` VARCHAR(255) NULL DEFAULT NULL AFTER `iptran_device_id`;