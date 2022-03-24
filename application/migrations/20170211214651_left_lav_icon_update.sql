-- left_lav_icon_update --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";

UPDATE phppos_modules SET icon = CONCAT('icon ti-',icon);

UPDATE phppos_modules SET icon = "glyphicon glyphicon-tags" WHERE name_lang_key = "module_price_rules";