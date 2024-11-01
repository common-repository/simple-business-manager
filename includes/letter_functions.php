<?php

// Letter Generator

function sbm_get_current_version_number( $letter_id )
{
	global $wpdb;
	$query = "SELECT
					version 
				FROM 
					".$wpdb->prefix."sbm_letter_content
				WHERE 
					letter_id = '$letter_id' 
				
				ORDER BY 
					version DESC
				LIMIT 0,1";
	$num = $wpdb->get_var($wpdb->prepare($query));
	return $num;
	
}
function sbm_get_current_letter_content_id( $letter_id )
{
	global $wpdb;
	$query = "SELECT
					ID 
				FROM 
					".$wpdb->prefix."sbm_letter_content
				WHERE 
					letter_id = '$letter_id' 
				
				ORDER BY 
					version DESC
				LIMIT 0,1";
	$num = $wpdb->get_var($wpdb->prepare($query));
	return $num;
	
}
//edit, remove, verify delete, view list
function sbm_edit_letter(){

	global $wpdb;
	global $current_user;
	
	$info = new sbm_letter();

     get_currentuserinfo($current_user->ID);
	
	$info->sbm_get_letter_data( $_GET['letter_content_id'] );
	


	
	if($_GET['status'] == 'new')
	{
		// Insert a record into the sbm_meta table
		$wpdb->insert(  $wpdb->prefix."sbm_letter", array(  'visible'=> 1 ), array(   '%d' ) );
		$letter_id = $wpdb->insert_id;		
	}
	else if(!empty($_GET['letter_content_id']))
	{
		$letter_content_id = $_GET['letter_content_id'];
		$letter_id = $info->letter_id;
	}
	else
	{
		echo '<h2>This page was reached in error</h2>';
		die();
	}
date_default_timezone_set(sbm_get_timezone());
echo '<div class="wrap">';


		if( ( isset( $_POST['letter_id'] ) ) && ( !isset( $_POST['verify_activate'] ) )  && ( !isset( $_POST['verify_delete']) ) )
		{			
			
			$errors = array();
			
			
			// If this is new, all the fields are required
			if($_GET['status'] == 'new')
			{
				if(empty($_POST['title']))
				{
					$errors[] = 'You forgot the title.';
				}
				if(empty($_POST['content']))
				{
					$errors[] = 'You forgot the content.';
				}
				
			}
			if(empty($errors))
			{
				$info->sbm_update_letter();
				die('Should go to update letter here');
			}
			else
			{
				echo '<div class="error">';
				echo '<div style="font-weight: bold;">Errors found</div>';
				foreach($errors as $list)
				{
					echo '<div class="errorDiv">&nbsp;&nbsp;'.$list.'</div>';
				}
				echo '</div>';
			}
		}

	// If this is new, we need this section
	if ( ($_GET['status'] != 'delete') && ($_GET['status'] != 'activate') )
	{
		if(empty($info->visible))
		{
			$visible = 1;
		}
		
		// Get the current version, then add one to get the  next version number
		$version = sbm_get_current_version_number( $info->letter_id ) + 1;
		
		
				echo '<h2>Letter: '.$info->title.'</h2>';
				
				echo '
                <form id="letter_form" method="post">
<table id="letter_generator">
      <tbody><tr>
      <td colspan="2"> Letter Name: <input type="text" class="required" value="' . sbm_sticky_input( $_POST['letter_name'], $info->title ) . '" maxlength="100" size="50" name="title"></td>
    </tr>
    <tr>
      <td id="left_side">
      	<textarea class="required" id="letterArea" name="content">' . sbm_sticky_input( $_POST['letter_content'], $info->content ) . '</textarea>
	  </td>
      <td id="right_side">
      
          <!--button id="LOGO: LEFT" class="letterInputButton button-primary">[LOGO: LEFT]</button>
          
          <button id="LOGO: CENTER" class="letterInputButton button-primary" >[LOGO: CENTER]</button>
          
          <buttonid="LOGO: RIGHT" class="letterInputButton button-primary" >[LOGO: RIGHT]</button -->

          <button id="DATE" class="letterInputButton button-primary">[DATE]</button>
		            
          <button id="COMPANY NAME" class="letterInputButton button-primary">[COMPANY NAME]</button>
		  
          <button id="FIRST NAME" class="letterInputButton button-primary">[FIRST NAME]</button>
          
          <button id="LAST NAME" class="letterInputButton button-primary">[LAST NAME]</button>
          
          <button id="ADDRESS" class="letterInputButton button-primary">[ADDRESS]</button>
		  
          <button id="ADDRESS 2" class="letterInputButton button-primary">[ADDRESS 2]</button>
                    
          <button id="CITY" class="letterInputButton button-primary">[CITY]</button>
          
          <button id="STATE/PROVINCE" class="letterInputButton button-primary">[STATE/PROVINCE]</button>
          
          <button id="ZIP" class="letterInputButton button-primary">[ZIP]</button>
		  
          <button id="PHONE" class="letterInputButton button-primary">[PHONE]</button>
		  
          <button id="EMAIL" class="letterInputButton button-primary">[EMAIL]</button>
		  
          <button id="HOURLY RATE" class="letterInputButton button-primary">[HOURLY RATE]</button>
		  
          <button id="TAX RATE" class="letterInputButton button-primary">[TAX RATE]</button>
          
          
       </div>
      </td>
    </tr>
    </table>';

				echo '<div><input type="hidden" name="visible" size="1" value="'.$visible.'"></div>';
				echo '<div><input type="hidden" name="version" size="1" value="'.$version.'"></div>';
				echo '<div><input type="hidden" name="letter_id" size="2" value="'.$letter_id.'"></div>';
				
				echo '<div class="float-left medium-padding">';
				//  general_functions.php:     sbm_check_read_only_user()	
				if(  sbm_check_read_only_user() == false  )
				{
					echo '<span><input type="submit" value="Submit" id="submit_letter"></span>';
					//  general_functions.php:     sbm_ok_to_delete()
					if( ( $_GET['status'] != 'new' ) && ( sbm_ok_to_delete('letter_id',$letter_id, 'no') == true ) )
					{
						echo '<span><input type="button" value="Delete this Letter" onclick="sbm_verifyDeleteLetter('.$id.');"></span>';
					}
				}
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_view_home_page', 'cancel');
			echo '</div><div class="clear"><div>';
			
			//  help_functions.php:     sbm_display_help()
			echo sbm_display_help( 'letter' );
			
			
	}
			// End New section

			// If this is Delete then use this section
	if($_GET['status'] == 'delete')
	{			
		
		
		if(isset($_POST['verify_delete']))
		{
				
			$info->sbm_delete_letter($_POST['letter_id']);
			die('The attempt to delete this letter failed, please contact customer services');
			
		}
		 
		$info->sbm_get_letter_data($_GET['letter_content_id']);
	
		echo '<div class="wrap">';
		//  general_functions.php:     sbm_ok_to_delete()
		if ( sbm_ok_to_delete('letter_id',$info->letter_id, 'no') == false )
		{
			if(!empty($_GET['letter_content_id']))
			{
				echo '<h2>Hide/Disable Letter: '.$info->title.'</h2>';
				
				echo '<div>Hiding this letter will prevent it from showing up in lists where you select a letter to send to a customer</div>';
				echo '<form method="post">
							<input type="hidden" name="verify_delete" value="true">';
				echo '<div><input type="hidden" name="disable" value="true"></div>';
				echo '<div><input type="hidden" name="letter_id" value="'.$info->letter_id.'"></div>';
				echo '<div class="float-left medium-padding">';
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_view_transaction_type_list', 'cancel');
				echo '<input type="submit" value="Disable/Hide">';
				echo '</div>';
	
			}
			else
			{
				echo '<h2>You need to select a letterbefore you can use this page</h2>';
			}
		}
		else if ( sbm_ok_to_delete('letter_id',$info->letter_id, 'no') == true )
		{
			if(!empty($_GET['letter_content_id']))
			{
				echo '<h2>Delete Letter: '.$info->title.'</h2>';
				
				echo '<div>Deleting this letter will remove all data associated with this letter</div>';
				echo '<form method="post">
							<input type="hidden" name="verify_delete" value="true">';
				echo '<div><input type="hidden" name="letter_id" size="2" value="'.$info->letter_id.'"></div>';
				echo '<div class="float-left medium-padding">';
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_view_transaction_type_list', 'cancel');
				echo '<input type="submit" value="Delete">';
				echo '</div>';
	
			}
			else
			{
				echo '<h2>You need to select a letterbefore you can use this page</h2>';
			}
		}
		
	}


// Activate an old letter that was hidden/deleted
			// If this is Delete then use this section
	if($_GET['status'] == 'activate')
	{			
		
		if(isset($_POST['verify_activate']))
		{
				
			$info->sbm_activate_letter($_POST['letter_id']);
			die('The attempt to activate this letter failed, please contact customer services');
			
		}
		 
		$info->sbm_get_letter_data($_GET['letter_content_id']);
	
		echo '<div class="wrap">';
		//  general_functions.php:     sbm_ok_to_delete()
		sbm_ok_to_delete('letter_id',$_GET['letter_content_id'], 'yes');
		
		if(!empty($_GET['letter_content_id']))
		{
			echo '<h2>Activate Letter: '.$info->title.'</h2>';
			
			echo '<div>Activate this letter will restore it and you will be able to use it to send letters to customers</div>';
			echo '<form method="post">
						<input type="hidden" name="verify_activate" value="true">';
			echo '<div><input type="hidden" name="letter_id" size="2" value="'.$info->letter_id.'"></div>';
			echo '<div class="float-left medium-padding">';
			//  general_functions.php:     sbm_cancel_button()
			echo sbm_cancel_button('sbm_view_transaction_type_list', 'cancel');
			echo '<input type="submit" value="Activate">';
			echo '</div>';
	
		}
		else
		{
			echo '<h2>You need to select a letterbefore you can use this page</h2>';
		}
	}
			echo '</form>';
			echo '<div id="output_div"></div>';
		echo '</div>';

}
function sbm_remove_abandoned_letter()
{
	global $wpdb;
	
	//$wpdb->query( "DELETE FROM ".$wpdb->prefix."sbm_letter WHERE title = '' AND content = '' " );
	//$wpdb->query( "DELETE FROM ".$wpdb->prefix."sbm_letter_content WHERE title = '' AND content = '' " );

}

