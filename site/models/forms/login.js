function tryLogin(){
    var userName = jQuery('#username').val();
    var password = jQuery('#password').val();
    var jtokenName = jQuery('#jtoken input').attr('name');
  
    if(userName.length > 0){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=login&task=login.tryLogin&format=raw',
            data: {username : userName, password : password, jtokenname : jtokenName },
            dataType: 'html',
            success: function(html, textStatus){
                console.log(html);
                var response = jQuery.parseJSON(html);
		
		if(response['valid'] == 1){
                    var menuItemid = jQuery('#cart_itemid').val();
                    var url = 'index.php?';
                    if(menuItemid > 0){
                        url = url +'Itemid=' + menuItemid;
                    }else{
                        url = url +'option=com_poecom&view=cart';
                    }
                    routeUrl(url);
		}else{
		    //Login failed
		    dialogTitle = 'Invalid Credentials';
		    jQuery('#dialogText').text(response['errormsg']);
		    jQuery('#dialog').dialog({title: dialogTitle, buttons:[{
				    text : 'Close',
				    click :  function(){
					jQuery('#dialog').dialog("close");
				    }
				}]   });
		    jQuery('#dialog').dialog("open");
		}
            },
            error: function(xhr, textStatus, errorThrown){
                alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
            }
       });  
    }
}

function skipLogin(){
    var menuItemId = jQuery('#cart_itemid').val();
    var url = 'index.php?option=com_poecom&view=cart&skiplogin=1&Itemid='+menuItemId;
    routeUrl(url);
}

function closeMsgModal(login){
    //pass control to parent
    window.parent.showLogin(login); 
}