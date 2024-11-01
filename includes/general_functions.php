<?php

function sbm_get_timezone()
{
	$timezone = get_option( 'sbm_timezone' );

	if( empty( $timezone ) )
	{
		// Set the timezone to CST since that will be our default
		update_option( 'sbm_timezone', 'America/Chicago' );
		$timezone = get_option( 'sbm_timezone' );
	}
	date_default_timezone_set( $timezone );
	
	return $timezone;
	
}
/*
 * Function: sbm_user_email_available( $email )
 * Description: Used to check if the customers email address has been used in the users table
 * @params email 
 * @returns boolean
 */
function sbm_user_email_available( $email ){
	global $wpdb;
			$query = "SELECT 
							COUNT(*)
						FROM 
							".$wpdb->prefix."users
						WHERE 
							user_email = '$email'";
			$count = $wpdb->get_var($wpdb->prepare($query));
			
		if($count == 0 )
		{
			return true;
		}
		else
		{
			return false;
		}

}
/*
 * Function: sbm_customer_user_email_validate( $email, $customer_id )
 * Description: Used to check if the customers email address belongs to the customer id
 * @params email and customer id
 * @returns boolean
 */
function sbm_customer_user_email_validate($email, $user_id)
{
	global $wpdb;
			$query = "SELECT 
						COUNT(*)
						FROM 
							`".$wpdb->prefix."usermeta`
						WHERE 
							meta_key = 'nickname'
						AND
							user_id = '$user_id'
						AND
							meta_value = '$email'";
			$count = $wpdb->get_var($wpdb->prepare($query));


		if($count == 1 )
		{

			return true;
		}
		else
		{
           
			return false;
		}
	
}
function sbm_get_user_id( $customer_id )
{
	global $wpdb;
			$query = "SELECT 
							user_id
						FROM 
							".$wpdb->prefix."usermeta
						WHERE 
							meta_key = 'customer_id'
						AND
							meta_value = '$customer_id'";
			
			return $wpdb->get_var($wpdb->prepare($query));
}

function sbm_get_user_customer_id( $user_id )
{
	global $wpdb;
			$query = "SELECT 
							meta_value
						FROM 
							".$wpdb->prefix."usermeta
						WHERE 
							user_id = '$user_id'
						AND
							meta_key = 'customer_id'";
			
			return $wpdb->get_var($wpdb->prepare($query));
}

function sbm_get_current_user_level($user_id)
{
	global $current_user;
	
	return $current_user->user_level;
}

/*
 * Returns 5 elements to an array
 * 0 = image name
 * 1 = height
 * 2 = width
 * 3 = type
 * 4 = attr: attributes height and width
 */
function sbm_get_invoice_image_info()
{
	$image_array = array();
	$sbm_image = get_option('sbm_invoice_image');
	if(empty($sbm_image))
	{
		$image_array[0] = '';
		$image_array[1] = '';
		$image_array[2] = '';
		$image_array[3] = '';
		$image_array[4] = '';
	}
	else
	{
		list($width, $height, $type, $attr) = getimagesize($sbm_image);
		
		$image_array[0] = $sbm_image;
		$image_array[1] = $width;
		$image_array[2] = $height;
		$image_array[3] = $type;
		$image_array[4] = $attr;
		
	}
	
	return $image_array;
}


function sbm_readme()
{
	?>
	<div class="wrap">
    <?php
	
		echo nl2br(file_get_contents( SBM_PLUGIN_URL . '/readme.txt' ) );
	
		?>
        
    </div>
    <?php
}




/*
*	function sbm_get_transaction_id();
*
*	params:  none
*	returns: array[0] = id array[1] = time
*/
function sbm_get_transaction_id()
{
		global $wpdb;
		
		// Insert a record into the sbm_meta table
		$wpdb->insert(  $wpdb->prefix."sbm_transaction", array(  'transaction_date'=> time() ), array(  '%s' ) );
		
		return $wpdb->insert_id;	
}

