<?php

function sbm_odometer_options()
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

			echo '<h2>(odometer Options) What do you want to do?</h2>';

			echo '<div><a href="admin.php?page=sbm_view_odometer_list">View/Edit odometers</a></div>';
			//  general_functions.php:     sbm_check_read_only_user()	
			if( sbm_check_read_only_user() == false ) 
			{
				echo '<div><a href="admin.php?page=sbm_edit_odometer&status=new">Add a odometer</a></div>';
			}
			echo '
					
					<div><a href="admin.php?page=sbm_view_odometer_account">odometer Accounts</a></div>';
			//  general_functions.php:     sbm_check_read_only_user()	
			if( sbm_check_read_only_user() == false ) 
			{

					echo '<div><a href="admin.php?page=sbm_misc_odometer">Misc Charge or Credit to odometer</a></div>';
			}
		echo '</div>';

}


function sbm_get_the_odometer_data( $odometer_id, $bg, $csv = ''  )
{
	
	global $current_user;
	
	$odometer_info 	    = new sbm_odometer();
	$payee_payer_info   = new sbm_payee_payer();

    //$payee_payer        = $payee_payer_info->payee_payer_name;

    get_currentuserinfo($current_user->ID);

    // reset values
    $trip_date      = '';
    $destination    = '';
    $total_miles    = '';
    $starting_miles = '';
    $ending_miles   = '';
    $payee_payer    = '';

						//  classes/sbm_odometer.php:     sbm_get_odometer_data()
						$odometer_info->sbm_get_odometer_data( $odometer_id);
						$payee_payer_id = $odometer_info->sbm_get_odometer_payee_payer_id( $odometer_id );
                        $payee_payer_info->sbm_get_payee_payer_data( $payee_payer_id );
						$list_of_attr = array( 'trip_date', 'destination', 'starting_miles', 'ending_miles', 'total_miles' );
						$payee_payer = $payee_payer_info->payee_payer_name;

						foreach( $list_of_attr as $list )
						{
							
							if(!empty( $odometer_info->$list ))
							{
								$$list = $odometer_info->$list;
							}
							else
							{
								$$list = NULL;
							}
						}
					
						if(empty($csv))
                        {
						    $content = '<tr class="'.$bg.' ">';
                        }
                        if(empty($csv))
                        {
						    $content .= '<td>';
                        }
						//  general_functions.php:     sbm_check_read_only_user()	
						if(  sbm_check_read_only_user() == false  )
						{
                            if(empty($csv))
                            {
							    $text = 'Edit';
                            }
						}
						else
						{
                            if(empty($csv))
                            {
							    $text = 'View';
						    }
                        }
                            if(empty($csv))
                            {
							    $content .= '<a href="admin.php?page=sbm_edit_odometer&id=' . $odometer_id . '">' . $text . '</a>';
                            }
						//  general_functions.php:     sbm_check_read_only_user()	
						//  general_functions.php:     sbm_ok_to_delete()
						if(  ( sbm_check_read_only_user() == false  ) &&  (sbm_ok_to_delete('odometer_id',$odometer_id, 'no') == true ) )
						{
                            if(empty($csv))
                            {
							    $content .= '| <a href="admin.php?page=sbm_edit_odometer&status=delete&id=' . $odometer_id . '">Delete</a>';
						    }
                        }
                            if(empty($csv))
                            {
						        $content .= '</td>';
                            }
                            if(empty($csv))
                            {
                                $content .= '<td>' . $trip_date . '</td>';
                                $content .= '<td>' . $destination . '</td>';
                                $content .= '<td>' . $total_miles . '</td>';
                                $content .= '<td>' . $starting_miles . '</td>';
                                $content .= '<td>' . $ending_miles . '</td>';
                                $content .= '<td>' . $payee_payer . '</td>';
                                $content .= '<td></td>';
                            }
                            else {
                                    $content .= $trip_date . ',';
                                    $content .= $destination . ',';
                                    $content .= $total_miles . ',';
                                    $content .= $starting_miles . ',';
                                    $content .= $ending_miles . ',';
                                    $content .= $payee_payer . ',';

                            }
						    if(empty($csv))
                            {
						        $content .= '</tr>';
                            }
                            else{
                                $content .= '|';
                            }
	
	return $content;
}

