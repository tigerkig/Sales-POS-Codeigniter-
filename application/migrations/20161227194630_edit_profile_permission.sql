-- edit_profile_permission --
SET SESSION sql_mode="NO_AUTO_CREATE_USER";
INSERT INTO `phppos_modules_actions` (`action_id`, `module_id`, `action_name_key`, `sort`) VALUES ('edit_profile', 'employees', 'common_edit_profile', 155);

INSERT INTO phppos_permissions_actions (module_id, person_id, action_id)
SELECT DISTINCT phppos_permissions.module_id, phppos_permissions.person_id, action_id
from phppos_permissions
inner join phppos_modules_actions on phppos_permissions.module_id = phppos_modules_actions.module_id
WHERE phppos_permissions.module_id = 'employees' and
action_id = 'edit_profile'
order by module_id, person_id;