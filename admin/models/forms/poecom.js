/**
 * Call JRoute::_(url) inorder to get SEF URL
 */
function routeUrl(url){
    var sefUrl = url;
    
    if(url.length){
        
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=poecom&task=poecom.routeJSUrl&format=raw',
            data: {routeurl : url},
            dataType: 'html',
            success: function(html, textStatus){
               
                var response = jQuery.parseJSON(html);
                
                if(response.routed == '1'){
                    
                    sefUrl = response.sefUrl;
                    //console.log('setting SEF :' + sefUrl);
                    window.location.replace(sefUrl);
                }else{
                    window.location.replace(url);
                }
            },
            error: '' //silent fail
        });
    } 
}

/**
 * Get full path for window.location.replace
 * 
 * FF fix
 */
function fullPathUrl(url){
    if(url.length > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=poecom&task=poecom.fullPathJSUrl&format=raw',
            data: {relative_url : url},
            dataType: 'html',
            success: function(html, textStatus){
               //console.log(html);
                var response = jQuery.parseJSON(html);
                
                if(response.error == 0){
                    window.location.replace(response.fullpath);
                }else{
                    //try relative path
                    window.location.replace(url);
                }
            },
            error: '' //silent fail
        });
    } 
}

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