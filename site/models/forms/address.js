window.addEvent('domready', function(){
   document.formvalidator.setHandler('postalcode', function(value) {
       var countryId = jQuery('#jform_country_id').val();
       var name = 'ZIP';
       if(countryId == '222'){
           //US
           regex = /^\b\d{5}(-\d{4})?\b$/;
           
       }else if(countryId == '38'){
           //Canada
           regex = /^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$/;
           name = 'Postal'
       }
       
       if(!regex.test(value)){
            alert(name+' Code not valid');
           
            return false;
       }else{
           return true;
       }
   });
});

jQuery(function(){
    var bypass = jQuery('#bypass').val();
    var addressID = jQuery('#jform_id').val();
    var enforceCCAddress = jQuery('#enforce_cc_address').val();
    
    if(enforceCCAddress == '1'){
        jQuery('#jform_stbt_same0').attr('disabled','disabled');
    }
   
    if(bypass == 1){
        var itemId = jQuery('#Itemid').val();
       
        // only after address update
        jQuery('#poecom-address').remove();
       
        closeModal(true, itemId);
    }else{
        var addressType = jQuery('#jform_address_type').val();
    
        if(addressType == 'BT'){
            jQuery('#address-skip').remove();
        }else if(addressID > 0){
            jQuery('input[name="jform[stbt_same]"]').eq(0).attr('checked', true);
            toggleSTFields();
        }else{
            // hide fields by default
            jQuery('#st_fields').hide();
            jQuery('#address-submit').hide();
            jQuery('#address-skip').show();
            
            // remove form validation
            jQuery('#poecom-address').removeAttr('class');
            jQuery('#address-submit').removeAttr('class');
        }
    }
    
    // prevent form submit with Enter on input fields
    jQuery('.poe-address').keypress(function(event){
        if ( event.which == 13 ) {
            event.preventDefault();
        }
    });
});


function updateRegions(){
    var countryID = jQuery('#jform_country_id').val();
    
    if(countryID > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=address&task=address.getRegions&format=raw',
            data: {
                country_id : countryID
            },
            dataType: 'html',
            success: function(html, textStatus){
                jQuery('#jform_region_id').replaceWith(html);
                jQuery('#jform_postal_code').val('');
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
        jQuery('#address-submit').hide();
        jQuery('#address-skip').show();
        
        removeValidation();

    }else{
        jQuery('#st_fields').show();
        jQuery('#address-submit').show();
        jQuery('#address-skip').hide();
        
        // add form validation
        jQuery('#poecom-address').attr('class', 'form-validate');
        jQuery('#address-submit').attr('class', 'validate');
        document.formvalidator = new JFormValidator();   
    }
}

function removeValidation(){
    // remove form validation
    jQuery('#poecom-address').removeAttr('class');
    jQuery('#address-submit').removeAttr('class');
    jQuery('#address-submit').attr('aria-invalid', "false");
    jQuery('input').attr('aria-invalid', "false");
    jQuery('label').attr('aria-invalid', "false");
    
    jQuery('input').each(function(index){
        var classApplied = jQuery('input').eq(index).attr('class');

        if(classApplied){
            var pos = classApplied.indexOf('invalid')
            if(pos){
                var classUpdate = classApplied.substring(0, pos);
                jQuery.trim(classUpdate);
                jQuery('input').eq(index).attr('class', classUpdate);
            }
        }
        
    });
    
    jQuery('label').each(function(index){
        var classApplied = jQuery('label').eq(index).attr('class');
        
        if(classApplied){
            var pos = classApplied.indexOf('invalid')
            if(pos){
                var classUpdate = classApplied.substring(0, pos);
                jQuery.trim(classUpdate);
                jQuery('label').eq(index).attr('class', classUpdate);
            }
        }
    });
}    


function closeModal(runUpdate, itemId){
    
    var stbtSame = jQuery('input[name="jform[stbt_same]"]:checked').val();
    var addressID = jQuery('#jform_id').val();
    
    if(addressID > 0 && stbtSame == '1'){
        // need to remove exiting ST
        jQuery('#poecom-address').submit();
    }else{
        //pass control to parent
        window.parent.updateAddresses(runUpdate,itemId, true); 
    }
}