<?php

function sbm_check_for_complete_profile()
{
	global $current_user;
	
	// Now we need to check for our required information
	// Company Name, Address, City, State, Zip, email
	// the login name should already be set! 
	$user_info =  get_currentuserinfo($current_user->ID);
	
	
	$errors = array();
	
	if(empty($user_info->first_name))
	{
		$errors[] = true;
	}
	if(empty($user_info->last_name))
	{
		$errors[] = true;
	}
	if(empty($user_info->user_email))
	{
		$errors[] = true;
	}
	if(empty($user_info->company_name))
	{
		$errors[] = true;
	}
	if(empty($user_info->address))
	{
		$errors[] = true;
	}
	if(empty($user_info->city))
	{
		$errors[] = true;
	}
	if(empty($user_info->state))
	{
		$errors[] = true;
	}
	if(empty($user_info->zip))
	{
		$errors[] = true;
	}
	
	if(!empty($errors))
	{
		return false;
	}
	else
	{
		return true;
	}
}
 
// function to create the meta_key if it did not exist
function sbm_create_usermeta($key, $list, $id) {
    global $wpdb;

	if(sbm_does_usermeta_key_exist($key, $id) == 0 )
	{
    	$wpdb->insert($wpdb->usermeta, array('meta_key' => $key, 'meta_value' => $list), array('%s',  '%s'));
	}
}

function sbm_create_sbm_meta($key, $value, $id) {
    global $wpdb;

    $wpdb->insert($wpdb->prefix . 'sbm_meta', array('meta_key' => $key,  'meta_value' => $value), array('%s',  '%s'));
}

function sbm_update_sbm_meta($key, $value, $id) {
    global $wpdb;

    $wpdb->query("UPDATE " . $wpdb->prefix . "sbm_meta SET meta_value = '$value' WHERE meta_key = '$key'");
}
function sbm_update_usermeta($key, $value, $id) {
    global $wpdb;

    $wpdb->query("UPDATE " . $wpdb->prefix . "usermeta SET meta_value = '$value' WHERE meta_key = '$key'");
}

// function to see if a meta key exists for a user
function sbm_does_usermeta_key_exist($meta_key) {
    global $wpdb;

    $query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "usermeta WHERE meta_key = '$meta_key'";
    $count = $wpdb->get_var($wpdb->prepare($query));

    return $count;
}

// function to see if a meta key exists for a user in the sbm_meta table
function sbm_does_sbm_meta_key_exist($meta_key) {
    global $wpdb;

    $query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "sbm_meta WHERE meta_key = '$meta_key' ";
    $count = $wpdb->get_var($wpdb->prepare($query));

    return $count;
}

function sbm_get_late_payment_fee() {
    global $wpdb;

    $query = "SELECT meta_value FROM " . $wpdb->prefix . "sbm_meta WHERE meta_key = 'late_payment_fee'";
    $result = $wpdb->get_var($query);

    return $result;
}

function sbm_get_late_payment_id() {
    global $wpdb;

    $query = "SELECT ID FROM " . $wpdb->prefix . "sbm_meta WHERE meta_key = 'late_payment_fee'";
    $id = $wpdb->get_var($query);

    return $id;
}
function sbm_get_late_payment_fee_value() {
    global $wpdb;

    $query = "SELECT meta_value FROM " . $wpdb->prefix . "sbm_meta WHERE meta_key = 'late_payment_fee_value'";
    $amount = $wpdb->get_var($query);

    return $amount;
}

function sbm_get_bounced_check_fee() {
    global $wpdb;

    $query = "SELECT meta_value FROM " . $wpdb->prefix . "sbm_meta WHERE meta_key = 'bounced_check_fee' ";
    $amount = $wpdb->get_var($query);

    return $amount;
}
function sbm_get_bounced_check_id() {
    global $wpdb;

    $query = "SELECT ID FROM " . $wpdb->prefix . "sbm_meta WHERE meta_key = 'bounced_check_fee'";
    $id = $wpdb->get_var($query);

    return $id;
}

