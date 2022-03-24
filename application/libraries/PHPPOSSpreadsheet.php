<?php
abstract class PHPPOSSpreadsheet
{
	public static function getSpreadsheetClass($inputFileName = null, $type='xlsx')
	{
		if (version_compare(PHP_VERSION, '5.4.0') >= 0)
		{			
			require_once APPPATH.'libraries/Spout/Autoloader/autoload.php';		
			require_once (APPPATH.'libraries/PHPPOSSpreadsheetSpout.php');
			return new PHPPOSSpreadsheetSpout($inputFileName, $type);
		}
		else
		{
			require_once (APPPATH.'libraries/PHPPOSSpreadsheetPHPExcel.php');
			return new PHPPOSSpreadsheetPHPExcel($inputFileName,$type);
		}
	}
	
	public static function getFirstRow($inputFileName, $type='xlsx')
	{
		if (version_compare(PHP_VERSION, '5.4.0') >= 0)
		{			
			require_once APPPATH.'libraries/Spout/Autoloader/autoload.php';		
			require_once (APPPATH.'libraries/PHPPOSSpreadsheetSpout.php');
			return PHPPOSSpreadsheetSpout::getFirstRow($inputFileName, $type);
		}
		else
		{
			require_once (APPPATH.'libraries/PHPPOSSpreadsheetPHPExcel.php');
			return PHPPOSSpreadsheetPHPExcel::getFirstRow($inputFileName,$type);
		}
	}
	
	//$column starts at 0 and row starts at 1
	public abstract function getCellByColumnAndRow($column, $row);
	
	public abstract function getNumberOfRows();
	
	//$data is a matrix to export to excel
	public abstract function arrayToSpreadsheet($arr,$filename, $is_report = false);
	
	protected function stripCurrency($val)
	{
		$CI =& get_instance();
		
		$currency_symbol = $CI->config->item('currency_symbol') ? $CI->config->item('currency_symbol') : '$';
		$thousands_separator = $CI->config->item('thousands_separator') ? $CI->config->item('thousands_separator') : ',';
	
		//If we have a currency number make it nice for excel
		if (strpos($val, $currency_symbol) !== false)
		{
			$thousands_separator = preg_quote($thousands_separator);
			$currency_symbol = preg_quote($currency_symbol);
			$val = preg_replace("/[${thousands_separator}${currency_symbol}]/", "", $val);
		}
		
		return $val;
		
	}
}
?>