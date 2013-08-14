jQuery(function(){
    updateImageLink();
});

function toggleImageButton(){
    var productId = jQuery('#jform_product_id').val();
    
    if(productId == 0){
        jQuery('#imageModal').hide();
    }else{
        jQuery('#imageModal').show();
    }
}

function updateImageLink(){
    toggleImageButton();
    var productId = jQuery('#jform_product_id').val();
    var imageType = jQuery('#jform_type').val();
    var link = jQuery('#imageModal').attr('href');
    
    //set opposite image type
    if(imageType == '1'){
        imageType = '2';//thumb
    }else{
        imageType = '1';//main
    }
    
    if(productId > 0){
        var pos = link.indexOf('product_id=');
        if(pos >= 0){
            link = link.substring(0,pos) + 'product_id='+productId+'&type_id='+imageType;
        }else{
            link = link+'&product_id='+productId+'&type_id='+imageType;
        }
        jQuery('#imageModal').attr('href', link);
    }
}