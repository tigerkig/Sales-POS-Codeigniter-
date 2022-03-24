<?php
require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
require_once (APPPATH.'libraries/PHPPOSSpreadsheet.php');

class PHPPOSSpreadsheetPHPExcel extends PHPPOSSpreadsheet
{
	private $phpExcelRef;
	private $sheet;
	
	function __construct($inputFileName = NULL, $type='xlsx')
	{
		if ($inputFileName)
		{
			$CI =& get_instance();
			PHPExcel_Shared_File::setUseUploadTempDirectory(true);
			
			if (strtolower($type) == 'xlsx')
			{
				$objReader = new PHPExcel_Reader_Excel2007();
			}
			else
			{
				$objReader = new PHPExcel_Reader_CSV();
				PHPExcel_Cell::setValueBinder(new TextValueBinder());
			}
	
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load($inputFileName);
	
			$this->phpExcelRef = $objPHPExcel;
			$this->sheet = $this->phpExcelRef->getActiveSheet();
		}
	}
	
	public static function getFirstRow($inputFileName, $type='xlsx')
	{
		$CI =& get_instance();		
		if (strtolower($type)== 'xlsx')
		{
			$objReader = new PHPExcel_Reader_Excel2007();
		}
		else
		{
			$objReader = new PHPExcel_Reader_CSV();
			PHPExcel_Cell::setValueBinder(new TextValueBinder());
		}

		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		
		$row = $objPHPExcel->getActiveSheet()->getRowIterator(1)->current();

		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);

		$first_row = array();
		foreach ($cellIterator as $cell) {
		    $first_row[] = $cell->getValue();
		}
		
		return $first_row;
	}
	
	//$column starts at 0 and row starts at 1
	public function getCellByColumnAndRow($column, $row)
	{
		if ($this->sheet)
		{
			return $this->sheet->getCellByColumnAndRow($column, $row)->getValue();
		}
		
		return null;
	}
	
	public function getNumberOfRows()
	{
		if ($this->phpExcelRef)
		{
			return $this->phpExcelRef->setActiveSheetIndex(0)->getHighestRow();
		}
		
		return null;
	}
	
	//$data is a matrix to export to excel
	public function arrayToSpreadsheet($arr,$filename, $is_report = false)
	{
		$CI =& get_instance();
		PHPExcel_Shared_File::setUseUploadTempDirectory(true);
		$objPHPExcel = new PHPExcel();
	
		//Default all cells to text if NOT report
		if (!$is_report)
		{
			$objPHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		}
	
		for($k = 0;$k < count($arr);$k++)
		{
			for($j = 0;$j < count($arr[$k]); $j++)
			{
				if (!$is_report)
				{
					$objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($j, $k+1, $arr[$k][$j]);	
				}
				else
				{
					
					$arr[$k][$j] = $this->stripCurrency($arr[$k][$j]);					
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $k+1, $arr[$k][$j]);
				}
			}
		}
	
		if ($CI->config->item('spreadsheet_format') == 'XLSX')
		{
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);		
		}
		else
		{
			$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
		}
	
		ob_start();
		$objWriter->save('php://output');
		$excelOutput = ob_get_clean();
	
		$CI->load->helper('download');
		force_download($filename, $excelOutput);
	}
}

class TextValueBinder implements PHPExcel_Cell_IValueBinder
{
	public function bindValue(PHPExcel_Cell $cell, $value = null) 
	{
	    $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
	    return true;
	}
}
?>