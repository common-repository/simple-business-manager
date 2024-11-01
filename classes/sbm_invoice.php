<?php

class sbm_invoice {
	
		
	public function sbm_get_invoice_data($invoice_id)
	{
		global $wpdb;
		//$wpdb->show_errors();
		
			$query = "SELECT meta_value, meta_key FROM ".$wpdb->prefix."sbm_invoice_data WHERE invoice_id = $invoice_id";
			$result = $wpdb->get_results($query);
			
			
			foreach($result as $row)
			{
				
				$key 				= $row->meta_key;
				$value 				= $row->meta_value;
				$this->$key 		= $value;	
				
								
			}
			
				$sql = "SELECT 
								invoice_type,
								paid_down,
								hourly_rate,
								tax_rate, 
								invoice_status
							FROM 
								".$wpdb->prefix."sbm_invoice
							WHERE
								ID = $invoice_id
							LIMIT 0 , 1";
				
				$row = $wpdb->get_row($wpdb->prepare($sql));
				
				$this->invoice_type 	= $row->invoice_type;
				$this->paid_down 		= $row->paid_down;
				$this->hourly_rate 		= $row->hourly_rate;
				$this->tax_rate 		= $row->tax_rate;
				$this->invoice_status	= $row->invoice_status;
			    
	}
	
	public function sbm_get_invoice_total( $invoice_id, $customer_id )
	{
		global $wpdb;
		$invoice_info = new sbm_invoice();
		//$wpdb->show_errors();
		
			$qty_query = "SELECT 
							meta_value, 
							meta_key
						FROM 
							".$wpdb->prefix."sbm_invoice_data 
						WHERE 
							invoice_id = $invoice_id
						AND
							meta_key LIKE '%_qty%'";
			$qty_result = $wpdb->get_results($qty_query);
			
			
			
			$price_query = "SELECT 
							meta_value, 
							meta_key
						FROM 
							".$wpdb->prefix."sbm_invoice_data 
						WHERE 
							invoice_id = $invoice_id
						AND
							meta_key LIKE '%_price%'";
			$price_result = $wpdb->get_results($price_query);
			
			
			$taxable_query = "SELECT 
							meta_value, 
							meta_key
						FROM 
							".$wpdb->prefix."sbm_invoice_data 
						WHERE 
							invoice_id = $invoice_id
						AND
							meta_key LIKE '%_taxable%'";
			$taxable_result = $wpdb->get_results($taxable_query);
			
			
			$invoice_query = "SELECT 
							paid_down, 
							tax_rate 
						FROM 
							".$wpdb->prefix."sbm_invoice 
						WHERE 
							ID = '$invoice_id'
						AND
							customer_id = '$customer_id'";
			$invoice_result = $wpdb->get_row($wpdb->prepare($invoice_query));
				
			$invoice_info->paid_down = $invoice_result->paid_down;
			$invoice_info->tax_rate = $invoice_result->tax_rate;

			$combined_array = array();
			$combined_array[] = array_merge( $qty_result, $price_result, $taxable_result) ;
			
			$new_array = array();
			
			
			$patterns = array();
			$patterns[0] = '/page/';
			$patterns[1] = '/_qty/';
			$patterns[2] = '/_price/';
			$patterns[3] = '/_taxable/';
			$replacements = array();
			$replacements[0] = '';
			$replacements[1] = '';
			$replacements[2] = '';
			$replacements[3] = '';
			
			foreach($combined_array[0] as $row)
			{
				
				$key 					= $row->meta_key;
				$value 					= $row->meta_value;
				$invoice_info->$key 	= $value;

				$num = preg_replace($patterns, $replacements, $key );

				if( preg_match( '/_qty/', $key, $matches ))
				{
					 $price[$num]	=	$value;
				}
				if( preg_match( '/_price/', $key, $matches ))
				{ 
					$qty[$num]	=		$value;
				}
				
				if( preg_match( '/_taxable/', $key, $matches ))
				{
					$taxable[$num]	= $value;
				}
				
			}
			
			if(!empty( $price ))
			{
				ksort( $price );
			}
			if(!empty( $qty ))
			{
				ksort( $qty );
			}
			if(!empty( $taxable ))
			{
				ksort( $taxable );
			}
			
			$total 				= 0;
			$line_total 		= 0;
			$taxable_line_total = 0;
			if( !empty( $price )) 
			{
				foreach($price as $key => $value)
				{
					$price_amount 	= floatval( $value );
					$qty_amount		= floatval( $qty[$key] );
					
					$line_total = $price_amount * $qty_amount;
					
					$tax_rate		= floatval( $invoice_info->tax_rate ) / 100;
					
					if($taxable[$key] == 'on')
					{	
						$taxable_line_total = ($line_total * $tax_rate) + $line_total;
						
						$total += $taxable_line_total;
					}
					else
					{
						$total += $line_total;
					}
				}
			}
		
	return $total;
	}
	
