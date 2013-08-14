<?php
defined('_JEXEC') or die('Restricted access');
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
$app = JFactory::getApplication();
$jsess = JFactory::getSession();

$appMsg = $jsess->get('appMsg', '', 'poecom');

if(strlen($appMsg)){
    $appMsgType = $jsess->get('appMsgType', 'message', 'poecom');
    $app->enqueueMessage($appMsg, $appMsgType);
    
    //clear message
    $jsess->clear('appMsg','poecom');
    $jsess->clear('appMsgType','poecom');
}

if($this->type == 'order' ){ 
    $title = JText::_('COM_POECOM_ORDER_TITLE');
    $number_label = JText::_('COM_POECOM_ORDER_NUMBER');
    $number = $this->data->id;
    $onclick = " onclick=\"openPrintView('order', '".$this->data->id."')\"";
}else{
    $title = JText::_('COM_POECOM_RFQ_TITLE');
    $number_label = JText::_('COM_POECOM_RFQ_NUMBER');
    $number = $this->data->rfq_number;
    $onclick = " onclick=\"openPrintView('rfq', '".$this->data->rfq_number."')\"";
}

$message = JText::_('COM_POECOM_CONFIRMATION_EMAILED_MSG');
?>
<div id="dialog" title="">
    <div id="loadingDivContainer">
        <div id="loadingDiv"></div>
        <div id="dialogText"></div>
    </div>
</div>
<div id="cart-wrap">
<div id="cart-container">
    <h1><?php echo $title; ?></h1>
        <div id="cart-header">
            <label><?php echo $number_label;  ?>: </label>
            <label><?php echo $number;  ?></label>
            
        </div>
    <div id="confirmation-msg"><label><?php echo $message; ?></label>
        <button type="button" class="poe-button poe-corner-all" <?php echo $onclick ?>><?php echo JText::_('COM_POECOM_PRINT');?></button>
    </div>
<div id="cart-address">
    <div id="bt-address">
        <div>
            <dl>
                <dt class="cart-title"><?php echo JText::_('COM_POECOM_USER_BT_LBL'); ?></dt>
                <dd><?php echo $this->data->bt->fname.' '.$this->data->bt->lname; ?></dd>
                <dd><?php echo $this->data->bt->street1 ?></dd>
                <dd><?php echo $this->data->bt->street2 ?></dd>
                <dd><?php echo $this->data->bt->city . ', '. $this->data->bt->region. ', '.$this->data->bt->postal_code ?></dd>
                <dd><?php echo $this->data->bt->country ?></dd>
                <dd><?php echo $this->data->bt->telephone ?></dd>
                <dd><?php echo $this->data->bt->email ?></dd>
                <dd><input type="hidden" name="bt_id" id="bt_id" value="<?php echo $this->data->bt->id ?>" /></dd>
            </dl>
        </div>
    </div>
    <div id="st-address">
        <?php if(!empty($this->data->st)){ ?>
            <div>
            <dl>
                <dt class="cart-title"><?php echo JText::_('COM_POECOM_USER_ST_LBL'); ?></dt>
                <dd><?php echo $this->data->st->fname.' '.$this->data->st->lname; ?></dd>
                <dd><?php echo $this->data->st->street1 ?></dd>
                <dd><?php echo $this->data->st->street2 ?></dd>
                <dd><?php echo $this->data->st->city . ', '. $this->data->st->region. ', '.$this->data->st->postal_code ?></dd>
                <dd><?php echo $this->data->st->country ?></dd>
                <dd><?php echo $this->data->st->telephone ?></dd>
                <dd><input type="hidden" name="st_id" id="st_id" value="<?php echo $this->data->st->id ?>" /></dd>
            </dl>
        </div>
       <?php }else{ ?>
            <div>
                    <div><label class="cart-title"><?php echo JText::_('COM_POECOM_USER_ST_LBL'); ?></label></div>
            </div>
            <div><?php echo JText::_('COM_POECOM_USER_STBT_SAME_LABEL') ?></div>
        <?php } ?>
    </div>
