<?php

class sbm_settings {
		
	public function sbm_get_settings_data($id)
	{
		global $wpdb;
		
			$query = "SELECT meta_value FROM ".$wpdb->prefix."sbm_meta WHERE ID = '$id'";
			$row = $wpdb->get_row($query);
			
			$pre = $wpdb->prefix;
			
			
				$this->ID = $id;
				$pieces = explode("|", $row->meta_value);
				$this->name = $pieces[0]; // Name
				$this->visible = $pieces[1]; // status, visible or not
	}

	
	public function sbm_get_settings_meta()
	{
		global $wpdb;
		
			$query = "SELECT 
								meta_key, 
								meta_value 
							FROM 
								".$wpdb->prefix."sbm_meta 
							WHERE 
								meta_key = 'transaction_type'
							
							AND 
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
	
	public function sbm_delete_settings($id)
	{
		global $wpdb;
		$query = "DELETE FROM ".$wpdb->prefix."sbm_meta WHERE ID = '$id' ";
			$wpdb->query($query);
		
	
		// redirect
		//  general_functions.php:     sbm_redirect()
		sbm_redirect('sbm_company_profile', 'delete_transaction_type');
	

	}
	
	public function sbm_update_settings()
	{
						
            foreach ($_POST as $key => $list) 
			{
                // do not do anything if the meta_value is empty
                if (!empty($key)) 
				{
					
					update_option($key, $list);
                }
            }
			//  general_functions.php:     sbm_redirect()
			sbm_redirect('sbm_settings', 'success_update_settings');
			die();
						
	}
	
	
	
}


?>