<?php

use Automattic\WooCommerce\Client;

require_once ("interfaces/Ecom.php");
class Woo extends  Ecom
{
	
	public $woo_store_url;
	public $woo_api_key;
	public $woo_api_secret;

	public $woo_read_chunk_size;
	public $woo_read_sleep;
	public $woo_write_chunk_size;
	public $woo_write_sleep;
	public $woo_curl_timeout;
	
	function __construct()
	{		
	    parent::__construct();
			$this->woo_store_url = $this->config->item('woo_api_url');
			$this->woo_api_key = $this->config->item('woo_api_key');
			$this->woo_api_secret = $this->config->item('woo_api_secret');	
			$this->woo_read_chunk_size = 50;
			$this->woo_read_sleep = 0;
			$this->woo_write_chunk_size = 100;
			$this->woo_write_sleep = 0;
			//60 minute timeout for curl
			$this->woo_curl_timeout = 3600;
			$this->start_time = time();
			$this->woo_api_version = $this->config->item('woo_version') == '3.0.0' ? 2 : 1;
			ini_set('memory_limit','1024M');
	}
	
	public function save_item_from_phppos_to_ecommerce($item_data, $item_id)
	{
		$this->log(lang("save_item_from_phppos_to_ecommerce").' '.$item_data['name']);
		require_once APPPATH.'models/MY_Woo.php';
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		if ($this->config->item('online_price_tier'))
		{
			$this->load->library('sale_lib');
			$online_price = $this->sale_lib->get_price_for_item($item_id,$this->config->item('online_price_tier'));
		}
		else
		{
			$online_price = to_currency_no_money($item_data['unit_price']);
		}
		
		$data = array(
			'name' => $item_data['name'],
			'regular_price' => $online_price,
			'description' => $item_data['description'],
			'manage_stock'=>isset($item_data['is_service']) && $item_data['is_service'] ? FALSE : TRUE
		);
		
		if (isset($item_data['quantity']))
		{
			$data['stock_quantity']= $item_data['quantity'] ? floor($item_data['quantity']) : 0;
		}

		if (isset($item_data['item_number']) && $item_data['item_number'])
		{
			$data['sku'] = $item_data['item_number'];
		}
		
		if(version_compare($this->config->item('woo_version'), '2.7.0b') >= 0)
		{
				$data['images'] = '';
		}
		
		if (isset($item_data['images']) && $item_data['images'])
		{
			$data['images'] = array();
			
			for($k=0;$k<count($item_data['images']);$k++)
			{
				$image_data =  array('src' => $item_data['images'][$k]['src'], 'alt' => $item_data['images'][$k]['alt'], 'name' => $item_data['images'][$k]['name'], 'position' => $k);
				
				if (isset($item_data['images'][$k]['ecommerce_image_id']) && $item_data['images'][$k]['ecommerce_image_id'])
				{
					$image_data['id'] = $item_data['images'][$k]['ecommerce_image_id'];
				}
				
				$data['images'][] = $image_data;
			}
		}
		
		if ($item_data['promo_price']!=0 && $item_data['start_date'] && $item_data['end_date'])
		{
			$data['sale_price'] = to_currency_no_money($item_data['promo_price']);
			$data['date_on_sale_from'] = $item_data['start_date'];
			$data['date_on_sale_to'] = $item_data['end_date'];
		}
		else
		{
			$data['sale_price'] = '';			
		}
		
		$this->load->model('Category');
		$phppos_cats = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_category_id(FALSE);
		
		$woo_cat_id = NULL;
		
		if (isset($phppos_cats[$item_data['category_id']]))
		{
			$woo_cat_id = $this->get_woo_category_id($phppos_cats[$item_data['category_id']]);
			
			if (!$woo_cat_id)
			{
				$this->export_phppos_categories_to_ecommerce($this->Category->get_root_parent_category_id($item_data['category_id']));
				$woo_cat_id = $this->get_woo_category_id($this->Category->get_full_path($item_data['category_id'], '|'));	
			}	
		}
					
		if ($woo_cat_id)
		{
			$data['categories'] = array(array('id' => $woo_cat_id));
		}
		
		if (isset($item_data['tags']))
		{
			$tag_created = FALSE;
			$woo_tags = $this->get_all_tags();
			
			foreach($item_data['tags'] as $phppos_tag)
			{
				if ($phppos_tag && !$this->get_woo_tag_id($phppos_tag,$woo_tags))
				{
					$this->save_tag_to_woocommerce($phppos_tag);
					$tag_created = TRUE;
				}	
			}
			
			if ($tag_created)
			{
				$woo_tags = $this->get_all_tags();
			}
			
			$woo_tags_ids = array();
			
			foreach($item_data['tags'] as $phppos_tag)
			{
				if ($phppos_tag)
				{
					$woo_tags_ids[] = array('id' => $woo_tags[strtoupper($phppos_tag)]);
				}
			}
							
			if (!empty($woo_tags_ids))
			{
				$data['tags'] = $woo_tags_ids;
			}
			else
			{
				$data['tags'] = array();
			}
		}
		
		if (isset($item_data['tags']))
		{
			$woo_tags = $this->get_all_tags();
			
		}
						
		if (!isset($item_data['ecommerce_product_id']) && !$item_data['ecommerce_product_id'])
		{
			//New product
			try
			{
				$woo_product = $woocommerce->post('products', $data);
				
				
			}
			catch(Exception $e)
			{
				
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
			}
		}
		else
		{
			//Existing product
			try
			{
				$woo_product = $woocommerce->put('products/'.$item_data['ecommerce_product_id'], $data);
				
				
			}
			catch(Exception $e)
			{
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
			}
		}
		
		if (isset($woo_product['images']) && !empty($woo_product['images']))
		{
			for($k=0;$k< count($item_data['images']);$k++)
			{
				$this->Item->link_image_to_ecommerce($item_data['images'][$k]['image_id'], $woo_product['images'][$k]['id']);
			}
		}
		
		//Sync quantity data
		if (isset($item_data['quantity']))
		{
			if (!isset($item_data['ecommerce_product_id']) && !$item_data['ecommerce_product_id'])
			{
				//Add newly created woocommerce product in the ecommerce_products table
				$this->db->insert('ecommerce_products',array('product_id' => $woo_product['id'],'product_quantity'=>$item_data['quantity']));
			}
			else
			{
				//Update quantity
				$this->db->where('product_id', $woo_product['id']);
				$this->db->update('ecommerce_products',array('product_quantity' => $item_data['quantity']));
			}
		}
		
		$this->link_item($item_id,$woo_product['id']);
	}
	
	
	function get_all_tags($use_cache = TRUE)
	{
		$this->log(lang("get_all_tags"));
		
		if($use_cache)
		{
			//Get tags from database cache
			$tags = array();
			$this->load->model('Tag');
			$phppos_tags = array();
			foreach($this->Tag->get_all() as $phppos_tag_id => $phppos_tag)
			{
				$phppos_tags[strtoupper($phppos_tag['name'])] = $phppos_tag_id;
			}
			$phppos_tags = array_flip($phppos_tags);
			
			$this->db->from('ecommerce_tags');
			foreach($this->db->get()->result_array() as $row)
			{
				$phppos_tag = $row['tag_id'];
				$ecommerce_tag_id = $row['ecommerce_tag_id'];
				$tags[strtoupper($phppos_tags[$phppos_tag])] = (int)$ecommerce_tag_id;
			}
			
			if (count($tags) != 0)
			{
				return $tags;
			}
		}
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		$send_call = true;
		$per_page = $this->woo_read_chunk_size;
		$categories = array();
		$page = 1;
		$tags = array();
		
		while($send_call == true) {
			
			try
			{
				$result_tags = $woocommerce->get('products/tags', array('per_page'=>$per_page, 'page'=>$page,'context' => 'view'));
				
				sleep($this->woo_read_sleep);
			}
			catch(Exception $e)
			{
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
			}
			$page++;
			$tags = array_merge( $tags, $result_tags );
			
			if( count($result_tags) < $per_page ){
				$send_call=false;
			}
			
		}				
		$return_woo_tags = array();
		$this->load->model('Tag');
		
		$phppos_tags = array();
		foreach($this->Tag->get_all() as $phppos_tag_id => $phppos_tag)
		{
			$phppos_tags[strtoupper($phppos_tag['name'])] = $phppos_tag_id;
		}
		
		foreach($tags as $index => $tag)
		{
			//Save in cache if we have a match
			if (isset($phppos_tags[strtoupper(html_entity_decode($tag['name']))]))
			{
				$phppos_tag_id = $phppos_tags[strtoupper(html_entity_decode($tag['name']))];
				$this->db->replace('ecommerce_tags', array('tag_id' => $phppos_tag_id, 'ecommerce_tag_id' => $tag['id']));
			}
			
			$return_woo_tags[strtoupper(html_entity_decode($tag['name']))] =  $tag['id'];
		}
		$tags = $return_woo_tags;

		return $tags;		
	}
	
