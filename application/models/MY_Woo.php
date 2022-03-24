<?php
require_once APPPATH.'libraries/wooapi/vendor/autoload.php';
class MY_Woo extends Automattic\WooCommerce\Client
{
	function __construct($controller, $url, $consumerKey, $consumerSecret, $options = [])
	{
		$this->controller = $controller;
		$url_parts = parse_url($url);
		if($url_parts['scheme'] == 'https')
		{
			$options['query_string_auth'] = TRUE;
		}
		parent::__construct($url, $consumerKey, $consumerSecret, $options);
	}
	
	private function kill_if_needed()
	{
		if ($this->controller->Appconfig->get_raw_kill_ecommerce_cron())
		{
			if (is_cli())
			{
				echo date(get_date_format().' h:i:s ').': KILLING CRON'."\n";
			}
			
			$this->controller->Appconfig->save('kill_ecommerce_cron',0);
			echo json_encode(array('success' => TRUE, 'cancelled' => TRUE, 'sync_date' => date('Y-m-d H:i:s')));
			die();
		}
	}
  /**
   * POST method.
   *
   * @param string $endpoint API endpoint.
   * @param array  $data     Request data.
   *
   * @return array
   */
  public function post($endpoint, $data)
  {
		if (is_cli())
		{
			echo date(get_date_format().' h:i:s ').': post'."\n";
		}
		
		$this->kill_if_needed();
    return parent::post($endpoint, $data);
  }

  /**
   * PUT method.
   *
   * @param string $endpoint API endpoint.
   * @param array  $data     Request data.
   *
   * @return array
   */
  public function put($endpoint, $data)
  {
		if (is_cli())
		{
			echo date(get_date_format().' h:i:s ').': put'."\n";
		}
		
		$this->kill_if_needed();
    return parent::put($endpoint, $data);
  }

  /**
   * GET method.
   *
   * @param string $endpoint   API endpoint.
   * @param array  $parameters Request parameters.
   *
   * @return array
   */
  public function get($endpoint, $parameters = [])
  {
		if (is_cli())
		{
			echo date(get_date_format().' h:i:s ').': get'."\n";
		}
		$this->kill_if_needed();
    return parent::get($endpoint, $parameters);
  }

  /**
   * DELETE method.
   *
   * @param string $endpoint   API endpoint.
   * @param array  $parameters Request parameters.
   *
   * @return array
   */
  public function delete($endpoint, $parameters = [])
  {
		if (is_cli())
		{
			echo date(get_date_format().' h:i:s ').': delete'."\n";
		}
		$this->kill_if_needed();
    return parent::delete($endpoint, $parameters);
  }

  /**
   * OPTIONS method.
   *
   * @param string $endpoint API endpoint.
   *
   * @return array
   */
  public function options($endpoint)
  {
		if (is_cli())
		{
			echo date(get_date_format().' h:i:s ').': options'."\n";
		}
		$this->kill_if_needed();
    return parent::options($endpoint);
  }
}