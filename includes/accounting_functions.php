<?php

function sbm_enter_transaction( $transaction_date= '' )
{
	global $wpdb;
	global $current_user;

     get_currentuserinfo($current_user->ID);
	$customer_info = new sbm_customer();
    $currency_symbol = get_option( 'sbm_currency' );

	if(empty($transaction_date))
	{
		// set to now
		$transaction_date = time();
	}


		$wpdb->insert( $wpdb->prefix.'sbm_transaction', array(   'transaction_date' => $transaction_date ), array(  '%d') );

	// return the newly entered id to be used in the next phase of a transaction
	return $wpdb->insert_id;
}


function sbm_view_company_report()
{

	$currency_symbol = get_option( 'sbm_currency' );

	?>
    <div class="wrap">

	<h2>Company Report for <span class="show_year"><?php echo date("Y"); ?></span></h2>

    <?php $years = sbm_get_year_range_from_invoices(); ?>


    <label for="filter_invoices_by_year">Change the year</label>
	<select  id="filter_invoices_by_year" <?php
	if($years[1] == $years[0] )
	{
        echo 'disabled="disabled"';
    }
        ?> name="filter_paid_invoices_by_year">

    <?php
		// now output only the years from the paid invoices
		for($i = $years[1]; $i >= $years[0]; $i-- )
		{
			if( date("Y") == $i )
			{
				$selected = 'selected="selected"';
			}
			else
			{
				$selected = '';
			}
			?>
            <option <?php echo $selected; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php
		}
	?>
    </select>

	<h3>Number of pending, invoiced and paid invoices for year ( <span class="show_year"><?php echo date("Y"); ?></span> ): <span id="year_invoices_total_amount"><?php echo sbm_get_all_invoices_for_year( date("Y") ); ?></span></h3>
	<h3>Invoices Total ( pending, invoiced and paid ) for year ( <span class="show_year"><?php echo date("Y"); ?></span> ):  <?php echo $currency_symbol; ?><span id="year_number_of_paid_invoices"><?php echo sbm_get_all_invoices_total_amount( date("Y") ); ?></span></h3>
	<h3>Number of Paid Invoices for year ( <span class="show_year"><?php echo date("Y"); ?></span> ): <span id="total_number_of_paid_invoices"><?php echo  sbm_get_total_number_of_paid_invoices( date("Y") ); ?></span></h3>
	<h3>Paid Invoices Total for year ( <span class="show_year"><?php echo date("Y"); ?></span> ):  <?php echo $currency_symbol; ?><span id="paid_invoices_total_amount"><?php echo sbm_get_paid_invoices_total_amount( date("Y") ); ?></span></h3>
	<h3>Total miles driven for year ( <span class="show_year"><?php echo date("Y"); ?></span> ): <span id="total_number_of_miles_for_year"><?php echo sbm_count_miles_for_year( date("Y") ); ?></span></h3>
    </div>
	<?php
		//  help_functions.php:     sbm_display_help()
	echo sbm_display_help( 'company_report' );

}



function sbm_accounting_view_options()
{
	global $wpdb;
	global $current_user;

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

		echo '<div><a href="admin.php?page=sbm_enter_deposit_expense">Enter Deposit or Expenses</a></div>';
	}
	else
	{
		$text = 'View';
	}

	echo '<div><a href="admin.php?page=sbm_view_bank_account_list">' . $text . ' bank accounts</a></div>';

	echo '<div>';
}



