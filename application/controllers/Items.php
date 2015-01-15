<?php
class Items extends CI_Controller {

   function __construct()
   {
       parent::__construct();
       include 'parent_construct.php';
   }

   function add()
   {
       $this->load->library('form_validation');
       $this->form_validation->set_error_delimiters('<div class="">', '</div>');
       
       $this->form_validation->set_rules('item_type_id', 'Item Type', "required");
       $this->form_validation->set_rules('item_name', 'Item Name', "required");
       
       
       if($this->input->post('has_subitem') == 'on')
       {
       		$this->form_validation->set_rules('unit_id', 'Unit', "required");
       		$this->form_validation->set_rules('item_code', 'Item Code', "required");
       		
       		// Service
       		if($this->input->post('item_type_id') == 1) // 1 = Service
       		{  			
       			if($this->input->post('options_service') == 'on')
       			{
       				$this->form_validation->set_rules('purchase_cost', 'Cost', "required");
       				$this->form_validation->set_rules('expense_account', 'Expense', "required");       				
       				$this->form_validation->set_rules('price', 'Price', "required");
       				$this->form_validation->set_rules('income_accounts', 'Income', "required");    				
       			}
       			else 
       			{
       				$this->form_validation->set_rules('price', 'Price', "required");
       				$this->form_validation->set_rules('income_accounts', 'Income', "required");
       			}       			
       		}
       		elseif($this->input->post('item_type_id') == 2) // 2 = Inventory Part
       		{
       			$this->form_validation->set_rules('purchase_cost', 'Cost', "required");
       			$this->form_validation->set_rules('cogs_account', 'COGS', "required");
       			$this->form_validation->set_rules('price', 'Price', "required");
       			$this->form_validation->set_rules('income_accounts', 'Income', "required");       			
       			$this->form_validation->set_rules('asset_account', 'Asset Account', "required");       			
       			$this->form_validation->set_rules('reorder_level', 'Reorder Level', "required");
       		
       		}
       		elseif($this->input->post('item_type_id') == 3) // 3 = Inventory Assembly
       		{
       			
       			$this->form_validation->set_rules('cogs_account', 'COGS', "required");
       			$this->form_validation->set_rules('price', 'Price', "required");
       			$this->form_validation->set_rules('income_accounts', 'Income', "required");
       			$this->form_validation->set_rules('asset_account', 'Asset Account', "required");
       			$this->form_validation->set_rules('reorder_level', 'Reorder Level', "required");
       			$this->form_validation->set_rules('on_hand', 'On Hand', "required");
       			$this->form_validation->set_rules('total_value', 'Total Value', "required");
       			
       			$product_id = $this->input->post('product_id');
       			$cost = $this->input->post('cost');
       			$quantity = $this->input->post('quantity');
       			$total = $this->input->post('total');
       			
       			if(count($product_id) > 0)
       			{
       				$error = 0;
       				for($i = 0; $i < count($product_id) ; $i++)
       				{
       					if($product_id[$i] != "")
       					{
	       					$error += ( empty($cost[$i])? 1 : 0) ;
	       					$error += ( empty($quantity[$i])? 1 : 0) ;
	       					$error += ( empty($total[$i])? 1 : 0) ;
       					}	
       				}
       				if($error > 0)
       				{
       					$this->form_validation->set_rules('bill_of_materials', 'Total Value', 'callback_customRule' );       					
       				}
       				
       			}       			
       		}       		
       		elseif($this->input->post('item_type_id') == 4) // 1 = // Non Inventory
       		{
       			if($this->input->post('options_non_inventory') == 'on')
       			{
       				$this->form_validation->set_rules('purchase_cost', 'Cost', "required");
       				$this->form_validation->set_rules('cogs_account', 'COGS', "required");
       				$this->form_validation->set_rules('price', 'Price', "required");
       				$this->form_validation->set_rules('income_accounts', 'Income', "required");
       			}
       			else
       			{
       				$this->form_validation->set_rules('office_supply_price', 'Price', "required");
       				$this->form_validation->set_rules('office_supply_accounts', 'Accounts', "required");
       			}
       		}  			
       }   
       
       if ($this->form_validation->run() == FALSE) 
       {
			
	       	if( $this->input->is_ajax_request() )
	       	{
	       		echo validation_errors();       		
	       	}
	       	else
	       	{
	       		$this->load->model('dropdown_items');       		 
	       		$data['dropdown_item_types']= $this->dropdown_items->create_dropdown('cx_item_types', 'item_type_id', 'item_type_name', 'Select an item type' );
	       		$data['dropdown_items']	= $this->dropdown_items->create_dropdown('cx_items', 'item_id', 'item_name', 'None' );
	       		$data['dropdown_units']	= $this->dropdown_items->create_dropdown('cx_units', 'unit_id', 'unit_name', 'Select an unit' );	       		
	       		$data['all_accounts_head']	= $this->dropdown_items->create_dropdown('cx_account_heads', 'acc_id', 'account_name', 'Select an account' );
	       		$data['cogs_accounts'] = $this->dropdown_items->get_account_heads( $this->config->item('cogs_group_id') );
	       		$data['income_accounts'] = $this->dropdown_items->get_account_heads( $this->config->item('income_group_id') );
	       		$data['assets_accounts'] = $this->dropdown_items->get_account_heads( $this->config->item('asset_group_id') );
	       		$data['expense_accounts'] = $this->dropdown_items->get_account_heads( $this->config->item('expense_group_id') );       		 
	       		 
	       		$data['page_title'] = 'Add items' ;
	       		$data['main_content'] = 'items/view_add' ;
	       		$this->load->view('includes/template', $data);
	       	}    	
       		
       } 
       else 
       { 
       		$this->load->model('mod_items');
       		$this->mod_items->add();
       	    echo "Successfully Added" ;      
       } 
   }

   function show()
   {
       $this->load->model('mod_items');
       $data['records']= $this->mod_items->get_all();
       $data['page_title'] = 'List of items' ;
       $data['main_content'] = 'items/view_show' ;
       $this->load->view('includes/template', $data);
   }

   function edit($id)
   {
       $this->load->library('form_validation');
       $this->form_validation->set_error_delimiters('<span class="error">', '</span>'); 

       if ($this->form_validation->run() == FALSE) 
       { 

	       	$this->load->model('mod_items');
	       	$data['records'] = $this->mod_items->get_single_row($id);
            $data['page_title'] = 'Edit items' ;
            $data['main_content'] = 'items/view_edit' ;
            $this->load->view('includes/template', $data);
       } 
       else 
       { 
       		$this->load->model('mod_items');
       		$response = $this->mod_items->edit();
       	   if ($response) 
           { 

       			$base = base_url().'items/show'; 
       			$data['type']='alert alert-success'; 
       			$data['msg']='Successfully Edited'; 
            } 
           else 
           { 
       			$base = base_url().'items/show'; 
       			$data['type']='alert alert-danger'; 
       			$data['msg']='Could not perform the requested action'; 
            } 
            $data['page_title'] = 'Edit items' ;
            $data['main_content'] = 'view_success' ;
            $this->load->view('includes/template', $data);
       } 
   }

   function delete_item()
   {
       $id = $this->input->post('id');
       $this->load->model('mod_items');
       echo $response= $this->mod_items->delete_item($id);
   }
   
   
   public function customRule()
   {
	   	$this->form_validation->set_message('customRule', 'Fill up the Bill of Materials Form Correctly');
	   	return FALSE;
   }

}