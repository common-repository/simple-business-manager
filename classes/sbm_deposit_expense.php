<?php

class sbm_deposit_expense {
		
	public function sbm_get_deposit_expense_data($id)
	{
		global $wpdb;
		
			$query = "SELECT meta_value FROM ".$wpdb->prefix."sbm_meta WHERE ID = '$id'";
			$row = $wpdb->get_row($query);
			
			$pre = $wpdb->prefix;
			
			
				$this->ID 			= $id;
				$pieces 			= explode("|", $row->meta_value);
				$this->name 	= $pieces[0]; // Name
				$this->visible 	= $pieces[1]; // status, visible or not
	}

	
	public function sbm_get_deposit_expense_meta()
	{
		global $wpdb;
		
			$query = "SELECT 
								meta_key, 
								meta_value 
							FROM 
								".$wpdb->prefix."sbm_meta 
							WHERE 
								meta_key = 'deposit_type'
							
								customer_id = '0' 
							ORDER BY 
								ID ASC";
			$meta_list = $wpdb->get_results($query);
	
			// output just one entry box
			if(count($meta_list ) == 0)
			{
				$content ='<tr id="row_0"><td><input type="text" name="meta_key[]" size="20"></td><td><input type="text" name="meta_value[]" size="20"></td><td><a href="javascript: void(0);" id="delete">Delete</a></td></tr>';
			}
			// output all the results
			foreach($meta_list as $key => $list)
			{
				$pieces = explode("|", $list->meta_value);
				/*
					$pieces[0] = Name
					$pieces[1] = status, visible or not
				*/
				
				$content .='<tr id="row_'.$key.'"><td><input type="text" id="meta_name_'.$key.'" name="meta_key[]" size="20" value="'.$pieces[0].'"></td><td><input type="text" id="meta_value_'.$key.'" name="meta_value[]" size="20" value="'.$list->meta_value.'"></td><td><div id="delete_'.$key.'"><a href="javascript: void(0);" onMouseUp="sbm_deleterow('.$key.');" id="delete">Delete</a></div></td></tr>';
			}
			//$wpdb->show_errors();
			//$wpdb->print_error();
		return $content;
	}
	
	public function sbm_delete_deposit_expense($id)
	{
		global $wpdb;
		//$query = "DELETE FROM ".$wpdb->prefix."sbm_meta WHERE ID = '$id' ";
			$wpdb->query($query);
		
	
		// redirect
		//  general_functions.php:     sbm_redirect()
		sbm_redirect('sbm_company_profile', 'delete_deposit_type');
	

	}
	
	public function sbm_update_deposit_expense($noredirect = '')
	{
		global $wpdb;
		
		$transaction_id = sbm_get_transaction_id();
		 
		
		if( !empty($_POST['expense_type_id']) )
		{
			$table 			= $wpdb->prefix."sbm_expenses";
			$type_id 		= 'expense_type_id';
			$type_id_value 	= $_POST['expense_type_id'];
		}
		if( !empty($_POST['deposit_type_id']) )
		{
			$table = $wpdb->prefix."sbm_deposits";
			$type_id = 'deposit_type_id';
			$type_id_value = $_POST['deposit_type_id'];
		}
		$date = sbm_convert_date( $_POST['transaction_date'] );
		
			// Insert a record into the proper table
		$wpdb->insert( $table , array(  
										'transaction_id' =>  $transaction_id, 
										'transaction_date' => $date,
										$type_id => $type_id_value,
										'payee_payer_id' => $_POST['payee_payer_id'],
										'transaction_type_id' => $_POST['transaction_type_id'],
										'amount' => $_POST['amount'],
										'description' => $_POST['description'],
										'check_number' => $_POST['check_number'],
						 			), 
								array(  
										'%s', 
										'%s',
										'%s',
										'%s',
										'%s',
										'%s',
										'%s',
										'%s'
									 ) );
	
					if(empty($noredirect))
					{
						// redirect user back to the view deposit type list page
						//  general_functions.php:     sbm_redirect()
						sbm_redirect('sbm_view_deposit_expense', 'success_deposit_expense');
					}
					else
					{
						return true;
					}	
	}
	
	
	
}


?>