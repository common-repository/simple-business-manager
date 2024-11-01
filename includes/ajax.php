<?php
// Ajax for simple-business-manager

function sbm_show_display_help()
{
	$page = $_POST['page'];
	if(!empty( $page ))
	{
		echo sbm_display_help( $page );
	}
	else
	{
		echo 'Sorry, no help available for that page!';
	}
	exit();
}
function sbm_get_customer_information()
{
	global $wpdb;
	$customer_info = new sbm_customer();
	
	$customer_id = $_POST['customer_id'];
	
	//  classes/sbm_customer.php:     sbm_get_customer_data()
	$customer_info->sbm_get_customer_data($customer_id);
	
	
	
	foreach( $customer_info as $key => $list )
	{
		
			$info .= $key . '|' . $list . '\\';
		
	}
	
	echo $info;
	exit();	
}

function sbm_show_invoice_totals_different_year()
{
	global $wpdb;
	
	$invoice_info 	= new sbm_invoice();
	$year 			= $_POST['year'];
    $early          = mktime(0,0,0,1,1,$year);
    $late           = mktime(23,59,59,12,31,$year);

		$total_invoices = sbm_get_all_invoices_for_year( $year );
	
		// Now get the total for all invoices for this year
	
		$query = "SELECT 
				".$wpdb->prefix."sbm_invoice.ID as invoice_id,
				".$wpdb->prefix."sbm_invoice.customer_id as customer_id
			FROM 
				".$wpdb->prefix."sbm_invoice
			WHERE
				".$wpdb->prefix."sbm_invoice.invoice_status != 'cancelled'
			AND 
				invoice_date 
			BETWEEN 
				$early 
			AND 
				$late
			ORDER BY
				invoice_date DESC";

		$invoice_list = $wpdb->get_results($query);
		$total = 0;
		foreach($invoice_list as $list)
		{
			$total += $invoice_info->sbm_get_invoice_total( $list->invoice_id, $list->customer_id );
		}
					
		
		echo $total_invoices . '|' . number_format( $total, 2 );
		
		exit();
}
function sbm_show_paid_invoice_totals_different_year()
{
	global $wpdb;
	
	$invoice_info 	= new sbm_invoice();
	$year 			= $_POST['year'];
	
	$early 			= mktime(0,0,0,1,1, $year);
	$late  			= mktime(23,59,59,12,31, $year);
		

		$sql = "SELECT COUNT(*)	FROM ".$wpdb->prefix."sbm_invoice WHERE invoice_status = 'paid' AND invoice_date BETWEEN $early AND $late";
		$total_invoices = $wpdb->get_var($wpdb->prepare($sql));
	
		// Now get the total for all invoices for this year
	
		$query = "SELECT 
				".$wpdb->prefix."sbm_invoice.ID as invoice_id,
				".$wpdb->prefix."sbm_invoice.customer_id as customer_id
			FROM 
				".$wpdb->prefix."sbm_invoice
			WHERE
				".$wpdb->prefix."sbm_invoice.invoice_status = 'paid'
			AND 
				invoice_date 
			BETWEEN 
				$early 
			AND 
				$late
			ORDER BY
				invoice_date DESC";

		$invoice_list = $wpdb->get_results($query);
		$total = 0;
		foreach($invoice_list as $list)
		{
			$total += $invoice_info->sbm_get_invoice_total( $list->invoice_id, $list->customer_id );
		}
					
		
		echo $total_invoices . '|' . number_format( $total, 2 );
		
		exit();
}