	public function sbm_get_invoice_difference($id)
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
	

	public function sbm_get_invoice_account_info( $customer_id, $customer_account_id )
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
	
    public function sbm_delete_invoice( $invoice_id, $customer_id )
    {
        global $wpdb;

        if($_POST['cancel_invoice'] == 'on' )
        {
            $query = "UPDATE ".$wpdb->prefix."sbm_invoice SET invoice_status = 'cancelled' WHERE ID = '$invoice_id' AND customer_id = '$customer_id'";
            $result = $wpdb->query($query);

                //  general_functions.php:     sbm_redirect()
                sbm_redirect('sbm_view_invoices', 'success_cancel_invoice');

        }
        else
        {
            $query = "DELETE FROM ".$wpdb->prefix."sbm_invoice WHERE ID = '$invoice_id' AND customer_id = '$customer_id'";
            $result = $wpdb->query($query);

            sbm_invoice::sbm_delete_invoice_data( $invoice_id );
            //  general_functions.php:     sbm_redirect()
            sbm_redirect('sbm_view_invoices', 'success_delete_invoice');
        }

    }
    public function sbm_reactivate_invoice( $invoice_id, $customer_id )
    {
        global $wpdb;

            $query = "UPDATE ".$wpdb->prefix."sbm_invoice SET invoice_status = 'pending' WHERE ID = '$invoice_id' AND customer_id = '$customer_id'";
            $wpdb->query($query);

                //  general_functions.php:     sbm_redirect()
                sbm_redirect('sbm_view_invoices', 'success_invoice');
    }

	public function sbm_delete_invoice_data($invoice_id)
	{
		global $wpdb;
		
			$query = "DELETE FROM ".$wpdb->prefix."sbm_invoice_data WHERE invoice_id = '$invoice_id'";
			$result = $wpdb->query($query);
			// redirect	
			//  general_functions.php:     sbm_redirect()
			//sbm_redirect('sbm_view_customer_list', 'delete_customer');
		return true;
	}	
	public function sbm_update_invoice_data( $invoice_id, $field, $value )
	{
		global $wpdb;
				$query = "UPDATE 
								".$wpdb->prefix."sbm_invoice
							SET
								$field = '$value'
							WHERE
								ID = $invoice_id";
				$wpdb->query( $wpdb->prepare( $query ) );							
		
	}
	
