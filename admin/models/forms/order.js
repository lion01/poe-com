jQuery(function(){
    toggleSTFields();
});


function updateRegions(addressType){
    var countryID;
        
    if(addressType == 'BT'){
        countryID = jQuery('#jform_country_id').val();
    }else if(addressType == 'ST'){
        countryID = jQuery('#jform_st_country_id').val();
    }else{
        countryID = 0;
    }        
    
    if(countryID > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=address&task=address.getRegions&format=raw',
            data: {country_id : countryID, address_type : addressType },
            dataType: 'html',
            success: function(html, textStatus){
                if(addressType == 'BT'){
                    jQuery('#jform_region_id').replaceWith(html);
                }else{
                    jQuery('#jform_st_region_id').replaceWith(html);
                }
            },
            error: function(xhr, textStatus, errorThrown){
                alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
            }
       });  
    }
}

function toggleSTFields(){
    
    var stbtSame = jQuery('input[name="jform[stbt_same]"]:checked').val();
    
    if(stbtSame == '1'){
        
        jQuery('#st_fields').hide();
        // remove form validation
        jQuery('.poe-staddress-toggle').each(function(){
            jQuery(this).parent().children().eq(0).removeAttr('class');
            jQuery(this).parent().children().eq(0).removeAttr('aira-invalid');
            jQuery(this).attr({'class': 'poe-staddress-toggle', 'aria-required' : 'false'});
            jQuery(this).removeAttr('required');
        });
    }else{
        jQuery('#st_fields').show();
        // set form validation
        jQuery('.poe-staddress-toggle').each(function(){
            jQuery(this).parent().children().eq(0).attr({'class':'required', 'aira-invalid':'false'}).append('<span class="star">&nbsp;*</span>');
            jQuery(this).attr({'class': 'poe-staddress-toggle required' ,'required':'required', 'aria-required' : 'true'});
        });
    }
}

/**
 * Updates the selected shipping string
 * The string is JSON encoded in the cart. To put it in this same format we have
 * to strip of the "jform_" naming.
 * 
 * Normally we use jQuery('.classname').serialArray(), but that will not work here 
 * due to jForm naming of name attribute, [] causes name to be treated as individual 
 * objects.
 */
function updateCarrier(){
    
    var carrierStr = '{';
    var name;
    var pos;
    
    jQuery('.poe-carrier').each(function(index){
        
        name = jQuery(this).attr('id');
        pos = name.indexOf('jform_');
        
        if(pos > -1){
            name = name.substring(6);
        }
        
        carrierStr += '"'+ name + '":"'+jQuery(this).val()+'"';
        
        if(index < jQuery('.poe-carrier').length - 1){
            carrierStr += ',';
        }
    });
    
    carrierStr += '}';
    
    jQuery('#jform_selected_shipping').val(carrierStr);
}

function removeFormPrefix(prefix, domID){
    var newDOMId;
    var pos = domID.indexOf(prefix);

    if(pos > -1){
        newDOMId = domID.substring(prefix.length);
    }else{
        newDOMId = domID;
    }
    
    return newDOMId;
}


function updateAddress(addressType){
    //note extra _id created by modal field type
    var juserID = jQuery('#jform_juser_id_id').val();
    var addressID;
    var addressFields;
    var dataValues = '{';
    var jtokenName = jQuery('#jtoken input').attr('name');
    var jtokenValue = jQuery('input[name='+jtokenName+']').val();
    var domID;
    var jformPrefix;
    
    if(addressType == 'BT'){
        addressID = jQuery('#jform_billing_id').val();
        addressFields = jQuery('.poe-btaddress');
        jformPrefix = 'jform_';
    }else if(addressType == 'ST'){
        addressID = jQuery('#jform_shipping_id').val();
        addressFields = jQuery('.poe-staddress');
        jformPrefix = 'jform_st_';
    }
    
    //create JSON string - only required becuase jForm name nomenclature messes up jQuery('#dom_el').serializeArray()
    addressFields.each(function(index){
        domID = removeFormPrefix(jformPrefix, addressFields.eq(index).attr('id'));
        
        dataValues += '"'+ domID  + '":"' + addressFields.eq(index).val() + '"';
        
        if(index < addressFields.length - 1){
            dataValues += ',';
        }
    });
    
    dataValues += '}';
    
    //alert(dataValues);
    
    if(addressID > 0 || addressID == 0 && addressType == 'ST'){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&task=address.update&format=raw',
            data: {juser_id : juserID, address_id : addressID, data_values : dataValues, address_type : addressType, jtoken_name : jtokenName, jtoken_value : jtokenValue },
            dataType: 'html',
            success: function(html, textStatus){
                //alert('html = ' + html);
                var updates = jQuery.parseJSON(html);
                
                if(updates){
                    if(updates['status'] == '1'){
                        alert('Address has been updated');
                        
                        if(updates['address_id'] != addressID){
                            if(addressType == 'BT'){
                                jQuery('#jform_billing_id').val(updates['address_id']);
                            }else{
                                jQuery('#jform_shipping_id').val(updates['address_id']);
                            }
                            //TODO: update order header
                            updateHeader();
                        }
                    }else{
                        alert('Address update failed');
                    }
                }
            },
                error: function(xhr, textStatus, errorThrown){
                    alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
                }
        });
    }
    
    function updateHeader(){
        jQuery('#toolbar-apply a').click();
    }
    
}