function sbm_convert_invoice()
{
	global $wpdb;
	$invoice_info = new sbm_invoice();
	
	$customer_id = $_POST['customer_id'];
	$invoice_id = $_POST['invoice_id'];
	
	$invoice_info->sbm_update_invoice_data( $invoice_id, 'invoice_status', 'invoiced' );
	
	echo $invoice_id ;
	
	exit();	
}
function sbm_suggest_customers()
{
	global $wpdb;

	$info 				= $_POST['info'];
	$company 			= array();
	$first_name 		= array();
	$last_name 			= array();
	$customer_results  	= array();
	
	
	$content = '';
	
	// first query is company
	// limit the results to the top 6 or so, to avoid too many showing up at once
	$query = "SELECT 
				customer_id,
				meta_value
			FROM 
				" . $wpdb->prefix . "sbm_customer_meta
			
			WHERE
				meta_key = 'company_name' 
			AND 
				meta_value LIKE '$info%'";
			
	$customer_list 	= $wpdb->get_results($query);
	
	if( count($customer_list) > 0 )
	{
		
		foreach( $customer_list as $list )
		{
			$customer_info = new sbm_customer();
			$customer_id = $list->customer_id;
			
			$customer_info->sbm_get_customer_data( $customer_id );
			$customer_status = $customer_info->customer_status;
			
			if ( $customer_status == 'Current' )
			{
				
			/*$content .= '<div><a href="javascript: void(0);">
								<span class="new_company_name">' . $customer_info->company_name . '</span>
								&nbsp;
								<span class="new_customer_first_name">' . $customer_info->first_name_1 . '</span>
								&nbsp;
								<span class="new_customer_last_name">' . $customer_info->last_name_1 . '</span>
								<input type="hidden" class="new_customer_id" value="'.$list->customer_id.'">
								<input type="hidden" class="new_customer_tax_rate" value="'.$customer_info->tax_rate.'">
								<input type="hidden" class="new_customer_hourly_rate" value="'.$customer_info->hourly_rate.'">
								
							  </a></div>';
							  */
				$customer_results[$list->customer_id] = array( 
												'new_company_name'=> $customer_info->company_name,
												'new_customer_first_name' => $customer_info->first_name_1,
												'new_customer_last_name' => $customer_info->last_name_1,
												'new_customer_id' => $list->customer_id,
												'new_customer_tax_rate' => $customer_info->tax_rate,
												'new_customer_hourly_rate' => $customer_info->hourly_rate
											);
			}
			else
			{
				if( count($customer_list) - 1 == 0 )
				{
					$company[] = 'none';
				}
			}
			
		}
	}
	else
	{
		$company[] = 'none';
	}
	$customer_list = array();
	// Second query is first name
	// limit the results to the top 6 or so, to avoid too many showing up at once
	$query = "SELECT 
				customer_id,
				meta_value
			FROM 
				" . $wpdb->prefix . "sbm_customer_meta
			
			WHERE
				meta_key = 'first_name_1' 
			AND 
				meta_value LIKE '$info%'";
			
	$customer_list 	= $wpdb->get_results($query);
	
	if( count($customer_list) > 0 )
	{			
		
		
		foreach( $customer_list as $list )
		{
			$customer_info = new sbm_customer();
			$customer_id = $list->customer_id;
			
			$customer_info->sbm_get_customer_data( $customer_id );
			$customer_status = $customer_info->customer_status;
			
			if ( $customer_status == 'Current' )
			{
				
			/*$content .= '<div><a href="javascript: void(0);">
								<span class="new_company_name">' . $customer_info->company_name . '</span>
								&nbsp;
								<span class="new_customer_first_name">' . $customer_info->first_name_1 . '</span>
								&nbsp;
								<span class="new_customer_last_name">' . $customer_info->last_name_1 . '</span>
								<input type="hidden" class="new_customer_id" value="'.$list->customer_id.'">
								<input type="hidden" class="new_customer_tax_rate" value="'.$customer_info->tax_rate.'">
								<input type="hidden" class="new_customer_hourly_rate" value="'.$customer_info->hourly_rate.'">
								
							  </a></div>';
							  */
				$customer_results[$list->customer_id] = array( 
												'new_company_name'=> $customer_info->company_name,
												'new_customer_first_name' => $customer_info->first_name_1,
												'new_customer_last_name' => $customer_info->last_name_1,
												'new_customer_id' => $list->customer_id,
												'new_customer_tax_rate' => $customer_info->tax_rate,
												'new_customer_hourly_rate' => $customer_info->hourly_rate
											);
							}
			else
			{
				if( count($customer_list) - 1 == 0 )
				{
					$first_name[] = 'none';
				}
			}
			
		}
		
	}
	else
	{
		$first_name[] = 'none';
	}
	
	$customer_list = array();
	// last query is last name
	// limit the results to the top 6 or so, to avoid too many showing up at once
	$query = "SELECT 
				customer_id,
				meta_value
			FROM 
				" . $wpdb->prefix . "sbm_customer_meta
			
			WHERE
				meta_key = 'last_name_1' 
			AND 
				meta_value LIKE '$info%'";
			
	$customer_list 	= $wpdb->get_results($query);
	
	if( count($customer_list) > 0 )
	{
		
		foreach( $customer_list as $list )
		{
			$customer_info = new sbm_customer();
			$customer_id = $list->customer_id;
			
			$customer_info->sbm_get_customer_data( $customer_id );
			$customer_status = $customer_info->customer_status;
			
			if ( $customer_status == 'Current' )
			{
				
			/*$content .= '<div><a href="javascript: void(0);">
								<span class="new_company_name">' . $customer_info->company_name . '</span>
								&nbsp;
								<span class="new_customer_first_name">' . $customer_info->first_name_1 . '</span>
								&nbsp;
								<span class="new_customer_last_name">' . $customer_info->last_name_1 . '</span>
								<input type="hidden" class="new_customer_id" value="'.$list->customer_id.'">
								<input type="hidden" class="new_customer_tax_rate" value="'.$customer_info->tax_rate.'">
								<input type="hidden" class="new_customer_hourly_rate" value="'.$customer_info->hourly_rate.'">
								
							  </a></div>';
							  */
				$customer_results[$list->customer_id] = array( 
												'new_company_name'=> $customer_info->company_name,
												'new_customer_first_name' => $customer_info->first_name_1,
												'new_customer_last_name' => $customer_info->last_name_1,
												'new_customer_id' => $list->customer_id,
												'new_customer_tax_rate' => $customer_info->tax_rate,
												'new_customer_hourly_rate' => $customer_info->hourly_rate
											);
							}
			else
			{
				if( count($customer_list) - 1 == 0 )
				{
					$last_name[] = 'none';
				}
			}
		
		}
		
	}
	else
	{
		$last_name[] = 'none';
	}
	
	if( ( !empty( $company ) ) && ( !empty( $first_name ) ) && ( !empty( $last_name ) ) )
	{
	
		$content .= '<div>There are no customers with a company name, </div>
					<div>first name or last name that starts with ' . $info . '</div>
					<div>Please try again</div>';	
	}
	
	
	//die(sbm_pre_array($customer_results));
	
	foreach($customer_results as $key => $list)
	{
		if(!empty($list['new_company_name']))
		{
			$add_company_name =  '<span class="new_company_name">' . $list['new_company_name'] . '&nbsp;</span>';
		}
		else
		{
			$add_company_name =  '<span class="new_company_name"></span>';
					}
		$content .= '<div><a href="javascript: void(0);">' . $add_company_name . '<span class="new_customer_first_name">' . $list['new_customer_first_name'] . ' </span>&nbsp;<span class="new_customer_last_name">' . $list['new_customer_last_name'] . '</span>
								<input type="hidden" class="new_customer_id" value="'.$key.'">
								<input type="hidden" class="new_customer_tax_rate" value="'.$list['new_customer_tax_rate'].'">
								<input type="hidden" class="new_customer_hourly_rate" value="'.$list['new_customer_hourly_rate'].'">
								
							  </a></div>';
	}
	
	echo $content;
	exit();	
}
function sbm_update_amount_paid()
{
	global $wpdb;
	

	$payment_id 			= $_POST['payment_id'];
	$new_amount_paid 		= $_POST['new_amount_paid'];
	$customer_id 			= $_POST['customer_id'];
	
	$customer_info 			= new sbm_customer();
	
	
	$query = "UPDATE " . $wpdb->prefix . "sbm_payments SET amount_paid = '$new_amount_paid' WHERE ID = '$payment_id'";        
    $result = $wpdb->query($query);
	
	if( $result != 0)
	{
		//  classes/sbm_customer.php:     sbm_get_customer_balance()
		sbm_customer::sbm_get_customer_balance($customer_id);  
		$new_balance_due = number_format(str_replace('-', '', sbm_customer::balance), 2);
		
	}
	else
	{
		$new_balance_due = 0; // this will use the alert to tell the user about a problem
	}
	
	echo $new_balance_due;
	exit();
	
}


