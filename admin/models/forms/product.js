jQuery(function(){
   /*tabs*/
   jQuery('#tabs').tabs(); 
   //bind onclick
   jQuery('#ui-id-5').click(function(){
        getOptions();
    });
});

window.addEvent('domready', function() {
    document.formvalidator.setHandler('default-qty',
    function (value) {
        var maxQty = parseInt(document.getElementById('jform_max_qty').value);
        
        if(value > maxQty && maxQty !== 0){
            alert(Joomla.JText._('COM_POECOM_PRODUCT_ERROR_DEFAULT_QTY','Default greater than Max'));
            return false;
        }else{
            return true;
        }
    });
});
function resetProduct(){
    toggleOptionSource('set');
}
function resetOptionSet(){
    var productId = jQuery('#jform_id').val();
    var sourceProductId = jQuery('#jform_copyoptions').val();
    //console.log('id : ' + productId + ' source id : ' + sourceProductId);
    if(productId !== sourceProductId){
        toggleOptionSource('prod');
    }else{
        alert('You can not copy product options to itself');
        toggleOptionSource('set');
    }
}

function toggleOptionSource(source){
    if(source === 'set'){
        //copy optionset - clear selected product
        jQuery('#jform_copyoptions').val('');
        jQuery('#jform_copyoptions_name').val('');
    }
    if(source === 'prod'){
        //copy product options - clear optionset selected
        jQuery('#jform_generateoptions').val(0);
    }
}
function generateOptions(){
    var productId = jQuery('#jform_id').val();
    var copyProductId = jQuery('#jform_copyoptions').val();
    var optionSetId = jQuery('#jform_generateoptions').val();
    var appendOptions = jQuery('input[name="jform[appendoptions]"]:checked').val();
    
    if(optionSetId.length > 0 && optionSetId !== 0){
        //generate options from an optionset
        jQuery.ajax({
            type : 'POST',
            url : 'index.php?option=com_poecom&task=optionset.generateOptions&format=raw',
            data : {id : optionSetId,
                product_id : productId,
                append_options : appendOptions},
                dataType : 'html',
            success : function(html){
                var response = jQuery.parseJSON(html);
                
                if(response.msg){
                    alert(response.msg);
                }
                
                if(response.error === 0){
                    getOptions();
                }
            },
            error : ''
        });
    }else if(copyProductId.length > 0 && copyProductId !== 0){
        //copy another products options
    }else{
        alert('Select an Option Set or Product first');
    }
}

//modal return method
function modalUpateOptions(){
    //close modal
    SqueezeBox.close(); 
    //refresh option list
    getOptions();
}
function getOptions(){
    var productId = jQuery('#jform_id').val();

    jQuery.ajax({
        type: 'POST',
        url: 'index.php?option=com_poecom&view=product&task=product.getOptions&format=raw',
        data: {product_id : productId },
        dataType: 'html',
        success: function(html, textStatus){
            //console.log(html);
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
   var productId = jQuery('#jform_id').val();
   var hrefNew = href + 'option.display&view=option&product_id='+productId+'&layout=productoptionmodal';
   var relNew = "{handler: 'iframe', size: {x: 450, y: 680}}";
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
        var productId = jQuery('#jform_id').val();
        var hrefNew = href + 'option.display&view=option&product_id='+productId+'&layout=productoptionmodal&option_idx='+ids[0];
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
    var productId = jQuery('#jform_id').val();
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
            url: 'index.php?option=com_poecom&view=option&task=option.deleteOptionByProductId&format=raw',
            data: {product_id : productId,
                option_ids : ids.toString()
                },
            dataType: 'html',
            success: function(html, textStatus){
                //console.log(html);
                var response = jQuery.parseJSON(html);

                if(response.msg){
                    alert(response.msg);
                }

                if(response.error === 0 ){
                    getOptions();
                }
            },
            error: ''
       });  
    }else{
        alert('Select an option first');
    }
}