function sbm_get_bg( $bg )
{

	switch($bg)
	{
		case 'even_bg'; $bg = 'odd_bg';
		break;
		case 'odd_bg'; $bg = 'even_bg';
		break;
		default;
			$bg = 'odd_bg';
		break;
	}

	return $bg;
	
}

function sbm_sort_list( $array, $order_by, $options = '' )
{

    if($options == 'natsort')
    {
        $order_by = 'natsort';
    }
	switch( $order_by )
	{
		case 'ASC';
				asort( $array );
		break;
		case 'DESC';
				arsort( $array );
		break;
        case 'natsort';
                natsort($array);
        break;
		default;
			if(!empty( $array ))
			{
				asort( $array );
			}
		break;
	}
	
	return $array;
}


function sbm_pre_array( $array )
{
    echo '<pre>';
    print_r( $array );
    echo '</pre>';
    
}

function sbm_check_read_only_user()
{
	global $wpdb;
	global $current_user;
	$user_level         = $current_user->user_level;
   
	if( $user_level == 0 )
	{ 
		return true;
	}
	else
	{
		return false;
	}
	
}


function sbm_ok_to_delete($name, $id, $die)
{
	global $wpdb;
	
	$errors = array();
	// This is a generic function that will either output a message that says you may not delete this 
	// It has a history in our system and that will affect reports, etc...  Or it will not stop the user from deleting it.
	
       // Set the value of $count1, etc to null
        $count1     = null;
        $count2     = null;
        $count3     = null;
        $count4     = null;
        $count5     = null;
        $count6     = null;
        $count7     = null;
        $count8     = null;
        $count9     = null;
        $count10    = null;
        $count11    = null;
        $count12    = null;
        $count13    = null;
        $count14    = null;
        
	switch($name)
	{
		case 'meta_id';

		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_payments WHERE meta_id = '$id'";
		$count1= $wpdb->get_var($wpdb->prepare($query));
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_customer_account WHERE meta_id = '$id'";
		$count2 = $wpdb->get_var($wpdb->prepare($query));
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_user_account WHERE deposit_type_id = '$id' OR expense_type_id = '$id'";
		$count3 = $wpdb->get_var($wpdb->prepare($query));
		
		break;
		case 'customer_id';
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_invoice WHERE customer_id = '$id'";
		$count1 = $wpdb->get_var($wpdb->prepare($query));
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_customer_payments WHERE customer_id = '$id'";
		$count3 = $wpdb->get_var($wpdb->prepare($query));
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_sent_letter WHERE customer_id = '$id'";
		$count4 = $wpdb->get_var($wpdb->prepare($query));
		
		break;
		case 'payee_payer_id';
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_user_deposits WHERE payee_payer_id = '$id'";
		$count1 = $wpdb->get_var($wpdb->prepare($query));
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_expenses WHERE meta_id = '$id'";
		$count2 = $wpdb->get_var($wpdb->prepare($query));
		
		break;
		case 'transaction_type_id';
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_deposits WHERE transaction_type_id = '$id'";
		$count1 = $wpdb->get_var($wpdb->prepare($query));
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_expenses WHERE transaction_type_id = '$id'";
		$count2 = $wpdb->get_var($wpdb->prepare($query));
		
		break;
		case 'expense_type_id';
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_deposits WHERE expense_type_id = '$id'";
		$count1 = $wpdb->get_var($wpdb->prepare($query));
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_expenses WHERE expense_type_id = '$id'";
		$count2 = $wpdb->get_var($wpdb->prepare($query));
		
		break;
		case 'deposit_type_id';
		
		$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_deposits WHERE deposit_type_id = '$id'";
		$count1 = $wpdb->get_var($wpdb->prepare($query));
		
		
		break;
		case 'invoice';
		
		$query = "SELECT invoice_status FROM ".$wpdb->prefix."sbm_invoice WHERE ID = '$id'";
		$result = $wpdb->get_var($wpdb->prepare($query));
		
		if($result == 'invoiced' )
		{
			$count1 = 1;
		}
		
		break;
		default;
		break;
	}

		if(($count1 + $count2 + $count3 + $count4 + $count5 + $count6 + $count7 + $count8 + $count9 + $count10 + $count11 + $count12 + $count13) > 0)
		{
			// do not allow delete
			$errors[] = 1; 
		}

	if(!empty($errors))
	{
		// If alert is empty, send the die message, otherwise return false...we are using this check on a list
		if($die == 'yes')
		{
			die('You can not delete this, it has a history in other tables.  That will affect reports and other related items.');
		}
		if($die == ' no')
		{
			return false;
		}
	}
	
	if(empty($errors))
	{
		return true;
	}
	
	
}