function sbm_update_amount_due()
{
	global $wpdb;
	
	
	$customer_account_id 	= $_POST['customer_account_id'];
	$new_amount_due 		= $_POST['new_amount_due'];
	$customer_id 			= $_POST['customer_id'];
	
	$customer_info 			= new sbm_customer();
	
	
		$query = "SELECT amount_due FROM " . $wpdb->prefix . "sbm_customer_account WHERE ID = '$customer_account_id'";
		$orignal_amount = $wpdb->get_var($wpdb->prepare($query));
		if ($orignal_amount < 0 )
		{
			$new_amount_due = '-' . $new_amount_due;
		}
	
	
	$query = "UPDATE " . $wpdb->prefix . "sbm_customer_account SET amount_due = '$new_amount_due' WHERE ID = '$customer_account_id'";        
    $result = $wpdb->query($query);
	
	if( $result != 0)
	{
		//  classes/sbm_customer.php:     sbm_get_customer_balance()
		sbm_customer::sbm_get_customer_balance($customer_id);  
		$new_balance_due = number_format(str_replace('-', '', sbm_customer::balance), 2);
		
	}
	else
	{
		$new_balance_due = 0;
	}
	
	echo $new_balance_due;
	exit();
	
}

function sbm_delete_financial_data_by_customer_account_id()
{
	global $wpdb;
	

	$customer_account_id 	= $_POST['customer_account_id'];
	$customer_id 			= $_POST['customer_id'];
	
	$customer_info = new sbm_customer();
	
	// remove all instances of this customer account id, it may be in several tables
	// sbm_customer_account
	/*
	Step 1:
		We should have it: $customer_account_id
	Step 2: 
		Get the transaction_id(s) from the table sbm_customer_account that is = $customer_account_id
	Step 3:
		Delete all records from sbm_customer_account and sbm_payments and sbm_transaction that match
	
	TODO: Check to see if we need to delete the references in the reconcile table regarding the values it stores as reconciled fields.
	
		
	
	*/
	  $query = "DELETE 
					ca, t, p, it
				FROM 
					" . $wpdb->prefix . "sbm_customer_account AS ca
				LEFT JOIN 
					" . $wpdb->prefix . "sbm_transaction AS t 
				ON 
					ra.transaction_id = t.ID
				LEFT JOIN 
					" . $wpdb->prefix . "sbm_payments AS p 
				ON 
					ra.ID = p.customer_account_id
				LEFT JOIN
					" . $wpdb->prefix . "sbm_transaction AS it
				ON 
					p.transaction_id = it.ID
				WHERE 
					ca.ID = $customer_account_id";
	$wpdb->query( $query );
	
	// After delete get new balance
	//  classes/sbm_customer.php:     sbm_get_customer_balance()
	sbm_customer::sbm_get_customer_balance($customer_id);   
            
			
	echo number_format(str_replace('-', '', sbm_customer::balance), 2);
	
	exit();
}



	

 function sbm_delete_sbm_meta()
{
		global $wpdb;
		
		
		echo 'Nothing deleted this was for property, building and units The Item has been deleted';
		
		sbm_clear_notice('message', '5');

		//This  exit(); is needed otherwise you geta a 0 at the end of the string/returned value
		exit();
}

