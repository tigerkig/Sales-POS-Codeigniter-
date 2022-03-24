-- employee_custom_fields --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";

ALTER TABLE `phppos_employees` 
ADD `custom_field_1_value` VARCHAR(255) NULL DEFAULT NULL, 
ADD INDEX (`custom_field_1_value`),

ADD `custom_field_2_value` VARCHAR(255) NULL DEFAULT NULL, 
ADD INDEX (`custom_field_2_value`),

ADD `custom_field_3_value` VARCHAR(255) NULL DEFAULT NULL, 
ADD INDEX (`custom_field_3_value`),

ADD `custom_field_4_value` VARCHAR(255) NULL DEFAULT NULL, 
ADD INDEX (`custom_field_4_value`),

ADD `custom_field_5_value` VARCHAR(255) NULL DEFAULT NULL, 
ADD INDEX (`custom_field_5_value`);