SET SESSION sql_mode="NO_AUTO_CREATE_USER";
CREATE INDEX last_activity_idx ON phppos_sessions(last_activity); 
ALTER TABLE phppos_sessions MODIFY user_agent VARCHAR(120) NOT NULL;