function sbm_check_user_name( $msg = '')
{
	
	global $wpdb; 	
		$query = "SELECT COUNT(*) FROM $wpdb->users WHERE user_login = '{$_POST['user_login']}'";
		$count = $wpdb->get_var($wpdb->prepare($query));
		
	if(empty($_POST[	'user_login']))
	{
		// We dont want the impression they can submit a blank username
		$count = 1;
	}
	if ($count == 0)
	{
		if(empty($msg))
		{
			echo '<img src="'.SBM_PLUGIN_URL.'/images/success.png">';
		}
		else
		{
			return true;
		}
	}
	else
	{
		if(empty($msg))
		{
			echo '<img src="'.SBM_PLUGIN_URL.'/images/fail.png">';
		}
		else
		{
			return false;
		}
	}
		//This  exit(); is needed otherwise you geta a 0 at the end of the string/returned value
		exit();
}

function sbm_display_customer_list()
{
	global $wpdb;
	
	$customer_info 			= new sbm_customer();
	
	$customer_status 		= $_POST['customer_status'];
	$filters 				= $_POST['filters'];
	/*
	// customer Status options
		Current		= Current customers only
		Past				= Past customers only
		Prospective	= Prospective customers only
		all					= List all customers	

	*/
	
	$query = "SELECT 
				GROUP_CONCAT( meta_key, '|', meta_value ) as customer_data,
					customer_id
				FROM
					" . $wpdb->prefix . "sbm_customer,		
					" . $wpdb->prefix . "sbm_customer_meta		
				WHERE 
					visible = 1
				AND 
					meta_key = 'first_name_1'
				OR 
					meta_key = 'last_name_1'
				OR 
					meta_key = 'company_name'
				GROUP BY 
					customer_id";			
				
	$customer_list 	= $wpdb->get_results($query);
	
        if (!empty($_GET['filter_by_status']))
        {
           
            $filter_by_status = $_GET['filter_by_status'];
            
        }
        else
        {
            $filter_by_status = 'Current';
        }
		
			if(count($customer_list) > 0)
			{

				
					$content .= '<table id="bw_table" style="width: 780px;"><tr><td style="width: 20px;"><input onClick="sbm_checkAllCheckNone()" type="checkbox" name="all_none" id="all_none"></td><td>Last Name</td><td>First Name</td><td>Company Name</td></tr>';
					$bg = 'even_bg'; // Start even
					foreach ($customer_list as $list)
					{
						
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
	
					// Break up the comma delimited
					$explode1 = explode( ',', $list->customer_data );
					
					foreach( $explode1 as $meta_content )
					{
						// now explode each result to seperate the information that is pipe delimited
						$explode2 = explode('|', $meta_content );
									
						$result[ $list->customer_id ][ $explode2[0] ] 	= $explode2[1];
						
					}
							
					if ( ($filter_by_status == 'All') || ( $filter_by_status == $result[ $list->customer_id ][ 'customer_status' ] ) )
					{	// My list of sortable fields for this table
						$id[ $list->customer_id ]				= $list->customer_id;
						$company_name[ $list->customer_id ] 	= $result[ $list->customer_id ][ 'company_name' ];
						$last_name[ $list->customer_id ] 		= $result[ $list->customer_id ][ 'last_name_1' ];
						$first_name[ $list->customer_id ] 		= $result[ $list->customer_id ][ 'first_name_1' ];
						$customer_status[ $list->customer_id ]	= $result[ $list->customer_id ][ 'customer_status' ];
						
					 }
										$content .= '<tr class="'.$bg.'">';
										$content .= '<td><input type="checkbox" class="checkbox letter-submit-toggle" id="' . $list->customer_id . '" value="' . $list->ID . '" name="customer_id_list[]"></td>';
										$content .= '<td>' . $result[ $list->customer_id ][ 'last_name_1' ] . '</td>';
										$content .= '<td>' . $result[ $list->customer_id ][ 'first_name_1' ] . '</td>';
										$content .= '<td>' . $result[ $list->customer_id ][ 'company_name' ] . '</td>';
										
										$content .= '</tr>';
				 
				  }
				
			}
			else
			{
				
					$content .= '<tr><td><br><br><br>No customers match the criteria<br><br></tr>';
				
			}
			
				
				$content .= '</table>';
				
		echo $content;
		//This  exit(); is needed otherwise you geta a 0 at the end of the string/returned value
		exit();
}




function sbm_get_number_of_notices( $no_echo = '')
{
	global $current_user;
    
	 get_currentuserinfo($current_user->ID);
	
	$number = 0;
		
	// This will check to make sure that the current installed version is up to date with the latest one on the internet
	
	// Not Required but suggested
	if ( sbm_count_payee_payers() == 0 )
	{
		$number += 1;
	}
	
	
	// Required for the system to work properly
	
	// Check for complete main user profile
	if ( sbm_check_for_complete_profile() == false )
	{
		$number +=  1;
	}
	
	// Transaction types
	if ( sbm_count_transaction_types() == 0 )
	{
		$number += 1;
	}
	
	// Expense types
	if ( sbm_count_expense_types() == 0 )
	{
		$number +=  1;
	}
	
	// Deposit Types
	if ( sbm_count_deposit_types() == 0 )
	{
		$number +=  1;
	}
	 
	
	
	// customers
	if ( sbm_count_customers() == 0 )
	{
		$number +=  1;
	}

	if($current_user->user_level > 7)
	{
		// We are setting this to be 0 since we dont want any notices to show up when they admin logs in to the admin panel.
		$number = 0;
	}
	
						
	
		if(empty($no_echo))
		{
			echo $number;
		}
		else
		{
			return $number;
		}
		
		
		//This  exit(); is needed otherwise you geta a 0 at the end of the string/returned value
		exit();	
}

