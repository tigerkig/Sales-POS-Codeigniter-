<?php
abstract class Report extends CI_Model 
{
	var $CI;
	var $params	= array();
	function __construct()
	{
		parent::__construct();
		$this->report_limit = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		
		//Make sure the report is not cached by the browser
		$this->output->set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");		
			
	}
	
	public function getTotalRows()
	{
		$this->db->select("COUNT(DISTINCT(sale_id)) as sale_count");
		$this->db->from('sales');
		$ret = $this->db->get()->row_array();
		return $ret['sale_count'];
	}
	
	public function setParams(array $params)
	{
		$this->params = $params;
	}
	
	public function receiving_time_where()
	{
		static $location_ids;
		
		if (!$location_ids)
		{
			$location_ids = implode(',',Report::get_selected_location_ids());
		}
		
		$where = 'receiving_time BETWEEN "'.$this->params['start_date'].'" and "'.$this->params['end_date'].'"'.' and '.$this->db->dbprefix('receivings').'.location_id IN ('.$location_ids.')'.(($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('receivings').'.store_account_payment=0' : '');
		//Added for detailed_suspended_report, we don't need this for other reports as we are always going to have start + end date
		if (isset($this->params['force_suspended']) && $this->params['force_suspended'])
		{
			$where .=' and suspended != 0';				
		}
		elseif ($this->config->item('hide_suspended_recv_in_reports'))
		{
			$where .=' and suspended = 0';
		}
		
		$this->db->where($where);
		
	}
	
	public function sale_time_where()
	{
		static $location_ids;
		
		if (!$location_ids)
		{
			$location_ids = implode(',',Report::get_selected_location_ids());
		}
		
		$where = 'sale_time BETWEEN '.$this->db->escape($this->params['start_date']).' and '.$this->db->escape($this->params['end_date']).' and '.$this->db->dbprefix('sales').'.location_id IN ('.$location_ids.')'. (($this->config->item('hide_store_account_payments_in_reports') ) ? ' and '.$this->db->dbprefix('sales').'.store_account_payment=0' : '');
	
		//Added for detailed_suspended_report, we don't need this for other reports as we are always going to have start + end date
		if (isset($this->params['force_suspended']) && $this->params['force_suspended'])
		{
			$where .=' and (suspended != 0 or (was_layaway = 1 or was_estimate = 1))';				
		}
		elseif ($this->config->item('hide_layaways_sales_in_reports'))
		{
			$where .=' and suspended = 0';
		}
		else
		{
			$where .=' and suspended != 2';					
		}
		
		$this->db->where($where);
	}
	
	public static function get_selected_location_ids()
	{
		$CI =& get_instance();
		
		if ($CI->session->userdata('reports_selected_location_ids'))
		{
			return $CI->session->userdata('reports_selected_location_ids');
		}
		else
		{
			return array($CI->Employee->get_logged_in_employee_current_location_id());
		}
	}
	
	//Returns the column names used for the report
	public abstract function getDataColumns();
	
	//Returns all the data to be populated into the report
	public abstract function getData();
		
	//Returns key=>value pairing of summary data for the report
	public abstract function getSummaryData();
}
?>