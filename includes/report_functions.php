<?php

// Report Generator
function sbm_view_report_list()
{
	global $current_user;

     get_currentuserinfo($current_user->ID);
		
	echo '<div class="wrap">';
	echo '<h2>Saved Reports</h2>';
	
	
	echo '</div>';
}

function sbm_generate_report()
{
	global $current_user;
	$report_info = new report();
	
     get_currentuserinfo($current_user->ID);
	
	if( isset( $_POST['report_name'] ) )
	{
		// Create the array to hold our values
		$fields = array();
		foreach( $_POST as $key => $list )
		{
			
			if( ( $key != 'report_name' ) && ($key != 'report_title' ) )
			{
				 $fields[ $key ] = $list;
			}
		}
		// Now that we have the array of fields we can pass it along to our funciton to get the results
		sbm_get_report( $fields );
		
	}
	
	echo '<div class="wrap">';
	echo '<form id="report_form" name="create_report" method="post">';
	
	echo '<h2>Generate a new report</h2>';
	echo '<div>Step 1: Set the report name and title ( <a href="javascript: void(0);" id="toggle_step_1">hide</a> )</div>';
	echo '<div id="step_1">';
		echo '<div><label for="report_name">Report Name</label><em>*</em> <input type="text" id="report_name" class="required" name="report_name" size="20" value="' . sbm_sticky_input($_POST['report_name'], $report_info->report_name) . '"><input type="checkbox" id="title_same_as_name">( Make the title the same as the name )</div>';
		echo '<div><label for="report_title">Report Title</label><em>*</em> <input type="text" id="report_title" class="required" name="report_title" size="20" value="' . sbm_sticky_input($_POST['report_title'], $report_info->report_title) . '"></div>';
	echo '</div>'; // ends step_1 div
	
	echo '<div>Step 2: Select Fields to display ( <a href="javascript: void(0);"id="toggle_step_2">hide</a> )</div>';
	echo '<div id="step_2">';
		echo '<div>The report can not be wider than 188mm.  Make sure that each field has enough room or the result will be cut off.</div>';
		echo '<div id="report_field"></div>';
		echo '<div id="select_field">
					<button id="first_name" class="select_button" value="first_name">First Name</button>
					<button id="last_name" class="select_button"  value="last_name">Last Name</button>
					<button id="property_name" class="select_button"  value="property_name">Property Name</button>
					<button id="street_number" class="select_button"  value="street_number">Street Number</button>
					<button id="street_name" class="select_button"  value="street_name">Street Name</button>
					<button id="city" class="select_button"  value="city">City</button>
					<button id="state" class="select_button"  value="state">State</button>
					<button id="zip" class="select_button"  value="zip">Zip</button>
					<button id="unit_number" class="select_button"  value="unit_number">Unit Number</button>
					<button id="rent_amount" class="select_button"  value="rent_amount">Rent Amount</button>
					<button id="lease_expiration" class="select_button"  value="lease_expiration">Lease Expiration</button>
	</div>';
	echo '</div>'; // ends step_2 div
	echo '<div class="clear"><input type="submit" id="submit_report" value="Submit"></div>';
	echo '</form>';
	echo '<div id="messages">Messages </div>';
	echo '</div>';
	
}

function sbm_get_report( $fields )
{
	global $wpdb;
	$i = 1;
	$total = count( $fields );
	// $fields is going to be an array and depending on what it contains will determin what tables we join
	foreach( $fields as $key => $list )
	{
		// do not use the user id, we already know what it is
		if ($key != 'user_id')
		{
			if( ( $total > 1 ) && ( $i < $total - 1 ) )
			{
				$add_comma = ", ";
			}
			else
			{
				$add_comma = "";
			}
			// figure out what tables need to be joined depending on what was entered.
			switch( $key )
			{
				case 'property_name';
					$left_join .= ' LEFT JOIN ' . $wpdb->prefix . 'sbm_property ON ID';
				break;
				
			}
			
			// Build our SQL with what is submitted.
			$select_item .= $key . $add_comma;
			$i++;
		}
	}
	$tables = $wpdb->prefix . "sbm_meta";
	$where = "WHERE meta_key = 'first_name_1'";
	
	$query = "SELECT meta_value FROM $tables $left_join $where  ";
	echo $query;
}
?>