function sbm_get_list_of_suggestions()
{
	global $wpdb; 
	$payee_payer_info = new sbm_payee_payer();


		$string = ($_POST['content']);
		
		$query = "SELECT 
									payee_payer_id,
									meta_value 
								FROM 
									".$wpdb->prefix."sbm_payee_payer_meta 
								WHERE 
									meta_key = 'payee_payer_name'
								AND
									meta_value LIKE '$string%'
								
								ORDER BY 
									ID ASC
								LIMIT 0,10";
	
		$suggested_list = $wpdb->get_results($query);
		foreach($suggested_list as $list)
		{
			$payee_payer_info->sbm_get_payee_payer_data($list->payee_payer_id);
			
			//<a class="suggested_name" href="javascript: void(0);">ABC</a>
			$content .= '<div class="suggested"><a class="suggested_name" href="javascript: void(0);" >' . $payee_payer_info->payee_payer_name . ' <input type="hidden" class="payee_payer_name" value="' . $payee_payer_info->payee_payer_name . '" ><input type="hidden" class="payee_payer_id" value="' . $list->payee_payer_id . '" ></a></div>';
		}
		if(count($suggested_list) == 0)
		{
			$content .= '<div>No Suggestions Available</div>';
			$content .= '<div id="new_payer_payee"></div>';
		}
		echo $content;
		//This  exit(); is needed otherwise you geta a 0 at the end of the string/returned value
	exit();
}

function sbm_update_reconcile()
{

		global $wpdb;
		
		
			if(isset($_POST['remove_transaction_id_list']))
			{
				// Get the current list and then add this to that
				$sql = "SELECT 
								transaction_id_list
							FROM 
								" . $wpdb->prefix . "sbm_reconcile
							WHERE
								ID = '{$_POST['ID']}'
							
							AND 
								bank_id = '{$_POST['bank_id']}'
							LIMIT 0 , 1";
      			$result = $wpdb->get_var($wpdb->prepare($sql));
				
				
					$remove_number = $_POST['remove_transaction_id_list'];
					//$reconcile_data['transaction_id_list'] = $result;
					
					$parts = explode(',', $result);
					$new_list = array();
					
					foreach($parts as $list)
					{
						if($list != $remove_number)
						{
							$new_list[] = $list;
						}		
						
					}
					
					$total_parts = count($new_list);
					
					
					$i = 1;
					foreach($new_list as $alist)
					{
						
							if($i < $total_parts)
							{
								// add a comma
								$reconcile_data['transaction_id_list'] .= $alist.',';
							}
							if($i == $total_parts)
							{
								// no comma
								$reconcile_data['transaction_id_list'] .= $alist;
							}
						
						$i++;
					}

					
					// If this is the only entry remove whatever may be left
					if($total_parts == 0)
					{
						$reconcile_data['transaction_id_list'] = '';
					}
					
				
				$type[] = '%s';
				
				$type[] = '%d';
				
				$wpdb->update( $wpdb->prefix."sbm_reconcile", $reconcile_data, array( 'ID' => $_POST['ID'] ), $type, array( '%d' ) );
			}
		
			if(isset($_POST['transaction_id_list']))
			{
				// Get the current list and then add this to that
				$sql = "SELECT 
								transaction_id_list
							FROM 
								" . $wpdb->prefix . "sbm_reconcile
							WHERE
								ID = '{$_POST['ID']}'
							
							AND 
								bank_id = '{$_POST['bank_id']}'
							LIMIT 0 , 1";
      			$result = $wpdb->get_var($wpdb->prepare($sql));

				
				
				//$list = str_replace(',,', '', $result->transaction_id_list);	
				
				if(count($result) != 0)
				{
					$reconcile_data['transaction_id_list'] = $_POST['transaction_id_list'] . ',' . $result;
				}
				else
				{
					$reconcile_data['transaction_id_list'] = $_POST['transaction_id_list'];
				}
				$type[] = '%s';
				
				
				
				$type[] = '%d';
						
				$wpdb->update( $wpdb->prefix."sbm_reconcile", $reconcile_data, array( 'ID' => $_POST['ID'] ), $type, array( '%d' ) );
			}
		
			if(isset($_POST['starting_balance']))
			{
				$reconcile_data['starting_balance'] = $_POST['starting_balance'];
				$type[] = '%s';
				
				
				$type[] = '%d';
							
				$wpdb->update( $wpdb->prefix."sbm_reconcile", $reconcile_data, array( 'ID' => $_POST['ID'] ), $type, array( '%d' ) );
			}
			
			if(isset($_POST['ending_balance']))
			{
				$reconcile_data['ending_balance'] = $_POST['ending_balance'];
				$type[] = '%s';
				
				
				$type[] = '%d';
							
				$wpdb->update( $wpdb->prefix."sbm_reconcile", $reconcile_data, array( 'ID' => $_POST['ID'] ), $type, array( '%d' ) );
			}
				echo 'Work Saved';
						
		//This  exit(); is needed otherwise you geta a 0 at the end of the string/returned value
	exit();

}



