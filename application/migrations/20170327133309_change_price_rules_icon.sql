-- change_price_rules_icon --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";

UPDATE phppos_modules SET icon = "ion-ios-pricetags-outline" WHERE name_lang_key = "module_price_rules";