jQuery.noConflict();
jQuery(document).ready(function($) {
	
	// jQuery for our plugin but only when the DOM is ready

    // Unassigned customers
    $('#select_all_unassigned').change(function(){

        if($(this).is(':checked'))
        {

            $('.check_box_add_customer').attr('checked', true);
        }
        else
        {

            $('.check_box_add_customer').attr('checked', false);
        }

    })


    // Invoices

        $("#cancel_invoice").click( function() {
            var newText = '';

            if ( $(this).is(':checked') )
            {
                newText = 'Cancel Invoice';
                $(".drop_delete_text").fadeOut( 100 );
                $("#messaging").html( 'Great! You chose to cancel this invoice, click again to change back to delete!' );
            }
            else
            {
                newText = 'Delete Invoice';
                $(".drop_delete_text").fadeIn( 100 );
                $("#messaging").html( 'Check this box IF you would rather cancel This Invoice do not delete it' );
            }
            $("#delete_invoice_submit_button").val( newText );
            $(".change_delete_text").html( newText );

        });
		
		$("form#payInvoice select#paid_with").change( function() {
			
			
			
			if( $("select option:selected").val() != 'check' )
			{
				$("#check_number").closest( 'div' ).css( 'display', 'none' );
				$("#check_number").removeClass( 'required' ).fadeOut( 500 );
				
			}
			else
			{
				$("#check_number").closest( 'div' ).css( 'display', 'block' );
				$("#check_number").addClass( 'required' ).fadeIn( 500 );
				
			}
			
		});
		
			
	    $("div.update_invoice a.convert_pending_to_invoiced").live('click', function() {
	        
			var invoice_id 	= $(this).closest( 'tr' ).find( ':input.invoice').val();
			var customer_id = $(this).closest( 'tr' ).find( ':input.customer').val();
	        $.post("./admin-ajax.php", {
	            action:"sbmConvertInvoice",
	            "customer_id": customer_id,
	            "invoice_id": invoice_id
				
	        }, function( row_id )	{
				
				//sbm_useConsole( 'row id = ', row_id );
				$("div#convert_invoice_" + row_id).fadeOut( 500 );
				$("td#update_" + row_id).html( '<a href="./admin.php?page=sbm_pay_invoice&invoice_id=' + row_id +'&customer_id=' + customer_id + '">Pay</a>' );
				$("td#status_" + row_id).html( 'invoiced' );
	          	$("td#click_to_edit_invoice_" + row_id).html( '' );
			   
	        });
			
	    });	
		
	    $("select#filter_invoices_by_year").change( function() {

			var year = $("select option:selected").val();
	
			// update total invoice information
	        $.post("./admin-ajax.php", {
	            action:"sbmShowInvoiceTotalsDifferentYear",
	            "year": year
	            				
	        }, function( data )	{
				
				var arr = data.split('|');
				// the first element of the array is the total number of invoices, the second is the new total amount for all the invoices for that year
	          	$("#year_invoices_total_amount").html( arr[0] );
				$("#year_number_of_paid_invoices").html( arr[1] );
			   
	        });
	
            Show_paid_invoice_totals_different_year(year);
			
			
			$("span.show_year").html( year );
			
	    });
        function Show_paid_invoice_totals_different_year(year){
            			// update paid invoice information
	        $.post("./admin-ajax.php", {
	            action:"sbmShowPaidInvoiceTotalsDifferentYear",
	            "year": year

	        }, function( paiddata )	{

				var paidarr = paiddata.split('|');
				// the first element of the array is the total number of invoices, the second is the new total amount for all the invoices for that year
	          	$("#total_number_of_paid_invoices").html( paidarr[0] );
				$("#paid_invoices_total_amount").html( paidarr[1] );

	        });
            show_total_miles_for_year( year );

        }

        function show_total_miles_for_year( year )
        {

            $.post("./admin-ajax.php", {
            	            action:"sbmGetMilesForyear",
            	            "year": year

            	        }, function( totalMiles )	{

            	          	$("#total_number_of_miles_for_year").html( totalMiles );

            	        });


        }
		$("form#payInvoice #payment_date").datepicker();
		
		$("td a.convert_to_invoice").live( 'click', function() {
			
			var invoice_id 	= $(this).closest( 'tr' ).find( ':input.invoice').val();
			var customer_id = $(this).closest( 'tr' ).find( ':input.customer').val();
			$(this).closest( 'tr' ).find( '.convert_invoice' ).fadeIn( 500 );
			
			return false;
		});
		
		$(".close_update_invoice").live( 'click', function() {
			
			var invoice_id 	= $(this).closest( 'tr' ).find( ':input.invoice').val();
			var customer_id = $(this).closest( 'tr' ).find( ':input.customer').val();
			$(this).closest( 'tr' ).find( '.convert_invoice' ).fadeOut( 500 );
			
			return false;
		});
		
		
		
		
		
		
		//sbm_useConsole( 'the value is ' , $("#invoice_type").val() );
		// make all the hourly classes visible if invoice_type is set to hourly
		if( $("select#invoice_type").length == 1 )
		{
			// check to see what its current value is
			if( $("#invoice_type").val() == 'hourly' )
			{
				$(".copyText").fadeOut( 1 );
				$(".hourly").fadeIn( 500 );
			}
			else
			{
				$(".copyText").fadeIn( 1 );
				$(".hourly").fadeOut( 500 );
			}
		}
		
		$("input.copyText").live( 'keyup', function() {
		
			var newAmount 	= $(this).val();
			var updateInput = $(this).closest( 'tr' ).find( ':input.input_qty' ).attr( 'id' );
			var updateSpan 	= $(this).next( 'span' ).attr( 'id' );
			//sbm_useConsole( 'input = ' + updateInput + ' updateSpan = ' + updateSpan );
			
			$("#" + updateInput).val( newAmount );
			$("#" + updateSpan).html( newAmount );
			
		});
		
		
		$("select#invoice_type").change( function() {
			
			if( $(this).val() == 'hourly' )
			{
				$(".copyText").fadeOut( 1 );
				$(".hourly").fadeIn( 500, function() {
					
				});
				
				
			}
			else
			{
				$(".hourly").fadeOut( 1 );
				$(".copyText").fadeIn( 500, function() {
					
				});
				
			}
			
		});
		
		// choose the customer for the invoice
		$("input#choose_customer").live( 'keyup', function() {
			
			if( $(this).val().length >= 1 )
			{
				$("#customer_suggestions").css( 'display', 'block' );
			
				sbm_suggest_customers( $(this).val() );
			}
			else
			{
				
				$("#customer_suggestions").fadeOut( 500 );
			}
		});
		
		
		
		$("#update_paid_down").live( 'click', function() {
			
			if( $("#update_paid_down_input").css( 'display' ) == 'none') 
			{
				$("span.paid_down").css( 'display', 'none' );
				$("#update_paid_down_input").css( 'display', 'block' );
				$(this).html( '<span class="bold-text">Save Changes</span>' );
			}
			else
			{
				var newAmount = sbm_convertCurrency( $("#updated_paid_down_value").val() );
				
				$("span.paid_down").css('display', 'block' ).html( '$&nbsp;' + newAmount );
				$("#paid_down").val( newAmount );
				$(this).html( 'update' );
				$("#update_paid_down_input").css( 'display', 'none' );
				
				// update each line item
				sbm_updateLineTotals();
	
			}
			
		});
	
		$("#customer_suggestions div a").live( 'click', function() {
			
			var customer_id 	= $(this).find('input.new_customer_id').val();
			var companyName 	= $(this).find('span.new_company_name').html();
			var firstName 	 	= $(this).find('span.new_customer_first_name').html();
			var lastName	 	= $(this).find('span.new_customer_last_name').html();
			var taxRate 		= Number( $(this).find('input.new_customer_tax_rate').val() );
			var hourlyRate		= Number( $(this).find('input.new_customer_hourly_rate').val() );
			var oldHourlyRate   = $("#hourly_rate").val();
			
			$(".customer_name").html( 'for ' + companyName + ' ' + firstName + ' ' + lastName );
			// Get the rest of the information for the customer
			// it will return the object with all the customer information
			      
			
	        $.post("./admin-ajax.php", {
	            action:"sbmGetCustomerInformation",
	            "customer_id": customer_id
				
	        }, function( result )	{
				
				var arr = result.split('\\');
				for(var i = 0; i<= arr.length; i++ )
				{ 
					//make sure its not undefined
					if(arr[i])
					{
						
						var part = arr[i].split('|');
						
						if( part[0].length > 0 )
						{
							switch(part[0])
							{
								case 'customer_status':
								case 'notes':
								break;
								default:
								// part[0] = div to update
								// part[1] = new information for the div
								
								$("span." + part[0]).html( part[1] );
								//sbm_useConsole( 'Part[0] = ' + part[0] + ' Part[1] = ' + part[1] );
								break;
							}
							
						}
					}
				}
				
	           
	        });
	
			
			// If tax rate is above 0
			if( taxRate > 0 )
			{
				$("input.tax_rate").val( taxRate );	
				$("span.tax_rate").html( taxRate );
			}
			// if hourly rate is is above 0 
			if( hourlyRate > 0 )
			{
				$("#hourly_rate").val( hourlyRate );	
			}
			// change the customer ID
			$("#customer_id").val( customer_id );
			// enter the name of the customer into the input box+ 
			if(companyName.length > 0)
			{
				$("#choose_customer").val( companyName + ' ' + firstName + ' ' + lastName );
			}
			else
			{
				$("#choose_customer").val( firstName + ' ' + lastName );
			}
			
			// hide the customer suggestion, and update the form
			$("#customer_suggestions").fadeOut( 1000, function() {
				// update the customer information
			
			// if the hourly rate changed update the current invoice
			if( oldHourlyRate != hourlyRate )
			{
				$("input.input_price").each( function() {
					if( $(this).closest( 'tr').find( 'input.input_qty' ).val() != '' )
					{
						$(this).val( hourlyRate );	
					}
				});
				
				sbm_updateLineTotals();
			}
				
			});
		});
	
	
		$(".input_start_time").live( 'focus', function() {
					
			$(this).timepicker();
			
		});
		
		
		
		$(".input_end_time").live( 'focus', function() {
			
			$(this).timepicker();
			
		});
			
		
		$('form .line_item_table input.input_taxable').live('change', function() {
			
			// update each line item
			sbm_updateLineTotals();
				 
		});
	
		// update each row as they tab through the rows for the line item form
		$("form#editInvoiceForm table.line_item_table input").live( 'blur', function() {
			
			// update each line item
			sbm_updateLineTotals();
			
		});
	
		$("form#editInvoiceForm #invoiceSubmitButton").click(function() {
														  
			$("#editInvoiceForm").validate();
	
		});
		$("form#payInvoice #payInvoiceSubmitButton").click(function() {
														  
			$("#payInvoice").validate();
	
		});
			
		$("form#editInvoiceForm table.line_item_table :input").live( 'blur', function() {
			
            var qty		        = $(this).closest( 'tr' ).find( 'input.input_qty' ).val();
            var copyTextQtyId   = $(this).closest( 'tr' ).find('input.copyText').attr( 'id' );
            var inputQtyId		= $(this).closest( 'tr' ).find( 'input.input_qty' ).attr( 'id' );
            var startTime		= $(this).closest( 'tr' ).find( 'input.input_start_time' ).val();
			var endTime			= $(this).closest( 'tr' ).find( 'input.input_end_time' ).val();
			var price			= $(this).closest( 'tr' ).find( 'input.input_price' ).val();
			var priceId			= $(this).closest( 'tr' ).find( 'input.input_price' ).attr( 'id' );
			var hourlyRate		= $("#hourly_rate").val();

			// if they left the field empty, insert a 0
			if( ( $(this).val() == '' ) && ( startTime != '' ) && ( endTime != '' ) )
			{
				$(this).val(0.00);
			}
			// now update the price if it has not been set
			if( ( qty.length > 0 ) && ( price.length == 0 ) )
			{
				$("#" + priceId ).val( hourlyRate );
					
			}
			else if ( ( qty.length == 0 ) && ( price.length > 0 ) )
			{
                $("#" + copyTextQtyId).val('1');
                $("#" + inputQtyId).val('1');

			}
            else
            {
                // do nothing for now
            }
            
			// update each line item
			sbm_updateLineTotals();
		});

    // Clear row for invoice
    $("form#editInvoiceForm table.line_item_table a.clear_row").live( 'click', function() {

        $(this).closest( 'tr' ).find(':input.copyText').val( '' );
        $(this).closest( 'tr' ).find( ':input.input_qty' ).val( '' );
        $(this).closest( 'tr' ).find( ':input.hasDatePicker' ).val( '' );
        $(this).closest( 'tr' ).find( ':input.input_status' ).val( '' );
        $(this).closest( 'tr' ).find( ':input.input_start_time' ).val( '' );
        $(this).closest( 'tr' ).find( ':input.input_end_time' ).val( '' );
        $(this).closest( 'tr' ).find( ':input.input_price' ).val( '' );
        $(this).closest( 'tr' ).find( ':input.input_description' ).val( '' );
        
        // uncheck the tax box
        $(this).closest( 'tr' ).find( 'input.input_taxable' ).removeAttr( 'checked' );
        // remove line total
        $(this).closest( 'tr' ).find( '.line_total' ).html( '' );
        // update each line item
		sbm_updateLineTotals();
    });
	// End invoices			
	
	
	// This hides all the empty admin menu items.
	 $("ul#adminmenu ul li a").each( function() {
	    
	        
	    if ($(this).text().length == 0 )
	        {
	            
	            $(this).closest( 'li' ).css( 'display', 'none' ).addClass( 'hidden');
	            
	            
	        }
	 });
	
	$("#clone_invoice_page").live('click', function() {
			
		
		$('#line_item_table_1').clone().appendTo('#line_item' ).addClass( 'new-table' );
		
		var newTotal = $('table.line_item_table').length;
		
		$(".total_pages").html( newTotal );
		$("#last_page_number").val( newTotal );
	           	
	    sbm_updateNewTable( newTotal );
		
	});
	    // check for any notices but only do it 
		// TODO Figure out a better way to check for notices
	   //sbm_checkForNotices();
	  /*  LETTERS */ 
	  $(".letter-submit-toggle").live('change', function() {
		  sbm_disableEnableSubmitPdf();
	  });
				// insert at cursor
			$('form#letter_form .letterInputButton').click(function() {
																	
					// do the insert at the current cursor position, it will default to the top left if the cursor is not in the textarea
					sbm_insertAtCaret('letterArea', $(this).html());
					// Prevents the form from being submitted
					return false;
			});
	
	
				// ends insert at cursor
	  			// validate letter generator
			 $("form#letter_form #submit_letter").click(function() {
															  
				$("#letter_form").validate();
						
			});
		/* END LETTERS */
	
		/* REPORTS */
	  			// validate report generator
			 $("form#report_form #submit_report").click(function() {
															  
				$("#report_form").validate();
						
			});
			 
			 // copy the information over as they type
			 $("form#report_form #report_name").keyup(function() {
				
				if ($("#title_same_as_name").attr("checked") == true )
				{
					$("#report_title").val( $(this).val() );
				}
				
			});
			 
			 // copy the information over if they use the check box after the fact
			 $("form#report_form #title_same_as_name").click( function() {
				
				if ( $(this).attr("checked") == true )
				{
					$("#report_title").val( $("#report_name").val() );		
				}
				else
				{
					$("#report_title").val( null );	
				}
			});
			 
			 // Toggle visibilty of the 3 sections
			 	/* Step 1 */
			$("form#report_form  a#toggle_step_1").toggle(function() {
				
				$(this).html('show');
				$("#step_1").fadeOut( 1000 );							   
				
			}, function() {
				$(this).html('hide');
				$("#step_1").fadeIn( 1000 );						   
		
			});
			 	/* Step 2 */
			$("form#report_form  a#toggle_step_2").toggle(function() {
				
				$(this).html('show');
				$("#step_2").fadeOut( 1000 );							   
				
			}, function() {
				$(this).html('hide');
				$("#step_2").fadeIn( 1000 );						   
		
			});
			 	/* Step 3 */
			$("form#report_form  a#toggle_step_3").toggle(function() {
				
				$(this).html('show');
				$("#step_3").fadeOut( 1000 );							   
				
			}, function() {
				$(this).html('hide');
				$("#step_3").fadeIn( 1000 );						   
		
			});
			
	    $("form#report_form .showDisplayOptions").click(function() {
	        
	       var user_id = '9999';
			alert( $(this).next().attr('id') );
	        $.post("./admin-ajax.php", {
	            action:"sbmDisplayOptions",
	            "user_id": user_id
				
	        }, function(result)	{
				
	            $("#result_div").html( result );
	        });
	    });	
		$("form#report_form .select_button").click( function() {
					
			// Figure out how many elements have already been used, and then add 1 to that number.
			// This way the div's can have seperate ID numbers
			var i = 1;
			$("button").each( function() {
									   
						if( $(this).attr( 'disabled' ) == true )
						{
							i++;
						}
			 });
		
					$(this).attr('disabled', 'disabled');
					$("form#report_form #report_field").append( '<div class="new_item" id="' + i + '"><input type="hidden" name="' + $(this).val() + '" value="' + $(this).val() + '">' + sbm_convertToUserFriendly( $(this).val() ) + '</div>' );
			// Set the width to each field depending on how many were selected
			switch( i )
			{
				case 1:
					$(".new_item").css( {'width': '710px'} );
				break;
				case 2:
					$(".new_item").css( {'width': '355px'} );
				break;
				case 3:
					$(".new_item").css( {'width': '236px'} );
				break;
				case 4:
					$(".new_item").css( {'width': '177px'} );
				break;
				case 5:
					$(".new_item").css( {'width': '142px'} );
				break;
				case 6:
					$(".new_item").css( {'width': '118px'} );
				break;
				case 7:
					$(".new_item").css( {'width': '101px'} );
				break;
				case 8:
					$(".new_item").css( {'width': '88px'} );
				break;
				case 9:
					$(".new_item").css( {'width': '78px'} );
				break;
				case 10:
					$(".new_item").css( {'width': '71px'} );
				break;
				default:
					alert( 'You have selected too many fields, they will not fit on the page.  Please remove one' );
				break;
			}
					
				return false;
		});
			 
		/* END REPORTS */
	    // print page
	    $('#sbm_print_page').click(function() {
	        window.print();
	        return false;
	    });
	
		/* View customer Account */
	        
	        
		$("#accounting_history .toggle_payment_info a.payment_link").toggle(function() {
			
			$(this).html('Hide');
			//$(this).next('div').css('display', 'block');
	                $(this).next('div').fadeIn( "slow" );
			
	    }, function() {
			$(this).html('Payment Info');
			$(this).next('div').fadeOut( "slow" );							   
	
		});
	        
		$("#accounting_history .delete").click( function() {
			
			if ( confirm( 'Are you sure you want to delete this?' ) )
			{
				$rowId = $(this).attr( 'id' );
				
				// Remove all items associated with this customer_account_id using ajax
				$.post("./admin-ajax.php", {
							   
								action:"sbmDeleteTransaction", 
								
								"customer_account_id": $rowId,
								"customer_id": $("#customer_id").val()
								
								
						}, function( newAccountBalanceTotal )	{
						
						// we are getting back the new account balance
						$(".customer_account_id_" + $rowId).fadeOut( 'slow' );
						// update the new account balance
						$("#balance").html( newAccountBalanceTotal );
							
					});
				
				
			}
			
		});
		
		$("#accounting_history .edit_amount_due").click( function() {
			
			
			$(this).parent().next( 'div' ).css('display', 'block' );
			$(this).parent( 'div:first' ).css('display', 'none' );
		});
		
		$("#accounting_history .update_value").click( function() {
			
			// need to validate that the values coming in are numbers only
			
			
			var num = $(this).closest('tr').find('.new_amount_due_value').val();
			var customer_account_id = $(this).closest('tr').find('td a:first').attr('id');
			
			// Do ajax to update the amount_due field then show the new value 
			$.post("./admin-ajax.php", {
							   
								action:"sbmUpdateAmountDue", 
								
								"customer_account_id": customer_account_id,
								"new_amount_due": num,
								"customer_id": $("#customer_id").val()
								
						}, function( newAccountBalanceTotal )	{
						
						
						if ( newAccountBalanceTotal == '0' )
						{
							alert('There was a problem saving the value');
							
						}
						else
						{
							// update the new account balance
							$("#balance").html( newAccountBalanceTotal );
							//$("#customer_account_id_" + customer_account_id ).find('.amount_due_td').css('display', 'block' ).html( '$' + num );
							
							$(".customer_account_id_" + customer_account_id ).each( function() {
								$(this).find('.amount_due_td').css('display', 'block' ).html( '$' + num );
							});
					
						}
						
					});
			
			
							
							$(this).parent().css('display', 'none' );
			
			
		});
		
		
		$(".cancel_update_new_amount").click( function() {
			
			$(this).parent().prev( 'div' ).css('display', 'block' );
			$(this).parent().css('display', 'none' );
			//$(this).parent( 'div:first' ).css('display', 'block' );
			//$(this).parent().next( 'div:last' ).css('display', 'none' );
		});
		
		$("#accounting_history .edit_amount_paid").click( function() {
			
			
			$(this).parent().next( 'div' ).css('display', 'block' );
			$(this).parent( 'div:first' ).css('display', 'none' );
		});
		
		$("#accounting_history .update_paid_value").click( function() {
			
			// need to validate that the values coming in are numbers only
			
			
			var num = $(this).closest('tr').find('.new_amount_paid_value').val();
			var payment_id = $(this).attr('id');
			$(this).closest('tr').find('.amount_paid_td').html('working...');
			
			
			// Do ajax to update the amount_due field then show the new value 
			$.post("./admin-ajax.php", {
							   
								action:"sbmUpdateAmountPaid", 
								
								"payment_id": payment_id,
								"new_amount_paid": num,
								"customer_id": $("#customer_id").val()
								
						}, function( newAccountBalanceTotal )	{
						
						
						if ( newAccountBalanceTotal == '0' )
						{
							alert('There was a problem saving the value');
							
						}
						else
						{
							// update the new account balance
							$("#balance").html( newAccountBalanceTotal );
							//$("#customer_account_id_" + customer_account_id ).find('.amount_due_td').css('display', 'block' ).html( '$' + num );
							
							$('#amount_paid_id_' + payment_id).html( num );
							// also update the payment info div
							$('#payment_info_' + payment_id).html( num );
						
					
						}
						
					});
			
			
							
							$(this).parent().css('display', 'none' );
			
			
		});
		
		
		$(".cancel_update_new_paid").click( function() {
			
			$(this).parent().prev( 'div' ).css('display', 'block' );
			$(this).parent().css('display', 'none' );
			//$(this).parent( 'div:first' ).css('display', 'block' );
			//$(this).parent().next( 'div:last' ).css('display', 'none' );
		});			
		
	        
	        $("#customer_filter_view_customer_list").change(function() {
	            
	           window.location = './admin.php?page=sbm_view_customer_list&filter_by_status=' + $(this).val();
	            
	        });


	        
	        // remove all the results that do not match the filter by property selected
	        $("#property_filter_view_customer_list").change(function() {
	            
	           // If they chose the default, that has no value, show all
	           if( $(this).val().length == 0 )
	               {
	                   
	                   $("tr:not(:visible)").fadeIn( 'fast' );
	                   
	               }
	               else
	               {
	                        $("tr:not(.property_id_" + $(this).val() +")").not(':first').fadeOut( 'fast' );
	            
	                        if( $("tr.property_id_" + $(this).val() +":not(:visible)") )
	                        {
	                            $("tr.property_id_" + $(this).val()).fadeIn( 'fast');
	                        }    
	               }
	           
	           
	                    
	                
	        });
	        
	        
	        
		/* End customer Account */				
	
	        
	
	        /* Adjust accounting record */
	        
	        // Enable submit should the button be disabled becuase of errors
	        
	        $("form#adjust_accounting_record #sumbit_button").click( function() {
	            
	           
	          var override = $("#override").val();
	          //alert(override);
	
	           // Reset the message div
	           $("#messages").html( null ).css('display', 'none');
	           
	           var amount_paid      = $("form#adjust_accounting_record #amount_paid").val();
	           var amount_due       = $("form#adjust_accounting_record #amount_due").val();
	           var check_number     = $("form#adjust_accounting_record #check_number").val();
	           var paid_with        = $("form#adjust_accounting_record #paid_with option:selected").val();
	           var apply_as_credit  = $("form#adjust_accounting_record #apply_as_credit").val();
	           
	           // If paid_with == 0, clear out the value in the check number
	           if( paid_with == 0 )
	           {
	               $("form#adjust_accounting_record #check_number").val( null );
	           }
	           
	           var errors           = new Array();
	           
	           //alert( $("form#adjust_accounting_record #override :checked").is(':checked') );
	           
	           if( ( amount_paid > amount_due ) && ( apply_as_credit == 'false' ) )
	           {
	               errors.push('<div class="error_icon"></div><div>The amound paid is greater than the amound due! ( Click here to override this notice <input type="checkbox" id="override" onChange="javascript: sbm_overRideCheckBox();"> )</div>');                   
	           }
	           
	           if( ( amount_paid < amount_due ) && ( apply_as_credit == 'false' ) )
	           {
	               errors.push('<div class="error_icon"></div><div>The amound due is greater than the amound paid! ( Click here to override this notice <input type="checkbox" id="override"onChange="javascript: sbm_overRideCheckBox();"> )</div>');                            
	           }       
	           
	           // check for a check number, and if there is one, and the selected option is not check, show error
	           if( ( paid_with == '1' ) && ( check_number.length == 0 ) )
	           {           
	               errors.push('<div class="warning_icon"></div><div>You have to have a check number, if you select check as the payment option! ( you CAN NOT submit )</div>');                                        
	               $("form#adjust_accounting_record #check_number").focus();
	           }
	           
	           if( (paid_with == '0') && ( check_number.length > 0 ) )
	           {           
	               errors.push('<div class="warning_icon"></div><div>You can not have a check number if you did not select Check as the payment option! ( you CAN NOT submit )');            
	           }
	           
	           if( ( amount_paid.length == 0 ) && ( apply_as_credit == 'false'  )  )
	           {
	               errors.push('<div class="warning_icon"></div><div>The amout paid section is empty, please enter a number! ( you CAN NOT submit )');                           
	           }
	           
	           if( amount_due.length == 0 )
	           {
	               errors.push('<div class="warning_icon"></div><div>The amout due section is empty, please enter a number! ( you CAN NOT submit )');                           
	           }
	           
	          
	           
	           if( ( errors.length > 0 ) && ( override != 'on' ) )
	           {
	               $("#messages").fadeIn("slow");
	               for(var i = 0; i <= errors.length - 1; i++)
	               {
	                   
	                   $("#messages").append( errors[ i ] );
	                   
	               }
	              // disable the submit button
	              $("form#adjust_accounting_record #sumbit_button").attr('disabled', 'disabled').val('Submit Disabled, Errors Found!');
	              return false;  
	                   
	           }
	           else
	           {
	               // Change the text of the submit button and disable it so they dont hit it again
	               $("form#adjust_accounting_record #sumbit_button").attr('disabled', 'disabled').val('Working, please wait');
	               
	               // We dont find any errors, now validate the form
	               $("form#adjust_accounting_record").validate();
	                
	           } 
	          
	        });
	        
	        // on key up on the check_number field, we are going to force the value to 1 ( for checks )
	        $("form#adjust_accounting_record #check_number").keyup( function() {
	        
	        
	            // check to see if the submit button is disabled, and if so, remove that attribute.
	            if( $("form#adjust_accounting_record #sumbit_button").attr("disabled") == true )
	            {
	                $("form#adjust_accounting_record #sumbit_button").removeAttr('disabled').val('Submit');
	            }
	            $("form#adjust_accounting_record #paid_with option[value='1']").attr('selected', 'selected');
	
	        });  
	        
	        // If paid_with changes to 0 (cash) clear the check_number field
	        $("form#adjust_accounting_record #paid_with").change( function() {
	           
	           if( $(this).val() == '0' )
	            {
	                $("form#adjust_accounting_record #check_number").val( null );
	            }         
	           
	        });
	
	        /* End adjust accounting record */
	    /* PAYEE PAYER */
					
		$("form#editPayeePayerForm #editpayeePayerSubmitButton").click(function() {
														  
			$("#editPayeePayerForm").validate();
	
		});
				
	    /* END PAYEE PAYER */
	    /* ODOMETER */

        $("#change_odometer_year").change( function() {

           window.location = 'admin.php?page=sbm_view_odometer_list&year=' + $(this).val();

        });

        // Save to PDF current year of odometer
        $("#download_csv_odometer").live( 'click', function() {
            var year = $("#change_odometer_year").val();
            if(year.length == 0)
            {
                year = 'all-years';
            }

            $.post("./admin-ajax.php", {
                action:"sbmSaveOdometer",
                "year": year
            }, function( msg )	{


               // we want to output a message that the download button is below
               //$("#show_results_div").html(  );
               // hide the submit button

              $("#csv_ready").html(msg);
                });

        });

		$("form#editOdometerForm #editOdometerSubmitButton").click(function() {
														  
			$("#editOdometerForm").validate();
	
		});
		
		// Show list of options for Destination
		$("form#editOdometerForm #destination").keyup(function() {
			
			if($(this).val().length >= 2)
			{
				// Do ajax to update the amount_due field then show the new value 
				$.post("./admin-ajax.php", {
								   
									action:"sbmSuggestDestination", 
									"destination": $(this).val()
							}, function( suggestions )	{
								// update the new account balance
								$("#destination_suggestions").html( suggestions );
							
						});			
			
			
			}
			else
			{
				// remove the suggestions
				$("#destination_suggestions").html();
			}
			
		});
		// copy over the suggestion
		$("#destination_suggestions div.suggested_destination").live('click', function(){
			
			
			$("#destination").val($(this).html());
			// remove the suggestions
			$("#destination_suggestions").html( 'Max 10 suggestions shown' );
		});
				
	    /* END ODOMETER */
            // Save to csv
        $("#download_csv_deposit_expenses").live( 'click', function() {
            var year = $("#change_deposits_expenses_year").val();
            if(year.length == 0)
            {
                year = 'all';
            }

            $.post("./admin-ajax.php", {
                action:"sbmSaveDepositsExpenses",
                "year": year
            }, function( msg )	{


               // we want to output a message that the download button is below
               //$("#show_results_div").html(  );
               // hide the submit button

              $("#csv_ready").html(msg);
                });

        });


	    /*  Deposits and Expenses  */
		/* change the year if needed */
        $("#change_deposits_expenses_year").change(function() {

           if($(this).val().length > 0)
           {
               window.location = './admin.php?page=sbm_view_deposit_expense&year=' + $(this).val();
           }
            else
           {
               window.location = './admin.php?page=sbm_view_deposit_expense&year=all';
           }


        });

	    // Validate the entire page before submitting
	    $("#enterDepositOrExpense").click(function() {
														  
	        $("#deposit_expense").validate();
	        // call the function to validate a field into currency..it will round up and chop off the extra numbers.
	        sbm_validateCurrency('amount');
	
				
	    });
		
		$("div.close_option a.close_link").live("click", function() {
			$("#payer_payee_suggestion").fadeOut('fast');
	        
		});
				
	    $("#deposit_expense input[type=text]").focus( function() {
											 
	        $("#payer_payee_suggestion").fadeOut('fast');
	        sbm_toggleSuggestion();
						
	    });
			
	    $("#deposit_expense .payer_payee").each(function() {
	        $(this).val(null);
	    });
				
			
	    $("#deposit_expense #payer_payee").keyup( function() {
										
	        if($(this).val().length >= 2)
	        {
	            $("#payer_payee_suggestion").fadeIn('fast');
	            // get similar entries based off what the user has typed
	            sbm_get_suggestions($(this).val(), 'payer_payee_suggestion');
									
	        }
	        if($(this).val().length < 2)
	        {
	            // remove the suggestion box
	            $("#payer_payee_suggestion").fadeOut('fast');
	        }
		
	    });
			
	// show input fields after you close the div
	$("#show_information").live('click', function() {
		 sbm_openPayerPayeeSuggestion();
	});
	   
				
	    // Autocomplete and show similar options
	    $('#deposit_expense #options_link').toggle(function() {
												 
					  
	        $('#options').fadeIn('slow');
	        $('#options_link').html('Hide Options');
	        // if the user clicks the link
	        $('#options .close_link').click(function() {
														 
	            $('#options').fadeOut('slow');
	            $('#options_link').html('Show Options');
			
	        });
				  
	
	
	    }, function() {
						
	        //lease_expiration_div fade back in
	        $('#options').fadeOut('slow');
	        $('#options_link').html('Show Options');
	    });
					
	    // What types of units to show options
	    $('#deposit_expense #unit_options_link').toggle(function() {
												 
	        //lease_expiration_div fade out
	        $('#unit_options').fadeIn('slow');
	        $('#unit_options_link').html('Hide Options');
					  
	        // if the user clicks the link
	        $('#unit_options .close_link').click(function() {
														 
	            $('#unit_options').fadeOut('slow');
	            $('#unit_options_link').html('Show Options');
			
	        });
					  
					  
	        $('#all_or_occupied').change(function() {
	            // adjust the options for the unit and display the new requested type of results all or occupied units
	            sbm_get_unit_details();
						
	        });
	
	    }, function() {
						
	        //lease_expiration_div fade back in
	        $('#unit_options').fadeOut('slow');
	        $('#unit_options_link').html('Show Options');
	    });
					
					
	
					
	    $('#choose_deposit_or_expense').change(function() {
	        var answer = $("#choose_deposit_or_expense option:selected").val();
					
	        switch(answer)
	        {
	            case 'deposit':
	                $("#deposit_description").css('color', '#000');
	                $('#deposit_type_id').removeAttr('disabled');
	                $('#deposit_type_id').addClass('required');
								
	                $("#expense_description").css('color', '#999');
	                $('#expense_type_id').attr('disabled', 'disabled');
	                $('#expense_type_id').removeClass('required error');
	                $('#expense_type_id').find('option:first').attr('selected', 'selected').parent('select');
	                break;
	            case 'expense':
	                $("#expense_description").css('color', '#000');
	                $('#expense_type_id').removeAttr('disabled');
	                $('#expense_type_id').addClass('required');
								
	                $("#deposit_description").css('color', '#999');
	                $('#deposit_type_id').attr('disabled', 'disabled');
	                $('#deposit_type_id').removeClass('required error');
	                $('#deposit_type_id').find('option:first').attr('selected', 'selected').parent('select');
	                break;
	            default:
	                // Make both disabled and grey color
	                $(".default_disabled").css('color', '#999');
	                $('#deposit_type_id').attr('disabled', 'disabled');
	                $('#expense_type_id').attr('disabled', 'disabled');
	                break;
	        }
																   
	    });
	    
	    // copy the information over for a new payee/payer
	    $('#offer_new_address').live("keyup", function() {
	    	$('#new_address').val($(this).val());
	    });
	    $('#offer_new_city').live("keyup", function() {
	    	$('#new_city').val($(this).val());
	    });
	    $('#offer_new_state').live("keyup", function() {
	    	$('#new_state').val($(this).val());
	    });
	    $('#offer_new_zip').live("keyup", function() {
	    	$('#new_zip').val($(this).val());
	    });
	    $('#offer_new_phone').live("keyup", function() {
	    	$('#new_phone').val($(this).val());
	    });
	    $('#offer_new_contact').live("keyup", function() {
	    	$('#new_contact').val($(this).val());
	    });
	    
	    // use an existing payee/payer
	    $('.suggested .suggested_name').live("click", function() {
	    	
	    	// copy the name to the input field
	    	$("#payer_payee").val($(this).find('.payee_payer_name').val());
	    	sbm_useSuggestedName($(this).find('.payee_payer_name').val(), $(this).find('.payee_payer_id').val(), 'payee_payer');
	    	
	    });
					
	    /*  END DEPOSIT EXPENSE */
					
					
					
	    /*  NEW customer SECTION */
	    
					
	
	    // use date picker for set_date
	    $("#set_date").datepicker();
	
		$(".hasDatePicker").live( 'focus', function() {
			
			$(this).datepicker();
			
		});
		
		
		// User date picker for  misc debit or credit
	    $("#misc_date").datepicker();
					
	    // User date picker for  deposit and expenses
	    $("form#deposit_expense #transaction_date").datepicker();
					
		$("form#editcustomerForm #editcustomerSubmitButton").click(function() {
														  
			$("#editcustomerForm").validate();
	
		});
	
		// Public customer validate
		$("form#public_customer_signup #publicCustomerSignupButton").click(function() {
			//sbm_useConsole( 'Click submit' );
			
			$("#public_customer_signup").validate();
	
		});
		
		// Custom Attributes for customers
		$("a#add_attribute").click( function() {
			
			var div_id	= $("#custom_attributes").find('div:last').attr('id');

			var matches = div_id.match(/\d+/);
           

			var newHtml = '<div id="add_custom_attribute"><div>Add new Custom Attribute #' + matches[0] + ':</div>';
			    newHtml += '<div>Description: <input type="text" id="new_description"></div>';
			    newHtml += '<div>Value: <input type="text" id="new_value"></div>';
			    newHtml += '<div><input type="hidden" id="add_div_id" value="' + matches[0] + '"></div>';
			    newHtml += '<div><input type="button" id="addNewAttribute" value="Save Attribute"></div></div>';
			
			$('#' + div_id ).html(newHtml);
		});
        
		
		/*$(":button").not("#verifyDeleteinvoice").live('click', function(e){*/
         $("#addNewAttribute").live('click', function(e) {

			
			// make the temporary information into actual data
			var divId 			= $("#add_div_id").val();
			var nextDivId		= parseFloat(divId) + 1;
			var newDescription 	= $("#new_description").val();		
			var slug			= newDescription.replace(" ", "_");
			var newValue 		= $("#new_value").val();		
			var newHtml 		= '<div class="description">' + newDescription + ':</div>';
	            newHtml			+= '<input type="text" name="' + slug + '" size="30" value="' + newValue + '"> ( <a href="javascript: void(0);" class="remove-custom-attribute" id="' + divId + '">remove</a> )';
	            newHtml 		+= '<div id="customer_attribute_' + nextDivId + '"></div>';
	            
	            $("#customer_attribute_" + divId).html( newHtml );
			    e.preventDefault();
			
		});
    $(".remove-custom-attribute").live('click', function() {

        var hideDiv = $(this).attr( 'id' );
        
        $("#customer_attribute_" + hideDiv ).fadeOut( 300, function() {
            $(this).html('');
        });
    })
		
		// If there is any value in password, then email_1 is required
		$('#customer_password').bind('keyup', function() {
			var passwordLength = $(this).val().length;
			if( passwordLength > 0 )
			{
				// Make the email_1 required
				$("#email_1").addClass('required email');
				$("#email_1_description").html('Email #1<em>*</em>');
			}
			else
			{
				// Remove the email_1 required
				$("#email_1").removeClass('required email');
				$("#email_1_description").html('Email #1');
				
			}
	    });
	
	    /*  END customerS SECTION */
					
	    /*
				************************************************
				This function does the validation for passwords, they both have to match and have certain length
		
			*/
			
	    /*              START FUNCTION FOR CHECKING PASSWORDS 				*/
	    $('#password1').bind('keyup', function() {
	        checkPasswords();
	    });
	    $('#password2').bind('keyup', function() {
	        checkPasswords();
	    });
								
	    function checkPasswords()
	    {
	        var pass1 = $('#password1').val();
	        var pass2 = $('#password2').val();
	        var minLength = 4;
							
							
							
	        if (pass1 != pass2)
	        {
	            var newMessage = 'The passwords do not match';
								
	
	            $('#password_check').css( 'background', '#FF0000' );
	            $('#password_check').html( newMessage );
	        }
	        if (pass1 == pass2)
	        {
	            if (( pass1.length < minLength ) && (  pass2.length < minLength ))
	            {
	                // Wait, they dont have enough letters / numbers in the passwords
	                var newMessage = 'At least ' + minLength + ' in length';
	                $('#password_check').css( 'background', '#FF0000' );
	                $('#password_check').html( newMessage );
	            }
	            else
	            {
	                // Its all ok, minimum numbers met AND they match
	                var newMessage = 'Passwords match!';
	                $('#password_check').css( 'background', '#00CC00' );
	                $('#password_check').html( newMessage );
	            }
	        }
	        if((pass1 == '') && (pass2 == ''))
	        {
	            var newMessage = 'Enter a password';
	            $('#password_check').css( 'background', '#FFCC00' );
	            $('#password_check').html( newMessage );
	        }
	    }
	    /*              END FUNCTION FOR CHECKING PASSWORDS 				*/
	
	    /* view deposits and expenses */
	
		
	    $("#view_deposit_and_expense #bank_id").change(function() {
					
	        var bank_id 	= $("#bank_id option:selected").val();
	        var url 				= 'admin.php?page=sbm_view_deposit_expense&bank_id=' + bank_id;
	        sbm_pageRedirect(url);
						
	    });
	
	    $("a.reverse-link").live('click', function() {
	    	// hide any other open one
	    	$('div.reverse').css('display', 'none');
	    	// show the hidden div that has the question if they want to reverse this or not
	    	$(this).next('div.reverse').css('display', 'block');
	    });
	    
	    $("a.no-link").live('click', function(){
	    	$('div.reverse').css('display', 'none');
	    });
	    
	    $("a.yes-link").live('click', function(){
	    	// save the data via ajax then refresh the page
	    	if($(this).next(':input').attr('class') == 'reverse_deposit_id')
    		{
	    		
	    		$.post("./admin-ajax.php", {
					   
					action:"sbmReverseDeposit", 
					"ID": $(this).next(':input').val()
					
			}, function(str)	{
			
				// refresh the page	or show error	
    			if(str == true)
    			{
    				window.location = 'admin.php?page=sbm_view_deposit_expense';
    			}
    			else
    			{
    				alert('There was a problem reversing the deposit.');
    			}
				
		});
    		}
	    	if($(this).next(':input').attr('class') == 'reverse_expense_id')
    		{
	    		
	    		$.post("./admin-ajax.php", {
					   
					action:"sbmReverseExpense", 					 
					"ID": $(this).next(':input').val()
					
	    		}, function(str)	{
	    			// refresh the page	or show error	
	    			if(str == true)
	    			{
	    				window.location = 'admin.php?page=sbm_view_deposit_expense';
	    			}
	    			else
	    			{
	    				alert('There was a problem reversing the expense.');
	    			}
	    		});
    		}
	    	
	    	
	    	
	    });
	
	    /* end view deposit and expenses */
	
	    /* Reconcile */
		
	    $("#reconcile #change_bank_id").change(function() {
					
	        var bank_id 	= $("#change_bank_id option:selected").val();
	        var url 				= 'admin.php?page=sbm_reconcile&bank_id=' + bank_id;
	        sbm_pageRedirect(url);
						
	    });
					
	    $("form#reconcileForm #quickLink").click(function() {
						
						
	        var bank_id 	= $("#bank_id").val();
	        var page 		= $("#quickLink").val();
						
	        var return_url 	= 'sbm_reconcile&bank_id=' + bank_id;
	        var url 		= 'admin.php?page=' + page + '&bank_id=' + bank_id + '&return_url=' + return_url;
						
	        $(location).attr('href',url).stop(true, true);
	        return false;
					
	    });
		
		// Validate and update the Starting Balance on blur
		
	    $("form#reconcileForm #starting_balance").blur(function() {
							
				sbm_validateCurrency('starting_balance');	
				updateStartingBalance();
				updateReconcileTotals();
				
	    });
		// Validate and update the Ending Balance on blur
		
	    $("form#reconcileForm #ending_balance").blur(function() {
							
				sbm_validateCurrency('ending_balance');	
				updateEndingBalance();
				updateReconcileTotals();
				
	    });
		
		$("form#reconcileForm .expense_list_item").click( function() {
				if($(this).is(':checked'))
				{
					saveReconcileTransactionId($(this).val());
				}
				else
				{
					removeReconcileTransactionId($(this).val());
				}
				totalExpenseList();												   
		});
		
		function totalExpenseList()
		{
				var divId;
				var getAmount;
				var newTotal = 0;
	
					// We need to keep track of every box that is checked or unchecked and adjust the totals accordingly
					$("form#reconcileForm .expense_list_item").each( function() {
						
							if($(this).is(':checked'))
							{
								// Save the data 
								
								// Get the value and update the total expense div
								
								divId = $(this).val();
								
								getAmount 	= Number(sbm_convertCurrency( $("#expense_"+divId).html()) );
								
								newTotal 		=  newTotal + getAmount;
							
							}
							
					});
				// Now update the total expenses div
				$("#show_expense_total").html(newTotal.toFixed(2));
				// Now update all the totals
				updateReconcileTotals();
		}
		
		$("form#reconcileForm .deposit_list_item").click( function() {
				if($(this).is(':checked'))
				{
					saveReconcileTransactionId($(this).val());
				}
				else
				{
					removeReconcileTransactionId($(this).val());
				}
				totalDepositList();												   
		});
		
		function totalDepositList()
		{
				var divId;
				var getAmount;
				var newTotal = 0;
	
					// We need to keep track of every box that is checked or unchecked and adjust the totals accordingly
					$("form#reconcileForm .deposit_list_item").each( function() {
						
							if($(this).is(':checked'))
							{
								// Save the data 
								
								// Get the value and update the total expense div
								
								divId = $(this).val();
								
								getAmount 	= Number(sbm_convertCurrency( $("#deposit_"+divId).html()) );
								
								newTotal 		=  newTotal + getAmount;
							
							}
							
					});
				// Now update the total expenses div
				$("#show_deposit_total").html(newTotal.toFixed(2));
				// Now update all the totals
				updateReconcileTotals();
		}
		
		// All this does is check to see if that input has a value,
		// it should be the last thing loaded on the page, 
		// and if so update all the totals
		if($("form#reconcileForm #ending_balance").val() > 0)
		{
			updateReconcileTotals();
		}
		
	    $('form#reconcileForm  #balance_message_link').toggle(function() {
					  
	        $('#balance_message').fadeIn('slow');
	
	    }, function() {
						
	        $('#balance_message').fadeOut('slow');
	    });	
		
		$("form#reconcileForm  #close_balance_message").click( function() {
													
			$('#balance_message').fadeOut('slow');		
						
		});
		
				function updateReconcileTotals()
				{
					/*
					
					*************************************************************
						REMEMBER DEPOSIT AND EXPENSE TOTALS ARE STRINGS
						THEY HAVE TO BE CONVERTED TO NUMBERS PRIOR TO MATH OPERATIONS
					*************************************************************
					*/
					var startingBalance			=	Number($("#starting_balance").val());
					var endingBalance			=	Number($("#ending_balance").val());
					var depositTotal				=	Number(sbm_convertCurrency($("#show_deposit_total").html()));
					var expenseTotal				=	Number(sbm_convertCurrency($("#show_expense_total").html()));
					var differenceAmount		= 	0;
					var clearedAmount			=	0;
					
					
					// Cleared Balance updates div id: show_cleared_balance
					// Logic: Starting Balance + (Deposit total - Expenses Total)	
					clearedAmount 				=	sbm_convertCurrency( (startingBalance) + ( (depositTotal) - (expenseTotal) ) );
					
						$("#show_cleared_balance").html( clearedAmount );
						
						// add the appropriate color the number
						colorNumber(clearedAmount, 'show_cleared_balance');
						
						
					// Difference updates div id: show_difference
					// Logic: ( Ending Balance  - Starting Balance ) - ( Deposit Total + Expenses Total )	
					differenceAmount				=	sbm_convertCurrency( (  endingBalance - startingBalance ) - ( depositTotal + expenseTotal ) );
					
						$("#show_difference").html( differenceAmount );
	
						// add the appropriate color the number
						colorNumber(differenceAmount, 'show_difference');
	
				}
		
		
				function colorNumber(amount, divId)
				{
						if(amount  >= 0 )
						{
							$("#"+ divId).css('color', '#006600');
						}
						else
						{
							$("#" + divId).css('color', '#FF0000');
						}
				}
				
				function saveReconcileTransactionId( newId )
				{
						$.post("./admin-ajax.php", {
							   
								action:"sbmUpdateReconcile", 
								"transaction_id_list": newId, 
								"ID": $("form#reconcileForm #reconcile_id").val(), 
								"bank_id": $("form#reconcileForm #bank_id").val() 
								
						}, function(str)	{
						
									// Update the message div to alert the user of the saved progress
									sbm_showSaveMessage(str);
							
					});
				}
				function removeReconcileTransactionId( newId )
				{
						$.post("./admin-ajax.php", {
							   
								action:"sbmUpdateReconcile", 
								"remove_transaction_id_list": newId, 
								"ID": $("form#reconcileForm #reconcile_id").val(), 
								"bank_id": $("form#reconcileForm #bank_id").val() 
								
						}, function(str)	{
						
									// Update the message div to alert the user of the saved progress
									sbm_showSaveMessage(str);
							
					});
				}
				
				function updateStartingBalance()
				{
						$.post("./admin-ajax.php", {
							   
								action:"sbmUpdateReconcile", 
								"starting_balance": $("form#reconcileForm #starting_balance").val(), 
								"ID": $("form#reconcileForm #reconcile_id").val(), 
								"bank_id": $("form#reconcileForm #bank_id").val() 
								
						}, function(str)	{
						
									// Update the message div to alert the user of the saved progress
									sbm_showSaveMessage(str);
							
					});
				}
				
				function updateEndingBalance()
				{
						$.post("./admin-ajax.php", {
							   
								action:"sbmUpdateReconcile", 
								"ending_balance": $("form#reconcileForm #ending_balance").val(), 
								"ID": $("form#reconcileForm #reconcile_id").val(), 
								"bank_id": $("form#reconcileForm #bank_id").val() 
								
						}, function(str)	{
						
									// Update the message div to alert the user of the saved progress
									sbm_showSaveMessage(str);
							
					});
				}
		
				function sbm_showSaveMessage(str)
				{
					
							$("form#reconcileForm #save_message").html(str);
							$("form#reconcileForm #save_message").css('display', 'block');
							$("form#reconcileForm #save_message").fadeOut(1500, function() {
							
								//  Reset the div to remove the html and display: none
								sbm_resetSaveMessage();
					 
							 });
	
				}
				function sbm_resetSaveMessage()
				{
							$("form#reconcileForm #save_message").html(null);
							$("form#reconcileForm #save_message").css('display', 'none');
				}
	
	    /* end Reconcile */
	
				
	    /*  ACCEPT PAYMENTS FROM customers */
		
	    
		
	    // Validate Recieve payments from customers
	    $("#validateRecievePayments").click(function() {
											
			 $("#recievePayments").validate();								
	        $('.amount_paid').each(function(index) {
											
	            rowNum = index + 1;
								
	            if($("#amount_paid_" + rowNum).val() > 0)
	            {
	
								
	                sbm_validateCurrency('amount_paid_' + rowNum);
								
								
	            }
	        });
	
	    });
				
	    // disable the submit button
	    $("#validateRecievePayments ").attr("disabled", "disabled");
				
				
	    $("form#recievePayments .amount_paid").blur( function() {
									
	        sbm_getTotals();
																	  
	    });
				
				
				
				
	
	    $("#miscCreditDebitSubmitButton").click(function() {
														  
	        $("#miscCreditDebitForm").validate();
					
	        // call the function to validate a field into currency..it will round up and chop off the extra numbers.
	        sbm_validateCurrency('amount');
					
	    /*
					var credit = $("#credit").attr('checked');
					var debit = $("#debit").attr('checked');
					// Check some basic fields to make sure they are valid
					if (!credit && !debit)
					{
						alert('You need to select either credit or debit');
					}
					*/
					
	    });
	
				
				
	    $("#editUserSubmitButton").click(function() {
														  
	        $("#editCompanyForm").validate();
					
	    });
					
								
	    $("#sbmClearAllTables").click(function() {
							
	        var answer = confirm('Delete All the data?');
	        if(answer == true)
	        {
						
	            $.post("./admin-ajax.php", {
	                action:"sbmClearAllTables"
	            }, function(str)	{
					
	                $("#message").html(str);
					$("h3").html( null );
					$("li.toplevel_page_sbm_settings").fadeOut( 1000 );
					$("li.toplevel_page_sbm_view_home_page").fadeOut( 1000 );	
					$("li.toplevel_page_sbm_company_profile").fadeOut( 1000 );
					$("li.toplevel_page_sbm_customer_profile").fadeOut( 1000 );	
					$("li.toplevel_page_sbm_invoices").fadeOut( 1000 );
					$("li.toplevel_page_sbm_accounting").fadeOut( 1000 );
					$("li.toplevel_page_sbm_letter_generator").fadeOut( 1000 );
					$("li.toplevel_page_sbm_view_notifications_page").fadeOut( 1000 );	 
	                $("div.wrap div").not("div#message").fadeOut( 1000 );
	               ;
	
	            });
						
	        }
	    });
				
				
	    $("#propertyManagerRemoveUsers").click(function() {
												  
	        $.post("./admin-ajax.php", {
	            action:"sbmRemoveUsers"
	        }, function(str)	{
	            $('#message').html ( str );
	            $('#remove_message').html( null );
	        });
	    });
				
	$("#toggle-table-structure a").click( function() {
		
		if( $("#table-structure").css( 'display' ) == 'none' )
		{
			$("#table-structure").css( 'display', 'block' );
		}
		else
		{
			$("#table-structure").css( 'display', 'none' );
		}
		
	});
								
								
	    $("#checkUserName").click(function() {
												  
	        $.post("./admin-ajax.php", {
	            action:"sbmCheckUserName",
	            "user_login": $('#user_login').val()
	            }, function(str)	{
	            $('#is_username_taken').html( str );
	        });
	    });
			
			
	    $("#addmore").click(function() {
	        var count = new Array();
	        $('#meta_table input[type=text]').each(function(n,element){
	            if ($(element).val()=='') {
	                alert('All fields must have a value before you can add more!');
	                count += "1";
	                return false;
	            }
	        });
							
	        if(count.length == 0)
	        {
	            var row = $('#meta_table tbody>tr:last').clone(true).insertAfter('#meta_table tbody>tr:last');
	            var num_rows = $("#meta_table tbody>tr").length;
	            var last_row = num_rows - 2;
	            $("td:eq(0) input", row).attr("name", "meta_key[]").attr("id", "meta_name_" + num_rows).attr("value", "");
	            $("td:eq(1) input", row).attr("name", "meta_value[]").attr("id", "meta_value_" + num_rows).attr("value", "");
	            $("tr:last").attr("id", "row_"+last_row);
	            $("tr:last td:nth-child(3)").html("<a href=\"javascript: void(0);\" onMouseUp=\"sbm_deleterow("+last_row+");\" id=\"delete\">Delete</a>");
	            $("#meta_name_"+num_rows).focus();
								
	        }
	        return false;
	    });
	
	
		
		
		$("form#letter_pdf select").change( function() {
																	 
					sbm_getNewList();							 
		});
		
			// validate letter pdf
		 
		 
		 $("form#letter_pdf #submit_button").click(function() {
						
								
					$("#letter_pdf").validate();
				
					var idList 	= new Array();
					
					$("form#letter_pdf :checkbox:checked").each( function() {
						
						
							if ( $(this).val() != 'on' )
							{
								idList.push($(this).attr( 'id' ));
							}
						});
					var letter_id	= $("form#letter_pdf #letter_id option:selected").val();								
			
			    $.post("./admin-ajax.php", {
	            	action:"sbmSaveLetter",
	            	"customer_id_list": idList,
	            	"letter_id": letter_id
	        	}, function( sent_date )	{
				
					
				   // we want to output a message that the download button is below
				   //$("#show_results_div").html(  );
				   // hide the submit button
				   sbm_outputToAPdf( sent_date, letter_id, idList );
				   
				   $("#submit_button").css('display', 'none');
				  
				   
				   $("#quick_view_div").css('display', 'block');
				   $("#reset").css('display', 'block');	
				
	       	 	});
			
			// now disable all input fields
			$('form#letter_pdf :input').each(function() {
	    		$(this).attr("disabled","disabled");
	  		});
	  
	
			return false;
					
		});
		
		
		// Quick view from sending a letter page
		 $("#quick_view_div #quick_view_document").click(function() {
						
					var idList 							= new Array();
					
					$("form#letter_pdf :checkbox:checked").each( function() {
						
							if ( $(this).val() != 'on' )
							{
								idList.push($(this).attr( 'id' ));
							}
						});
					var letter_id		 				= $("form#letter_pdf #letter_id option:selected").val();				
					var sent_date						= $("#sent_date").val();
					
	 		$.post("./admin-ajax.php", {
	            action:"sbmQuickViewLetter",
	            "customer_id_list": idList,
	            "letter_id": letter_id,
	            "sent_date": sent_date
				
	        }, function( result )	{
					  
			   	
				$( "#quick_view" ).css( 'display', 'block' ).html( result );	
				
	        });
			
			   
			return false;
					
		});
		
		// Quick view from customer page, NOT sending a letter page
		$(".quick_view_link").click(function() {
			
			var letter_id 			= $(this).closest( 'tr' ).find( '.letter_id' ).val();
			var sent_date 			= $(this).closest( 'tr' ).find( '.sent_date' ).val();
			var customer_id			= Array( $(this).closest( 'tr' ).find( '.customer_id' ).val() );
			
			
			 		$.post("./admin-ajax.php", {
	            action:"sbmQuickViewLetter",
	            "customer_id_list": customer_id,
	            "letter_id": letter_id,
	            "sent_date": sent_date
				
	        }, function( result )	{
					  
			   	
				$( "#quick_view" ).css( 'display', 'block' ).html( result );	
				
	        });
				   
			return false;
			
		});
		
		// Quickpdf creator
		$(".quick_pdf_link").click(function() {
			
			var letter_id 			= $(this).closest( 'tr' ).find( '.letter_id' ).val();
			var sent_date 			= $(this).closest( 'tr' ).find( '.sent_date' ).val();
			var customer_id			= Array( $(this).closest( 'tr' ).find( '.customer_id' ).val() );
			
			$("#download_pdf_div_" + sent_date).fadeOut( 'slow', function() {
				 $.post("./admin-ajax.php", {
					action:"sbmCreatePdf",
					"customer_id_list": customer_id,
					"letter_id": letter_id,
					"sent_date": sent_date,
					"quick_pdf": true /* This will output a different message than the one when you are creating a letter */
					
				}, function( result )	{
								
						$("#download_pdf_div_" + sent_date).html( result );
						$("#download_pdf_div_" + sent_date).fadeIn( 'slow' );		
					
				});
			
			});
				   
			return false;
			
		});
		
		// END Letter PDF creation
	
		// Invoice pdf creator
		$(".quick_pdf_invoice_link").click(function() {
			
			var invoice_id 			= $(this).closest( 'tr' ).find( '.invoice_id' ).val();
			
			$("#download_pdf_invoice_" + invoice_id).fadeOut( 'slow', function() {
				
				 $.post("./admin-ajax.php", {
					action:"sbmCreateInvoicePdf",				
					"invoice_id": invoice_id,
					"quick_pdf": true /* This will output a different message than the one when you are creating a letter */
					
				}, function( result )	{
								
						$("#download_pdf_invoice_" + invoice_id).html( result );
						$("#download_pdf_invoice_" + invoice_id).fadeIn( 'slow' );		
					
				});
			
			});
				   
			return false;
			
		});
		
		// END Invoice pdf creator
		
		// Show Help
		$("#show_help_document").change( function() {
			
			if( $(this).length > 0 )
			{	
				
				$.post("./admin-ajax.php", {
					action:"sbmDisplayHelp",				
					"page": $(this).val()
					
				}, function( result )	{
								
					$("#show_help_information").html( result );
				});
				
			}
			else
			{
				// do nothting for now
				
			}
		});
			

	  
	  
	  
	  // Ajax functions
		function sbm_suggest_customers(info)
		{
		    $.post("./admin-ajax.php", {
		        action:"sbmSuggestCustomers",
		        "info": info
		    }, function( result )	{
						
		        $("#customer_suggestions").html( result );
							
						
		    });
		}
		function sbm_deleterow(id)
		{
			
			// check to make sure that there is at least one character in the name field otherwise do nothing
			if( $('#meta_name_' + id).val().length > 0 )
			{
				if (confirm('Are you sure want to delete?'))
				{
					
					
					var meta_key = $('#meta_name_'+id).value;
							
							
					$("#row_"+id).fadeOut('slow', function() {
						// Animation complete.
							
							
						$.post("./admin-ajax.php", {
							action:"sbm_deleterow",
							"meta_key": meta_key 
						}, function(result)	{
																																															   
							var newHtml = '<div id="message" class="success">'+result+'</div>';
			
								
							$("#ajax_message").html(newHtml);
						// Ouput a result or message if needed...in this case we do not need to
						//document.getElementById('output_div').innerHTML =  str;
						});
							
						$(this).remove();
							
					});
				}
			}
			else
			{
				// do nothing
			}
		}
		
		
		function sbm_get_suggestions(string, destination_id)
		{
		    $.post("./admin-ajax.php", {
		        action:"sbmGetSuggestions",
		        "content": string
		    }, function(result)	{
						
		        var msg ='<div class="close_option" style="float: right;"><a href="javascript: void(0);" class="close_link">Close</a></div>' + result;
		        $("#" + destination_id).html(msg);
							
		        if( ($("#enter_new_payer_payee").val() == 'yes') && (result == '<div>No Suggestions Available</div><div id="new_payer_payee"></div>'))
		        {
								
		            var address = $("#new_address").val();
					if( address == 'null' )
					{
						address = '';
					}
		            var city = $("#new_city").val();
					if( city == 'null' )
					{
						city = '';
					}
		            var state = $("#new_state").val();
					if(state == 'null' )
					{
						state = '';
					}
		            var zip = $("#new_zip").val();
					if( zip == 'null' )
					{
						zip = '';
					}
		            var phone = $("#new_phone").val();
					if(phone == 'null' )
					{
						phone = '';
					}
		            var contact = $("#new_contact").val();
					if( contact == 'null' )
					{
						contact = '';
					}
		            var newHtml;
									
		            newHtml = '<div>Is this a new entry?</div><div>All these fields are optional</div>';
		            newHtml += '<div>Address<input type="text" name="address" id="offer_new_address" value="' + address + '"></div>';
		            newHtml += '<div>City<input type="text" name="city" id="offer_new_city" value="' + city + '" value="' + city + '"></div>';
		            newHtml += '<div>State<input type="text" name="state" id="offer_new_state"  value="' + state + '"></div>';
		            newHtml += '<div>Zip<input type="text" name="zip"  id="offer_new_zip"  value="' + zip + '"></div>';
								
		            newHtml += '<div>Phone<input type="text" name="phone"id="offer_new_phone"  value="' + phone + '"></div>';
		            newHtml += '<div>Contact<input type="text" name="contact" id="offer_new_contact"  value="' + contact + '"></div>';
								
		            $("#new_payer_payee").html(newHtml);
		        }
						
		    });
		}
		// End Ajax Functions
		
		// Utility functions
		
		// Administrative type functions
			function sbm_checkForNotices()
			{
			    $.post("./admin-ajax.php", {
			        action:"sbmGetNotices"
			    }, function(number)	{
									
			        if (number > 0)
			        {
											 
			            $("#toplevel_page_sbm_view_notifications_page a").append('<span class="update-plugins"><span class="update-count">'  + number + '</span></span>' );
											
			            // Now add the yellow bar at the top of the page
			            var verb;
			            var plural;
												
			            if(number > 1)
			            {
			                verb = 'are';
			                plural = 's';
			            }
			            if(number == 1)
			            {
			                verb = 'is';
			                plural = '';
			            }
			            //create one rigth before this <div class="wrap">
			            $('#wpbody-content').prepend('<div id="notice">There ' + verb + ' ' + number + ' notice' + plural + ' you should take care of!</div>');
			            $('#notice').css('display', 'block');
												
												
											
											
											
											
			        }
			    });
			}

			// Output a pdf javascript to ajax
				
				function sbm_outputToAPdf( sent_date, letter_id, customer_id_list )
				{
                            $("#download_pdf_div").css('display', 'block' ).html('<img src="../wp-content/plugins/simple-business-manager/images/building_pdf.gif">');

					 		$.post("./admin-ajax.php", {
								action:"sbmCreatePdf",
								"customer_id_list": customer_id_list,
								"letter_id": letter_id,
								"sent_date": sent_date
			        		}, function( result )	{
								
								$("#download_pdf_div").css('display', 'block'); 	  
					   			$("#download_pdf_div").html( result );
						
			        });
						   
					return false;
					
				};
				// get new list for letters
					
				function sbm_getNewList()
				{
						var recheck;
						
						if ($("form#letter_pdf #all_none").is(':checked') == true )
						{
							recheck = true;
						}
						else
						{
							recheck = false;
						}
						
							var count 						= $(":checkbox:checked").length;
							var customer_status		 		= $("form#letter_pdf #customer_status option:selected").val();		
							var filters						= $("form#letter_pdf #filters option:selected").val();	
			        $.post("./admin-ajax.php", {
			            action:"sbmDisplaycustomerList",
			            "customer_status": customer_status,
			            "filters": filters
						
			        }, function(result)	{
						
			            $("#show_results_div").html(result);
						
						var letter_id 						= $("form#letter_pdf #letter_id option:selected").val();
						
						if ( recheck == true )
						{
							$(':checkbox').attr( 'checked', true );
						}
						else
						{
							$(':checkbox').attr( 'checked', false );
						}
						// utility.js
						sbm_disableEnableSubmitPdf();
						
			        });
				}

			
			
		// end Administrative type functions	
		function sbm_insertAtCaret(areaId,text) {
		    var txtarea = document.getElementById(areaId);
		    var scrollPos = txtarea.scrollTop;
		    var strPos = 0;
		    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? 
		        "ff" : (document.selection ? "ie" : false ) );
		    if (br == "ie") { 
		        txtarea.focus();
		        var range = document.selection.createRange();
		        range.moveStart ('character', -txtarea.value.length);
		        strPos = range.text.length;
		    }
		    else if (br == "ff") strPos = txtarea.selectionStart;

		    var front = (txtarea.value).substring(0,strPos);  
		    var back = (txtarea.value).substring(strPos,txtarea.value.length); 
		    txtarea.value=front+text+back;
		    strPos = strPos + text.length;
		    if (br == "ie") { 
		        txtarea.focus();
		        var range = document.selection.createRange();
		        range.moveStart ('character', -txtarea.value.length);
		        range.moveStart ('character', strPos);
		        range.moveEnd ('character', 0);
		        range.select();
		    }
		    else if (br == "ff") {
		        txtarea.selectionStart = strPos;
		        txtarea.selectionEnd = strPos;
		        txtarea.focus();
		    }
		    txtarea.scrollTop = scrollPos;
		}

		/* Utility functions */


		function sbm_copyAmount(i)
		{
					
		    var amount = $("#amount_due_" + i ).html();
		    $("#amount_paid_" + i ).val(amount);
		    sbm_getTotals();
		}
				
		function sbm_clearAmount(i)
		{
					
		    var amount = '';
		    $("#amount_paid_" + i ).val(amount);
		    sbm_getTotals();
		}
						
		function sbm_validateCurrency(id_name)
		{
					 	
		    var amount = 	$('#' + id_name).val();
		    amount = parseFloat(amount.replace(/[^0-9.]/,'')).toFixed(2);
							
		    if( isNaN(amount) == true )
		    {
		        // This is NOT a number we want the field to be null
		        $('#'+id_name).val(null);
		    }
		    else
		    {
		        // this is a number and we want it to be displayed
		        $('#'+id_name).val(amount);
		    }
						
		}

		// convert string into currenct and return that number
		function sbm_convertCurrency(num)
		{

			var amount = 0;
		    amount = parseFloat(num).toFixed(2);
							
		    if( isNaN(amount) == true )
		    {
		        // This is NOT a number we want the field to be null
		       return null;
		    }
		    else
		    {
		        // this is a number and we want it to be displayed
		        return Number(amount).toFixed(2);
		    }
						
		}

				 
		function sbm_validateDigits(id_name, msg)
		{
		    if (/^[0-9]+$/.test($("#late_payment_percentage").val()) == false)
		    {
		        alert(msg);
		    }
		}
				
		function sbm_hideMenu()
		{
		    $('ul').hide();
				
		}
				


		// FUNCTION USED WHEN ACCEPTING PAYMENTS FROM customers
		function sbm_getTotals()
		{
		    // total amount paid = sum of all input boxes
		    // total number paid = total number of entries that do not have 0 or empty in the amount_paid input box
		    var totalPaid = 0;
		    var totalAmount = 0;
		    var rowNum = 0;
		    $('.amount_paid').each(function(index) {
												
		        rowNum = index + 1;
							
		        if($("#amount_paid_" + rowNum).val() !=  '')
		        {
		            var amount = $("#amount_paid_" + rowNum).val();
		            amount = amount.replace(/[^0-9.]{2}/,'');
		            $("#amount_paid_" + rowNum).val(parseFloat(amount).toFixed(2));
							
		            totalPaid			= totalPaid + 1;
		            totalAmount  	+= Number($("#amount_paid_" + rowNum).val());
							
		        }
		    });
		    // Output how many paid
		    $("#total_number_paid").html(totalPaid);
		    $("#total_amount_paid").html(totalAmount.toFixed(2));
		    // if too many are marked as paid, send an alert and disable the submit button
		    if(totalPaid == 0)
		    {
		        // can not submit with 0 entered
		        $("#validateRecievePayments ").attr("disabled", "disabled");
		        $("#validateRecievePayments ").val('Please select at least one customer!');
		    }
		    else if(totalPaid > 18)
		    {
		        alert('You can only have 18 marked as paid at one time');
		        // can not submit with more than 18 selected
		        $("#validateRecievePayments ").attr("disabled", "disabled");
		        $("#validateRecievePayments ").val('Too Many Selected, unable to submit!');

		    }
		    else
		    {
		        // Its ok to submit
		        $("#validateRecievePayments ").removeAttr("disabled");
		        $("#validateRecievePayments ").val('Enter Deposit');
		    }
		}
				
		function sbm_useSuggestedName(data, id, desitination_id)
		{
					
		    $("#"+desitination_id).val(data);
		    // Also update the hidden input that has the ID for this payer_payee
		    $("#payee_payer_id").val(id);
					
		    // Now clear the suggestion box and set focus to the next field
		    $("#payer_payee_suggestion").fadeOut('fast');
		    $("#amount").focus();
					
		}
				
		function sbm_toggleAdditionalInformation()
		{
													 
		    var val =  $("#enter_new_payer_payee").val();
						
		    if(val == 'yes')
		    {
		        $("#enter_new_payer_payee").val('no');
		    }
		    else
		    {
		        $("#enter_new_payer_payee").val('yes');
		    }
						
		}
		function sbm_copyInformation(sourceId, destinationId)
		{
		    var string  = $('#'+sourceId).val();
		    $('#'+destinationId).val(string);
					
		}
		function sbm_closePayerPayeeSuggestion()
		{
			
		    sbm_toggleSuggestion();
		    $("#payer_payee_suggestion").fadeOut('fast');
		}
				
		function sbm_openPayerPayeeSuggestion()
		{
		    $("#payer_payee_suggestion").fadeIn('fast');
																		 

		}
				
		function sbm_toggleSuggestion()
		{
		    var counter = Number(0);
		    $("#deposit_expense .payer_payee").each(function() {
																	 
		        if($(this).val().length > 0)
		        {
		            counter = counter + 1;
		        }
		        if(counter > 0)
		        {
		            $("#suggestion_toggle").css('display', 'block');
		        }
		        if(counter == 0)
		        {
		            $("#suggestion_toggle").css('display', 'none');
		        }
						
		    });

		}
				
		function sbm_pageRedirect(url)
		{
		    $(location).attr('href',url);

		}
			// This checks/unchecks the boxes when showing the list of customers to submit the letter to
		function sbm_checkAllCheckNone()
		{
				if ($("form#letter_pdf #all_none").is(':checked') )
				{
					$(':checkbox').attr( 'checked', true );
				}
				else
				{
					$(':checkbox').attr( 'checked', false );
				}
				sbm_disableEnableSubmitPdf();
		}

		function sbm_disableEnableSubmitPdf()
		{
				var letter_id 						= $("form#letter_pdf #letter_id option:selected").val();
				var count 							= $(":checkbox:checked").length;
				
				if( ( count != 0 ) && ( letter_id != 0 ) )
				{
					// enable the submit button
					$("form#letter_pdf #submit_button").removeAttr('disabled').val('Submit');
				}
				else
				{
					// disable the submit button
					$("form#letter_pdf #submit_button").attr('disabled', true).val('Submit Not Available');
				}
		}

		function sbm_convertToUserFriendly( name )
		{
			var newName;
			
			switch( name )
			{
				case 'first_name':
					newName = 'First Name';
				break;
				case 'last_name':
					newName = 'Last Name';
				break;
				case 'property_name':
					newName = 'Property Name';
				break;
				case 'street_number':
					newName = 'Street Number';
				break;
				case 'street_name':
					newName = 'Street Name';
				break;
				case 'city':
					newName = 'City';
				break;
				case 'state':
					newName = 'State';
				break;
				case 'zip':
					newName = 'Zip';
				break;
				default:
					alert( ' Error Finding name match ' );
				break;
				
			}
			
			return newName;
		}

		function sbm_overRideCheckBox()
		{
		        // $("form#adjust_accounting_record #override").toggle(function() {
			
		        if( $("#override").attr("checked") == true )
		        {
		            $("form#adjust_accounting_record #sumbit_button").removeAttr('disabled').val('Submit');
		        }
		        else
		        {
		            $("form#adjust_accounting_record #sumbit_button").attr('disabled', 'disabled').val('Submit Disabled, Errors Found!');             				
		        
		        }

		           
		        
		    
		}


		function sbm_useConsole( message, result )
		{
			console.log( message, result );
		}

		/*  Invoices */
		if( $("form#editInvoiceForm #update_invoice_totals").val() == 'true' )
		{
			sbm_updateLineTotals();
		}
		
		function sbm_updateLineTotals()
		{
            
			var qty					= 0;
			var price				= 0;
			var taxable				= false;
			var taxRate				= 0; 
			var unpaidTime 			= 0;
			var unpaid				= 0;
			var total				= 0;
			var hourlyRate			= $("#hourly_rate").val();
			
			$("form .line_item_table tr.invoice_data_row").each( function() {
				
				qty 			= $(this).find( 'input.input_qty' ).val();
				qtyId			= $(this).find( 'input.input_qty' ).attr( 'id' );
				price 			= $(this).find( 'input.input_price' ).val();
				priceId 		= $(this).find( 'input.input_price' ).attr( 'id' );
				startTime		= $(this).find( 'input.input_start_time' ).val();
				endTime			= $(this).find( 'input.input_end_time' ).val();
				unpaidTime		= $(this).find( 'input.input_unpaid_time' ).val();
				taxable			= $(this).find( 'input.input_taxable' ).attr( 'checked' );
				taxRate			= $("#tax_rate").val();
				unpaidTime 		= $(this).find( 'input.input_unpaid_time' ).val();
				lineTotalId 	= $(this).find( 'div.line_total' ).attr( 'id' );

				// convert minutes to decimal
				unpaid			= sbm_convertMinuteToNumber( unpaidTime );
				//sbm_useConsole( 'unpaid = ' , unpaid );
				
				
				// check to see if price has been set, if not set it with the hourly rate
				if( ( price == '' ) && ( qty != '' ) ) 
				{

					$("#"+priceId).val( hourlyRate );
				}
				
				
				if( qty != 'undefined' )
				{

					if( ( startTime != '' ) && ( endTime != '' ) && ( unpaidTime != '' ) )
					{		
						
						// This will change the qty for the multiplication AND updates the qty div
						qty = sbm_getTimeDifference( startTime, endTime, unpaid, qtyId );
						// now if unpaid == 0 set that input to have the number zero
					}
				}

				total			= Number( qty * price );
				//var totalUnpaid		= Number(Number(qty) - Number(unpaid) ) * price;
				//var currencytotalUnpaid = sbm_convertCurrency( totalUnpaid );
				var totalWithTax = sbm_getTotalWithTax(total, taxRate, unpaid);
				//sbm_useConsole('taxable = ' + taxable );
				
				if( total > 0 )
				{

					if( taxable == 'checked' )
					{
						
						$("#" + lineTotalId ).html(sbm_convertCurrency(totalWithTax));
						
					}
					else
					{

						$("#" + lineTotalId ).html( sbm_convertCurrency(total) );
						
					}
				}
				else if ( total == null )
				{
				 	$("#" + lineTotalId ).html( null );

				}
				else if ( isNaN( total ) )
				{
					// do nothing for now

                }
                else
                {
                    if(qty.length > 0 )
                    {
                        //sbm_useConsole('qty = ', qty);
                        //sbm_useConsole('price = ', price);
                        //sbm_useConsole('!the total is = ', total);
                        $("#" + lineTotalId ).html( sbm_convertCurrency(total) );
                    }



                }
				
			});
			
			// now update the invoice totals
			sbm_getInvoiceTotal();	 
		}

		function sbm_getInvoiceTotal()
		{
			var tableId				= '';
			var oldTableId			= '';
			var lineTotal 			= 0;
			var taxableLineTotal 	= 0;
			var taxableTotal		= 0;
			var totalTaxPaid		= 0;
			var nontaxableTotal 	= 0;
			var nontaxableLineTotal = 0;
			var newInvoiceTotal 	= 0;
			var paidDown			= $("#paid_down").val();
			var	taxable				= false;
			var taxRate				= $("#tax_rate").val();
			var qty					= 0;
			var price				= 0;
			var nonTaxPrice 		= 0;
			
			$("form .line_item_table tr.invoice_data_row").each( function() {
				
				// use this to update the taxable and untaxable totals
				tableId		= $(this).closest( 'table' ).attr( 'id' );	
				taxable 	= $(this).find( 'input.input_taxable' ).attr( 'checked' );
				if( ( oldTableId == '' ) || ( oldTableId != tableId ) )
				{
					// reset the incrementing values
					totalTaxPaid    = 0;
					nontaxableTotal = 0;
					taxableTotal 	= 0;
				}
				
				if( taxable == true )
				{
					
					// taxable totals
					// convert all the results into numbers	
					taxableLineTotal = parseFloat( $(this).find('div.line_total').html() );
					if(isNaN(taxableLineTotal))
					{
						// do nothing for now
					}
					else
					{
						taxableTotal += taxableLineTotal;
						
						qty 	= $(this).find( 'input.input_qty' ).val(); 
						price 	= $(this).find( 'input.input_price' ).val(); 
						
						nonTaxPrice = ( qty * price );
						
						totalTaxPaid += ( taxableLineTotal - nonTaxPrice );
					}
					
				}
				else if ( taxable == false )
				{
					// nontaxable totals
					// convert all the results into numbers	
					nontaxableLineTotal = parseFloat( $(this).find('div.line_total').html() );
					if(isNaN(nontaxableLineTotal))
					{
						// do nothing for now
					}
					else
					{
						nontaxableTotal += nontaxableLineTotal;
					}
				}
				// convert all the results into numbers	
				lineTotal = parseFloat( $(this).find('div.line_total').html() );
				if(isNaN(lineTotal))
				{
					// do nothing for now
				}
				else
				{
					newInvoiceTotal += lineTotal;
				}
				
				
				// update all .invoice_total with newInvoiceTotal
				$(".invoice_total").html( sbm_convertCurrency( newInvoiceTotal ) );
				// update each page with new taxable and untaxable totals
				$("#" + tableId + "_taxable_total").html( sbm_convertCurrency( taxableTotal ) );
				$("#" + tableId + "_page_total").html( sbm_convertCurrency( (nontaxableTotal + taxableTotal) ) );
				$("#" + tableId + "_nontaxable_total").html( sbm_convertCurrency( nontaxableTotal ) );
				$("#" + tableId + "_total_tax_paid_by_page").html( sbm_convertCurrency( totalTaxPaid ) );
				$("span.balance_due").html( sbm_convertCurrency( newInvoiceTotal - paidDown ) );
				
				oldTableId = tableId;
			});
			
		}

		function sbm_getTotalWithTax( subTotal, taxRate, unpaid)
		{
			
			var taxAmount = subTotal * (taxRate/parseFloat("100")); //15000 * .1
			
			var newTotal = ( subTotal + (taxAmount) ) - unpaid;
			
			return 	newTotal;
		}

		function sbm_updateNewTable( newPageNumber )
		{
			
		    $('.new-table :input').not( ':checkbox' ).each( function() {
				
				$(this).val( '' );
			});
			
		    $('.date :input').each( function() {
				$(this).removeClass( 'hasDatePicker hasDatepicker' );
				$(this).addClass( 'hasDatePicker' );
			});
			
			$('.new-table :checkbox').each( function() {
			
				$(this).attr( 'checked', false );
			});
			
			$('.new-table div.line_total').html( null );
			$('.new-table span.hourly').html( null );
			
			
			$(".new-table:first").attr( 'id', 'line_item_table_'+newPageNumber );
			$(".new-table td.page_total span:first").attr( 'id', 'line_item_table_' + newPageNumber + '_page_total' );
			$(".new-table td.untaxed_price span:first").attr( 'id', 'line_item_table_' + newPageNumber + '_nontaxable_total' ).html( '0.00' );
			
			$(".new-table td.taxed_price span:first").attr( 'id', 'line_item_table_' + newPageNumber + '_taxable_total' ).html( '0.00' );
			
			$(".new-table td.total_tax_paid_by_page span:first").attr( 'id', 'line_item_table_' + newPageNumber + '_total_tax_paid_by_page' ).html( '0.00' );
			
			$(".new-table").find('span.current_page').html( newPageNumber );

			
			$('#line_item_table_'+newPageNumber+' :input').each( function() {
				var id = $(this).attr( 'id' );
                if(id > 0)
                {
                    var newId = id.replace(/page1/, "page"+newTotal);

                    $(this).attr( 'id', newId );
                    $(this).attr( 'name', newId );
                }
			});
			
			$('#line_item_table_'+newPageNumber+' span').each( function() {
				var id = $(this).attr( 'id' );
				var newId = id.replace(/span1/, "span"+newPageNumber);

				$(this).attr( 'id', newId );
			});
			$('#line_item_table_'+newPageNumber+' :input.copyText').each( function() {
				var id = $(this).attr( 'id' );
				var newId = id.replace(/input1/, "input"+newPageNumber);

				$(this).attr( 'id', newId );
			});
			// update the line_total divs
			$('#line_item_table_'+newPageNumber+' div.line_total').each( function() {
				var id = $(this).attr( 'id' );
				var newId = id.replace(/page1/, "page"+newPageNumber);

				$(this).attr( 'id', newId );
				$(this).attr( 'name', newId );
			});
			
			
			$('.new-table').removeClass( 'new-table' );
			
		}

		function sbm_getTimeDifference( startTime, endTime, unpaidTime, qtyId ) 
		{
			
				var start 			= startTime.split(':');
				var startHour		= ( start[0] );
				var startMinute		= ( start[1] );
				var end				= endTime.split(':');
				var endHour			= ( end[0] );
				var endMinute		= ( end[1] );
				var totalHours 		= Number( Number(endHour) - Number(startHour) );
				var unpaid			= Number( unpaidTime );
				var diff			= totalHours - unpaid;
				var totalMinutes 	= 0;
				var minutes			= 0;
				var newTotal		= 0.0;
			
				var time1 = Number(startHour) + Number( sbm_convertMinuteToNumber( startMinute ) ); 
				var time2 = Number(endHour) + Number( sbm_convertMinuteToNumber( endMinute ) ); 
				
			
				var newTotal	= ( time2 - time1 ) - unpaidTime;
				if( newTotal < 0 )
				{
					
					newTotal = null;
				}
				if( ( startHour.length > 0) && ( endHour.length > 0 ) )
				{
					$('#' + qtyId).val( newTotal);
					
					
					
					$('#' + qtyId).closest( 'tr' ).find( 'span').html( newTotal );
					$('#' + qtyId).closest( 'tr' ).find( ':input.copyText').val( newTotal );
				}
				else
				{
					// do nothing for now
				}
			
		   return newTotal;
		}


		function sbm_convertMinuteToNumber( minutes )
		{
			var convertedMinute = 0;
			var hour			= 0;
			var remainder		= 0;
			var convertedResult = 0;
			
			
				convertedMinute 	= parseFloat(Number(minutes))/60;
				
				remainder			= parseFloat(Number(minutes))%60;
				
				hour = Number( Math.floor( convertedMinute ) );
				
				if( hour == 0 )
				{
					// convert the minutes to be 25, 50 or 75
					if( remainder <= 15 ) 
					{
					   convertedResult = hour + .25;
					}
					if( ( remainder > 15 ) && ( remainder <= 30 ))
					{
					   convertedResult = hour + .5;
					}
					if( ( remainder > 30 ) && ( remainder <= 45 ) )
					{
					   convertedResult = hour + .75;
					}
					if( ( remainder > 45 ) && ( remainder <= 60 ) )
					{
						convertedResult = ( hour + 1 );
					}
					
					// since hour is = 0 and remainder is 0 the result is 0
					if( remainder == 0 )
					{
					   convertedResult = 0;
					
					}
				}
				
			   if( ( hour > 0 ) && ( remainder == 0 ) )
			   {
				   convertedResult = hour;
				  
			   }
			   
			   if( ( hour > 0 ) && ( remainder > 0 ) )
			   {
				   convertedResult = ( Number( hour ) + Number( sbm_convertMinuteToNumber( remainder )  ) );
				  
			   }
			
			
		   return convertedResult;
		}

		function sbm_CheckDecimal(num)
		{
			return Number( num );
		    
		}
		/* End invoices */


		function sbm_verifyDeletePayeePayer(id)
		{
			window.location = 'admin.php?page=sbm_edit_payee_payer&status=delete&id='+id;
		}
		 
		function sbm_verifyDeleteTransactionType(id)
		{
			window.location = 'admin.php?page=sbm_edit_transaction_type&status=delete&id='+id;
		}
		function sbm_verifyDeleteExpenseType(id)
		{
			window.location = 'admin.php?page=sbm_edit_expense_type&status=delete&id='+id;
		}
		function sbm_verifyDeleteDepositType(id)
		{
			window.location = 'admin.php?page=sbm_edit_deposit_type&status=delete&id='+id;
		}
		function sbm_verifyDeleteBankAccount(id)
		{
			window.location = 'admin.php?page=sbm_edit_bank_account&status=delete&id='+id;
		}
		$("#verifyDeleteinvoice").live('click', function(){

            var invoice_id = $("#invoice_id").val();
            var customer_id = $("#customer_id").val();

			window.location = 'admin.php?page=sbm_create_invoice&status=delete&invoice_id='+invoice_id+'&customer_id='+customer_id;
		});

        $("#verifyActivateinvoice").live('click', function() {
            var invoice_id = $("#invoice_id").val();
            var customer_id = $("#customer_id").val();
            window.location = 'admin.php?page=sbm_create_invoice&status=reactivate&invoice_id='+invoice_id+'&customer_id='+customer_id;
        })
		// end Utility functions
        function sbm_showMessage(divId, delayTime)
        {
            if(delayTime > 0)
            {
            var newTime = delayTime*1000;

                                $("#" + divId).fadeIn(1000).animate({opacity: 1.0}, 2000).fadeOut(newTime, function() {


                                      $("#" + divId).remove();

                                });
            }
            else
            {

                $("#" + divId).fadeIn(1000, function() {
                    $(this).delay( 5000 ).fadeOut( 1000 );

                });
            }

        }

});
