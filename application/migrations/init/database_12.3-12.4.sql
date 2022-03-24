SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE  `phppos_receivings` ADD  `deleted` INT( 1 ) NOT NULL DEFAULT  '0', ADD INDEX (  `deleted` );