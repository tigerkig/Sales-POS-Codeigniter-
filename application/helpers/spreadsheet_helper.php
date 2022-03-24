<?php
require_once (APPPATH.'libraries/PHPPOSSpreadsheet.php');
function array_to_spreadsheet($arr,$filename,$is_report=FALSE)
{	
	$spreadsheet = PHPPOSSpreadsheet::getSpreadsheetClass();
	$spreadsheet->arrayToSpreadsheet($arr,$filename, $is_report);
}

function file_to_spreadsheet($inputFileName,$type = 'xlsx')
{
	$spreadsheet = PHPPOSSpreadsheet::getSpreadsheetClass($inputFileName,$type);
	return $spreadsheet;
}

function get_spreadsheet_first_row($inputFileName,$type = 'xlsx')
{
	if (version_compare(PHP_VERSION, '5.4.0') >= 0)
	{			
		require_once APPPATH.'libraries/Spout/Autoloader/autoload.php';		
		require_once (APPPATH.'libraries/PHPPOSSpreadsheetSpout.php');
		return PHPPOSSpreadsheetSpout::getFirstRow($inputFileName,$type);
	}
	else
	{
		require_once (APPPATH.'libraries/PHPPOSSpreadsheetPHPExcel.php');
		return PHPPOSSpreadsheetPHPExcel::getFirstRow($inputFileName,$type);
	}
	
}