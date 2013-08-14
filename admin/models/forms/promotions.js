jQuery(function(){
   //remove generic onclick
   jQuery('#toolbar-generate-coupon a').removeAttr('onclick');
   jQuery('#toolbar-generate-pdf a').removeAttr('onclick');
   
   //bind custom onlick
   jQuery('#toolbar-generate-coupon a').click(function(){
       var promoID;
       var checkedCount = 0;
       
       jQuery('input[name="cid[]"]').each(function(){
	   
	   if(jQuery(this).attr("checked")){
	       promoID = jQuery(this).val();
	       checkedCount++;
	   }
       });
       
       if(checkedCount > 1){
	  alert(Joomla.JText._('COM_POECOM_SELECT_MAX_ONE_MSG', 'missing lang var promotions.js')); 
       }else if(checkedCount == 0){
	  alert(Joomla.JText._('COM_POECOM_SELECT_ONE_MSG', 'missing lang var promotions.js')); 
      }else if(promoID > 0){
	  
	  var href = jQuery('#generateCoupons').attr('href');
	  var hrefNew = href+'&task=generatecoupons&layout=generate&promotion_id='+promoID;
	  jQuery('#generateCoupons').attr('href', hrefNew);
	  
	  jQuery('#modallink').click();
	  
	  jQuery('#generateCoupons').attr('href', href);
      }
       
   });
   
   //bind custom onlick
   jQuery('#toolbar-generate-pdf a').click(function(){
       var promoID;
       var checkedCount = 0;
       
       jQuery('input[name="cid[]"]').each(function(){
	   
	   if(jQuery(this).attr("checked")){
	       promoID = jQuery(this).val();
	       checkedCount++;
	   }
       });
       
	if(checkedCount > 1){
	    alert(Joomla.JText._('COM_POECOM_SELECT_MAX_ONE_MSG', 'missing lang var promotions.js')); 
	}else if(checkedCount == 0){
	    alert(Joomla.JText._('COM_POECOM_SELECT_ONE_MSG', 'missing lang var promotions.js')); 
	}else if(promoID > 0){

	    var href = jQuery('#generateCoupons').attr('href');
	    var hrefNew = href+'&task=generatepdf&layout=generatepdf&promotion_id='+promoID;
	    jQuery('#generateCoupons').attr('href', hrefNew);

	    jQuery('#modallink').click();

	    jQuery('#generateCoupons').attr('href', href);
	}
   });
});