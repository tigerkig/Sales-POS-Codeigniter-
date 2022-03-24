<?php
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;

class PHPPOSSpreadsheetSpout extends PHPPOSSpreadsheet
{
	private $reader;
	private $data;
	
	function __construct($inputFileName = NULL, $type='xlsx')
	{
		if ($inputFileName)
		{
			$CI =& get_instance();
			
			if (strtolower($type) == 'xlsx')
			{
				$this->reader = ReaderFactory::create(Type::XLSX);
			}
			else
			{
				$this->reader = ReaderFactory::create(Type::CSV);
			}
	
			$this->reader->open($inputFileName);
			foreach ($this->reader->getSheetIterator() as $sheet) 
			{
				$this->data = iterator_to_array($sheet->getRowIterator());
				break;
			}
		}
	}
	
	public static function getFirstRow($inputFileName, $type='xlsx')
	{
		$CI =& get_instance();
		if (strtolower($type) == 'xlsx')
		{
			$reader = ReaderFactory::create(Type::XLSX);
		}
		else
		{
			$reader = ReaderFactory::create(Type::CSV);
		}

		$reader->open($inputFileName);
		foreach ($reader->getSheetIterator() as $sheet) 
		{
			$it = $sheet->getRowIterator();
			$it->rewind();
			return $it->current();
		}
	}
	
	//$column starts at 0 and row starts at 1
	public function getCellByColumnAndRow($column, $row)
	{
		if ($this->data)
		{
			if (isset($this->data[$row][$column]))
			{
					return $this->data[$row][$column];	
			}
		}
		
		return NULL;
		
	}
	public function getNumberOfRows()
	{
		if ($this->data)
		{
			return count($this->data);
		}
		
		return null;
	}
	
	//$data is a matrix to export to excel
	public function arrayToSpreadsheet($arr,$filename, $is_report = false)
	{
		$CI =& get_instance();

		
		if ($is_report)
		{
			define('SPOUT_EXCEL_WRITER_CELL_FORMAT',0);
		}
		else
		{
			//If we are NOT a report make sure we set text format to 49 (Text format for excel imports)
			define('SPOUT_EXCEL_WRITER_CELL_FORMAT',49);
		}
		if ($CI->config->item('spreadsheet_format') == 'XLSX')
		{
			$writer = WriterFactory::create(Type::XLSX); // for XLSX files				
		}
		else
		{
			$writer = WriterFactory::create(Type::CSV); // for CSV files
		}
				
		$writer->openToBrowser($filename); // stream data directly to the browser

		if ($is_report)
		{
			
			for($k = 0;$k < count($arr);$k++)
			{
				for($j = 0;$j < count($arr[$k]); $j++)
				{
					$arr[$k][$j] = $this->stripCurrency($arr[$k][$j]);
					
					if (is_numeric($arr[$k][$j]))
					{
						$arr[$k][$j] = (double)$arr[$k][$j];
					}
				}
			}
		}		
		$writer->addRows($arr);		
		$writer->close();			
	}
}	
?>