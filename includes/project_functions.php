<?php
function sbm_list_projects()
{

    echo '<div class="wrap">';
die('Under Development');
    global $wpdb;
    global $current_user;
    $customer_info = new sbm_customer();

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


    $query = "SELECT
                    ".$wpdb->prefix."sbm_project.*

                FROM
                    ".$wpdb->prefix."sbm_project";

    $project_list = $wpdb->get_results($query);

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
            foreach( $project_list as $list )
            {
                    // now explode each result to seperate the information that is pipe delimited
                    $explode2 = explode('|', $meta_content );

                    $result[ $list->customer_id ][ $explode2[0] ] 	= $explode2[1];
                
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
                        <td><a href="admin.php?page=sbm_view_customer_list&sort_by=customer_status&order_by=<?php echo $customer_status_order_by; ?>&filter_by_status=<?php echo $filter_by_status; ?>"><div class="float-left">Status</div><div class="<?php echo $customer_status_arrow; ?>"></div></a></td>
                        <td>View Account</td>
                        <td>Edit/Delete</td>
                        <td>Create Invoice</td>
                        <td><a href="admin.php?page=sbm_view_customer_list&sort_by=last_name&order_by=<?php echo $last_name_order_by; ?>&filter_by_status=<?php echo $filter_by_status; ?>"><div class="float-left">Last Name<div class="<?php echo $last_name_arrow; ?>"></div></div></a></td>
                        <td><a href="admin.php?page=sbm_view_customer_list&sort_by=first_name&order_by=<?php echo $first_name_order_by; ?>&filter_by_status=<?php echo $filter_by_status ; ?>"><div class="float-left">First Name<div class="<?php echo $first_name_arrow; ?>"></div></div></a></td>
                        <td><a href="admin.php?page=sbm_view_customer_list&sort_by=company_name&order_by=<?php echo $company_name_order_by; ?>&filter_by_status=<?php echo $filter_by_status ; ?>"><div class="float-left">Company Name<div class="<?php echo $company_name_arrow; ?>"></div></div></a></td>
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
echo '</div>';

}

function sbm_edit_project()
{

    global $wpdb;
    global $current_user;

    $project_info = new sbm_project();
     get_currentuserinfo($current_user->ID);


    if(!empty($_GET['id']))
    {
        $id = $_GET['id'];
    }
echo '<div class="wrap">';

        if (isset($_POST['project_id']))
        {
            if(isset($_POST['verify_delete']))
            {
                $project_info->sbm_delete_project($_POST['project_id']);
                die('Delete project, if this is visible please contact support');
            }


            $errors = array();


            // If this is new, all the fields are required
            if($_GET['status'] == 'new')
            {
                if(empty($_POST['destination']))
                {
                    $errors[] = 'You forgot the first name.';
                }
                if(empty($_POST['trip_date']))
                {
                    $errors[] = 'You forgot the trip date.';
                }




            }

            // Now check the trip date
                $trip_date 	= explode("/", $_POST['trip_date']);
                $month 		= $trip_date[0];
                $day 		= $trip_date[1];
                $year 		= $trip_date[2];
                if(strlen($month) == 1 )
                {
                    $month = "0$month";
                    $adjust = true;
                }
                if(strlen($day) == 1 )
                {
                    $day = "0$day";
                    $adjust = true;
                }
                if($adjust == true)
                {
                    $_POST['trip_date'] = $month . '/' . $day . '/' . $year;
                }
                // set up the errors if the format is wrong
                if( ( strlen($month) != 2 ) || (sbm_check_month($month) == false ) )
                {
                    $errors[] = 'There is a problem with the trip date, check the month!';
                }
                if( ( strlen($day) != 2 ) || ( sbm_check_day($day) == false ) )
                {
                    die();
                    $errors[] = 'There is a problem with the trip date, check the day!';
                }
                if( strlen($year) != 4 )
                {
                    $errors[] = 'There is a problem with the trip date, check the year!';
                }



                if($_POST['not_duplicate'] != 'true')
                {
                    // Warn if potential match of duplicate record, unless verification has been checked
                    if( sbm_check_for_duplicate_odometer_entry($_POST['odometer_id'], $_POST['trip_date'], $_POST['destination'], $_POST['total_miles'], $_POST['starting_miles'], $_POST['ending_miles'], $_POST['description'] ) == true )
                    {
                        // show possible match and offer over ride
                        $errors[] = 'Possible duplicate?';
                        $show_duplicate_checkbox = true;
                    }
                }
                else
                {
                    // allow it to pass through
                }

            if(empty($errors))
            {
                // we dont want to store this value;
                $_POST['not_duplicate'] = null;

                $odometer_info->sbm_update_odometer();
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
            //  classes/sbm_odometer.php:     sbm_get_odometer_data()
            $project_info->sbm_get_project_data($id);
                echo '<h2>Project '.$project_info->name.'</h2>';
                echo '<form method="post" id="editOdometerForm">';
                echo '<fieldset style="border: 1px solid #000; padding: 10px; ">';

                echo '<div><input type="hidden" id="odometer_id" name="odometer_id" value="'.$id.'"></div>';

die('Under Development!');
                ?>


                <div id="project_information_edit" class="float-left">
                    <h2>Project Information</h2>
                    <div>
                        <label for="date_submitted">Date Submitted<em>*</em></label>
                        <input type="text" autocomplete="off" id="date_submitted" class="required hasDatePicker" name="date_submitted" size="20" value="<?php echo sbm_sticky_input($_POST['date_submitted'], $project_info->date_submitted); ?>">
                    </div>
                    <div>
                        <label for="destination">Destination<em>*</em></label>
                        <input type="text" autocomplete="off" id="destination" class="required" name="destination" size="20" value="<?php echo sbm_sticky_input($_POST['destination'], $odometer_info->destination); ?>">
                    </div>
                    <div>
                        <label for="total_miles">Total Miles<em>*</em></label>
                        <input type="text" autocomplete="off" id="total_miles" class="required number" name="total_miles" size="20" value="<?php echo sbm_sticky_input($_POST['total_miles'], $odometer_info->total_miles); ?>">
                    </div>
                    <div>
                        <label for="starting_miles">Starting Miles</label>
                        <input type="text" autocomplete="off" id="starting_miles" name="starting_miles" size="20" value="<?php echo sbm_sticky_input($_POST['starting_miles'], $odometer_info->starting_miles); ?>">
                    </div>
                    <div>
                        <label for="ending_miles">Ending Miles</label>
                        <input type="text" autocomplete="off" id="ending_miles" name="ending_miles" size="20" value="<?php echo sbm_sticky_input($_POST['ending_miles'], $odometer_info->ending_miles); ?>">
                    </div>

                </div>

                <div class="clear"></div>
                <div class="float-left medium-padding">
                <?php
                //  general_functions.php:     sbm_check_read_only_user()
                if(  sbm_check_read_only_user() == false  )
                {
                        echo '<span><input type="submit" value="Submit" id="editOdometerSubmitButton"></span>';
                        //  general_functions.php:     sbm_ok_to_delete()
                    if( ( $_GET['status'] != 'new' ) && ( sbm_ok_to_delete('odometer_id',$id, 'no') == true ) )
                        {
                            echo '<span><input type="button" value="Delete this odometer" onclick="sbm_verifyDeleteodometer('.$id.');"></span>';
                        }
                }
                //  general_functions.php:     sbm_cancel_button()
                echo sbm_cancel_button('sbm_view_odometer_list', 'cancel');

            //  help_functions.php:     sbm_display_help()
            echo sbm_display_help( 'enter_odometer' );
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

                $odometer_info->sbm_delete_odometer($_GET['id']);
                die('The attempt to delete this property failed, please contact odometer services');

            }
                     //  classes/sbm_odometer.php:     sbm_get_odometer_data()
                    $odometer_info->sbm_get_odometer_data($_GET['id']);
            echo '<div class="wrap">';
            // check to see if we can even delete this
            //  general_functions.php:     sbm_ok_to_delete()
            sbm_ok_to_delete('odometer_id', $_GET['id'], 'yes');

            if(!empty($_GET['id']))
            {
                echo '<h2>Delete odometer '.$odometer_info->trip_date.' '.$odometer_info->destination.' '.$odometer_info->total_miles.'</h2>';
                echo '<div>Deleting this odometer will remove all data associated with this odometer</div>';
                echo '<form method="post">
                            <input type="hidden" name="verify_delete" value="true">';
                echo '<div class="float-left medium-padding">';
                //  general_functions.php:     sbm_cancel_button()
                echo sbm_cancel_button('sbm_view_odometer_list', 'cancel');
                echo '<input type="submit" value="Delete">';
                echo '</div>';
                echo '</form>';

            }
            else
            {
                echo '<h2>You need to select a odometer before you can use this page</h2>';
            }

    }
            // end Delete
            echo '</fieldset>';
            echo '</form>';
            echo '<div id="output_div"></div>';
        echo '</div>';


	    
}

?>