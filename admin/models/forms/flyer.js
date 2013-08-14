jQuery(function(){
    //check for set id
    var optionsetId = jQuery('#jform_id').val();
    
    if(optionsetId.length > 0){
        getOptions();
    }else{
        jQuery('#option_input').hide();
        jQuery('#option_list').hide();
    }

});
//modal return method
function modalUpateOptions(){
    //close modal
    SqueezeBox.close(); 
    //refresh option list
    getOptions();
}
function getOptions(){
    var optionsetId = jQuery('#jform_id').val();

    jQuery.ajax({
        type: 'POST',
        url: 'index.php?option=com_poecom&view=optionset&task=optionset.getOptions&format=raw',
        data: {id : optionsetId },
        dataType: 'html',
        success: function(html, textStatus){
            
            jQuery('#optionset_option_list_buttons').remove();
            jQuery('#options').replaceWith(html);
        },
        error: ''
   });  
}
//modal function
function addOption(){
   var href = jQuery('#modalPOEcom').attr('href');
   var rel = jQuery('#modalPOEcom').attr('rel');
   var optionsetId = jQuery('#jform_id').val();
   var hrefNew = href + 'option.display&view=option&optionset_id='+optionsetId+'&layout=optionsetmodal';
   var relNew = "{handler: 'iframe', size: {x: 700, y: 660}}";
   //override
   jQuery('#modalPOEcom').attr('href', hrefNew);
   jQuery('#modalPOEcom').attr('rel', relNew);
   //open modal
   jQuery('#modalLink').click();
   //reset
   jQuery('#modalPOEcom').attr('href', href);
   jQuery('#modalPOEcom').attr('rel', rel);
}
//modal function
function editOption(){
    //get slecteded option
    var cbk = jQuery('input[name="optionset[options]"]');
    var ids = [];
    var idx = 0;
    cbk.each(function(index){
       if(jQuery(this).is(":checked")){
           ids[idx] = index;
           idx++;
       }
    });
    
    if(ids.length > 1){
        alert('Only select one option to edit');
    }else if(ids.length > 0){
        
        var href = jQuery('#modalPOEcom').attr('href');
        var rel = jQuery('#modalPOEcom').attr('rel');
        var optionsetId = jQuery('#jform_id').val();
        var hrefNew = href + 'option.display&view=option&optionset_id='+optionsetId+'&layout=optionsetmodal&option_idx='+ids[0];
        var relNew = "{handler: 'iframe', size: {x: 700, y: 660}}";
        //override
        jQuery('#modalPOEcom').attr('href', hrefNew);
        jQuery('#modalPOEcom').attr('rel', relNew);
        //open modal
        jQuery('#modalLink').click();
        //reset
        jQuery('#modalPOEcom').attr('href', href);
        jQuery('#modalPOEcom').attr('rel', rel);
    }else{
        alert('Select an option first');
    }
    
    
   
}
function deleteOption(){
    var optionsetId = jQuery('#jform_id').val();
    var cbk = jQuery('input[name="optionset[options]"]');
    var ids = [];
    var idx = 0;
    cbk.each(function(index){
       if(jQuery(this).is(":checked")){
           ids[idx] = index;
           idx++;
       }
    });
    if(ids.length > 0){
    
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=optionset&task=optionset.deleteOption&format=raw',
            data: {optionset_id : optionsetId,
                option_ids : ids.toString()
                },
            dataType: 'html',
            success: function(html, textStatus){
                //console.log(html);
                var response = jQuery.parseJSON(html);

                if(response.msg){
                    alert(response.msg);
                }

                if(response.error == 0 ){
                    getOptions();
                }
            },
            error: ''
       });  
    }else{
        alert('Select an option first');
    }
}

