SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE  `phppos_customers` ADD  `taxable` INT( 1 ) NOT NULL DEFAULT  '1';