	public function sbm_pay_invoice()
	{
		global $wpdb;
		$invoice_info = new sbm_invoice();
		$wpdb->show_errors();
		// get a transaction id
		//  accounting_functions.php:     sbm_enter_transaction() 
		$transaction_id 	= sbm_enter_transaction(time());
		$customer_id 		= $_POST['customer_id'];
		$invoice_id 		= $_POST['invoice_id'];
		$amount_paid 		= floatval($_POST['amount_paid']);
		$payment_date		= $_POST['payment_date'];			
		$check_number		= $_POST['check_number'];
		$paid_with			= $_POST['paid_with'];
		$description		= $_POST['description'];
		$invoice_amount		= floatval($_POST['invoice_amount']);
        $already_paid       = floatval(sbm_get_total_amount_paid_for_invoice( $invoice_id, $customer_id  ));

		
		$date = sbm_convert_date( $payment_date );
		// Create the invoice ID
			// do an insert
			$query = "
						INSERT INTO 
							".$wpdb->prefix."sbm_customer_payments 
						( 
							transaction_id,
							customer_id,
							invoice_id,
							amount_paid,
							payment_date,
							check_number,
							paid_with,
							description
						 )
						VALUES
						(
							%d,
							%d,
							%d,
							%s,
							%s,
							%s,
							%s,
							%s
						)";
						
			$wpdb->query( $wpdb->prepare( $query, array(  $transaction_id, $customer_id, $invoice_id, $amount_paid, $date, $check_number, $paid_with, $description  ) ) );
		
		
		$total_paid		=  floatval( $amount_paid + $already_paid );

		// Update the invoice if amount paid is >= invoice amount
		if( $invoice_amount == $total_paid )
		{
			
			$invoice_info->sbm_update_invoice_data( $invoice_id, 'invoice_status', 'paid' );
			//  general_functions.php:     sbm_redirect()
			sbm_redirect('sbm_get_unpaid_invoices_list', 'success_invoice_exact_payment');
		}
		else if( $invoice_amount < $total_paid )
		{
			
			$invoice_info->sbm_update_invoice_data( $invoice_id, 'invoice_status', 'paid' );
			//  general_functions.php:     sbm_redirect()
			sbm_redirect('sbm_get_unpaid_invoices_list', 'success_invoice_over_payment');
		}
		else if( $invoice_amount > $total_paid )
		{
			//
			// do not mark the invoice as paid
			//  general_functions.php:     sbm_redirect()
			sbm_redirect('sbm_get_unpaid_invoices_list', 'success_invoice_under_payment');
		} else
        {
		
            // Should not get this far but just in case
            // failed
            //  general_functions.php:     sbm_redirect()
            sbm_redirect('sbm_get_unpaid_invoices_list', 'failed_pay_invoice');
            die();
        }
	}

	public function sbm_update_invoice($invoice_id)
	{

		global $wpdb;
		
				// remove all old data, then insert new
				if( sbm_invoice::sbm_delete_invoice_data( $invoice_id ) == false )
				{
					// redirect	
					//  general_functions.php:     sbm_redirect()
					sbm_redirect('sbm_view_customer_list', 'failed_delete_invoice_data');
				}
				else
				{
					foreach( $_POST as $key => $list )
					{
						if(!empty( $list ))
						{
							switch(  $key )
							{
								case 'status';
								case 'customer_id';
								case 'invoice_id';
								case 'choose_customer';
								case 'invoice_status';	
								case 'print_invoice';
								break;							
								case 'invoice_type';
								case 'tax_rate';
								case 'paid_down';
								case 'hourly_rate';
								 sbm_invoice::sbm_update_invoice_data( $invoice_id, $key, $list );
								break;

								break;
								default;
								
								$query = "INSERT INTO 
												".$wpdb->prefix."sbm_invoice_data 
											( 
												
												invoice_id,
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
								$wpdb->query( $wpdb->prepare( $query , array( $invoice_id, $key, $list ) ) );
								break;
							}
						}
					}
				}
								
					// redirect user back to the view customer list page
					//  general_functions.php:     sbm_redirect()
					sbm_redirect('sbm_view_invoices', 'success_invoice');
					die( 'There was a problem saving the invoice, contact customer service' );
	}
	
	public function sbm_create_invoice(  $time, $customer_id, $invoice_type, $hourly_rate, $paid_down, $tax_rate )
	{
		global $wpdb;
		
		
		
		// Create the invoice ID
			// do an insert
			$query = "
						INSERT INTO 
							".$wpdb->prefix."sbm_invoice 
						( 
							customer_id,
							tax_rate,
							invoice_date,
							hourly_rate,
							paid_down,
							invoice_type,
							invoice_status
						 )
						VALUES
						(
							%d,
							%d,
							%d,
							%s,
							%s,
							%s,
							%s
						)";
						
			$wpdb->query( $wpdb->prepare( $query, array(  $customer_id, $tax_rate, $time, $hourly_rate, $paid_down, $invoice_type, 'pending' ) ) );
					
			return $wpdb->insert_id;	
		
	}
	
}
?>