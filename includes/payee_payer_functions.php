<?php

function sbm_payee_payer_options()
{
	
	global $current_user;
	
       get_currentuserinfo($current_user->ID);
	
		echo '
			<div class="wrap">';
				if(!empty($_GET['message']))
				{
					//  general_functions.php:     sbm_get_message()
					//  general_functions.php:     sbm_message_details() 
					//  general_functions.php:     sbm_clear_notice() 
					
					echo '<div id="message" class="success">'.sbm_get_message($_GET['message']).'</div>';
					// call the function that will remove the success div after 5 seconds
					sbm_clear_notice('message', '5');
				}

			echo '<h2>(payee_payer Options) What do you want to do?</h2>';

			echo '<div><a href="admin.php?page=sbm_view_payee_payer_list">View/Edit payee_payers</a></div>';
			//  general_functions.php:     sbm_check_read_only_user()	
			if( sbm_check_read_only_user() == false ) 
			{
				echo '<div><a href="admin.php?page=sbm_edit_payee_payer&status=new">Add a payee_payer</a></div>';
			}
			echo '
					
					<div><a href="admin.php?page=sbm_view_payee_payer_account">payee_payer Accounts</a></div>';
		echo '</div>';

}

function sbm_get_payee_payer_as_option( $payee_payer_id )
{
    global $wpdb;
    global $current_user;


      get_currentuserinfo($current_user->ID);
    $user_info 	=  get_currentuserinfo($current_user->ID);


    $query = "SELECT
                GROUP_CONCAT( meta_key, '|', meta_value ) as payee_payer_data,
                    payee_payer_id
                FROM
                    " . $wpdb->prefix . "sbm_payee_payer,
                    " . $wpdb->prefix . "sbm_payee_payer_meta
                WHERE

                    " . $wpdb->prefix . "sbm_payee_payer.ID = " . $wpdb->prefix . "sbm_payee_payer_meta.payee_payer_id
                AND
                    visible = 1
                GROUP BY
                    payee_payer_id";

    $payee_payer_list 	= $wpdb->get_results($query);

    $result = array();

    if( count($payee_payer_list) == 0 )
    {
        $content = '<option value=""> -- No Payee/Payer have been setup -- </option>';
    }
    else
    {
        $content = '<option value=""> -- Select One -- </option>';

                // If the payee_payer ID was not used as a display option use this
                foreach( $payee_payer_list as $list )
                {
                    // Break up the comma delimited
                    $explode1 = explode( ',', $list->payee_payer_data );

                    foreach( $explode1 as $meta_content )
                    {
                        // now explode each result to seperate the information that is pipe delimited
                        $explode2 = explode('|', $meta_content );

                        $result[ $list->payee_payer_id ][ $explode2[0] ] 	= $explode2[1];

                    }
                        if( $list->payee_payer_id == $payee_payer_id )
                        {
                            $selected = 'selected="selected"';
                        }
                        else
                        {
                            $selected = '';
                        }
                        // $result[ $list->payee_payer_id ][ 'payee_payer_status' ]
                        $content .= '<option ' . $selected . ' value="' . $list->payee_payer_id . '">' . $result[ $list->payee_payer_id ][ 'payee_payer_name' ] . ' &nbsp;&nbsp;</option>';

                }
    }

	return $content;
}


