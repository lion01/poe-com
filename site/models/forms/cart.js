var statusInterval;
var updateSpinner;
var dialogTitle;

jQuery(function(){
   jQuery('#dialog').dialog({autoOpen: false});
    // create spinner object
   startSpinner();
   
   /* update spinner */
   jQuery('#loadingDiv')
        //.hide()  // hide it initially
        .ajaxStart(function() {
            jQuery(this).show();
            updateSpinner.spin(document.getElementById('loadingDiv'));
            jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_PLEASE_WAIT'));
            jQuery('#dialog').dialog({title: dialogTitle});
            jQuery('#dialog').dialog("open");
            
        })
        .ajaxStop(function() {
        /*    updateSpinner.stop();
            jQuery(this).hide();
            jQuery('#dialogText').text('');
            
            jQuery('#dialog').dialog("close"); */
        }); 
});

function startSpinner(){
    var opts = {
          lines: 6, // The number of lines to draw
          length: 5, // The length of each line
          width: 3, // The line thickness
          radius: 1, // The radius of the inner circle
          color: '#FFA500', // #rgb or #rrggbb
          speed: 1.5, // Rounds per second
          trail: 35, // Afterglow percentage
          shadow: false, // Whether to render a shadow
          hwaccel: false // Whether to use hardware acceleration
    };
    var target = document.getElementById('loadingDiv');
    
    updateSpinner = new Spinner(opts).spin(target);
 
}

function cartDeleteItem(cartIndex){
    
    if(cartIndex >= 0){
        var cart_itemid = jQuery('#cart_itemid').val();
        var url =  'index.php?option=com_poecom&view=cart&deleteitemidx='+cartIndex;
        var token = jQuery('#jtoken input').attr('name');
        routeUrl(url+'&'+token+'=1&Itemid='+cart_itemid);
    }
}

function cartChangeItem(cartIndex, productId){
    
    if(cartIndex >= 0){
        var product_itemid = jQuery('#product_itemid').val();
        var url =  'index.php?option=com_poecom&view=product&id='+productId+'&changeitemidx='+cartIndex;
        var token = jQuery('#jtoken input').attr('name');
        routeUrl(url+'&'+token+'=1&Itemid='+product_itemid);
    }
}


function setBilling(){
    var itemID = jQuery('#Itemid').val();
    var href = jQuery('#modalPOEcom').attr('href');
    var hrefNew = href+'registration.display&view=registration&Itemid='+itemID;
    var rel = jQuery('#modalPOEcom').attr('rel');
    var relNew = "{handler: 'iframe', size: {x: 500, y: 500}}";
    
    jQuery('#modalPOEcom').attr('href', hrefNew);
    jQuery('#modalPOEcom').attr('rel', relNew);
    
    jQuery('#modallink').click();
    
    jQuery('#modalPOEcom').attr('href', href);
    jQuery('#modalPOEcom').attr('rel', rel);
    
}

/* Update the BT/ST addresses */
function updateAddresses(runUpdate, itemId, closeModal){
    if(closeModal === true ){
       SqueezeBox.close(); 
    }
   
    if(runUpdate === true){
         var url;
        if(itemId > 0){
            url = 'index.php?Itemid='+itemId;
        }else{
            url = 'index.php?option=com_poecom&view=cart&Itemid=';
        }
       routeUrl(url);
    }
}

function updateAddress(addressType){
    var addressID;
    var relNew;
    
    if(addressType === 'BT'){
        addressID = jQuery('#bt_id').val();
        relNew = "{handler: 'iframe', size: {x: 500, y: 500}}";
    }else{
        addressID = jQuery('#st_id').val();
        relNew = "{handler: 'iframe', size: {x: 500, y: 500}}";
    }
    
    var rel = jQuery('#modalPOEcom').attr('rel');
    
    var jtokenName = jQuery('#jtoken input').attr('name');
    var jtokenValue = jQuery('input[name='+jtokenName+']').val();
    var href = jQuery('#modalPOEcom').attr('href');
    var hrefNew = href+'address.display&view=address&address_type='+addressType+'&jtoken_name='+jtokenName+'&jtoken_value='+jtokenValue+'&address_id='+addressID;
    
    jQuery('#modalPOEcom').attr('href', hrefNew);
    jQuery('#modalPOEcom').attr('rel', relNew);
    
    jQuery('#modallink').click();
    
    jQuery('#modalPOEcom').attr('href', href);
    jQuery('#modalPOEcom').attr('rel', rel);
    
}