function sbm_view_letter_list()
{
	global $wpdb;
	global $current_user;
	$info = new sbm_letter();
	date_default_timezone_set('America/Chicago');


     get_currentuserinfo($current_user->ID);
	$user_info =  get_currentuserinfo($current_user->ID);
	echo '<div class="wrap">';
	
	if(!empty($_GET['message']))
	{
			//  general_functions.php:     sbm_get_message()
			//  general_functions.php:     sbm_message_details() 
			//  general_functions.php:     sbm_clear_notice() 
		
			echo '<div id="message" class="success">'.sbm_get_message($_GET['message']).'</div>';
			// call the function that will remove the success div after 5 seconds
			sbm_clear_notice('message', '5');

	}
	

	if( ( !empty( $_GET['show_hidden'] ) ) && ( ( $_GET['show_hidden'] == 'true' ) ) )
	{
		$visible 								= 0;
		$main_message 					= 'Hidden / disabled templates';
		$delete_activate 					= 'activate';
		$link_to_different_version 	= '<a href="admin.php?page=sbm_view_letter_list">Click here to view only active templates</a>';
		$no_results_message 		= 'You do not have any letters that are hidden/disabled!';
	}
	else
	{
		$visible 								= 1;
		$main_message 					= 'Letters templates';
		$delete_activate 					= 'delete';
		$link_to_different_version 	= '<a href="admin.php?page=sbm_view_letter_list&show_hidden=true">Click here to view only hidden/deleted templates</a>';
		$no_results_message 		= 'You do not have any letters, please use the link below to add them!';
	}
	echo '<h2>' . $main_message . ' </h2>';	
	
	echo '<div style="float: right;">' . $link_to_different_version . '</div><div style="clear: right;">&nbsp;</div>';
	
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
											visible = '$visible'
										GROUP BY ".$wpdb->prefix."sbm_letter.ID";
										
			$result = $wpdb->get_results($query);
			if(count($result) > 0)
			{
					echo '<table id="bw_table" style="width: 800px;"><tr><td></td><td>Title</td><td>Version</td><td>Modified Date</td></tr>';
			
			
				$bg = 'even_bg'; // Start even
				foreach ($result as $list) {
					$letter_content_id = sbm_get_current_letter_content_id( $list->letter_id );
					$info->sbm_get_letter_data($letter_content_id);
					
						switch($bg)
						{
							case 'even_bg'; $bg = 'odd_bg';
							break;
							case 'odd_bg'; $bg = 'even_bg';
							break;
							default;
								$bg = 'current_bg';
							break;
						}
						
						
						echo '<tr class="'.$bg.'">';
						
						echo '<td>';
						 
						//  general_functions.php:     sbm_check_read_only_user()	
						if(  sbm_check_read_only_user() == false  )
						{
							// We dont need this to show up as an option, since it is disabled, they should have to activate it before they use it.
							if ( $_GET['show_hidden'] != 'true' )
							{
								echo '<a href="admin.php?page=sbm_edit_letter&letter_content_id=' . $letter_content_id . '">Edit</a> | ';
							}
							
							//  general_functions.php:     sbm_ok_to_delete()
							if ( ( sbm_ok_to_delete('letter_id',$list->letter_id, 'no') == true ) || ( $_GET['show_hidden'] == 'true' ) )
							{
								echo '<a href="admin.php?page=sbm_edit_letter&status=' . $delete_activate . '&letter_content_id=' . $letter_content_id . '">' . ucfirst($delete_activate) . '</a>';
							}
							else if ( sbm_ok_to_delete('letter_id',$list->letter_id, 'no') == false )
							{
								echo '<a href="admin.php?page=sbm_edit_letter&status=delete&letter_content_id=' . $letter_content_id . '">Disable/Hide</a>';
							}
							
							// We dont need this to show up as an option, since it is disabled, they should have to activate it before they use it.
							if ( $_GET['show_hidden'] != 'true' )
							{
								echo '&nbsp;|&nbsp;<a href="admin.php?page=sbm_generate_letter&letter_content_id=' . $letter_content_id . '">Send</a>';
							}
						}
						echo '</td>';
						echo '<td>' . $info->title . '</td>';
						
						echo '<td>' . sbm_get_current_version_number( $list->letter_id ) . '</td>';
						echo '<td>' . date("m/d/Y \@ h:i:s A", $info->modified_date) . '</td>';
						echo '</tr>';
				}
					echo '</table>';
					
					
			}
			else
			{
				echo '<h3>' . $no_results_message . '</h3>';
			}
	echo '<div class="clear"></div><div class="float-left medium-padding">';
	//  general_functions.php:     sbm_cancel_button()
	echo sbm_cancel_button('sbm_view_home_page', 'cancel');
	//  general_functions.php:     sbm_check_read_only_user()	
	if( sbm_check_read_only_user() == false ) 
	{
		echo '<span><input type="button" value="Add a Letter" onclick="javascript: window.location = \'./admin.php?page=sbm_edit_letter&status=new\';"></span>';
	}
	echo '</div>';
	echo '</div>';
}

