<?php
class App_files extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();	
	}
	
	function view($file_id)
	{ 
		//Don't allow images to cause hangups with session
		session_write_close();
		$this->load->model('Appfile');
		$file = $this->Appfile->get($file_id);
		$this->load->helper('file');
		header("Cache-Control: max-age=2592000");
		header('Expires: '.gmdate('D, d M Y H:i:s', strtotime('+1 month')).' GMT');
		header('Pragma: cache');
		header("Content-type: ".get_mime_by_extension($file->file_name));
		
		if (function_exists('header_remove'))
		{
		  foreach(headers_list() as $header)
			{
				if (strpos($header, 'Set-Cookie') === 0) 
				{
		         header_remove('Set-Cookie');
				}
			}
		}
		echo $file->file_data;
	}
}
?>