<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Migration_timestamp_app_files extends MY_Migration 
	{

	    public function up() 
			{
				$this->execute_sql(realpath(dirname(__FILE__).'/20161227200323_timestamp_app_files.sql'));
	    }

	    public function down() 
			{
	    }

	}