function sbm_list_of_display_options()
{
	$options =  		'<option value="company_name">Company Name</option>';
	$options .=  		'<option value="first_name">First Name</option>';
	$options .= 		'<option value="last_name">Last Name</option>';
	$options .= 		'<option value="address">Address</option>,';
	$options .= 		'<option value="address_2">Address 2</option>,';
	$options .= 		'<option value="city">City</option>';
	$options .= 		'<option value="state">State</option>';
	$options .= 		'<option value="zip">Zip</option>';
	$options .= 		'<option value="phone_1">Phone 1</option>';
	$options .= 		'<option value="phone_2">Phone 2</option>';
	
	echo $options;
		//This  exit(); is needed otherwise you geta a 0 at the end of the string/returned value
	exit();
}

function sbm_save_letter()
{


	$letter_info = new sbm_letter();
	// this is the common thread that ties all the letters for this submition together, they all share this time
	$sent_date = time();

	$letter_id = $_POST['letter_id'];
	
	
	$letter_content_id = sbm_get_current_letter_content_id( $letter_id );

	
				 
    foreach ( $_POST['customer_id_list'] as $list )
    { 
	

	// classes/letter.php:	   sbm_save_letter
	 $letter_info->sbm_save_letter(  $letter_id, $letter_content_id, $list, $sent_date );

	// letter_functions.php:  sbm_display_letter()
	 $htmlcontent  =  sbm_display_letter( $letter_content_id, $sent_date, $list );
		

    } // ends loop to output the amount of pages
	
		
			
// just return the sent date thats all we need for now
	echo $sent_date;

exit();	
	
	
}

// Quick view of the letter
function sbm_quickview_letter()
{
	global $wpdb;
		

	$letter_info 		= new sbm_letter();
	$sent_date 			= $_POST['sent_date'];
	$letter_id 			= $_POST['letter_id'];
	$customer_id_list 	= $_POST['customer_id_list'];
				
	$letter_content_id = sbm_get_current_letter_content_id( $letter_id );
 	
    foreach ( $customer_id_list as $list )
    { 
		
		// letter_functions.php:  sbm_display_letter()
		$htmlcontent  .= sbm_display_letter( $letter_content_id, $sent_date, $list ) . '<br /><hr><br />';
		
    } 

	

	echo $htmlcontent;

exit();	
	
	
}

function sbm_create_pdf()
{
	
	global $current_user;	

	
	date_default_timezone_set(sbm_get_timezone());


/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: WriteHTML and RTL support
 * @author Nicola Asuni
 * @copyright 2004-2009 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2008-03-04
 */

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
$user_info =  get_currentuserinfo($current_user->ID);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor( $user_info->company_name );
$pdf->SetTitle('PDF created from a letter');
$pdf->SetSubject('Letter Generator');
$pdf->SetKeywords('');

// set default header data
$header_logo = sbm_get_invoice_image_info();

if(!empty($header_logo[0]))
{
	$pdf->setPrintHeader(true);
		
	
		$new_width = $header_logo[1];
	
}
else
{
	$pdf->setPrintHeader(false);
}
// remove default footer
$pdf->setPrintFooter( false );

$pdf->SetHeaderData($header_logo[0], 0, '', '');


// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

//set some language-dependent strings
$pdf->setLanguageArray($l); 

// ---------------------------------------------------------

// set font
$pdf->SetFont('times', '', 10);

 		 // wait 
		//usleep(100000);  		

	$letter_info = new sbm_letter();
	// this is the common thread that ties all the letters for this submition together, they all share this time
	$sent_date = time();

	$letter_id = $_POST['letter_id'];
				
    $total_pages = count( $_POST['customer_id_list'] );
 
	$letter_content_id = sbm_get_current_letter_content_id( $letter_id );
 	
    foreach ( $_POST['customer_id_list'] as $list )
    { 
	// add a page
	$pdf->AddPage();
	

	// letter_functions.php:  sbm_display_letter()
	 $htmlcontent  =  sbm_display_letter( $letter_content_id, $sent_date, $list );

		$pdf->writeHTML( $htmlcontent, false, 0, true, 0 );

    } // ends loop to output the amount of pages
	
	$letter_info->sbm_get_letter_data( $letter_content_id );
	$letter_title = $letter_info->title;

	$pdf_file_name = $letter_title . '_'.date( "m_d_Y_H_i_s", $sent_date ).'.pdf';
	
	$uploads = wp_upload_dir();
	$uploadDir = $uploads['basedir'].'/simple-business-manager/';
	$uploadURL = $uploads['baseurl'].'/simple-business-manager/';


	
	// Save this as a file to be downloaded
	$pdf->Output( $uploadDir . "$pdf_file_name", 'F');
	//Close and output PDF document

//============================================================+
// END OF FILE                                                 
//============================================================+

	

	if(!isset($_POST['quick_pdf']))
	{
		echo  '<a href="' . $uploadURL  . $pdf_file_name . '" target="_blank" ><img src="' . SBM_PLUGIN_URL . '/images/downloadpdf2.png"></a>';
	}
	else
	{
		echo  '<a class="red" href="' . $uploadURL  . $pdf_file_name . '" target="_blank" >Download Ready ( click here )</a>';	
	}
	
exit();
}

