<?php

function sbm_install() 
{
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 
	$sbm_database_version = $_SESSION['installed_database_version'];
	
	
	if (empty( $_SESSION['installed_database_version'] ))
	{
		$sbm_database_version = 'new';
	}

	// run through the upgrade to see if we need any updates
	//  admin_functions.php:     sbm_upgrade_database()
	sbm_upgrade_database( $sbm_database_version );
	
	// Update the current version
	update_option("sbm_version", $_SESSION['sbm_version']);
						
	// Update the multiple_user_status
	update_option("sbm_multiple_user_status", $_SESSION['multiple_user_status']);
	
	 //  admin_functions.php:     sbm_update_meta()
	//sbm_update_meta();
				
}

	
?>