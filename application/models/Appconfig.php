<?php
class Appconfig extends CI_Model 
{
	
	function exists($key)
	{
		$this->db->from('app_config');	
		$this->db->where('app_config.key',$key);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function get_all()
	{
		$this->db->from('app_config');
		$this->db->order_by("key", "asc");
		return $this->db->get();		
	}
	
	function get($key)
	{
		return $this->config->item($key);
	}
	
	function save($key,$value)
	{
		$config_data=array(
		'key'=>$key,
		'value'=>$value
		);
		return $this->db->replace('app_config', $config_data);
	}
	
	function get_raw_kill_ecommerce_cron()
	{
		$this->db->from('app_config');
		$this->db->where("key", "kill_ecommerce_cron");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			return $row['value'];
		}
		return 0;	
	}
	
	function get_raw_ecommerce_cron_running()
	{
		$this->db->from('app_config');
		$this->db->where("key", "ecommerce_cron_running");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			return $row['value'];
		}
		return 0;	
	}
	
	function get_raw_number_of_decimals()
	{
		$this->db->from('app_config');
		$this->db->where("key", "number_of_decimals");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			return $row['value'];
		}
		return 2;	
	}
	
	function get_raw_language_value()
	{
		$this->db->from('app_config');
		$this->db->where("key", "language");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			return $row['value'];
		}
		return '';	
	}

	function get_raw_version_value()
	{
		$this->db->from('app_config');
		$this->db->where("key", "version");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			return $row['value'];
		}
		return '';	
	}
	
	function get_do_not_force_http()
	{
		$this->db->from('app_config');
		$this->db->where("key", "do_not_force_http");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			return $row['value'];
		}
		return '';
	}
	
	function get_force_https()
	{
		if ($this->db->table_exists('app_config'))
		{
			$this->db->from('app_config');
			$this->db->where("key", "force_https");
			$row = $this->db->get()->row_array();
			if (!empty($row))
			{
				return $row['value'];
			}
			return '';
		}
		
		return '';
	}
	
	
	
	function get_raw_phppos_session_expiration()
	{
		$this->db->from('app_config');
		$this->db->where("key", "phppos_session_expiration");
		$row = $this->db->get()->row_array();
		if (!empty($row))
		{
			if (is_numeric($row['value']))
			{
				return (int)$row['value'];
			}
			
		}
		return NULL;	
	}
	
	function batch_save($data)
	{
		if (isset($data['default_tax_1_name']))
		{
			//Check for duplicate taxes
			for($k = 1;$k<=5;$k++)
			{
				$current_tax = $data["default_tax_${k}_name"].$data["default_tax_${k}_rate"];
			
				for ($j = 1;$j<=5;$j++)
				{
					$check_tax = $data["default_tax_${j}_name"].$data["default_tax_${j}_rate"];
					if ($j!=$k && $current_tax != '' && $check_tax != '')
					{
						if ($current_tax == $check_tax)
						{
							return FALSE;
						}
					}
				}
			}
		}
		
		$success = true;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		foreach($data as $key=>$value)
		{
			if(!$this->save($key, $value))
			{
				$success=false;
				break;
			}
		}
		
		$this->db->trans_complete();		
		return $success;
		
	}
		
	function get_logo_image()
	{
		if ($this->config->item('company_logo'))
		{
			return app_file_url($this->get('company_logo'));
		}
		return  base_url().'assets/img/header_logo.png';
	}
		
	function get_additional_payment_types()
	{
		$return = array();
		$payment_types = $this->get('additional_payment_types');
		
		if ($payment_types)
		{
			$return = array_map('trim', explode(',',$payment_types));
		}
		
		return $return;
	}
	
	function mark_mercury_activate($mercury_activate_seen = true)
	{
		$this->db->query('REPLACE INTO '.$this->db->dbprefix('app_config').' (`key`, `value`) VALUES ("mercury_activate_seen", "'.($mercury_activate_seen ? 1 : 0).'")');
	}
	
	function set_all_locations_use_global_tax()
	{
		$this->load->model('Location');
		return $this->Location->set_all_locations_use_global_tax();
	}
	
	function all_locations_use_global_tax()
	{
		$this->load->model('Location');
		return $this->Location->all_locations_use_global_tax();
	}
	
	function get_primary_key_next_index($table)
	{
		$tables_to_col = array(
			'items' => 'item_id',
			'item_kits'=> 'item_kit_id',
			'sales' => 'sale_id',
			'receivings' => 'receiving_id',	
		);
		
		if(isset($tables_to_col[$table]))
		{
			$this->db->select("IFNULL(MAX(".$tables_to_col[$table]."),0)+1 as max_id", false);
			$this->db->from($table);
			$max_id = $this->db->get()->row()->max_id;
			
			return $max_id;
		}
		
		return false;
	}
	
	function change_auto_increment($table, $value)
	{	
		if(!is_numeric($value) || intval($value) < 1)
		{
			return false;
		}
		
		$max = intVal($this->get_primary_key_next_index($table));
			
		if(intval($value) < $max)
		{
			$value = $max +1;
		}
			
		$this->db->query('ALTER TABLE '. $this->db->dbprefix($table). ' AUTO_INCREMENT '. $value);
		
		return $value;
	}
	
	function get_exchange_rates()
	{
		$this->db->from('currency_exchange_rates');
		$this->db->order_by('id');
		return $this->db->get();
	}
		
	
	
	
	function save_exchange_rates($currency_exchange_rates_to, $currency_exchange_rates_symbol, $currency_exchange_rates_rate,$currency_exchange_rates_symbol_location,$currency_exchange_rates_number_of_decimals,$currency_exchange_rates_thousands_separator,$currency_exchange_rates_decimal_point)
	{
		$this->db->truncate('currency_exchange_rates');
		
		for($k = 0; $k< count($currency_exchange_rates_to); $k++)
		{
			$currency_exchange_rate_to = $currency_exchange_rates_to[$k];
			$currency_exchange_rate_symbol = $currency_exchange_rates_symbol[$k];
			$currency_exchange_rate = $currency_exchange_rates_rate[$k];			
			$currency_exchange_rate_symbol_location = $currency_exchange_rates_symbol_location[$k];
			$currency_exchange_rate_number_of_decimals = $currency_exchange_rates_number_of_decimals[$k];
			$currency_exchange_rate_thousands_separator = $currency_exchange_rates_thousands_separator[$k];
			$currency_exchange_rate_decimal_point = $currency_exchange_rates_decimal_point[$k];
				
			$this->db->insert('currency_exchange_rates', array(
				'currency_symbol' => $currency_exchange_rate_symbol,
				'currency_code_to' => $currency_exchange_rate_to,
				'exchange_rate' => $currency_exchange_rate,
				'currency_symbol_location' => $currency_exchange_rate_symbol_location,
				'number_of_decimals' => $currency_exchange_rate_number_of_decimals,
				'thousands_separator' => $currency_exchange_rate_thousands_separator,
				'decimal_point' => $currency_exchange_rate_decimal_point,
			));
		}
		
		return true;
	}
	
}

?>