function sbm_generate_letter()
{
	global $wpdb;
	global $current_user;
	date_default_timezone_set('America/Chicago');
	$letter_info = new sbm_letter();
	

     get_currentuserinfo($current_user->ID);
	
	// this function removes all the old letters that have been created until this point.
	//  letter_functions.php:     sbm_remove_letters()  
	sbm_remove_letters();
	
	if(!empty($_GET['letter_content_id']))
	{
		$letter_info->sbm_get_letter_data($_GET['letter_content_id']);
	}
	echo '<div class="wrap">';
	
	echo '<form id="letter_pdf" method="post">';
	
	if(isset($_POST['customer_id']))
	{
		$errors = array();
		
		// get the letter_id, and get the current version
		if(empty($_POST['letter_id']))
		{
			$errors[] = 'You forgot the letter you want to use!';
		}
		else
		{
			$letter_id = $_POST['letter_id'];
			$letter_content_id = sbm_get_current_letter_content_id( $letter_id );
			
		}
		
		// this is the common thread that ties all the letters for this submition together, they all share this time
		$sent_date = time();
		
		
		if(empty($errors))
		{
			
			// Enter the information into the database
		
			foreach($_POST['customer_id'] as $list)
			{
				
				
				// Nothing should happen here, we are using ajax to get and store the information
				// ajax.php:     functions/ajax.php
				
			}
			
			// If no errors were found, ouput the PDF
			//  general_functions.php:     sbm_get_message()
			//  general_functions.php:     sbm_message_details() 
			//  general_functions.php:     sbm_clear_notice() 

        //echo '<div id="message" class="success">All Letters were logged and the PDF should have been loaded for you to save</div>';
        // call the function that will remove the success div after 5 seconds
        //sbm_clear_notice('message', '10');
	
		die();
		}
		else
		{
			//  general_functions.php:     sbm_get_message()
			//  general_functions.php:     sbm_message_details() 
			//  general_functions.php:     sbm_clear_notice() 

        echo '<div id="message" class="error">There were errors, please fix them.</div>';
        // call the function that will remove the success div after 5 seconds
        sbm_clear_notice('message', '5');
		}
	}
	
	
	if (empty($_GET['letter_content_id']))
	{
		echo '<h2>Step 1, choose the letter you want to sent</h2>';
		echo '<select id="letter_id" name="letter_id" class="required"><option selected="selected" value="">-- Select One --</option>'. $letter_info->sbm_show_letter_as_select() .'</select>';
	}
	else
	{
		echo '<h2>Step 1, choose the letter you want to sent ( Already Selected )</h2>';
		echo '<select id="letter_id" name="letter_id" class="required"><option value="">-- Select One --</option>'. $letter_info->sbm_show_letter_as_select( $letter_info->letter_id) .'</select>';

	}
	echo '<h2>Step 2, select the customers who will receive this letter</h2>';
	echo '<div>customer Status <select id="customer_status" name="customer_status">
												<option value="Current" selected="selected">-- Select One --</option>
									
												<option value="all">List all customers</option>
											</select>
										</div>';
									
	echo '<div id="show_results_div"></div><div id="button"><input type="submit" value="Submit Not Available" disabled=true id="submit_button"></div>';
	echo '</form>';
	echo '<div><input type="hidden" id="sent_date" value="' . time() . '"></div>';
	echo '<div id="download_pdf_div" style="display: none;" class="button_decoration">PLEASE WAIT, PDF LOADING</div>
		  
		  <div id="quick_view_div" style="display: none;" class="button_decoration"><a href="javascript: void(0);" id="quick_view_document"><img src="' . SBM_PLUGIN_URL . '/images/quick_view.png"></a></div>
		  <div id="reset" style="display: none;" class="button_decoration"><a href="./admin.php?page=sbm_generate_letter" id="reset"><img src="' . SBM_PLUGIN_URL . '/images/reset.png"></a></div>
		  <div id="quick_view" class="clear"></div>';
	echo '</div>';
}