function sbm_fix_bad_odometer_data($date, $id)
{
    global $wpdb;


     $query = "UPDATE ".$wpdb->prefix."sbm_odometer SET odometer_date = '" . date( "Y-m-d H:i:s", strtotime($date)) . "' WHERE ID = " . $id;
     $wpdb->query($query);

}

function sbm_count_miles_for_year( $year = '' )
{
    global $wpdb;
    if(empty($year))
    {
        $year = $_POST['year'];
    }
    $early = date("Y-m-d H:i:s", mktime(0,0,0,1,1,$year));
    $late = date("Y-m-d H:i:s", mktime(23,59,59,12,31,$year));

    $query = "SELECT
                GROUP_CONCAT( meta_key, '|', meta_value ) as odometer_data,
                    odometer_id,
                    odometer_date
                FROM
                    " . $wpdb->prefix . "sbm_odometer,
                    " . $wpdb->prefix . "sbm_odometer_meta
                WHERE
                    " . $wpdb->prefix . "sbm_odometer.ID = " . $wpdb->prefix . "sbm_odometer_meta.odometer_id
                AND
                    visible = 1
                AND odometer_date BETWEEN '$early' AND '$late' 
                GROUP BY
                    odometer_id";

    $odometer_list 	= $wpdb->get_results($query);

    $result = array();

     $total = 0;
            // If the odometer ID was not used as a display option use this
            foreach( $odometer_list as $list )
            {
                // Break up the comma delimited
                $explode1 = explode( ',', $list->odometer_data );

                foreach( $explode1 as $meta_content )
                {
                    // now explode each result to seperate the information that is pipe delimited
                    $explode2 = explode('|', $meta_content );

                    $result[ $list->odometer_id ][ $explode2[0] ] 	= $explode2[1];

                }
                    // My list of sortable fields for this table

                     $total += $result[ $list->odometer_id ][ 'total_miles' ];

              }


    return $total;

}
function sbm_view_odometer_list( $just_output = '', $year = '')
{
	
	global $wpdb;
	global $current_user;

	
      get_currentuserinfo($current_user->ID);
	$user_info 	=  get_currentuserinfo($current_user->ID);
    if(!empty($just_output)){
        $csv = 'csv';
    }
    else{
        $csv = null;
    }
    if(empty($year))
    {
        $year = $_GET['year'];
    }


    if((!empty($year)) && ( $year != 'all-years'))
    {
        $early = date("Y-m-d H:i:s", mktime(0,0,0,1,1,$year));
        $late = date("Y-m-d H:i:s", mktime(23,59,59,12,31,$year));
        $limit_by_year = " AND odometer_date BETWEEN '$early' AND '$late' ";
    }
	$query = "SELECT 
				GROUP_CONCAT( meta_key, '|', meta_value ) as odometer_data,
					odometer_id,
					odometer_date,
					payee_payer_id
				FROM
					" . $wpdb->prefix . "sbm_odometer,		
					" . $wpdb->prefix . "sbm_odometer_meta		
				WHERE
					" . $wpdb->prefix . "sbm_odometer.ID = " . $wpdb->prefix . "sbm_odometer_meta.odometer_id
				AND
					visible = 1
			    $limit_by_year
				GROUP BY
					odometer_id";

	$odometer_list 	= $wpdb->get_results($query);
	
	$result = array();
	

			// If the odometer ID was not used as a display option use this    
			foreach( $odometer_list as $list )
			{
				// Break up the comma delimited
				$explode1 = explode( ',', $list->odometer_data );
				
				foreach( $explode1 as $meta_content )
				{
					// now explode each result to seperate the information that is pipe delimited
					$explode2 = explode('|', $meta_content );
								
					$result[ $list->odometer_id ][ $explode2[0] ] 	= $explode2[1];
					
				}
					// My list of sortable fields for this table
					$id[ $list->odometer_id ]			= $list->odometer_id;
					$destination[ $list->odometer_id ] 	= $result[ $list->odometer_id ][ 'destination' ];
					$trip_date[ $list->odometer_id ]	= $list->odometer_date;
					

                
                     if( $list->odometer_date == '0000-00-00 00:00:00' )
                     {
                         sbm_fix_bad_odometer_data($trip_date[ $list->odometer_id ], $list->odometer_id);
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
			$sort_by = 'trip_date';
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
            $order_by = 'DESC';
        }
        
        $bg     = '';
        $output = '';
   
	switch( $sort_by )
	{
		case 'destination';
			if( count( $destination ) >= 1 )
			{
				foreach( sbm_sort_list( $destination, $order_by ) as $key => $list )
				{
					$bg = sbm_get_bg( $bg );

					$output .= sbm_get_the_odometer_data( $key, $bg, $csv );
				}
			}
			else
			{
				$output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';	
			}
			$destination_order_by = $toggle_order_by;
			$destination_arrow = 'arrow-' . $order_by;
		break;
		case 'trip_date';
        
			if( count( $trip_date ) >= 1 )
			{
				foreach( sbm_sort_list( $trip_date, $order_by ) as $key => $list )
				{
					$bg = sbm_get_bg( $bg );
					
					$output .= sbm_get_the_odometer_data( $key, $bg, $csv );
				}
			}
			else
			{
				$output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';	
			}
			$trip_date_order_by = $toggle_order_by;
			$trip_date_arrow = 'arrow-' . $order_by;
		break;
		default;

            if( count( $trip_date ) >= 1 )
            {
                foreach( sbm_sort_list( $trip_date, $order_by ) as $key => $list )
                {
                    $bg = sbm_get_bg( $bg );

                    $output .= sbm_get_the_odometer_data( $key, $bg, $csv );
                }
            }
            else
            {
                $output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';
            }
            $trip_date_order_by = $toggle_order_by;
            $trip_date_arrow = 'arrow-' . $order_by;
        /*
			if( count( $destination ) >= 1 )
			{
				foreach( sbm_sort_list( $destination, $order_by ) as $key => $list )
				{
					$bg = sbm_get_bg( $bg );
					
					$output .= sbm_get_the_odometer_data( $key, $bg, $csv );
				}
			}
			else
			{
				$output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';	
			}
			$destination_order_by = $toggle_order_by;
			$destination_arrow = 'arrow-' . $order_by;
			*/
		break;
	}
	
	// If the order by have not been set use ASC
	if( empty( $destination_order_by ) )
	{
		$destination_order_by = 'DESC';
	}
	
	
	if( empty( $trip_date_order_by ) )
	{
		$trip_date_order_by = 'DESC';
	}
	
    // If $just_output is NOT empty, stop here
    if(!empty($just_output))
    {
        return $output;
    }
    else
    {
         // Otherwise, show it all


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
        <div><label for="change_odometer_year">Change year:</label>
            <select name="change_odometer_year" id="change_odometer_year">
                <option value="" <?php if(empty($year)) { echo 'selected="selected"'; } else { echo ''; } ?>>-- All --</option>
                <?php
                    $odometer_range = sbm_get_year_range_from_odometers();
                    for($i = $odometer_range[1]; $i >= $odometer_range[0]; $i--)
                    {
                        ?>
                         <option value="<?php echo $i; ?>" <?php if($year == $i) { echo 'selected="selected"'; } else { echo ''; } ?>><?php echo $i; ?></option>
                        <?php

                    }
                ?>
            </select></div>
            <div><a href="javascript: void(0);" id="download_csv_odometer">Download a csv version</a></div>
            <div id="csv_ready"></div>
            <div>&nbsp;</div>
        <div class="clear">
			<input type="button" value="Add to Odometer" onclick="javascript: window.location = './admin.php?page=sbm_edit_odometer&status=new';">
		</div>
    <h3>Total Miles for year ( <?php echo $year; ?> ): <?php echo sbm_count_miles_for_year( $year ) ?></b></h3>

		<div id="invoice_list">
               <table id="bw_table">
					<tr>
						<td>View/Edit</td>
						<td><a href="admin.php?page=sbm_view_odometer_list&sort_by=trip_date&order_by=<?php echo $trip_date_order_by; ?>&filter_by_status=<?php echo $filter_by_status; ?>&year=<?php echo $year; ?>"><div class="float-left"></div><div class="<?php echo $trip_date_arrow; ?>"></div>Trip Date</a></td>
						<td><a href="admin.php?page=sbm_view_odometer_list&sort_by=destination&order_by=<?php echo $destination_order_by; ?>&filter_by_status=<?php echo $filter_by_status; ?>&year=<?php echo $year; ?>"><div class="float-left"></div><div class="<?php echo $destination_arrow; ?>"></div>Destination</a></td>
						<td>Total Miles</td>
						<td>Starting Miles</td>
						<td>Ending Miles</td>
						<td>Payee/Payer</td>
						
					</tr>
		<?php
			echo $output;
		?>
        			<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
		</table>
		</div>
        <div class="clear">
			<input type="button" value="Add to Odometer" onclick="javascript: window.location = './admin.php?page=sbm_edit_odometer&status=new';">
		</div>
	<?php
	
    }
}

function sbm_edit_odometer()
{

	global $wpdb;
	global $current_user;
	
	$odometer_info = new sbm_odometer();
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
		// take the user to the view odometer list
		//  general_functions.php:     sbm_redirect()
		sbm_redirect('sbm_view_odometer_list');
		//echo '<h2>This page was reached in error</h2>';
		die();
	}
echo '<div class="wrap">';

		if (isset($_POST['odometer_id']))
		{
			if(isset($_POST['verify_delete']))
			{
				$odometer_info->sbm_delete_odometer($_POST['odometer_id']);
				die('Delete odometer, if this is visible please contact odometer support');
			}
			
			
			$errors = array();
			
			
			// If this is new, all the fields are required
			if($_GET['status'] == 'new')
			{
				if(empty($_POST['destination']))
				{
					$errors[] = 'You forgot the first name.';
				}
				if(empty($_POST['trip_date']))
				{
					$errors[] = 'You forgot the trip date.';
				}
				
																	 
																	   
																	 
			}
			
			// Now check the trip date
				$trip_date 	= explode("/", $_POST['trip_date']);
				$month 		= $trip_date[0];
				$day 		= $trip_date[1];
				$year 		= $trip_date[2];
				if(strlen($month) == 1 )
				{
					$month = "0$month";
					$adjust = true;
				}
				if(strlen($day) == 1 )
				{
					$day = "0$day";
					$adjust = true;
				}
				if($adjust == true)
				{
					$_POST['trip_date'] = $month . '/' . $day . '/' . $year;
				} 
				// set up the errors if the format is wrong
				if( ( strlen($month) != 2 ) || (sbm_check_month($month) == false ) )
				{
					$errors[] = 'There is a problem with the trip date, check the month!';
				} 
				if( ( strlen($day) != 2 ) || ( sbm_check_day($day) == false ) )
				{
					die();
					$errors[] = 'There is a problem with the trip date, check the day!';
				} 
				if( strlen($year) != 4 )
				{
					$errors[] = 'There is a problem with the trip date, check the year!';
				} 
				
				
				
				if($_POST['not_duplicate'] != 'true')
				{
					// Warn if potential match of duplicate record, unless verification has been checked
					if( sbm_check_for_duplicate_odometer_entry($_POST['odometer_id'], $_POST['trip_date'], $_POST['destination'], $_POST['total_miles'], $_POST['starting_miles'], $_POST['ending_miles'], $_POST['description'] ) == true )
					{
						// show possible match and offer over ride
						$errors[] = 'Possible duplicate?';
						$show_duplicate_checkbox = true;
					}
				}
				else
				{
					// allow it to pass through
				}
				
			if(empty($errors))
			{
				// we dont want to store this value;
				$_POST['not_duplicate'] = null;
			
				$odometer_info->sbm_update_odometer();
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
			//  classes/sbm_odometer.php:     sbm_get_odometer_data()
			$odometer_info->sbm_get_odometer_data($id);
            $payee_payer_id = $odometer_info->sbm_get_odometer_payee_payer_id($id);
				echo '<h2>odometer '.$odometer_info->destination.'</h2>';
				echo '<form method="post" id="editOdometerForm">';
				echo '<fieldset style="border: 1px solid #000; padding: 10px; ">';
				if($show_duplicate_checkbox == true)
				{
					
					echo '<div class="success">This is NOT a duplicate, I am sure of it. Check this box if you want to verify this is not a duplicate entry:<input type="checkbox" name="not_duplicate" value="true" /><br /></div>';
					
				}
				
				echo '<div><input type="hidden" id="odometer_id" name="odometer_id" value="'.$id.'"></div>';
				
				
				?>
				
				
				<div id="odometer_information_edit" class="float-left">
                    <h2>odometer Information</h2>
                    <div>
                        <label for="trip_date">Trip Date<em>*</em></label> 
                        <input type="text" autocomplete="off" id="trip_date" class="required hasDatePicker" name="trip_date" size="20" value="<?php echo sbm_sticky_input($_POST['trip_date'], $odometer_info->trip_date); ?>">
                    </div>
                    <div>
                        <label for="destination">Destination<em>*</em></label> 
                        <input type="text" autocomplete="off" id="destination" class="required" name="destination" size="20" value="<?php echo sbm_sticky_input($_POST['destination'], $odometer_info->destination); ?>">
                    </div>
                    <div id="destination_suggestions">Max 10 suggestions shown</div>
                    <div>
                        <label for="total_miles">Total Miles<em>*</em></label>
                        <input type="text" autocomplete="off" id="total_miles" class="required number" name="total_miles" size="20" value="<?php echo sbm_sticky_input($_POST['total_miles'], $odometer_info->total_miles); ?>">
                    </div>
                    <div>
                        <label for="starting_miles">Starting Miles</label>
                        <input type="text" autocomplete="off" id="starting_miles" name="starting_miles" size="20" value="<?php echo sbm_sticky_input($_POST['starting_miles'], $odometer_info->starting_miles); ?>">
                    </div>
                    <div>
                        <label for="ending_miles">Ending Miles</label>
                        <input type="text" autocomplete="off" id="ending_miles" name="ending_miles" size="20" value="<?php echo sbm_sticky_input($_POST['ending_miles'], $odometer_info->ending_miles); ?>">
                    </div>
                    <div>
                        <label for="ending_miles">Payee/Payer</label>
                        <select name="payee_payer_id">
                            <?php
                             echo sbm_get_payee_payer_as_option( $payee_payer_id );
                            ?>
                        </select>
                    </div>

				</div>
				
				<div class="clear"></div>
				<div class="float-left medium-padding">
                <?php
				//  general_functions.php:     sbm_check_read_only_user()	
				if(  sbm_check_read_only_user() == false  )
				{
						echo '<span><input type="submit" value="Submit" id="editOdometerSubmitButton"></span>';
						//  general_functions.php:     sbm_ok_to_delete()
					if( ( $_GET['status'] != 'new' ) && ( sbm_ok_to_delete('odometer_id',$id, 'no') == true ) )
						{
							echo '<span><input type="button" value="Delete this odometer" onclick="sbm_verifyDeleteodometer('.$id.');"></span>';
						}
				}
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_view_odometer_list', 'cancel');
				
			//  help_functions.php:     sbm_display_help()
			echo sbm_display_help( 'enter_odometer' );
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
					
				$odometer_info->sbm_delete_odometer($_GET['id']);
				die('The attempt to delete this property failed, please contact odometer services');
				
			}
			 		//  classes/sbm_odometer.php:     sbm_get_odometer_data()
					$odometer_info->sbm_get_odometer_data($_GET['id']);
			echo '<div class="wrap">';
			// check to see if we can even delete this
			//  general_functions.php:     sbm_ok_to_delete()
			sbm_ok_to_delete('odometer_id', $_GET['id'], 'yes');
			
			if(!empty($_GET['id']))
			{
				echo '<h2>Delete odometer '.$odometer_info->trip_date.' '.$odometer_info->destination.' '.$odometer_info->total_miles.'</h2>';
				echo '<div>Deleting this odometer will remove all data associated with this odometer</div>';
				echo '<form method="post">
							<input type="hidden" name="verify_delete" value="true">';
				echo '<div class="float-left medium-padding">';
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_view_odometer_list', 'cancel');
				echo '<input type="submit" value="Delete">';
				echo '</div>';
				echo '</form>';
				
			}
			else
			{
				echo '<h2>You need to select a odometer before you can use this page</h2>';
			}
			
	}
			// end Delete
			echo '</fieldset>';
			echo '</form>';
			echo '<div id="output_div"></div>';
		echo '</div>';

	
	
}





