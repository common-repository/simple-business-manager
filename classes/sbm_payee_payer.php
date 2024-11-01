<?php

class sbm_payee_payer {
	
	public function sbm_create_payee_payer()
	{
		global $wpdb;
		
		$wpdb->insert(  $wpdb->prefix."sbm_payee_payer", array(  'visible'=> '1' ), array( '%d' ) );
			
		return $wpdb->insert_id;
		
	}
	
	public function sbm_get_payee_payer_data( $id )
	{
		global $wpdb;
		
		
			$query = "SELECT meta_value, meta_key FROM " . $wpdb->prefix . "sbm_payee_payer_meta WHERE payee_payer_id = $id";
			$result = $wpdb->get_results($query);
			
			foreach($result as $row)
			{
			
				$key 				= $row->meta_key;
				$value 				= $row->meta_value;
				$this->$key 		= $value;					
			}
				
	}
	
	public function sbm_get_payee_payer_balance($payee_payer_id)
	{
		global $wpdb;
		
	
							
			$sql = "SELECT 
							SUM(amount_due) as amount_due
						FROM 
							".$wpdb->prefix."sbm_payee_payer_account
						
							payee_payer_id =$payee_payer_id";
			 
			$amount_due=$wpdb->get_var($wpdb->prepare($sql));
	
			$sql = "SELECT 
							SUM(amount_paid) as amount_paid
						FROM 
							".$wpdb->prefix."sbm_payments
						
							payee_payer_id =$payee_payer_id";
			$amount_paid=$wpdb->get_var($wpdb->prepare($sql));
		
			$this->balance = ($amount_paid - $amount_due);
			
	 
	}
	
	public function sbm_get_difference($id)
	{
		global $wpdb;
		
			$sql ="SELECT 
						SUM(t1.amount_due - t2.amount_paid) AS total
					FROM
						".$wpdb->prefix."sbm_payee_payer_account as t1,
						".$wpdb->prefix."sbm_payments as t2 
					WHERE 
						
						t1.ID = $id ";
			$balance=$wpdb->get_var($wpdb->prepare($sql, $id));
	
			$this->total = $balance;
			
	 
	}
	

	public function sbm_get_payee_payer_account_info(  $payee_payer_id, $payee_payer_account_id )
	{
		global $wpdb;
			
				$query = "SELECT 
									meta_id,
									amount_due,
									transaction_date,
									description,
									meta_value
								FROM
									".$wpdb->prefix."sbm_payee_payer_account,
									".$wpdb->prefix."sbm_payee_payer_meta
								WHERE 
									
									".$wpdb->prefix."sbm_payee_payer_account.payee_payer_id = $payee_payer_id
								AND
									".$wpdb->prefix."sbm_payee_payer_account.ID = $payee_payer_account_id
								AND
									".$wpdb->prefix."sbm_payee_payer_account.meta_id = ".$wpdb->prefix."sbm_payee_payer_meta.ID";
														
				$result = $wpdb->get_row($query);
			
				$this->amount_due 					= $result->amount_due;		
				$this->transaction_date 			= $result->transaction_date;		
				$this->description 					= $result->description;		
				$this->description_meta_value 		= $result->meta_value;		
	 
	}

	public function sbm_delete_payee_payer($payee_payer_id)
	{
		global $wpdb;
		
			$query = "DELETE FROM ".$wpdb->prefix."sbm_payee_payer WHERE ID = '$payee_payer_id'";
			$wpdb->query($query);
			// redirect	
			//  general_functions.php:     sbm_redirect()
			sbm_redirect('sbm_view_payee_payer_list', 'delete_payee_payer');
	
	}
	