function sbm_get_multiple_user_status()
{
	$check = get_option( "multiple_user_status" );
	
	if( ( empty($check) ) || ( $check != 'true' ) )
	{
		update_option( "multiple_user_status", "false" );
	}
	
	$status = get_option( "multiple_user_status" );
	
	return $status;
}
/* this function allows users to edit a profile, not necessarily their own, but others they are the parent of */

function sbm_settings() {
	
    global $wpdb;
    global $current_user;
	$sbm_currency = get_option( 'sbm_currency' );
	$sbm_timezone = get_option( 'sbm_timezone' );
	if(empty($sbm_timezone))
	{
		$sbm_timezone = 'America/Chicago';		
	}
	    
    get_currentuserinfo($current_user->ID);
	
    echo '<div class="wrap">';


    if (isset($_POST['sbm_company_name'])) {
		
	
        $errors = array();


	   
			if (empty($_POST['sbm_company_name'])) 
			{
				$errors[] = 'You forgot the company name';
			}
			if (empty($_POST['sbm_address'])) 
			{
				$errors[] = 'You forgot the company address';
			}
			if (empty($_POST['sbm_city'])) 
			{
				$errors[] = 'You forgot the company city';
			}
			if (empty($_POST['sbm_state'])) 
			{
				$errors[] = 'You forgot the company state';
			}
			if (empty($_POST['sbm_zip'])) 
			{
				$errors[] = 'You forgot the company zip code';
			}
			if (empty($_POST['sbm_default_hourly_rate'])) 
			{
				$errors[] = 'You forgot the default hourly rate';
			}
			if (empty($_POST['sbm_default_tax_rate'])) 
			{
				$errors[] = 'You forgot the default tax rate';
			}
		
        
        if (empty($errors)) 
		{
			sbm_settings::sbm_update_settings();
			die();		
        } 
		else 
		{
            echo '<div class="error">';
            echo '<div style="font-weight: bold;">Errors found</div>';
            foreach ($errors as $list) {
                echo '<div class="errorDiv">&nbsp;&nbsp;' . $list . '</div>';
            }
            echo '</div>';
        }
    }

        $wp_user_level 	= $wpdb->prefix . 'user_level';
        $current_level 	= $current_user->$wp_user_level;


        if ( $current_user->user_level >= 7 ) 
		{
			$current_sbm_version = get_option( '_site_transient_update_plugins' );
			$sbm_version = $current_sbm_version->checked['simple-business-manager/simple-business-manager.php'];
			
			?>
               <div class="float-right"><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="DCPB9JYHV8XU6">
                    <input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" width="1" height="1">
                    </form>	
               </div>	

            <h3>Simple Business Manager version <?php echo $sbm_version; ?> Database Version <?php echo get_option( 'sbm_database_version' ); ?></h3>
            <h3>Your Company Information</h3>
            <div>&nbsp;</div>
        	<form method="post" id="editCompanyForm">
			<table id="editCompanyTable">
				<tr>
					<td class="company-settings-description">            
                		Company Name <em>*</em>
                	</td>
                	<td class="company-settings-value">
                		<input type="text" class="required" name="sbm_company_name" id="sbm_company_name" size="40" value="<?php echo sbm_sticky_input($_POST['sbm_company_name'], get_option('sbm_company_name')); ?>">
            		</td>
            	</tr>
            
           		<tr>
					<td class="company-settings-description">Address<em>*</em>
					</td>
					<td class="company-settings-value">
                		<input type="text" class="required" id="sbm_address" name="sbm_address" size="40" value="<?php echo sbm_sticky_input($_POST['sbm_address'], get_option('sbm_address')); ?>">
            		</td>
            	</tr>
            	<tr>
            		<td class="company-settings-description">Address 2</td>
                
                	<td class="company-settings-value">
                		<input type="text" class="" id="sbm_address_2" name="sbm_address_2" size="40" value="<?php echo sbm_sticky_input($_POST['sbm_address_2'], get_option('sbm_address_2')); ?>">
            		</td>
            	</tr>
            	<tr>
            		<td class="company-settings-description">City<em>*</em></td>
            		<td class="company-settings-value">
                		<input type="text" id="sbm_city" class="required" name="sbm_city" size="40" value="<?php echo sbm_sticky_input($_POST['sbm_city'], get_option('sbm_city')); ?>">
            		</td>
            	</tr>
            <tr>
            		<td class="company-settings-description">State/Province<em>*</em></td>
            		<td class="company-settings-value">
               			<input type="text" id="sbm_state" class="required" name="sbm_state" size="40" maxlength="50" value="<?php echo sbm_sticky_input($_POST['sbm_state'], get_option('sbm_state')); ?>">
           			</td>
           	</tr>
           	<tr>
           		<tr>
            		<td class="company-settings-description">Zip<em>*</em></td>
            		<td class="company-settings-value">
                		<input type="text" id="sbm_zip" class="required" name="sbm_zip" size="8" maxlength="15" value="<?php echo sbm_sticky_input($_POST['sbm_zip'], get_option('sbm_zip')); ?>">
           			</td>
           		</tr>
           		<tr>
           	 		<td class="company-settings-description">Phone<em>*</em></td>
           	 		<td class="company-settings-value">
               		 	<input type="text" id="sbm_phone" class="required" name="sbm_phone" size="12" maxlength="20" value="<?php echo sbm_sticky_input($_POST['sbm_phone'], get_option('sbm_phone')); ?>">
            		</td>
            	</tr>
            	<tr>
            		<td class="company-settings-description">Fax</td>
                	<td class="company-settings-value">
                		<input type="text" id="sbm_fax" class="" name="sbm_fax" size="12" maxlength="20" value="<?php echo sbm_sticky_input($_POST['sbm_fax'], get_option('sbm_fax')); ?>">
            		</td>
            	</tr>
            	<tr>
            		<td class="company-settings-description">Email #1<em>*</em></td>
            		<td class="company-settings-value">
                		<input type="text" id="sbm_email_1" class="required" name="sbm_email_1" size="40" maxlength="60" value="<?php echo sbm_sticky_input($_POST['sbm_email_1'], get_option('sbm_email_1')); ?>">
           			</td>
           		</tr>
           		<tr>
           			<td class="company-settings-description">Email #2</td>
           			<td class="company-settings-value">
						<input type="text" id="sbm_email_2" class="" name="sbm_email_2" size="40" maxlength="60" value="<?php echo sbm_sticky_input($_POST['sbm_email_2'], get_option('sbm_email_2')); ?>">
            		</td>
            	</tr>
            	<tr>
            		<td colspan="2" class="settings-wide">URL for invoice/letter header image
            		
                		<input type="text" id="sbm_invoice_image" class="" name="sbm_invoice_image" size="80" value="<?php echo sbm_sticky_input($_POST['sbm_invoice_image'], get_option('sbm_invoice_image')); ?>">
            			<div>( max 89px height and 570px wide ) This will be 100% on the PDF that is created but on invoices, its about 70% of the total area</div>
           			</td>
           		</tr>
           		<tr>
           			<td colspan="2" class="settings-wide">
            <?php 
            	// If its not empty show it here
            	$sbm_image = get_option('sbm_invoice_image');
            	if(!empty($sbm_image))
            	{
            		$sbm_invoice_image_data = sbm_get_invoice_image_info();
            		
            		?>
            					
            				<?php
            		
            		if( ( $sbm_invoice_image_data[1] > 900 ) || ( $sbm_invoice_image_data[2] > 89 ) )
            		{
            			?>
            				<h2>The image you chose for your invoices is too large</h2>
            			<?php 	
            		}
            		switch($sbm_invoice_image_data[3])
            		{
            			case '1';
            			case '2';
            			case '3';
            			
            				?>
            					<div>Your Image is W: <?php echo $sbm_invoice_image_data[1]; ?> H: <?php echo $sbm_invoice_image_data[2]; ?></div>
            					
            					<div><img src="<?php echo $sbm_invoice_image_data[0]; ?>" <?php echo $sbm_invoice_image_data[4]; ?>></div>
            				<?php
            			break;
            			default;
							echo '<h2>You chose something that is not a valid image type png, gif, jpg only</h2>';
            			break;
            		}
            		
            	}
            ?>
            		</td>
            	</tr>
            	<tr>
            		<td class="company-settings-description">Invoice image position</td>
            		<td class="company-settings-value">
		                <select name="sbm_invoice_image_position">
		                <?php
							switch( get_option( 'sbm_invoice_image_position' ) )
							{
								case 'left-text';
									$left_text = 'selected = "selected"';
								break;
								case 'center-text';
									$center_text = 'selected="selected"';
								break;
								case 'right-text';
									$right_text = 'selected="selected"';
								break;
								default;
									$center_text = 'selected="selected"';
								break;
							}
						?>
		                	<option <?php echo $left_text; ?> value="left-text">Left</option>
		                    <option <?php echo $center_text; ?> value="center-text">Center</option>
		                    <option <?php echo $right_text; ?> value="right-text">Right</option>
		                 </select>
		                 *** ( Important ) The PDF header image always is left justified this is for invoices
	            	</td>
	            </tr>
	            <tr>
	            	<td class="company-settings-description">Default Hourly rate<em>*</em></td>
	            	<td class="company-settings-value">
                		<input type="text" id="sbm_default_hourly_rate" class="required number" max="9999" name="sbm_default_hourly_rate" size="4" maxlength="4" value="<?php echo sbm_sticky_input($_POST['sbm_default_hourly_rate'], get_option('sbm_default_hourly_rate')); ?>">
                		<span class="smaller_text">(e.g. 40 for <?php echo $currency_symbol; ?>40 per hour)</span> 
           			</td>
           		</tr>
           		<tr>
           			<td class="company-settings-description">Default tax rate<em>*</em></td>
           			<td class="company-settings-value">
                		<input type="text" id="sbm_default_tax_rate" class="required number" max="100" name="sbm_default_tax_rate" size="4" maxlength="5" value="<?php echo sbm_sticky_input($_POST['sbm_default_tax_rate'], get_option('sbm_default_tax_rate')); ?>">
                		<span class="smaller_text">(e.g. 7.0 for 7%)</span> 
           			</td>
           		</tr>
           		<tr>
           			<td class="company-settings-description">Invoice Terms</td>
           			<td class="company-settings-value">
                		<textarea id="sbm_terms" rows="2" cols="75" name="sbm_terms"><?php echo sbm_sticky_input($_POST['sbm_terms'], get_option('sbm_terms')); ?></textarea>
                
            		</td>
            	</tr>
            	<tr>
            		<td colspan="2" class="settings-wide">Default currency ( <?php echo $sbm_currency; ?> )
               
               		
               			<input type="text" name="sbm_currency" value="<?php echo sbm_sticky_input($_POST['sbm_currency'], htmlentities($sbm_currency) ); ?>">
                		<div>This will be used if you do not change it on each customer profile.  This will also be used on your reports.</div>
           				<blockquote class="currency-help">
           					Here are some examples.  <ul>
           									<li>If you try to use a symbol but for some reason it does not work, please use the HTML code for that currency</li>
           									<li>For a Dollar Sign ( &#36; ) you must type in &amp;#36;</li>
           									<li>For a Euro ( &euro; ) you must type in &amp;euro;</li>
           									<li>For a Pound ( &pound; ) you must type in &amp;pound;</li>
           									<li>For a South African Rand ( R ) you must type in R </li>
           									<li>For a Japanese Yen ( &yen; ) you must type in &amp;yen;</li>
           								</ul>
           				</blockquote>
            		</td>
            	</tr>
            	<tr>
            		<td colspan="2" class="settings-wide">Your current timezone ( <?php echo $sbm_timezone; ?> )
                
                	
                		<input type="text" name="sbm_timezone" value="<?php echo sbm_sticky_input($_POST['sbm_timezone'], $sbm_timezone ); ?>">
                		<div>This is to make sure that wherever you are, the reports and time stamps for letters, etc are closest to you</div>
           				<blockquote class="timezone-help">
           				Here are some examples.  <ul>
           									<li>Default will be America/Chicago</li>
           									<li>For London England use: Europe/London</li>
           									<li>For Cairo Egypt use: Africa/Cairo</li>
           									<li>For Sydney Australia use: Australia/Sydney</li>
           									<li>For Hawaii use: US/Hawaii</li>
           									<li>For tokyo use: Asia/Tokyo</li>
           								</ul>
           								
           								
           				</blockquote>
            		</td>
            	</tr>
            	<tr>
            		<td colspan="2" class="settings-wide">What type of sidebar menu do you want 
            		
		                <select name="sbm_menu_option">
		                <?php
							switch( get_option( 'sbm_menu_option' ) )
							{
								case 'sbm_short_menu';
									$short_menu_selected = 'selected="selected"';
								break;
								default;
									$full_menu_selected = 'selected="selected"';
								break;
							}
						?>
		                	<option <?php echo $full_menu_selected; ?> value="sbm_full_menu">Full Menu</option>
		                	<option <?php echo $short_menu_selected; ?> value="sbm_short_menu">Short Menu</option>
		                </select> 
            		</td>
            	</tr>
                <tr>
                	<td colspan="2" class="settings-wide">&nbsp;</td>
                </tr>
			</table>
            
		<?php	
			
		}
		else
		{
		?>
            <h2>Sorry you cant adjust the settings, you need to be an administrator</h2>
        <?php
		}
		?>
       <div class="float-left medium-padding">
		   <?php
            // Make sure this is not a read only user
            //  general_functions.php:     sbm_check_read_only_user()	
            if( ( sbm_check_read_only_user() == false ) || ( $current_user->ID == $id ) )
            {
                echo '<span><input type="submit" value="Submit" id="editUserSubmitButton"></span>';
            }
            //  general_functions.php:     sbm_cancel_button()
            echo sbm_cancel_button('sbm_view_home_page', 'cancel');
            ?>
        
        </div>
        
        </form>
        <br>
        <br>
        <div id="remove_message6">
        	<div>If you need to uninstall this plugin, the database tables and options that were created durning setup will NOT be deleted.</div>
        	<div>You can remove all the old settings and any database tables that were created by clicking this <a href="javascript: void(0);" id="sbmClearAllTables">link</a>.</div>
        </div>
       <div id="message"></div>

        <?php
			//  help_functions.php:     sbm_display_help()
			echo sbm_display_help( 'settings' );
		?>
     
    <div id="toggle-table-structure"><a href="javascript: void(0);">Show/HIde Table Structure</a></div>
	<div id="table-structure">
    	<?php
			$query = "SHOW TABLES LIKE '%sbm%'";
			$result = mysql_query($query);
			
			while($row = mysql_fetch_array($result))
			{
				echo '<h3>';
				sbm_pre_array( $row[0] );
				echo '</h3>';
				$sql = "DESCRIBE {$row[0]}";
				$ans = mysql_query($sql);
				
				while( $return = mysql_fetch_array($ans) )
				{
					echo '&nbsp;&nbsp;<b>';
					sbm_pre_array( $return[Field] );
					echo '</b>';
				}
				echo '<hr>';
				
			}
		?>
    </div>

   </div>
   <?php
}


function sbm_user_level_options($current_level, $user_level) {
    global $wpdb;
	global $current_user;
	
    if (isset($_POST[$wpdb->prefix . 'user_level'])) {
        $current_level = $_POST[$wpdb->prefix . 'user_level'];
    }

    switch ($current_level) {
        case '2';
            $two = 'selected="selected"';
            break;
        case '1';
            $one = 'selected="selected"';
            break;
        case '0';
            $zero = 'selected="selected"';
            break;
        default;
            $zero = 'selected="selected"';
            break;
    }

    $content = '<select name="' . $wpdb->prefix . 'user_level">';
    if ($current_user->user_level == 10) {
        $content .= '<option value="7">Owner</option';
    }
	
	if($current_user->user_level >= 2)
	{
    	$content .= '<option ' . $two . ' value="2">Equal to Owner</option';
	}
	if($current_user->user_level>= 1)
	{
    	$content .= '<option ' . $one . ' value="1">Read / Write</option>';
	}
    $content .= '<option ' . $zero . ' value="0">Read</option>';
    $content .= '</select>';

    return $content;
}


?>