function sbm_count_letters()
{
	global $wpdb;
	global $current_user;

     get_currentuserinfo($current_user->ID);
				$query = "SELECT 
							COUNT(*)
							FROM 
								".$wpdb->prefix."sbm_letter 
							 ";
				$count = $wpdb->get_var($wpdb->prepare($query));
	
	return $count;
}


function sbm_display_letter( $letter_content_id, $sent_date, $customer_id )
{
	if(empty($customer_id))
	{
		die( 'No customer ID' );	
	}
	global $wpdb;
	$letter_info 			= new sbm_letter();
	$customer_info 			= new sbm_customer();
	
	// This will take the customer id, and create the letter that replace the [FIRST NAME], etc into a formated page 
	$letter_info->sbm_get_letter_data( $letter_content_id );
	
	$customer_info->sbm_get_customer_data( $customer_id );
	// customer Balance
	$customer_info->sbm_get_customer_balance( $customer_id );
	//sbm_pre_array( $customer_info );
	$string 				= $letter_info->content;
	
	$patterns 				= array();

	$patterns[0] 			= '/\[DATE\]/';
	$patterns[1] 			= '/\[COMPANY NAME\]/';
	$patterns[2] 			= '/\[FIRST NAME\]/';
	$patterns[3] 			= '/\[LAST NAME\]/';
	$patterns[4] 			= '/\[ADDRESS\]/';
	$patterns[5] 			= '/\[ADDRESS 2\]/';
	$patterns[6] 			= '/\[CITY\]/';
	$patterns[7] 			= '/\[STATE\/PROVINCE\]/';
	$patterns[8] 			= '/\[ZIP\]/';
	$patterns[9] 			= '/\[PHONE\]/';
	$patterns[10] 			= '/\[EMAIL\]/';
	$patterns[11] 			= '/\[BALANCE\]/';
	$patterns[12] 			= '/\[HOURLY RATE\]/';
	$patterns[13] 			= '/\[TAX RATE\]/';
	$patterns[14] 			= '/\\n/';
	$patterns[15] 			= '/\\r/';
	
	$replacements 			= array();
	$replacements[0] 		= date("m/d/Y", $sent_date);
	$replacements[1] 		= $customer_info->company_name;
	$replacements[2] 		= $customer_info->first_name_1;
	$replacements[3] 		= $customer_info->last_name_1;
	$replacements[4] 		= $customer_info->address;
	$replacements[5] 		= $customer_info->address_2;
	$replacements[6] 		= $customer_info->city;
	$replacements[7] 		= $customer_info->state;
	$replacements[8] 		= $customer_info->zip;
	$replacements[9] 		= $customer_info->phone;
	$replacements[10] 		= $customer_info->email_1;
	$replacements[11] 		= $customer_info->balance;
	$replacements[12] 		= $customer_info->hourly_rate;
	$replacements[13] 		= $customer_info->tax_rate;
	$replacements[14] 		= '<br>';
	$replacements[15] 		= '<br>';
	
	
	return preg_replace($patterns, $replacements, $string);	
}


function sbm_remove_letters()
{
	
	$uploads = wp_upload_dir();
	$uploadDir = $uploads['basedir'].'/simple-business-manager/';
	$uploadURL = $uploads['baseurl'].'/simple-business-manager/';
	
		if ($handle = opendir( $uploadDir )) {
			while (false !== ($file = readdir($handle))) {
				if ( $file != "." && $file != ".." && $file != ".DS_Store" && $file != ".svn" ) {					
					
					 unlink( $uploads['basedir'].'/simple-business-manager/'. $file );
					
				}
			}
			closedir($handle);
		}

}


?>