</div>
<div id="cart-list">
    <div id="cart-list-hd">
        <ul>
            <li><div id="cart-prod-hd"><?php echo JText::_('COM_POECOM_CART_PROD_HD') ?></div></li>
            <li><div id="cart-price-hd"><?php echo JText::_('COM_POECOM_CART_PRICE_HD') ?></div></li>
            <li><div id="cart-qty-hd"><?php echo JText::_('COM_POECOM_CART_QTY_HD') ?></div></li>
            <li><div id="cart-line-total-hd"><?php echo JText::_('COM_POECOM_CART_LINE_TOTAL_HD') ?></div></li>
        </ul>
    </div>
    <div id="cart-container-items">
        <?php 
            if($this->data->items){
                $idx = 0;
                foreach($this->data->items as $itm){
                    $html = '<div class="cart-item">';
                    $html .= '<div class="cart-item-id">'.JText::_('COM_POECOM_CART_LINE_SKU').': '.$itm->product_sku;
                    $html .= '<div>'.$itm->list_description.'</div>';
                    if($itm->properties){
                        $html .= '<ul>';

                        foreach($itm->properties as $property){
                            $html .= '<li>'.$property->name. ' : '. $property->option_label. '</li>';
                        }
                        $html .= '</ul>';
                    }
                    if($itm->selected_options){
                        $html .= '<ul>';
                        
                        foreach($itm->selected_options as $op => $val){
                            $html .= '<li>'.$op. " : ". $val. '</li>';
                        }
                            $html . '</ul>';     
                    }
                    $html .='</div>';
                    $html .= '<div class="cart-item-price">'.number_format($itm->price, 2).'</div>';
                    $html .= '<div class="cart-item-qty"><div>'.$itm->quantity.'</div>';
                    $html .= '</div>';
                    $html .= '<div class="cart-item-total">'.number_format($itm->total, 2).'</div>';
                    $html .= '</div>';
                    
                    echo $html;
                    
                    $idx++;
                }
            }else{ ?>
                <div class="cart-item"><?php echo JText::_('COM_POECOM_CART_EMPTY_MSG') ?></div>
       <?php     } ?>
    </div>
    
    <div id="cart-bottom-container">
        <div id="shipping-wrap">
        <div id="cart-shipping">
            <?php if(!empty($this->data->carrier) ){
                $language = JFactory::getLanguage();
                //load plugin site language
                $language->load('plg_poecomship_'.$this->data->carrier->plugin, 'plugins/poecomship/'.$this->data->carrier->plugin.'/', $language->getTag(), true);
                
                    $html = '<div class="carrier-container"><div class="carrier-logo">';
                
                    if(strlen($this->data->carrier->carrier_logo)){
                        $html .= '<img src="'.$this->data->carrier->carrier_logo.'" alt="'.JText::_($this->data->carrier->carrier).'"/>';
                    }else{ 
                        $html .= JText::_($this->data->carrier->carrier);
                    }
                    
                    $html .= '</div>';
                    $html .= '<div class="ship-rate-container">';
                    $html .= '<div class="ship-rate-radio">';
                    $html .= '</div><div class="ship-rate">';
                    $html .= '<div class="ship-rate-service">'.JText::_($this->data->carrier->service). '</div>';
                    $html .= '<div class="ship-rate-eta">' . JText::_($this->data->carrier->eta).'</div></div></div>';
                    $html .= '</div>';
                    
                    echo $html;
              
            } ?>
        </div>
        <div id="cart-totals-wrap">
        <div id="cart-list-totals">
            <div id="cart-totals-lbl">
                <ul>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_LINE_SUBTOTAL_LBL')?></div></li>
                    <?php if(!empty($this->data->discount_amount)){ ?>
                        <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_DISCOUNT_LBL')?></div></li>
                    <?php }
                    if(!empty($this->data->product_tax) && $this->data->product_tax > 0 ){?>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_PROD_TAX_LBL')?></div></li>
                    <?php }
                    if(!empty($this->data->shipping_cost) && $this->data->shipping_cost > 0){ ?>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_SHIP_COST_LBL')?></div></li>
                    <?php }
                    if(!empty($this->data->shipping_tax) && $this->data->shipping_tax > 0){ ?>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_SHIP_TAX_LBL')?></div></li>
                    <?php } ?>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_TOTAL_LBL')?></div></li>
                </ul>
            </div>
            <div id="cart-totals-amt">
                <ul>
                    <li><div class="cart-totals"><?php echo $this->data->currency->symbol.number_format($this->data->subtotal,2) ?></div></li>
                    <?php if(!empty($this->data->discount_amount)){ ?>
		    <li><div class="cart-totals" id="discount_amount"><?php echo number_format($this->data->discount_amount, 2); ?></div></li>
                    <?php }
                    if(!empty($this->data->product_tax) && $this->data->product_tax > 0){ ?>
                    <li><div class="cart-totals"><?php echo number_format($this->data->product_tax, 2) ?></div></li>
                    <?php }
                    if(!empty($this->data->shipping_cost) && $this->data->shipping_cost > 0){ ?>
                    <li><div class="cart-totals" id="shipping_cost"><?php echo number_format($this->data->shipping_cost, 2) ?></div></li>
                    <?php }
                    if(!empty($this->data->shipping_tax) && $this->data->shipping_tax > 0){ ?>
                    <li><div class="cart-totals" id="shipping_tax"><?php echo number_format($this->data->shipping_tax, 2) ?></div></li>
                    <?php } ?>
                    <li><div class="cart-totals" id="total"><?php echo $this->data->currency->symbol.number_format($this->data->total, 2) ?></div></li>
                </ul>
            </div>
        </div>
        </div>
        </div>
	<div id="poe-coupon">
	    <?php
	    if(!empty($this->data->coupon_code)){ ?>
	    <label><?php echo JText::_('COM_POECOM_COUPON_CODE_USED_LBL') . ": " . $this->data->coupon_code; ?></label>
	    <?php }?>
	</div>
        <div id="pay-methods">
            <?php if(!empty($this->data->paymethod)){
                $language = JFactory::getLanguage();
                //load plugin site language
                $language->load('plg_poecompay_'.$this->data->paymethod->plugin, 'plugins/poecompay/'.$this->data->paymethod->plugin.'/', $language->getTag(), true);
                
                ?>
           
            <div class="pay-method">
                 <label><?php echo JText::_('COM_POECOM_ORDER_EMAIL_PAY_METHOD');?></label>
                <div class="pay-method-logo">
                    <?php if(strlen($this->data->paymethod->logo)){ ?>
                        <img src="<?php echo $this->data->paymethod->logo ?>" alt="<?php echo JText::_($this->data->paymethod->name); ?>"/>
                    <?php }else{
                        echo JText::_($this->data->paymethod->name);
                    } ?>
                </div>
                 <?php if(!empty($this->data->payment) && $this->data->payment->type != 3 ){ ?>
               
                <div class="pay-method-radio">
                    <div><?php echo JText::_($this->data->payment->name) . ' : '. JText::_('COM_POECOM_PAYMENT_STATUS'). ' - ' . $this->data->payment->status;  ?></div>
                    <div><?php echo JText::_('COM_POECOM_PAYMENT_TXN_ID'). ' : '. $this->data->payment->transaction_number;  ?></div>
                </div>
                <?php if($this->data->mandatory_fields){ ?>
                <div>
                    <dl>
                        <dt id="poe-txn-receipt"><?php echo JText::_('COM_POECOM_PAYMT_RECEIPT_TITLE'); ?></dt>
                    <?php foreach($this->data->mandatory_fields as $k => $v){ ?>
                        <dd><?php echo $k .' : ' . $v ?></dd>  
                    <?php } ?>
                    </dl>
                </div>
                <?php } 
                }?>
             </div>
        <?php } ?>
        </div>
         <?php if(strlen($this->return_policy) ){ ?>
        <div id="cart-return-policy">
            <div><?php echo JText::_('COM_POECOM_RETURN_POLICY_TITLE'); ?></div>
            <?php echo $this->return_policy ?>
        </div>
        <?php } ?>
    </div>
