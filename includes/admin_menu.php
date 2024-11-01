<?php

function sbm_admin_menu() {
	
    global $current_user;
     get_currentuserinfo($current_user->ID);

	$sbm_menu_option = get_option( 'sbm_menu_option' );
	
			// Short menu
	if( $sbm_menu_option == 'sbm_short_menu' ) 
	{

		
        // Logged in Home page
        add_menu_page(
                "Small Business Manager Home page", // description of this / page title
                "SBM Home", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_home_page",
        		"sbm_view_home_page", // function
        		SBM_PLUGIN_URL . "/images/sbm_home_icon.gif" // icon url
        );

        // company profile
        add_menu_page(
                "Small Business Manager Company profile", // description of this
                "SBM Company",
                "edit_posts", // level of access, read is the lowest
                "sbm_company_profile", // related function name to tie sub menus to this main menu
        		"sbm_view_company_report", // function
        		SBM_PLUGIN_URL . "/images/sbm_company_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View Company Report", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_company_profile", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_company_report" // function that is being called
        );
		add_submenu_page(
                "sbm_company_profile", // name of function
                "Home Page", // title
                "Home Page", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "edit_posts", // level of access, read is the lowest
                "sbm_view_home_page", // unique name
                "sbm_view_home_page"// function that is being called
        );


        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "View Company Report", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View Company Report", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_company_report", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_company_report"// function that is being called
        );

        add_submenu_page(
                "sbm_company_profile",
                "Transaction types", // title
                "View or Edit Transaction types", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_transaction_type_list", // unique name
                "sbm_view_transaction_type_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                "View or Edit Transaction Type", // title
                "", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_transaction_type", // unique name
                "sbm_edit_transaction_type"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                "Expense Types", // title
                "View or Edit Expense types", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_expense_type_list", // unique name
                "sbm_view_expense_type_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                "View or Edit Transaction Type", // title
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_expense_type", // unique name
                "sbm_edit_expense_type"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                "Deposit Types", // title
                "View or edit deposit types", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_deposit_type_list", // unique name
                "sbm_view_deposit_type_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                "View or Edit Transaction Type", // title
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_deposit_type", // unique name
                "sbm_edit_deposit_type"// function that is being called
        );

        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_customer_profile", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_customer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Add customer", // title
                "Add customer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_customer&status=new", // unique name
                "sbm_edit_customer&status=new"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "View customer List", // title
                "View/Edit customers", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_customer_list", // unique name
                "sbm_view_customer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                NULL,
               	NULL,
                "edit_posts",
                "sbm_edit_customer",
                "sbm_edit_customer"
        );
        add_submenu_page(
                "sbm_company_profile",
                "View customer account",
                NULL,
                "edit_posts",
                "sbm_view_customer_account",
                "sbm_view_customer_account"
        );
        add_submenu_page(
                "sbm_company_profile",
                "Adjust accounting record",
                NULL,
                "edit_posts",
                "sbm_adjust_accounting_record",
                "sbm_adjust_accounting_record"
        );
		// Invoices
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_invoices", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_invoices"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "New Invoice", // title
                "New Invoice", // what is displayed
                "edit_posts", // level of access, read is the lowest, read is right above read
                "sbm_create_invoice&status=new", // unique name
                "sbm_create_invoice&status=new"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Show invoices", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Show invoice list", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_invoices", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_invoices"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Show pending invoices", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Show pending invoice list", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_get_pending_invoices_list", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_get_pending_invoices_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Show unpaid invoices", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Show unpaid invoice list", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_get_unpaid_invoices_list", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_get_unpaid_invoices_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Show paid invoices", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Show paid invoice list", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_get_paid_invoices_list", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_get_paid_invoices_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // title
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest, read is right above read
                "sbm_create_invoice", // unique name
                "sbm_create_invoice"// function that is being called
        );
		// payee and payer
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_payee_payer_profile", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_payee_payer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Add Payee/Payer", // title
                "Add Payee/Payer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_payee_payer&status=new", // unique name
                "sbm_edit_payee_payer&status=new"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "View customer List", // title
                "View/Edit Payee/Payer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_payee_payer_list", // unique name
                "sbm_view_payee_payer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                NULL,
                NULL,
                "edit_posts",
                "sbm_edit_payee_payer",
                "sbm_edit_payee_payer"
        );
        add_submenu_page(
                "sbm_company_profile",
                NULL,
                NULL,
                "edit_posts",
                "sbm_view_payee_payer_account",
                "sbm_view_payee_payer_account"
        );


		// odometer
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_odometer", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_odometer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Add Odometer", // title
                "Add Odometer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_odometer&status=new", // unique name
                "sbm_edit_odometer&status=new"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "View Odometer List", // title
                "View/Edit Odometer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_odometer_list", // unique name
                "sbm_view_odometer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                NULL,
                NULL,
                "edit_posts",
                "sbm_edit_odometer",
                "sbm_edit_odometer"
        );
		// Accounting
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_accounting", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_customers_with_balance"// function that is being called
        );

        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Show customers with a balance", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Accept Payments from customers", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_customers_with_balance", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_customers_with_balance"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Deposit Review", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_deposit_review", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_deposit_review"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Enter Deposit and Expenses", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Enter Deposit and Expenses", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_enter_deposit_expense", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_enter_deposit_expense"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "View Deposits and Expenses", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View Deposit and Expenses", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_deposit_expense", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_deposit_expense"// function that is being called
        );
		
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_pay_invoice", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_pay_invoice"// function that is being called
        );
		


		// Letter Generator
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_letter_generator", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_letter_list"// function that is being called
        );

        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                NULL, // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_letter_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "View ", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View Completed Templates", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_letter_list", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_letter_list"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "View ", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_letter", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_edit_letter"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "Generate a letter to customer ", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Generate a letter", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_generate_letter", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_generate_letter"// function that is being called
        );
	
		
		// Link to view letters for individual customers
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View letters for customer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_list_letters_for_customer", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_list_letters_for_customer"// function that is being called
        );

		// Project
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_list_projects", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_list_projects"// function that is being called
        );

		// Notifications
        add_submenu_page(
                "sbm_company_profile", // name of function
                NULL, // title
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "read", // level of access, read is the lowest
                "sbm_view_notifications_page", // unique name
                "sbm_view_notifications_page"// function that is being called
        );
		
		// Settings
        add_submenu_page(
                "sbm_company_profile", // name of function
                "Settings", // title
                "Settings", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "manage_options", // level of access, read is the lowest
                "sbm_settings", // unique name
                "sbm_settings"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // name of function
                "Readme / Changelog", // title
                "Readme / Changelog", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "manage_options", // level of access, read is the lowest
                "sbm_readme", // unique name
                "sbm_readme"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // name of function
                "Remove All Data", // title
                "Remove All Data", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "manage_options", // level of access, read is the lowest
                "sbm_remove_data", // unique name
                "sbm_remove_data"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // name of function
                "Display Help", // title
                "Display Help", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "read", // level of access, read is the lowest
                "sbm_display_help", // unique name
                "sbm_display_help"// function that is being called
        );
        
	
		
	} // End short menu
	else 
	{
	// Full Menu 
        // Logged in Home page
        add_menu_page(
                "Small Business Manager Home page", // page title
                "SBM Home", // menu title
                "read", // capability, read is the lowest
                "sbm_view_home_page", // menu slug
        		"sbm_view_home_page", // function
        		SBM_PLUGIN_URL . "/images/sbm_home_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_view_home_page", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View Company Report", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_home_page", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_home_page"// function that is being called
        );

        // company profile
        
        add_menu_page(
                "Small Business Manager admin menu options", // description of this
                "SBM Company",
                "edit_posts", // level of access, read is the lowest
                "sbm_company_profile", // related function name to tie sub menus to this main menu
        		"sbm_view_company_report", // function
        		SBM_PLUGIN_URL . "/images/sbm_company_icon.gif" // icon url
        );
        
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_company_profile", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_company_report" // function that is being called
        );
        add_submenu_page(
                "sbm_company_profile", // ties this submenu to parent menu
                "View Company Report", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View Company Report", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_company_report", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_company_report"// function that is being called
        );

        add_submenu_page(
                "sbm_company_profile",
                "Transaction types", // title
                "View or Edit Transaction types", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_transaction_type_list", // unique name
                "sbm_view_transaction_type_list"// function that is being called
        );
        add_submenu_page(
                "",
                "View or Edit Transaction Type", // title
                "", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_transaction_type", // unique name
                "sbm_edit_transaction_type"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                "Expense Types", // title
                "View or Edit Expense types", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_expense_type_list", // unique name
                "sbm_view_expense_type_list"// function that is being called
        );
        add_submenu_page(
                "",
                "View or Edit Transaction Type", // title
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_expense_type", // unique name
                "sbm_edit_expense_type"// function that is being called
        );
        add_submenu_page(
                "sbm_company_profile",
                "Deposit Types", // title
                "View or edit deposit types", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_deposit_type_list", // unique name
                "sbm_view_deposit_type_list"// function that is being called
        );
        add_submenu_page(
                NULL,
                "View or Edit Transaction Type", // title
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_deposit_type", // unique name
                "sbm_edit_deposit_type"// function that is being called
        );

        // customer menu
        add_menu_page(
                "Small Business Manager customer options", // description of this
                "SBM Customers",
                "read", // level of access, read is the lowest
                "sbm_customer_profile", // related function name to tie sub menus to this main menu
        		"sbm_view_customer_list", // function
        		SBM_PLUGIN_URL . "/images/sbm_customer_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_customer_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "read", // level of access, read is the lowest
                "sbm_customer_profile", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_customer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_customer_profile", // ties this submenu to parent menu
                "Add customer", // title
                "Add customer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_customer&status=new", // unique name
                "sbm_edit_customer&status=new"// function that is being called
        );
        add_submenu_page(
                "sbm_customer_profile", // ties this submenu to parent menu
                "View customer List", // title
                "View/Edit customers", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_customer_list", // unique name
                "sbm_view_customer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_customer_profile",
                NULL,
               	NULL,
                "edit_posts",
                "sbm_edit_customer",
                "sbm_edit_customer"
        );
        add_submenu_page(
                "sbm_customer_profile",
                "View customer account",
                NULL,
                "read",
                "sbm_view_customer_account",
                "sbm_view_customer_account"
        );
        add_submenu_page(
                "sbm_customer_profile",
                "Adjust accounting record",
                NULL,
                "edit_posts",
                "sbm_adjust_accounting_record",
                "sbm_adjust_accounting_record"
        );
		
		// Invoices
        add_menu_page(
                "Small Business Manager invoices", // description of this
                "SBM Invoices",
                "read", // level of access, read is the lowest
                "sbm_invoices", // related function name to tie sub menus to this main menu
        		"sbm_view_invoices", // function
        		SBM_PLUGIN_URL . "/images/sbm_invoice_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_invoices", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "read", // level of access, read is the lowest
                "sbm_invoices", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_invoices"// function that is being called
        );
        add_submenu_page(
                "sbm_invoices", // ties this submenu to parent menu
                "New Invoice", // title
                "New Invoice", // what is displayed
                "edit_posts", // level of access, read is the lowest, read is right above read
                "sbm_create_invoice&status=new", // unique name
                "sbm_create_invoice&status=new"// function that is being called
        );
        add_submenu_page(
                "sbm_invoices", // ties this submenu to parent menu
                "Show invoices", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Show invoice list", // what is displayed
                "read", // level of access, read is the lowest
                "sbm_view_invoices", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_invoices"// function that is being called
        );
        add_submenu_page(
                "sbm_invoices", // ties this submenu to parent menu
                "Show pending invoices", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Show pending invoice list", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_get_pending_invoices_list", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_get_pending_invoices_list"// function that is being called
        );
        add_submenu_page(
                "sbm_invoices", // ties this submenu to parent menu
                "Show unpaid invoices", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Show unpaid invoice list", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_get_unpaid_invoices_list", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_get_unpaid_invoices_list"// function that is being called
        );
        add_submenu_page(
                "sbm_invoices", // ties this submenu to parent menu
                "Show paid invoices", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Show paid invoice list", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_get_paid_invoices_list", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_get_paid_invoices_list"// function that is being called
        );
        add_submenu_page(
                "sbm_invoices", // ties this submenu to parent menu
                NULL, // title
                NULL, // what is displayed
                "read", // level of access, read is the lowest, read is right above read
                "sbm_create_invoice", // unique name
                "sbm_create_invoice"// function that is being called
        );
		
		// Payee Payer
        add_menu_page(
                "Small Business Manager Payee and Payer", // description of this
                "SBM Payee/Payer",
                "edit_posts", // level of access, read is the lowest
                "sbm_payee_payer_profile", // related function name to tie sub menus to this main menu
				"sbm_view_payee_payer_list", // function
        		SBM_PLUGIN_URL . "/images/sbm_payee_payer_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_payee_payer_profile", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_payee_payer_profile", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_payee_payer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_payee_payer_profile", // ties this submenu to parent menu
                "Add Payee/Payer", // title
                "Add Payee/Payer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_payee_payer&status=new", // unique name
                "sbm_edit_payee_payer&status=new"// function that is being called
        );
        add_submenu_page(
                "sbm_payee_payer_profile", // ties this submenu to parent menu
                "View customer List", // title
                "View/Edit Payee/Payer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_payee_payer_list", // unique name
                "sbm_view_payee_payer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_payee_payer_profile",
                NULL,
                NULL,
                "edit_posts",
                "sbm_edit_payee_payer",
                "sbm_edit_payee_payer"
        );

        add_submenu_page(
                "sbm_payee_payer_profile",
                NULL,
                NULL,
                "edit_posts",
                "sbm_view_payee_payer_account",
                "sbm_view_payee_payer_account"
        );

		
		// Odometer
        add_menu_page(
                "Small Business Manager Odometer", // description of this
                "SBM Odometer",
                "edit_posts", // level of access, read is the lowest
                "sbm_odometer", // related function name to tie sub menus to this main menu
        		"sbm_view_odometer_list", // function
        		SBM_PLUGIN_URL . "/images/sbm_odometer_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_odometer", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_odometer", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_odometer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_odometer", // ties this submenu to parent menu
                "Add Odometer", // title
                "Add Odometer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_odometer&status=new", // unique name
                "sbm_edit_odometer&status=new"// function that is being called
        );
        add_submenu_page(
                "sbm_odometer", // ties this submenu to parent menu
                "View Odometer List", // title
                "View/Edit Odometer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_odometer_list", // unique name
                "sbm_view_odometer_list"// function that is being called
        );
        add_submenu_page(
                "sbm_odometer",
                NULL,
                NULL,
                "edit_posts",
                "sbm_edit_odometer",
                "sbm_edit_odometer"
        );

        // Accounting
        add_menu_page(
                "Small Business Manager Accounting options", // description of this
                "SBM Accounting",
                "edit_posts", // level of access, read is the lowest
                "sbm_accounting", // related function name to tie sub menus to this main menu
        		"sbm_customers_with_balance", // function
        		SBM_PLUGIN_URL . "/images/sbm_accounting_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_accounting", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_accounting", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_customers_with_balance"// function that is being called
        );

        add_submenu_page(
                "sbm_accounting", // ties this submenu to parent menu
                "Show customers with a balance", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Accept Payments from customers", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_customers_with_balance", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_customers_with_balance"// function that is being called
        );
        add_submenu_page(
                "sbm_accounting", // ties this submenu to parent menu
                "Deposit Review", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_deposit_review", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_deposit_review"// function that is being called
        );
        add_submenu_page(
                "sbm_accounting", // ties this submenu to parent menu
                "Enter Deposit and Expenses", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Enter Deposit and Expenses", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_enter_deposit_expense", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_enter_deposit_expense"// function that is being called
        );
        add_submenu_page(
                "sbm_accounting", // ties this submenu to parent menu
                "View Deposits and Expenses", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View Deposit and Expenses", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_deposit_expense", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_deposit_expense"// function that is being called
        );
		
        add_submenu_page(
                "sbm_accounting", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_pay_invoice", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_pay_invoice"// function that is being called
        );
		


		// Letter Generator
        add_menu_page(
                "Small Business Manager Letter Generator", // description of this
                "SBM Letters",
                "edit_posts", // level of access, read is the lowest
                "sbm_letter_generator", // related function name to tie sub menus to this main menu
        		"sbm_view_letter_list", // function
        		SBM_PLUGIN_URL . "/images/sbm_letter_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_letter_generator", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_letter_generator", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_letter_list"// function that is being called
        );

        add_submenu_page(
                "sbm_letter_generator", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                NULL, // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_letter_list"// function that is being called
        );
        add_submenu_page(
                "sbm_letter_generator", // ties this submenu to parent menu
                "View ", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View Completed Templates", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_view_letter_list", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_view_letter_list"// function that is being called
        );
        add_submenu_page(
                "sbm_letter_generator", // ties this submenu to parent menu
                "View ", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_letter", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_edit_letter"// function that is being called
        );
        add_submenu_page(
                "sbm_letter_generator", // ties this submenu to parent menu
                "Generate a letter to customer ", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Generate a letter", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_generate_letter", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_generate_letter"// function that is being called
        );
	
		
		// Link to view letters for individual customers
        add_submenu_page(
                "sbm_letter_generator", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View letters for customer", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_list_letters_for_customer", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_list_letters_for_customer"// function that is being called
        );

		// Projects
        add_menu_page(
                "Small Business Manager Project Management", // description of this
                "SBM Projects",
                "edit_posts", // level of access, read is the lowest
                "sbm_project", // related function name to tie sub menus to this main menu
        		"sbm_list_projects", // function
        		SBM_PLUGIN_URL . "/images/sbm_project_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_project", // ties this submenu to parent menu
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                NULL, // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_project", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_list_projects"// function that is being called
        );


        add_submenu_page(
                "sbm_project", // ties this submenu to parent menu
                "View ", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "Add Project", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_edit_project", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_edit_project"// function that is being called
        );
        add_submenu_page(
                "sbm_project", // ties this submenu to parent menu
                "View ", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "View Projects", // what is displayed
                "edit_posts", // level of access, read is the lowest
                "sbm_list_projects", // same as add_menu_page to prevent a duplicate link from appearing
                "sbm_list_projects"// function that is being called
        );
		// Notifications
        add_menu_page(
                "Small Business Manager Notifications", // description of this / page title
                "SBM Notifications", // what is displayed
                "manage_options", // level of access, read is the lowest
                "sbm_view_notifications_page",
        		"sbm_view_notifications_page", // function
        		SBM_PLUGIN_URL . "/images/sbm_notification_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_view_notifications_page_function", // name of function
                NULL, // title
                NULL, // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "manage_options", // level of access, read is the lowest
                "sbm_view_notifications_page", // unique name
                "sbm_view_notifications_page"// function that is being called
        );
		
		// Settings
		add_menu_page(
                "SBM Settings", // description of this / page title
                "SBM Settings", // what is displayed
                "manage_options", // level of access, read is the lowest
                "sbm_settings",
				"sbm_settings",
				SBM_PLUGIN_URL . "/images/sbm_settings_icon.gif" // icon url
        );
        add_submenu_page(
                "sbm_settings", // name of function
                "Settings", // title
                "Settings", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "manage_options", // level of access, read is the lowest
                "sbm_settings", // unique name
                "sbm_settings"// function that is being called
        );
        add_submenu_page(
                "sbm_settings", // name of function
                "Readme / Changelog", // title
                "Readme / Changelog", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "manage_options", // level of access, read is the lowest
                "sbm_readme", // unique name
                "sbm_readme"// function that is being called
        );
        add_submenu_page(
                "sbm_settings", // name of function
                "Remove All Data", // title
                "Remove All Data", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "manage_options", // level of access, read is the lowest
                "sbm_remove_data", // unique name
                "sbm_remove_data"// function that is being called
        );
        add_submenu_page(
                "sbm_settings", // name of function
                "Display Help", // title
                "Display Help", // what is displayed  You can leave it blank to have the main menu to act as a link and not have a sub menu
                "manage_options", // level of access, read is the lowest
                "sbm_display_help", // unique name
                "sbm_display_help"// function that is being called
        );
        
	}
		// End Full Menu
		
    
}

?>