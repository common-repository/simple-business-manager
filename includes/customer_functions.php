<?php



function sbm_customer_options()
{

    global $current_user;

    get_currentuserinfo($current_user->ID);

    echo '
			<div class="wrap">';
    if(!empty($_GET['message']))
    {
        //  general_functions.php:     sbm_get_message()
        //  general_functions.php:     sbm_message_details()
        //  general_functions.php:     sbm_clear_notice()

        echo '<div id="message" class="success">'.sbm_get_message($_GET['message']).'</div>';
        // call the function that will remove the success div after 5 seconds
        sbm_clear_notice('message', '5');
    }

    echo '<h2>(customer Options) What do you want to do?</h2>';

    echo '<div><a href="admin.php?page=sbm_view_customer_list">View/Edit customers</a></div>';
    //  general_functions.php:     sbm_check_read_only_user()
    if( sbm_check_read_only_user() == false )
    {
        echo '<div><a href="admin.php?page=sbm_edit_customer&status=new">Add a customer</a></div>';
    }
    echo '

					<div><a href="admin.php?page=sbm_view_customer_account">customer Accounts</a></div>';
    //  general_functions.php:     sbm_check_read_only_user()
    if( sbm_check_read_only_user() == false )
    {

        echo '<div><a href="admin.php?page=sbm_misc_customer">Misc Charge or Credit to customer</a></div>';
    }
    echo '</div>';

}