function updateShipping(){
    var rateID = jQuery('input:radio[name=ship_rate]:checked').val();
    dialogTitle = Joomla.JText._('COM_POECOM_UPDATE_SHIPPING');
    
    if(rateID.length > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&task=cart.updateShipping&format=raw',
            data: {rate_id : rateID},
            dataType: 'html',
            success: function(html, textStatus){
                
                var updates = jQuery.parseJSON(html);
                
                if(updates){
                    jQuery('#shipping_cost').text(parseFloat(updates['shipping']).toFixed(2));
                    jQuery('#shipping_tax').text(parseFloat(updates['shipping_tax']).toFixed(2));
                    jQuery('#total').text(parseFloat(updates['total']).toFixed(2));
                }
                
                updateSpinner.stop();
                jQuery(this).hide();
                jQuery('#dialogText').text('');

                jQuery('#dialog').dialog("close");
                
            },
                error: ''
            });
    }
}

function setPayment(){
    var payMethodID = jQuery('input:radio[name=pay_method]:checked').val();
    var agreedTerms = jQuery('#agree_terms').prop('checked');
   
    if(agreedTerms === false){
        ccMessages(4);
        return;
    }
    
    var ccData = [];
    
    //If credit card direct get cc data
    if(jQuery('#pay_method_cc_container').length){
        
        ccData = [{
            ccName:jQuery('#cc_name').val(),
            ccNumber:jQuery('#cc_number').val(),
            ccCVV:jQuery('#cc_cvv').val(),
            ccExpiryMonth: jQuery('#cc_expiry_month').val(),
            ccExpiryYear: jQuery('#cc_expiry_year').val()
        }];
    }
    
    dialogTitle = Joomla.JText._('COM_POECOM_CREATE_RFQ');
    if(payMethodID > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&task=payment.process&view=payment&format=raw',
            data: {pay_method_id : payMethodID, cc_data : ccData, agreed_tos : agreedTerms},
            dataType: 'html',
            success: function(html, textStatus){
                //console.log(html);
                var response = jQuery.parseJSON(html);
                
                if(response){
                    jQuery('#rfq_number').val(response['request_number']);
                    
                    switch(response['pay_type']){
                        case '1': // Direct Credit Card API
                            
                            if(response['debug_msg'].length > 0){
                                
                                var debugWindow = window.open('','','width=600,height=600');
                                debugWindow.document.write(response['debug_msg']);
                                debugWindow.focus();
                            }
                         
                            //check if succesful payment
                            if(parseInt(response['cc_pay_approved']) === 1){
                                //show order
                                ccMessages(3, response['rfq_number']);
                            }else if(parseInt(response['blocked']) === 0 && response['error'] !== 'tos' ){
                                //txn number and attempt count stored in session
                                //show declined message and stay on cart page
                                ccMessages(1, '');
                            }else if(response['error'] === 'tos'){
                                ccMessages(4);
                            }else{
                                //show blocked message and redirect to rquest
                                ccMessages(2, response['rfq_number']);
                            }
                            
                            break;
                        case '2': // External Form
                            updateSpinner.stop();
                            jQuery('#loadingDiv').hide();
                            jQuery('#dialog').dialog("close");
                            
                            dialogTitle = Joomla.JText._('COM_POECOM_PAY_TXN');
                            jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_ORDER_SAVED'));
                            jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
                                    text : Joomla.JText._('COM_POECOM_MAKE_PAYMT'),
                                    click :  function(){
                                        window.open(response['url']+response['query'],'_blank','',true );

                                        // check order status every 10 seconds
                                        statusInterval = window.setInterval(checkPaymentStatus, 10000);

                                        checkPaymentStatus();
                                    }
                                }]   
                            });

                            jQuery('#dialog').dialog("open");
                            
                            
                            break;
                        case '3': // RFQ Only
                            updateSpinner.stop();
                            jQuery('#loadingDiv').hide();
                            jQuery('#dialog').dialog("close");
                            //payment handled aftering RFQ is placed e.g. manual, virtual terminal
                           
                            showRequest(response['request_number'], 5);
                            break;
                        case '4': // Account
                            updateSpinner.stop();
                            jQuery('#loadingDiv').hide();
                            jQuery('#dialog').dialog("close");
                            //payment handled aftering RFQ is placed e.g. manual, virtual terminal
                           
                            showOrder(response['order_id'], response['payment_status_id']);
                            break;
                        default:
                            // error - remove button
                            jQuery('#next_step').remove();
                            break;
                    }
                }
            },
            error: ''
        });   
    }
}