//  Create a PDF of the invoice
function sbm_create_invoice_pdf()
{
		
		global $current_user;	
		$invoice_info = new sbm_invoice();
		
		date_default_timezone_set(sbm_get_timezone());
		$invoice_id 	= $_POST['invoice_id'];
		$tax_rate		= sbm_get_tax_rate_for_invoice( $invoice_id );
	 	$total_pages 	= sbm_get_total_pages_for_invoice( $invoice_id );
		$invoice_title 	= sbm_get_invoice_status( $invoice_id );
		$customer_id 	= sbm_get_customer_id_for_invoice( $invoice_id );
		$invoice_total	= $invoice_info->sbm_get_invoice_total( $invoice_id, $customer_id );
		$already_paid   = sbm_get_total_amount_paid_for_invoice( $invoice_id, $customer_id  );
		$balance_due	= $invoice_total - $already_paid;
        $invoice_data 	= new sbm_invoice();
		$invoice_data->sbm_get_invoice_data( $invoice_id, $customer_id );

        $purchase_order = $invoice_data->purchase_order;
	
	/**
	 * Creates an example PDF TEST document using TCPDF
	 * @package com.tecnick.tcpdf
	 * @abstract TCPDF - Example: WriteHTML and RTL support
	 * @author Nicola Asuni
	 * @copyright 2004-2009 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
	 * @link http://tcpdf.org
	 * @license http://www.gnu.org/copyleft/lesser.html LGPL
	 * @since 2008-03-04
	 */
	
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
	$user_info =  get_currentuserinfo($current_user->ID);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor( $user_info->company_name );
	$pdf->SetTitle( $invoice_title . ' #' . $invoice_id);
	$pdf->SetSubject('Invoice Generator');
	$pdf->SetKeywords('');
	
	// set default header data
	$header_logo = sbm_get_invoice_image_info();
	
	if(!empty($header_logo[0]))
	{
		$pdf->setPrintHeader(true);
			
		
			$new_width = $header_logo[1];
		
	}
	else
	{
		$pdf->setPrintHeader(false);
	}
	// remove default footer
	$pdf->setPrintFooter( false );
	
	$pdf->SetHeaderData($header_logo[0], 0, '', '');
		
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
	
	//set some language-dependent strings
	$pdf->setLanguageArray($l); 
	
	// ---------------------------------------------------------
	
	// set font
	$pdf->SetFont('times', '', 10);
	
	 		 // wait 
			//usleep(100000);  		
	
	 	
	    for( $page_number = 1; $page_number <= $total_pages; $page_number++)
	    { 
			// add a page
			$pdf->AddPage();
			
			// letter_functions.php:  sbm_display_letter()
		 	$htmlcontent  =  sbm_get_invoice_page( $invoice_id, $page_number, $invoice_title, $total_pages, $tax_rate, $customer_id, $invoice_total, $already_paid, $balance_due, $purchase_order );
	
			$pdf->writeHTML( $htmlcontent, false, 0, true, 0 );
	
	    } // ends loop to output the amount of pages
		
		
        if($invoice_title == 'invoiced')
        {
            $invoice_title = 'invoice';
        }
		$pdf_file_name = $invoice_title . '_'.$invoice_id.'.pdf';
		
		$uploads = wp_upload_dir();
		$uploadDir = $uploads['basedir'].'/simple-business-manager/';
		$uploadURL = $uploads['baseurl'].'/simple-business-manager/';
	
	
		
		// Save this as a file to be downloaded
		$pdf->Output( $uploadDir . "$pdf_file_name", 'F');
		//Close and output PDF document
	
	//============================================================+
	// END OF FILE                                                 
	//============================================================+
	
		
	
		if(!isset($_POST['quick_pdf']))
		{
			// If this is NOT set show an image of our download 
			echo  '<a href="' . $uploadURL  . $pdf_file_name . '" target="_blank" ><img src="' . SBM_PLUGIN_URL . '/images/downloadpdf2.png"></a>';
		}
		else
		{
			//otherwise its a hyper link
			echo  '<a class="red" href="' . $uploadURL  . $pdf_file_name . '" target="_blank" >Download Ready ( click here )</a>';	
		}
		
	exit();
}

function sbm_suggest_destination()
{
	
	global $wpdb;
	global $current_user;
	$match = array();
	$destination = $_POST['destination'];
	
    get_currentuserinfo($current_user->ID);
	$user_info 	=  get_currentuserinfo($current_user->ID);

				
	$query = "SELECT DISTINCT
				odometer.*,
				meta.*
					
				FROM
					".$wpdb->prefix."sbm_odometer AS odometer
				LEFT JOIN		
					".$wpdb->prefix."sbm_odometer_meta AS meta
				ON
					odometer.ID = meta.odometer_id	
				WHERE 
					meta_key = 'destination'
				AND
					meta_value LIKE '%$destination%'
				AND
					visible = 1
				GROUP BY 
					meta_value";			
				
	$odometer_list 	= $wpdb->get_results($query);
	
	$result = array();
	
 	$total = 0;
			// If the odometer ID was not used as a display option use this    
			foreach( $odometer_list as $list )
			{
				
					$match[] = $list->meta_value;
					
			  }
		 
			  	 
			  	$new = array_unique($match);
			  	$total_results = count($new);
			  	
			  	if($total_results > 0 )
			  	{
			  		echo '<div>' . $total_results . ' Result';
			  		if( $total_results > 1 )
			  		{
			  			echo 's';
			  		}
			  		echo '</div>';
			  		$i = 1;
;			  		foreach($new as $item)
			  		{
				  		if($i <= 10)
				  		{
				  			echo '<div class="suggested_destination">' .$item . '</div>';
				  		} 
				  		$i++;
				  	}
				  	
			  	}
			  	else
			  	{
			  		echo 'No results match your information';
			  	}
			 
			  
	exit();	
}