function sbm_pay_invoice()
{
	global $wpdb;
	$invoice_info = new sbm_invoice();

	$invoice_id 	= $_GET['invoice_id'];
	$customer_id 	= $_GET['customer_id'];
	$invoice_amount = $invoice_info->sbm_get_invoice_total( $invoice_id, $customer_id );
	$already_paid   = sbm_get_total_amount_paid_for_invoice( $invoice_id, $customer_id );
    $description    = $invoice_info->sbm_get_invoice_data($invoice_id)->description;
    $check_number   = null;
	?>
    <div class="wrap">

    <?php

	if(isset( $_POST['invoice_id'] ) )
	{
		// verify that all the fields were filled in
		$errors = array();

		if( empty( $_POST['invoice_id'] ))
		{
			$errors[] = 'You forgot the invoice id';
		}
		if( empty( $_POST['customer_id'] ))
		{
			$errors[] = 'You forgot the customer id';
		}
		if( empty( $_POST['paid_with'] ))
		{
			$errors[] = 'You forgot enter how the customer paid';
		}
		else
		{
			// Now that we know that the paid with is not empty, see if they paid with a check
			// if so, the check number is required
			if( $_POST['paid_with'] == 'check' )
			{
				if( empty( $_POST['check_number'] ))
				{
					$errors[] = 'You forgot the check number';
				}
			}
		}
		if( empty( $_POST['amount_paid'] ))
		{
			$errors[] = 'You forgot enter how the amount paid';
		}
		if( empty( $_POST['payment_date'] ))
		{
			$errors[] = 'You forgot enter how the payemnt date';
		}

		if( empty( $errors ))
		{
			$invoice_info->sbm_pay_invoice();
			die();
		}
		else
		{
			echo '<div class="error">';
			echo '<br />';

			foreach( $errors as $list )
			{
				echo '<div>' . $list . '</div>';
			}

			echo '<br />';
			echo '</div>';
		}

		//die();
	}



		if( ( empty( $invoice_id ) ) || ( empty( $customer_id ) ) )
		{
			?>
            <div class="error"><br />This page was reached in error<br /></div>
            <?php
		}
		else
		{


	switch( $_POST['paid_with'])
	{
		case 'check';
			$check_selected = 'selected="selected"';
		break;
        case 'cash';
            $cash_selected = 'selected="selected"';
        break;
        case 'ach';
            $ach_selected = 'selected="selected"';
        break;
		default;
			$none = 'selected="selected"';
		break;
	}
	?>
		<h2>Pay Invoice #<?php echo  $invoice_id; ?> Invoice Total: $<?php echo $invoice_amount; ?></h2>

        <form method="post" id="payInvoice">
        <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>" />
        <input type="hidden" name="invoice_amount" value="<?php echo $invoice_amount; ?>" />
        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />

        <label for="paid_with">Paid with:</label>
        <select id="paid_with" name="paid_with" class="required">
        	<option <?php echo $none; ?> value="">-- Select One --</option>
        	<option <?php echo $check_selected; ?>  value="check">Check</option>
            <option <?php echo $cash_selected; ?>  value="cash">Cash</option>
            <option <?php echo $ach_selected; ?>  value="ach">ACH</option>
        </select>
        <div class="clear"></div>
        <div id="check_div">
        	<label for="check_number">Check Number</label><input autocomplete="off" id="check_number" type="text" name="check_number" class="required" value="<?php echo sbm_sticky_input($_POST['check_number'], $check_number); ?>" />
        </div>
        <div class="clear"></div>
        <div id="amount_paid_div">
        	<?php
				// If the already_paid is above 0, then show what has already been paid
				if( $already_paid > 0 )
				{
					?>
                    <div>Already paid a total of $<?php echo number_format( $already_paid, 2 ); ?></div>
                    <?php
					$invoice_amount = $invoice_amount - $already_paid;
				}
					?>

        	<label for="amount_paid">Amount Paid</label><input autocomplete="off" type="text" class="required number" id="amount_paid" name="amount_paid" value="<?php echo sbm_sticky_input($_POST['amount_paid'], $invoice_amount); ?>" />
        </div>
        <div class="clear"></div>
        <div id="payment_date_div">
        	<label for="payment_date">Payment Date</label><input autocomplete="off" type="text" class="required date" id="payment_date" name="payment_date" value="<?php echo sbm_sticky_input($_POST['payment_date'], date("m/d/Y")); ?>" />
        </div>
        <div id="description_div">
        	<label for="description">Description/notes ( optional )</label><input autocomplete="off" type="text" id="description" name="description" value="<?php echo sbm_sticky_input($_POST['description'], $description); ?>" />
        </div>
        <div class="clear"></div>
        <input type="submit" id="payInvoiceSubmitButton" value="Pay Invoice"/>
        </form>

	</div>

    <?php
		}
}


