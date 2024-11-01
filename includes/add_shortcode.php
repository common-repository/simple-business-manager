<?php
// Add the code to allow for public sign up
add_shortcode( 'public_customer_signup', 'public_customer_signup' );
// To allow for public sign up, and use our form except for the hourly rate and tax rate, 
// You can use the shortcode [public-customer-signup-form]
function public_customer_signup( $atts ) {
 
			
	$customer_info = new sbm_customer();
			
			// If this is new, all the fields are required
			if(isset( $_POST['first_name_1'] ))
			{
				$errors = array();
				
				if(empty($_POST['first_name_1']))
				{
					$errors[] = 'You forgot the first name.';
				}
				if(empty($_POST['last_name_1']))
				{
					$errors[] = 'You forgot the last name.';
				}
																	   
				if(empty($errors))
				{
					
					if(sbm_customer::sbm_update_customer( $public = TRUE ) == TRUE )
					{
						
						$do_not_display = TRUE;
					}
					else
					{
						echo '<h2>There was a problem saving your information please contact us</h2>You can try again!';
							
					}
					
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
	  if( $do_not_display != TRUE )
	  {
	?>
<form id="public_customer_signup" method="post">
				<div class="float-left">
					<h4 class="clear">Primary Customer Information</h4>
                    <div>
                        <div class="description">Company Name</div> 
                        <input type="text" id="company_name" class="" name="company_name" size="30" value="<?php echo sbm_sticky_input($_POST['company_name'], $customer_info->company_name); ?>">
                    </div>
                    <div>
                        <div class="description">First Name<em>*</em></div>
                        <input type="text" id="first_name_1" class="required" name="first_name_1" size="20" value="<?php echo sbm_sticky_input($_POST['first_name_1'], $customer_info->first_name_1); ?>">
                    </div>
                    <div>
                        <div class="description">Last Name<em>*</em></div>
                        <input type="text" id="last_name_1" class="required" name="last_name_1" size="20" value="<?php echo sbm_sticky_input($_POST['last_name_1'], $customer_info->last_name_1); ?>">
                    </div>
                    <div>
                        <div class="description">Address</div>
                        <input type="text" id="address" name="address" size="20" value="<?php echo sbm_sticky_input($_POST['address'], $customer_info->address); ?>">
                    </div>
                    <div>
                        <div class="description">Address 2</div>
                        <input type="text" id="address_2" name="address_2" size="20" value="<?php echo sbm_sticky_input($_POST['address_2'], $customer_info->address_2); ?>">
                    </div>
                    <div>
                        <div class="description">City</div>
                        <input type="text" id="city" name="city" size="20" value="<?php echo sbm_sticky_input($_POST['city'], $customer_info->city); ?>">
                    </div>
                    <div>
                        <div class="description">State/Province</div>
                        <input type="text" id="state" name="state" size="20" maxlength="50" value="<?php echo sbm_sticky_input($_POST['state'], $customer_info->state); ?>">
                    </div>
                    <div>
                        <div class="description">Zip</div>
                        <input type="text" id="zip" name="zip" size="20" value="<?php echo sbm_sticky_input($_POST['zip'], $customer_info->zip); ?>">
                    </div>
                    <div>
                        <div class="description">Main Phone:</div>
                        <input type="text" name="main_phone" size="16" maxlength="13" value="<?php echo sbm_sticky_input($_POST['main_phone'], $customer_info->main_phone); ?>">
                    </div>
                    <div>
                        <div class="description">Secondary Phone:</div>
                        <input type="text" name="secondary_phone" size="16" maxlength="13" value="<?php echo sbm_sticky_input($_POST['secondary_phone'], $customer_info->secondary_phone); ?>">
                    </div>
                    <div>
                        <div class="description">Fax:</div>
                        <input type="text" name="fax" size="16" maxlength="13" value="<?php echo sbm_sticky_input($_POST['fax'], $customer_info->fax); ?>">
                    </div>
                    <div>
                        <div class="description">Email #1:</div>
                        <input type="text" name="email_1" size="30" value="<?php echo sbm_sticky_input($_POST['email_1'], $customer_info->email_1); ?>">
                    </div>
				
				</div>
				
				<div class="float-left" style="padding-left: 10px;">
                    <h4 class="clear">Secondary customer Information</h4>
                    <div>
                        <div class="description">First Name ( #2 ):</div>
                        <input type="text" name="first_name_2" size="20" value="<?php echo sbm_sticky_input($_POST['first_name_2'], $customer_info->first_name_2); ?>">
                    </div>
                    <div>
                        <div class="description">Last Name( #2 ):</div>
                        <input type="text" name="last_name_2" size="20" value="<?php echo sbm_sticky_input($_POST['last_name_2'], $customer_info->last_name_2); ?>">
                    </div>
                    <div>
                        <div class="description">Email ( #2 ):</div>
                        <input type="text" name="email_2" size="30" value="<?php echo sbm_sticky_input($_POST['email_2'], $customer_info->email_2); ?>">
                    </div>
				</div>
				<input type="hidden" name="customer_status" value="Prospective">
				<div class="clear"></div>
	<input id="publicCustomerSignupButton" type="submit" value="Submit">
</form>
<?php
	  }
	  else
	  {
	  	// output a message if needed that it was successful
	  	?>
	  	<h3>Your information was saved, and we will be in contact with you soon</h3>
	  	<br>
	  	<br>
	  	<?php 
	  }
}

?>