function sbm_reverse_desposit()
{
	global $wpdb;
	$payee_payer_info 	= new sbm_payee_payer();
	$deposit_type_info 	= new sbm_deposit_type();
	$customer_info 		= new sbm_customer();
	$id 				= $_POST['ID'];
    get_currentuserinfo($current_user->ID);
    
    
    		$query = "SELECT 											
						payee_payer_id,
						transaction_type_id,
						deposit_type_id,
						description,
						amount,
						check_number
					FROM 
						".$wpdb->prefix."sbm_deposits
					WHERE
						ID = $id";	

					
		$transaction_info = $wpdb->get_row($query);
		//  classes/expense_type.php:     sbm_get_expense_type_data()
		$payee_payer_info->sbm_get_payee_payer_data($transaction_info->payee_payer_id);
		$deposit_type_info->sbm_get_deposit_type_data($transaction_info->deposit_type_id);
	
		$_POST['deposit_type_id'] 	= $id;
		$deposit_description 		= $deposit_type_info->payee_payer_name;
		$_POST['description'] 		= 'REVERSAL - ' . $transaction_info->description;
	    $_POST['amount'] 			= '-' .$transaction_info->amount;
	    $_POST['transaction_date']	= date("m/d/Y");
		$save 						= sbm_deposit_expense::sbm_update_deposit_expense('noredirect');
		
		if($save == true)
		{
			echo true;
		}
		else
		{
			echo false;
		}
	exit();
}

function sbm_reverse_expense()
{
	global $wpdb;
	$payee_payer_info 	= new sbm_payee_payer();
	$expense_type_info 	= new sbm_expense_type();
	$customer_info 		= new sbm_customer();
	$id 				= $_POST['ID'];
    get_currentuserinfo($current_user->ID);
    
    
    		$query = "SELECT 											
						payee_payer_id,
						transaction_type_id,
						expense_type_id,
						description,
						amount,
						check_number
					FROM 
						".$wpdb->prefix."sbm_expenses
					WHERE
						ID = $id";	

					
		$transaction_info = $wpdb->get_row($query);
		//  classes/expense_type.php:     sbm_get_expense_type_data()
		$payee_payer_info->sbm_get_payee_payer_data($transaction_info->payee_payer_id);
		$expense_type_info->sbm_get_expense_type_data($transaction_info->expense_type_id);
				
		$_POST['expense_type_id'] 	= $id;
		$expense_description 		= $expense_type_info->payee_payer_name;
		$_POST['description'] 		= 'REVERSAL - ' . $transaction_info->description;
	    $_POST['amount'] 			= '-' .$transaction_info->amount;
	    $_POST['transaction_date']	= date("m/d/Y");
		$save 						= sbm_deposit_expense::sbm_update_deposit_expense('noredirect');
		
		if($save == true)
		{
			echo true;
		}
		else
		{
			echo false;
		}
	exit();
	}

function sbm_save_odometer()
{
    $year       = $_POST['year'];
    $uploads    = wp_upload_dir();
    $uploadDir  = $uploads['basedir'].'/simple-business-manager/';
    $uploadURL  = $uploads['baseurl'].'/simple-business-manager/';
    $myFile     = 'odometer-' . $year.".csv";
    $fh         = fopen($uploadDir . $myFile, 'w+') or die("can't open file");
    $output     = sbm_view_odometer_list( 'just_output', $year );
    $data       = explode( '|', $output );
    $header     = "Date,Destination,total Miles,Starting Miles,Ending Miles,Payee Payer\n";
    fwrite($fh, $header);
    foreach($data as $row)
    {
       fwrite($fh, "$row\n");
    }


    fclose($fh);

    echo '<a href="' . $uploadURL . $myFile . '" target="_blank">' . $myFile . ' ready to download</a>';
exit();
}
function sbm_save_deposits_expenses()
{
    $year       = $_POST['year'];
    $uploads    = wp_upload_dir();
    $uploadDir  = $uploads['basedir'].'/simple-business-manager/';
    $uploadURL  = $uploads['baseurl'].'/simple-business-manager/';
    $myFile     = 'deposits-expenses-' . $year.".csv";
    $fh         = fopen($uploadDir . $myFile, 'w+') or die("can't open file");
    $output     = sbm_get_deposits_list($year, 'csv');
    $output    .= sbm_get_expense_list($year, 'csv');
    $data       = explode( '|', $output );
    $header     = "Date,Name,Description,Type,Amount\n";
    fwrite($fh, $header);
    foreach($data as $row)
    {
       fwrite($fh, "$row\n");
    }


    fclose($fh);

    echo '<a href="' . $uploadURL . $myFile . '" target="_blank">' . $myFile . ' ready to download</a>';
exit();
}

function sbm_count_miles_for_year_using_post()
{
    echo sbm_count_miles_for_year();
    exit();
}


?>