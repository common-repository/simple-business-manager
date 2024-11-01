<?php

function sbm_get_invoice_balance($customer_id)
{
	global $wpdb;



		$sql = "SELECT
						SUM(amount_paid) as amount_paid
					FROM
						".$wpdb->prefix."sbm_customer_payments

					AND
						customer_id =$customer_id";
		$amount_paid=$wpdb->get_var($wpdb->prepare($sql));

		return ($amount_paid - $amount_due);


}

function sbm_get_total_number_of_paid_invoices( $year = '' )
{
	global $wpdb;

	if( empty( $year ) )
	{
		$sql = "SELECT COUNT(*)	FROM ".$wpdb->prefix."sbm_invoice WHERE invoice_status = 'paid'";
		$total_paid=$wpdb->get_var($wpdb->prepare($sql));
	}
	else
	{
		$early = mktime(0,0,0,1,1, $year);
		$late  = mktime(23,59,59,12,31, $year);

		$sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."sbm_invoice WHERE invoice_status = 'paid' AND invoice_date BETWEEN $early AND $late";
		$total_paid=$wpdb->get_var($wpdb->prepare($sql));
	}

		return $total_paid;

}


function sbm_get_all_invoices_for_year( $year = '' )
{
	global $wpdb;

	if( empty( $year ) )
	{
		$sql = "SELECT COUNT(*)	FROM ".$wpdb->prefix."sbm_invoice WHERE invoice_status != 'cancelled'";
		$total = $wpdb->get_var($wpdb->prepare($sql));

	}
	else
	{
		$early = mktime(0,0,0,1,1, $year);
		$late  = mktime(23,59,59,12,31, $year);

		$sql = "SELECT COUNT(*)	FROM ".$wpdb->prefix."sbm_invoice WHERE invoice_status != 'cancelled' AND invoice_date BETWEEN $early AND $late";
		$total = $wpdb->get_var($wpdb->prepare($sql));
	}
		return $total;

}

function sbm_get_all_invoices_total_amount( $year = '' )
{

	global $wpdb;
	$invoice_info = new sbm_invoice();
	$total = 0;

	// Get all the invoices and then we can get the total amount for each invoice
	if( empty( $year ) )
	{
					$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.customer_id as customer_id
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status != 'cancelled'
						ORDER BY
							invoice_date DESC";

					$invoice_list = $wpdb->get_results($query);

					foreach($invoice_list as $list)
					{
					 	$total += $invoice_info->sbm_get_invoice_total( $list->invoice_id, $list->customer_id );
					}
	}
	else
	{
		$early = mktime(0,0,0,1,1, $year);
		$late  = mktime(23,59,59,12,31, $year);

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

					foreach($invoice_list as $list)
					{
					 	$total += $invoice_info->sbm_get_invoice_total( $list->invoice_id, $list->customer_id );
					}


	}
		return number_format( $total, 2 );
}

function sbm_get_paid_invoices_total_amount( $year = '' )
{
	global $wpdb;
	$invoice_info = new sbm_invoice();
	$total = 0;

	// Get all the invoices and then we can get the total amount for each invoice
	if( empty( $year ) )
	{
					$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.customer_id as customer_id
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'paid'
						ORDER BY
							invoice_date DESC";

					$invoice_list = $wpdb->get_results($query);

					foreach($invoice_list as $list)
					{
					 	$total += $invoice_info->sbm_get_invoice_total( $list->invoice_id, $list->customer_id );
					}
	}
	else
	{
		$early = mktime(0,0,0,1,1, $year);
		$late  = mktime(23,59,59,12,31, $year);

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

					foreach($invoice_list as $list)
					{
					 	$total += $invoice_info->sbm_get_invoice_total( $list->invoice_id, $list->customer_id );
					}


	}
		return number_format( $total, 2 );
}

function sbm_get_year_range_from_invoices()
{
	global $wpdb;

		$sql = "SELECT invoice_date	FROM ".$wpdb->prefix."sbm_invoice ORDER BY invoice_date ASC LIMIT 0,1";
		$oldest =$wpdb->get_var($wpdb->prepare($sql));

		$sql = "SELECT invoice_date	FROM ".$wpdb->prefix."sbm_invoice ORDER BY invoice_date DESC LIMIT 0,1";
		$newest =$wpdb->get_var($wpdb->prepare($sql));

		return array(date("Y", $oldest), date("Y", $newest));

}
function sbm_invoices_view_options()
{
	global $wpdb;
	global $current_user;

     get_currentuserinfo($current_user->ID);
	$customer_info = new sbm_customer();

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
	echo '<h2>What do you want to do?</h2>';
	//  general_functions.php:     sbm_check_read_only_user()
	if( sbm_check_read_only_user() == false )
	{
		$text = 'Create/View/Edit';

		echo '<div><a href="admin.php?page=sbm_create_invoice&status=new">Create a new invoice</a></div>';
	}
	else
	{
		$text = 'View';
	}

	echo '<div><a href="admin.php?page=sbm_view_invoices">' . $text . ' invoices</a></div>';

	echo '<div>';
}


function sbm_view_invoices()
{
	global $current_user;

     get_currentuserinfo($current_user->ID);
    $invoice_status = null;


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

	if( sbm_get_current_user_level($current_user->ID) > 0 )
	{
	?>
	    <div>
			<input type="button" value="Add a new Invoice" onclick="javascript: window.location = './admin.php?page=sbm_create_invoice&status=new';">
		</div>
	<?php
	}

	?>
    <div id="invoice_list">
        <table class="bw_table" id="invoices">
                    <th colspan="10">View invoices</th>
                    <tr class="descriptions">
                        <td class="center-text">Invoice #</td>
                        <td>Date</td>
                        <td>Company</td>
                        <td>First Name</td>
                        <td>Last Name</td>
                        <td>Invoice Total</td>
                        <td>Already Paid</td>
                        <td>Invoice Balance</td>
                        <td>Invoice Status</td>
                        <td>Update / Pay Invoice</td>
                    </tr>
	<?php

		echo sbm_get_invoice_list( $invoice_status );
	?>


		</table>
    </div>
	<?php

	if( sbm_get_current_user_level($current_user->ID) > 0 )
	{
	?>
	    <div class="clear">
			<input type="button" value="Add a new Invoice" onclick="javascript: window.location = './admin.php?page=sbm_create_invoice&status=new';">
		</div>
	<?php
	}

    ?>
	</div>
    <?php
}