	function get_all_categories_and_sub_categories($use_cache = TRUE)
	{
		$this->log(lang("get_all_categories_and_sub_categories"));
		
		if($use_cache)
		{				
			$cat_map = array();
			//Get cats from database cache
			$this->load->model('Category');
			$phppos_cats = array_flip($this->Category->get_all_categories_and_sub_categories_as_indexed_by_name_key($use_cache,'strsame'));
			
			$this->db->from('ecommerce_categories');
			foreach($this->db->get()->result_array() as $row)
			{
				$phppos_cat = $row['category_id'];
				$ecommerce_category_id = $row['ecommerce_category_id'];
				$cat_map[$phppos_cats[$phppos_cat]] = $ecommerce_category_id;
			}
			
			if (count($cat_map) != 0)
			{
				return $cat_map;
			}
		}
		
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		$send_call = true;
		$per_page = $this->woo_read_chunk_size;
		$categories = array();
		$page = 1;
		
		$categories = array();
		
		while($send_call == true) {
			
			try
			{
				$result_categories = $woocommerce->get('products/categories', array('per_page'=>$per_page, 'page'=>$page,'context' => 'view'));
				
				sleep($this->woo_read_sleep);
			}
			catch(Exception $e)
			{
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
			}
			$page++;
			$categories = array_merge( $categories, $result_categories );
			
			if( count($result_categories) < $per_page ){
				$send_call=false;
			}
			
		}				
		$return_woo_categories = array();
		
		foreach($categories as $index => $category)
		{
			$return_woo_categories[] = array('name' => html_entity_decode($category['name']), 'id' => $category['id'], 'parent' => $category['parent'], 'image' => isset($category['image']['src']) ? $category['image']['src'] : FALSE);
		}
		$tree = array();
		foreach ($return_woo_categories as $cat) 
		{
		    if (!isset($tree[$cat['id']])) 
				{ 
					$tree[$cat['id']] = array(); 
				}
		    $tree[$cat['id']]['name'] = $cat['name'];
				
		    if (!isset($tree[$cat['parent']])) 
				{ 
					$tree[$cat['parent']] = array(); 
				}
				
		    $tree[$cat['parent']]['children'][$cat['id']] =& $tree[$cat['id']];
		}
		
		if (!empty($tree))
		{
			$cat_map = array_flip($this->build_paths($tree[0]['children']));
		}
		else
		{
			$cat_map = array();
		}
		$this->load->model('Category');
		$categories_indexed_by_name = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_name_key($use_cache);
		
		foreach($cat_map as $cat_path => $woo_cat_id)
		{
			//Save in cache if we have match
			if(isset($categories_indexed_by_name[strtoupper($cat_path)]))
			{
				$phppos_category_id = $categories_indexed_by_name[strtoupper($cat_path)];
				$this->db->replace('ecommerce_categories', array('category_id' => $phppos_category_id, 'ecommerce_category_id' => $woo_cat_id));
			}
		}
		return $cat_map;
	}
	