function sbm_get_the_payee_payer_data( $payee_payer_id, $bg  )
{
	
	global $current_user;
	
	$payee_payer_info 	= new sbm_payee_payer();
	
	
	
	$letter_info 	= new sbm_letter();
	
       get_currentuserinfo($current_user->ID);
	$user_info =  get_currentuserinfo($current_user->ID);
					
					
						//  classes/sbm_payee_payer.php:     sbm_get_payee_payer_data()
						$payee_payer_info->sbm_get_payee_payer_data( $payee_payer_id);
						//  classes/sbm_payee_payer.php:     sbm_get_payee_payer_balance() 
						$payee_payer_info->sbm_get_payee_payer_balance( $payee_payer_id);
						
						$list_of_attr = array( 'payee_payer_name', 'address', 'city', 'state', 'zip', 'main_phone', 'contact' );
						
						foreach( $list_of_attr as $list )
						{
							
							if(!empty( $payee_payer_info->$list ))
							{
								$$list = $payee_payer_info->$list;
							}
							else
							{
								$$list = NULL;
							}
						}
					
						
						$content = '<tr class="'.$bg.' ">';
						$content .= '<td>' .  $payee_payer_info->payee_payer_status . '</td><td><a href="admin.php?page=sbm_view_payee_payer_account&payee_payer_id=' . $payee_payer_id . '">View Account</a></td>';
						$content .= '<td>';
						//  general_functions.php:     sbm_check_read_only_user()	
						if(  sbm_check_read_only_user() == false  )
						{
							$text = 'Edit';
						}
						else
						{
							$text = 'View';
						}
							$content .= '<a href="admin.php?page=sbm_edit_payee_payer&id=' . $payee_payer_id . '">' . $text . '</a>';
						//  general_functions.php:     sbm_check_read_only_user()	
						//  general_functions.php:     sbm_ok_to_delete()
						if(  ( sbm_check_read_only_user() == false  ) &&  (sbm_ok_to_delete('payee_payer_id',$payee_payer_id, 'no') == true ) )
						{
							$content .= '| <a href="admin.php?page=sbm_edit_payee_payer&status=delete&id=' . $payee_payer_id . '">Delete</a>';
						}
						$content .= '</td>';
						$content .= '<td>' . $payee_payer_name . '</td>';
						$content .= '<td>' . $address . '</td>';
						$content .= '<td>' . $city . ' ' . $state . ' ' . $zip . '</td>';
						$content .= '<td>' . $main_phone . '</td>';
						$content .= '<td>' . $contact . '</td>';
						$content .= '<td>$';
							$balance = $payee_payer_info->balance;
							if ($balance < 0 ) 
							{
								$content .= '<span class="red">'.  number_format(str_replace('-', '', $balance), 2) . '</span>';
							}
							if ($balance >= 0 ) 
							{
								$content .= '<span class="green">' . number_format($balance, 2) . '</span>';
							}
							
						$content .= '</td>';
						
						$content .= '</tr>';
	
	return $content;
}


