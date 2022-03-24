-- ebt_non_integrated_flag --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
ALTER TABLE phppos_locations ADD ebt_integrated int(1) NOT NULL DEFAULT 0;
UPDATE phppos_locations SET ebt_integrated = 1;