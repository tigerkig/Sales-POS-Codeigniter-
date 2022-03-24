-- sessions_table_change --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE phppos_sessions CHANGE id id varchar(128) NOT NULL;