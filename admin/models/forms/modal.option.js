function saveOption(optionIdx){
    var optionsetId = jQuery('#optionset_id').val();
    var optionName = jQuery('#jform_name').val();
    var optionSku = jQuery('#jform_option_sku').val();
    var optionTypeId = jQuery('#jform_option_type_id').val();
    var uomId = jQuery('#jform_uom_id').val();
    var priceControlId = jQuery('#jform_price_control_id').val();
    var cssClass = jQuery('#jform_class').val();
    var detailId = jQuery('#jform_detail_id').val();
    var description = jQuery('#jform_description').val();
    
    if(optionName.length > 0 ){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=optionset&task=optionset.add&format=raw',
            data: {optionset_id : optionsetId,
                name : optionName,
                option_sku : optionSku,
                option_type_id : optionTypeId,
                price_control_id : priceControlId,
                option_class : cssClass,
                uom_id : uomId,
                detail_id : detailId,
                description : description,
                option_idx : optionIdx
                },
            dataType: 'html',
            success: function(html, textStatus){
                //console.log(html);
                var response = jQuery.parseJSON(html);

                if(response.msg){
                    alert(response.msg);
                }

                if(response.error == 0 ){
                    window.parent.modalUpateOptions();    
                }
            },
            error: ''
       });  
    }else{
        alert('Enter a name for the option first');
    }
}