<?php

class sbm_odometer {
	
	public function sbm_create_odometer()
	{
		global $wpdb;
		
		$wpdb->insert(  $wpdb->prefix."sbm_odometer", array(  'visible'=> '1' ), array( '%d' ) );
			
		return $wpdb->insert_id;
		
	}
	
    public function sbm_get_odometer_data( $id )
    {
        global $wpdb;
		
		
            $query = "SELECT
                            meta_value,
                            meta_key
                      FROM
                        " . $wpdb->prefix . "sbm_odometer_meta
                      WHERE
                       " . $wpdb->prefix . "sbm_odometer_meta.odometer_id = $id";
            $result = $wpdb->get_results($query);
			
            foreach($result as $row)
            {
                    $key 				= $row->meta_key;
                    $value 				= $row->meta_value;
                    $this->$key 		= $value;
                
            }
				
    }
	
    public function sbm_get_odometer_payee_payer_id( $id )
    {
        global $wpdb;
            $query = "SELECT
                            payee_payer_id
                      FROM
                        " . $wpdb->prefix . "sbm_odometer
                      WHERE
                       " . $wpdb->prefix . "sbm_odometer.ID = $id";
            $result = $wpdb->get_var($wpdb->prepare($query));

            return $result;

    }


	

	public function sbm_delete_odometer($odometer_id)
	{
		global $wpdb;

			$query = "DELETE 
						odometer, 
						meta
					FROM
						".$wpdb->prefix."sbm_odometer AS odometer
						
					LEFT JOIN
						".$wpdb->prefix."sbm_odometer_meta AS meta
					ON 
						odometer.ID = meta.odometer_id 
					WHERE 
						odometer.ID = '$odometer_id'";
			
			$wpdb->query($query);
			// Clean up any old entries that dont have odometer ID ( abandoned entries prior to fix )
			$query = "DELETE 
						odometer, 
						meta
					FROM 
						".$wpdb->prefix."sbm_odometer_meta as meta 
					LEFT JOIN 
						".$wpdb->prefix."sbm_odometer AS odometer 
					ON 
						meta.odometer_id = odometer.ID 
					WHERE 
						odometer.ID IS NULL";
			$wpdb->query($query);
			// redirect	
			//  general_functions.php:     sbm_redirect()
			sbm_redirect('sbm_view_odometer_list', 'delete_odometer');
	
	}
	
	
	

	public function sbm_update_odometer()
	{

		global $wpdb;
		global $current_user;
		 get_currentuserinfo($current_user->ID);
		
		
		if(!empty($_POST['odometer_id']))
		{
			
			$odometer_id = $_POST['odometer_id'];
			// Remove all old entries
			$query = "DELETE FROM 
								".$wpdb->prefix."sbm_odometer_meta 
							WHERE 
								odometer_id = '{$_POST['odometer_id']}' 
							";
			$wpdb->query($query);
		}
		else
		{
			$odometer_id = sbm_odometer::sbm_create_odometer();	
		}
			
					foreach($_POST as $key => $value)
					{
						
						if(!empty($value))
						{
							switch($key)
							{
								case 'odometer_id':
                                case 'payee_payer_id':
								break;
								default;
							$query = "INSERT INTO 
											".$wpdb->prefix."sbm_odometer_meta
										( 
											odometer_id,
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
							$wpdb->query( $wpdb->prepare( $query , array(  $odometer_id, $key, $value ) ) );
								break;
							}
						}
					
					}

				
					if(empty($_POST['not_visible']))
					{
						$visible = 1; // stays visible
					}
					else
					{
						$visible = 0; // is hidden from data being shown, basically it appears as if this is deleted
					}
                    $trip_date = date("Y-m-d H:i:s", strtotime($_POST['trip_date']));
                    $payee_payer_id = $_POST['payee_payer_id'];
					// Update the original table with the new values
					$query = "UPDATE ".$wpdb->prefix."sbm_odometer SET visible = '$visible', odometer_date = '$trip_date', payee_payer_id = '$payee_payer_id' WHERE ID = '$odometer_id'";
					$wpdb->query($query);	
					
					
					// redirect user back to the view odometer list page
					//  general_functions.php:     sbm_redirect()
					sbm_redirect('sbm_view_odometer_list', 'success_odometer');
						
	}
	

	
}
?>