function sbm_cancel_button($page,$message)
{
		$content = '<span><input type="button" value="Cancel" onclick="javascript: window.location = \'./admin.php?page='.$page.'&message='.$message.'\';"></span>';
		
		return $content;
}


function sbm_return_url_button( $params )
{
        $misc = '';
		foreach($params as $key => $list)
		{
			switch($key)
			{
				case 'page';
					$page = $list;
				break;
				case 'return_url';
					$return_url = $list;
				break;
				default;
					$misc .= '&'.$key.'='.$list;
				break;
			}
		}
		switch($return_url)
		{
			case 'sbm_enter_deposit_expense';
				$text = 'Return to Enter Deposits and Expenses';
			break;
			case 'sbm_reconcile';
				$text = 'Do Not submit and return to reconcile';
			break;
			default;
			break;
		}
		$content = '<span><input type="button" value="' . $text . '" onclick="javascript: window.location = \'./admin.php?page='.$return_url.'&'.$misc.'\';"></span>';
		
		return $content;
}

/*
*
*	Clean up Tables that have data that was started but never finished 
*  Or the user left before submitting.
*	This will remove any errand entries that are empty
*/
function sbm_cleanup_tables()
{
	  sbm_remove_abandoned_payee_payer();
	 
	  sbm_remove_abandoned_customer();
	  sbm_remove_abandoned_transaction_type();
}

// Clear notice
// First argument is the div id and the second is the delay
function sbm_clear_notice($div_id, $delay = '')
{
    ?>
    <script type="text/javascript">
        jQuery.noConflict();
        jQuery(document).ready(function($){
        var delayTime   = '<?php echo $delay; ?>';
        var divId       = '<?php echo $div_id; ?>';
            
        if(delayTime > 0)
        {
        var newTime = delayTime*1000;

                            $("#" + divId).fadeIn(1000).animate({opacity: 1.0}, 2000).fadeOut(newTime, function() {


                                  $("#" + divId).remove();

                            });
        }
        else
        {

            $("#" + divId).fadeIn(1000, function() {
                $(this).delay( 5000 ).fadeOut( 1000 );

            });
        }
        })
    </script>
	<?php
}


function sbm_company_options()
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

			echo '<h2>(Company Profile) What do you want to do?</h2>';
			
	//  general_functions.php:     sbm_check_read_only_user()	
	if(  sbm_check_read_only_user() == false  )
	{
		$text = 'View or Edit';
	}
	else
	{
		$text = 'View';
	}
	
			echo '
				<div><a href="admin.php?page=sbm_view_transaction_type_list">' . $text . ' Transaction types</a></div>
				<div><a href="admin.php?page=sbm_view_expense_type_list">' . $text . ' Expense types</a></div>
				<div><a href="admin.php?page=sbm_view_deposit_type_list">' . $text . ' deposit types</a></div>';
		echo '</div>';
}


function sbm_get_suffix($number)
{
									 switch($number)
								 {
									case 1; $suffix = 'st';
									break;
									case 2; $suffix = 'nd';
									break;
									case 3; $suffix = 'rd';
									break;
									default; $suffix = 'th';
									break;
								 }
								 
	return $suffix;
}

	// Get a more descriptive message