function sbm_misc_customer()
{

    global $current_user;
    $customer_info  = new sbm_customer();




    echo '<div class="wrap">';

    // check to see if the submit button was used
    if(isset($_POST['id']))
    {

        if(isset($_POST['verify_delete']))
        {
            die('Delete misc credit/debit, not setup');
        }


        $errors = array();


        // If this is new, all the fields are required
        if($_GET['status'] == 'new')
        {
            if(empty($_POST['credit_debit']))
            {
                $errors[] = 'You forgot to choose if this is a credit or debit.';
            }
            if(empty($_POST['misc_date']))
            {
                $errors[] = 'You forgot the date.';
            }
            else
            {
                $misc_date = explode("/", $_POST['misc_date']);


                $misc_month = $misc_date[0];
                $misc_day = $misc_date[1];
                $misc_year = $misc_date[2];
                // all should have a value before proceeding
                if(( !empty($misc_month)) && (!empty($misc_day)) && (!empty($misc_year)) )
                {
                    $misc_time = mktime(0,0,0,$misc_month,$misc_day,$misc_year);
                }
                else
                {
                    $errors[] = 'There is a problem with the date';
                }
            }
            if(empty($_POST['amount']))
            {
                $errors[] = 'You forgot the amount';
            }


        }
        if(empty($errors))
        {

            if($_POST['credit_debit'] == 'debit')
            {
                $customer_info->sbm_submit_debit();
                die('Should go to submit debit here');
            }
            if($_POST['credit_debit'] == 'credit')
            {
                $customer_info->sbm_submit_credit();

                die('Should go to submit credit here');
            }

            die(); // The page should never get this far, but we do not want it to reload
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



    if(!empty($_GET['message']))
    {
        //  general_functions.php:     sbm_get_message()
        //  general_functions.php:     sbm_message_details()
        //  general_functions.php:     sbm_clear_notice()

        echo '<div id="message" class="success">'.sbm_get_message($_GET['message']).'</div>';
        // call the function that will remove the success div after 5 seconds
        sbm_clear_notice('message', '5');
    }
    if(!empty($_GET['id']))
    {
        get_currentuserinfo($current_user->ID);


        //  classes/sbm_customer.php:     sbm_get_customer_data()
        $customer_info->sbm_get_customer_data($_GET['id']);

        echo '<h2>Misc Debit/Credit to customer</h2>';
        echo '<h3>customer Information</h3>';
        echo '<div>First Name: '.($customer_info->first_name_1).'</div>';
        echo '<div>Last Name: '.($customer_info->last_name_1).'</div>';
        echo '<form method="post" id="miscCreditDebitForm">';
        echo '<div>Please select one (Credit or Debit):  <select  name="credit_debit"  class="required">
					<option value="">-- Please Select One --</option>
					<option value="credit">Credit</option>
					<option value="debit">Debit</option>
					</select>';
        echo '<div>Date<em>*</em>:<input type="text" class="required"  id="misc_date" name="misc_date" value="' . date("m/d/Y") . '"></div>';
        echo '<div>Amount<em>*</em>:<input type="text"  class="required number"  id="amount" name="amount" value=""></div>';
        echo '<div>Description <input type="text" id="description" name="description" value=""></div>';
        echo '<div><input type="hidden" name="id" value="'.$_GET['id'].'">

						     </div>';
        echo '<div></div>';
        echo '<div class="float-left medium-padding">';
        echo '<span><input type="submit" value="Submit" id="miscCreditDebitSubmitButton"></span>';
        //  general_functions.php:     sbm_cancel_button()
        echo sbm_cancel_button('sbm_view_customer_list', 'cancel');
        echo '</div>';
        echo '</form>';
    }
    else
    {
        //  general_functions.php:     sbm_redirect()
        sbm_redirect('sbm_view_customer_list');
    }

    echo '</div>';

}

function sbm_get_the_customer_data( $customer_id, $bg  )
{
    if($customer_id > 0 )
    {
        global $current_user;
        $user_level         = $current_user->user_level;

        $currency_symbol    = get_option( 'sbm_currency' );
        $customer_info 	    = new sbm_customer();
        $letter_info 	    = new sbm_letter();

        get_currentuserinfo($current_user->ID);
        $user_info =  get_currentuserinfo($current_user->ID);


        //  classes/sbm_customer.php:     sbm_get_customer_data()
        $customer_info->sbm_get_customer_data( $customer_id);
        //  classes/sbm_customer.php:     sbm_get_customer_balance()
        $customer_info->sbm_get_customer_balance( $customer_id);
        //  classes/letter.php:    	   sbm_get_total_letters_by_customer()
        $letter_info->sbm_get_total_letters_by_customer( $customer_id );
        //  classes/property.php:     sbm_get_property_data()

        $list_of_attr = array( 'last_name_1', 'first_name_1', 'company_name', 'address', 'city', 'state', 'zip' );

        foreach( $list_of_attr as $list )
        {
            // Last Name
            if(!empty( $customer_info->$list ))
            {
                $$list = $customer_info->$list;
            }
            else
            {
                $$list = NULL;
            }
        }


        $content = '<tr class="'.$bg.' ">';
        $content .= '<td>' .  $customer_info->customer_status . '</td><td><a href="admin.php?page=sbm_view_customer_account&customer_id=' . $customer_id . '">View Account</a></td>';
        $content .= '<td>';
        //  general_functions.php:     sbm_check_read_only_user()
        if(  sbm_check_read_only_user() == false  )
        {
            $text = 'Edit';
            $content .= '<a href="admin.php?page=sbm_edit_customer&id=' . $customer_id . '">' . $text . '</a>';
        }
        else
        {
            //$text = 'View';
            //$content .= '<a href="admin.php?page=sbm_edit_customer&id=' . $customer_id . '">' . $text . '</a>';
        }
        //  general_functions.php:     sbm_check_read_only_user()
        //  general_functions.php:     sbm_ok_to_delete()
        if(  ( sbm_check_read_only_user() == false  ) &&  (sbm_ok_to_delete('customer_id',$customer_id, 'no') == true ) )
        {
            $content .= '| <a href="admin.php?page=sbm_edit_customer&status=delete&id=' . $customer_id . '">Delete</a>';
        }
        $content .= '</td>';
        //  general_functions.php:     sbm_check_read_only_user()
        if(  sbm_check_read_only_user() == false  )
        {

            $content .= '<td><a href="admin.php?page=sbm_create_invoice&status=new&customer_id=' . $customer_id . '">New Invoice</a></td>';
        }
        else
        {
            $content .= '<td></td>';
        }
        $content .= '<td>' . $last_name_1 . '</td>';
        $content .= '<td>' . $first_name_1 . '</td>';
        $content .= '<td>' . $company_name . '</td>';
        $content .= '<td>' . $address . '</td>';
        $content .= '<td>' . $city . ' ' . $state . ' ' . $zip . '</td>';
        $content .= '<td>' . $currency_symbol . '';
        $balance = $customer_info->balance;
        if ($balance < 0 )
        {
            $content .= '<span class="red">'.  number_format(str_replace('-', '', $balance), 2) . '</span>';
        }
        if ($balance >= 0 )
        {
            $content .= '<span class="green">' . number_format($balance, 2) . '</span>';
        }

        $content .= '</td>';

        if( $letter_info->total_letters > 0 )
        {
            $content .= '<td><a href="admin.php?page=sbm_list_letters_for_customer&customer_id=' . $customer_id . '">' . $letter_info->total_letters . '</a></td>';
        }
        else
        {
            $content .= '<td>none</td>';
        }
        $content .= '</tr>';

        return $content;
    }
}


function sbm_view_customer_list()
{

    global $wpdb;
    global $current_user;
    $customer_info      = new sbm_customer();
    $user_level         = $current_user->user_level;

    get_currentuserinfo($current_user->ID);
    $user_info 	=  get_currentuserinfo($current_user->ID);
    if( sbm_check_read_only_user() == true )
    {
        // Get the customer id
        $customer_id = sbm_get_user_customer_id( $current_user->ID );
        // make the $company_name the current customers company name
        //  classes/sbm_customer.php:     sbm_get_customer_data()
        $customer_info->sbm_get_customer_data( $customer_id );

        $company_name[] = $customer_info->company_name;

        $show_only_this_customer = " AND  " . $wpdb->prefix . "sbm_customer.ID = '$customer_id' ";
    }

    if($_GET['filter_by_status'] == 'Unassigned')
    {

        if(isset($_POST['submitted']))
        {

            foreach($_POST['add_customer'] as $user_id)
            {
                $customer_id = $customer_info->sbm_create_customer();

                /*
                * first_name_1
                * last_name_1
                * email_1
                *
                */
                $data = get_userdata($user_id);
                $first_name = strtolower($data->first_name);
                $last_name = strtolower($data->last_name);
                $email = $data->user_email;


                $query = "INSERT INTO
                                                ".$wpdb->prefix."sbm_customer_meta
                                            (
                                                customer_id,
                                                meta_key,
                                                meta_value
                                             )
                                            VALUES
                                            (
                                                %d,
                                                %s,
                                                %s
                                            )";

                // do an insert
                $wpdb->query( $wpdb->prepare( $query , array(  $customer_id, 'first_name_1', $first_name ) ) );
                $wpdb->query( $wpdb->prepare( $query , array(  $customer_id, 'last_name_1', $last_name ) ) );
                $wpdb->query( $wpdb->prepare( $query , array(  $customer_id, 'email_1', $email ) ) );
                $wpdb->query( $wpdb->prepare( $query , array(  $customer_id, 'customer_status', 'Current' ) ) );

                $query = "DELETE FROM ".$wpdb->prefix."usermeta WHERE user_id = $user_id AND meta_key = 'nickname'";
                $wpdb->query( $wpdb->prepare($query));
                $query = "DELETE FROM ".$wpdb->prefix."usermeta WHERE user_id = $user_id AND meta_key = 'customer_id'";
                $wpdb->query( $wpdb->prepare($query));
                $query = "INSERT INTO
										    ".$wpdb->prefix."usermeta
											(
												user_id,
												meta_key,
												meta_value
											 )
											VALUES
											(
												%d,
												%s,
												%s
											)";

                // do an insert
                $wpdb->query( $wpdb->prepare( $query , array(  $user_id, 'customer_id', $customer_id ) ) );
                $wpdb->query( $wpdb->prepare( $query , array(  $user_id, 'nickname', $email ) ) );
                $is = 0;

            }
        }
        $compare    = array();
        $final_list = array();

        // This is to display a list of all subscribers who have not been setup as customers yet
        $query = "SELECT
				GROUP_CONCAT( meta_value ) as customer_data

				FROM
					" . $wpdb->prefix . "sbm_customer_meta
				WHERE
                    meta_key = 'last_name_1'
                 OR
                    meta_key = 'first_name_1'
				GROUP BY
					customer_id";
        $customer_list 	= $wpdb->get_results($query);
        foreach($customer_list as $customer)
        {
            $name       = explode(',', $customer->customer_data);
            $compare[]  = array(strtolower($name[0]), strtolower($name[1]));
        }

        $subscribers = get_users('role=subscriber');
        foreach($subscribers as $user)
        {
            $data = get_userdata($user->ID);
            $first_name = strtolower($data->first_name);
            $last_name = strtolower($data->last_name);
            $email = $data->user_email;
            $check = 0;
            foreach($compare as $customer)
            {
                if(( $customer[0] == $first_name ) && ($customer[1] == $last_name))
                {
                    $check++;
                }

            }
            if($check == 0)
            {
                $final_list[$user->ID] = array($first_name, $last_name, $email);
            }

        }

        ?><div class="wrap">
        <h1>This is a list of Subscribers that are NOT customers, check the ones you want to convert to customers</h1>
        <form action="./admin.php?page=sbm_view_customer_list&filter_by_status=Unassigned" method="post">

            <table class="bw_table">
                <tr>
                    <td style="width: 40px;"><input type="checkbox" id="select_all_unassigned"> (all)</td>
                    <td>First Name</td>
                    <td>Last Name</td>
                    <td>Email</td>
                </tr>
                <?php
                $i = 1;
                foreach($final_list as $id => $customer)
                {
                    if($i % 2)
                    {
                        $bg = 'odd_bg';
                    }
                    else
                    {
                        $bg = 'even_bg';
                    }
                    ?>
                    <tr class="<?php echo $bg; ?>">
                        <td style="width: 40px;"><input type="checkbox" class="check_box_add_customer" name="add_customer[]" value="<?php echo $id; ?>"></td>
                        <td><?php echo $customer[0]; ?></td>
                        <td><?php echo $customer[1]; ?></td>
                        <td><?php echo $customer[2]; ?></td>
                    </tr>
                    <?php
                    $customer = array();
                    $i++;
                }
                ?>
            </table>
            <div class="clear" style="padding: 20px;">
                <input type="hidden" name="submitted" value="true">
                <input type="submit" value="Add as SBM Customers">
            </div>
        </form>
    </div>

    <?php
        $final_list = array();
    }
    else
    {

        // Gather the results as typically expected
        $query = "SELECT
				GROUP_CONCAT( meta_key, '|', meta_value ) as customer_data,
					customer_id
				FROM
					" . $wpdb->prefix . "sbm_customer,
					" . $wpdb->prefix . "sbm_customer_meta
				WHERE

					" . $wpdb->prefix . "sbm_customer.ID = " . $wpdb->prefix . "sbm_customer_meta.customer_id
					$show_only_this_customer
				AND
					visible = 1
				GROUP BY
					customer_id";
        $customer_list 	= $wpdb->get_results($query);



        $result = array();

        if (!empty($_GET['filter_by_status']))
        {

            $filter_by_status = $_GET['filter_by_status'];

        }
        else
        {
            $filter_by_status = 'Current';
        }


        // If the customer ID was not used as a display option use this
        foreach( $customer_list as $list )
        {
            // Break up the comma delimited
            $explode1 = explode( ',', $list->customer_data );

            foreach( $explode1 as $meta_content )
            {
                // now explode each result to seperate the information that is pipe delimited
                $explode2 = explode('|', $meta_content );

                $result[ $list->customer_id ][ $explode2[0] ] 	= $explode2[1];

            }

            if ( ($filter_by_status == 'All') || ( $filter_by_status == $result[ $list->customer_id ][ 'customer_status' ] ) )
            {	// My list of sortable fields for this table
                $id[ $list->customer_id ]				= $list->customer_id;
                $company_name[ $list->customer_id ] 	= $result[ $list->customer_id ][ 'company_name' ];
                $last_name[ $list->customer_id ] 		= $result[ $list->customer_id ][ 'last_name_1' ];
                $first_name[ $list->customer_id ] 		= $result[ $list->customer_id ][ 'first_name_1' ];
                $customer_status[ $list->customer_id ]	= $result[ $list->customer_id ][ 'customer_status' ];

            }


        }




        //  general_functions.php:     sbm_check_read_only_user()
        if(  sbm_check_read_only_user() == false  )
        {
            $text = 'Charge/Credit';
        }
        else
        {
            $text = '';
        }

        if( (!empty( $_GET['sort_by'])) )
        {
            $sort_by 	= $_GET['sort_by'];
        }
        else
        {
            $sort_by = 'company_name';
        }

        if((!empty($_GET['order_by'])))
        {
            $order_by	= $_GET['order_by'];

            if($order_by == 'ASC')
            {
                $toggle_order_by = 'DESC';
            }
            else
            {
                $toggle_order_by = 'ASC';
            }

        }
        else
        {
            $order_by = 'ASC';
        }

        $bg     = '';
        $output = '';

        switch( $sort_by )
        {
            case 'last_name';

                if( count( $last_name ) >= 1 )
                {
                    foreach( sbm_sort_list( $last_name, $order_by ) as $key => $list )
                    {
                        $bg = sbm_get_bg( $bg );

                        $output .= sbm_get_the_customer_data( $key, $bg );
                    }
                }
                else
                {
                    $output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';
                }
                $last_name_order_by = $toggle_order_by;
                $last_name_arrow = 'arrow-' . $order_by;
                break;
            case 'first_name';
                if( count( $first_name ) >= 1 )
                {
                    foreach( sbm_sort_list( $first_name, $order_by ) as $key => $list )
                    {
                        $bg = sbm_get_bg( $bg );

                        $output .= sbm_get_the_customer_data( $key, $bg );
                    }
                }
                else
                {
                    $output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';
                }
                $first_name_order_by = $toggle_order_by;
                $first_name_arrow = 'arrow-' . $order_by;
                break;
            case 'company_name';
                // This is default landing page for first visit and especially for customer
                if( count( $company_name ) >= 1 )
                {
                    foreach( sbm_sort_list( $company_name, $order_by ) as $key => $list )
                    {
                        $bg = sbm_get_bg( $bg );

                        $output .= sbm_get_the_customer_data( $key, $bg );
                    }
                }
                else
                {
                    $output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';
                }
                $company_name_order_by = $toggle_order_by;
                $company_name_arrow = 'arrow-' . $order_by;
                break;
            case 'customer_status';
                if( count( $customer_status ) >= 1 )
                {
                    foreach( sbm_sort_list( $customer_status, $order_by ) as $key => $list )
                    {
                        $bg = sbm_get_bg( $bg );

                        $output .= sbm_get_the_customer_data( $key, $bg );
                    }
                }
                else
                {
                    $output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';
                }
                $customer_status_order_by = $toggle_order_by;
                $customer_status_arrow = 'arrow-' . $order_by;
                break;
            default;

                if( count( $company_name ) >= 1 )
                {
                    foreach( sbm_sort_list( $company_name, $order_by ) as $key => $list )
                    {
                        $bg = sbm_get_bg( $bg );

                        $output .= sbm_get_the_customer_data( $key, $bg );
                    }
                }
                else
                {
                    $output = '<tr class="odd_bg"><td colspan="11" style="text-align: center;"><h2>No matches found</h2></td></tr>';
                }
                $company_name_order_by = $toggle_order_by;
                $company_name_arrow = 'arrow-' . $order_by;
                break;
        }

        // If the order by have not been set use ASC
        if( empty( $last_name_order_by ) )
        {
            $last_name_order_by = 'ASC';
        }

        if( empty( $first_name_order_by ) )
        {
            $first_name_order_by = 'ASC';
        }

        if( empty( $customer_status_order_by ) )
        {
            $customer_status_order_by = 'ASC';
        }
        if( empty( $company_name_order_by ) )
        {
            $company_name_order_by = 'ASC';
        }

        switch( $filter_by_status )
        {
            case 'All';
                $filter_by_status_all           = 'selected="selected"';
                $filter_by_status_prospective   = '';
                $filter_by_status_current       = '';
                $filter_by_status_past          = '';

                break;
            case 'Current';
                $filter_by_status_current       = 'selected="selected"';
                $filter_by_status_prospective   = '';
                $filter_by_status_all           = '';
                $filter_by_status_past          = '';

                break;
            case 'Past';
                $filter_by_status_past          = 'selected="selected"';
                $filter_by_status_current       = '';
                $filter_by_status_all           = '';
                $filter_by_status_prospective   = '';
                break;
            case 'Prospective';
                $filter_by_status_prospective   = 'selected="selected"';
                $filter_by_status_current       = '';
                $filter_by_status_all           = '';
                $filter_by_status_past          = '';
                break;
            case 'Unassigned';
                $filter_by_status_unassigned    = 'selected="selected"';
                $filter_by_status_current       = '';
                $filter_by_status_all           = '';
                $filter_by_status_past          = '';
                break;
            default;
                $filter_by_status_all           = 'selected="selected"';
                $filter_by_status_prospective   = '';
                $filter_by_status_current       = '';
                $filter_by_status_past          = '';
                break;
        }

        if( sbm_check_read_only_user() == false )
        {


            echo '<label for="customer_filter_view_customer_list" class="larger_label">Filter by customer status</label><select name="customer_filter_view_customer_list" id="customer_filter_view_customer_list">
                                <option ' . $filter_by_status_all . ' value="All">Show All customers</option>
                                <option ' . $filter_by_status_current . ' value="Current">Show only Current customers</option>
                                <option ' . $filter_by_status_past . ' value="Past">Show only Past customers</option>
                                <option ' . $filter_by_status_prospective . ' value="Prospective">Show only Prospective customers</option>
                                <option ' . $filter_by_status_unassigned . ' value="Unassigned">Show unassigned subscribers to this site</option>
                                </select>';
            echo '<br /><br />';
        }

        if(!empty($_GET['message']))
        {
            //  general_functions.php:     sbm_get_message()
            //  general_functions.php:     sbm_message_details()
            //  general_functions.php:     sbm_clear_notice()

            echo '<div id="message" class="success">'.sbm_get_message($_GET['message']).'</div>';
            // call the function that will remove the success div after 5 seconds
            sbm_clear_notice('message', '5');

        }
        if( sbm_check_read_only_user() == false )
        {
            ?>
        <div>
            <input type="button" value="Add a Customer" onclick="javascript: window.location = './admin.php?page=sbm_edit_customer&status=new';">
        </div>
        <?php
        }
        ?>

    <div id="customer_list">
        <table id="bw_table">
            <tr>
                <td><a href="admin.php?page=sbm_view_customer_list&sort_by=customer_status&order_by=<?php echo $customer_status_order_by; ?>&filter_by_status=<?php echo $filter_by_status; ?>"><div class="float-left"></div><div class="<?php echo $customer_status_arrow; ?>"></div>Status</a></td>
                <td>View Account</td>
                <td>Edit/Delete</td>
                <td>Create Invoice</td>
                <td><a href="admin.php?page=sbm_view_customer_list&sort_by=last_name&order_by=<?php echo $last_name_order_by; ?>&filter_by_status=<?php echo $filter_by_status; ?>"><div class="float-left"><div class="<?php echo $last_name_arrow; ?>"></div></div>Last Name</a></td>
                <td><a href="admin.php?page=sbm_view_customer_list&sort_by=first_name&order_by=<?php echo $first_name_order_by; ?>&filter_by_status=<?php echo $filter_by_status ; ?>"><div class="float-left"><div class="<?php echo $first_name_arrow; ?>"></div></div>First Name</a></td>
                <td><a href="admin.php?page=sbm_view_customer_list&sort_by=company_name&order_by=<?php echo $company_name_order_by; ?>&filter_by_status=<?php echo $filter_by_status ; ?>"><div class="float-left"><div class="<?php echo $company_name_arrow; ?>"></div></div>Company Name</a></td>
                <td>Address</td>
                <td>City, State. Zip</td>
                <td>Balance</td>
                <td>Letters</td>
            </tr>
            <?php

            echo $output;
            ?>
        </table>
    </div>
    <?php
        if( sbm_check_read_only_user() == false )
        {
            ?>

        <div class="clear">
            <input type="button" value="Add a Customer" onclick="javascript: window.location = './admin.php?page=sbm_edit_customer&status=new';">
        </div>

        <?php
        }

    } // ends check to see if we are trying to display Unassigned customers


}

function sbm_edit_customer()
{

    global $wpdb;
    global $current_user;
    $currency_symbol 	= get_option( 'sbm_currency' );


    $customer_info 		= new sbm_customer();

    get_currentuserinfo($current_user->ID);


    if($_GET['status'] == 'new')
    {

    }
    else if(!empty($_GET['id']))
    {
        $id = $_GET['id'];
    }
    else
    {
        // take the user to the view customer list
        //  general_functions.php:     sbm_redirect()
        sbm_redirect('sbm_view_customer_list');
        //echo '<h2>This page was reached in error</h2>';
        die();
    }
    echo '<div class="wrap">';

    if (isset($_POST['customer_id']))
    {
        if(isset($_POST['verify_delete']))
        {
            $customer_info->sbm_delete_customer($_POST['customer_id']);
            die('Delete customer, if this is visible please contact customer support');
        }


        $errors = array();


        // If this is new, all the fields are required
        if($_GET['status'] == 'new')
        {
            if(empty($_POST['first_name_1']))
            {
                $errors[] = 'You forgot the first name.';
            }
            if(empty($_POST['last_name_1']))
            {
                $errors[] = 'You forgot the last name.';
            }
            if(empty($_POST['customer_status']))
            {
                $errors[] = 'You forgot the status of this customer: Current,  Past or Prospective';
            }



        }

        // if the password is set, then email_1 is required
        if(isset($_POST['password']))
        {
            // check to see if email_1 is set, if not show error
            if( ( !empty( $_POST['password'] ) ) && ( empty($_POST['email_1']) ) )
            {
                $errors[] = 'You forgot the email#1, this is required if setting the password';
            }
            else
            {
                if( (!empty($_POST['email_1']) ) &&  ( sbm_user_email_available( $_POST['email_1'] ) == false ) )
                {
                    // Get the user id
                    $user_id = sbm_get_user_id( $_POST['customer_id'] );
                    // Does this email belong to this customer, if not show the error
                    if(sbm_customer_user_email_validate($_POST['email_1'], $user_id) == false )
                    {
                        $errors[] = 'The email you chose has already been taken, please try again';
                    }
                }
            }

        }

        if(empty($errors))
        {

            $customer_info->sbm_update_customer();
            die('');
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
        //  classes/sbm_customer.php:     sbm_get_customer_data()
        $customer_info->sbm_get_customer_data($id);
        echo '<h2>customer '.$customer_info->first_name.' '.$customer_info->last_name.'</h2>';
        echo '<form method="post" id="editcustomerForm">';

        echo '<fieldset style="border: 1px solid #000; padding: 10px; ">';

        echo '<div>What is the status of this customer <select id="selected_customer_status" name="customer_status">';
        if(isset($_POST['customer_status']))
        {
            // The submit button has been used but there were errors, select that option for the user again
            switch($_POST['customer_status'])
            {
                case 'Current'; $Current = 'selected="selected"';
                break;
                case 'Past'; $Past = 'selected="selected"';
                break;
                case 'Prospective'; $Prospective = 'selected="selected"';
                break;
                case 'Unassigned': $Unassigned = 'selected="selected"';
                default;
                    break;
            }
        }
        else
        {
            if(!empty($_GET['id']))
            {
                // This is an existing customer, and the submit button has not been used
                switch($customer_info->customer_status)
                {
                    case 'Current'; $Current = 'selected="selected"';
                    break;
                    case 'Past'; $Past = 'selected="selected"';
                    break;
                    case 'Prospective'; $Prospective = 'selected="selected"';
                    break;
                    default;
                        break;
                }
            }
            else
            {
                // This must be a new customer and the submit button has not been used.
                $Current = 'selected="selected"';
            }
        }

        echo '<option '.$Current.' value="Current">Current</option>';
        echo '<option '.$Past.' value="Past">Past</option>';
        echo '<option '.$Prospective.' value="Prospective">Prospective</option>';
        echo '<option '.$Unassigned.' value="Unassigned">Unassigned</option>';


        echo '</select></div>';
        echo '<div><input type="hidden" id="customer_id" name="customer_id" value="'.$id.'"></div>';




        if( $customer_info->customer_status == 'Past' )
        {
            echo '<h3>Previous Information: </h3>';
        }
        else if ( $customer_info->customer_status == 'Current' )
        {
            echo '<h3>Current Information: </h3>';
        }
        else
        {
            // This should not appear if the choice is prospective...

        }


        // If currency is NOT set use the default
        if(!empty( $customer_info->currency ) )
        {
            $currency = $customer_info->currency;
            $currency_symbol = get_option( 'sbm_currency' );

        }
        ?>


    <div id="customer_information_edit" class="float-left">
        <h2>Customer Information</h2>
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
            <div class="description" id="email_1_description">Email #1</div>
            <input type="text" id="email_1" name="email_1" size="30" value="<?php echo sbm_sticky_input($_POST['email_1'], $customer_info->email_1); ?>">
        </div>
        <h3>Password</h3>
        <div>If this is set, the user name for your customer will be their email address.</div>
        <div>When the log in, they will only see their account information.</div>
        <div>
            <div class="description">Password:</div>
            <input type="text" id="customer_password" name="password" size="30" value="<?php echo sbm_sticky_input($_POST['password'], $customer_info->password); ?>">
        </div>

    </div>

    <div class="float-left" style="padding-left: 10px;">
        <h2 class="clear">Secondary customer Information</h2>
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



    <div style="clear: left;"></div>
    <h2>Custom Attributes</h2>
    <div id="custom_attributes">
        <?php
        $custom_content = array();
        $i = 1;
        foreach($customer_info as $key => $item)
        {
            switch ($key) {
                case 'customer_status':
                case 'company_name':
                case 'first_name_1':
                case 'last_name_1':
                case 'address':
                case 'address_2':
                case 'city':
                case 'state':
                case 'zip':
                case 'main_phone':
                case 'secondary_phone':
                case 'fax':
                case 'email_1':
                case 'first_name_2':
                case 'last_name_2':
                case 'email_2':
                case 'hourly_rate':
                case 'tax_rate':
                case 'password':
                case 'notes':
                    break;

                default:
                    $remove_slug = str_replace("_", " ", $key);
                    $custom_content[] = '<div id="customer_attribute_' . $i . '"> <div class="description">' . $remove_slug . ':</div>
                        						 <input type="text" name="' . $key . '" size="30" value="' . $item . '"> ( <a href="javascript: void(0);" class="remove-custom-attribute" id="' . $i . '">remove</a> )</div>';
                    $i++;
                    break;
            }

        }
        if( count($custom_content) > 0 )
        {
            $next_div = 1;
            foreach( $custom_content as $list )
            {
                echo $list;
                $next_div++;
            }
        }

        echo '<div id="customer_attribute_' . $i . '"></div>';
        ?>

        <a href="javascript: void(0);" id="add_attribute">Add Custom Attribute</a>
    </div>
    <h2>Hourly Rate</h2>
    <div>
        <div class="description">Hourly Rate:</div>
        <input type="text" name="hourly_rate" size="3" maxlength="3" value="<?php echo sbm_sticky_input($_POST['hourly_rate'], $customer_info->hourly_rate); ?>">
        <span class="smaller_text">(e.g. 40 for <?php echo $currency_symbol; ?>40 per hour)</span>
    </div>
    <div>
        <div class="description">Tax Rate:</div>
        <input type="text" name="tax_rate" size="3" value="<?php echo sbm_sticky_input($_POST['tax_rate'], $customer_info->tax_rate); ?>">
        <span class="smaller_text">(e.g. 7.0 for 7%)</span>
    </div>

    <div id="customer_notes_div">
        <h2><?php if(empty($id)) { echo 'Add some customer notes'; } else { echo 'Edit notes if needed'; } ?></h2>
        <?php
        // This gets rid of the escaped slashes if the user has a " or a ' in the notes
        $patterns = array();
        $patterns[0] = "/\\\'/";
        $patterns[1] = '/\\\"/';
        $replacements = array();
        $replacements[0] = "'";
        $replacements[1] = '"';

        //  general_functions.php:     sbm_sticky_input()
        ?>
        <textarea name="notes" id="notes" rows="8" cols="80"><?php echo preg_replace($patterns, $replacements, sbm_sticky_input($_POST['notes'], $customer_info->notes)); ?></textarea>
    </div>

    <div class="float-left medium-padding">
        <?php
        //  general_functions.php:     sbm_check_read_only_user()
        if(  sbm_check_read_only_user() == false  )
        {
            echo '<span><input type="submit" value="Submit" id="editcustomerSubmitButton"></span>';
            //  general_functions.php:     sbm_ok_to_delete()
            if( ( $_GET['status'] != 'new' ) && ( sbm_ok_to_delete('customer_id',$id, 'no') == true ) )
            {
                echo '<span><input type="button" value="Delete this customer" onclick="sbm_verifyDeletecustomer('.$id.');"></span>';
            }
        }
        //  general_functions.php:     sbm_cancel_button()
        echo sbm_cancel_button('sbm_customer_profile', 'cancel');

        //  help_functions.php:     sbm_display_help()
        echo sbm_display_help( 'customer' );
        ?>
    </div>
    <?php
    }
    // End New section

    // If this is Delete then use this section
    if($_GET['status'] == 'delete')
    {


        if(isset($_POST['verify_delete']))
        {

            $customer_info->sbm_delete_customer($_GET['id']);
            die('The attempt to delete this property failed, please contact customer services');

        }
        //  classes/sbm_customer.php:     sbm_get_customer_data()
        $customer_info->sbm_get_customer_data($_GET['id']);
        echo '<div class="wrap">';
        // check to see if we can even delete this
        //  general_functions.php:     sbm_ok_to_delete()
        sbm_ok_to_delete('customer_id', $_GET['id'], 'yes');

        if(!empty($_GET['id']))
        {
            echo '<h2>Delete customer '.$customer_info->first_name_1.' '.$customer_info->last_name_1.'</h2>';
            echo '<div>Deleting this customer will remove all data associated with this customer</div>';
            echo '<form method="post">
							<input type="hidden" name="verify_delete" value="true">';
            echo '<div class="float-left medium-padding">';
            //  general_functions.php:     sbm_cancel_button()
            echo sbm_cancel_button('sbm_view_customer_list', 'cancel');
            echo '<input type="submit" value="Delete">';
            echo '</div>';
            echo '</form>';

        }
        else
        {
            echo '<h2>You need to select a customer before you can use this page</h2>';
        }

    }
    // end Delete
    echo '</fieldset>';
    echo '</form>';
    echo '<div id="output_div"></div>';
    echo '</div>';



}


function sbm_view_customer_account()
{

    /*
     *
     *		@TODO:
     *		The line item as they are displayed are editable, however after you edit that amount, you can not re-edit that value.
     *		Need to figure out why this is the case and fix it so you can edit that amount over and over again until your fingers bleed
     *
     */

    global $wpdb;
    global $current_user;
    $currency_symbol = get_option( 'sbm_currency' );

    // remove any pdfs that are in the uplodas folder
    sbm_remove_letters().

        get_currentuserinfo($current_user->ID);

    $customer_info = new sbm_customer();

    $customer_id = $_GET['customer_id'];

    //  classes/sbm_customer.php:     sbm_get_customer_data()
    $customer_info->sbm_get_customer_data( $customer_id );
    //  classes/sbm_customer.php:     sbm_get_customer_balance()
    $customer_info->sbm_get_customer_balance( $customer_id );


    echo '<div class="wrap">';

    //sbm_pre_array(get_post_custom_keys(5));

    //sbm_pre_array(get_post_meta('5', '_wp_attachment_metadata') );

    if(!empty($_GET['message']))
    {
        //  general_functions.php:     sbm_get_message()
        //  general_functions.php:     sbm_message_details()
        //  general_functions.php:     sbm_clear_notice()

        echo '<div id="message" class="success">'.sbm_get_message($_GET['message']).'</div>';
        // call the function that will remove the success div after 5 seconds
        sbm_clear_notice('message', '5');

    }


    if( !empty( $customer_id ) )
    {
        if(!empty($customer_info->company_name))
        {
            echo '<h2>' . $customer_info->company_name . ' History</h2>';
        }
        else
        {
            echo '<h2>' . $customer_info->first_name_1 . ' ' . $customer_info->last_name_1 . ' history</h2>';
        }

        if(!empty($customer_info->password))
        {
            echo '<h3>Customer Password: ' . $customer_info->password . '</h3>';
        }

        echo '<input type="hidden" id="customer_id" value="' . $customer_id . '">';
        echo '<table class="bw_table" style="margin-top: 8px;">
							<th colspan="6">' . $customer_info->company_name . ' Company Information</th>
							<tr>
								<td>Company Name</td>
								<td>Last Name</td>
								<td>First Name</td>
								<td>Address</td>
								<td>City State Zip</td>
								<td>Balance</td>
							</tr>';
        echo '<tr class="odd_bg">';
        echo '<td>' . $customer_info->company_name . '</td>';
        echo '<td>' . $customer_info->last_name_1 . '</td>';
        echo '<td>' . $customer_info->first_name_1 . '</td>';
        echo '<td>' . $customer_info->address . '</td>';
        echo '<td>' . $customer_info->city . ' ' . $customer_info->state . ' ' . $customer_info->zip . '</td>';
        echo '<td>' . $currency_symbol . '';
        $balance = $customer_info->balance;

        if ($balance < 0 )
        {
            echo '<span id="balance" class="red">' . number_format(str_replace('-', '', $balance), 2) . '</span>';
        }
        if ($balance >= 0 )
        {
            echo '<span id="balance" class="green">' . number_format($balance, 2) . '</span>';
        }

        echo '</td>';
        echo '</tr>';


        echo '</table>';

        // Supplimental customer information
        echo '<table class="bw_table" style="margin-top: 8px;">
							<th colspan="6">Supplimental Company Information</th>
							<tr>
								<td>Phone Numbers</td>
								<td>Email Addresses</td>
								<td>Secondary Contact Info</td>

							</tr>';
        echo '<tr class="odd_bg">';
        echo '<td>
								<div>Main Phone: ' . $customer_info->main_phone . '</div>
								<div>Secondary Phone: ' . $customer_info->secondary_phone . '</div>
								<div>Fax: ' . $customer_info->fax . '</div>
								</td>';
        echo '<td>
								<div>Email 1: ' . $customer_info->email_1 . '</div>
								<div>Email 2: ' . $customer_info->email_2 . '</div>
							</td>';
        echo '<td>
								<div>First Name (#2): ' . $customer_info->first_name_2 . '</div>
								<div>Last Name (#2): ' . $customer_info->last_name_2 . '</div>

								</td>';

        echo '</tr>';


        echo '</table>';

        // Custom Attributes
        echo '<table class="bw_table" style="margin-top: 8px;">
							<th colspan="2">' . $customer_info->company_name . ' Custom Attributes</th>
							<tr>
								<td>Description</td>
								<td>Value</td>
							</tr>';

        $custom_content = array();
        $i = 1;
        foreach($customer_info as $key => $item)
        {
            if( ($i % 2) == 0 )
            {
                // Its Even
                $bg = 'even_bg';
            }
            else
            {
                $bg = 'odd_bg';
            }
            switch ($key) {
                case 'customer_status':
                case 'company_name':
                case 'first_name_1':
                case 'last_name_1':
                case 'address':
                case 'address_2':
                case 'city':
                case 'state':
                case 'zip':
                case 'main_phone':
                case 'secondary_phone':
                case 'fax':
                case 'email_1':
                case 'first_name_2':
                case 'last_name_2':
                case 'email_2':
                case 'hourly_rate':
                case 'tax_rate':
                case 'password':
                case 'notes':
                case 'balance':
                    break;

                default:
                    $remove_slug = str_replace("_", " ", $key);
                    $custom_content[] = '<tr class="' . $bg . '"><td>' . $remove_slug . '</td>
                        						 <td>' . $item . '</td></tr>';
                    break;
            }
            $i++;
        }
        if( count($custom_content) > 0 )
        {
            foreach( $custom_content as $list )
            {
                echo $list;
            }
        }
        else
        {
            echo '<tr><td colspan="2">No custom attributes</td></tr>';
        }


        echo '</table>';


        $patterns = array();
        $patterns[0] = "/\\\'/";
        $patterns[1] = '/\\\"/';
        $replacements = array();
        $replacements[0] = "'";
        $replacements[1] = '"';

        echo '<table class="bw_table" id="rates" style="margin-top: 8px;">
							<tr>
								<td style="width: 100px;">Hourly Rate</td>
								<td style="width: 100px;">Tax Rate</td>
								<td>Notes</td>
						</tr>
						<tr class="odd_bg">
								<td style="width: 100px; vertical-align: top;">' . $currency_symbol . '' . $customer_info->hourly_rate . '</td>
								<td style="width: 100px; vertical-align: top;">' . $customer_info->tax_rate . '%</td>
								<td>' . nl2br( preg_replace($patterns, $replacements, $customer_info->notes)) . '</td>
						</tr>
						</table>';


        echo '<table class="bw_table" id="invoices" style="margin-top: 8px;">
					<th colspan="10">View invoices for ' . $customer_info->company_name . '</th>
					<tr class="descriptions">
						<td>Download PDF</td>
						<td class="center-text">Invoice #</td>
						<td>Date</td>
						<td>Total</td>
						<td>Paid</td>
						<td>Balance</td>
						<td>Status</td>
						<td>Update / Pay Invoice</td>
					</tr>';

        // NULL will show all invoices
        echo sbm_get_invoice_list_by_customer_id( NULL, $customer_id );

        echo '</table>';


        //  functions/customer_functions.php:     sbm_display_letters_for_customer()
        echo sbm_display_letters_for_customer( $customer_id );

        echo '<div class="clear-both float-left medium-padding">';
        echo '<form>';
        //  functions/general_functions.php:     sbm_check_read_only_user()
        if( sbm_check_read_only_user() == false )
        {
            // Not going to impliment this now
            //echo '<input type="button" value="Apply a Misc Charge or Credit" onclick="self.location=\'admin.php?page=sbm_misc_customer&id=' . $customer_id . '\'">';
            echo '<span><input type="button" value="Edit this customer" onclick="self.location=\'admin.php?page=sbm_edit_customer&id=' . $customer_id . '\'"></span>';
            echo '<span><input type="button" value="Create Invoice for this customer" onclick="self.location=\'admin.php?page=sbm_create_invoice&status=new&customer_id=' . $customer_id . '\'"></span>';

        }
        //  general_functions.php:     sbm_cancel_button()
        echo sbm_cancel_button('sbm_customer_profile', 'cancel');

        echo '</form>';

        echo '</div>';
        echo '<div id="output"></div>';
        echo '</div>';
    }
    else
    {
        //  general_functions.php:     sbm_redirect()
        sbm_redirect('sbm_view_customer_list');
    }

}

function sbm_adjust_accounting_record()
{
    global $wpdb;
    global $current_user;

    get_currentuserinfo($current_user->ID);



    echo '<div class="wrap">';

    if(isset($_POST['amount_due']))
    {

        if ( $_POST['apply_as_credit'] == 'true' )
        {

            // Update the sbm_customer_account table, this should be the only table affected
            $query = "UPDATE " . $wpdb->prefix . "sbm_customer_account SET amount_due = '{$_POST['amount_due']}' WHERE ID = '{$_POST['customer_account_id']}'";
            $wpdb->query($query);
            $msg = 'success_adjust_account';

        }
        else
        {
            // This is NOT a credit alone so we also need to update the sbm_payments table along with the sbm_customer_account table
            // First Step, update the sbm_customer_account table
            $query = "UPDATE " . $wpdb->prefix . "sbm_customer_account SET amount_due = '{$_POST['amount_due']}' WHERE ID = '{$_POST['customer_account_id']}'";
            $wpdb->query($query);

            // Now update the sbm_payments table
            $query = "UPDATE " . $wpdb->prefix . "sbm_payments SET amount_paid = '{$_POST['amount_paid']}' WHERE ID = '{$_POST['payments_id']}'";
            $wpdb->query($query);

            $msg = 'success_adjust_account';

        }
        // now take the user back to the view customer account page
        //  general_functions.php:     sbm_redirect()
        sbm_redirect('sbm_view_customer_account&customer_id=' . $_POST['customer_id'], $msg );

    }

    if( empty($_GET['customer_account_id']))
    {
        echo '<div>You need to select an item from a customer accounting history to modify before using this page</div>';

    }
    else
    {

        // WE may have a payment ID, if so, we need to use that to display the proper payment information
        if(!empty( $_GET['payment_id'] ))
        {

            $add = "AND ".$wpdb->prefix."sbm_payments.ID = '{$_GET['payment_id']}'";

        }
        else
        {
            $add = null;
        }


        $query = "SELECT
                                ".$wpdb->prefix."sbm_customer_account.meta_id,
                                ".$wpdb->prefix."sbm_customer_account.transaction_id as customer_account_transaction_id,
                                ".$wpdb->prefix."sbm_customer_account.customer_id,
                                ".$wpdb->prefix."sbm_customer_account.amount_due,
                                ".$wpdb->prefix."sbm_customer_account.transaction_date,
                                ".$wpdb->prefix."sbm_customer_account.description,
                                ".$wpdb->prefix."sbm_customer_account.ID as customer_account_id,
                                ".$wpdb->prefix."sbm_payments.ID as payments_id,
                                ".$wpdb->prefix."sbm_payments.amount_paid,
                                ".$wpdb->prefix."sbm_payments.transaction_id as payments_transaction_id,
                                ".$wpdb->prefix."sbm_payments.bounced,
                                ".$wpdb->prefix."sbm_payments.bounced_date,
                                ".$wpdb->prefix."sbm_payments.check_number,
                                ".$wpdb->prefix."sbm_payments.paid_with,
                                ".$wpdb->prefix."sbm_payments.payment_date
                        FROM
                                ".$wpdb->prefix."sbm_customer_account
                        LEFT JOIN
                                ".$wpdb->prefix."sbm_payments
                        ON
                                ".$wpdb->prefix."sbm_customer_account.ID = ".$wpdb->prefix."sbm_payments.customer_account_id
                        WHERE
                                ".$wpdb->prefix."sbm_customer_account.ID = '{$_GET['customer_account_id']}'

                                $add
                        LIMIT 1";

        $customer_list = $wpdb->get_row($query);

        //  general_functions.php:     sbm_pre_array()
        //sbm_pre_array( $customer_list );

        // Amount Due should always have a value so lets output the information
        if( empty( $customer_list->amount_due ) )
        {
            echo 'There is no amount due, there was an error!';
            exit();
        }


        if ($_GET['status'] != 'delete')
        {
            echo '<div id="messages"></div><div class="clear"></div>';

            echo '<form id="adjust_accounting_record" method="post">';

            // Details for the amount due
            echo '<div>Amount Due: <input class="required number" type="text" name="amount_due" id="amount_due" value="' . str_replace( "-", "", $customer_list->amount_due ) . '"></div>';

            // Check to see if amount_paid has a value, and if so, output the data to modify that.
            if(( !empty( $customer_list->amount_paid )) && ( $customer_list->amount_due > 0 ) )
            {

                switch( $customer_list->paid_with )
                {
                    case '0';

                        $cash_selected     = 'selected="selected"';
                        $check_selected    = '';

                        break;
                    case '1';

                        $cash_selected     = '';
                        $check_selected    = 'selected="selected"';

                        break;
                    default;
                        // default should never be used
                        break;
                }

                echo '<div>Amount Paid: <input class="required number" type="text" name="amount_paid" id="amount_paid" value="' . $customer_list->amount_paid . '"></div>';
                echo '<div>Check Number: <input class="number" type="text" name="check_number" id="check_number" value="' . $customer_list->check_number . '"></div>';
                echo '<div>Paid With: <select class="required" name="paid_with" id="paid_with">

                                                    <option ' . $cash_selected . ' value="0">Cash</option>
                                                    <option ' . $check_selected . ' value="1">Check</option>
                                                </select>

                                </div>';
                echo '<input type="hidden" id="apply_as_credit" name="apply_as_credit" value="false">';
                echo '<input type="hidden" id="payments_id" name="payments_id" value="' . $customer_list->payments_id  . '">';


            }
            else
            {
                echo '<div>If this needs to be credited to the customer account make sure you put a negative sign in front of the number</div>';
                // Create some hidden fields so we can do our jquery with no errors
                echo '<input type="hidden" id="amount_paid">';
                echo '<input type="hidden" id="check_number">';
                echo '<input type="hidden" id="paid_with">';
                echo '<input type="hidden" id="apply_as_credit" name="apply_as_credit" value="true">';


            }
            echo '<input type="hidden" id="customer_account_id" name="customer_account_id" value="' . $customer_list->customer_account_id . '">';
            echo '<input type="hidden" id="customer_account_id" name="customer_id" value="' . $customer_list->customer_id . '">';
            echo '</form>';

            echo '<div class="float-left medium-padding">';
            //  general_functions.php:     sbm_check_read_only_user()
            if(  sbm_check_read_only_user() == false  )
            {
                echo '<span><input type="submit" id="sumbit_button" value="submit"></span>';
                echo '<span><input type="button" id="delete" value="Delete"></span>';

            }
            //  general_functions.php:     sbm_cancel_button()
            echo sbm_cancel_button('property_management', 'cancel');
            //  help_functions.php:     sbm_display_help()
            echo sbm_display_help( 'adjust_accounting_record' );
            echo '</div>';

        }
        // If this is Delete then use this section
        if ($_GET['status'] == 'delete') {

            if (isset($_POST['verify_delete']))
            {

                die('The attempt to delete this property failed, please contact customer services');
            }

            echo '<h2>Delete transaction ' . $property_info->name . '</h2>';
            echo '<div>Amount Paid: ' . $customer_list->amount_paid . '</div>';
            echo '<div>Amound Due: ' . $customer_list->amount_due . '</div>';
            echo '<div>Check #: ' . $customer_list->check_number . '</div>';
            echo '<div>Deleting this will remove all data associated with this accounting record</div>';
            echo '<form method="post">
							<input type="hidden" name="verify_delete" value="true">';
            echo '<div class="float-left medium-padding">';
            //  general_functions.php:     sbm_cancel_button()
            echo sbm_cancel_button('sbm_view_customer_list', 'cancel');
            echo '<input type="submit" value="Delete">';
            echo '</div>';
            echo '</form>';


        }


    }


    echo '</div>';

}

function sbm_list_letters_for_customer()
{

    global $wpdb;
    global $current_user;

    $customer_id = $_GET['customer_id'];

    echo '<div class="wrap">';

    if( empty( $_GET['customer_id']))
    {
        // take the user to the view customer list
        //  general_functions.php:     sbm_redirect()
        sbm_redirect('sbm_view_customer_list');
        //echo '<h2>This page was reached in error</h2>';
        die();
    }
    else
    {
        //  customer_functions.php:     sbm_display_letters_for_customer()
        echo sbm_display_letters_for_customer( $customer_id );
    }

    echo '</div>';
}

function sbm_display_letters_for_customer( $customer_id )
{
    // We want to clean out the folder that keeps the temporary pdfs
    sbm_remove_letters();

    global $wpdb;
    $customer_info = new sbm_customer();

    //  classes/sbm_customer.php:     sbm_get_customer_data()
    $customer_info->sbm_get_customer_data($customer_id);

    $uploads = wp_upload_dir();
    $uploadDir = $uploads['basedir'].'/simple-business-manager/';
    $uploadURL = $uploads['baseurl'].'/simple-business-manager/';
    $bg = 'even_bg';

    $content = '<table class="bw_table" style="margin-top: 8px;">
						<th colspan="4">Letters for ' . $customer_info->company_name . '</th>
						<tr>
							<td>Download PDF </td>
							<td>Quick View</td>
							<td>Title</td>
							<td>Date Sent</td>
						</tr>';

    $query =  "SELECT
			".$wpdb->prefix."sbm_sent_letter.sent_date,
			".$wpdb->prefix."sbm_sent_letter.letter_id,
			".$wpdb->prefix."sbm_letter_content.title
		FROM
			".$wpdb->prefix."sbm_sent_letter,
			".$wpdb->prefix."sbm_letter_content
		WHERE

			".$wpdb->prefix."sbm_sent_letter.customer_id =$customer_id
		AND
			".$wpdb->prefix."sbm_letter_content.ID = ".$wpdb->prefix."sbm_sent_letter.letter_content_id";

    $result = $wpdb->get_results($query);

    foreach( $result as $list )
    {


        switch($bg)
        {
            case 'even_bg'; $bg = 'odd_bg';
            break;
            case 'odd_bg'; $bg = 'even_bg';
            break;
            default;
                $bg = 'odd_bg';
                break;
        }



        $content .= '<tr class="' . $bg . '">
						<td>
								<div id="download_pdf_div_' . $list->sent_date . '"><a class="quick_pdf_link" href="javascript: void(0);">Click to prepare download</a></div>


						</td>
						<td>
							<form id="' . $list->sent_date . '" class="td_form">
								<div><a class="quick_view_link" href="javascript: void(0);">Quickview</a></div>
								<input type="hidden" class="letter_id" value="' . $list->letter_id . '">
								<input type="hidden" class="sent_date" value="' . $list->sent_date . '">
								<input type="hidden" class="customer_id" value="' . $customer_id . '">
							</form>
						</td>
						<td>' . $list->title . '</td>
						<td>' . date( "m/d/Y H:i:s", $list->sent_date ) . '</td>
					</tr>';
    }
    if (count( $result ) == 0 )
    {

        $content .= '<tr><td colspan="4" class="odd_bg" style="text-align: center;">No letters have been generated for this customer</td></tr>';

    }

    $content .= '</table><br>&nbsp;</br>';
    $content .='<div id="download_pdf_div" style="display: none;"></div>
		  			<div id="quick_view_div" style="display: none;" class="button_decoration"><a href="javascript: void(0);" id="quick_view_document">
						<img src="' . SBM_PLUGIN_URL . '/images/quick_view.png"></a>
					</div>
		  			<div id="quick_view" class="clear"></div>';

    return $content;
}



function sbm_count_customers()
{
    global $wpdb;
    global $current_user;

    $query = "SELECT
											COUNT(*)
										FROM
											".$wpdb->prefix."sbm_customer
										 ";
    $count = $wpdb->get_var($wpdb->prepare($query));

    return $count;
}
function sbm_get_customer_status($id)
{
    global $wpdb;
    $query = "SELECT
								meta_value
							FROM
								".$wpdb->prefix."sbm_meta
							WHERE
								meta_key = 'customer_status'

							AND
								customer_id = $id";
    $result = $wpdb->get_var($wpdb->prepare($query));

    return $result;
}

?>