function sbm_count_odometers()
{
	global $wpdb;
	global $current_user;
		
				$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_odometer";
				$count = $wpdb->get_var($wpdb->prepare($query));
	
	return $count;
}
function sbm_get_trip_date($id)
{
	global $wpdb;
			$query = "SELECT 
								meta_value
							FROM 
								".$wpdb->prefix."sbm_meta
							WHERE 
								meta_key = 'trip_date'
							
							AND 
								odometer_id = $id";
			$result = $wpdb->get_var($wpdb->prepare($query));
			
	return $result;
}

function sbm_check_for_duplicate_odometer_entry($odometer_id, $trip_date, $destination, $total_miles, $starting_miles ='', $ending_miles= '', $description= '' )
{

	global $wpdb;
	global $current_user;
	$match = array();
	
      get_currentuserinfo($current_user->ID);
	$user_info 	=  get_currentuserinfo($current_user->ID);

				
	$query = "SELECT 
				GROUP_CONCAT( meta_key, '|', meta_value ) as odometer_data,
					odometer_id
				FROM
					" . $wpdb->prefix . "sbm_odometer,		
					" . $wpdb->prefix . "sbm_odometer_meta		
				WHERE 
					visible = 1
				GROUP BY 
					odometer_id";			
				
	$odometer_list 	= $wpdb->get_results($query);
	
	$result = array();
	
 	$total = 0;
			// If the odometer ID was not used as a display option use this    
			foreach( $odometer_list as $list )
			{
				// Break up the comma delimited
				$explode1 = explode( ',', $list->odometer_data );
				
				foreach( $explode1 as $meta_content )
				{
					// now explode each result to seperate the information that is pipe delimited
					$explode2 = explode('|', $meta_content );
								
					$result[ $list->odometer_id ][ $explode2[0] ] 	= $explode2[1];
					
				}
					
					if( 
						( $trip_date == $result[ $list->odometer_id ][ 'trip_date' ] )
						&&
						( strtolower($destination) == strtolower($result[ $list->odometer_id ][ 'destination' ] ))
						&&
						($total_miles == $result[ $list->odometer_id ][ 'total_miles' ])
						&&
						($odometer_id != $list->odometer_id)
					)
					{
						
						$match[] = true;
					}	
					else
					{
						// do nothing
					}
							 
			  }

			  if(count($match) == 0 )
			  {
			  	return false;
			  }
			  else 
			  {
			  	return true;
			  }
			  

}

function sbm_get_year_range_from_odometers()
{
	global $wpdb;

		$sql = "SELECT odometer_date FROM ".$wpdb->prefix."sbm_odometer ORDER BY odometer_date ASC LIMIT 0,1";
		$oldest =$wpdb->get_var($wpdb->prepare($sql));

		$sql = "SELECT odometer_date FROM ".$wpdb->prefix."sbm_odometer ORDER BY odometer_date DESC LIMIT 0,1";
		$newest =$wpdb->get_var($wpdb->prepare($sql));

        if( $oldest == '0000-00-00 00:00:00' )
        {
            $oldest = date("Y-m-d H:i:s");
        }
        if( $newest == '0000-00-00 00:00:00' );
        {
            $newest = date("Y-m-d H:i:s");
        }

		return array(date("Y", strtotime($oldest)), date("Y", strtotime($newest)));

}



?>