<?php
/*
Plugin Name: simple-business-manager
Plugin URI: http://www.russellalbin.com/wordpress-plugins/wordpress-plugin-simple-business-manager/
Description: Simple Business Manager allows you to manage your company, track invoices and keep finances in order. Letter generator allows you to create letter templates and then send them to your customers. A Company report allows you to quickly see your company finances, expenses and deposits as well as the ability to track miles traveled
Version: 4.6.7.4
Author: Russell Albin
Author URI: http://www.russellalbin.com
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define('SBM_PLUGIN_URL', get_option('siteurl') . '/wp-content/plugins/simple-business-manager');
define('SBM_SITE_LOGIN', get_option('siteurl') . '/wp-login.php');

$_SESSION['sbm_version']										= '4.6.7.4'; // this has to match up with the Plugin Data above
$_SESSION['sbm_database_version']								= '2.6';
$_SESSION['multiple_user_status']								= 'true';
$_SESSION['installed_version'] 									= get_option( "sbm_version" );
$_SESSION['installed_database_version'] 						= get_option( "sbm_database_version" );


// Functions
require_once(dirname(__FILE__) . '/includes/admin_functions.php');
require_once(dirname(__FILE__) . '/includes/admin_menu.php');
require_once(dirname(__FILE__) . '/includes/add_action.php');
require_once(dirname(__FILE__) . '/includes/add_filter.php');
require_once(dirname(__FILE__) . '/includes/add_shortcode.php');
require_once(dirname(__FILE__) . '/includes/ajax.php');
require_once(dirname(__FILE__) . '/includes/accounting_functions.php');
require_once(dirname(__FILE__) . '/includes/deposit_type_functions.php');
require_once(dirname(__FILE__) . '/includes/expense_type_functions.php');
require_once(dirname(__FILE__) . '/includes/general_functions.php');
require_once(dirname(__FILE__) . '/includes/help_functions.php');
require_once(dirname(__FILE__) . '/includes/invoice_functions.php');
require_once(dirname(__FILE__) . '/includes/letter_functions.php');
require_once(dirname(__FILE__) . '/includes/notification_functions.php');
require_once(dirname(__FILE__) . '/includes/odometer_functions.php');
require_once(dirname(__FILE__) . '/includes/payee_payer_functions.php');
require_once(dirname(__FILE__) . '/includes/project_functions.php');
require_once(dirname(__FILE__) . '/includes/customer_functions.php');
require_once(dirname(__FILE__) . '/includes/report_functions.php');
require_once(dirname(__FILE__) . '/includes/transaction_type_functions.php');
require_once(dirname(__FILE__) . '/includes/settings_functions.php');
require_once(dirname(__FILE__) . '/includes/setup.php');
require_once(dirname(__FILE__) . '/includes/uninstall.php');

// Classes
require_once(dirname(__FILE__) . '/classes/sbm_deposit_expense.php');
require_once(dirname(__FILE__) . '/classes/sbm_deposit_type.php');
require_once(dirname(__FILE__) . '/classes/sbm_expense_type.php');
require_once(dirname(__FILE__) . '/classes/sbm_invoice.php');
require_once(dirname(__FILE__) . '/classes/sbm_letter.php');
require_once(dirname(__FILE__) . '/classes/sbm_odometer.php');
require_once(dirname(__FILE__) . '/classes/sbm_payee_payer.php');
require_once(dirname(__FILE__) . '/classes/sbm_project.php');
require_once(dirname(__FILE__) . '/classes/sbm_report.php');
require_once(dirname(__FILE__) . '/classes/sbm_transaction_type.php');
require_once(dirname(__FILE__) . '/classes/sbm_customer.php');
require_once(dirname(__FILE__) . '/classes/sbm_settings.php');

// PDF
require_once(dirname(__FILE__) . '/tcpdf/config/lang/eng.php');
require_once(dirname(__FILE__) . '/tcpdf/tcpdf.php');

	$uploadDir = wp_upload_dir();
	$newDir = $uploadDir['basedir'].'/simple-business-manager';

// Setup folder to hold any PDF we create
	if(!file_exists( $newDir )){
		mkdir( $newDir, 0777 );
	}


// Activation and deactivation
register_activation_hook(__FILE__, 'sbm_install');
register_deactivation_hook(__FILE__, 'sbm_deactivation');

// If the installed database version != the current one, go ahead and run the setup
if( $_SESSION['sbm_database_version'] > $_SESSION['installed_database_version'] )
{
    sbm_install();
}

// Uninstall
if (function_exists('register_uninstall_hook'))
{
    register_uninstall_hook(__FILE__, 'sbm_uninstall');
}
// Get the timezone
sbm_get_timezone();

    // make sure that the database does not need to be updated
    if( $_SESSION['sbm_database_version'] != $_SESSION['installed_database_version'] )
    {
        sbm_upgrade_database( $_SESSION['installed_database_version'] );
    }

?>