	public function sbm_submit_debit()
	{
		global $wpdb;
		global $current_user;
		 get_currentuserinfo($current_user->ID);
		
		$payee_payer_info = new sbm_payee_payer();
		//  classes/sbm_payee_payer.php:     sbm_get_payee_payer_data()
		$payee_payer_info->sbm_get_payee_payer_data($_POST['id']);
			

		
					$misc_date 									= explode("/", $_POST['misc_date']);
					$misc_month 								= $misc_date[0];
					$misc_day 									= $misc_date[1];
					$misc_year 									= $misc_date[2];
					// all should have a value before proceeding
					if(( !empty($misc_month)) && (!empty($misc_day)) && (!empty($misc_year)) )
					{
						$misc_time = mktime(0,0,0,$misc_month,$misc_day,$misc_year);
					}


									// First get the ID for the deposit from the meta table for this users
									$sql = "SELECT ID
												FROM 
													".$wpdb->prefix."sbm_payee_payer_meta
												WHERE
													
													meta_key = 'payee_payer_type'
												AND 
													meta_value = 'misc debit'
												LIMIT 0 , 1";
									$meta_id = $wpdb->get_var($wpdb->prepare($sql));
									
							// get a transaction id
							//  accounting_functions.php:     sbm_enter_transaction() 
							 $transaction_id = sbm_enter_transaction($misc_time);								
									// do an insert
										$wpdb->query( $wpdb->prepare( "
										INSERT INTO
											".$wpdb->prefix."sbm_payee_payer_account
													( 
													 	transaction_id, 
													 	payee_payer_id, 
													 	amount_due, 
													 	transaction_date,
														description
													 )
										VALUES
													(
	 
														%d, 
														%d,
														%s, 
														%d,
														%s 
													)", 
													array($transaction_id, $_POST['id'], $_POST['amount'], $misc_time, $_POST['description'] ) ) );
									
									
							
													//  general_functions.php:     sbm_redirect()
													sbm_redirect('sbm_view_payee_payer_list', 'misc_debit');

	}
	public function sbm_submit_credit()
	{
		global $wpdb;
		global $current_user;
		 get_currentuserinfo($current_user->ID);
		
		
		$payee_payer_info = new sbm_payee_payer();
		//  classes/sbm_payee_payer.php:     sbm_get_payee_payer_data()
		$payee_payer_info->sbm_get_payee_payer_data($_POST['id']);
			
					$misc_date 									= explode("/", $_POST['misc_date']);
					$misc_month 								= $misc_date[0];
					$misc_day 									= $misc_date[1];
					$misc_year 									= $misc_date[2];
					// all should have a value before proceeding
					if(( !empty($misc_month)) && (!empty($misc_day)) && (!empty($misc_year)) )
					{
						$misc_time = mktime(0,0,0,$misc_month,$misc_day,$misc_year);
					}


									// First get the ID for the deposit from the meta table for this users
									$sql = "SELECT ID
												FROM 
													".$wpdb->prefix."sbm_payee_payer_meta
												WHERE
													
													meta_key = 'payee_payer_type'
												AND 
													meta_value = 'misc credit'
												LIMIT 0 , 1";
									$meta_id = $wpdb->get_var($wpdb->prepare($sql));									
									
									
									// enter the transtion into the proper table
									//  accounting_functions.php:     sbm_enter_transaction() 
									$transaction_id = sbm_enter_transaction($misc_time);
									
										// Create a record in the payee_payer accounts table to link the payment to
										// do an insert
										$wpdb->query( $wpdb->prepare( "
										INSERT INTO
											".$wpdb->prefix."sbm_payee_payer_account
													( 
													 	transaction_id, 
													 	payee_payer_id, 
													 	amount_due, 
													 	transaction_date,
														description
													 )
										VALUES
													(
													 	%d, 
													 	%d, 
													 	%d, 
														%d,
														%d, 
														%s,
														%d, 
														%s
													)", 
													array($transaction_id, $_POST['id'], $amount, $misc_time, $_POST['description']) ) );

										
									$payee_payer_account_id = $wpdb->insert_id;
									
									// do an insert
										$wpdb->query( $wpdb->prepare( "
										INSERT INTO
											".$wpdb->prefix."sbm_payments
													( 
													 	transaction_id, 
														payee_payer_id,
													 	payee_payer_account_id, 
													 	amount_paid, 
													 	payment_date,
														description
													 )
										VALUES
													(
													 	%d, 
														%d,
														%d, 
														%s,
														%d, 
														%s
													)", 
													array($transaction_id, $_POST['id'], $payee_payer_account_id,  $_POST['amount'], $misc_time, $_POST['description']) ) );
									
									
													//  general_functions.php:     sbm_redirect()
													sbm_redirect('sbm_view_payee_payer_list', 'misc_credit');

	}
	
	
	// if the variable is passed, it will not redirect, otherwise it will redirect
	public function sbm_update_payee_payer($noredirect = '')
	{

		global $wpdb;
		global $current_user;
		 get_currentuserinfo($current_user->ID);
		
		
		if(!empty($_POST['payee_payer_id']))
		{
			
			$payee_payer_id = $_POST['payee_payer_id'];
			// Remove all old entries
			$query = "DELETE FROM 
								".$wpdb->prefix."sbm_payee_payer_meta 
							WHERE 
								payee_payer_id = '{$_POST['payee_payer_id']}' 
							";
			$wpdb->query($query);
		}
		else
		{
			$payee_payer_id = sbm_payee_payer::sbm_create_payee_payer();	
		}
			
					foreach($_POST as $key => $value)
					{
						
						if(!empty($value))
						{
							switch($key)
							{
								case 'payee_payer_id':
								// also skip these when creating this on the fly
								case 'name';
								case 'transaction_date':
								case 'transaction_type_id':
								case 'amount':
								case 'check_number':
								case 'description':
								case 'expense_type_id':
								case 'deposit_type_id':
								break;
								default;
							$query = "INSERT INTO 
											".$wpdb->prefix."sbm_payee_payer_meta
										( 
											payee_payer_id,
											meta_key, 
											meta_value 
										 )
										VALUES
										(
											%d,
											%s, 
											%s
										)";
										
							// do an insert
							$wpdb->query( $wpdb->prepare( $query , array(  $payee_payer_id, $key, $value ) ) );
								break;
							}
						}
					
					}

				
					if(empty($_POST['not_visible']))
					{
						$visible = 1; // stays visible
					}
					else
					{
						$visible = 0; // is hidden from data being shown, basically it appears as if this is deleted
					}
					// Update the original table with the new values
					$query = "UPDATE ".$wpdb->prefix."sbm_payee_payer SET visible = '$visible' WHERE ID = '$payee_payer_id'";
					$wpdb->query($query);	
					
				if(empty($noredirect))
				{	
					// redirect user back to the view payee_payer list page
					//  general_functions.php:     sbm_redirect()
					sbm_redirect('sbm_view_payee_payer_list', 'success_payee_payer');
				}
				else
				{
					// This is creating the payee/payer on the fly, we do not want a redirect
					// Simply return the id
					return $payee_payer_id;	
				}
					
	}
	

	
}
?>