function sbm_get_message($message)
{
	switch($message)
	{
		case 'cancel': $output = 'The cancel button was used, no further actions were taken.';
		break;
		case 'success_update_settings': $output = 'The settings were updated!';
		break;
        case 'success_delete_invoice': $output = 'The invoice was deleted';
        break;
        case 'success_cancel_invoice': $output = 'The invoice was cancelled';
        break;
		case 'success_profile': $output = 'Your profile was updated!';
		break;
		case 'success_payee_payer': $output = 'The payee / payer was updated!';
		break;
		case 'success_transaction_type': $output = 'The transaction type was updated!';
		break;
		case 'success_expense_type': $output = 'The expense type was updated!';
		break;
		case 'success_deposit_type': $output = 'The deposit type was updated!';
		break;
		case 'success_customer': $output = 'The customer was updated!';
		break;
		case 'success_bank_account': $output = 'The bank account was updated!';
		break;
		case 'success_reconcile': $output = 'Reconcile was successful!';
		break;
		case 'success_invoice': $output = 'Invoice Saved!';
		break;
        case 'success_adjust_account': $output = 'Success adjusting the account';
        break;
		case 'success_invoice_exact_payment': $output = 'The invoice was marked as paid for the exact amount!';
		break;
		case 'success_invoice_over_payment': $output = 'The invoice was paid, however it was for more than the requested amount!';
		break;
		case 'success_invoice_under_payment': $output = 'The invoice was paid, however it was for LESS than the requested amount!';
		break;
        case 'success_adjust_account': $output = 'Success adjusting the account';
        break;
        case 'success_deposit_expense': $output = 'Success adding the deposit/expense';
        break;
        case 'success_odometer': $output = 'Success adding the odomter';
        break;
        case 'delete_odometer': $output = 'The odometer entry was deleted';
        break;
		case 'failed_delete_invoice_data': $output = 'The old invoice data could not be deleted!';
		break;
        case 'success_pay_invoice': $output = 'Invoice was paid';
        break;
		case 'failed_pay_invoice': $output = 'There was an error logging the payment of the invoice!';
		break;
        case 'failure_adjust_account': $output = 'FAILURE in adjusting the account';
        break;
		case 'misc_debit': $output = 'The misc debit was applied!';
		break;
		case 'misc_credit': $output = 'The misc credit was applied!';
		break;
		case 'payment_recieved': $output = 'The payments have been logged';
		break;
		case 'expense_deposit_success': $output = 'Transaction Complete: ';
		break;
		case 'delete_payee_payer': $output = 'The payee / payer was deleted';
		break;
		case 'delete_transaction_type': $output = 'The transaction type was deleted';
		break;
		case 'delete_expense_type': $output = 'The expense type was deleted';
		break;
		case 'delete_deposit_type': $output = 'The deposit type was deleted';
		break;
		case 'delete_customer': $output = 'The customer was deleted';
		break;
		default;
		$output = 'Default message';
		break;
	
	}
	return $output;
}

function sbm_convert_date( $date )
{
	$misc_date 									= explode("/", $date);
	$misc_month 								= $misc_date[0];
	$misc_day 									= $misc_date[1];
	$misc_year 									= $misc_date[2];
	// all should have a value before proceeding
	if(( !empty($misc_month)) && (!empty($misc_day)) && (!empty($misc_year)) )
	{
		return mktime(0,0,0,$misc_month,$misc_day,$misc_year);
	}
	else
	{
		return time();	
	}
}

function sbm_get_meta_value( $meta_id)
{
		global $wpdb;
		
			
			$value=$wpdb->get_var($wpdb->prepare(
												   "SELECT 
												   		meta_value 
													FROM
														".$wpdb->prefix."sbm_meta 
													WHERE 
														ID = %s
													", 
													$meta_id));
	
			
			return $value;
	
}
function sbm_get_meta_value_by_meta_key( $meta_key)
{
		global $wpdb;
		
			
			$value=$wpdb->get_var($wpdb->prepare(
												   "SELECT 
												   		meta_value 
													FROM
														".$wpdb->prefix."sbm_meta 
													WHERE 
														meta_key = %s
													", 
													$meta_key));
	
			
			return $value;
	
}



