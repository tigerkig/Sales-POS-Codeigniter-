<?php
class MY_Session extends CI_Session 
{
	public function __construct(array $params = array())
	{
		$CI =& get_instance();
		if (!$CI->db->table_exists('sessions') || !$CI->db->field_exists('data','sessions'))
		{
			log_message('debug', 'Session: Initialization aborted; no table.');
			return;
		}
		parent::__construct($params);
	}
	
	/**
	 * Configuration
	 *
	 * Handle input parameters and configuration defaults
	 *
	 * @param	array	&$params	Input parameters
	 * @return	void
	 */
	protected function _configure(&$params)
	{
		$CI =& get_instance();
		$CI->load->model('Appconfig');
		$phppos_session_expiration = $CI->db->table_exists('app_config') ? $CI->Appconfig->get_raw_phppos_session_expiration() : 0;		
		$expiration = $phppos_session_expiration !== NULL ? $phppos_session_expiration : config_item('sess_expiration');
		$CI->config->set_item('sess_expiration',$expiration);
		
		parent::_configure($params);
	}
	
}