	private function build_paths($tree, $path = '') 
	{
	    $result = array();
	    foreach ($tree as $id => $cat) 
			{
	        $result[$id] = $path . $cat['name'];
	        if (isset($cat['children'])) 
					{
	            $result += $this->build_paths($cat['children'], $result[$id] . '|');
	        }
	    }
	    return $result;
	}

	
	function is_configured()
	{
		return 	$this->woo_store_url && $this->woo_api_key && $this->woo_api_secret;
	}
	
	function sync_phppos_item_changes()
	{
		$this->log(lang("sync_phppos_item_changes"));		
		if ( $this->is_configured() == false) {
			return FALSE;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		$changed_items = $this->get_products_modified_since_last_sync();
		while($changed_item = $changed_items->unbuffered_row('array'))
		{			
			$this->log(lang('common_item_changed').': '.$changed_item['name']);
			
			$changed_item['tags'] = explode(',', $changed_item['tags']);
			$this->load->model('Item');
			$item_images = $this->Item->get_item_images($changed_item['item_id']);
			
			
			if(version_compare($this->config->item('woo_version'), '2.7.0b') >= 0)
			{
					$changed_item['images'] = '';
			}
			
			if ($item_images && isset($item_images[0]))
			{
				$changed_item['images'] = array();
				
				//We have at least one image
				foreach($item_images as $item_image)
				{
					$changed_item['images'][] = array('image_id' =>$item_image['image_id'], 'ecommerce_image_id' => $item_image['ecommerce_image_id'], 'src' => app_file_url($item_image['image_id']),'alt' => $item_image['alt_text'], 'name' => $item_image['title']);
				}
			}
			
			$this->save_item_from_phppos_to_ecommerce($changed_item, $changed_item['item_id']);
		}
	}
	
	function sync_inventory_changes()
	{
		$this->log(lang("sync_inventory_changes"));		
		if ( $this->is_configured() == false) {
			return FALSE;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		$send_call = true;
		$per_page = $this->woo_read_chunk_size;
		$products = array();
		$page = 1;
		
		while($send_call == true) 
		{
			
			try
			{
				$result_products = $woocommerce->get('products', array('per_page'=>$per_page, 'page'=>$page));
				
				sleep($this->woo_read_sleep);
			}
			catch(Exception $e)
			{
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
			}
			$page++;
			
			foreach($result_products as $woo_product)
			{
				$this->db->from('ecommerce_products');
				$this->db->where('product_id', $woo_product['id']);
				$result = $this->db->get();
				if ($result->num_rows() > 0) 
				{
					$items=$result->result_array();
					$item_quantity=$woo_quantity="";
					$woo_quantity=$woo_product['stock_quantity'];
					$item_id=$this->get_item_id_for_ecommerce_product($woo_product['id']);
					if($item_id!=NULL)
					{
						$item_quantity=$this->get_item_quantity($item_id);
					}
					if($item_quantity=="" && $woo_quantity=="")
					{
						//quantity field not available in woocommerce and phppos
						$actual_quantity=0;
					}
					else if($item_quantity=="")
					{
						//quantity field not available in phppos but available in woocommerce
						$actual_quantity=$woo_quantity;
					}
					else if($woo_quantity=="")
					{
						//quantity field not available in woocommerce but available in phppos
						$actual_quantity=$item_quantity;
					}
					else
					{
						//quantity field present both on woocommerce and phppos
						$prev_quantity=   $items[0]['product_quantity'];
						$pos_difference = $prev_quantity - $item_quantity;
						$woo_difference = $prev_quantity - $woo_quantity;
						$difference_sum	= $pos_difference + $woo_difference;
						$actual_quantity = $prev_quantity - $difference_sum;
					}
				
					$this->db->where('product_id', $woo_product['id']);
					$this->db->update('ecommerce_products',array('product_quantity' => (int)$actual_quantity));
				
					//update quantity to woocommerce
					if( $actual_quantity != $woo_quantity )
					{
						$data = array(
							'stock_quantity' => (int)$actual_quantity
						);
						try
						{
							$woocommerce->put('products/'.$woo_product['id'], $data);
							$this->log(lang('item inventory changed in woo')." ".$woo_product['id'] .'('.$actual_quantity.')');
						
							sleep($this->woo_write_sleep);
						}
						catch(Exception $e)
						{
							$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
							continue;
						
						}
					}
					//update quantity to phppos
					if( $actual_quantity != $item_quantity) 
					{
						$difference = (int)$actual_quantity - (int)$item_quantity;
						if($item_id!=NULL && $difference!=0){
						$cron_job_entry=lang('woo_cron_job_entry');
						$this->db->insert('inventory',array('trans_items' => $item_id,'trans_user'=>1,'trans_comment'=>$cron_job_entry,'trans_inventory'=> $difference,'location_id'=>$this->ecommerce_store_location));
					
						$this->db->where(array('item_id' => $item_id,'location_id'=>$this->ecommerce_store_location));
						$this->log(lang("item inventory changed in php pos").' '.$item_id .'('.$actual_quantity.')');
						$this->db->update('location_items',array('quantity'=>$actual_quantity));
					
						}
					}
				}
			}		
			
			
			if( count($result_products) < $per_page ){
				$send_call=false;
			}
			
		}
		
		return TRUE;
	}
	
	public function update_category_from_phppos_to_woocommerce($category_name,$category_image_url,$phppos_category_id, $woo_category_id)
	{
		$this->log(lang("update_category_from_phppos_to_woocommerce").': '.$category_name);
		
		if ( $this->is_configured() == false ) 	
		{
			return NULL;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		$cat_data = array('name' => $category_name);
		
		if ($category_image_url)
		{
			$cat_data['image'] = array('src' => $category_image_url);
		}
		
		try
		{
			$woo_response = $woocommerce->put('products/categories/'.$woo_category_id, $cat_data);
			return $woo_response['id'];
		}
		catch(Exception $e)
		{
			$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
		}
		
		return NULL;
	}
	
	public function save_category_to_woocommerce($category_name, $category_image_url, $woo_parent_id,$phppos_category_id)
	{
		$this->log(lang("save_category_to_woocommerce").': '.$category_name);
		
		if ( $this->is_configured() == false ) 	
		{
			return NULL;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		$cat_data = array('name' => $category_name, 'parent' => $woo_parent_id);
		
		if ($category_image_url)
		{
			$cat_data['image'] = array('src' => $category_image_url);
		}
		
		try
		{
			$woo_response = $woocommerce->post('products/categories', $cat_data);
			$this->db->replace('ecommerce_categories', array('category_id' => $phppos_category_id, 'ecommerce_category_id' => $woo_response['id']));
			
			return $woo_response['id'];
		}
		catch(Exception $e)
		{
			$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
		}
		
		return NULL;
	}
	
	public function save_tag_to_woocommerce($tag_name)
	{
		$this->log(lang("save_tag_to_woocommerce").': '.$tag_name);
		
		if ( $this->is_configured() == false ) 	
		{
			return NULL;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		try
		{
			$this->load->model('Tag');
			$woo_response = $woocommerce->post('products/tags', array('name' => $tag_name));
			$phppos_tag_id = $this->Tag->get_tag_id_by_name($tag_name);
			
			if ($phppos_tag_id !== FALSE)
			{
				$this->db->replace('ecommerce_tags', array('tag_id' => $phppos_tag_id, 'ecommerce_tag_id' => $woo_response['id']));
			}
			
			return $woo_response['id'];
		}
		catch(Exception $e)
		{
			$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
		}
		return NULL;
		
	}
	
	
	public function export_phppos_categories_to_ecommerce($parent_id = NULL)
	{
		$this->log(lang("export_phppos_categories_to_ecommerce"));
		
		if ( $this->is_configured() == false ) 	
		{
			return FALSE;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		$woo_cats = $this->get_all_categories_and_sub_categories();
		$this->load->model('Category');
		$this->load->model('Appfile');
		
		//This category list is in order by name and in hierarchy order
		$phppos_cats = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_category_id(FALSE,$parent_id);
		foreach($phppos_cats as $phppos_category_id => $phppos_category_path)
		{
			if($this->is_category_ok_to_sync($phppos_category_id) && !isset($woo_cats[$phppos_category_path]))
			{
				$category_parts = explode('|',$phppos_category_path);
				$category_name = end($category_parts);
				$woo_parent_id = $this->get_woo_category_parent_id($phppos_category_path,$woo_cats);
				$cat_info = $this->Category->get_info($phppos_category_id);
				$category_image_url = $cat_info->image_id ? $this->Appfile->get_url_for_file($cat_info->image_id) : FALSE;
			
				//Update woo_cats array with new category we just made
				$woo_cats[$phppos_category_path] =	$this->save_category_to_woocommerce($category_name, $category_image_url, $woo_parent_id,$phppos_category_id);
				sleep($this->woo_write_sleep);
			}
		}		
	}
	
	public function get_woo_category_parent_id($category_path, $woo_cats = NULL)
	{
		$this->log(lang("get_woo_category_parent_id"));
		
		if ($woo_cats == NULL)
		{
			set_time_limit(0);
			$woo_cats = $this->get_all_categories_and_sub_categories();
		}
		
		$path_parts = explode('|',$category_path);
		
		//Remove last part of path as we only want parent
		$path_parts = array_slice($path_parts, 0, count($path_parts) - 1);
		for($k=0;$k < count($path_parts);$k++)
		{
			$category_path = implode('|',array_slice($path_parts,$k));
			
			if (isset($woo_cats[$category_path]))
			{
				return $woo_cats[$category_path];
			}
		}
		
		return 0;
	}
			
	public function get_woo_category_id($category_path, $woo_cats = NULL)
	{
		$this->log(lang("get_woo_category_id"));
		
		if ($woo_cats == NULL)
		{
			set_time_limit(0);
			$woo_cats = $this->get_all_categories_and_sub_categories();
		}
		
		if (isset($woo_cats[$category_path]))
		{
			return $woo_cats[$category_path];
		}
		
		return NULL;
	}
	
	public function get_woo_tag_id($tag_name,$woo_tags = NULL)
	{
		$this->log(lang("get_woo_tag_id"));
		
		if ($woo_tags == NULL)
		{
			set_time_limit(0);
			$woo_tags = $this->get_all_tags();
		}
		
		if (isset($woo_tags[strtoupper($tag_name)]))
		{
			return $woo_tags[strtoupper($tag_name)];
		}
		
		return NULL;
	}
	
	
	public function export_phppos_tags_to_ecommerce()
	{
		$this->log(lang("export_phppos_tags_to_ecommerce"));
		
		if ( $this->is_configured() == false ) 	
		{
			return FALSE;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woo_tags = $this->get_all_tags();
		$this->load->model('Tag');
		foreach($this->Tag->get_all() as $phppos_tag_id => $phppos_tag_data)
		{			
			if($phppos_tag_data['name'] && !isset($woo_tags[strtoupper($phppos_tag_data['name'])]))
			{
				$this->save_tag_to_woocommerce($phppos_tag_data['name']);
				sleep($this->woo_write_sleep);
			}
		}
	}
	
	function export_phppos_items_to_ecommerce()
	{
		$this->log(lang("export_phppos_items_to_ecommerce"));
		
		if ( $this->is_configured() == false ) 	
		{
			return FALSE;
		}
		set_time_limit(0);
				
		$this->load->model('Category');
		$this->load->model('Item');
		
		$phppos_cats = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_category_id(FALSE);
		
		$woo_cats = $this->get_all_categories_and_sub_categories();
		$woo_tags = $this->get_all_tags();
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		$products_not_in_woo = $this->get_products_not_in_ecommerce();
		$this->load->model('Appfile');
		$woo_items = array();	
		$prod_images = array();
		$phppos_item_ids = array();
		while($prod = $products_not_in_woo->unbuffered_row('array'))
		{			
			$quantity = $this->get_item_quantity($prod['item_id']);
			
			if ($this->config->item('online_price_tier'))
			{
				$this->load->model('Tier');
				$this->load->model('Item_location');
				$this->load->library('sale_lib');	
				$online_price = $this->sale_lib->get_price_for_item($prod['item_id'], $this->config->item('online_price_tier'));
			}
			else
			{
				$online_price = to_currency_no_money($prod['unit_price']);
			}
			
			$woo_item_data = array(
				'name' =>$prod['name'],
				'regular_price' =>$online_price,
				'description' =>$prod['description'],
				'stock_quantity' => $quantity ? floor($quantity) : 0,
				'manage_stock'=>isset($prod['is_service']) && $prod['is_service'] ? FALSE : TRUE
			);
			
			
			if ($prod['promo_price'] !=0 && $prod['start_date'] && $prod['end_date'])
			{
				$woo_item_data['sale_price'] = to_currency_no_money($prod['promo_price']);
				$woo_item_data['date_on_sale_from'] = $prod['start_date'];
				$woo_item_data['date_on_sale_to'] = $prod['end_date'];
			}
			else
			{
				$woo_item_data['sale_price'] = '';				
			}
			
			
			if (isset($prod['item_number']) && $prod['item_number'])
			{
				$woo_item_data['sku'] = $prod['item_number'];
			}
			
			$phppos_item_images = $this->Item->get_item_images($prod['item_id']);
			$woo_item_data['images'] = array();
			
			for($k=0;$k<count($phppos_item_images);$k++)
			{
				 	$woo_item_data['images'][] = array('alt' =>$phppos_item_images[$k]['alt_text'], 'name' =>$phppos_item_images[$k]['title'], 'src' => $this->Appfile->get_url_for_file($phppos_item_images[$k]['image_id']), 'position' => $k);
			}
			$prod_images[] = $phppos_item_images;
			
			if (isset($phppos_cats[$prod['category_id']]) && isset($woo_cats[$phppos_cats[$prod['category_id']]]))
			{
				$item_category_path = $phppos_cats[$prod['category_id']];
				$woo_cat_id = $woo_cats[$item_category_path];
				$categories =	array(array('id' => $woo_cat_id));
				$woo_item_data['categories'] = $categories;
			}
			
			if (isset($prod['tags']))
			{
				$item_tags = explode(',',$prod['tags']);
				$woo_tags_ids = array();
				
				foreach($item_tags as $phppos_tag)
				{
					if ($phppos_tag)
					{
						$woo_tags_ids[] = array('id' => $woo_tags[strtoupper($phppos_tag)]);
					}
				}
								
				if (!empty($woo_tags_ids))
				{
					$woo_item_data['tags'] = $woo_tags_ids;
				}
			}
			
			$phppos_item_ids[] = $prod['item_id'];
			$woo_items[] = $woo_item_data;
		}
		
		foreach($woo_items as $item_to_save)
		{
			$this->log(lang('save_item_to_ecommerce').': '.$item_to_save['name']);
		}
		
		$woo_items_chunked = array_chunk($woo_items,$this->woo_write_chunk_size);
		
		$counter = 0;
		foreach($woo_items_chunked as $woo_items_chunk)
		{
			$woo_response = NULL;
			
			try
			{
				$woo_response = $woocommerce->post('products/batch', array('create' => $woo_items_chunk));
				
				sleep($this->woo_write_sleep);
			}
			catch(Exception $e)
			{
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
				$woo_response = NULL;
			}
			
			if ($woo_response!= NULL && !empty($woo_response))
			{
				for($k=0;$k<count($woo_response['create']);$k++)
				{
					//Add newly created woocommerce product in the ecommerce_products table
					$this->db->insert('ecommerce_products',array('product_id' =>$woo_response['create'][$k]['id'],'product_quantity'=>$woo_response['create'][$k]['stock_quantity']));
					$this->link_item($phppos_item_ids[$counter],$woo_response['create'][$k]['id']);

					if (!empty($prod_images[$counter]))
					{
						for($j=0;$j<count($woo_response['create'][$k]['images']);$j++)
						{
							$woo_image_id = $woo_response['create'][$k]['images'][$j]['id'];
							$this->Item->link_image_to_ecommerce($prod_images[$counter][$j]['image_id'], $woo_image_id);							
						}
					}
					
					$counter++;
					
				}
			}
			
		}
				
		return TRUE;
	}
	
	function import_ecommerce_items_into_phppos()
	{
		$this->log(lang("import_ecommerce_items_into_phppos"));
			
		if ( $this->is_configured() == false ) 
		{
			return FALSE;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		$woo_cats = $this->get_all_categories_and_sub_categories();
		$woo_tags = $this->get_all_tags();
		
		//Save new tags
		$this->load->Model('Tag');
		foreach(array_keys($woo_tags) as $tag_name)
		{
			if (!$this->Tag->tag_name_exists($tag_name))
			{
					$this->Tag->save(ucwords(strtolower($tag_name)));
			}
		}
		
		//Save new categories
		$this->load->model('Category');
		$categories_indexed_by_name = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_name_key();
		
		foreach(array_keys($woo_cats) as $category_path)
		{
			$this->Category->create_categories_as_needed($category_path,$categories_indexed_by_name);
		}
		
		
		$send_call = true;
		$per_page = $this->woo_read_chunk_size;
		$products = array();
		$page = 1;
		
		while($send_call == true) 
		{
			try
			{
				$result_products = $woocommerce->get('products', array('per_page'=>$per_page, 'page'=>$page));
				
				sleep($this->woo_read_sleep);
				
			}
			catch(Exception $e)
			{
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
			}
			
			$page++;
			
			foreach($result_products as $woo_product)
			{
				$this->db->from('ecommerce_products');
				$this->db->where('product_id', $woo_product['id']);
				$result = $this->db->get();
			
				//New Item
				if ($result->num_rows() == 0) 
				{
					$this->add_item_from_ecommerce_to_phppos($woo_product);
				}
			}
			
			if( count($result_products) < $per_page )
			{
				$send_call=false;
			}
		}
		
		
		return TRUE;
	}
	
	
	function add_item_from_ecommerce_to_phppos($woo_product)
	{
		
		$this->log(lang("add_item_from_ecommerce_to_phppos").": ".$woo_product['name']);
			
		static $phppos_cats;
		static $ecom_cats;
			
		if (!$phppos_cats)
		{			
			$this->load->model('Category');
			$phppos_cats = array_flip($this->Category->get_all_categories_and_sub_categories_as_indexed_by_category_id(FALSE));
		}
		
		if (!$ecom_cats)
		{
			$ecom_cats = array_flip($this->get_all_categories_and_sub_categories());
		}
	
		//Add to our database of ecommerce
		$this->db->insert('ecommerce_products',array('product_quantity'=>$woo_product['stock_quantity'],'product_id' => $woo_product['id']));
		
		//checking if product exists already in the phppos items
		$product_name = $woo_product['name'];
		$product_id = $woo_product['id'];
		$item_number = $woo_product['sku'];
		$product_description = $woo_product['description'];

		$product_category=NULL;
		$product_price=0;
		$product_quantity=0;
		$product_categories=$woo_product['categories'];
		
		if(count($product_categories)>0)
		{
			$product_selected_category=$product_categories[0]['id'];
			
			$this->load->model('Category');
			
			if (isset($phppos_cats[$ecom_cats[$product_selected_category]])) 
			{
				$product_category=$phppos_cats[$ecom_cats[$product_selected_category]];
			}
		}
			
		$product_tags=$woo_product['tags'];
		$pos_tags = '';
		//importing tags from woocommerce
		if(count($product_tags)>0)
		{
			foreach($product_tags as $pro_tag)
			{
				$this->db->from('tags');
				$this->db->where('name',$pro_tag['name']);
				$result = $this->db->get();
				if ($result->num_rows() > 0) 
				{
					$tag_from_phppos=$result->row_array();
					$pos_tags.=",".$tag_from_phppos['id'];
				}
			}
		}
		
		$product_price=$woo_product['regular_price'];
		$product_promo_price = $woo_product['sale_price'];
		$product_quantity=$woo_product['stock_quantity'];

		$product_promo_start_date = $woo_product['date_on_sale_from'];
		$product_promo_end_date = $woo_product['date_on_sale_to'];

			
		$item_array = array('name'=>$product_name,'description' => strip_tags($product_description),'category_id'=>$product_category,'unit_price'=>$product_price,'ecommerce_product_id'=>$product_id);
		$item_id = FALSE;
		
		if ($item_number)
		{
			$item_array['item_number'] = $item_number;
			$this->load->model('Item');
			$item_id = $this->Item->get_item_id($item_number);
			
		}
		
		if ($product_promo_price && $product_promo_start_date && $product_promo_end_date)
		{
			$item_array['promo_price'] = $product_promo_price;
			$item_array['start_date'] = $product_promo_start_date;
			$item_array['end_date'] = $product_promo_end_date;
		}
		
		$this->load->model('Item');
		$this->Item->save($item_array,$item_id);
		$this->db->from('items');
		$this->db->where('ecommerce_product_id',$product_id);
		$result = $this->db->get();
		$result_row = $result->row_array();
		$item_id = $result_row['item_id'];
		
		if(count($product_tags)>0)
		{
			$this->load->model('Tag');
			$this->Tag->save_tags_for_item($item_id, $pos_tags);
		}
		
		if (isset($woo_product['images'][0]) && $woo_product['images'][0]['id'])
		{
			foreach($woo_product['images'] as $woo_image)
			{
		    $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
				$extension = strtolower(pathinfo(basename($woo_product['images'][0]['src']), PATHINFO_EXTENSION));

		    if (in_array($extension, $allowed_extensions))
		    {
			    $config['image_library'] = 'gd2';
			    $config['source_image']	= $woo_image['src'];
			    $config['create_thumb'] = FALSE;
			    $config['maintain_ratio'] = TRUE;
			    $config['width']	 = 400;
			    $config['height']	= 300;
			    $this->load->library('image_lib', $config); 
			    $this->image_lib->resize();
		   	 	$this->load->model('Appfile');
	
			    $image_file_id = $this->Appfile->save(basename($woo_image['src']), file_get_contents($woo_image['src']));
					$this->Item->add_image($item_id, $image_file_id);
					$this->Item->link_image_to_ecommerce($image_file_id, $woo_image['id']);
	  			$this->Item->save_image_metadata($image_file_id, $woo_image['name'],$woo_image['alt']);
					
		    }
			}
		}
		
		$cron_job_entry=lang('woo_cron_job_entry');
		$inventory_array = array('trans_items' => $item_id,'trans_user'=>1,'trans_comment'=>$cron_job_entry,'trans_inventory'=> $product_quantity,'location_id'=>$this->ecommerce_store_location);
		$this->load->model('Inventory');
		$this->Inventory->insert($inventory_array);
		
		$location_item_array = array('item_id'=>$item_id,'location_id'=>$this->ecommerce_store_location,'quantity'=>$product_quantity);
		$this->load->model('Item_location');
		$this->Item_location->save($location_item_array, $item_id, $this->ecommerce_store_location);
				
	}
	
	public function unlink_item($item_id)
	{
		$this->log(lang("unlink_item").": ".$item_id);
		
		if ( $this->is_configured() == false ) 
		{
			return FALSE;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		$this->load->model('Item');
		$item_info = $this->Item->get_info($item_id);
		//Delete this woo
		$woo_id = $item_info->ecommerce_product_id;

		if ($woo_id)
		{
			//Delete
			try
			{
				$woocommerce->delete("products/$woo_id", array('force' => true));
				parent::unlink_item($item_id);
			}
			catch(Exception $e)
			{
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
			}
		}
		else
		{
			parent::unlink_item($item_id);
		}
	}
	
	public function unlink_items($item_ids)
	{
		$this->log(lang("unlink_item").": ".var_export($item_ids, TRUE));
		
		if ( $this->is_configured() == false ) 
		{
			return FALSE;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
	
		$this->load->model('Item');
		$items = $this->Item->get_multiple_info($item_ids)->result_array();

		$woo_items = array();
		
		foreach($items as $item)
		{
			if ($item['ecommerce_product_id'])
			{
				$woo_items[] = $item['ecommerce_product_id'];
			}
		}
		
		$woo_items_chunked = array_chunk($woo_items,$this->woo_write_chunk_size);
		
		foreach($woo_items_chunked as $woo_items_chunk)
		{
			$woo_response = NULL;
			
			try
			{
				$woo_response = $woocommerce->post('products/batch', array('delete' => $woo_items_chunk));
				
				sleep($this->woo_write_sleep);
			}
			catch(Exception $e)
			{
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
				$item_ids = array();
			}
		}
		parent::unlink_items($item_ids);
	}
	
	public function unlink_all()
	{
		$this->log(lang("unlink_all"));
		
		if ( $this->is_configured() == false ) 
		{
			return FALSE;
		}
		set_time_limit(0);
		
		require_once APPPATH.'models/MY_Woo.php';
		
		$woocommerce	=	new MY_Woo($this,$this->woo_store_url,$this->woo_api_key,$this->woo_api_secret,array('wp_api' => true,'version' => 'wc/v'.$this->woo_api_version, 'timeout' => $this->woo_curl_timeout));
		
		$this->load->model('Item');
		$items = $this->Item->get_all()->result_array();

		$woo_items = array();
		
		foreach($items as $item)
		{
			if ($item['ecommerce_product_id'])
			{
				$woo_items[] = $item['ecommerce_product_id'];
			}
		}
		
		$woo_items_chunked = array_chunk($woo_items,$this->woo_write_chunk_size);
		
		foreach($woo_items_chunked as $woo_items_chunk)
		{
			$woo_response = NULL;
			
			try
			{
				$woo_response = $woocommerce->post('products/batch', array('delete' => $woo_items_chunk));
				
				sleep($this->woo_write_sleep);
			}
			catch(Exception $e)
			{
				$this->log("*******".lang('common_EXCEPTION').": ".var_export($e->getMessage(),TRUE));
				$woo_response = NULL;
			}
		}
		
		if ($woo_response !== NULL)
		{
			parent::unlink_all();
		}
	}
		
	function import_ecommerce_tags_into_phppos()
	{
		//Get tags with NO cache to force a full fetch
		$woo_tags = $this->get_all_tags(FALSE);
		
		//Save new tags
		$this->load->Model('Tag');
		foreach($woo_tags as $tag_name => $tag_id)
		{
			if (!$this->Tag->tag_name_exists($tag_name))
			{
					$phppos_tag_id = $this->Tag->save(ucwords(strtolower($tag_name)));
					$this->db->replace('ecommerce_tags', array('tag_id' => $phppos_tag_id, 'ecommerce_tag_id' => $tag_id));
			}
		}
	}
	
	function import_ecommerce_categories_into_phppos()
	{
		//Get categories with NO cache to force a full fetch
		$woo_cats = $this->get_all_categories_and_sub_categories(FALSE);
		
		//Save new categories
		$this->load->model('Category');
		$categories_indexed_by_name = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_name_key();
		
		foreach(array_keys($woo_cats) as $category_path)
		{
			$this->Category->create_categories_as_needed($category_path,$categories_indexed_by_name);
		}
		
		//Do another fetch to re-cache
		$this->get_all_categories_and_sub_categories(FALSE);
	}
}
?>