</div>
</div>
</div>
<script type="text/javascript">
    function openPrintView(docType, docId){
        window.open('index.php?option=com_poecom&task=request.printview&view=request&tmpl=component&doc_type='+docType+'&doc_id='+docId,'_blank','width=600,height=600',true);
    }
    
    function checkPaymentStatus(){
        
        var rfqNumber = jQuery('#rfq_number').val();

        if(rfqNumber.length > 0){
        jQuery.ajax({
                type: 'POST',
                url: 'index.php?option=com_poecom&task=payment.getpaymentstatus&view=payment&format=raw',
                data: {rfq_number : rfqNumber},
                dataType: 'html',
                success: function(html, textStatus){
                    var response = jQuery.parseJSON(html);
                    var dialogTitle = 'Payment Status';
                    switch(response['payment_status']){
                        case '1': //pending
                            //Request has been stored but payment confirmation not received in time
                            jQuery('#dialogText').text('Payment confirmation has been received, but the status is Pending. Your order has been saved and copy has been emailed to you. Customer Service will contact you shortly.');
                            jQuery('#dialog').dialog({title: dialogTitle});
                            jQuery('#dialog').dialog("open");
                            break;
                        case '2': //complete
                            //Payment transaction completed 
                            jQuery('#dialogText').text('Payment confirmation has been received, close this box this see the order.');
                            jQuery('#dialog').dialog({title: dialogTitle, close: showRequest(rfqNumber)});
                            jQuery('#dialog').dialog("open");
                            break;
                        case '3': //failed
                            jQuery('#dialogText').text('Payment transaction failed. Your request has been saved and copy has been emailed to you. Customer Service will contact you shortly.');
                            jQuery('#dialog').dialog({title: dialogTitle});
                            jQuery('#dialog').dialog("open");
                            break;
                        case '4': //waiting
                        default:
                            jQuery('#dialogText').text('Payment status is unknown. Your request has been saved and copy has been emailed to you. Customer Service will contact you shortly.');
                            jQuery('#dialog').dialog({title: dialogTitle});
                            jQuery('#dialog').dialog("open");
                            break;
                    }
                },
                error: function(xhr, textStatus, errorThrown){
                    alert('An error occurred ' + ( errorThrown ? errorThrown: xhr.status) );
                }
            }); 
        }
    }
    
    function showRequest(rfqNumber){
        window.location.replace('index.php?option=com_poecom&task=request.display&view=request&rfq='+rfqNumber);    
    }
</script>