function ccMessages(dialogMsg, rfqNumber){
    //remove spinner
    updateSpinner.stop();
    jQuery('#loadingDiv').hide();
    
    switch(dialogMsg){
        case 1: //retry
           
            dialogTitle = Joomla.JText._('COM_POECOM_TXN_DECLINED');
            jQuery('#dialog').dialog({title: dialogTitle});
            jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_TXN_DECLINED_MSG'));
            jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
                        text : Joomla.JText._('COM_POECOM_CLOSE'),
                        click :  function(){
                            jQuery('#dialog').dialog("close");
                        }
                    }]   
            });
            jQuery('#dialog').dialog("open");
            break;
        case 2: //blocked
            
            dialogTitle = Joomla.JText._('COM_POECOM_TXN_BLOCKED');
            jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_TXN_BLOCKED_MSG'));
            jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
                        text : Joomla.JText._('COM_POECOM_CLOSE'),
                        click :  function(){
                            showRequest(rfqNumber, 3);
                        }
                    }]   
            });
            
            jQuery('#dialog').dialog("open");
            break;
        case 3: //approved
            
            dialogTitle = Joomla.JText._('COM_POECOM_PAYMT_RECD');
            jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_PAYMT_RECD_MSG'));
            jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
                        text : Joomla.JText._('COM_POECOM_SHOW_ORDER'),
                        click :  function(){
                            showRequest(rfqNumber, 2);
                        }
                    }]   
            });
            
            jQuery('#dialog').dialog("open");
            break;
        case 4: // agreed to terms
            dialogTitle = Joomla.JText._('COM_POECOM_TOS');
            jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_TOS_MSG'));
            jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
                        text : Joomla.JText._('COM_POECOM_CLOSE'),
                        click :  function(){
                            jQuery('#dialog').dialog("close");
                        }
                    }]   
            });
            
            jQuery('#dialog').dialog("open");
            break;
        default: //should not get here - do nothing
            break;
    }
}

function checkPaymentStatus(){
    
    var rfqNumber = jQuery('#rfq_number').val();
    var statusCheckLimit = jQuery('#status_check_limit').val();
 
    statusCheckLimit++;
    
    jQuery('#status_check_limit').val(statusCheckLimit);
    
    // Check the order payment status for upto 3 minutes
    // 10 seconds * 12 = 120/60 = 2 minutes
    if(parseInt(statusCheckLimit) < 3 && rfqNumber.length > 0){
       jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&task=payment.getpaymentstatus&view=payment&format=raw',
            data: {rfq_number : rfqNumber},
            dataType: 'html',
            success: function(html,textStatus){
            
                var response = jQuery.parseJSON(html);
                
                switch(response['payment_status']){
                    case '2': //complete
                        // payment processor has reponded
                        // stop the order status check
                        window.clearInterval(statusInterval);
                        //resirect to request/order view depending on status
                        showOrder(response['order_id'], response['payment_status']);
                        break;
                    case '1': //pending
                    case '3': //failed
                        // payment processor has reponded
                        // stop the order status check
                        window.clearInterval(statusInterval);
                        //resirect to request/order view depending on status
                        showRequest(rfqNumber, response['payment_status']);
                        break;
                    case '4': //waiting
                    default:
                        //keep polling for processor response
                        break;
                }
            },
            error: ''
        }); 
    }else{
        // stop the order status check
        window.clearInterval(statusInterval);
        
        //TODO: redirect to failed rfq creation
        if(rfqNumber.length > 0){
            updateSpinner.stop();
            jQuery('#loadingDiv').hide();
            //Request has been stored but payment confirmation not received in time
            dialogTitle = Joomla.JText._('COM_POECOM_RFQ_RECD');
            jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_RFQ_RECD_MSG'));
            jQuery('#dialog').dialog({title: dialogTitle, close: showRequest(rfqNumber)});
            jQuery('#dialog').dialog("open");
        }
    }
}

