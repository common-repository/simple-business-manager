<?php

function sbm_uninstall()
{
   global $wpdb;

				 	delete_option( 'sbm_database_version' );
                    delete_option( 'sbm_timezone' );
                    delete_option( 'sbm_terms' );
                    delete_option( 'sbm_address_2' );
                    delete_option( 'sbm_phone' );
                    delete_option( 'sbm_fax' );
                    delete_option( 'sbm_email_1' );
                    delete_option( 'sbm_email_2' );
                    delete_option( 'sbm_invoice_image' );
                    delete_option( 'sbm_invoice_image_position' );
                    delete_option( 'sbm_currency' );
				 	delete_option( 'sbm_version' );
					delete_option( 'sbm_company_name' );
					delete_option( 'sbm_address' );
					delete_option( 'sbm_city' );
					delete_option( 'sbm_state' );
					delete_option( 'sbm_zip' );
					delete_option( 'sbm_default_hourly_rate' );
					delete_option( 'sbm_default_tax_rate' );
					delete_option( 'sbm_multiple_user_status' );
					remove_filter( 'plugin_action_links', 'sbm_settings_link', 10, 1 );

					$_SESSION['sbm_version'] 					= NULL;
   					$_SESSION['sbm_database_version'] 			= NULL;
   					$_SESSION['multiple_user_status'] 			= NULL;
   					$_SESSION['installed_version'] 				= NULL;
					$_SESSION['installed_database_version'] 	= NULL;

		return true;


}

// Deactivaion will just remove some of our options.
function sbm_deactivation ()
{
   global $wpdb;
  					// We dont want to get rid of these, it is needed to keep our version so we can re-activate and just delete the tables as needed
				 	//delete_option("sbm_database_version" );
				 	//delete_option("sbm_version" );


}

?>