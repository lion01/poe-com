jQuery(function(){
    jQuery('#toolbar-cancel a').removeAttr('onclick');
    jQuery('#toolbar-cancel a').attr('onclick', 'showList()');
});

function showList(){
    var url = 'administrator/index.php?option=com_poecom&view=customers';
    fullPathUrl(url);
}

function updateUser(){
    var id = parseInt(jQuery('#jform_id').val());
    var name = jQuery('#jform_name').val();
    var email = jQuery('#jform_email').val();
    
    if(id > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&task=customer.updateuser&format=raw',
            data: { id : id, name : name, email : email},
            dataType: 'html',
            success: function(html) {
                
                var response = jQuery.parseJSON(html);
                
                if (response.msg) {
                    alert(response.msg);
                }
            },
            error: ''
        });
    }
}

function updateBT(){
    var id = parseInt(jQuery('#jform_btid').val());
    var fname = jQuery('#jform_fname').val();
    var lname = jQuery('#jform_lname').val();
    var street1 = jQuery('#jform_street1').val();
    var street2 = jQuery('#jform_street2').val();
    var city = jQuery('#jform_city').val();
    var countryId = jQuery('#jform_country_id').val();
    var regionId = jQuery('#jform_region_id').val();
    var postalCode = jQuery('#jform_postal_code').val();
    var telephone = jQuery('#jform_telephone').val();
    
    if(id > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&task=customer.updateAddress&format=raw',
            data: { id : id, fname : fname, lname : lname, street1 : street1,
                street2 : street2, city : city, region_id : regionId,
                country_id : countryId, postal_code : postalCode, telephone : telephone},
            dataType: 'html',
            success: function(html) {
                console.log(html);
                var response = jQuery.parseJSON(html);
                
                if (response.msg) {
                    alert(response.msg);
                }
            },
            error: ''
        });
    }
}

function updateRegions(addressType){
    var countryID;
        
    if(addressType === 'BT'){
        countryID = jQuery('#jform_country_id').val();
    }else if(addressType === 'ST'){
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
                if(addressType === 'BT'){
                    jQuery('#jform_region_id').replaceWith(html);
                }else{
                    jQuery('#jform_st_region_id').replaceWith(html);
                }
            },
            error: ''
       });  
    }
}