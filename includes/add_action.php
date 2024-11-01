<?php

if(!empty( $_SESSION['installed_database_version'] 	))
{
	
	add_action('admin_menu', 'sbm_admin_menu');	
	add_action( 'wp_before_admin_bar_render', 'sbm_admin_bar_add_links' );
		
	wp_register_script( 'sbm-jquery-datepicker', SBM_PLUGIN_URL . '/jquery/datepicker.js' );
	wp_register_script( 'sbm-jquery-ui-core', SBM_PLUGIN_URL . '/jquery/jquery.ui.core.js' );
	wp_register_script( 'sbm-jquery-ui-widget', SBM_PLUGIN_URL . '/jquery/jquery.ui.widget.js' );
	wp_register_script( 'sbm-jquery-dom-ready', SBM_PLUGIN_URL . '/jquery/sbm_dom_ready.js' );
	wp_register_script( 'sbm-jquery-timepicker', SBM_PLUGIN_URL . '/jquery/timepicker.js' );
	wp_register_script( 'sbm-jquery-validate', SBM_PLUGIN_URL . '/jquery/validate.js' );
	
	function enqueue_admin_jquery() {
		wp_enqueue_script('sbm-jquery-datepicker');		
		wp_enqueue_script('sbm-jquery-ui-core');
		wp_enqueue_script('sbm-jquery-ui-widget');		
		wp_enqueue_script('sbm-jquery-dom-ready');
		wp_enqueue_script('sbm-jquery-timepicker');
		wp_enqueue_script('sbm-jquery-validate');	
	}
	
	add_action('admin_init', 'enqueue_admin_jquery');

    function sbm_load_custom_wp_admin_style()
    {
        wp_register_style( 'sbm-custom_wp_admin_css', SBM_PLUGIN_URL . '/css/sbm-admin-style.css', false, '1.0.0' );
        wp_enqueue_style( 'sbm-custom_wp_admin_css' );
        wp_register_style( 'sbm-timepicker_admin_css', SBM_PLUGIN_URL . '/css/sbm-timepicker.css', false, '1.0.0' );
        wp_enqueue_style( 'sbm-timepicker_admin_css' );
    }

    // Loads our custom ADMIN section css
    add_action('admin_enqueue_scripts', 'sbm_load_custom_wp_admin_style');

	/*  AJAX add_action calls
	
		The functions for this are found in the ajax.php in this plugin
		The first argument is what we are calling this when we need it to execute
		for example, here is the jquer to execute a call to check the availablity of a username
		
					$("#checkUserName").click(function() {
												  
						$.post("./admin-ajax.php", {action:"sbmCheckUserName", "user_login": document.getElementById('user_login').value}, function(str)	{
						document.getElementById('is_username_taken').innerHTML =  str;
						});
				});
					
		The execution is looking for the action pmCheckUserName which is linked up to our add_action function:
		add_action('wp_ajax_sbmCheckUserName', 'sbm_check_user_name');
		It will find the add_action that has the pmCheckUserName in the first argument
		Then execute the second argument as a function
		That second argument is the function found on the ajax.php in this plugin or other function pages
	*/
	
	// This is for the username check when creating a new user.
	add_action('wp_ajax_sbmCheckUserName', 'sbm_check_user_name');
	
	// This deletes the meta data for the SBM items
	add_action('wp_ajax_sbm_deleterow', 'sbm_delete_sbm_meta');
	
	// this removes all tables that were created or modified  as well as any options during setup 
	add_action('wp_ajax_sbmClearAllTables', 'sbm_clear_all_tables');
		
	// This returns a list of suggested similar entries based on user input
	add_action('wp_ajax_sbmGetSuggestions', 'sbm_get_list_of_suggestions');
	
	// This updates the entire page of reconcile
	add_action('wp_ajax_sbmUpdateReconcile', 'sbm_update_reconcile');
	
	// Displaying the list of customers when sending a letter from the letter generator
	add_action('wp_ajax_sbmDisplaycustomerList', 'sbm_display_customer_list');
	
	// Save the letter 
	add_action('wp_ajax_sbmSaveLetter', 'sbm_save_letter');

	// Output a PDF
	add_action('wp_ajax_sbmCreateInvoicePdf', 'sbm_create_invoice_pdf');
	
	// Output a PDF
	add_action('wp_ajax_sbmCreatePdf', 'sbm_create_pdf');
	
	//quick view of letter
	add_action('wp_ajax_sbmQuickViewLetter', 'sbm_quickview_letter');
	
	// Returns an array of values to popluate the select box in the create report section
	add_action('wp_ajax_sbmDisplayOptions', 'sbm_list_of_display_options');
	
	// removes all entries in all tables that have the posted ID for customer_account_id
	add_action('wp_ajax_sbmDeleteTransaction', 'sbm_delete_financial_data_by_customer_account_id');
	
	// updates the amount due 
	add_action('wp_ajax_sbmUpdateAmountDue', 'sbm_update_amount_due');
	
	// updates amount paid
	add_action('wp_ajax_sbmUpdateAmountPaid', 'sbm_update_amount_paid');
	
	// suggest customers
	add_action('wp_ajax_sbmSuggestCustomers', 'sbm_suggest_customers');
	
	// convert invoice from pending to invoiced
	add_action('wp_ajax_sbmConvertInvoice', 'sbm_convert_invoice');
	
	// show invoice totals for different years
	add_action('wp_ajax_sbmShowInvoiceTotalsDifferentYear', 'sbm_show_invoice_totals_different_year');
	
	// show invoice totals for different years
	add_action('wp_ajax_sbmShowPaidInvoiceTotalsDifferentYear', 'sbm_show_paid_invoice_totals_different_year');
	
	// for invoices, this will update the customer information if they choose a different customer
	add_action('wp_ajax_sbmGetCustomerInformation', 'sbm_get_customer_information' );
	// help
	add_action('wp_ajax_sbmDisplayHelp', 'sbm_show_display_help');
	
	// suggest destination for odometer
	add_action('wp_ajax_sbmSuggestDestination', 'sbm_suggest_destination');
	
	// reverse a deposit
	add_action('wp_ajax_sbmReverseDeposit', 'sbm_reverse_desposit');
	
	// revers an expense
	add_action('wp_ajax_sbmReverseExpense', 'sbm_reverse_expense');
    // Save year of odometer as excel format
    add_action('wp_ajax_sbmSaveOdometer', 'sbm_save_odometer');
    // get total miles for year
    add_action('wp_ajax_sbmGetMilesForyear', 'sbm_count_miles_for_year_using_post');
    // Save/output the csv
    add_action('wp_ajax_sbmSaveDepositsExpenses', 'sbm_save_deposits_expenses');


}
?>