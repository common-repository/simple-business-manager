<?php

function sbm_check_duplicate_expense_type( $name )
{
	global $wpdb;
	global $current_user;

     get_currentuserinfo($current_user->ID);
		
							$query = "SELECT 
											COUNT(*)
										FROM 
											".$wpdb->prefix."sbm_meta 
										WHERE 
											meta_key = 'expense_type'
										AND 
											meta_value = '$name'
										 ";
				$count = $wpdb->get_var($wpdb->prepare($query));
	
	return $count;
}
function sbm_edit_expense_type(){

	global $wpdb;
	global $current_user;
	
	$info = new sbm_expense_type();

     get_currentuserinfo($current_user->ID);

	//  classes/expense_type.php:     sbm_get_expense_type_data()
	$info->sbm_get_expense_type_data($_GET['id']);
	
	
	if($_GET['status'] == 'new')
	{
		$id = 'new';
	}
	else if(!empty($_GET['id']))
	{
		$id = $_GET['id'];
	}
	else
	{
		echo '<h2>This page was reached in error</h2>';
		die();
	}
	
echo '<div class="wrap">';

		if (isset($_POST['ID']))
		{			
			
			$errors = array();
			
			
			// If this is new, all the fields are required
			if($_GET['status'] == 'new')
			{
				if(empty($_POST['name']))
				{
					$errors[] = 'You forgot the name.';
				}
				
			}
			
			if( sbm_check_duplicate_expense_type( $_POST['name'] ) > 0 )
			{
				$errors[] = 'You tried to enter a duplicate expense type name!';
			}
			if(empty($errors))
			{
				$info->sbm_update_expense_type();
				die('Should go to update expense type here');
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

	// If this is new, we need this section
	if ($_GET['status'] != 'delete')
	{
		if(empty($info->visible))
		{
			$visible = 1;
		}
				echo '<h2>Expense type  '.$info->name.' '.$info->city.'</h2>';
				echo '<form method="post">';
				echo '<div><input type="hidden" id="ID" name="ID" value="'.$id.'"></div>';
				
				echo '<div>Name: <input type="text" name="name" size="20" value="'.sbm_sticky_input($_POST['name'], $info->name).'"></div>';
				echo '<div><input type="hidden" name="visible" size="1" value="'.$visible.'"></div>';
				
				echo '<div class="float-left medium-padding">';
				//  general_functions.php:     sbm_check_read_only_user()	
				if(  sbm_check_read_only_user() == false  )
				{
					echo '<span><input type="submit" value="Submit"></span>';
					//  general_functions.php:     sbm_ok_to_delete()
					if( ( $_GET['status'] != 'new' ) && ( sbm_ok_to_delete('expense_type_id',$id, 'no') == true ) )
					{
						echo '<span><input type="button" value="Delete this Expense Type" onclick="sbm_verifyDeleteExpenseType('.$id.');"></span>';
					}
				}
				//  general_functions.php:     sbm_cancel_button()
				echo sbm_cancel_button('sbm_view_home_page', 'cancel');
			echo '</div><div class="clear"></div>';

			//  help_functions.php:     sbm_display_help()
			echo sbm_display_help( 'expense_type' );
	}
			// End New section

			// If this is Delete then use this section
	if($_GET['status'] == 'delete')
	{			
		$info = new sbm_expense_type();

		
		// get_currentuserinfo($current_user->ID);
		
		if(isset($_POST['verify_delete']))
		{
		 //  classes/expense_type.php:     sbm_delete_expense_type()
			$info->sbm_delete_expense_type($_GET['id']);
			die('The attempt to delete this expense type failed, please contact customer services');
			
		}
		 //  classes/expense_type.php:     sbm_get_expense_type_data()
		$info->sbm_get_expense_type_data($_GET['id']);
	
		echo '<div class="wrap">';
		//  general_functions.php:     sbm_ok_to_delete()
		sbm_ok_to_delete('expense_type_id',$_GET['id'], 'yes');
		
		if(!empty($_GET['id']))
		{
			echo '<h2>Delete expense: '.$info->name.'</h2>';
			echo '<div>Deleting this expense type will remove all data associated with this item</div>';
			echo '<form method="post">
						<input type="hidden" name="verify_delete" value="true">';
			echo '<div class="float-left medium-padding">';
			//  general_functions.php:     sbm_cancel_button()
			echo sbm_cancel_button('sbm_view_expense_type_list', 'cancel');
			echo '<input type="submit" value="Delete">';
			echo '</div>';
	
		}
		else
		{
			echo '<h2>You need to select a expense type before you can use this page</h2>';
		}
	}

			echo '</form>';
			echo '<div id="output_div"></div>';
		echo '</div>';

}

function sbm_view_expense_type_list()
{
	global $wpdb;
	
	
	global $current_user;
	$info = new sbm_expense_type();
	

     get_currentuserinfo($current_user->ID);
	
	
	echo '<div class="wrap">';
	$user_info =  get_currentuserinfo($current_user->ID);
	
	if(!empty($_GET['message']))
	{
			//  general_functions.php:     sbm_get_message()
			//  general_functions.php:     sbm_message_details() 
			//  general_functions.php:     sbm_clear_notice() 
		
			echo '<div id="message" class="success">'.sbm_get_message($_GET['message']).'</div>';
			// call the function that will remove the success div after 5 seconds
			sbm_clear_notice('message', '5');

	}
	echo '<h2>Expense Type List </h2>';	
					   $query = "SELECT 
											ID,
											meta_value 
										FROM 
											".$wpdb->prefix."sbm_meta 
										WHERE 
											meta_key = 'expense_type'
										
										ORDER BY 
											meta_value ASC";
						$result = $wpdb->get_results($query);
			
			if(count($result) > 0)
			{
					echo '<table id="bw_table" style="width: 400px;"><tr><td></td><td>Name</td></tr>';
			
			
				$bg = 'even_bg'; // Start even
				foreach ($result as $list) {
					
						switch($bg)
						{
							case 'even_bg'; $bg = 'odd_bg';
							break;
							case 'odd_bg'; $bg = 'even_bg';
							break;
							default;
								$bg = 'current_bg';
							break;
						}
						//  classes/expense_type.php:     sbm_get_expense_type_data()
						$info->sbm_get_expense_type_data($list->ID);
						
						echo '<tr class="'.$bg.'">';
						
						echo '<td>';
						//  general_functions.php:     sbm_check_read_only_user()	
						if(  sbm_check_read_only_user() == false  )
						{
							echo '<a href="admin.php?page=sbm_edit_expense_type&id=' . $list->ID . '">Edit</a>';
							//  general_functions.php:     sbm_ok_to_delete()
							if (sbm_ok_to_delete('expense_type_id',$list->ID, 'no') == true )
							{
								echo '| <a href="admin.php?page=sbm_edit_expense_type&status=delete&id=' . $list->ID . '">Delete</a>';
							}
						}
						echo '</td>';
						echo '<td>' . $info->name . '</td>';
						echo '</tr>';
				}
					echo '</table><div class="clear"></div>';
			}
			else
			{
				echo '<h3>You do not have any expense types, please use the link below to add them!</h3>';
			}
	echo '<div class="float-left medium-padding">';
	//  general_functions.php:     sbm_cancel_button()
	echo sbm_cancel_button('sbm_view_home_page', 'cancel');
	//  general_functions.php:     sbm_check_read_only_user()	
	if( sbm_check_read_only_user() == false ) 
	{
		echo '<span><input type="button" value="Add a expense type" onclick="javascript: window.location = \'./admin.php?page=sbm_edit_expense_type&status=new\';"></span>';
	}
	echo '</div>';
	echo '</div>';
}

function sbm_get_expense_types_as_option()
{
	global $wpdb;
	$expense_type_info = new sbm_expense_type();
	
			$query = "SELECT 
											ID,
											meta_value 
										FROM 
											".$wpdb->prefix."sbm_meta 
										WHERE 
											meta_key = 'expense_type'
										
										 
										ORDER BY 
											meta_value ASC";
			$expense_type = $wpdb->get_results($query);
			
			if(count($expense_type) != 0)
			{
				$content = '';
				foreach($expense_type as $list)
				{
					
					// check to see if the page was found by a submit button event
					if(isset($_POST['expense_type_id']))
					{
						// helsp make the select box sticky if they hit submit
						if ($_POST['expense_type_id'] == $list->ID)
						{
							$selected = 'selected="selected"';
							
						}
						else
						{
							$selected = '';
							
						}
					}
					else
					{
						if(empty($get_expense_type_id))
						{
							$sbm_expense_type_id = $list->ID;
						}
						else
						{
							$sbm_expense_type_id = $get_expense_type_id;
						}
						// This is for new or edit
						if ($db_expense_type_id == $list->ID)
						{
							$selected = 'selected="selected"';
							
						}
						else
						{
							$selected = '';
							
						}
					}
					//  classes/expense_type.php:     sbm_get_expense_type_data()
					$expense_type_info->sbm_get_expense_type_data($list->ID);
					$content .= '<option ' . $selected . ' value="' . $list->ID . '">' . $expense_type_info->name . ' &nbsp;&nbsp;</option>';
				}
			}
			else
			{
				$content = '<option value=""> -- No transaction types have been setup -- </option>';
			}
	return $content;
}



function sbm_count_expense_types()
{
	global $wpdb;
	global $current_user;

     get_currentuserinfo($current_user->ID);
		
				$query = "SELECT 
											COUNT(*)
										FROM 
											".$wpdb->prefix."sbm_meta 
										WHERE 
											meta_key = 'expense_type'
										
										 ";
				$count = $wpdb->get_var($wpdb->prepare($query));
	
	return $count;
}

?>