function showRequest(rfqNumber,statusID){
    var url = 'index.php?option=com_poecom&task=request.display&view=request&rfq='+rfqNumber+'&payment_status_id='+statusID;
    //window.location.replace( Joomla.JText._("SITEURL")+'index.php?option=com_poecom&task=request.display&view=request&rfq='+rfqNumber+'&payment_status_id='+statusID);    
    routeUrl(url);
}

function showOrder(orderId, paymentStatus){
    var url = 'index.php?option=com_poecom&view=request&task=request.displayOrder&order_id='+orderId+'&payment_status_id='+paymentStatus;
   // window.location.replace( Joomla.JText._("SITEURL")+'index.php?option=com_poecom&task=request.display&view=request&rfq='+rfqNumber+'&payment_status_id='+statusID);    
    routeUrl(url);
}


function setProductPage(){
    var url = 'index.php?option=com_poecom&defaultpage=1';
    //window.location.replace( ); 
    routeUrl(url);
}

function useCoupon(){
    var couponCode = jQuery('#coupon_code').val().toUpperCase();
    
    if(couponCode.length >= 10){
	dialogTitle = Joomla.JText._('COM_POECOM_VALIDATE_COUPON');
	
	jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&task=cart.useCoupon&format=raw',
            data: {coupon_code : couponCode},
            dataType: 'html',
            success: function(html, textStatus){
               
                var response = jQuery.parseJSON(html);
                
		updateSpinner.stop();
		
                if(parseInt(response['valid']) === 1){
                    jQuery('#discount_amount').text(parseFloat(response['discount_amount']).toFixed(2));
                    jQuery('#total').text(parseFloat(response['total']).toFixed(2));
		    jQuery('#product_tax').text(parseFloat(response['product_tax']).toFixed(2));
		    jQuery('#use_coupon').remove();
		    jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_COUPON_APPLIED'));
		    
                }else{
		    jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_COUPON_NOT_APPLIED') + response['error_msg']);
		}
                
                jQuery('#dialog').dialog({buttons:[{
                        text : Joomla.JText._('COM_POECOM_CLOSE'),
                        click :  function(){
                            jQuery('#dialog').dialog("close");
                        }
                    }]
		});
            },
                error: ''
            });
	
    }else{
	updateSpinner.stop();
	jQuery('#loadingDiv').hide();
	//Coupon code is to short or empty
	dialogTitle = Joomla.JText._('COM_POECOM_COUPON_INVALID');
	jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_COUPON_INVALID_MSG1') + couponCode + Joomla.JText._('COM_POECOM_COUPON_INVALID_MSG2'));
	jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
                        text : Joomla.JText._('COM_POECOM_CLOSE'),
                        click :  function(){
                            jQuery('#dialog').dialog("close");
                        }
                    }]   });
	jQuery('#dialog').dialog("open");
    }  
}

function showLogin(login){
    SqueezeBox.close();
    
    if(login){
	//poecom.js function for window.location.replace()
        fullPathUrl('index.php?option=com_poecom&view=login');
    }
}