function sbm_logout()
{
			$url = wp_logout_url(get_permalink());
			
			echo '<script type="text/javascript">document.location.href="'.str_replace('&amp;', '', $url).'"</script>';
			

}

// Add the jquery so we can use some effects
function sbm_include_jquery() {
	
	
}

function sbm_public_css() {
	
    echo '<link rel="stylesheet" type="text/css" href="' . SBM_PLUGIN_URL.'/css/public_css.css" />';
    		
}


// add our custom style.css
function sbm_style_css() {
	
    echo '<link rel="stylesheet" type="text/css" href="' . SBM_PLUGIN_URL.'/css/style.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . SBM_PLUGIN_URL.'/css/timepicker.css" />';
	echo '<link rel="stylesheet" type="text/css" media="print" href="' .SBM_PLUGIN_URL.'/css/print.css" />';
}


	

function sbm_sticky_input($post, $default)
{
	if (!empty($post))
	{
		$content = $post;
	}
	else
	{
		$content = $default;
	}
	return $content;
}

function sbm_multiple_user()
{
	$multiple_user_status = get_option( "sbm_version" );
	
	if ( ( $multiple_user_status == 'false' ) || empty( $multiple_user_status )  ) 
	{
		return false;
	}
	else
	{
		return true;
	}
	
}




function sbm_view_home_page()
{

?>
<div class="wrap">
	
    <noscript>You NEED javascript for this plugin to work! Otherwise you will have problems</noscript>
	<h3>This plugin does NOT work well with Internet Explorer 5, 6 or 7.</h3>
	<h3>Recommended browsers are Safari, Chrome and Firefox! Please give them a try</h3>
    <br />
    
    <?php
    
	 echo sbm_view_invoices();
	?>
	
	<?php
}

function sbm_redirect($page, $message = '', $message_details = '')
{
			echo '<script language="javascript" type="text/javascript">window.location = \'./admin.php?page=' . $page . '', '&message=' . $message . '', '&message_details=' . $message_details . '\';</script>';

}


function sbm_message_details($message_details)
{
	// An example output would be: Expense to Farmers Insurance in the amount of $603.56 on 09/10/2010 
	$payee_payer_info = new sbm_payee_payer();

	$content = explode(",", $message_details);
	$expense_type_id = $content[0];
	$deposit_type_id = $content[1];
	$payee_payer_id = $content[2];
	$amount = $content[3];
	$transaction_date = $content[4];
	$details = '';
    
	if(!empty($expense_type_id))
	{
		$expense_or_deposit = 'Expense';
	}
	if(!empty($deposit_type_id))
	{
		$expense_or_deposit = 'Deposit';
	}
	
	$payee_payer_info->sbm_get_payee_payer_data($payee_payer_id);
	
	$details .= $expense_or_deposit . ' to ' . $payee_payer_info->name . ' in the amount of $' . $amount . ' on ' . date("m/d/Y", ''.$transaction_date.'') . '.';
	
	return $details;
}

function sbm_check_month($month)
{
	$valid = array();
	for($i = 1; $i <= 12; $i++)
	{
		if(strlen($i) < 2 )
		{
			$i = "0$i";	
		}
		
		$valid[] =  $i;
	}
	foreach($valid as $list)
	{
		if($list == $month)
		{
			$match = true;	
		}
			
	}
	if($match)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function sbm_check_day($day)
{
	$valid = array();
	$match = array();
	for($i = 1; $i <= 31; $i++)
	{
		if(strlen($i) < 2 )
		{
			$i = "0$i";	
		}
		
		$valid[] =  $i;
	}
	foreach($valid as $list)
	{
		if($list == $day)
		{
			$match[] = true;
			
		}
		else 
		{
			// do nothing for now	
		}
			
	}
	if(count($match) == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
	
	die();
}

?>