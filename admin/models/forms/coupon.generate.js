function checkAllUsers(){
    
    var selected = jQuery('#promo_users').val();
   
    if(selected == 'all'){
	var arrayStr = [];
	jQuery('#promo_users option').each(function(index){
	   
	    if(index > 0){
		//arrayStr += jQuery(this).val();
		arrayStr[index-1] = jQuery(this).val();
	    }
	});
	
	jQuery('#promo_users').val(arrayStr);
    }
}

jQuery('#adminForm').submit(function(){
    var promoType = jQuery('#promotion_type_id').val();
   
    switch(promoType){
	case '1': //Direct
	    var selected = jQuery('#promo_users').val();
	   
	    if(selected == null){
		alert(Joomla.JText._('COM_POECOM_NO_USERS_SELECTED_MSG', 'Select at least one user'));
		return false;
	    }
	    break;
	case '2': //General
	    break;
	case '3': //Numbered
	    break;
	default:
	    break;
    }
    
    return true;
    
});