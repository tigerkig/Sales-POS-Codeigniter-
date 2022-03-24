-- employee_security_password --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE `phppos_employees` ADD `always_require_password` int(1) NOT NULL DEFAULT '0' AFTER `force_password_change`;