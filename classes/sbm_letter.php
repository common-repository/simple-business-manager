<?php

class sbm_letter {

	public function sbm_get_total_letters_by_customer( $customer_id )
	{
		global $wpdb;
			$sql = "SELECT 
							COUNT(*)
						FROM 
							".$wpdb->prefix."sbm_sent_letter
						
						WHERE
							customer_id =$customer_id";
			$count=$wpdb->get_var($wpdb->prepare($sql));
		
			$this->total_letters = $count;
		
	}
	public function sbm_show_letter_as_select(  $letter_id = '' )	
	{
		global $wpdb;
		
		
			$query = "SELECT
											".$wpdb->prefix."sbm_letter_content.title,
											".$wpdb->prefix."sbm_letter.ID as letter_id,
											".$wpdb->prefix."sbm_letter_content.ID as letter_content_id,
											".$wpdb->prefix."sbm_letter_content.modified_date
										FROM 
											".$wpdb->prefix."sbm_letter,
											".$wpdb->prefix."sbm_letter_content 
										WHERE
											
											".$wpdb->prefix."sbm_letter.ID = ".$wpdb->prefix."sbm_letter_content.letter_id
										AND
											visible = '1'
										GROUP BY ".$wpdb->prefix."sbm_letter.ID";
										
			$result = $wpdb->get_results($query);
			if(count($result) == 0)
			{
				$content = '<option value="">No letters have been created</option>';
			}
			else
			{
				foreach( $result as $list )
				{
					if( !empty( $letter_id ) )
					{
						if( $letter_id == $list->letter_id )
						{
							$selected = 'selected="selected"';
						}
						else
						{
							$selected = '';
						}
					}
					
					$content .= '<option ' . $selected . ' value="' . $list->letter_id . '">'.$list->title.'</option>';
				}
			}
		
		return $content;
	}


	public function sbm_get_letter_data($letter_content_id)
	{
		global $wpdb;
		
			$query = "SELECT * FROM ".$wpdb->prefix."sbm_letter_content WHERE ID = '$letter_content_id'";
			$row = $wpdb->get_row($query);			
			
				$this->ID = $id;
				
				$this->letter_id = $row->letter_id; // last user id to modify this letter
				$this->modified_date = $row->modified_date; // date this was last modified
				$this->title = $row->title; // title of the letter
				$this->content = $row->content; // content of the letter
				$this->version = $row->version; // version of the letter
	}

	public function sbm_activate_letter( $letter_id )
	{
		global $wpdb;
		$wpdb->update( $wpdb->prefix."sbm_letter", array( 'visible' => 1 ), array( 'ID' => $letter_id, ), array( '%d' ), array( '%d' ) );
		// redirect
		//  general_functions.php:     sbm_redirect()
		sbm_redirect('sbm_view_letter_list', 'activate_letter');
		
	}

	
	public function sbm_delete_letter($id)
	{
		global $wpdb;
		if(!isset($_POST['disable']))
		{
			$query = "DELETE FROM ".$wpdb->prefix."sbm_letter WHERE ID = '$id'";
				$wpdb->query($query);
			$query = "DELETE FROM ".$wpdb->prefix."sbm_letter_content WHERE letter_id = '$id'";
				$wpdb->query($query);
			$msg = 'delete_letter';
		}
		else
		{
			$wpdb->update( $wpdb->prefix."sbm_letter", array('visible' => '0'), array( 'ID' => $_POST['letter_id'] ), array('%d'), array( '%d' ) );
			$msg = 'disable_letter';
		}
		// redirect
		//  general_functions.php:     sbm_redirect()
		sbm_redirect('sbm_view_letter_list', $msg);
	

	}
	
	public function sbm_update_letter()
	{
		global $wpdb;
		global $current_user;
		date_default_timezone_set( sbm_get_timezone() );

		 get_currentuserinfo($current_user->ID);
		
			
				
						
						
						$letter_data 						= array();
						$type 								= array();
						
						$letter_data['title'] 				= $_POST['title'];
						$letter_data['content'] 			= $_POST['content'];
						$letter_data['version']			 	= $_POST['version'];
						$letter_data['letter_id']		 	= $_POST['letter_id'];
						$letter_data['modified_date']		= time();
						
						
						$type[] 							= '%s';
						$type[] 							= '%s';
						$type[] 							= '%d';
						$type[] 							= '%d';
						$type[] 							= '%d';
						$type[] 							= '%d';
						$type[] 							= '%d';
						
							
					$wpdb->update( $wpdb->prefix."sbm_letter", array('visible' => $_POST['visible']), array( 'ID' => $_POST['letter_id'] ), array('%d'), array( '%d' ) );
					$wpdb->insert(  $wpdb->prefix."sbm_letter_content", $letter_data, $type );
					// redirect user back to the view transaction type list page
					//  general_functions.php:     sbm_redirect()
					sbm_redirect('sbm_view_letter_list', 'success_letter');
						
	}
	
	public function sbm_save_letter( $letter_id, $letter_content_id, $customer_id, $sent_date )
	{
		global $wpdb;
		
						$letter_data 						= array();
						$type 								= array();
						
						
						$letter_data['letter_id'] 			= $letter_id;
						$letter_data['letter_content_id']	= $letter_content_id;
						$letter_data['customer_id']			= $customer_id;
						$letter_data['sent_date']			= $sent_date;
						
						$type[] 							= '%s';
						$type[] 							= '%s';
						$type[] 							= '%s';
						$type[] 							= '%s';
						$type[] 							= '%s';
						
							
					$wpdb->insert(  $wpdb->prefix."sbm_sent_letter", $letter_data, $type );
	}
	
	
	
}


?>