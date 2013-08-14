function updateRegions(){
    var countryID = jQuery('#jform_country_id').val();
    
    if(countryID > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=registration&task=registration.getRegions&format=raw',
            data: {country_id : countryID },
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