function sbm_view_deposit_expense()
{
	global $current_user;
    get_currentuserinfo($current_user->ID);
    if(!empty($_GET['year']))
    {
        switch($_GET['year'])
        {
            case 'all':
                $year = null;
                break;
            default:
                $year = $_GET['year'];
                break;
        }

    }
    else
    {
        $year = date("Y");
    }

?>
    <div class="wrap">
    <?php

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
        <div><label for="change_deposits_expenses_year">Change year:</label>
        <select name="change_deposits_expenses_year" id="change_deposits_expenses_year">
            <option value="" <?php if(empty($year)) { echo 'selected="selected"'; } else { echo ''; } ?>>-- All --</option>
            <?php
            $deposits_expenses = sbm_get_year_range_from_deposits_expenses();
                for($i = $deposits_expenses[1]; $i >= $deposits_expenses[0]; $i--)
                {
                    ?>
                     <option value="<?php echo $i; ?>" <?php if($year == $i) { echo 'selected="selected"'; } else { echo ''; } ?>><?php echo $i; ?></option>
                    <?php

                }
            ?>
        </select></div>
        <div><a href="javascript: void(0);" id="download_csv_deposit_expenses">Download a csv version</a></div>
        <div id="csv_ready"></div>
        <div>&nbsp;</div>
<?php
	echo '<br>';
	echo '<table class="bw_table" style="width: 900px;">
				<th colspan="8">View Deposits</th>
				<tr class="descriptions">
					<td></td>
					<td>Date</td>
					<td>Payee/Payer</td>
					<td>Description</td>
					<td>Type</td>
					<td>Amount</td>
					<td>Total</td>
				</tr>';

	echo sbm_get_deposits_list($year);



	echo '</table>';

	echo '<table class="bw_table" style="width: 900px; margin-top: 20px;">
				<th colspan="8">View Expenses</th>
				<tr class="descriptions">
					<td></td>
					<td>Date</td>
					<td>Payee/Payer</td>
					<td>Description</td>
					<td>Type</td>
					<td>Amount</td>
					<td>Total</td>
				</tr>';

	echo sbm_get_expense_list($year)	;



	echo '</table>';


	echo '</div>';
}

function sbm_get_deposits_list($year = '', $csv = '')
{
	global $wpdb;
	global $current_user;
	$currency_symbol = get_option( 'sbm_currency' );


	$payee_payer_info 	= new sbm_payee_payer();
	$deposit_type_info 	= new sbm_deposit_type();
	$customer_info 		= new sbm_customer();
    $content            = '';
    $filter             = null;
    $csv_data           = '';
     get_currentuserinfo($current_user->ID);

            echo '<div class="wrap">';


        if(!empty($year))
        {
            $jan = mktime(0,0,0,1,1,$year);
            $dec = mktime(23,23,59,12,31,$year);
            $filter = "WHERE transaction_date BETWEEN '$jan' AND '$dec'";
        }
        else
        {
            $filter = null;
        }
		$query = "SELECT
						ID,
						transaction_id,
						transaction_date,
						payee_payer_id,
						transaction_type_id,
						deposit_type_id,
						description,
						amount,
						check_number
					FROM
						".$wpdb->prefix."sbm_deposits
						$filter
					ORDER BY
						transaction_date DESC";


	$transaction_list = $wpdb->get_results($query);
			$i = 1;
			$amount = 0;
		foreach($transaction_list as $list)
		{

				//  classes/expense_type.php:     sbm_get_expense_type_data()
				$payee_payer_info->sbm_get_payee_payer_data($list->payee_payer_id);
				$deposit_type_info->sbm_get_deposit_type_data($list->deposit_type_id);

				$deposit_description 	= $deposit_type_info->payee_payer_name;
				$description 			= $list->description;
				$type 					= 'Deposit';
				$amount += $list->amount;


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



				$content .= '<tr class="' . $bg . '">
							<td class="position-relative">';
				if( $list->amount > 0 )
				{
					$content .= '
							<a class="reverse-link" href="javascript: void(0);">Reverse</a>
							<div class="reverse">
									<h3>Are you sure you want to reverse this?</h3>
									<div class="reverse_yes">
										<a class="yes-link" href="javascript: void(0);">Yes</a>
										<input type="hidden" class="reverse_deposit_id" value="' . $list->ID . '">
									</div>
									<div class="reverse_no">
										<a class="no-link" href="javascript: void(0);">No</a>
									</div>
								</div>';
				}
					$content .= '
							</td>
							<td>' . date("m/d/Y", $list->transaction_date) . '</td>
							<td>' . $payee_payer_info->payee_payer_name . '</td>
							<td>' . $description . '</td>
							<td>' . $type . '</td>
							<td>' . $currency_symbol . '' . number_format($list->amount, 2) . '</td>
							<td></td>
						</tr>';

				$i++;
            $csv_data .= date("m/d/Y", $list->transaction_date).',';
            $csv_data .= $payee_payer_info->payee_payer_name . ',';
            $csv_data .= $description . ',';
            $csv_data .= $type . ',';
            $csv_data .= $currency_symbol . '' . $list->amount . '|';

		}
		// now show the total
				$content .= '<tr class="' . $bg . '">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>Total ' . $currency_symbol . '' . number_format($amount, 2) . '</td>
						</tr>';
        if(empty($csv))
        {
            return $content;
        }
        else
        {
            return $csv_data;
        }


}

