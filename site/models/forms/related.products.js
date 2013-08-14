function rpAddToCart(productId){
    if(productId > 0){
        var jtokenName = jQuery('#jtoken input').attr('name');
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=cart&task=cart.ajaxAddItem&format=raw',
            data: {product_id : productId, quantity : 1, jtokenname : jtokenName },
            dataType: 'html',
            success: function(html, textStatus){
                //console.log(html);
                var response = jQuery.parseJSON(html);
		
		if(response.error == 0){
                    //Item added to cart
		    dialogTitle = Joomla.JText._('COM_POECOM_ITEM_ADDED');
		    jQuery('#dialogText').text(response.msg);
		    jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
				    text : Joomla.JText._('COM_POECOM_CHECKOUT'),
				    click :  function(){
					jQuery('#dialog').dialog("close");
                                        window.location = jQuery('#related-checkout-go').attr('href');
				    }
				},
                                {
				    text : Joomla.JText._('COM_POECOM_ADD_MORE'),
				    click :  function(){
					jQuery('#dialog').dialog("close");
				    }
				}]   });
		    jQuery('#dialog').dialog("open");
		}else{
		    //Item not added
		    dialogTitle = Joomla.JText._('COM_POECOM_ITEM_NOT_ADDED');
			
		    jQuery('#dialogText').text(response.msg);
		    jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
				    text : Joomla.JText._('COM_POECOM_CLOSE'),
				    click :  function(){
					jQuery('#dialog').dialog("close");
				    }
				}]   });
		    jQuery('#dialog').dialog("open");
		}
            },
            error: ''
           
       });  
    }
}

function closeMsgModal(login){
    //pass control to parent
    window.parent.showLogin(login); 
}