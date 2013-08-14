/**
 * Product Options E-commerce Extension
 * 
 * @author Micah Fletcher
 * @copyright 2011 - 2012 Extensible Point Solutions Inc. All Right Reserved
 * @license GNU GPL version 3, http://www.gnu.org/copyleft/gpl.html
 * @link http://www.exps.ca
 * @version 2.5.0
 * @since 2.5
**/ 
var inputQty;
var updateSpinner;
var imgContainerHeight;

jQuery(function(){
    // Control is stop update on first load
    var runUpdate = jQuery('#run_update').val();
  
   // Added for page reload
   // fire change on first option
   if(jQuery('.optioninput').length > 0 && runUpdate == 1){
        jQuery('.optioninput').eq(0).change();
   }
   
   // prevent form submit with Enter on optioninput field
   jQuery('.optioninput').keypress(function(event){
        if ( event.which == 13 ) {
            event.preventDefault();
        }
    });
   
   // Set to 1 for reloads
   jQuery('#run_update').val(1);
   
   startSpinner();
   
   /* price update spinner */
   jQuery('#loadingDiv')
        .hide()  // hide it initially
        .ajaxStart(function() {
            jQuery(this).show();
            updateSpinner.spin(document.getElementById('loadingDiv'));
        })
        .ajaxStop(function() {
            updateSpinner.stop();
            jQuery(this).hide();
        })
    ;
    
    /* price update spinner */
   jQuery('#price-display')
        .show()  // hide it initially
        .ajaxStart(function() {
            jQuery(this).hide();
        })
        .ajaxStop(function() {
            jQuery(this).show();
        })
    ;
    
    // assign validate to form
   jQuery('form[name="addtocart"]').validate({
        errorElement: "div",
        wrapper: "div",  // a wrapper around the error message
        errorPlacement: function(error, element) {
            error.appendTo(element.parent())
            error.addClass('err_msg');  // add a class to the wrapper
            error.css('left', jQuery('.poe-option').width());
        },
        messages: { 
           registersite: {
               required: "The Site URL is required for registration",
               url: "Please use format http://www.yoursite.com"
            }
    } 
    });
 
    jQuery('form[name="addtocart"]').submit(function(){
        // Update serialize options field
        jQuery('#serial_options').val(jQuery('.optioninput').serialize());
    });
    
    init_tabs();
    
    var images = jQuery('#product-image img');
    
    if(images.length > 1){
        //must be more than one slide
        jQuery('#product-image').after('<div id="nav-container"><ul id="nav">').cycle({ 
        fx:     'fade', 
        speed:  'fast', 
        timeout: 0, 
        pager:  '#product-nav', 

        // callback fn that creates a thumbnail to use as pager anchor 
        pagerAnchorBuilder: function(idx, slide) { 
            //get img src
            var src = jQuery('#product-image img').eq(idx).attr('src');
            return '<li><a href="#" onclick="setHeight()" onmouseover="getImageHeight('+idx+')"><img src="' + src + '" width="50" height="50" /></a></li>'; 
        } 
        
        });
        
        jQuery('#nav-container').append('<button type="button" id="imgzoom" onclick="showMediaPopUp()"><img src="media/com_poecom/images/magnify.png"/></button>');
    }
    //get image container height
    jQuery('.img-container').each(function(i){
        if(jQuery('.img-container').eq(i).css('opacity') == 1){
            imgContainerHeight = jQuery('.img-container').eq(i).height();
            setHeight();
        }
    });
});

function startSpinner(){
    var opts = {
          lines: 6, // The number of lines to draw
          length: 5, // The length of each line
          width: 3, // The line thickness
          radius: 1, // The radius of the inner circle
          color: '#FFA500', // #rgb or #rrggbb
          speed: 1.5, // Rounds per second
          className: 'pricespinner',
          trail: 35, // Afterglow percentage
          shadow: false, // Whether to render a shadow
          hwaccel: false // Whether to use hardware acceleration
         
    };
    var target = document.getElementById('loadingDiv');
    
    updateSpinner = new Spinner(opts).spin(target);
 
}

jQuery('.register-site').change(function(){
    var site = jQuery(this).val();
    var httpPos = site.indexOf('http://');
  
    if(httpPos < 0 ){
        httpPos = site.indexOf('https://');
    }
  
    if(httpPos < 0 ){
        jQuery(this).val('http://' + site);
    }
});

function validateURL(url){
    
}

function buttonControl(){
    jQuery('#addtocart').hide();
    jQuery('#get-price-but').show();
}

function quantityFocus(){
    inputQty = parseInt(jQuery('input[name="quantity[]"]').val());
    buttonControl();
}

function quantityBlur(){
    var maxQty = parseInt(jQuery('#max_qty').val());
    var updateQty = parseInt(jQuery('input[name="quantity[]"]').val());
    
    if(inputQty != updateQty ){
        if(updateQty <= maxQty || maxQty == 0){
            updatePrice('qtyButton', false);
        }else{
            alert('max allowed');
            jQuery('input[name="quantity[]"]').val(maxQty);
            if(inputQty == maxQty){
                jQuery('#addtocart').show();
                jQuery('#get-price-but').hide();
            }
        }
        
    }else{
        jQuery('#addtocart').show();
        jQuery('#get-price-but').hide();
    }
}

