<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_employee_security_password extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/20161227195028_employee_security_password.sql'));
	    }

	    public function down() 
			{
	    }

	}