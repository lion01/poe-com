/* example
window.addEvent('domready', function() {
    document.formvalidator.setHandler('default-qty',
    function (value) {
        var maxQty = parseInt(document.getElementById('jform_max_qty').value);
        
        if(value > maxQty && maxQty != 0){
            alert(Joomla.JText._('COM_POECOM_PRODUCT_ERROR_DEFAULT_QTY','Default greater than Max'));
            return false;
        }else{
            return true;
        }
    });
});
*/

jQuery(function(){
    var stbtSame = jQuery('input[name="jform[stbt_same]"]:checked').val();
    if(stbtSame == '1'){
        toggleSTFields();
    }
});


function updateRegions(addressType){
    var countryId;
    
    if(addressType == 'BT'){
        countryId = jQuery('#bt_country_id').val();
    }else if(addressType == 'ST'){
        countryId = jQuery('#st_country_id').val();
    }else{
        countryId = 0;
    }        
    if(countryId > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=address&task=address.getRegions&format=raw',
            data: {country_id : countryId, address_type : addressType },
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
    var el;
    
    if(stbtSame == '1'){
        // remove form validation
        jQuery('.poe-staddress-toggle').each(function(){
            el = jQuery(this).parent().children().eq(0);
            //remove <span>*</span>
            jQuery(el).children().remove();
            
            jQuery(el).removeAttr('class');
            jQuery(el).removeAttr('aira-invalid');
            jQuery(this).attr({'class': 'poe-staddress-toggle', 'aria-required' : 'false'});
            jQuery(this).removeAttr('required');
        });
        jQuery('#stfields').hide();
    }else{
        // set regions
        updateRegions('ST');
        
        jQuery('#stfields').show();
        // set form validation
        jQuery('.poe-staddress-toggle').each(function(){
            jQuery(this).parent().children().eq(0).attr({'class':'required', 'aira-invalid':'false'}).append('<span class="star">&nbsp;*</span>');
            jQuery(this).attr({'class': 'poe-staddress-toggle required' ,'required':'required', 'aria-required' : 'true'});
        });
    }
}