function sbm_get_expense_list($year = '', $csv = '')
{
	global $wpdb;
	global $current_user;
	$currency_symbol = get_option( 'sbm_currency' );


	$payee_payer_info 	= new sbm_payee_payer();
	$expense_type_info 	= new sbm_expense_type();
	$customer_info 		= new sbm_customer();
    $content            = '';
    $filter             = null;
    $csv_data           = '';

    if(!empty($year))
    {
        $jan = mktime(0,0,0,1,1,$year);
        $dec = mktime(23,23,59,12,31,$year);
        $filter = "WHERE transaction_date BETWEEN '$jan' AND '$dec'";
    }
    else
    {
        $filter = null;
    }

     get_currentuserinfo($current_user->ID);

		echo '<div class="wrap">';


		$query = "SELECT
						ID,
						transaction_id,
						transaction_date,
						payee_payer_id,
						transaction_type_id,
						expense_type_id,
						description,
						amount,
						check_number
					FROM
						".$wpdb->prefix."sbm_expenses
						$filter
					ORDER BY
						transaction_date DESC";


	$transaction_list = $wpdb->get_results($query);
			$i = 1;
			$amount = 0;
		foreach($transaction_list as $list)
		{

				//  classes/expense_type.php:     sbm_get_expense_type_data()
				$payee_payer_info->sbm_get_payee_payer_data($list->payee_payer_id);
				$expense_type_info->sbm_get_expense_type_data($list->expense_type_id);

				$expense_description 	= $expense_type_info->payee_payer_name;
				$description 			= $list->description;
				$type 					= 'Expense';
				$amount 				= $amount + $list->amount;


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


				$content .= '<tr class="' . $bg . '">
							<td class="position-relative">';
				if( $list->amount > 0 )
				{
					$content .= '
							<a class="reverse-link" href="javascript: void(0);">Reverse</a>
								<div class="reverse">
									<h3>Are you sure you want to reverse this?</h3>
									<div class="reverse_yes">
										<a class="yes-link" href="javascript: void(0);">Yes</a>
										<input type="hidden" class="reverse_expense_id" value="' . $list->ID . '">
									</div>
									<div class="reverse_no">
										<a class="no-link" href="javascript: void(0);">No</a>
									</div>
								</div>';
				}
				$content .= '
							</td>
							<td>' . date("m/d/Y", $list->transaction_date) . '</td>
							<td>' . $payee_payer_info->payee_payer_name . '</td>
							<td>' . $description . '</td>
							<td>' . $type . '</td>
							<td>' . $currency_symbol . '' . number_format($list->amount, 2) . '</td>
							<td></td>
						</tr>';

				$i++;
            $csv_data .= date("m/d/Y", $list->transaction_date) . ',';
            $csv_data .= $payee_payer_info->payee_payer_name . ',';
            $csv_data .= $description .',';
            $csv_data .= $type .',';
            $csv_data .= $currency_symbol . '' . $list->amount . '|';

		}
		// now show the total
				$content .= '<tr class="' . $bg . '">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>Total ' . $currency_symbol . '' . number_format($amount, 2) . '</td>
						</tr>';
    if(empty($csv))
    {
        return $content;
    }
    else
    {
        return $csv_data;
    }

}


function sbm_customers_with_balance()
{
	global $wpdb;
	global $current_user;
	$wpdb->show_errors();
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

	echo '<table class="bw_table" id="invoices">
				<th colspan="7">Unpaid invoices</th>
				<tr class="descriptions">
					<td>Invoice #</td>
					<td>Date</td>
					<td>Total</td>
					<td>Company</td>
					<td>First Name</td>
					<td>Last Name</td>
					<td>Invoice Total</td>
					<td>Pay Invoice</td>
				</tr>';

	echo sbm_get_invoice_list( $invoice_status );

	echo '</table>';


			echo '</div>';

}



