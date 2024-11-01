<?php

class sbm_deposit_type {
	
	private function sbm_create_deposit_type()
	{
		global $wpdb;
		// Insert a record into the sbm_meta table
		$wpdb->insert(  $wpdb->prefix."sbm_meta", array(  'meta_key'=> 'deposit_type' ), array(  '%s' ) );
		
		return $wpdb->insert_id;	
		
	}
		
	public function sbm_get_deposit_type_data($id)
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

	
	public function sbm_get_deposit_type_meta()
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
	
	public function sbm_delete_deposit_type($id)
	{
		global $wpdb;
		$query = "DELETE FROM ".$wpdb->prefix."sbm_meta WHERE ID = '$id' ";
			$wpdb->query($query);
		
	
		// redirect
		//  general_functions.php:     sbm_redirect()
		sbm_redirect('sbm_company_profile', 'delete_deposit_type');
	

	}
	
	public function sbm_update_deposit_type()
	{
		global $wpdb;
		global $current_user;
		$deposit_type_info = new sbm_deposit_type();
		
		 get_currentuserinfo($current_user->ID);
		
		if( $_POST['ID'] == 'new' )
		{
			$id = $deposit_type_info->sbm_create_deposit_type();
			
		}
		else
		{
			$id = $_POST['ID'];
		}
		

						
						foreach($_POST as $key => $list)
						{
							
							switch($key)
							{
								case 'ID'; 
								break;
								default;
									$item[] = $list;
								break;
							}
						}
						
						$implode = implode("|", $item);
						
						
						$deposit_data 						= array();
						$type 								= array();
						$deposit_data['meta_value'] 		= $implode;
						
						$type[] 							= '%s';
						$type[] 							= '%d';

						
							
					$wpdb->update( $wpdb->prefix."sbm_meta", $deposit_data, array( 'ID' => $id ), $type, array( '%d' ) );
					// redirect user back to the view deposit type list page
					//  general_functions.php:     sbm_redirect()
					sbm_redirect('sbm_view_deposit_type_list', 'success_deposit_type');
						
	}
	
	
	
}


?>