function sbm_view_payee_payer_list()
{
	
	global $wpdb;
	global $current_user;

	
      get_currentuserinfo($current_user->ID);
	$user_info 	=  get_currentuserinfo($current_user->ID);

				
	$query = "SELECT 
				GROUP_CONCAT( meta_key, '|', meta_value ) as payee_payer_data,
					payee_payer_id
				FROM
					" . $wpdb->prefix . "sbm_payee_payer,		
					" . $wpdb->prefix . "sbm_payee_payer_meta		
				WHERE 
					
					" . $wpdb->prefix . "sbm_payee_payer.ID = " . $wpdb->prefix . "sbm_payee_payer_meta.payee_payer_id
				AND
					visible = 1
				GROUP BY 
					payee_payer_id";			
				
	$payee_payer_list 	= $wpdb->get_results($query);
	
	$result = array();
	
        if (!empty($_GET['filter_by_status']))
        {
           
            $filter_by_status = $_GET['filter_by_status'];
            
        }
        else
        {
            $filter_by_status = 'Current';
        }
		
 
			// If the payee_payer ID was not used as a display option use this    
			foreach( $payee_payer_list as $list )
			{
				// Break up the comma delimited
				$explode1 = explode( ',', $list->payee_payer_data );
				
				foreach( $explode1 as $meta_content )
				{
					// now explode each result to seperate the information that is pipe delimited
					$explode2 = explode('|', $meta_content );
								
					$result[ $list->payee_payer_id ][ $explode2[0] ] 	= $explode2[1];
					
				}
						
				if ( ($filter_by_status == 'All') || ( $filter_by_status == $result[ $list->payee_payer_id ][ 'payee_payer_status' ] ) )
				{	// My list of sortable fields for this table
					$id[ $list->payee_payer_id ]				= $list->payee_payer_id;
					$payee_payer_name[ $list->payee_payer_id ] 	= $result[ $list->payee_payer_id ][ 'payee_payer_name' ];
					$payee_payer_status[ $list->payee_payer_id ]	= $result[ $list->payee_payer_id ][ 'payee_payer_status' ];
					
				 }
					
			 
			  }
				
			
			

		//  general_functions.php:     sbm_check_read_only_user()	
		if(  sbm_check_read_only_user() == false  )
		{
			$text = 'Charge/Credit';
		}
		else
		{
			$text = '';
		}
	
		if( (!empty( $_GET['sort_by'])) )
        {
            $sort_by 	= $_GET['sort_by'];
        }
        else
        {
			$sort_by = 'payee_payer_name';
        }
		
        if((!empty($_GET['order_by'])))
        {
            $order_by	= $_GET['order_by'];
	
            if($order_by == 'ASC')
            {
                $toggle_order_by = 'DESC';
            }
            else
            {
                $toggle_order_by = 'ASC';
            }

        }
        else
        {
            $order_by = 'ASC';
        }
        
        $bg     = '';
        $output = '';
		
	switch( $sort_by )
	{
		case 'payee_payer_name';
			if( count( $payee_payer_name ) >= 1 )
			{
				foreach( sbm_sort_list( $payee_payer_name, $order_by ) as $key => $list )
				{
					$bg = sbm_get_bg( $bg );
					
					$output .= sbm_get_the_payee_payer_data( $key, $bg );
				}
			}
			else
			{
				$output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';	
			}
			$payee_payer_name_order_by = $toggle_order_by;
            $payee_payer_name_arrow = 'arrow-' . $order_by;
		break;
		case 'payee_payer_status';
			if( count( $payee_payer_status ) >= 1 )
			{
				foreach( sbm_sort_list( $payee_payer_status, $order_by ) as $key => $list )
				{
					$bg = sbm_get_bg( $bg );
					
					$output .= sbm_get_the_payee_payer_data( $key, $bg );
				}
			}
			else
			{
				$output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';	
			}
			$payee_payer_status_order_by = $toggle_order_by;
            $payee_payer_status_arrow = 'arrow-' . $order_by;
		break;
		default;
			if( count( $payee_payer_name ) >= 1 )
			{
				foreach( sbm_sort_list( $payee_payer_name, $order_by ) as $key => $list )
				{
					$bg = sbm_get_bg( $bg );
					
					$output .= sbm_get_the_payee_payer_data( $key, $bg );
				}
			}
			else
			{
				$output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';	
			}
			$payee_payer_name_order_by = $toggle_order_by;
            $payee_payer_name_arrow = 'arrow-' . $order_by;
		break;
	}
	
	// If the order by have not been set use ASC
	if( empty( $payee_payer_name_order_by ) )
	{
		$payee_payer_name_order_by = 'ASC';
	}
	
	
	if( empty( $payee_payer_status_order_by ) )
	{
		$payee_payer_status_order_by = 'ASC';
	}
	
        
        switch( $filter_by_status )
        {
            case 'All';
                $filter_by_status_all           = 'selected="selected"'; 
                $filter_by_status_prospective   = '';
                $filter_by_status_current       = '';                
                $filter_by_status_past          = '';                
                
            break;
           case 'Current';
                $filter_by_status_current       = 'selected="selected"'; 
                $filter_by_status_prospective   = '';
                $filter_by_status_all           = '';                
                $filter_by_status_past          = '';                
                
            break;
           case 'Past';
                $filter_by_status_past          = 'selected="selected"'; 
                $filter_by_status_current       = '';
                $filter_by_status_all           = '';
                $filter_by_status_prospective   = '';
            break;
           case 'Prospective';
                $filter_by_status_prospective   = 'selected="selected"'; 
                $filter_by_status_current       = '';
                $filter_by_status_all           = '';
                $filter_by_status_past          = '';
            break;
            default;
                $filter_by_status_all           = 'selected="selected"'; 
                $filter_by_status_prospective   = '';
                $filter_by_status_current       = '';                
                $filter_by_status_past          = '';                
            break;
        }
        
        
                        echo '<label for="payee_payer_filter_view_payee_payer_list" class="larger_label">Filter by payee_payer status</label><select name="payee_payer_filter_view_payee_payer_list" id="payee_payer_filter_view_payee_payer_list">
                                <option ' . $filter_by_status_all . ' value="All">Show All payee_payers</option> 
                                <option ' . $filter_by_status_current . ' value="Current">Show only Current payee_payers</option>
                                <option ' . $filter_by_status_past . ' value="Past">Show only Past payee_payers</option>
                                <option ' . $filter_by_status_prospective . ' value="Prospective">Show only Prospective payee_payers</option>
                                </select>';
			echo '<br /><br />';
                        
                       
	if(!empty($_GET['message']))
	{
			//  general_functions.php:     sbm_get_message()
			//  general_functions.php:     sbm_message_details() 
			//  general_functions.php:     sbm_clear_notice() 
		
			echo '<div id="message" class="success">'.sbm_get_message($_GET['message']).'</div>';
			// call the function that will remove the success div after 5 seconds
			sbm_clear_notice('message', '5');

	}
                      ?>
                      	    <div class="clear">
			<input type="button" value="Add a New Payee / Payer" onclick="javascript: window.location = './admin.php?page=sbm_edit_payee_payer&status=new';">
		</div>

                      <table id="bw_table" style="width: 1000px;">
					<tr>
						<td><a href="admin.php?page=sbm_view_payee_payer_list&sort_by=payee_payer_status&order_by=<?php echo $payee_payer_status_order_by; ?>&filter_by_status=<?php echo $filter_by_status; ?>"><div class="float-left"></div><div class="<?php echo $payee_payer_status_arrow; ?>"></div>Status</a></td>
						<td>View Account</td>
						<td>Edit/Delete</td>
						<td><a href="admin.php?page=sbm_view_payee_payer_list&sort_by=payee_payer_name&order_by=<?php echo $payee_payer_name_order_by; ?>&filter_by_status=<?php echo $filter_by_status; ?>"><div class="float-left"><div class="<?php echo $payee_payer_name_arrow; ?>"></div></div>Payee Payer Name</a></td>
						<td>Address</td>
						<td>City, State. Zip</td>
						<td>Phone</td>
						<td>Contact</td>
						<td>Balance</td>
					</tr>
	<?php
			echo $output;
	?>		
			</table>
	    <div class="clear">
			<input type="button" value="Add a New Payee / Payer" onclick="javascript: window.location = './admin.php?page=sbm_edit_payee_payer&status=new';">
		</div>
	<?php		
	

	exit();
	
}