function updatePrice(option, cycle){
    var optionName = jQuery(option).attr('name');
 //   var optionValue;
    if(cycle ){
        // Check if this option is an image control, if yes fire cycleImage(index)
        for(var i = 0; i <imageGroups.length; i++){
            
            if(optionName == imageGroups[i][11] ){
                cycleImage(i);
            }
        }
    }
    
    var currentStageNum = jQuery('#current_stage').val();
    var lastStageNum = jQuery('#last_stage').val();
 
    if(currentStageNum == lastStageNum){
    
    // Hide addtocart
    buttonControl();
    
    if(option == 'qtyButton'){
        optionName = option;
  //      optionValue = 0;
    }else{
        optionName = jQuery(option).attr('name');
  //      optionValue = jQuery(option).val();
    }
 
    var productID = jQuery('#product_id').val();
    var options = '';
    var quantity = jQuery('#quantity'+productID).val();
    var idx = 0;
    
    // Create string with options and values
    jQuery('.optioninput').each(function(){
        options += jQuery(this).attr('name') + "=" + jQuery(this).val();
        if(idx < (jQuery('.optioninput').length - 1) ){
            options += "&";
        }
        idx++;
    });
    
     jQuery('#addtocart').hide();
     if(productID > 0 ){
        // Check constraints of this options before updating price
        setPrice(productID,options, quantity);
        /* Constraints not implemented in 2.0.1
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&controller=constraints&task=checkConstraints&format=raw',
            data: {product_id : productID, option_name : optionName, option_value : optionValue, options : options },
            dataType: 'html',
            success: function(html, textStatus){
                //alert(html);
                if(html == 'OK'){
                    // Value wihtin constraints do nothing
                    setPrice(productID,options, quantity);
                    
                }else{
                    // Adjust value
                    // Get the adjusted option value and update display
                    // Example: Colours,green;Width,1000;Drop,4500;Stand,white;Fittings,0;Arms,overhang
                    var optionsArray = html.split(";");
                    var optionArray = new Array();
                    
                    for(var i = 0; i < optionsArray.length; i++){
                        optionArray = optionsArray[i].split(",");
                        
                        if(optionArray[0] == optionName){
                            jQuery('#'+optionName).val(optionArray[1]);
                           
                            setPrice(productID,options, quantity);
                            inlineMsg(optionName, 'Constraint Value', 3);
                        }
                    }
                }
            },
                error: function(xhr, textStatus, errorThrown){
                    alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
                }
            });
            */
        }
    }
}

function setPrice(productID,options, quantity ){
    var productTaxRate = jQuery('#product_tax_rate').val();
    
    if(productID > 0 ){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?option=com_poecom&view=productprice&task=ProductPrice.getProductPrice&format=raw',
            data: {product_id : productID, options : options, quantity : quantity, product_tax_rate : productTaxRate },
            dataType: 'html',
            success: function(html, textStatus){
                //console.log(html);
                jQuery('#price-display').text(html);
                jQuery('#price').val(html);
                jQuery('#get-price-but').hide();
                jQuery('#addtocart').show();
            },
            error: function(xhr, textStatus, errorThrown){
                alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
            }
       });  
    }
}

function qtyUp(){
    var productID = jQuery('#product_id').val();
    var quantity = parseInt(jQuery('#quantity'+productID).val());
    var maxQty = parseInt(jQuery('#max_qty').val());
    
    if(quantity < maxQty || maxQty == 0){
        jQuery('#quantity'+productID).val(quantity+1);
    
        updatePrice('qtyButton', false);
    }else{
        alert('max allowed');
        jQuery('#quantity'+productID).val(maxQty);
    }
    
    
}

function qtyDown(){
    var productID = jQuery('#product_id').val();
    var quantity = jQuery('#quantity'+productID).val();
    
    if( parseInt(quantity) > 1 ){
        jQuery('#quantity'+productID).val(quantity-1);
        updatePrice('qtyButton',false);
    }else{
        jQuery('#quantity'+productID).val(1);
    }
}
function init_tabs(){
    if(!jQuery('ul.poe-tabs').length){
        return;
    }
   
    jQuery('div.poe-tab-content').hide();
    
    // Show inital content
    jQuery('div.poe-tab-content-wrap').each(function(){
        jQuery(this).find('div.poe-tab-content:first').show();
    });
    
    // Listen for click on tabs
    jQuery('ul.poe-tabs a').click(function(){
        
        // If not current tab
        if(!jQuery(this).hasClass('current')){
            //Change the current indicator
            jQuery(this).addClass('current').parent('li').siblings('li').find('a.current').removeClass('current');
            
            //Show target, hide others
            jQuery(jQuery(this).attr('href')).show().siblings('div.poe-tab-content').hide();
        }
        
        // Nofollow
        this.blur();
        return false;
        
    });
}
function getImageHeight(idx){
    imgContainerHeight = jQuery('.img-container img').eq(idx).height();
}

function setHeight(){
    jQuery('#product-image').css({'min-height' : imgContainerHeight});
     
}