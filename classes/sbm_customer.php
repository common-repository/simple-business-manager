<?php

class sbm_customer {
	
	public function sbm_create_customer()
	{
		global $wpdb;
		
		$wpdb->insert(  $wpdb->prefix."sbm_customer", array(  'visible'=> '1' ), array( '%d' ) );
			
		return $wpdb->insert_id;
		
	}
	
	public function sbm_get_customer_data( $id )
	{
		global $wpdb;
		
		if($id != null )
        {


			$query = "SELECT meta_value, meta_key FROM " . $wpdb->prefix . "sbm_customer_meta WHERE customer_id = $id";
			$result = $wpdb->get_results($query);
			
			foreach($result as $row)
			{
			
				$key 				= $row->meta_key;
				$value 				= $row->meta_value;
				$this->$key 		= $value;					
			}
        }
        else
        {
            $this->company_name   = null;
        }
				
	}
	
	public function sbm_get_customer_balance($customer_id)
	{
		global $wpdb;
        $invoice = new sbm_invoice();

			$sql = "SELECT
							( SUM(amount_paid) )
						FROM 
							".$wpdb->prefix."sbm_customer_payments AS cp
						WHERE
						    cp.customer_id =$customer_id";
			 
			$amount_paid = ( int ) $wpdb->get_var($wpdb->prepare($sql));
	
			// Get all invoices and then add up their totals

			$sql = "SELECT
			            ID
			        FROM
			            ".$wpdb->prefix."sbm_invoice
			        WHERE
			            customer_id = '$customer_id'
			        AND
                        invoice_status != 'cancelled'
                    AND
                        invoice_status != 'pending'";
            $result = $wpdb->get_results($sql);

            $total_amount = 0;
            foreach($result as $invoice_id )
            {
                $total_amount += ( int )$invoice->sbm_get_invoice_total( $invoice_id->ID, $customer_id );

            }
            $balance =  $amount_paid -  $total_amount;

            return $this->balance = $balance;
	 
	}
	
	public function sbm_get_difference($id)
	{
		global $wpdb;
		
			$sql ="SELECT 
						SUM(t1.amount_due - t2.amount_paid) AS total
					FROM
						".$wpdb->prefix."sbm_customer_account as t1,
						".$wpdb->prefix."sbm_payments as t2 
					WHERE 
						
						t1.ID = $id ";
			$balance=$wpdb->get_var($wpdb->prepare($sql, $id));
	
			$this->total = $balance;
			
	 
	}
	

	public function sbm_get_customer_account_info(  $customer_id, $customer_account_id )
	{
		global $wpdb;
			
				$query = "SELECT 
									meta_id,
									amount_due,
									transaction_date,
									description,
									meta_value
								FROM
									".$wpdb->prefix."sbm_customer_account,
									".$wpdb->prefix."sbm_customer_meta
								WHERE 
									
									".$wpdb->prefix."sbm_customer_account.customer_id = $customer_id
								AND
									".$wpdb->prefix."sbm_customer_account.ID = $customer_account_id
								AND
									".$wpdb->prefix."sbm_customer_account.meta_id = ".$wpdb->prefix."sbm_customer_meta.ID";
														
				$result = $wpdb->get_row($query);
			
				$this->amount_due 					= $result->amount_due;		
				$this->transaction_date 			= $result->transaction_date;		
				$this->description 					= $result->description;		
				$this->description_meta_value 		= $result->meta_value;		
	 
	}

	public function sbm_delete_customer($customer_id)
	{
		global $wpdb;
		
			$query = "DELETE FROM ".$wpdb->prefix."sbm_customer WHERE ID = '$customer_id'";
			$wpdb->query($query);
			// redirect	
			//  general_functions.php:     sbm_redirect()
			sbm_redirect('sbm_view_customer_list', 'delete_customer');
	
	}
	
