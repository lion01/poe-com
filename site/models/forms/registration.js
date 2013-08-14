function updateRegions(){
    var countryID = jQuery('#jform_country_id').val();
    
    if(countryID > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=address&task=address.getRegions&format=raw',
            data: {country_id : countryID },
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

function closeModal(runUpdate){
    var itemID = jQuery('#ItemId').val();
    
    //pass control to parent
    window.parent.updateAddresses(runUpdate, itemID, true); 
}

function checkName(){
   
    var userName = jQuery('#jform_email1').val();
    
    if(userName.length){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&task=registration.checkUserName&format=raw',
            data: {username : userName},
            dataType: 'html',
            success: function(html, textStatus){
                var response = jQuery.parseJSON(html);
                
                if(response.found == 1){
                    //close model and redirect to login
                    dialogTitle = Joomla.JText._('COM_POECOM_EMAIL_REGISTERED');
					jQuery('#dialogText').text(response.msg);
					jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
				    text : Joomla.JText._('COM_POECOM_CLOSE'),
				    click :  function(){
					window.parent.showLogin(true);
				    }
				}]   });
		    jQuery('#dialog').dialog("open");
                }else{
                    validateEmail();
                }
            },
            error: function(xhr, textStatus, errorThrown){
                alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
            }
        });
    }
}

function validateEmail(){
    var userName = jQuery('#jform_email1').val();
    var userNameConfirm = jQuery('#jform_email2').val();
   
    if(userNameConfirm.length){
        if(userName != userNameConfirm){
            dialogTitle = Joomla.JText._('COM_POECOM_EMAIL_MATCH');
            jQuery('#dialogText').text(Joomla.JText._('COM_POECOM_EMAIL_NO_MATCH'));
            jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
                            text : Joomla.JText._('COM_POECOM_CLOSE'),
                            click :  function(){
                                jQuery('#dialog').dialog("close");
                                jQuery('#jform_email2').val('');
                                jQuery('#jform_email2').focus();
                            }
                        }]   });
            jQuery('#dialog').dialog("open");
        }
    }
}