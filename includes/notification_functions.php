<?php

function sbm_view_notifications_page() 
{
	global $wpdb;
	global $current_user;

     get_currentuserinfo($current_user->ID);
		
	echo '<div class="wrap">';
	

	$num = sbm_get_number_of_notices('noecho');
	
	
	if ($num > 0 )
	{
			// Display any notices that are affecting the user, such as no properties, buildings, units or expense types, etc.
			echo '<h2>Notifications</h2>';
			
			// This is for the version check
			$installed_version = get_option( "sbm_version" );
			
			/*
			if($installed_version != $online_version)
			{
				echo '<div class="recommended">You do not have the latest version of SBM<br>
				Installed version:  ' .$installed_version . '<br>
				Online Version: ' .$online_version . '
				</div>';
			}
		*/
			
			// Not Required but suggested
			if ( sbm_count_payee_payers() == 0 )
			{
				echo '<div class="recommended">You do not have any Payee / Payer, you should consider adding them by clicking <a href="admin.php?page=sbm_edit_payee_payer&status=new">here</a>.</div>';
			}
			
			
			
			// Required for the system to work properly
			// User account is complete
			// Transaction types
			if ( sbm_count_transaction_types() == 0 )
			{
				echo '<div class="needed">You do not have any transaction types, you need them and can add them by clicking <a href="admin.php?page=sbm_edit_transaction_type&status=new">here</a>.</div>';
			}
			
			// Expense types
			if ( sbm_count_expense_types() == 0 )
			{
				echo '<div class="needed">You do not have any expense types, you need them and can add them by clicking <a href="admin.php?page=sbm_edit_expense_type&status=new">here</a>.</div>';
			}
			
			// Deposit Types
			if ( sbm_count_deposit_types() == 0 )
			{
				echo '<div class="needed">You do not have any deposit types, you need them and can add them by clicking <a href="admin.php?page=sbm_edit_deposit_type&status=new">here</a>.</div>';
			}
			 
			
			
			// customers
			if ( sbm_count_customers() == 0 )
			{
				echo '<div class="needed">You do not have any customers, you need them and can add them by clicking <a href="admin.php?page=sbm_edit_customer&status=new">here</a>.</div>';
			}
	}
	else
	{
		echo '<h2>You do not have any notifications!</h2>';
	}
	
	echo '</div>';
}



?>