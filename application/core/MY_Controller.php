<?php
function force_http_if_needed()
{
	if(!is_cli())
	{
		if (is_https())
		{
		    $CI =& get_instance();
				
				if ($CI->db->table_exists('app_config'))
				{	
						$CI->load->model('Appconfig');
					//If we have setup credit card processing enabled
					if (!$CI->Appconfig->get_do_not_force_http() && ($CI->Location->get_info_for_key('enable_credit_card_processing') || $CI->uri->segment(1) == 'locations'))
					{	
						//EMV
						if ($CI->uri->segment(1) == 'locations' || ($CI->Location->get_info_for_key('emv_merchant_id') && $CI->Location->get_info_for_key('com_port') && $CI->Location->get_info_for_key('listener_port')))
						{
							$full_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
							header('HTTP/1.1 307 Temporary Redirect');
							header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
							header('Pragma: no-cache'); // HTTP 1.0.
							header('Expires: 0'); // Proxies.
							//Redirect to new codebase (temporary)
							header("Location: $full_url",TRUE,307);
							exit();
						}
					}	
				}
		}
	}
}

function force_https_if_needed()
{
	if(!is_cli())
	{	
		if (!is_https())
		{
				$full_url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				header('HTTP/1.1 307 Temporary Redirect');
				header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
				header('Pragma: no-cache'); // HTTP 1.0.
				header('Expires: 0'); // Proxies.
				//Redirect to new codebase (temporary)
				header("Location: $full_url",TRUE,307);
				exit();
		}
	}
}

$lazy_load = (!defined("LAZY_LOAD") or LAZY_LOAD == TRUE);

if (!$lazy_load)
{
	class MY_Controller extends CI_Controller 
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->helper('demo');
	    $CI =& get_instance();
			if ($CI->db->table_exists('app_config'))
			{
				$CI->load->model('Appconfig');	
				if ($CI->Appconfig->get_force_https())
				{
					force_https_if_needed();				
				}
				elseif (!is_on_demo_host())
				{
					force_http_if_needed();
				}
			}
		}
	}
}
else
{
	class MY_Controller extends CI_Controller 
	{	
		//Lazy loading based on http://stackoverflow.com/questions/17579449/model-library-lazy-load-in-codeigniter
		public function __construct()
		{
			foreach (is_loaded() as $var => $class)
			{
			     $this->$var = '';
			}

			$this->load = '';
			parent::__construct();
			$this->load->helper('demo');
	    $CI =& get_instance();
			if ($CI->db->table_exists('app_config'))
			{
				$CI->load->model('Appconfig');	
				if ($CI->Appconfig->get_force_https())
				{
					force_https_if_needed();				
				}
				elseif (!is_on_demo_host())
				{
					force_http_if_needed();
				}
			}
		}
	
	
		// Lazy load models + libraries....If we can't load a model that we have; then we will try to load library $name
		public function __get($name)
		{
			//Cache models so we only scan model dir once

			static $models = FALSE;
			$this->load->helper('file');

			if (!$models)
			{
				$model_files = get_filenames(APPPATH.'models', TRUE);
				foreach($model_files as $model_file)
				{
					$model_relative_name = str_replace('.php','',substr($model_file,strlen(APPPATH.'models'.DIRECTORY_SEPARATOR)));
					$model_folder = strpos($model_relative_name, DIRECTORY_SEPARATOR) !== FALSE ? substr($model_relative_name,0,strrpos($model_relative_name,DIRECTORY_SEPARATOR)) : '';
					$model_name = str_replace($model_folder.DIRECTORY_SEPARATOR, '',$model_relative_name);

					$models[$model_name] = $model_folder.'/'.$model_name;
				}
			}

			if (isset($models[$name]))
			{
				$this->load->model($models[$name]);
				$log_message = "Lazy Loaded model $name CURRENT_URL: ".current_url().' REQUEST '.var_export($_REQUEST, TRUE);
				log_message('error', $log_message);
				return $this->$name;
			}
			else //Try a library if we cannot load a model
			{
				$this->load->library($name);
				$log_message = "Lazy Loaded library $name CURRENT_URL: ".current_url().' REQUEST '.var_export($_REQUEST, TRUE);
				log_message('error', $log_message);
				return $this->$name;
			}
		
			return NULL;
		}
	}
}