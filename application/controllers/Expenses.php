<?php

require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");

class Expenses extends Secure_area implements Idata_controller {

    function __construct() {

        parent::__construct('expenses');
		  $this->load->model('Expense');
		  $this->load->model('Category');
  			$this->lang->load('expenses');
  			$this->lang->load('module');		
		  
    }

    function index($offset = 0) {
        $params = $this->session->userdata('expenses_search_data') ? $this->session->userdata('expenses_search_data') : array('offset' => 0, 'order_col' => 'id', 'order_dir' => 'desc', 'search' => FALSE);

        if ($offset != $params['offset']) {
            redirect('expenses/index/' . $params['offset']);
        }

        $this->check_action_permission('search');
        $config['base_url'] = site_url('expenses/sorting');
        $config['total_rows'] = $this->Expense->count_all();
        $config['per_page'] = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
        $data['controller_name'] = strtolower(get_class());
        $data['per_page'] = $config['per_page'];
        $data['search'] = $params['search'] ? $params['search'] : "";
        if ($data['search']) {
            $config['total_rows'] = $this->Expense->search_count_all($data['search']);
            $table_data = $this->Expense->search($data['search'], $data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        } else {
            $config['total_rows'] = $this->Expense->count_all();
            $table_data = $this->Expense->get_all($data['per_page'], $params['offset'], $params['order_col'], $params['order_dir']);
        }
        $this->load->library('pagination');$this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['order_col'] = $params['order_col'];
        $data['order_dir'] = $params['order_dir'];
        $data['total_rows'] = $config['total_rows'];
        $data['manage_table'] = get_expenses_manage_table($table_data, $this);
        $this->load->view('expenses/manage', $data);
    }

    function sorting() {
        $this->check_action_permission('search');

        $search = $this->input->post('search') ? $this->input->post('search') : "";
        $per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;

        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'id';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';

        $expenses_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("expenses_search_data", $expenses_search_data);

        if ($search) {
            $config['total_rows'] = $this->Expense->search_count_all($search);
            $table_data = $this->Expense->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        } else {
            $config['total_rows'] = $this->Expense->count_all();
            $table_data = $this->Expense->get_all($per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        }
        $config['base_url'] = site_url('expenses/sorting');
        $config['per_page'] = $per_page;
        $this->load->library('pagination');$this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_expenses_manage_table_data_rows($table_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
    }

    function search() {
 		//allow parallel searchs to improve performance.
 		session_write_close();
		 
        $this->check_action_permission('search');

        $search = $this->input->post('search');
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'id';
        $order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc';

        $expenses_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
        $this->session->set_userdata("expenses_search_data", $expenses_search_data);
        $per_page = $this->config->item('number_of_items_per_page') ? (int) $this->config->item('number_of_items_per_page') : 20;
        $search_data = $this->Expense->search($search, $per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'id', $this->input->post('order_dir') ? $this->input->post('order_dir') : 'asc');
        $config['base_url'] = site_url('expenses/search');
        $config['total_rows'] = $this->Expense->search_count_all($search);
        $config['per_page'] = $per_page;
        $this->load->library('pagination');$this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['manage_table'] = get_expenses_manage_table_data_rows($search_data, $this);
        echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
    }

    function clear_state() {
        $this->session->unset_userdata('expenses_search_data');
        redirect('expenses');
    }

    /*
      Gives search suggestions based on what is being searched for
     */

    function suggest() {
 		//allow parallel searchs to improve performance.
 		session_write_close();
		 
        $suggestions = $this->Expense->get_search_suggestions($this->input->get('term'), 100);
        echo json_encode($suggestions);
    }

    function view($expense_id = -1, $redirect_code = 0) {
        $this->check_action_permission('add_update');
        $logged_employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
        $data['expense_info'] = $this->Expense->get_info($expense_id);
        $data['logged_in_employee_id'] = $logged_employee_id;
        $data['all_modules'] = $this->Module->get_all_modules();
        $data['controller_name'] = strtolower(get_class());

        $data['redirect_code'] = $redirect_code;
		  $data['categories'][''] = lang('common_select_category');
		  
		  if ($this->config->item('track_cash'))
		  {
	  			$data['registers'] = array();
				$data['registers'][''] = lang('common_none');
			  
			  foreach($this->Register->get_all_open()->result() as $register)
			  {
				  $data['registers'][$register->register_id] = $register->name;
			  }
		  }
		
			$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
			foreach($categories as $key=>$value)
			{
				$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
				$data['categories'][$key] = $name;
			}
				
			$employees = array();
			
			foreach($this->Employee->get_all()->result() as $employee)
			{
				$employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
			}
			
			$data['employees'] = $employees;
		  
      $this->load->view("expenses/form", $data);
    }

    function save($id = -1) 
	 {
			$this->check_action_permission('add_update');		 

			if (!$this->Category->exists($this->input->post('category_id')))
			{
				if (!$category_id = $this->Category->get_category_id($this->input->post('category_id')))
				{
					$category_id = $this->Category->save($this->input->post('category_id'));
				}
			}	
			else
			{
				$category_id = $this->input->post('category_id');
			}
		  
        $expense_data = array(
            'expense_type' => $this->input->post('expenses_type'),
            'expense_description' => $this->input->post('expenses_description'),
				'expense_reason' => $this->input->post('expense_reason'),
            'expense_date' => date('Y-m-d',  strtotime($this->input->post('expenses_date'))),
            'expense_amount' => $this->input->post('expenses_amount'),
            'expense_tax' => $this->input->post('expenses_tax'),
            'expense_note' => $this->input->post('expenses_note'),
            'employee_id' => $this->input->post('employee_id'),
				'approved_employee_id' => $this->input->post('approved_employee_id') ? $this->input->post('approved_employee_id') : NULL,
				'category_id' => $category_id,
            'location_id' => $this->Employee->get_logged_in_employee_current_location_id(),
        );

		  
        if ($this->Expense->save($expense_data, $id)) 
		  {
			  if ($this->input->post('cash_register_id'))
			  {
		  			$amount = to_currency_no_money($this->input->post('expenses_amount') + $this->input->post('expenses_tax'));
					$cash_register = $this->Register->get_register_log_by_id($this->input->post('cash_register_id'));
		  			$cash_register->total_cash_subtractions+=$amount;
		  			$this->Register->update_register_log($cash_register,$this->input->post('cash_register_id'));
								
		  			$employee_id_audit = $this->Employee->get_logged_in_employee_info()->person_id;
				
		  			$register_audit_log_data = array(
		  				'register_log_id'=> $cash_register->register_log_id,
		  				'employee_id'=> $employee_id_audit,
		  				'date' => date('Y-m-d H:i:s'),
		  				'amount' => -$amount,
		  				'note' => lang('common_expenses'). ' - '.$this->input->post('expenses_note'),
		  			);
			
		  			$this->Register->insert_audit_log($register_audit_log_data);
			}
			
         	$redirect = $this->input->post('redirect');
			
			   $success_message = '';
            //New item
            if ($id == -1) 
				{
                $success_message = lang('expenses_successful_adding').' '.$expense_data['expense_type'].' - '.to_currency($this->input->post('expenses_amount'));
                echo json_encode(array('success' => true, 'message' => $success_message, 'id' => $expense_data['id'], 'redirect' => $redirect));
            } else 
				{ //previous item
                $success_message = lang('common_items_successful_updating') . ' ' . $expense_data['expense_type'].' - '.to_currency($this->input->post('expenses_amount'));
                $this->session->set_flashdata('manage_success_message', $success_message);
                echo json_encode(array('success' => true, 'message' => $success_message, 'id' => $id, 'redirect' => $redirect));
            }
        } 
		  else 
		  {//failure
            echo json_encode(array('success' => false, 'message' => lang('expenses_error_adding_updating')));
        }
    }

    function delete() {
        $this->check_action_permission('delete');
        $expenses_to_delete = $this->input->post('ids');
        if ($this->Expense->delete_list($expenses_to_delete)) {

            echo json_encode(array('success' => true, 'message' => lang('expenses_successful_deleted') . ' ' . lang('expenses_one_or_multiple')));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('expenses_cannot_be_deleted')));
        }
    }
}

?>