function sbm_enter_deposit_expense()
{
	$currency_symbol = get_option( 'sbm_currency' );


	$deposit_expense_info   = new sbm_deposit_expense();
    $transaction_date       = null;
    $payee_payer_id         = null;
    $name                   = null;
    $amount                 = null;
    $description            = null;

	echo '<div class="wrap">';

	if(!empty($_GET['message']))
	{
			//  general_functions.php:     sbm_get_message()
			//  general_functions.php:     sbm_message_details()
			//  general_functions.php:     sbm_clear_notice()

			echo '<div id="message" class="success">' . sbm_get_message($_GET['message']) . ' ' . sbm_message_details($_GET['message_details']) . '</div>';
			// call the function that will remove the success div after 3 seconds
			sbm_clear_notice('message', '3');
			// An example output would be: Transaction Complete: Expense to Farmers Insurance in the amount of $603.56 on 09/10/2010

	}

	// Validate posted information and check for errors, and if all is well, enter the information
	if(  (isset($_POST['amount'])) && (isset($_POST['transaction_date'])) && (isset($_POST['name'])) 	)
	{
		$errors = array();


		if(empty($_POST['amount']))
		{
			$errors[] = 'You forgot to enter an amount.';
		}
		if(empty($_POST['transaction_date']))
		{
			$errors[] = 'You forgot to enter the date.';
		}
		if(empty($_POST['name']))
		{
			$errors[] = 'You forgot to enter the name of the payer / payee.';
		}
		// No payee/payer used from an existing entry, this is being created on the fly
		if(empty($_POST['payee_payer_id']))
		{
			// insert the new payee/payer information and get the id
			// if it fails, show the error, and have them enter the payee/payer first then return here and select it from the list
			$payee_payee_info = new sbm_payee_payer();
			$_POST['payee_payer_status'] = 'Current';
			$_POST['payee_payer_name'] = $_POST['name'];
			$_POST['payee_payer_id'] = $payee_payee_info->sbm_update_payee_payer('noredirect');

		}



		if(empty($errors))
		{
				$deposit_expense_info->sbm_update_deposit_expense();
				die('Should go to sbm_update_deposit_expense here');
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
	echo '<form id="deposit_expense" method="post" action="' . $_SERVER['REQUEST_URI'] . '">
			<table>';

	// Date
	echo'<tr>
				<td>Date:</td>
				<td><input type="text" autocomplete="off" id="transaction_date" name="transaction_date" size="20" value="'.sbm_sticky_input($_POST['transaction_date'], $transaction_date).'" class="required"> </td>
				<td></td>
			</tr>';
	// Transaction Type
	echo '<tr>
					<td>Transaction Type</td>
					<td colspan="6"><select id="transaction_type_id" name="transaction_type_id"><option value="">-- Select if needed --</option>' . sbm_get_transaction_types_as_option() . '</select></td>
					<td></td>
				</tr>';
	// Payer / Payee
	echo'<tr>
				<td><div>Payer / Payee (  <a href="javascript: void(0);" id="options_link">Show Options</a>  )</div>
					<div id="options" class="options">
						<div class="close_option"><a href="javascript: void(0);" class="close_link">Close</a></div>
						<div><b><i>This stuff may or may not work I have not had time to check it</i></b></div>
						<div>Autocomplete (default unchecked): <input type="checkbox" id="autocomplete"><div>
						<div>Autocomplete: This means that all fields will be filled in based on the last input from that payer/payee if a match is found and selected </div>
						<div>&nbsp;</div>
						<div>Show Similar Names(default checked): <input type="checkbox" id="similar_names" checked="checked"></div>
						<div> This means that a drop down box will appear below the input field for the payer/payee with names that are similar to what you typed in. <br> If you click on one it will enter that information into the box for you. </div>
						<div>&nbsp;</div>
						<div>Additional information (default checked): <input type="checkbox" onMouseUp="sbm_toggleAdditionalInformation();" id="fill_in_data" checked="checked" ></div>
						<div>What this means is that any new entries will be asked to fill in other related data such as address, and phone numbers<br> 			*** unchecked will just use the name and thats all, no other prompts occur ***</div>
					</div>
				</td>
				<td style="vertical-align: top;">
				<div style="position: relative;">
					<input type="text" style="float: left;" autocomplete="off" id="payer_payee" name="name" size="20" value="'.sbm_sticky_input($_POST['name'], $name).'" class="required"><div style="float: left;" id="suggestion_toggle">( <a href="javascript: void(0);" id="show_information">Show Information</a> )</div>

				<div style="clear: left;"></div>

					<input type="hidden" name="payee_payer_id" id="payee_payer_id" value="'.sbm_sticky_input($_POST['payee_payer_id'], $payee_payer_id).'">
					<input type="hidden" id="enter_new_payer_payee" value="yes">
					<input type="hidden" class="payer_payee"  id="new_address" name="address">
					<input type="hidden" class="payer_payee"  id="new_city" name="city">
					<input type="hidden" class="payer_payee"  id="new_state" name="state">
					<input type="hidden" class="payer_payee"  id="new_zip" name="zip">
					<input type="hidden" class="payer_payee"  id="new_phone" name="phone">
					<input type="hidden" class="payer_payee"  id="new_contact" name="contact">
					<div id="payer_payee_suggestion"></div>


				</div>
				</td>
				<td></td>
			</tr>';

	// Amount
	echo'<tr>
				<td>Amount:</td>
				<td>' . $currency_symbol . '<input type="text" autocomplete="off" id="amount" name="amount" size="20" value="'.sbm_sticky_input($_POST['amount'], $amount).'" class="required"> </td>
				<td></td>
			</tr>';
	// Check Number
	echo'<tr>
				<td>Check Number:</td>
				<td><input type="text" autocomplete="off" id="check_number" name="check_number" size="20" value="'.sbm_sticky_input($_POST['check_number'], $amount).'"> </td>
				<td></td>
			</tr>';
	// Memo
	echo'<tr>
				<td>Memo:</td>
				<td><input type="text" autocomplete="off" id="description" name="description" size="20" value="'.sbm_sticky_input($_POST['description'], $description).'"> </td>
				<td></td>
			</tr>';

	echo '<tr>
				<td>Is this a deposit or expense</td>
				<td><select id="choose_deposit_or_expense" class="required">
							<option value="">-- Select One -- </option>
							<option value="deposit">Deposit</option>
							<option value="expense">Expense</option>
						</select>
				</td>
			</tr>';
	// Deposit
	echo '<tr id="deposit_section">
					<td id="deposit_description" class="default_disabled">Deposit Category</td>
					<td colspan="6"><select id="deposit_type_id" name="deposit_type_id" disabled><option value="">-- Select if needed --</option>' . sbm_get_deposit_types_as_option() . '</select></td>
					<td></td>
				</tr>';
	// Expense
	echo '<tr id="expense_section">
					<td id="expense_description" class="default_disabled">Expense Category</td>
					<td colspan="6"><select id="expense_type_id" name="expense_type_id" disabled><option value="">-- Select if needed --</option>' . sbm_get_expense_types_as_option() . '</select></td>
					<td></td>
				</tr>
			<tr>
				<td colspan="3">


					</div>
				</td>
			</tr>';



	// submit button and cancel button
	echo '<tr>
				<td colspan="3"><div style="clear: left;"></div>
				<div class="float-left medium-padding">
				<span><input type="submit" value="Submit" id="enterDepositOrExpense"></span>';
	// sbm_cancel_button: general_functions.php
	echo sbm_cancel_button('sbm_accounting', 'cancel');
	// check for a return to url request, if one is found, create the button
	if(!empty($_GET['return_url']))
	{
		//	sbm_return_url_button: general_functions.php
		echo sbm_return_url_button($_GET);
	}

	echo '</div>
				</td>
			</tr>';


	echo '</table></form>';


	//  help_functions.php:     sbm_display_help()
	echo sbm_display_help( 'deposit_expense' );
	echo '</div>';
}

function sbm_get_year_range_from_deposits_expenses()
{
	global $wpdb;

        $sql = "SELECT transaction_date FROM ".$wpdb->prefix."sbm_deposits ORDER BY transaction_date ASC LIMIT 0,1";
  		$oldest_deposit = (int) $wpdb->get_var($wpdb->prepare($sql));

  		$sql = "SELECT transaction_date FROM ".$wpdb->prefix."sbm_deposits ORDER BY transaction_date DESC LIMIT 0,1";
  		$newest_deposit = (int) $wpdb->get_var($wpdb->prepare($sql));


        $sql = "SELECT transaction_date FROM ".$wpdb->prefix."sbm_expenses ORDER BY transaction_date ASC LIMIT 0,1";
  		$oldest_expense = (int) $wpdb->get_var($wpdb->prepare($sql));

  		$sql = "SELECT transaction_date FROM ".$wpdb->prefix."sbm_expenses ORDER BY transaction_date DESC LIMIT 0,1";
  		$newest_expense = (int) $wpdb->get_var($wpdb->prepare($sql));

    if($oldest_deposit < $oldest_expense)
    {
        $oldest = $oldest_deposit;
    }
    else
    {
        $oldest = $oldest_expense;
    }
    if($newest_deposit > $newest_expense)
    {
        $newest = $newest_deposit;
    }
    else
    {
        $newest = $newest_expense;
    }

		return array(date("Y", $oldest), date("Y", $newest));

}


?>