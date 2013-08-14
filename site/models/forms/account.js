var updateSpinner;
var dialogTitle;
var showDialog;

jQuery(function(){
    jQuery('#dialog').dialog({autoOpen: false});
    // create spinner object
    startSpinner();
   
   /* update spinner */
   jQuery('#loadingDiv')
        .ajaxStart(function() {
            if(showDialog == 1){
                jQuery(this).show();
                updateSpinner.spin(document.getElementById('loadingDiv'));
                jQuery('#dialogText').text('Please wait');
                jQuery('#dialog').dialog({title: dialogTitle});
                jQuery('#dialog').dialog("open");
            }
        })
        .ajaxStop(function() {
        }); 
    
    jQuery('#accountinfo').accordion();
    
    toggleSTFields();
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

function updateRegions(){
    showDialog = 0;
    var countryID = jQuery('#jform_country_id').val();
    
    if(countryID > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=address&task=address.getRegions&format=raw',
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
function updateSTRegions(){
    showDialog = 0;  
    var countryID = jQuery('#jform_stcountry_id').val();
  
    if(countryID > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=address&task=address.getRegions&format=raw',
            data: {country_id : countryID, type : 'st' },
            dataType: 'html',
            success: function(html, textStatus){
                jQuery('#jform_stregion_id').replaceWith(html);
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
        //remove validation
        jQuery('.poe-staddress').each(function(){
            jQuery(this).removeAttr('class');
            jQuery(this).attr('class', 'poe-staddress');
            jQuery(this).attr('aria-required', 'false');
            jQuery(this).attr('aria-invalid', 'false');
            jQuery(this).removeAttr('required');
        });
        
        jQuery('.poe-staddress').prop('disabled', true);
        
    }else{
        //add validation
        jQuery('.poe-staddress').each(function(){
            jQuery(this).removeAttr('class');
            jQuery(this).attr('class', 'poe-staddress required');
            jQuery(this).attr('aria-required', 'true');
            jQuery(this).attr('required', 'required');
        });
        jQuery('.poe-staddress').prop('disabled', false);
        
    }
}

function updateProfile(){
    showDialog = 1;
    var juserID = jQuery('#juser_id').val();
    var password1 = jQuery('#jform_password1').val();
    var password2 = jQuery('#jform_password2').val();
    var email = jQuery('#jform_email').val();
    var name = jQuery('#jform_name').val();
    var jtokenName = jQuery('#jtoken input').attr('name');
    
    dialogTitle = 'Update Profile';
   
    jQuery.ajax({
        type: 'POST',
        url: 'index.php?option=com_poecom&view=account&task=account.updateProfile&format=raw',
        data: { juser_id : juserID, password : password1, password_2 : password2, email : email, jtoken : jtokenName, name : name  },
        dataType: 'html',
        success: function(html, textStatus){
           
            var response = jQuery.parseJSON(html);
            
            updateSpinner.stop();
            jQuery('#loadingDiv').hide();
            jQuery('#dialogText').text(response.msg);
           
        },
        error: function(xhr, textStatus, errorThrown){
            alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
        }
    });  
}

function updateBT(){
    showDialog = 1;
    var juserID = jQuery('#juser_id').val();
    var jtokenName = jQuery('#jtoken input').attr('name');
    var btId = jQuery('#jform_btid').val();
    var fname = jQuery('#jform_fname').val();
    var lname = jQuery('#jform_lname').val();
    var street1 = jQuery('#jform_street1').val();
    var street2 = jQuery('#jform_street2').val();
    var city = jQuery('#jform_city').val();
    var countryId = jQuery('#jform_country_id').val();
    var regionId = jQuery('#jform_region_id').val();
    var postalCode = jQuery('#jform_postal_code').val();
    var telephone = jQuery('#jform_telephone').val();
    
    dialogTitle = 'Update Billing Address';
   
    jQuery.ajax({
        type: 'POST',
        url: 'index.php?option=com_poecom&view=account&task=account.updateAddress&format=raw',
        data: { juser_id : juserID,
            jtoken : jtokenName,
            id : btId,
            address_type : 'BT',
            fname : fname,
            lname : lname,
            street1 : street1,
            street2 : street2,
            city : city,
            country_id : countryId,
            region_id : regionId,
            postal_code : postalCode,
            telephone : telephone},
        dataType: 'html',
        success: function(html, textStatus){
            var response = jQuery.parseJSON(html);
            
            updateSpinner.stop();
            jQuery('#loadingDiv').hide();
            jQuery('#dialogText').text(response.msg);
        },
        error: function(xhr, textStatus, errorThrown){
            alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
        }
    });
}
    
function updateST(){
    showDialog = 1;
    var juserID = jQuery('#juser_id').val();
    var jtokenName = jQuery('#jtoken input').attr('name');
    var stbtSame = jQuery('input[name="jform[stbt_same]"]:checked').val();
    var stId = jQuery('#jform_stid').val();
    var fname = jQuery('#jform_stfname').val();
    var lname = jQuery('#jform_stlname').val();
    var street1 = jQuery('#jform_ststreet1').val();
    var street2 = jQuery('#jform_ststreet2').val();
    var city = jQuery('#jform_stcity').val();
    var countryId = jQuery('#jform_stcountry_id').val();
    var regionId = jQuery('#jform_stregion_id').val();
    var postalCode = jQuery('#jform_stpostal_code').val();
    var telephone = jQuery('#jform_sttelephone').val();
    
    dialogTitle = 'Update Shipping Address';
   
    jQuery.ajax({
        type: 'POST',
        url: 'index.php?option=com_poecom&view=account&task=account.updateAddress&format=raw',
        data: { juser_id : juserID,
            jtoken : jtokenName,
            id : stId,
            stbt_same : stbtSame,
            address_type : 'ST',
            fname : fname,
            lname : lname,
            street1 : street1,
            street2 : street2,
            city : city,
            country_id : countryId,
            region_id : regionId,
            postal_code : postalCode,
            telephone : telephone},
        dataType: 'html',
        success: function(html, textStatus){
           
            var response = jQuery.parseJSON(html);
            
            updateSpinner.stop();
            jQuery('#loadingDiv').hide();
            jQuery('#dialogText').text(response.msg);
        },
        error: function(xhr, textStatus, errorThrown){
            alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
        }
    });  
}
function openPrintView(rfqNumber){
    window.open('index.php?option=com_poecom&task=request.printview&view=request&tmpl=component&rfq='+rfqNumber,'_blank','width=600,height=600',true);
}