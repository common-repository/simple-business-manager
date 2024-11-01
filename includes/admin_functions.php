<?php
// The purpose for this page is to hold the functions that are for admin section only, not directly related to the SBM

	// Creates a direct link to settings for SBM
	function sbm_settings_link($links)
	{

		$settings_link = '<a href="admin.php?page=sbm_settings">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}
	// Add a quick link to view invoices on the new adminbar
	function sbm_admin_bar_add_links()
	{
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array(
			'parent' => false, // use 'false' for a root menu, or pass the ID of the parent menu
			'id' => 'sbm_view_invoices', // link ID, defaults to a sanitized title value
			'title' => __('Invoices'), // link title
			'href' => admin_url( 'admin.php?page=sbm_view_invoices'), // name of file
			'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
		));
		$wp_admin_bar->add_menu( array(
			'parent' => 'sbm_view_invoices', // use 'false' for a root menu, or pass the ID of the parent menu
			'id' => 'sbm_view_invoices', // link ID, defaults to a sanitized title value
			'title' => __('New Invoice'), // link title
			'href' => admin_url( 'admin.php?page=sbm_create_invoice&status=new'), // name of file
			'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
		));
		$wp_admin_bar->add_menu( array(
			'parent' => 'sbm_view_invoices', // use 'false' for a root menu, or pass the ID of the parent menu
			'id' => 'sbm_view_pending_invoices', // link ID, defaults to a sanitized title value
			'title' => __('Show Pending Invoice List'), // link title
			'href' => admin_url( 'admin.php?page=sbm_get_pending_invoices_list'), // name of file
			'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
		));
		$wp_admin_bar->add_menu( array(
			'parent' => 'sbm_view_invoices', // use 'false' for a root menu, or pass the ID of the parent menu
			'id' => 'sbm_view_unpaid_invoices', // link ID, defaults to a sanitized title value
			'title' => __('Show Unpaid Invoice List'), // link title
			'href' => admin_url( 'admin.php?page=sbm_get_unpaid_invoices_list'), // name of file
			'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
		));
		$wp_admin_bar->add_menu( array(
			'parent' => 'sbm_view_invoices', // use 'false' for a root menu, or pass the ID of the parent menu
			'id' => 'sbm_view_paid_invoices', // link ID, defaults to a sanitized title value
			'title' => __('Show Paid Invoice List'), // link title
			'href' => admin_url( 'admin.php?page=sbm_get_paid_invoices_list'), // name of file
			'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
		));



	}


	function sbm_clear_all_tables()
	{
		global $wpdb;

		//This removes the tables created during setup
		$sql = "DROP TABLE
						".$wpdb->prefix."sbm_letter,
						".$wpdb->prefix."sbm_letter_content,
						".$wpdb->prefix."sbm_customer_meta,
						".$wpdb->prefix."sbm_customer_payments,
						".$wpdb->prefix."sbm_sent_letter,
						".$wpdb->prefix."sbm_expenses,
						".$wpdb->prefix."sbm_deposits,
						".$wpdb->prefix."sbm_invoice,
						".$wpdb->prefix."sbm_invoice_data,
						".$wpdb->prefix."sbm_customer,
						".$wpdb->prefix."sbm_meta,
						".$wpdb->prefix."sbm_customer_account,
						".$wpdb->prefix."sbm_transaction,
						".$wpdb->prefix."sbm_payee_payer,
						".$wpdb->prefix."sbm_payee_payer_meta,
						".$wpdb->prefix."sbm_project,
						".$wpdb->prefix."sbm_project_attribute,
						".$wpdb->prefix."sbm_project_details,
						".$wpdb->prefix."sbm_odometer,
						".$wpdb->prefix."sbm_odometer_meta,
						".$wpdb->prefix."sbm_user_account";
		$result = $wpdb->query($sql);
		if($result)
		{
			echo '<div>All tables dropped</div>';
		}


			// remove all entries from the options table
			$sql = "DELETE FROM " . $wpdb->prefix . "options WHERE option_value LIKE 'sbm%'  OR  option_value LIKE '%simple-business-manager%' ";
			$wpdb->query($sql);

			$c = $wpdb->print_error();
			if($c)
			{
				echo '<div>All options that were created are now removed.</div>';
			}


			// remove the column from the users table
			$sql ="ALTER TABLE ".$wpdb->prefix."users DROP parent_user_id";
			$d = $wpdb->query($sql);
			if($d)
			{
				echo '<div>The main user table has been modified to remove any modifications created during setup.</div>';
			}

				 	delete_option("sbm_database_version");
				 	delete_option("sbm_version");
					delete_option("multiple_user_status");
					// removes the settings link in the plugin section

		if( sbm_uninstall() == true )
		{

			echo '<h2><a href="plugins.php">Please click here, to deactivate the plugin!</a></h2>';
		}
		else
		{
			echo '<h2>There was a problem uninstalling</h2>';
		}
		//This  exit(); is needed otherwise you geta a 0 at the end of the string/returned value
		exit();
	}


   function sbm_upgrade_database( $sbm_database_version )
   {

		global $wpdb;

        $latest_database_version = $_SESSION['sbm_database_version'];

			 	switch( $sbm_database_version )
				{
						case 'new': // current installed version, in this case its a new install
							// Set our latest database version number
							$next_version = $latest_database_version;

							// Create a new table - customer **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_customer'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_customer')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_customer` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 = not visible, 1 = visible',
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);

						   }


							// Create a new table - customer meta **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_customer_meta'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_customer_meta')
						   {
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_customer_meta` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `customer_id` int(11) DEFAULT NULL,
										  `meta_key` varchar(255) NOT NULL,
										  `meta_value` longtext NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);
						   }


							// Create a new table - customer payments **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_customer_payments'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_customer_payments')
						   {
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_customer_payments` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `transaction_id` int(11) NOT NULL,
										  `customer_account_id` int(11) NOT NULL,
										  `customer_id` int(11) NOT NULL,
										  `invoice_id` int(11) NOT NULL,
										  `amount_paid` decimal(12,2) NOT NULL,
										  `payment_date` decimal(12,0) NOT NULL,
										  `description` text NOT NULL,
										  `check_number` varchar(30) NOT NULL,
										  `paid_with` tinyint(1) NOT NULL COMMENT '0 = cash, 1 = check',
										  `bounced` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=no 1=yes',
										  `bounced_date` decimal(12,0) DEFAULT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);
						   }

							// Create a new table - deposits **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_deposits'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_deposits')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_deposits` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `transaction_id` int(11) NOT NULL,
										  `transaction_date` decimal(12,0) DEFAULT NULL,
										  `deposit_type_id` int(11) DEFAULT NULL,
										  `payee_payer_id` int(11) DEFAULT NULL,
										  `transaction_type_id` int(11) DEFAULT NULL,
										  `amount` decimal(12,2) NOT NULL,
										  `description` text,
										  `check_number` varchar(30) NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
							  $wpdb->query($sql);

						   }

							// Create a new table - expenses **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_expenses'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_expenses')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_expenses` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `transaction_id` int(11) NOT NULL,
										  `transaction_date` decimal(12,0) DEFAULT NULL,
										  `expense_type_id` int(11) DEFAULT NULL,
										  `payee_payer_id` int(11) DEFAULT NULL,
										  `transaction_type_id` int(11) DEFAULT NULL,
										  `amount` decimal(12,2) NOT NULL,
										  `description` text,
										  `check_number` varchar(30) NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
							  $wpdb->query($sql);

						   }

							// Create a new table - invoice **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_invoice'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_invoice')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_invoice` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `customer_id` int(11) NOT NULL,
										  `tax_rate` decimal(10,2) NOT NULL,
										  `invoice_date` decimal(12,0) NOT NULL,
										  `hourly_rate` decimal(10,2) NOT NULL,
										  `paid_down` decimal(10,2) NOT NULL,
										  `invoice_status` varchar(9) NOT NULL COMMENT 'pending , invoiced, paid, cancelled',
										  `invoice_type` varchar(20) NOT NULL,
										  `purchase_order` VARCHAR( 100 ) NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);
						   }

							// Create a new table - invoice data **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_invoice_data'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_invoice_data')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_invoice_data` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `invoice_id` int(11) NOT NULL,
										  `meta_key` varchar(255) NOT NULL,
										  `meta_value` longtext NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);
						   }

							// Create a new table - letter **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_letter'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_letter')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_letter` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=no, 1=yes',
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);
						   }



						   // Create a new table - letter content **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_letter_content'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_letter_content')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_letter_content` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `letter_id` int(11) NOT NULL,
										  `title` varchar(100) NOT NULL,
										  `content` longtext NOT NULL,
										  `version` int(11) NOT NULL,
										  `modified_date` decimal(12,0) DEFAULT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							 $wpdb->query($sql);
						   }


							// Create a new table - meta **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_meta'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_meta')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_meta` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `meta_key` varchar(255) NOT NULL,
										  `meta_value` longtext NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);


							$list 		= array();
							// Deposit default / setup values
							$list[] 	= "deposit";
							$list[] 	= "misc credit";
							$list[] 	= "misc debit";
							$list[] 	= "opening account balance";
							$meta_key 	= 'customer_type';

							foreach($list as $meta_value)
							{
								$wpdb->insert(  $wpdb->prefix."sbm_meta", array(  'meta_key' => $meta_key, 'meta_value' => $meta_value ), array(  '%s', '%s' ) );
								$id = $wpdb->insert_id;
								if(empty($id))
								{
									die( 'No ID customer type = ' . $meta_value );
								}
							}

							$list 		= array();
							// Deposit default / setup values
							$list[] 	= "Deposit Received";
							$list[] 	= "Other";
							$meta_key 	= 'deposit_type';

							foreach($list as $meta_value)
							{
								$wpdb->insert(  $wpdb->prefix."sbm_meta", array(  'meta_key' => $meta_key, 'meta_value' => $meta_value ), array(  '%s', '%s' ) );
								$id = $wpdb->insert_id;
								if(empty($id))
								{
									die( 'No ID deposit type = ' . $meta_value );
								}
							}

							$list 		= array();
							// Transaction default / setup values
							$list[] 	= "ATM";
							$list[] 	= "Check";
							$list[] 	= "Debit";
							$list[] 	= "Deposit";
							$list[] 	= "Transfer";
							$meta_key 	= 'transaction_type';

							foreach($list as $meta_value)
							{
								$wpdb->insert(  $wpdb->prefix."sbm_meta", array(  'meta_key' => $meta_key, 'meta_value' => $meta_value ), array(  '%s', '%s' ) );
								$id = $wpdb->insert_id;
								if(empty($id))
								{
									die( 'No ID transaction type = ' . $meta_value );
								}
							}

							$list = array();
							// Expense default / setup values
							$list[] 	= "Advertising";
							$list[] 	= "Auto and Travel";
							$list[] 	= "Capitol";
							$list[] 	= "Cleaning and maintenance";
							$list[] 	= "Commissions";
							$list[] 	= "Insurance";
							$list[] 	= "Legal and other professional services";
							$list[] 	= "Interest paid";
							$list[] 	= "Repairs";
							$list[] 	= "Purchased Services";
							$list[] 	= "Personnel";
							$list[] 	= "Supplies";
							$list[] 	= "Taxes";
							$list[] 	= "Utilities";
							$list[] 	= "Management Fees";
							$meta_key 	= 'expense_type';

							foreach($list as $meta_value)
							{
								$wpdb->insert(  $wpdb->prefix."sbm_meta", array(  'meta_key' => $meta_key, 'meta_value' => $meta_value ), array(  '%s', '%s' ) );
								$id = $wpdb->insert_id;
								if(empty($id))
								{
									die( 'No ID expense = ' . $meta_value );
								}
							}


						   }


							// Create a new table - odometer **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_odometer'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_odometer')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_odometer` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 = not visible, 1 = visible',
									  `odometer_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
									  `payee_payer_id` INT( 11 ) NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);

						   }


							// Create a new table - odometer meta **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_odometer_meta'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_odometer_meta')
						   {
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_odometer_meta` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `odometer_id` int(11) DEFAULT NULL,
										  `meta_key` varchar(255) NOT NULL,
										  `meta_value` longtext NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);
						   }

							// Create a new table - payee_payer **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_payee_payer'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_payee_payer')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_payee_payer` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 = not visible, 1 = visible',
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);

						   }


							// Create a new table - payee_payer meta **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_payee_payer_meta'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_payee_payer_meta')
						   {
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_payee_payer_meta` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `payee_payer_id` int(11) DEFAULT NULL,
										  `meta_key` varchar(255) NOT NULL,
										  `meta_value` longtext NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);
						   }

							// Create a new table - project **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_project'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_project')
						   {
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_project` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `parent_project_id` int(11) NULL,
										  `date_submitted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
										  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);
						   }
							// Create a new table - project_attribute **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_project_attribute'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_project_attribute')
						   {
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_project_attribute` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `value` varchar(255) NOT NULL DEFAULT '',
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);

                              $list = array();
                              // Project default / setup values
                              $list[] 	= "name";
                              $list[] 	= "category";
                              $list[] 	= "status";
                              $list[] 	= "reporter";
                              $list[] 	= "assigned to";
                              $list[] 	= "priority";
                              $list[] 	= "summary";
                              $list[] 	= "description";
                              $list[] 	= "customer id";
                              $list[] 	= "change";

                              foreach($list as $value)
                              {
                                  $wpdb->insert(  $wpdb->prefix."sbm_project_attribute", array(  'value' => $value ), array( '%s' ) );

                              }

						   }

							// Create a new table - project_status_options **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_project_status_options'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_project_status_options')
						   {
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_project_status_options` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `value` varchar(255) NOT NULL DEFAULT '',
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);

                              $list = array();
                              // Project default / setup values
                              $list[] 	= "new";
                              $list[] 	= "unassigned";
                              $list[] 	= "assigned";
                              $list[] 	= "acknowledged";
                              $list[] 	= "feedback";
                              $list[] 	= "confirm";
                              $list[] 	= "resolved";
                              $list[] 	= "closed";

                              foreach($list as $value)
                              {
                                  $wpdb->insert(  $wpdb->prefix."sbm_project_status_options", array(  'value' => $value ), array( '%s' ) );

                              }

						   }

							// Create a new table - project_details **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_project_details'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_project_details')
						   {
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_project_details` (
								          `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `project_id` int(11) NOT NULL,
										  `attribute_id` int(11) NOT NULL,
										  `value` varchar(255) NOT NULL DEFAULT '',
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);
						   }

							// Create a new table - sent letter **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_sent_letter'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_sent_letter')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_sent_letter` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `letter_id` int(11) NOT NULL,
										  `letter_content_id` int(11) NOT NULL,
										  `customer_id` int(11) NOT NULL,
										  `sent_date` decimal(12,0) NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;
							   $wpdb->query($sql);

						   }


							// Create a new table - transaction **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_transaction'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_transaction')
						   {
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_transaction` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `transaction_date` decimal(12,0) NOT NULL,
										  `reconcile_id` int(11) NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;
							   $wpdb->query($sql);

						   }


							// Create some default values for the company
							update_option('sbm_company_name', 'Please set your Company Name');
							update_option('sbm_address', '1234 Main Street');
							update_option('sbm_city', 'Omaha');
							update_option('sbm_state', 'NE');
							update_option('sbm_zip', '68164');
							update_option('sbm_default_hourly_rate', '40');
							update_option('sbm_default_tax_rate', '7.0');
							update_option('sbm_terms', 'Terms: 25% downpayment, NET on completion.  Payment as work progresses.');

							// now update the database version
							update_option("sbm_database_version", $next_version);
							// run it again to see if we need to continue move the
							sbm_upgrade_database($next_version);
						break;
						case '1.0':
						case '1.1':
						case '1.2':
						case '1.3':
						case '1.4':
						case '1.5':
						case '1.6':
						case '1.7':
						case '1.8':
						case '1.9': // current installed version that we are checking and upgrading from
							// Set our next database version number
							$next_version 	= $latest_database_version;
							$errors 		= array();


							$sql = "DROP TABLE `".$wpdb->prefix."sbm_customer`";
							$wpdb->query($sql);


							$sql =	"CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_customer` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 = not visible, 1 = visible',
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);


							$sql = "DROP TABLE `".$wpdb->prefix."sbm_customer_meta`";
							$wpdb->query($sql);

							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_customer_meta` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `customer_id` int(11) DEFAULT NULL,
									  `meta_key` varchar(255) NOT NULL,
									  `meta_value` longtext NOT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);

							$sql = "DROP TABLE `".$wpdb->prefix."sbm_customer_payments`";
							$wpdb->query($sql);

							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_customer_payments` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `transaction_id` int(11) NOT NULL,
									  `customer_account_id` int(11) NOT NULL,
									  `customer_id` int(11) NOT NULL,
									  `invoice_id` int(11) NOT NULL,
									  `amount_paid` decimal(12,2) NOT NULL,
									  `payment_date` decimal(12,0) NOT NULL,
									  `description` text NOT NULL,
									  `check_number` varchar(30) NOT NULL,
									  `paid_with` tinyint(1) NOT NULL COMMENT '0 = cash, 1 = check',
									  `bounced` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=no 1=yes',
									  `bounced_date` decimal(12,0) DEFAULT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);

							$sql = "DROP TABLE `".$wpdb->prefix."sbm_expenses`";
							$wpdb->query($sql);

							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_expenses` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `transaction_id` int(11) NOT NULL,
									  `transaction_date` decimal(12,0) DEFAULT NULL,
									  `expense_type_id` int(11) DEFAULT NULL,
									  `payee_payer_id` int(11) DEFAULT NULL,
									  `transaction_type_id` int(11) DEFAULT NULL,
									  `amount` decimal(12,2) NOT NULL,
									  `description` text,
									  `check_number` varchar(30) NOT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);

							$sql = "DROP TABLE `".$wpdb->prefix."sbm_invoice`";
							$wpdb->query($sql);

							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_invoice` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `customer_id` int(11) NOT NULL,
									  `tax_rate` decimal(10,2) NOT NULL,
									  `invoice_date` decimal(12,0) NOT NULL,
									  `hourly_rate` decimal(10,2) NOT NULL,
									  `paid_down` decimal(10,2) NOT NULL,
									  `invoice_status` varchar(9) NOT NULL COMMENT 'pending , invoiced, paid, cancelled',
									  `invoice_type` varchar(20) NOT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);
							$sql = "DROP TABLE `".$wpdb->prefix."sbm_invoice_data`";
							$wpdb->query($sql);

							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_invoice_data` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `invoice_id` int(11) NOT NULL,
									  `meta_key` varchar(255) NOT NULL,
									  `meta_value` longtext NOT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);
							$sql = "DROP TABLE `".$wpdb->prefix."sbm_letter`";
							$wpdb->query($sql);

							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_letter` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=no, 1=yes',
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);
							$sql = "DROP TABLE `".$wpdb->prefix."sbm_letter_content`";

							$wpdb->query($sql);
							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_letter_content` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `letter_id` int(11) NOT NULL,
									  `title` varchar(100) NOT NULL,
									  `content` longtext NOT NULL,
									  `version` int(11) NOT NULL,
									  `modified_date` decimal(12,0) DEFAULT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);

							$sql = "DROP TABLE `".$wpdb->prefix."sbm_meta`";
							$wpdb->query($sql);

							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_meta` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `meta_key` varchar(255) NOT NULL,
									  `meta_value` longtext NOT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

							$wpdb->query($sql);

							$sql = "DROP TABLE `".$wpdb->prefix."sbm_sent_letter`";
							$wpdb->query($sql);

							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_sent_letter` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `letter_id` int(11) NOT NULL,
									  `letter_content_id` int(11) NOT NULL,
									  `customer_id` int(11) NOT NULL,
									  `sent_date` decimal(12,0) NOT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);
							$sql = "DROP TABLE `".$wpdb->prefix."sbm_transaction`";

							$wpdb->query($sql);
							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_transaction` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `transaction_date` decimal(12,0) NOT NULL,
									  `reconcile_id` int(11) NOT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);
							$sql = "ALTER TABLE
										`".$wpdb->prefix."users`
									DROP
										`parent_user_id` ";

							$wpdb->query($sql);// check for errors

								// now update the
								update_option("sbm_database_version", $next_version);
								$_SESSION['installed_database_version'] = $next_version;
								// run it again to see if we need to continue move the
								sbm_upgrade_database($next_version);


						break;
						case '2.0': // current installed version that we are checking and upgrading from
						$next_version = '2.1';

							$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_deposits` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `transaction_id` int(11) NOT NULL,
									  `transaction_date` decimal(12,0) DEFAULT NULL,
									  `deposit_type_id` int(11) DEFAULT NULL,
									  `payee_payer_id` int(11) DEFAULT NULL,
									  `transaction_type_id` int(11) DEFAULT NULL,
									  `amount` decimal(12,2) NOT NULL,
									  `description` text,
									  `check_number` varchar(30) NOT NULL,
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							$wpdb->query($sql);

							// Create a new table - customer **
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_payee_payer` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 = not visible, 1 = visible',
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);

							// Create a new table - customer meta **
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_payee_payer_meta` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `payee_payer_id` int(11) DEFAULT NULL,
										  `meta_key` varchar(255) NOT NULL,
										  `meta_value` longtext NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);


								// now update the
								update_option("sbm_database_version", $next_version);
								$_SESSION['installed_database_version'] = $next_version;
								// run it again to see if we need to continue move the
								sbm_upgrade_database($next_version);
						break;
						case '2.1': // current installed version that we are checking and upgrading from
						$next_version = '2.2';

							// Create a new table - odometer **
							  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_odometer` (
									  `ID` int(11) NOT NULL AUTO_INCREMENT,
									  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 = not visible, 1 = visible',
									  PRIMARY KEY (`ID`)
									) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);


							// Create a new table - odometer meta **
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_odometer_meta` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `odometer_id` int(11) DEFAULT NULL,
										  `meta_key` varchar(255) NOT NULL,
										  `meta_value` longtext NOT NULL,
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);

								// now update the
								update_option("sbm_database_version", $next_version);
								$_SESSION['installed_database_version'] = $next_version;
								// run it again to see if we need to continue move the
								sbm_upgrade_database($next_version);
						break;
						case '2.2': // current installed version that we are checking and upgrading from

						$next_version = '2.3';

							// Create a new table - project **
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_project` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `parent_project_id` int(11) NULL,
										  `date_submitted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
										  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

							  $wpdb->query($sql);
							// Create a new table - project attribute **
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_project_attribute` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `value` varchar(255) NOT NULL DEFAULT '',
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);
							// Create a new table - project details **
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_project_details` (
								          `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `project_id` int(11) NOT NULL,
										  `attribute_id` int(11) NOT NULL,
										  `value` varchar(255) NOT NULL DEFAULT '',
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);

                              $list = array();
                              // Project default / setup values
                              $list[] 	= "name";
                              $list[] 	= "category";
                              $list[] 	= "status";
                              $list[] 	= "reporter";
                              $list[] 	= "assigned to";
                              $list[] 	= "priority";
                              $list[] 	= "summary";
                              $list[] 	= "description";
                              $list[] 	= "customer id";
                              $list[] 	= "change";

                              foreach($list as $value)
                              {
                                  $wpdb->insert(  $wpdb->prefix."sbm_project_attribute", array(  'value' => $value ), array( '%s' ) );

                              }
							// Create a new table - project_status_options **
							$query = "SHOW TABLES LIKE '".$wpdb->prefix."sbm_project_status_options'";
							if($wpdb->get_var($query) != $wpdb->prefix.'sbm_project_status_options')
						   {
								$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sbm_project_status_options` (
										  `ID` int(11) NOT NULL AUTO_INCREMENT,
										  `value` varchar(255) NOT NULL DEFAULT '',
										  PRIMARY KEY (`ID`)
										) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

							  $wpdb->query($sql);

                              $list = array();
                              // Project default / setup values
                              $list[] 	= "new";
                              $list[] 	= "unassigned";
                              $list[] 	= "assigned";
                              $list[] 	= "acknowledged";
                              $list[] 	= "feedback";
                              $list[] 	= "confirm";
                              $list[] 	= "resolved";
                              $list[] 	= "closed";

                              foreach($list as $value)
                              {
                                  $wpdb->insert(  $wpdb->prefix."sbm_project_status_options", array(  'value' => $value ), array( '%s' ) );

                              }

						   }

								// now update the
								update_option("sbm_database_version", $next_version);
								$_SESSION['installed_database_version'] = $next_version;
								// run it again to see if we need to continue move the
								sbm_upgrade_database($next_version);
						break;
                        case '2.3': // current installed version that we are checking and upgrading from
                        $next_version = '2.4';

                                $query = "ALTER TABLE `" . $wpdb->prefix . "sbm_odometer` ADD  `odometer_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER  `visible`";
								$wpdb->query($query);// check for errors

                            // Loop through all existing entries and fix old records

                                $query = "SELECT
                                            GROUP_CONCAT( meta_key, '|', meta_value ) as odometer_data,
                                                odometer_id
                                            FROM
                                                " . $wpdb->prefix . "sbm_odometer,
                                                " . $wpdb->prefix . "sbm_odometer_meta
                                            WHERE
                                                " . $wpdb->prefix . "sbm_odometer.ID = " . $wpdb->prefix . "sbm_odometer_meta.odometer_id
                                            AND
                                                visible = 1
                                            GROUP BY
                                                odometer_id";

                                $odometer_list 	= $wpdb->get_results($query);

                                $result = array();


                                        // If the odometer ID was not used as a display option use this
                                        foreach( $odometer_list as $list )
                                        {
                                            // Break up the comma delimited
                                            $explode1 = explode( ',', $list->odometer_data );

                                            foreach( $explode1 as $meta_content )
                                            {
                                                // now explode each result to seperate the information that is pipe delimited
                                                $explode2 = explode('|', $meta_content );

                                                $result[ $list->odometer_id ][ $explode2[0] ] 	= $explode2[1];

                                            }


                                                 $query = "UPDATE ".$wpdb->prefix."sbm_odometer SET odometer_date = '" . date( "Y-m-d H:i:s", strtotime( $result[ $list->odometer_id ][ 'trip_date' ]) ) . "' WHERE ID = " . $list->odometer_id;
                                                 $wpdb->query($query);
                                        }



								// now update the
								update_option("sbm_database_version", $next_version);
								$_SESSION['installed_database_version'] = $next_version;
								// run it again to see if we need to continue move the
								sbm_upgrade_database($next_version);

                        break;
						case '2.4': // current installed version that we are checking and upgrading from
						$next_version = '2.5';

                                $query = "ALTER TABLE `".$wpdb->prefix."sbm_invoice` ADD `purchase_order` VARCHAR( 100 ) NOT NULL";
                                $wpdb->query($query);
										// now update the
								update_option("sbm_database_version", $next_version);
								$_SESSION['installed_database_version'] = $next_version;
								// run it again to see if we need to continue move the
								sbm_upgrade_database($next_version);
						break;
						case '2.5': // current installed version that we are checking and upgrading from
						$next_version = '2.6'; // If this is the last one, this needs to match whatever our current DB version is

                        $query = "ALTER TABLE `".$wpdb->prefix."sbm_odometer` ADD `payee_payer_id` INT( 11 ) NULL";
                        $wpdb->query($query);
								// now update the
								update_option("sbm_database_version", $next_version);
								$_SESSION['installed_database_version'] = $next_version;
								// run it again to see if we need to continue move the
								sbm_upgrade_database($next_version);
						break;

						/*

							commented out for future use this is used to upgrade the database if needed

						case '2.6':
						$next_version = '2.7'; // If this is the last one, this needs to match whatever our current DB version is

								// now update the
								update_option("sbm_database_version", $next_version);
								$_SESSION['installed_database_version'] = $next_version;
								// run it again to see if we need to continue move the
								sbm_upgrade_database($next_version);
						break;
						*/
						default;


							// This is either a new install OR the last loop through
							if(empty($sbm_database_version))
							{
								$sbm_database_version = $latest_database_version;
							}
							update_option("sbm_database_version", $sbm_database_version);
							$_SESSION['installed_database_version'] = $sbm_database_version;
						break;
				}

   }


   function sbm_update_meta()
   {
	   global $wpdb;

				// check to see if we updated the ID in wp_meta
	 			// if the column does not exist, alter the table to create it.
				$query = "SHOW COLUMNS FROM ".$wpdb->prefix."sbm_meta";
				$row = $wpdb->get_results($query);
				$match = array();

				foreach($row as $list)
				{
					// itterate through the results and see if the resulting object has the value we are looking for
					if($list->Field == $wpdb->prefix.'meta_id')
					{
						$match[] = 1;
					}
				}

				if(!empty($match))
				{

					// This will make the new field in the users table because we proved it did not exist
					$sql = "ALTER TABLE ".$wpdb->prefix."sbm_meta CHANGE ".$wpdb->prefix."meta_id ID INT( 11 ) NOT NULL AUTO_INCREMENT  ";
					$wpdb->query($sql);

				}

   }

function sbm_remove_data()
{
        echo '<div class="wrap">';


        echo '<br><br><div id="remove_message6"><div>If you need to uninstall this plugin, the database tables and options that were created durning setup will NOT be deleted.</div><div>You can remove all the old settings and any database tables that were created by clicking this <a href="javascript: void(0);" id="sbmClearAllTables">link</a>.</div></div>';
        echo '<div id="message"></div>';

		echo '<br><br><br><br><br><br><br><br><br><br><br>';

        echo '</div>';

}

?>