function sbm_edit_payee_payer()
{

	global $wpdb;
	global $current_user;
	
	$payee_payer_info = new sbm_payee_payer();
     get_currentuserinfo($current_user->ID);
	
	
	if($_GET['status'] == 'new')
	{
		
	}
	else if(!empty($_GET['id']))
	{
		$id = $_GET['id'];
	}
	else
	{
		// take the user to the view payee_payer list
		//  general_functions.php:     sbm_redirect()
		sbm_redirect('sbm_view_payee_payer_list');
		//echo '<h2>This page was reached in error</h2>';
		die();
	}
echo '<div class="wrap">';

		if (isset($_POST['payee_payer_id']))
		{
			if(isset($_POST['verify_delete']))
			{
				$payee_payer_info->sbm_delete_payee_payer($_POST['payee_payer_id']);
				die('Delete payee_payer, if this is visible please contact payee_payer support');
			}
			
			
			$errors = array();
			
			
			// If this is new, all the fields are required
			if($_GET['status'] == 'new')
			{
				if(empty($_POST['payee_payer_name']))
				{
					$errors[] = 'You forgot the first name.';
				}
				if(empty($_POST['payee_payer_status']))
				{
					$errors[] = 'You forgot the status of this payee payer: Current,  Past or Prospective';
				}
				
																	 
																	   
																	 
			}
			if(empty($errors))
			{
				
				$payee_payer_info->sbm_update_payee_payer();
				die('');
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
	if ($_GET['status'] != 'delete')
	{
			//  classes/sbm_payee_payer.php:     sbm_get_payee_payer_data()
			$payee_payer_info->sbm_get_payee_payer_data($id);
				echo '<h2>Payee / Payer '.$payee_payer_info->payee_payer_name.'</h2>';
				echo '<form method="post" id="editPayeePayerForm">';
				
				echo '<fieldset style="border: 1px solid #000; padding: 10px; ">';
				
				echo '<div>What is the status of this payee_payer <select id="selected_payee_payer_status" name="payee_payer_status">';
				if(isset($_POST['payee_payer_status']))
				{
					// The submit button has been used but there were errors, select that option for the user again
					switch($_POST['payee_payer_status'])
					{
						case 'Current'; $Current = 'selected="selected"';
						break;
						case 'Past'; $Past = 'selected="selected"';
						break;
						case 'Prospective'; $Prospective = 'selected="selected"';
						break;
						default;
						break;
					}
				}
				else
				{
					if(!empty($_GET['id']))
					{
						// This is an existing payee_payer, and the submit button has not been used
					switch($payee_payer_info->payee_payer_status)
					{
						case 'Current'; $Current = 'selected="selected"';
						break;
						case 'Past'; $Past = 'selected="selected"';
						break;
						case 'Prospective'; $Prospective = 'selected="selected"';
						break;
						default;
						break;
					}
					}
					else
					{
						// This must be a new payee_payer and the submit button has not been used.
						$Current = 'selected="selected"';
					}
				}
				
				echo '<option '.$Current.' value="Current">Current</option>';
				echo '<option '.$Past.' value="Past">Past</option>';
				echo '<option '.$Prospective.' value="Prospective">Prospective</option>';
				
				
				echo '</select></div>';
				echo '<div><input type="hidden" id="payee_payer_id" name="payee_payer_id" value="'.$id.'"></div>';
				
				
				if(!empty($payee_payer_info->unit_id ))
				{
					
					
					if( $payee_payer_info->payee_payer_status == 'Past' )
					{
						echo '<h3>Previous Information: </h3>';
					}
					else if ( $payee_payer_info->payee_payer_status == 'Current' )
					{
						echo '<h3>Current Information: </h3>';
					}
					else
					{
						// This should not appear if the choice is prospective...
						
					}
					
					
				}
				?>
				
				
				<div id="payee_payer_information_edit" class="float-left">
                    <h2>Payee / Payer Information</h2>
                    <div>
                        <label for="payee_payer_name">Payee Payer Name<em>*</em></label> 
                        <input type="text" id="payee_payer_name" class="required" name="payee_payer_name" size="20" value="<?php echo sbm_sticky_input($_POST['company_name'], $payee_payer_info->payee_payer_name); ?>">
                    </div>
                    <div>
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" size="20" value="<?php echo sbm_sticky_input($_POST['address'], $payee_payer_info->address); ?>">
                    </div>
                    <div>
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" size="20" value="<?php echo sbm_sticky_input($_POST['city'], $payee_payer_info->city); ?>">
                    </div>
                    <div>
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" size="2" value="<?php echo sbm_sticky_input($_POST['state'], $payee_payer_info->state); ?>">
                    </div>
                    <div>
                        <label for="zip">Zip</label>
                        <input type="text" id="zip" name="zip" size="20" value="<?php echo sbm_sticky_input($_POST['zip'], $payee_payer_info->zip); ?>">
                    </div>
                    <div>
                        <label for="main_phone">Main Phone:</label>
                        <input type="text" name="main_phone" size="16" maxlength="13" value="<?php echo sbm_sticky_input($_POST['main_phone'], $payee_payer_info->main_phone); ?>">
                    </div>
                    <div>
                        <label for="fax">Contact:</label>
                        <input type="text" name="contact" size="16" maxlength="13" value="<?php echo sbm_sticky_input($_POST['contact'], $payee_payer_info->contact); ?>">
                    </div>
				
				</div>
				
				<div class="clear"></div>
				<div class="float-left medium-padding">
                <?php
				//  general_functions.php:     sbm_check_read_only_user()	
				if(  sbm_check_read_only_user() == false  )
				{
						echo '<span><input type="submit" value="Submit" id="editpayeePayerSubmitButton"></span>';
						//  general_functions.php:     sbm_ok_to_delete()
					if( ( $_GET['status'] != 'new' ) && ( sbm_ok_to_delete('payee_payer_id',$id, 'no') == true ) )
						{
							echo '<span><input type="button" value="Delete this payee_payer" onclick="sbm_verifyDeletepayee_payer('.$id.');"></span>';
						}
				}
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_payee_payer_profile', 'cancel');
				
			//  help_functions.php:     sbm_display_help()
			echo sbm_display_help( 'payee_payer' );
			?>
			</div>
			<?php		
	}
			// End New section

			// If this is Delete then use this section
	if($_GET['status'] == 'delete')
	{		
		
			
			if(isset($_POST['verify_delete']))
			{
					
				$payee_payer_info->sbm_delete_payee_payer($_GET['id']);
				die('The attempt to delete this property failed, please contact payee_payer services');
				
			}
			 		//  classes/sbm_payee_payer.php:     sbm_get_payee_payer_data()
					$payee_payer_info->sbm_get_payee_payer_data($_GET['id']);
			echo '<div class="wrap">';
			// check to see if we can even delete this
			//  general_functions.php:     sbm_ok_to_delete()
			sbm_ok_to_delete('payee_payer_id', $_GET['id'], 'yes');
			
			if(!empty($_GET['id']))
			{
				echo '<h2>Delete payee_payer '.$payee_payer_info->first_name_1.' '.$payee_payer_info->last_name_1.'</h2>';
				echo '<div>Deleting this payee_payer will remove all data associated with this payee_payer</div>';
				echo '<form method="post">
							<input type="hidden" name="verify_delete" value="true">';
				echo '<div class="float-left medium-padding">';
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_view_payee_payer_list', 'cancel');
				echo '<input type="submit" value="Delete">';
				echo '</div>';
				echo '</form>';
				
			}
			else
			{
				echo '<h2>You need to select a payee_payer before you can use this page</h2>';
			}
			
	}
			// end Delete
			echo '</fieldset>';
			echo '</form>';
			echo '<div id="output_div"></div>';
		echo '</div>';

	
	
}


function sbm_view_payee_payer_account()
{
	
	/*
	*
	*		TODO:
	*		The line item as they are displayed are editable, however after you edit that amount, you can not re-edit that value.
	*		Need to figure out why this is the case and fix it so you can edit that amount over and over again until your fingers bleed
	*
	*/
	
	
	
	global $wpdb;
	global $current_user;
	
	$payee_payer_info = new sbm_payee_payer();
	$odometer_info = new sbm_odometer();
	
	
	

		
       get_currentuserinfo($current_user->ID);
	
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
	
	$payee_payer_id = $_GET['payee_payer_id'];
	
	

	$query = "SELECT ID FROM ".$wpdb->prefix."sbm_payee_payer WHERE ID = $payee_payer_id";
	$payee_payer_list = $wpdb->get_results($query);
	
			if(count($payee_payer_list) > 0)
			{
					echo '<h2>Payee/Payer Account information</h2>';
					echo '<input type="hidden" id="payee_payer_id" value="' . $payee_payer_id . '">';
					echo '<table class="bw_table" style="width: 900px;"><tr><th></th></tr><tr><td>Status</td><td>Name</td><td>Address</td><td>City</td><td>State</td><td>Zip</td><td>Balance</td></tr>';
				
				$bg = 'even_bg'; // Start even
				foreach ($payee_payer_list as $list) {
					
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
					
						//  classes/sbm_payee_payer.php:     sbm_get_payee_payer_data()
						$payee_payer_info->sbm_get_payee_payer_data($list->ID);
						//  classes/sbm_payee_payer.php:     sbm_get_payee_payer_balance()
						$payee_payer_info->sbm_get_payee_payer_balance($list->ID);
						
						echo '<tr class="'.$bg.'">';
						echo '<td>' . $payee_payer_info->payee_payer_status . '</td>';
                        echo '<td>' . $payee_payer_info->payee_payer_name . '</td>';
                        echo '<td>' . $payee_payer_info->address . '</td>';
                        echo '<td>' . $payee_payer_info->city . '</td>';
                        echo '<td>' . $payee_payer_info->state . '</td>';
                        echo '<td>' . $payee_payer_info->zip . '</td>';
                        echo '<td>' . $payee_payer_info->balance . '</td>';
						echo '</tr>';
					
				}
				echo '</table>';
				echo '<br>&nbsp;<br>';
				
				




	$query = "SELECT
				    ID
		        FROM
					" . $wpdb->prefix . "sbm_odometer
				WHERE
					payee_payer_id = '$payee_payer_id'
			    AND
					visible = 1";

	$odometer_list 	= $wpdb->get_results($query);




						if(count($odometer_list) > 0)
						{
							echo '<table id="odometer_history" class="bw_table" style="width: 900px; margin-top: 4px;">
                                                                <th colspan="6">Odometer History</th>';
							echo '<tr><td>Trip Date</td><td>Starting Miles</td><td>Ending Miles</td><td>Total Miles</td><td>Destination</td></tr>';
							$bg = 'even_bg'; // Start even
			// If the odometer ID was not used as a display option use this
			foreach( $odometer_list as $list )
			{
				$odometer_info->sbm_get_odometer_data( $list->ID );

            
								
									switch($bg)
									{
										case 'even_bg'; 
											$bg 	= 'odd_bg';
										break;
										case 'odd_bg'; 
											$bg 	= 'even_bg';
										break;
										default;
											$bg 	= 'current_bg';
										break;
									}
								
                                    $ending_miles 		= $odometer_info->ending_miles;
                                    $starting_miles 	= $odometer_info->starting_miles;
                                    $total_miles 		= $odometer_info->total_miles;
                                    $destination 		= $odometer_info->destination;
                                    $trip_date          = $odometer_info->trip_date;


									echo '<tr class="' . $bg . '">';
                                    echo '<td>' . $trip_date . '</td>';
                                    echo '<td>' . $starting_miles . '</td>';
                                    echo '<td>' . $ending_miles . '</td>';
                                    echo '<td>' . $total_miles . '</td>';
                                    echo '<td>' . $destination . '</td>';
									echo '</tr>';
								
								// Reset all values
                                                            $ending_miles    = '';
                                                            $starting_miles    = '';
                                                            $total_miles   = '';
                                                            $destination   = '';
															$trip_date = '';
							}
								
							
						}
						else
						{
							echo '<br><br>';
							echo '<table class="bw_table" style="width: 900px;"><th colspan="4">Odometer History</th>';
							echo '<tr><td colspan="4">This payee/payer does not have any odometer history</td></tr>';
							
						}
						
						echo '</table>';
						
						echo '<br>&nbsp;<br>';

	echo '<div style="float: left;" class="float-left medium-padding">';
	echo '<form>';
			echo '<span><input type="button" value="Edit this payee_payer" onclick="self.location=\'admin.php?page=sbm_edit_payee_payer&id=' . $payee_payer_id . '\'"></span>';

	//  general_functions.php:     sbm_cancel_button()
	echo sbm_cancel_button('sbm_payee_payer_profile', 'cancel');
	
	echo '</form>';
	
	echo '</div>';
	echo '<div id="output"></div>';
	echo '</div>';
			}
			else
			{
				//  general_functions.php:     sbm_redirect()
				sbm_redirect('sbm_view_payee_payer_list');
			}
	
}






function sbm_count_payee_payers()
{
	global $wpdb;
	global $current_user;
		
				$query = "SELECT 
											COUNT(*)
										FROM 
											".$wpdb->prefix."sbm_payee_payer 
										 ";
				$count = $wpdb->get_var($wpdb->prepare($query));
	
	return $count;
}
function sbm_get_payee_payer_status($id)
{
	global $wpdb;
			$query = "SELECT 
								meta_value
							FROM 
								".$wpdb->prefix."sbm_meta
							WHERE 
								meta_key = 'payee_payer_status'
							
							AND 
								payee_payer_id = $id";
			$result = $wpdb->get_var($wpdb->prepare($query));
			
	return $result;
}
?>