	public function sbm_submit_debit()
	{
		global $wpdb;
		global $current_user;
		 get_currentuserinfo($current_user->ID);
		
		$customer_info = new sbm_customer();
		//  classes/sbm_customer.php:     sbm_get_customer_data()
		$customer_info->sbm_get_customer_data($_POST['id']);
			

		
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
													".$wpdb->prefix."sbm_customer_meta
												WHERE
													
													meta_key = 'customer_type'
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
											".$wpdb->prefix."sbm_customer_account
													( 
													 	transaction_id, 
													 	customer_id, 
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
													sbm_redirect('sbm_view_customer_list', 'misc_debit');

	}
	public function sbm_submit_credit()
	{
		global $wpdb;
		global $current_user;
		 get_currentuserinfo($current_user->ID);
		
		
		$customer_info = new sbm_customer();
		//  classes/sbm_customer.php:     sbm_get_customer_data()
		$customer_info->sbm_get_customer_data($_POST['id']);
			
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
													".$wpdb->prefix."sbm_customer_meta
												WHERE
													
													meta_key = 'customer_type'
												AND 
													meta_value = 'misc credit'
												LIMIT 0 , 1";
									$meta_id = $wpdb->get_var($wpdb->prepare($sql));									
									
									
									// enter the transtion into the proper table
									//  accounting_functions.php:     sbm_enter_transaction() 
									$transaction_id = sbm_enter_transaction($misc_time);
									
										// Create a record in the customer accounts table to link the payment to
										// do an insert
										$wpdb->query( $wpdb->prepare( "
										INSERT INTO
											".$wpdb->prefix."sbm_customer_account
													( 
													 	transaction_id, 
													 	customer_id, 
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

										
									$customer_account_id = $wpdb->insert_id;
									
									// do an insert
										$wpdb->query( $wpdb->prepare( "
										INSERT INTO
											".$wpdb->prefix."sbm_payments
													( 
													 	transaction_id, 
														customer_id,
													 	customer_account_id, 
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
													array($transaction_id, $_POST['id'], $customer_account_id,  $_POST['amount'], $misc_time, $_POST['description']) ) );
									
									
													//  general_functions.php:     sbm_redirect()
													sbm_redirect('sbm_view_customer_list', 'misc_credit');

	}
	
	

	public function sbm_update_customer( $public = '' )
	{
		global $wpdb;
		global $current_user;
		 get_currentuserinfo($current_user->ID);
		
		
		if(!empty($_POST['customer_id']))
		{
			
			$customer_id = $_POST['customer_id'];
			// Remove all old entries
			$query = "DELETE FROM 
								".$wpdb->prefix."sbm_customer_meta 
							WHERE 
								customer_id = '{$_POST['customer_id']}' 
							";
			$wpdb->query($query);
		}
		else
		{
			$customer_id = sbm_customer::sbm_create_customer();	
		}
			
			
					foreach($_POST as $key => $value)
					{
						
						if(!empty($value))
						{
							switch($key)
							{
								case 'customer_id';
								break;
								default;
							$query = "INSERT INTO 
											".$wpdb->prefix."sbm_customer_meta
										( 
											customer_id,
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
							$wpdb->query( $wpdb->prepare( $query , array(  $customer_id, $key, $value ) ) );
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
					$query = "UPDATE ".$wpdb->prefix."sbm_customer SET visible = '$visible' WHERE ID = '$customer_id'";
					$wpdb->query($query);	
					
					//  If password is set and email_1, create a user account
					if( ( isset($_POST['password'] ) ) && ( ( isset( $_POST['email_1'] ) ) ) )
					{
                      
						// First make sure that the email address has not be used already
						if( ( sbm_user_email_available( $_POST['email_1'] ) == true ) && ( strlen(sbm_get_user_id( $customer_id ) ) == 0  ) )
						{

							// enter the customer into the users table
							$user_login 		= $_POST['email_1'];
							$user_password		= MD5($_POST['password']);
							$user_nicename 		= $_POST['email_1'];
							$user_email			= $_POST['email_1'];
							$user_registered 	= date("Y-m-d H:i:s");
							$user_status		= '0';
							$display_name		= $_POST['email_1'];							
							
							$wpdb->insert(  $wpdb->prefix."users", array(  
																			'user_login'=> $user_login,
																			'user_pass' => $user_password,
																			'user_nicename' => $user_nicename,
																			'user_email' => $user_email,
																			'user_registered' => $user_registered,
																			'user_status' => $user_status,
																			'display_name' => $display_name
																		 ), 
																	array( 
																			'%s',
																			'%s',
																			'%s',
																			'%s',
																			'%s',
																			'%s' 
																		) );
								
							$user_id = $wpdb->insert_id;
							
							// Enter the user meta data into the usermeta table
							
							$user_meta_data = array(
												'first_name'=>$_POST['first_name_1'],
												'last_name'=>$_POST['last_name_1'],
												'nickname'=>$_POST['email_1'],
												'description'=>NULL,
												'rich_editing'=>'true',
												'comment_shortcuts'=> 'false',
												'admin_color'=>'fresh',
												'use_ssl'=>'0',
												'show_admin_bar_front'=>'false',
												'show_admin_bar_admin'=>'false',
												'aim'=>NULL,
												'yim'=>NULL,
												'jabber'=>NULL,
												$wpdb->prefix . 'capabilities'=>'a:1:{s:10:"subscriber";s:1:"1";}',
												$wpdb->prefix . 'user_level'=>'0',
												'customer_id'=>$customer_id
												);
							foreach($user_meta_data as $key => $value)
							{
								$query = "INSERT INTO 
												".$wpdb->prefix."usermeta
											( 
												user_id,
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
								$wpdb->query( $wpdb->prepare( $query , array(  $user_id, $key, $value ) ) );
							}
						}
						else
						{
							// Get the user id
							$user_id = sbm_get_user_id( $customer_id );
							// If its not available, does this email address belong to this customer
							if( sbm_customer_user_email_validate($_POST['email_1'], $user_id) == true )
							{
								
								if(!empty($user_id))
								{
									$query = "UPDATE ".$wpdb->prefix."users SET user_pass = MD5('{$_POST['password']}') WHERE ID = '$user_id'";
									$wpdb->query($query);
								}
							}
						}
						
					}
					
						// final part, either return true or do a redirect
					
						if($public)
						{
							// this is a public user sign up, send a message
							return TRUE;
							
						}
						else
						{
							// redirect user back to the view customer list page
							//  general_functions.php:     sbm_redirect()
							sbm_redirect('sbm_view_customer_list', 'success_customer');
							
						}
					
						
	}
	

	
}
?>