function sbm_get_invoice_list_by_customer_id( $invoice_status, $customer_id )
{

	global $wpdb;
	$customer_info 	= new sbm_customer();
	$invoice_info 	= new sbm_invoice();
	$currency_symbol = get_option( 'sbm_currency' );
	$total_paid_invoices = 0;

	//$wpdb->show_errors();

		switch( $invoice_status )
		{
			case 'pending';
			// Only show customers with invoice with a status as pending
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'pending'
						AND
							".$wpdb->prefix."sbm_invoice.customer_id = '$customer_id'
						ORDER BY
							invoice_date DESC";
			break;
			case 'invoiced';
			// Only show customers with invoice with a status as invoiced
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'invoiced'
						AND
							".$wpdb->prefix."sbm_invoice.customer_id = '$customer_id'
						ORDER BY
							invoice_date DESC";

			break;
			case 'paid';
			// Only show customers with invoice with a status as invoiced
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'paid'
						AND
							".$wpdb->prefix."sbm_invoice.customer_id = '$customer_id'
						ORDER BY
							invoice_date DESC";
			break;
			case 'cancelled';
			// Only show customers with invoice with a status as invoiced
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'cancelled'
						AND
							".$wpdb->prefix."sbm_invoice.customer_id = '$customer_id'
						ORDER BY
							invoice_date DESC";
			break;
			default;

			// Only show customers with invoice with a status as invoiced
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.customer_id = '$customer_id'
						ORDER BY
							invoice_date DESC";
			break;
		}

		$invoice_list = $wpdb->get_results($query);


		if( count( $invoice_list ) == 0 )
		{
				$content .= '<tr class="odd_bg">
							<td colspan="9" class="center_text"><h3>This customer does not have any invoices</h3></td>
						</tr>';
		}
		else
		{

			$i = 1;
			$amount = 0;
			$count_paid = 0;
			foreach($invoice_list as $list)
			{


					//  classes/sbm_customer.php:     sbm_get_customer_data()
					$customer_info->sbm_get_customer_data( $customer_id );


					$q = $i % 2;
					// if there is a remainder it is an odd row
					if( $q > 0)
					{
						$bg = 'even_bg';
					}
					else
					{
						$bg = 'odd_bg';
					}


					$content .= '<tr id="' . $list->invoice_id . '" class="' . $bg . '">
								<td><div id="download_pdf_invoice_' . $list->invoice_id . '"><a class="quick_pdf_invoice_link" href="javascript: void(0);">Click to prepare download</a><input type="hidden" class="invoice_id" value="'.$list->invoice_id.'"></td>
								<td class="center-text" id="click_to_edit_invoice_' . $list->invoice_id . '">';
								switch( $list->invoice_status )
								{
									case 'pending':
									if(sbm_check_read_only_user() == false)
									{
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=edit&invoice_id=' . $list->invoice_id . '&customer_id=' . $customer_id . '">Edit #' . $list->invoice_id . '</a>';
										$content .= '<div class="update_invoice"><div class="convert_invoice" id="convert_invoice_' . $list->invoice_id . '">
											<div><a class="close_update_invoice" href="javascript: void(0); return false; ">Cancel</a></div>
											<h3>Invoice # ' . $list->invoice_id . ' for customer ' . $customer_info->company_name . '</h3>
											<a class="convert_pending_to_invoiced">Change from pending to invoiced</a>

										</div></div>';
									}
									else
									{
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=edit&invoice_id=' . $list->invoice_id . '&customer_id=' . $list->customer_id . '">View #' . $list->invoice_id . '</a>';

									}
									break;
									case 'invoiced':
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=invoiced&invoice_id=' . $list->invoice_id . '&customer_id=' . $list->customer_id . '">View Invoice #' . $list->invoice_id . '</a>';

									break;
									case 'paid':
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=paid&invoice_id=' . $list->invoice_id . '&customer_id=' . $customer_id . '">View Invoice #' . $list->invoice_id . '</a>';

										$count_paid += 1;
									break;
									case 'cancelled':
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=cancelled&invoice_id=' . $list->invoice_id . '&customer_id=' . $customer_id . '">View Invoice #' . $list->invoice_id . '</a>';

									break;
									default;
										$content .= '';
									break;

								}



					$invoice_total	= $invoice_info->sbm_get_invoice_total( $list->invoice_id, $customer_id );
					$already_paid   = sbm_get_total_amount_paid_for_invoice( $list->invoice_id, $customer_id  );




					$content .= '</td>
								<td>' . date("m/d/Y", $list->invoice_date) . '</td>

								<td>' . $currency_symbol. '' . number_format( $invoice_total, 2 ) . '</td>
								<td>' . $currency_symbol . '' . number_format( $already_paid, 2 )  . '</td>
								<td>' . $currency_symbol . '' . number_format( ($invoice_total - $already_paid), 2 ) . '</td>
								<td id="status_' . $list->invoice_id . '">' . $list->invoice_status. '</td>
								<td id="update_' . $list->invoice_id . '">';
								switch( $list->invoice_status)
								{
									case 'pending';
										if(sbm_check_read_only_user() == false )
										{
											$content .= '<a class="convert_to_invoice" href="javascript:void(0); return false;">Change Status to Invoiced</a><input type="hidden" class="invoice" value="' . $list->invoice_id .'"><input type="hidden" class="customer" value="' . $customer_id . '">';
										}
										else
										{
											$content .= 'Pending';
										}
									break;
									case 'invoiced';
									if(sbm_check_read_only_user() == false )
										{
											$content .= '<a href="./admin.php?page=sbm_pay_invoice&invoice_id=' . $list->invoice_id .'&customer_id=' . $customer_id . '">Pay</a>';
										}
										else
										{
											$content .= 'Invoiced';
										}
									break;
									case 'paid';
										$content .= 'Paid';
										$total_paid_invoices += $invoice_total;
									break;
									case 'cancelled';
										$content .= 'Cancelled';
									break;
									default;
										//$content .= '<a href="./admin.php?page=sbm_cancel_invoice&invoice_id=' . $list->invoice_id .'&customer_id=' . $list->customer_id . '">click to cancel</a>';
										$content .= '';
									break;

								}
								$content .= '</td>
							</tr>';

					$i++;



			}
			$content .='<tr><td></td><td></td><td>Total Paid Invoices ( ' . $count_paid . ' )</td><td>' . $currency_symbol . number_format($total_paid_invoices, 2 ) . '</td><td colspan="5"></td></tr>';
		}
		return $content;


}

