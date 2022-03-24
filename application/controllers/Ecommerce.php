<?php
class Ecommerce extends MY_Controller 
{
	function __construct()
	{
			parent::__construct();
			if (!is_cli())//Running from web should have store config permissions
			{	
				$this->load->model('Employee');
				$this->load->model('Location');
				if(!$this->Employee->is_logged_in())
				{
					redirect('login?continue='.rawurlencode(uri_string().'?'.$_SERVER['QUERY_STRING']));
				}
		
				if(!$this->Employee->has_module_permission('config',$this->Employee->get_logged_in_employee_info()->person_id))
				{
					redirect('no_access/config');
				}
			}
		}
		
		public function cancel()
		{
			$this->load->model('Appconfig');
			$this->Appconfig->save('kill_ecommerce_cron',1);
			$this->Appconfig->save('ecommerce_cron_running',0);
			$this->Appconfig->save('ecommerce_sync_progress',100);
			$platform=$this->Appconfig->get("ecommerce_platform");
			
			if($platform=="woocommerce")
			{
				$platform_model="woo";
				$this->load->model($platform_model);
				$this->$platform_model->save_log();
			}
		}
		
		function manual_sync()
		{
			$this->cron();
		}
		
		/*
		This function is used to sync the PHPPOS items with online ecommerce store.
		*/
		// $base_url is used NOT used in this function but in application/config/config.php
		//$db_override is NOT used at all; but in database.php to select database based on CLI args for cron in cloud
      public function cron($base_url='', $db_override = '')
      {
				ignore_user_abort(TRUE);
				set_time_limit(0);
				session_write_close();
				
				//Cron's always run on current server path; but if we are between migrations we should run the cron on the previous folder passing along any arguements
				if (defined('SHOULD_BE_ON_OLD') && SHOULD_BE_ON_OLD)
				{
					global $argc, $argv;
					$prev_folder = isset($_SERVER['CI_PREV_FOLDER']) ?  $_SERVER['CI_PREV_FOLDER'] : 'PHP-Point-Of-Sale-Prev';
					system('php '.FCPATH."$prev_folder/index.php ecommerce cron ".$argv[3].$prev_folder.'/ '.$argv[4]);
					exit();
				}
				if (is_on_demo_host())
				{
					echo json_encode(array('success' => FALSE, 'message' => lang('common_disabled_on_demo')));
					die();
				}
				try
				{	
					$this->Appconfig->save('kill_ecommerce_cron',0);
					
					$platform_model="";
					$this->load->model("Appconfig");
					if ($this->Appconfig->get_raw_ecommerce_cron_running())
					{
						echo json_encode(array('success' => FALSE, 'message' => lang('common_ecommerce_running')));
						die();
					}
				
					$this->load->model('Location');
					if ($timezone = ($this->Location->get_info_for_key('timezone',$this->config->item('ecom_store_location') ? $this->config->item('ecom_store_location') : 1)))
					{
						date_default_timezone_set($timezone);
					}

					$this->Appconfig->save('ecommerce_cron_running',1);
					$this->Appconfig->save('ecommerce_sync_progress',0);
					$platform=$this->Appconfig->get("ecommerce_platform");
					if($platform=="woocommerce")
					{
						$platform_model="woo";
					}
					if( $platform_model != "" )
					{
						$ecommerce_cron_sync_operations = unserialize($this->config->item('ecommerce_cron_sync_operations'));
						
						$this->load->model($platform_model);
						$this->lang->load('config');
						$valid = array("import_ecommerce_tags_into_phppos", "import_ecommerce_categories_into_phppos", "sync_phppos_item_changes", "import_ecommerce_items_into_phppos", "sync_inventory_changes", "export_phppos_tags_to_ecommerce", "export_phppos_categories_to_ecommerce", "export_phppos_items_to_ecommerce");
						
						$numsteps = count($ecommerce_cron_sync_operations);
						$stepsCompleted = 0;
						foreach($ecommerce_cron_sync_operations as $operation)
						{
							if (is_cli())
							{
								echo "START $operation\n";
							}
							
							if(in_array($operation, $valid))
							{
								$percent = floor(($stepsCompleted/$numsteps)*100);
								$message = lang("config_".$operation);
								$this->$platform_model->update_sync_progress($percent, $message);
								
								$this->$platform_model->$operation();
								$stepsCompleted ++;
							}
							
							if (is_cli())
							{
								echo "DONE $operation\n";
							}
						}
						
						$percent = floor(($stepsCompleted/$numsteps)*100);
						$message = lang("config_".$operation);
						$this->$platform_model->update_sync_progress($percent, $message);
						
						$this->load->model('Appconfig');
						$sync_date = date('Y-m-d H:i:s');
						$this->Appconfig->save('last_ecommerce_sync_date', $sync_date);
						if (is_cli())
						{
							echo "\n\n***************************DONE***********************\n";
						}
						
						$this->$platform_model->save_log();
						echo json_encode(array('success' => TRUE, 'date' =>$sync_date));
					}
		
					$this->Appconfig->save('ecommerce_sync_progress',100);
					$this->Appconfig->save('ecommerce_cron_running',0);
	      }
				catch(Exception $e)
				{
					if (is_cli())
					{
						echo "*******EXCEPTION: ".var_export($e->getMessage(),TRUE);
					}
					$this->Appconfig->save('ecommerce_cron_running',0);				
				}
			}
		}
?>