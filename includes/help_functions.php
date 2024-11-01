<?php

function sbm_display_help( $page )
{
	
	$help = array(
					'adjust_accounting_record' => 'Adjust Accounting Record',
					'bounced_check' => 'Bounced Check',
					'deposit_expense' => 'Deposits and Expenses',
					'deposit_type' => 'Deposit Types',
					'expense_type' => 'Expense Types',
					'letter' => 'Letters',
					'payee_payer' => 'Payee and Payers',
	 				'customer' => 'Customers',
					'settings' => 'Settings for the Simple Business Manager',
					'transaction_type' => 'Transaction Types',
					'user' => 'User Management',
					'enter_odometer' => 'Odometer',
					'company_report' => 'Company Report',
					'public_customer_signup' => 'Public Customer Sign Up',
					'invoice' => 'Invoices'
				);
	
				
	
	if( empty( $page ) )
	{
		$content = '<div class="wrap">
		<h2>For questions or comments, please go to <a href="http://www.russellalbin.com/contact/" target="_blank">www.russellalbin.com/contact/</a></h2><h3>Help</h3>';
		$content .= '<label for="show_help">Show help for</label><select name="show_help" id="show_help_document"><option value="">-- Please Select --</option>';
	}
	else 
	{
		$content = '<div id="help"><h3>Help for ' . $help[$page] . '</h3>';
	}
	
	foreach($help as $key => $name )
	{
		if( empty( $page ))
		{
			$content .= '<option value="' . $key . '">' . $name . '</option>';
		}
	
		if($page == $key)
		{
		 switch($name)
	       {     
	        case $help[adjust_accounting_record];
	                $content .='<div>Adjust Accounting Records</div>
						
						<div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li>Amount Due</li>
	                            <li>If there is an amount paid, that will also be required</li>
							</ul>
						</div>';    
	       	break;
			case $help[bounced_check];
			$content .='<div>Bounced Checks</div>
						
						<div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li>Bank Account</li>
								<li>Date</li>
								<li>customer from the list</li>
								<li>Bounced check fee</li>
								<li>Late Payment type (either a percentage of the rent or a flat rate)</li>
								<li>Select wether or not to re-apply the original amount back to the customer</li>
								<li>If you select NO, to re-apply, the description is required</li>
							</ul>
						</div>';
			break;
			case $help[deposit_expense];
			$content .='<h4>Deposit and Expenses  </h4>';
			break;
			case $help[deposit_type];
			$content .='<div>Add a new or edit a deposit type</div>
						<div>This list is used when entering deposits and expenses</div>
						<div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li>Name</li>
							</ul>
						</div>';
			break;
			case $help[expense_type];
			$content .='<div>Add a new or edit a expense type</div>
						<div>This list is used when entering deposits and expenses</div>
						<div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li>Name</li>
							</ul>
						</div>';
			break;
			case $help[letter];
			$content .='<div>Add a new or edit a letter</div>
						<div>This list is used when entering a new letter template</div>
						<div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li>Title of the letter</li>
								<li>Letter Content</li>
							</ul>
						</div>';
			break;
			
			case $help[payee_payer];
			$content .= '<div>This is used when entering deposits and expenses.  You are required to designate either the payee or the payer for the transaction.</div>
								<div>This is a list of people and companies that you are either paying or are paying you for services</div>
								<div>The purpose of this page is to edit the information for this payee / payer</div>
						 <div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li>Name</li>
							</ul>
						</div>';
			break;
			case $help[customer];
			$content .='<div>Add a new or edit a customer</div>
						<div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li>Last Name</li>
								<li>First Name</li>
							</ul>
						</div>';
			break;
			case $help[settings];
			$content .= '<div>Settings for your company</div>
							<ul>
								<li>Company Name</li>
								<li>Address</li>
								<li>city</li>
								<li>State</li>
								<li>Zip</li>
								<li>Default Tax Rate</li>
								<li>Default Hourly Rate</li>
								<li>URL to image, please use the media upload as you would any image, and make note of the File Url after you upload it  If it is larger than 89px Height, it will automatically scale it down.</li>
								<li>Invoice image position is if you have an image that is not the max 89px Height x 900px Wide you can have it left, center or right justified</li>
							</ul>';
			break;
			case $help[transaction_type];
			$content .='<div>Add a new or edit a transaction type</div>
						<div>This list is used when entering deposits and expenses</div>
						<div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li>Name</li>
							</ul>
						</div>';
			break;
			case $help[user];
			$content .= '<div>Add a new or edit a user</div>
						 <div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li>Login Name</li>
								<li>Email</li>
								<li>First Name</li>
								<li>Last Name</li>
								<li>Password</li>
							</ul>
						</div>';
			break;
			case $help[enter_odometer];
			$content .= '<div>Enter odometer to track miles.  Useful  if you get reimbursed for miles traveled</div>
						 <div><span  class="underline">Required fields</span>:<br>
							<ul>
								<li></li>
							</ul>
						</div>';
			break;
			case $help[company_report];
			$content .= '<div>Company Report allows you a quick overview of the company\'s financial status</div>
						 <div><span class="underline">Invoices</span>:<br>
							<ul>
								<li>Number of invoices is all pending, invoiced and paid</li>
								<li>Invoice totals is the combined amount of all pending, invoiced and paid</li>
							</ul>
						</div>';
			break;
			case $help[public_customer_signup];
				$content .= '<div>To have customers sign up using a page or post on the public side of your web site, just use the short code:';
				$content .= '<div>[public_customer_signup]</div>
							<div>You can put this short code on any page or post viewable to the general public</div>
							<div>The page will look similar to this</div>
							<div><img src="' . SBM_PLUGIN_URL . '/images/public_customer_signup.jpg" border="0" alt="Sample Public User Sign Up"></div>
							<h3>When saved, the customer will be entered as a Prospective customer</h3>';
			break;		
			case $help[settings];
			$content .= '<div>Settings help</div>';
			$content .= '<div></div>';
			break;
			default;
			$content .= '<div>There are no help topics</div>';
			break;
	       }
		}
	}
	
	if( empty( $page ) )
	{
		$content .= '</select></div><div id="show_help_information">Nothing to show yet, please choose from the select box</div>';
	}
	

	$content .='</div>';
	
	if( empty( $page ) )
	{
		echo $content;
	}
	else 
	{
		return $content;
	}
	
	
}




?>