function sbm_get_total_amount_paid_for_invoice( $invoice_id, $customer_id )
{
	global $wpdb;

	$query = "SELECT SUM( amount_paid ) FROM " . $wpdb->prefix . "sbm_customer_payments WHERE invoice_id = '$invoice_id' AND customer_id = '$customer_id'";

	$balance = $wpdb->get_var($wpdb->prepare($query));

	return $balance;

}
function sbm_get_invoice_list( $invoice_status )
{
	global $wpdb;
	global $current_user;
	$customer_info 		= new sbm_customer();
	$invoice_info 		= new sbm_invoice();
	$currency_symbol 	= get_option( 'sbm_currency' );
	$user_level         = $current_user->user_level;
    $customer_id        = $current_user->customer_id;

	//$wpdb->show_errors();
	if($user_level == 0)
    {
        $add = " AND customer_id = $customer_id ";
    }
		switch( $invoice_status )
		{
			case 'pending';
			// Only show customers with invoice with a status as pending
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.customer_id as customer_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'pending'
							$add
						ORDER BY
							invoice_date DESC";
			break;
			case 'invoiced';
			// Only show customers with invoice with a status as invoiced
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.customer_id as customer_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'invoiced'
							$add
						ORDER BY
							invoice_date DESC";

			break;
			case 'paid';
			// Only show customers with invoice with a status as invoiced
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.customer_id as customer_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'paid'
							$add
						ORDER BY
							invoice_date DESC";
			break;
			case 'cancelled';
			// Only show customers with invoice with a status as invoiced
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.customer_id as customer_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'cancelled'
							$add
						ORDER BY
							invoice_date DESC";
			break;
			default;
			// Get the customer_id
			//$customer_id = sbm_get_user_customer_id($current_user->ID);
			// Only show customers with invoice with a status as invoiced
			// Only show customers with invoice with a status as pending
			/*$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.customer_id as customer_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
						WHERE
							".$wpdb->prefix."sbm_invoice.invoice_status = 'pending'
						ORDER BY
							invoice_date DESC";
							*/
                if(!empty($add))
                {
                    // change to where
                    $where = " WHERE customer_id = $customer_id ";
                }

			// Only show customers with invoice with a status as pending
			$query = "SELECT
							".$wpdb->prefix."sbm_invoice.ID as invoice_id,
							".$wpdb->prefix."sbm_invoice.customer_id as customer_id,
							".$wpdb->prefix."sbm_invoice.invoice_date,
							".$wpdb->prefix."sbm_invoice.invoice_status
						FROM
							".$wpdb->prefix."sbm_invoice
                               $where
						ORDER BY
							invoice_date DESC";
			break;
		}

		$invoice_list = $wpdb->get_results($query);


		if( count( $invoice_list ) == 0 )
		{
            if($user_level > 0 )
            {
                $content .= '<tr class="odd_bg">
                            <td colspan="10" class="center_text"><h3>No customers have invoices</h3></td>
                        </tr>';
            }
            else
            {
                $content .= '<tr class="odd_bg">
                            <td colspan="10" class="center_text"><h3>Your account does not have any invoices</h3></td>
                        </tr>';
            }
		}
		else
		{

			$i = 1;
			$amount = 0;
			foreach($invoice_list as $list)
			{


					//  classes/sbm_customer.php:     sbm_get_customer_data()
					$customer_info->sbm_get_customer_data( $list->customer_id );

					if( !empty( $customer_info->currency ) )
					{
						$currency_symbol 	= get_option( 'sbm_currency' );
					}

					$q = $i % 2;
					// if there is a remainder it is an odd row
					if( $q > 0)
					{
						$bg = 'even_bg';
					}
					else
					{
						$bg = 'odd_bg';
					}


					$content .= '<tr id="' . $list->invoice_id . '" class="' . $bg . '">
								<td class="center-text" id="click_to_edit_invoice_' . $list->invoice_id . '">';
								switch( $list->invoice_status )
								{
									case 'pending';

									if( sbm_check_read_only_user() == false )
									{
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=edit&invoice_id=' . $list->invoice_id . '&customer_id=' . $list->customer_id . '">Edit #' . $list->invoice_id . '</a>';
										$content .= '<div class="update_invoice"><div class="convert_invoice" id="convert_invoice_' . $list->invoice_id . '">
											<div><a class="close_update_invoice" href="javascript: void(0); return false; ">Cancel</a></div>
											<h3>Invoice # ' . $list->invoice_id . ' for customer ' . $customer_info->company_name . '</h3>
											<a class="convert_pending_to_invoiced">Change from pending to invoiced</a>

										</div></div>';
									}
									else
									{
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=edit&invoice_id=' . $list->invoice_id . '&customer_id=' . $list->customer_id . '">View #' . $list->invoice_id . '</a>';
									}
									break;
									case 'invoiced';
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=invoiced&invoice_id=' . $list->invoice_id . '&customer_id=' . $list->customer_id . '">View Invoice #' . $list->invoice_id . '</a>';
									break;
									case 'paid';
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=paid&invoice_id=' . $list->invoice_id . '&customer_id=' . $list->customer_id . '">View Paid Invoice #' . $list->invoice_id . '</a>';
									break;
									case 'cancelled';
										$content .= '<a href="./admin.php?page=sbm_create_invoice&status=cancelled&invoice_id=' . $list->invoice_id . '&customer_id=' . $list->customer_id . '">View Cancelled #' . $list->invoice_id . '</a>';
									break;
									default;
										$content .= '';
									break;

								}
					$invoice_total	= $invoice_info->sbm_get_invoice_total( $list->invoice_id, $list->customer_id );
					$already_paid   = sbm_get_total_amount_paid_for_invoice( $list->invoice_id, $list->customer_id  );

					$content .= '</td>

								<td>' . date("m/d/Y", $list->invoice_date) . '</td>
								<td><a href="admin.php?page=sbm_view_customer_account&customer_id=' . $list->customer_id  . '">' . $customer_info->company_name . '</a></td>
								<td>' . $customer_info->first_name_1 . '</td>
								<td>' . $customer_info->last_name_1 . '</td>
								<td>' . $currency_symbol . '' . number_format( $invoice_total, 2 ) . '</td>
								<td>' . $currency_symbol . '' . number_format( $already_paid, 2 )  . '</td>
								<td>' . $currency_symbol . '' . number_format( ($invoice_total - $already_paid), 2 ) . '</td>
								<td id="status_' . $list->invoice_id . '">' . $list->invoice_status. '</td>
								<td id="update_' . $list->invoice_id . '">';
								switch( $list->invoice_status)
								{
									case 'pending';
										if(sbm_get_current_user_level($current_user->ID) > 0 )
										{
											$content .= '<a class="convert_to_invoice" href="javascript:void(0); return false;">Change Status to Invoiced</a><input type="hidden" class="invoice" value="' . $list->invoice_id .'"><input type="hidden" class="customer" value="' . $list->customer_id . '">';
										}
										else
										{
											$content .= 'Pending';
										}
									break;
									case 'invoiced';
										if(sbm_get_current_user_level($current_user->ID) > 0 )
										{
											$content .= '<a href="./admin.php?page=sbm_pay_invoice&invoice_id=' . $list->invoice_id .'&customer_id=' . $list->customer_id . '">Pay</a>';
										}
										else
										{
											$content .= 'Invoiced';
										}
									break;
									case 'paid';
										$content .= 'Paid';
									break;
									case 'cancelled';
										$content .= 'Cancelled';
									break;
									default;
										//$content .= '<a href="./admin.php?page=sbm_cancel_invoice&invoice_id=' . $list->invoice_id .'&customer_id=' . $list->customer_id . '">click to cancel</a>';
										$content .= '';
									break;

								}
								$content .= '</td>
							</tr>';

					$i++;

	                // Reset the company name to NULL to prevent it from showing up if the next entry is empty
                    $customer_info->company_name = NULL;

			}
		}
		return $content;

}



function sbm_get_pending_invoices_list()
{
	global $wpdb;
	global $current_user;

     get_currentuserinfo($current_user->ID);

	$customer_info = new sbm_customer();




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
	$invoice_status = 'pending';
	?>
	<div id="invoice_list">
			<table class="bw_table" id="invoices">
				<th colspan="10">Pending ( In Progress ) invoices</th>
				<tr class="descriptions">
					<td>Invoice #</td>
					<td>Date</td>
					<td>Company</td>
					<td>First Name</td>
					<td>Last Name</td>
					<td>Invoice Total</td>
					<td>Already Paid</td>
					<td>Invoice Balance</td>
					<td>Invoice Status</td>
					<td>Pay Invoice</td>
				</tr>
	<?php

	echo sbm_get_invoice_list( $invoice_status );
	?>
	</table>
	</div>
</div>
	<?php
}


function sbm_get_unpaid_invoices_list()
{
	global $wpdb;
	global $current_user;

     get_currentuserinfo($current_user->ID);

	$customer_info = new sbm_customer();




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
	$invoice_status = 'invoiced';

	?>
	<div id="invoice_list">

			<table class="bw_table" id="invoices">
				<th colspan="10">Unpaid invoices</th>
				<tr class="descriptions">
					<td>Invoice #</td>
					<td>Date</td>
					<td>Company</td>
					<td>First Name</td>
					<td>Last Name</td>
					<td>Invoice Total</td>
					<td>Already Paid</td>
					<td>Invoice Balance</td>
					<td>Invoice Status</td>
					<td>Pay Invoice</td>
				</tr>
	<?php
	echo sbm_get_invoice_list( $invoice_status );
	?>
	</table>
	</div>
	</div>
	<?php
}

function sbm_get_paid_invoices_list()
{
	global $wpdb;
	global $current_user;

     get_currentuserinfo($current_user->ID);

	$customer_info = new sbm_customer();




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
	$invoice_status = 'paid';
	?>
	<div id="invoice_list">
			<table class="bw_table" id="invoices">
				<th colspan="10">Paid invoices</th>
				<tr class="descriptions">
					<td>Invoice #</td>
					<td>Date</td>
					<td>Company</td>
					<td>First Name</td>
					<td>Last Name</td>
					<td>Invoice Total</td>
					<td>Already Paid</td>
					<td>Invoice Balance</td>
					<td>Invoice Status</td>
					<td>Pay Invoice</td>
				</tr>
	<?php
	echo sbm_get_invoice_list( $invoice_status );
	?>
		</table>
	</div>
	</div>
	<?php
}

function sbm_get_invoice_value_by_meta_key( $key, $invoice_id )
{
		global $wpdb;

		if((!empty( $invoice_id )) || ( $invoice_id != 0 ))
		{
			$sql ="SELECT
						meta_value
					FROM
						".$wpdb->prefix."sbm_invoice_data
					WHERE
						meta_key = '$key'
					AND
						invoice_id = '$invoice_id'";
			$value=$wpdb->get_var($wpdb->prepare($sql));
		}
		else
		{
			$value = NULL;
		}
			return $value;


}

