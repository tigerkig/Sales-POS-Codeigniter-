<?php
class Item_serial_number extends CI_Model
{

	function get_all($item_id)
	{
		$this->db->from('items_serial_numbers');
		$this->db->where('item_id',$item_id);
		$this->db->order_by('id');
		
		return $this->db->get();
	}
	
	function save($item_id, $serial_numbers, $serial_number_prices = array())
	{
		$this->db->trans_start();
		
		$data = array_combine($serial_numbers, $serial_number_prices);
		
		$this->delete($item_id);
		
		foreach($data as $serial_number => $unit_price)
		{
			if ($serial_number != '')
			{
				if($unit_price === '')
				{
					$unit_price = NULL;
				}
				$this->add_serial($item_id, $serial_number, $unit_price);
			}
		}
		
		$this->db->trans_complete();
		
		return TRUE;
	}
	
	function get_price_for_serial($serial_number)
	{
		$this->db->from('items_serial_numbers');
		$this->db->where('serial_number',$serial_number);
		$row = $this->db->get()->row_array();
		
		if (isset($row['unit_price']) && $row['unit_price'] !== NULL)
		{
			return $row['unit_price'];
		}
		
		return FALSE;
	}
	/*
	Deletes one item
	*/
	function delete($item_id)
	{		
		return $this->db->delete('items_serial_numbers', array('item_id' => $item_id));
	}
	
	function delete_serial($item_id, $serial_number)
	{
		return $this->db->delete('items_serial_numbers', array('item_id' => $item_id, 'serial_number' => $serial_number));		
	}
	
	function add_serial($item_id, $serial_number, $unit_price = NULL)
	{
		return $this->db->insert('items_serial_numbers', array('item_id' => $item_id, 'serial_number' => $serial_number, 'unit_price' => $unit_price));
	}
	
	function get_item_id($serial_number)
	{
		$this->db->from('items_serial_numbers');
		$this->db->where('serial_number',$serial_number);

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->row()->item_id;
		}
		
		return FALSE;
	}
	
}
?>
