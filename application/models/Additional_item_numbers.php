<?php
class Additional_item_numbers extends CI_Model
{
	/*
	Returns all the item numbers for a given item
	*/
	function get_item_numbers($item_id)
	{
		$this->db->from('additional_item_numbers');
		$this->db->where('item_id',$item_id);
		return $this->db->get();
	}
	
	function get_all()
	{
		$this->db->from('additional_item_numbers');
		
		$return = array();
		
		foreach($this->db->get()->result_array() as $result)
		{
			$return[$result['item_id']][] = $result['item_number'];
		}
		
		return $return;
	}
	
	function save($item_id, $additional_item_numbers)
	{
		$this->db->trans_start();

		$this->db->delete('additional_item_numbers', array('item_id' => $item_id));
		
		foreach($additional_item_numbers as $item_number)
		{
			if ($item_number!='')
			{
				$this->db->insert('additional_item_numbers', array('item_id' => $item_id, 'item_number' => $item_number));
			}
		}
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}
	
	function delete($item_id)
	{
		return $this->db->delete('additional_item_numbers', array('item_id' => $item_id));
	}
	
	function cleanup()
	{
		$addit_items_table = $this->db->dbprefix('additional_item_numbers');
		$items_table = $this->db->dbprefix('items');
		return $this->db->query("DELETE FROM $addit_items_table WHERE item_id IN (SELECT item_id FROM $items_table WHERE deleted = 1)");
	}
	
	function get_item_id($item_number)
	{
		$this->db->from('additional_item_numbers');
		$this->db->where('item_number',$item_number);

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->row()->item_id;
		}
		
		return FALSE;
	}
}
?>