function sbm_create_invoice()
{

	global $wpdb;
	global $current_user;

	$currency_symbol = get_option( 'sbm_currency' );

	$customer_info 		= new sbm_customer();
	$time				= time();

     get_currentuserinfo($current_user->ID);

	if( !empty( $_GET['invoice_id'] ) )
	{
		$invoice_id = $_GET['invoice_id'];
	}
	if( !empty( $_POST['invoice_id'] ))
	{
		$invoice_id = $_POST['invoice_id'];
	}

	if( !empty( $_GET['status'] ) )
	{
		$status = $_GET['status'];
	}
	if( !empty( $_POST['status'] ) )
	{
		$status = $_POST['status'];
	}


	if( !empty( $_GET['customer_id'] ) )
	{
		$customer_id = $_GET['customer_id'];
	}
	if( !empty( $_POST['customer_id'] ))
	{
		$customer_id = $_POST['customer_id'];
	}

	if( ($status == 'new') && ( empty( $invoice_id ) ) && ( !empty( $customer_id ) ) )
	{
		$invoice_id = sbm_invoice::sbm_create_invoice( $time, $customer_id, $_POST['invoice_type'], $_POST['hourly_rate'], $_POST['paid_down'], $_POST['tax_rate'] );

	}
	else if( ($status == 'edit') && (!empty( $invoice_id ) ) )
	{


	}
	else if( empty( $status ))
	{
		// take the user to the view customer list
		//  general_functions.php:     sbm_redirect()
		sbm_redirect('sbm_view_invoices');
		//echo '<h2>This page was reached in error</h2>';
		//die();
	}


	// If $invoice_id is not empty, we now need to validate that required fields were filled out and then run the update for this invoice
	if((!empty( $invoice_id )) && ($status == 'new' ))
	{
		$errors = array();
		if( empty( $_POST['customer_id']) )
		{
			$errors[] = 'You forgot the customer';
		}

		if( empty( $errors ))
		{
			sbm_invoice::sbm_update_invoice( $invoice_id );
			die('There was a problem updating the invoice please contact customer service');
		}
	}
?>

<div class="wrap">
<?php

		if (isset($_POST['customer_id']))
		{
			if(isset($_POST['verify_delete']))
			{
				sbm_customer::sbm_delete_invoice($_POST['customer_id'], $invoice_id);
				die('Delete customer, if this is visible please contact customer support');
			}


			$errors = array();


			// If this is new, all the fields are required
			if($_GET['status'] == 'new')
			{

				if(empty($_POST['customer_id']))
				{
					$errors[] = 'You forgot the customer.';
				}

			}
			if(empty($errors))
			{

				sbm_invoice::sbm_update_invoice( $invoice_id );

				die('There was a problem updating the invoice, please contact customer service');
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
		if(!empty( $_GET['customer_id'] ))
		{
			$customer_id = $_GET['customer_id'];
		}

	//  classes/sbm_customer.php:     sbm_get_customer_data()
	$customer_info->sbm_get_customer_data( $customer_id );
	// Get our tax rate
	if(!empty( $customer_info->tax_rate ))
	{
		// Use the customers tax rate
		$tax_rate = $customer_info->tax_rate;
	}
	else
	{
		// use our default tax rate
		$tax_rate = get_option( 'sbm_default_tax_rate' );
	}


	// get the currency
	if( !empty ( $customer_info->currency ) )
	{
		$currency_symbol = get_option( 'sbm_currency' );
	}


	// If this is new, we need this section
	if ( ( $status != 'delete' ) && ( $status != 'reactivate' ) )
	{

            if(!empty($customer_info->company_name))
            {
                ?>
                <h2 id="invoice_name">Invoice <span class="company_name"> for <?php echo $customer_info->company_name; ?></span></h2>
                <?php

            }
            else
            {
                switch($_GET['status'])
                {
                    case 'new';
                        ?>
                            <h2 id="invoice_name">New Invoice <span class="company_name">( Please select a customer )</span></h2>
                        <?php
                    break;
                    case 'edit';
                        ?>
                            <h2 id="invoice_name">Edit Invoice <span class="company_name">( Please change customer if needed )</span></h2>
                        <?php
                    break;
                    default;
                    break;
                }
            }



            //echo 'Invoice Total: ' . sbm_invoice::sbm_get_invoice_total( $invoice_id, $customer_id );
            ?>
        <div id="message" class="display-none error"></div>

        <form method="post" id="editInvoiceForm">
        <?php

            if(!empty( $invoice_id ))
            {
                // check to see if this is edit, and if so populate the page/pages
                $invoice_info 	= new sbm_invoice();
                $invoice_info->sbm_get_invoice_data( $invoice_id, $customer_id );
                $page_number 	= array();
                $adjusted_pages = array();

                foreach( $invoice_info as $key => $list )
                {
                    // find out how many times the word page with number after it occurs
                    if( preg_match('/(page)?([0-9])/', $key, $matches) )
                    {
                        preg_match('/[0-9]/', $matches[0], $number );
                        $page_number[] = $number[0];
                    }

                }
                // remove duplicate numbers in our array
                $adjusted_pages = array_unique( $page_number );
                sort( $adjusted_pages );
                foreach( $adjusted_pages as $new_list )
                {
                    $adjusted_pages[ $new_list ];
                }
                $total_pages = count( $adjusted_pages );
            }
            // Just in case this has not bee set. or is a new invoice, we need to make sure this is set to 1
            if( empty( $adjusted_pages ))
            {
                $adjusted_pages[1] = 1;
                $total_pages = 1;
            }



            /*
            *
            *	Invoice Status options
            *
            *	pending
            *	invoiced
            *	paid
            *	cancelled
            *
            */



            switch( $_GET['status'] )
            {
                case 'new';
                    $invoice_status 	= 'pending';
                    $paid_down			= '0.00';
                        // Get our hourly rate
                    if(!empty( $customer_info->hourly_rate ))
                    {
                        // Use the customers hourly rate
                        $hourly_rate = $customer_info->hourly_rate;
                    }
                    else
                    {
                        // user our default hourly rate
                        $hourly_rate = get_option( 'sbm_default_hourly_rate' );
                    }
                    $invoice_description    = 'Proposal';
                    $disabled               = '';
                    $clear_row              = true;
                break;
                case 'edit';
                    $invoice_status		= $invoice_info->invoice_status;
                    $tax_rate			= $invoice_info->tax_rate;
                    $invoice_paid_down	= $invoice_info->paid_down;
                    $current_paid_down	= sbm_get_total_amount_paid_for_invoice( $invoice_id, $customer_id );
                    // Get our hourly rate
                    if(!empty( $invoice_info->hourly_rate ))
                    {
                        // Use the customers hourly rate
                        $hourly_rate = $invoice_info->hourly_rate;
                    }
                    else
                    {
                        // user our default hourly rate
                        $hourly_rate = get_option( 'sbm_default_hourly_rate' );
                    }
                    $invoice_description    = 'Proposal';
                    $disabled               = '';
                    $clear_row              = true;
                break;
                case 'paid';
                    $invoice_description = 'Paid Invoice';
                    $invoice_status		= $invoice_info->invoice_status;
                    // Get our hourly rate
                    if(!empty( $customer_info->hourly_rate ))
                    {
                        // Use the customers hourly rate
                        $hourly_rate = $customer_info->hourly_rate;
                    }
                    else
                    {
                        // user our default hourly rate
                        $hourly_rate = get_option( 'sbm_default_hourly_rate' );
                    }
                    $disabled = 'disabled="disabled"';
                    $clear_row              = false;
                break;
                case 'cancelled';
                    $invoice_description = 'Cancelled Invoice';
                    $invoice_status		= $invoice_info->invoice_status;
                    // Get our hourly rate
                    if(!empty( $customer_info->hourly_rate ))
                    {
                        // Use the customers hourly rate
                        $hourly_rate = $customer_info->hourly_rate;
                    }
                    else
                    {
                        // user our default hourly rate
                        $hourly_rate = get_option( 'sbm_default_hourly_rate' );
                    }
                    $disabled = 'disabled="disabled"';
                    $clear_row              = false;
                break;
                case 'invoiced';
                    $disabled = 'disabled="disabled"';
                    $invoice_description = 'Invoice';
                break;
                default;
                    $invoice_status		= $invoice_info->invoice_status;
                    $invoice_description = 'Invoice';
                    // Get our hourly rate
                    if(!empty( $customer_info->hourly_rate ))
                    {
                        // Use the customers hourly rate
                        $hourly_rate = $customer_info->hourly_rate;
                    }
                    else
                    {
                        // user our default hourly rate
                        $hourly_rate = get_option( 'sbm_default_hourly_rate' );
                    }
                    $disabled = '';
                    $clear_row              = true;
                break;
            }

            // We are going to over-ride the disabled should the current user be read only
            if (sbm_check_read_only_user() == true)
            {
                $disabled = 'disabled="disabled"';
            }


            if( isset( $_POST['invoice_type'] ))
            {
                $invoice_type = $_POST['invoice_type'];

            }
            else
            {
                $invoice_type = $invoice_info->invoice_type;

            }
            // See what was either used in the old invoice OR what was posted to make sure they are selected again
            switch( $invoice_type )
            {
             case 'line_item';
                $line_item_selected = 'selected="selected"';
                $hourly_selected	= '';
            break;
            case 'hourly';
                $line_item_selected = '';
                $hourly_selected	= 'selected="selected"';
            break;
            default;
                $line_item_selected = '';
                $hourly_selected	= '';
            break;
            }


        ?>
          <input type="hidden" id="invoice_status" name="invoice_status" value="<?php echo sbm_sticky_input($_POST['invoice_status'], $invoice_status); ?>">
          <input type="hidden" id="invoice_id" name="invoice_id" value="<?php echo sbm_sticky_input($_POST['invoice_id'], $invoice_id); ?>">
          <input type="hidden" id="customer_id" name="customer_id" value="<?php echo sbm_sticky_input($_POST['customer_id'], $customer_id); ?>">
          <input type="hidden" class="tax_rate" id="tax_rate" name="tax_rate" value="<?php echo sbm_sticky_input($_POST['tax_rate'], $tax_rate); ?>">
          <input type="hidden" class="hourly_rate" id="hourly_rate" name="hourly_rate" value="<?php echo sbm_sticky_input($_POST['hourly_rate'], $hourly_rate); ?>">
          <input type="hidden" class="paid_down" id="paid_down" name="paid_down" value="<?php echo sbm_sticky_input($_POST['paid_down'], $paid_down); ?>">
        <?php
        // Only show if this is pending
        if($invoice_status == 'pending')
        {
            ?>
        <div id="type_of_invoice">
          <label for="invoice_type">What type of invoice:</label>
          <select id="invoice_type" name="invoice_type">
            <option <?php echo $line_item_selected; ?> value="line_item">Line Item</option>
            <option <?php echo $hourly_selected; ?> value="hourly">Hourly</option>
          </select>

        </div>

        <div id="customer_data">

        <label for="choose_customer">Choose customer ( First Name OR Last Name OR Company Name )</label>
        <input type="text" autocomplete="off" id="choose_customer" name="choose_customer" class="required" size="40" value="<?php

            if( !empty( $customer_info->company_name ) )
            {
                echo sbm_sticky_input($_POST['choose_customer'],$customer_info->company_name);
            }
            else
            {
                $name = $customer_info->first_name_1 . ' ' . $customer_info->last_name_1;

                if( ( empty( $customer_info->first_name_1 ) ) && ( empty( $customer_info->last_name_1 ) ) )
                {
                    $name = NULL;
                }

                 echo sbm_sticky_input($_POST['choose_customer'],$name);
            }
         ?>" />
        <div id="customer_suggestions"></div>
        </div>
        <?php
        }
        ?>


        <div id="line_item">
        <?php
        foreach( $adjusted_pages as $key => $page )
        {
        ?>
        <table id="line_item_table_<?php echo $adjusted_pages[ $key ]; ?>" class="line_item_table">
          <tbody>
          <?php
            if( get_option( 'sbm_invoice_image' ) )
            {
          ?>
            <tr>
                <td colspan="8" class="sbm_invoice_image <?php echo get_option( 'sbm_invoice_image_position' ); ?>"><img src="<?php echo get_option( 'sbm_invoice_image' ); ?>" alt="<?php echo get_option( 'sbm_company_name' ); ?>" border="0" /></td>
            </tr>
            <?php
            }
            else
            {
            ?>
            <tr>
                <td colspan="8" class="company_info">
                    <h3><?php echo $invoice_description; ?> From:</h3>
                    <div>Name: <?php echo get_option( 'sbm_company_name' ); ?></div>
                    <div>Address: <?php echo get_option( 'sbm_address' ); ?></div>
                    <div>Address 2: <?php echo get_option( 'sbm_address_2' ); ?></div>
                    <div><?php echo get_option( 'sbm_city' ); ?> <?php echo get_option( 'sbm_state' ); ?> <?php echo get_option( 'sbm_zip' ); ?></div>
                    <div>&nbsp;</div>
                    <div>Phone: <?php echo get_option( 'sbm_phone' ); ?></div>
                    <div>Fax: <?php echo get_option( 'sbm_fax' ); ?></div>
                    <div>Email 1: <?php echo get_option( 'sbm_email_1' ); ?></div>
                    <div>Email 2: <?php echo get_option( 'sbm_email_2' ); ?></div>
                </td>
            </tr>
            <?php
            }
            ?>
            <tr>
                <td colspan="8">


                    <div class="float-left" style="padding: 0px 40px 0px 10px;">
                        <div class="bold-text"><?php echo $invoice_description; ?> # <span class="invoice_id"><?php echo $invoice_id; ?></span></div>
                    </div>
                    <div class="float-left" style="padding: 0px 40px 0px 10px;">
                        <div>Company: <span class="company_name"><?php echo $customer_info->company_name; ?></span></div>

                        <div>Attn: <span class="first_name_1"><?php echo $customer_info->first_name_1; ?></span>
                               <span class="last_name_1"><?php echo $customer_info->last_name_1; ?></span>
                        </div>

                        <div>Address:<span class="address"><?php echo $customer_info->address; ?></span></div>
                        <div>Address (2):<span class="address_2"><?php echo $customer_info->address_2; ?></span></div>
                        <div>City State/Provence: <span class="city"><?php echo $customer_info->city; ?></span>
                            <span class="state"><?php echo $customer_info->state; ?></span>
                            <span class="zip"><?php echo $customer_info->zip; ?></span>
                        </div>

                    </div>
                    <div class="float-left" style="padding: 0 40px 0 10px;">
                        <div>Main Phone: <span class="main_phone"><?php echo $customer_info->main_phone; ?></span></div>
                        <div>Tax Rate: <span class="tax_rate"><?php echo $tax_rate; ?></span>%</div>
                        <div>Paid Down: <?php echo $currency_symbol; ?><span class="paid_down">&nbsp;<?php
                            if( empty( $current_paid_down ))
                            {
                                echo '0.00';
                            }
                            else
                            {
                                echo $current_paid_down;
                            }
                            ?></span>
                        </div>
                        <div><label for="purchase_order">Purchase Order:</label><input <?php echo $disabled; ?> type="text" name="purchase_order" value="<?php echo sbm_sticky_input($_POST['purchase_order'], $invoice_info->purchase_order ); ?>" maxlength="100"></div>


                    </div>
                    <div style="clear"></div>

                </td>
            </tr>
            <tr>
              <td class="qty center-text">Qty</td>
              <td class="date center-text">Date</td>
              <td class="start_time center-text hourly">Start Time</td>
              <td class="end_time center-text hourly">End Time</td>
              <td class="unpaid_time center-text hourly">Unpaid Minutes</td>
              <td class="status center-text">Status</td>
              <td class="description center-text">Description</td>
              <td class="price center-text">Price</td>
              <td class="taxable center-text">Taxable</td>
              <td class="total_price center-text">Total</td>
              <td><?php if ( $clear_row ) { ?>Clear Row<?php } ?></td>
            </tr>
            <?php
            for ( $i = 1; $i <= 22; $i++ )
            {
                // check the check box
                if( sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_taxable' . $i , $invoice_id ) == 'on' )
                {
                    $tax_checked = 'checked="checked"';
                }
                else
                {
                    $tax_checked = '';
                }
                ?>
            <tr class="invoice_data_row">
              <td class="qty center-text "><input <?php echo $disabled; ?> type="text" autocomplete="off" class="copyText" id="input<?php echo $adjusted_pages[ $key ]; ?>_qty<?php echo $i; ?>" maxlength="11" size="4" value="<?php echo sbm_sticky_input($_POST['page' . $adjusted_pages[ $key ] . '_qty' . $i ], sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_qty' . $i , $invoice_id ) ); ?>" /><span class="hourly" id="span<?php echo $adjusted_pages[ $key ]; ?>_qty<?php echo $i; ?>"><?php echo sbm_sticky_input($_POST['page' . $adjusted_pages[ $key ] . '_qty' . $i ], sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_qty' . $i , $invoice_id ) ); ?></span><input type="hidden"  class="input_qty" value="<?php echo sbm_sticky_input($_POST['page' . $adjusted_pages[ $key ] . '_qty' . $i ], sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_qty' . $i , $invoice_id ) ); ?>" id="page<?php echo $adjusted_pages[ $key ]; ?>_qty<?php echo $i; ?>" name="page<?php echo $adjusted_pages[ $key ]; ?>_qty<?php echo $i; ?>"></td>
              <td class="date "><input <?php echo $disabled; ?> type="text" autocomplete="off" value="<?php echo sbm_sticky_input($_POST['page' . $adjusted_pages[ $key ] . '_date' . $i ], sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_date' . $i , $invoice_id ) ); ?>" id="page<?php echo $adjusted_pages[ $key ]; ?>_date<?php echo $i; ?>" name="page<?php echo $adjusted_pages[ $key ]; ?>_date<?php echo $i; ?>" maxlength="10" size="11" class="hasDatePicker"></td>
              <td class="start_time hourly"><input <?php echo $disabled; ?> autocomplete="off" type="text" class="input_start_time" value="<?php echo sbm_sticky_input($_POST['page' . $adjusted_pages[ $key ] . '_start_time' . $i ], sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_start_time' . $i , $invoice_id ) ); ?>" id="page<?php echo $adjusted_pages[ $key ]; ?>_start_time<?php echo $i; ?>" name="page<?php echo $adjusted_pages[ $key ]; ?>_start_time<?php echo $i; ?>" maxlength="12" size="7"></td>
              <td class="end_time hourly"><input <?php echo $disabled; ?> autocomplete="off" type="text" class="input_end_time" value="<?php echo sbm_sticky_input($_POST['page' . $adjusted_pages[ $key ] . '_end_time' . $i ], sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_end_time' . $i , $invoice_id ) ); ?>" id="page<?php echo $adjusted_pages[ $key ]; ?>_end_time<?php echo $i; ?>" name="page<?php echo $adjusted_pages[ $key ]; ?>_end_time<?php echo $i; ?>" maxlength="12" size="7"></td>
              <td class="unpaid_time hourly"><input <?php echo $disabled; ?> type="text" class="input_unpaid_time" value="<?php echo sbm_sticky_input($_POST['page' . $adjusted_pages[ $key ] . '_unpaid_time' . $i ], sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_unpaid_time' . $i , $invoice_id ) ); ?>" id="page<?php echo $adjusted_pages[ $key ]; ?>_unpaid_time<?php echo $i; ?>" name="page<?php echo $adjusted_pages[ $key ]; ?>_unpaid_time<?php echo $i; ?>" maxlength="12" size="7"></td>
              <td class="status "><input <?php echo $disabled; ?> autocomplete="off" type="text" value="<?php echo sbm_sticky_input($_POST['page' . $adjusted_pages[ $key ] . '_status' . $i ], sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_status' . $i , $invoice_id ) ); ?>" id="page<?php echo $adjusted_pages[ $key ]; ?>_status<?php echo $i; ?>" name="page<?php echo $adjusted_pages[ $key ]; ?>_status<?php echo $i; ?>" maxlength="50" size="7" class="input_status"></td>
              <td class="description ">
                  <input  <?php echo $disabled; ?>autocomplete="off" type="text" value="<?php echo sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_description' . $i , $invoice_id ); ?>" id="page<?php echo $adjusted_pages[ $key ]; ?>_description<?php echo $i; ?>" name="page<?php echo $adjusted_pages[ $key ]; ?>_description<?php echo $i; ?>" maxlength="60" size="60" class="input_description">
              </td>
              <td class="price ">
                  <input <?php echo $disabled; ?> autocomplete="off" type="text" class="input_price" value="<?php echo sbm_sticky_input($_POST['page' . $adjusted_pages[ $key ] . '_price' . $i ], sbm_get_invoice_value_by_meta_key('page' . $adjusted_pages[ $key ] . '_price' . $i , $invoice_id ) ); ?>" id="page<?php echo $adjusted_pages[ $key ]; ?>_price<?php echo $i; ?>" name="page<?php echo $adjusted_pages[ $key ]; ?>_price<?php echo $i; ?>" maxlength="12" size="6">
              </td>
              <td class="taxable center-text "><input <?php echo $disabled; ?> type="checkbox" <?php echo $tax_checked; ?> class="input_taxable" name="page<?php echo $adjusted_pages[ $key ]; ?>_taxable<?php echo $i; ?>" id="page<?php echo $adjusted_pages[ $key ]; ?>_taxable<?php echo $i; ?>"></td>
              <td class="total_price right-text "><div class="line_total" id="page<?php echo $adjusted_pages[ $key ]; ?>_row<?php echo $i; ?>_total"></div></td>
              <td><?php if ( $clear_row ) { ?><a href="javascript: void(0);" class="clear_row">clear</a> <?php } ?></td>
            </tr>
            <?php
            }
            ?>
            <tr>
              <td></td>
              <td></td>
              <td class="hourly"></td>
              <td class="hourly"></td>
              <td class="hourly"></td>
              <td colspan="4" class="right-text">Total Tax (@ <span class="tax_rate"><?php echo $tax_rate; ?></span>%) Paid for page <span class="current_page"><?php echo $adjusted_pages[ $key ]; ?></span></td>
              <td class="right-text total_tax_paid_by_page"><div><?php echo $currency_symbol; ?>&nbsp;<span id="line_item_table_<?php echo $adjusted_pages[ $key ]; ?>_total_tax_paid_by_page">0.00</span></div></td>
            </tr>
            <tr>
              <td class="hourly" colspan="3"></td>
              <td colspan="4"><div><?php echo get_option( 'sbm_terms' ); ?></div></td>
              <td class="untaxed_price "><div>Non-taxable</div>
                <div><?php echo $currency_symbol; ?>&nbsp;<span id="line_item_table_<?php echo $adjusted_pages[ $key ]; ?>_nontaxable_total">0.00</span>
                  <div id="nonTaxableSum<?php echo $adjusted_pages[ $key ]; ?>"></div>
                </div></td>
              <td class="taxed_price "><div>Taxable</div>
                <div><?php echo $currency_symbol; ?>&nbsp;<span id="line_item_table_<?php echo $adjusted_pages[ $key ]; ?>_taxable_total">0.00</span>
                  <div id="taxableSum<?php echo $adjusted_pages[ $key ]; ?>"></div>
                </div></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr class="right-text">
              <td class="hourly" colspan="3"></td>
              <td class="textRight boldText" colspan="6">Page <span class="current_page"><?php echo $adjusted_pages[ $key ]; ?></span> total w/ <span class="tax_rate"><?php echo $tax_rate; ?></span> % tax&nbsp;</td>
              <td class="page_total"><?php echo $currency_symbol; ?>&nbsp;<span id="line_item_table_<?php echo $adjusted_pages[ $key ]; ?>_page_total">0.00</span></td>
            </tr>
            <tr class="right-text">
             <td class="hourly" colspan="3"></td>
              <td class="textRight boldText" colspan="6"><?php $invoice_description; ?> Total&nbsp;&nbsp;</td>
              <td class=""><?php echo $currency_symbol; ?>&nbsp;<span class="invoice_total">0.00</span></td>
            </tr>
            <tr class="right-text">
             <td class="hourly" colspan="3"></td>
              <td class="textRight boldText" colspan="6">Paid Down&nbsp;&nbsp;</td>
              <td class=""><span class="paid_down"><?php echo $currency_symbol; ?>&nbsp;0.00</span></td>
            </tr>
            <tr class="right-text">
             <td class="hourly" colspan="3"></td>
              <td class="textRight boldText" colspan="6"><?php

              switch($invoice_description)
              {
                case 'Proposal':
                    $msg = 'Proposal total';
                break;
                case 'Invoice':
                    $msg = 'Total Balance Due';
                break;
                case 'Paid Invoice':
                    $msg = 'Paid Invoice total';
                break;
                case 'Cancelled Invoice':
                    $msg = 'Cancelled Invoice total';
                break;
                default:
                    $msg = 'Invoice total';
                break;
              }
              echo $msg;
              ?>&nbsp;&nbsp;</td>
              <td class=""><?php echo $currency_symbol; ?>&nbsp;<span class="balance_due">0.00</span></td>
            </tr>
            <tr class="center-text">
              <td class="" colspan="7"><div>Page <span class="current_page"><?php echo $adjusted_pages[ $key ]; ?></span> of <span class="total_pages"><?php echo $total_pages; ?></span></div></td>
              <td>&nbsp;</td>
            </tr>
          </tbody>
        </table>
        <?php
        }
        ?>

        </div>
        <div class="float-left medium-padding">
        <?php

                        //  general_functions.php:     sbm_check_read_only_user()
                        if(  sbm_check_read_only_user() == false  )
                        {
                        // check to make sure this is ONLY pending, no other status should be able to submit
                        if($invoice_status == 'pending' )
                        {
                        ?>
                            <span><input type="submit" value="Submit" id="invoiceSubmitButton"></span>
                        <?php
                        }
                                //  general_functions.php:     sbm_ok_to_delete()
                            if( ( $invoice_status != 'paid' ) && ( $_GET['status'] != 'new' ) && ( sbm_ok_to_delete('invoice',$invoice_id, 'no') == true ) )
                                {
                                    if( $_GET['status'] == 'cancelled' )
                                    {
                                        echo '<span><input type="button" value="Activate Invoice" id="verifyActivateinvoice"></span>';
                                    }
                                    else{
                                        echo '<span><input type="button" value="Delete/Cancel this invoice" id="verifyDeleteinvoice"></span>';
                                    }
                                }
                        }
                        //  general_functions.php:     sbm_cancel_button()
                        echo sbm_cancel_button('sbm_view_invoices', 'Return to main page');

                        if($status == 'invoiced' )
                        {
                            echo '<span><input type="button" value="Change status to cancelled" id="verifyDeleteinvoice"></span>';
                        }
                        if($invoice_status == 'pending' )
                        {
                            //  general_functions.php:     sbm_check_read_only_user()
                            if(  sbm_check_read_only_user() == false  )
                            {
                            ?>
                                <!-- Having issues with the add another page functionality disabled for now ->
                                <!-- input type="button" id="clone_invoice_page" onclick="return false;" value="Add Another Page" name="Add" -->
                                <input type="hidden" id="last_page_number"  value="<?php echo $total_pages; ?>">
                                <?php
                            }
                        }
                    //  help_functions.php:     sbm_display_help()
                    echo sbm_display_help( 'invoice' );
                    ?>
        </div>
        <?php
            if(!empty($invoice_id ))
            {
                ?>
                <input type="hidden" id="update_invoice_totals" value="true">
                <?php
                }

	}
			// End New section

			// If this is Delete then use this section
	if($_GET['status'] == 'delete')
	{


			if(isset($_POST['verify_delete']))
			{

				sbm_invoice::sbm_delete_invoice($invoice_id, $customer_id);
				die('The attempt to delete this invoice failed, please contact customer services');

			}
			 		//  classes/sbm_customer.php:     sbm_get_customer_data()
			//sbm_invoice::sbm_get_invoice_data( $_GET['invoice_id'] );
			echo '<div class="wrap">';
			// check to see if we can even delete this
			//  general_functions.php:     sbm_ok_to_delete()
			sbm_ok_to_delete('invoice_id', $_GET['invoice_id'], 'yes');


			if(!empty($invoice_id))
			{
				echo '<h2><span class="change_delete_text">Delete Invoice</span> # '. $invoice_id . '</h2>';
				echo '<div class="drop_delete_text">Deleting this invoice will remove all data associated with it.</div>';
				echo '<form method="post">
                            <h3><label for="cancel_invoice" id="messaging">Check this box IF you would rather cancel This Invoice do not delete it </label> <input type="checkbox" name="cancel_invoice" id="cancel_invoice"></h3>
							<input type="hidden" name="verify_delete" value="true">';
				echo '<div class="float-left medium-padding">';
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_view_invoices', 'cancel');
				echo '<input type="submit" id="delete_invoice_submit_button" value="Delete Invoice">';
				echo '</div>';
				echo '</form>';

			}
			else
			{
				echo '<h2>You need to select an invoice before you can use this page</h2>';
			}

	}
			// end Delete
			// If this is Delete then use this section
	if($_GET['status'] == 'reactivate')
	{


			if(isset($_POST['verify_reactivate']))
			{

				sbm_invoice::sbm_reactivate_invoice($invoice_id, $customer_id);
				die('The attempt to reactivate this invoice failed, please contact customer services');

			}
			 		//  classes/sbm_customer.php:     sbm_get_customer_data()
			//sbm_invoice::sbm_get_invoice_data( $_GET['invoice_id'] );
			echo '<div class="wrap">';
			// check to see if we can even delete this
			//  general_functions.php:     sbm_ok_to_delete()
			//sbm_ok_to_delete('invoice_id', $_GET['invoice_id'], 'yes');


			if(!empty($invoice_id))
			{
				echo '<h2>Reactivate invoice # '. $invoice_id . '</h2>';
				echo '<h3>Reactivating this invoice will change its status to pending.</h3>';
				echo '<form method="post">

							<input type="hidden" name="verify_reactivate" value="true">';
				echo '<div class="float-left medium-padding">';
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_view_invoices', 'cancel');
				echo '<input type="submit" id="reactivate_invoice_submit_button" value="Reactivate Invoice">';
				echo '</div>';
				echo '</form>';

			}
			else
			{
				echo '<h2>You need to select an invoice before you can use this page</h2>';
			}

	}
			// end Cancelled

			echo '</fieldset>';
			echo '</form>';
			echo '<div id="output_div"></div>';
		echo '</div>';



}

function sbm_get_total_pages_for_invoice( $invoice_id )
{
		// check to see if this is edit, and if so populate the page/pages
		$invoice_info 	= new sbm_invoice();
		$invoice_info->sbm_get_invoice_data( $invoice_id, $customer_id );
		$page_number 	= array();
		$adjusted_pages = array();

		foreach( $invoice_info as $key => $list )
		{
			// find out how many times the word page with number after it occurs
			if( preg_match('/(page)?([0-9])/', $key, $matches) )
			{
				preg_match('/[0-9]/', $matches[0], $number );
				$page_number[] = $number[0];
			}

		}
		// remove duplicate numbers in our array
		$adjusted_pages = array_unique( $page_number );
		sort( $adjusted_pages );
		foreach( $adjusted_pages as $new_list )
		{
			$adjusted_pages[ $new_list ];
		}

		$total_pages = count( $adjusted_pages );

	return $total_pages;
}

function sbm_get_invoice_page( $invoice_id, $page_number, $invoice_description, $total_pages, $tax_rate, $customer_id, $invoice_total, $already_paid, $balance_due, $purchase_order = '' )
{

	global $current_user;

	$currency_symbol = get_option( 'sbm_currency' );

	$customer_info 	= new sbm_customer();
	//  classes/sbm_customer.php:     sbm_get_customer_data()
	$customer_info->sbm_get_customer_data( $customer_id );
	$company_name 	= $customer_info->company_name;
	$first_name 	= $customer_info->first_name_1;
	$last_name 		= $customer_info->last_name_1;
	$taxable_total  = 0;
	$nontax_total   = 0;
	$total_tax_paid = 0;

	  switch($invoice_description)
      {
      	case 'Proposal':
      		 $invoice_description =  'Proposal total';
      	break;
      	case 'Invoice':
      		 $invoice_description =  'Total Balance Due';
      	break;
      	case 'Paid Invoice':
      		 $invoice_description =  'Paid Invoice total';
    	break;
      	case 'Cancelled Invoice':
      		 $invoice_description =  'Cancelled Invoice total';
      	break;
      	default:
      		 $invoice_description =  'Invoice total';
      	break;
      }

	//$name = 'abc company';
	$content = '
		<table width="575" border="1">
  <tbody>';

  	if( get_option( 'sbm_invoice_image' ) )
	{

	}
	else
	{
	$content .= '
  	<tr>
  		<td>' . $invoice_description . '#' . $invoice_id .'</td>
    	<td colspan="8">
        	Name: ' .   get_option( 'sbm_company_name' ) . '<br />
            Address: ' .  get_option( 'sbm_address' ) . '<br />
            Address 2: ' .   get_option( 'sbm_address_2' ) . '<br />
            ' . get_option( 'sbm_city' ) . ' ' . get_option( 'sbm_state' ) . ' ' . get_option( 'sbm_zip' ) . '<br />
            < br/>
            Phone: ' . get_option( 'sbm_phone' ) . '<br />
            Fax: ' .  get_option( 'sbm_fax' ) . '<br />
            Email 1: ' . get_option( 'sbm_email_1' ) . '<br />
            Email 2: ' . get_option( 'sbm_email_2' ) . '<br />
        </td>
    </tr>';

	}



	$content .='
    <tr>
    	<td width="125">' . $invoice_description . '#' . $invoice_id .'</td>
        <td colspan="8" width="450">
                Company: ' . $customer_info->company_name . '<br />
            	Attn: ' . $customer_info->first_name_1 . '
  					  ' . $customer_info->last_name_1 . '<br />
                Address:' . $customer_info->address . '<br />
                ' . $customer_info->address_2 . '<br />
                City State/Provence: ' . $customer_info->city . '
                    ' . $customer_info->state . '
                Zip/Postal Code: ' . $customer_info->zip . '<br />
            	Main Phone: ' . $customer_info->main_phone . '<br />
                Tax Rate: ' . $tax_rate . '<br />
                Paid Down: ' . $currency_symbol;
					if( empty( $current_paid_down ))
					{
						$content .= '0.00';
					}
					else
					{
						$content .= $current_paid_down;
					}
					$content .= '<br />
                Purchase Order: ' . $purchase_order . '

        </td>
    </tr>

    <tr>
      <td width="25" style="text-align: center;">Qty</td>
      <td width="50" style="text-align: center;">Date</td>
      <td width="50" style="text-align: center;">Start Time</td>
      <td width="50" style="text-align: center;">End Time</td>
      <td width="50" style="text-align: center;">Unpaid Minutes</td>
      <td width="50" style="text-align: center;">Status</td>
      <td width="150">Description</td>
      <td width="50" style="text-align: center;">Price</td>
      <td width="25" style="text-align: center;">Tax</td>
      <td width="75" style="text-align: center;">Total</td>
    </tr>';

	for ( $i = 1; $i <= 22; $i++ )
	{
		$qty 			= sbm_get_invoice_value_by_meta_key('page' . $page_number . '_qty' . $i , $invoice_id );
		$price 			= sbm_get_invoice_value_by_meta_key('page' . $page_number . '_price' . $i , $invoice_id );
		$unpaid			= sbm_get_invoice_value_by_meta_key('page' . $page_number . '_unpaid_time' . $i , $invoice_id );
		$row_total  	= NULL;
		$line_total 	= NULL;
		$tax_amount 	= NULL;
		$sub_total  	= NULL;
		$tax_checked	= NULL;
		// check the check box
		if( sbm_get_invoice_value_by_meta_key('page' . $page_number . '_taxable' . $i , $invoice_id ) == 'on' )
		{
			$tax_checked = 'X';
			$sub_total = $qty * $price;
			$tax_amount = $sub_total * ( $tax_rate/100 ); //15000 * .1

			$line_total = ( $sub_total + ($tax_amount) ) - $unpaid;
			$taxable_total += $line_total;
			$total_tax_paid += $tax_amount;
		}
		else
		{
			$tax_checked = NULL;
			$line_total = $qty * $price;
			$nontax_total += $line_total;
		}
		if( $line_total == 0 )
		{
			$line_total = NULL;
		}
		else
		{
			$row_total = number_format($line_total, 2);
		}

		$content .= "
    <tr>
      <td width=\"25\" height=\"18\" style=\"text-align: center;\">" . $qty ."</td>
      <td width=\"50\" height=\"18\" style=\"text-align: center;\">" . sbm_get_invoice_value_by_meta_key('page' . $page_number . '_date' . $i , $invoice_id ) . "</td>
      <td width=\"50\" height=\"18\" style=\"text-align: center;\">" . sbm_get_invoice_value_by_meta_key('page' . $page_number . '_start_time' . $i , $invoice_id ) . "</td>
      <td width=\"50\" height=\"18\" style=\"text-align: center;\">" . sbm_get_invoice_value_by_meta_key('page' . $page_number . '_end_time' . $i , $invoice_id ) . "</td>
      <td width=\"50\" height=\"18\" style=\"text-align: center;\">" . $unpaid . "</td>
      <td width=\"50\" height=\"18\" style=\"text-align: center;\">" . sbm_get_invoice_value_by_meta_key('page' . $page_number . '_status' . $i , $invoice_id ) . "</td>
      <td width=\"150\" height=\"18\" >&nbsp;" . sbm_get_invoice_value_by_meta_key('page' . $page_number . '_description' . $i , $invoice_id ) . "</td>
      <td width=\"50\" height=\"18\" style=\"text-align: center;\">$price</td>
      <td width=\"25\" height=\"18\" style=\"text-align: center;\">$tax_checked</td>
      <td width=\"75\" height=\"18\" style=\"text-align: center;\">" . $row_total ."</td>
    </tr>";

	}
	$content .='
    <tr>
      <td width="25" height="20"></td>
      <td width="50" height="20"></td>
      <td width="50" height="20"></td>
      <td width="375" height="20" colspan="4" style="text-align: right;">Total Tax (@ '. $tax_rate . '%) Paid for page ' . $page_number .'&nbsp;</td>
      <td width="75" height="20">&nbsp;' . $currency_symbol. '&nbsp;' . number_format( $total_tax_paid, 2 ) . '</td>
    </tr>
    <tr>
    	<td colspan="7" width="425"><br />' . get_option( 'sbm_terms' ) . '<br /></td>
    	<td width="50">Non Taxable<br />' . $currency_symbol. '&nbsp;' . number_format( $nontax_total, 2 ) . '</td>
    	<td width="50">Taxable<br />' . $currency_symbol. '&nbsp;' . number_format( $taxable_total, 2 ) . '</td>
    	<td width="50"></td>
    </tr>
    <tr>
    	<td colspan="9" width="500" height="20" style="text-align: right;">Total&nbsp;</td>
    	<td width="75">&nbsp;' . $currency_symbol. '&nbsp;' . number_format( $invoice_total, 2 ) . '</td>
    </tr>
    <tr>
    	<td colspan="9" width="500" height="20" style="text-align: right;">Paid Down&nbsp;</td>
    	<td width="75">&nbsp;' . $currency_symbol. '&nbsp;' . number_format( $already_paid, 2 ) . '</td>
    </tr>
    <tr>
    	<td colspan="9" width="500" height="20" style="text-align: right;">' . $invoice_description . '&nbsp;</td>
    	<td width="75">&nbsp;' . $currency_symbol. '&nbsp;' . number_format( $balance_due, 2 ) . '</td>
    </tr>
    <tr>
      <td colspan="10" width="575" height="20" style="text-align: center;">Page ' . $page_number . ' of ' . $total_pages . '</td>
    </tr>
  </tbody>
</table>';

	return $content;
}

function sbm_get_invoice_status( $invoice_id )
{
	global $wpdb;
					$sql = "SELECT
								invoice_status
							FROM
								".$wpdb->prefix."sbm_invoice
							WHERE
								ID = $invoice_id
							LIMIT 0 , 1";

				$row = $wpdb->get_row($wpdb->prepare($sql));
				return $row->invoice_status;

}

function sbm_get_tax_rate_for_invoice( $invoice_id )
{
	global $wpdb;
					$sql = "SELECT
								tax_rate
							FROM
								".$wpdb->prefix."sbm_invoice
							WHERE
								ID = $invoice_id
							LIMIT 0 , 1";

				$row = $wpdb->get_row($wpdb->prepare($sql));
				return $row->tax_rate;

}
function sbm_get_customer_id_for_invoice( $invoice_id )
{
	global $wpdb;
					$sql = "SELECT
								customer_id
							FROM
								".$wpdb->prefix."sbm_invoice
							WHERE
								ID = $invoice_id
							LIMIT 0 , 1";

				$row = $wpdb->get_row($wpdb->prepare($sql));
				return $row->customer_id;

}

?>
