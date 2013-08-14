jQuery(function(){
    //if region not set reset regions for country
    var regionId = jQuery('#jform_region_id').val();
    
    if(!regionId.length > 0 || regionId == 0){
        updateRegions(); 
    }
});
function updateRegions(){
    var countryID;
    var addressType = 'BT'; //required by updateRegions()
    countryID = jQuery('#jform_country_id').val();
    
    if(countryID > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=address&task=address.getRegions&format=raw',
            data: {country_id : countryID, address_type : addressType },
            dataType: 'html',
            success: function(html, textStatus){
                jQuery('#jform_region_id').replaceWith(html); 
            },
            error: function(xhr, textStatus, errorThrown){
                alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
            }
       });  
    }
}
