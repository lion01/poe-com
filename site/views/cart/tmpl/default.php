<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Cart Template
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 4:24:14 PM
 *
 * http://www.exps.ca
**/
JHTML::_('behavior.modal', 'a.cartmodal'); 
$shipping_block = false;
$c_symbol = $this->item['currency'][0]->symbol;
?>
<div id="dialog" title="">
    <div id="loadingDivContainer">
        <div id="loadingDiv"></div>
        <div id="dialogText"></div>
    </div>
</div>
<div id="cart-wrap">
<div id="cart-container">
<h1><?php echo JText::_('COM_POECOM_CART_TITLE'); ?></h1>
<div id="msg-target">
    <div id="request-number"></div>
    <div id="request-msg"></div>
</div>
<div id="cart-address">
    <div id="bt-address">
    <?php if($this->user_bt){ ?>
        <div>
            <dl>
                <dt class="cart-title"><?php echo JText::_('COM_POECOM_USER_BT_LBL'); ?><button class="address-update-btn" type="button" id="update_bt" onclick="updateAddress('BT')" title="Update Billing Address"></button></dt>
                <dd><?php echo $this->user_bt->full_name ?></dd>
                <dd><?php echo $this->user_bt->street1 ?></dd>
                <dd><?php echo $this->user_bt->street2 ?></dd>
                <dd><?php echo $this->user_bt->city . ', '. $this->user_bt->region. ', '.$this->user_bt->postal_code ?></dd>
                <dd><?php echo $this->user_bt->country ?></dd>
                <dd><?php echo $this->user_bt->telephone ?></dd>
                <dd><?php echo $this->user_bt->email ?></dd>
                <dd><input type="hidden" name="bt_id" id="bt_id" value="<?php echo $this->user_bt->id ?>" /></dd>
            </dl>
        </div>
    <?php }else{ ?>
        <div>
            <div><label class="cart-title"><?php echo JText::_('COM_POECOM_USER_BT_NOT_SET'); ?></label></div>
        </div>
    <?php } ?>
    </div>
    
    <div id="st-address">
        <?php if($this->user_st){ ?>
            <div>
            <dl>
                <dt class="cart-title"><?php echo JText::_('COM_POECOM_USER_ST_LBL'); ?><button class="address-update-btn" type="button" id="update_st" onclick="updateAddress('ST')" title="Update Shipping Address"></button></dt>
                <dd><?php echo $this->user_st->full_name ?></dd>
                <dd><?php echo $this->user_st->street1 ?></dd>
                <dd><?php echo $this->user_st->street2 ?></dd>
                <dd><?php echo $this->user_st->city . ', '. $this->user_st->region. ', '.$this->user_st->postal_code ?></dd>
                <dd><?php echo $this->user_st->country ?></dd>
                <dd><?php echo $this->user_st->telephone ?></dd>
               <!-- <dd><?php //echo $this->user_st->email ?></dd> -->
                <dd><input type="hidden" name="st_id" id="st_id" value="<?php echo $this->user_st->id ?>" /></dd>
            </dl>
        </div>
       <?php }else{ ?>
            <div>
                <div><label class="cart-title"><?php echo JText::_('COM_POECOM_USER_ST_LBL'); ?></label>
                <?php if($this->user_bt && $this->enforce_cc_address === 1){ ?>
                <button class="address-update-btn" type="button" id="update_st" onclick="updateAddress('ST')" title="Update Shipping Address"></button></div>
                <?php }else{ ?>
				</div>
                <?php } ?>
            </div>
            <div><?php echo JText::_('COM_POECOM_USER_STBT_SAME_LABEL') ?></div>
        <?php } ?>
    </div>
</div>
<div id="cart-msg"><?php echo $this->tax_msg; ?></div>
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
            if(isset($this->item['items'])){ 
                $idx = 0;
                foreach($this->item['items'] as $itm){
                 
                    $html = '<div class="cart-item">';
                    $html .= '<div class="cart-item-id">';
                    $html .= '<div class="cart-product-name">'.$itm->product_name.'</div>';
                    $html .= '<div>'.JText::_('COM_POECOM_CART_LINE_SKU').': '.$itm->product_sku.'</div>';
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
                        }
                    $html .= '</ul>';
                    
                    if(!empty($itm->selected_options)){ 
                        //configuration changes possible
                        $html .= '<div><button class="poe-button poe-corner-all" type="button" name="change_item" id="change_item" onclick="cartChangeItem('.$idx.','.$itm->product_id.')" title="'.JText::_('COM_POECOM_CART_LINE_CHANGE').'"><span class="ui-button-text">'.JText::_('COM_POECOM_CART_LINE_CHANGE').'</span></button></div>';
                    }
                    
                    $html .='</div>';
                    $html .= '<div class="cart-item-price">'.number_format($itm->price, 2).'</div>';
                    $html .= '<div class="cart-item-qty"><div>'.$itm->quantity.'</div>';
                    
                    $html .= '<div><button class="poedeletebutton" type="button" name="delete_item" id="delete_item" onclick="cartDeleteItem('.$idx.')" title="'.JText::_('COM_POECOM_CART_LINE_DELETE').'"></button></div>';
                    
                    $html .= '</div>';
                    $html .= '<div class="cart-item-total">'.number_format($itm->total, 2).'</div>';
                    $html .= '</div>';
                    
                    echo $html;
                    
                    $idx++;
                }
            }else{ ?>
                <div class="cart-item">
                    <div class="cart-item-id">
                    <div class="cart-product-name"><?php echo JText::_('COM_POECOM_CART_EMPTY_MSG') ?></div>
                          
                    </div>
                    <div class="cart-item-price"><?php echo number_format(0, 2) ?></div>
                    <div class="cart-item-qty"><div>0</div>
                    </div>
                    <div class="cart-item-total"><?php echo number_format(0, 2) ?></div>
                </div>
       <?php     } ?>
    </div>
    
    <div id="cart-bottom-container">
	<div id="shipping-wrap">
        <div id="cart-shipping">
            <?php if(isset($this->shipping->ship_methods)){
                $language = JFactory::getLanguage();
                foreach($this->shipping->ship_methods as $sm){
                    //load language
                    $language->load('plg_poecomship_'.$sm->plugin, 'plugins/poecomship/'.$sm->plugin.'/', $language->getTag(), true);
                
                    $html = '<div class="carrier-container"><div class="carrier-logo">';
                
                    if(!empty($sm->logo)){
                        $html .= '<img src="'.$sm->logo.'" alt="'.JText::_($sm->name).'"/>';
                    }else{ 
                        $html .= JText::_($sm->name);
                    }
                    
                    $html .= '</div>';
                   
                    if($sm->rates){
                        
                        foreach($sm->rates as $r){
                            if($r->id == $this->shipping->id){
                                $checked = 'checked="checked"';
                            }else{
                                $checked = '';
                            }
                            
                            $html .= '<div class="ship-rate-container">';
                            $html .= '<div class="ship-rate-radio">';
                            $html .= '<input type="radio" name="ship_rate" id="ship_rate" value="'.$r->id.'" '.$checked.' onchange="updateShipping()" />';
                            $html .= '</div><div class="ship-rate">';
                            $html .= '<div class="ship-rate-service">'.JText::_($r->service). '</div>';
                            $html .= '<div class="ship-rate-cost">' . $c_symbol.number_format($r->cost, 2) . '</div>';
                            $html .= '<div class="ship-rate-eta">' . JText::_($r->eta).'</div></div></div>';
                        }
                    }else{
                        $shipping_block = true;
                        $html .= '<div class="ship-rate-container">';
                        $html .= '<div class="ship-rate-radio">';
                        $html .= '<div style="max-width:300px;">'.JText::_('COM_POECOM_SHIP_RATES_ERROR').'</div>';
                        $html .= '</div></div>';
                    }
                    
                    $html .= '</div>';
                    
                    echo $html;
                }
                
            } ?>
        </div>
        <div id="cart-totals-wrap">
        <div id="cart-list-totals">
            <div id="cart-totals-lbl">
                <ul>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_LINE_SUBTOTAL_LBL')?></div></li>
                    <?php if($this->item['discount_amount'] > 0){ ?>
		    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_DISCOUNT_LBL')?></div></li>
                    <?php }
                    if($this->item['product_tax'] > 0){?>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_PROD_TAX_LBL')?></div></li>
                    <?php }
                    if(isset($this->shipping->ship_methods) ){ ?>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_SHIP_COST_LBL')?></div></li>
                    <?php if($this->shipping->tax > 0){ ?>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_SHIP_TAX_LBL')?></div></li>
                    <?php 
                        }
                    } ?>
                    <li><div class="cart-lbl"><?php echo JText::_('COM_POECOM_CART_TOTAL_LBL')?></div></li>
                </ul>
            </div>
            <div id="cart-totals-amt">
                <ul>
                    <li><div class="cart-totals"><?php echo $c_symbol.number_format($this->item['subtotal'],2) ?></div></li>
		    <?php if($this->item['discount_amount'] > 0){ ?>
                    <li><div class="cart-totals" id="discount_amount"><?php echo number_format($this->item['discount_amount'], 2); ?></div></li>
                    <?php }
                    if($this->item['product_tax'] > 0){?>
                    <li><div class="cart-totals" id="product_tax"><?php echo number_format($this->item['product_tax'], 2) ?></div></li>
                    <?php }
                    if(isset($this->shipping->ship_methods) ){ ?>
                    <li><div class="cart-totals" id="shipping_cost"><?php echo number_format($this->shipping->cost, 2) ?></div></li>
                    <?php if($this->shipping->tax > 0){ ?>
                    <li><div class="cart-totals" id="shipping_tax"><?php echo number_format($this->shipping->tax, 2) ?></div></li>
                    <?php }
                    } ?>
                    <li><div class="cart-totals" id="total"><?php echo $c_symbol.number_format($this->item['total'], 2) ?></div></li>
                </ul>
            </div>
        </div>
        </div>
	</div>
	<?php if($this->item['entrystatus'] == 'shipping') { ?>
        <?php if($this->use_coupon){ ?>
	<div id="poe-coupon">
	    <?php if($this->item['discount']->status_id == 2){ ?>
	    <label><?php echo JText::_('COM_POECOM_COUPON_CODE_USED_LBL'); ?></label>
	    <input type="text" name="coupon_code" id="coupon_code" value="<?php echo $this->item['discount']->coupon_code ?>" />
	    <?php }else{ ?>
	    <label><?php echo JText::_('COM_POECOM_COUPON_CODE_LBL'); ?></label>
	    <input type="text" name="coupon_code" id="coupon_code" value="" />
	    <button name="use_coupon" id="use_coupon" class="poe-button poe-corner-all" onclick="useCoupon()"><?php echo JText::_('COM_POECOM_USE_COUPON'); ?></button>
	    
	    <?php } ?>
	</div>
        <?php } ?>
	<?php } ?>
        <div id="pay-methods">
        <?php
        if($shipping_block === false && $this->pay_methods && isset($this->item['items'])){ 
            $language = JFactory::getLanguage();
            foreach($this->pay_methods as $pm){

                //load plugin site language
                $language->load('plg_poecompay_'.$pm->plugin, 'plugins/poecompay/'.$pm->plugin.'/', $language->getTag(), true);
               
                if($pm->pm_default == 1){
                    $checked = 'checked="checked"';
                    if($pm->type == 3 || $pm->type == 1){
                        //preorder or credit card
                        // show confirm button
                        $show_confirm = true; 
                    }else{
                        $show_confirm = false;
                    }
                }else{
                    $checked = '';
                }
                
                ?>
            <div class="pay-method">
                <div class="pay-method-logo">
                    <?php if(!empty($pm->logo)){ ?>
                        <img src="<?php echo $pm->logo ?>" alt="<?php echo JText::_($pm->name); ?>"/>
                    <?php }else{
                        echo JText::_($pm->name);
                    } ?>
                </div>
                <div class="pay-method-radio">
                    <input type="radio" name="pay_method" id="pay_method" value="<?php echo $pm->id ?>" <?php echo $checked ?>/><span class="poe-radio-name"><?php echo JText::_($pm->name); ?></span>
                </div>
                <?php if($pm->type == 1){ //cc direct
                    //show credit card form
                    ?>
                    <div id="pay_method_cc_container">
                        <dl>
                            <dt class="poe-cc-accept"><?php echo JText::_('COM_POECOM_CC_ACCEPTED'). " : ";
                                if($pm->accepted_cards){
                                    $html = '';
                                    foreach($pm->accepted_cards as $card){
                                        $html .= '<img src="'.JURI::root(true).'/media/com_poecom/images/cclogos/'.$card->list_logo.'" alt="'.$card->name.'" />';
                                    }
                                    echo $html;
                                }
                            
                            ?></dt>
                            <dt class="poe-cc-cardholder-info"><?php echo JText::_('COM_POECOM_CC_TITLE'); ?></dt>
                            <dt class="poe-cc-dt"><?php echo JText::_('COM_POECOM_CC_NAME'); ?></dt>
                            <dd class="poe-cc-dd"><input id="cc_name" value="" /></dd>
                            <dt class="poe-cc-dt"><?php echo JText::_('COM_POECOM_CC_NUMBER'); ?></dt>
                            <dd class="poe-cc-dd"><input id="cc_number" value="" /></dd>
                            <dt class="poe-cc-dt"><?php echo JText::_('COM_POECOM_CC_CVV'); ?></dt>
                            <dd class="poe-cc-dd"><input id="cc_cvv" value="" size="5" /></dd>
                            <dt class="poe-cc-dt"><?php echo JText::_('COM_POECOM_CC_EXPIRY'); ?></dt>
                            <dd class="poe-cc-dd"><?php echo JHTML::_('select.genericList', $this->cc_months, 'cc_expiry_month',null, 'val', 'text' );
                            echo JHTML::_('select.genericList', $this->cc_years, 'cc_expiry_year',null, 'val', 'text' ); ?></dd>
                        </dl> 
                    </div>
               <?php } ?>
            </div>
        <?php }
        } ?>
        </div>
        <?php if(strlen($this->return_policy) && $this->item['entrystatus'] == 'shipping'){ ?>
        <div id="cart-return-policy">
            <?php echo $this->return_policy ?>
        </div>
        <?php } 
        if(strlen($this->terms_link) && $this->item['entrystatus'] == 'shipping' ){ ?>
        <div id="cart-terms">
            <div>
                <input type="checkbox" name="agree_terms" id="agree_terms"/><span id="poe-terms-msg"><?php echo JText::_('COM_POECOM_AGREE_TERMS_MSG'); ?></span>
            </div>
            <div>
                <span id="poe-terms-link"><a href="<?php echo $this->terms_link ?>" target="_blank"><?php echo JText::_('COM_POECOM_TERMS_LINK_TEXT'); ?></a></span>
                <span id="poe-privacy-link"><a href="<?php echo $this->privacy_link ?>" target="_blank"><?php echo JText::_('COM_POECOM_PRIVACY_POLICY_LINK_TEXT'); ?></a></span>
            </div>
        </div>
        <?php  } ?>
        <div id="cart-continue">
            <?php 
                if(isset($this->item['items']) ){
                   $next_button = $this->item['entrystatus'];
                }else{
                    //empty cart show products button
                    $next_button = '';
                }
                
                switch($next_button){
                    case 'cart':    // items selected in cart
                        $user = JFactory::getUser();
                        if($user->guest == 1){
                            $onclick = 'setBilling()';
                        }else{
                            $onclick = "updateAddress('BT')";
                        }
                        
                        $button_text = JText::_('COM_POECOM_CART_NEXT_BILLING');
                        break;
                    case 'shipping': // shipping selected
                        $onclick = 'setPayment()';
                        $button_text = JText::_('COM_POECOM_CART_NEXT_CONFIRM');
                        break;
                    case 'payment': // payment selected
                        $onclick = 'setConfirm()';
                        $button_text = JText::_('COM_POECOM_CART_NEXT_CONFIRM');
                    case 'rfq': // request for quote submitted
                        break;
                    default:
                        $onclick = 'setProductPage()';
                        $button_text = JText::_('COM_POECOM_CART_NEXT_DEFAULT_PRODUCT');
                        break;
                }
              
                if(($this->item['entrystatus'] != 'expired' || $this->item['entrystatus'] != 'rfq' )&& !$shipping_block ){
            ?>
            <button class="poe-button poe-corner-all" type="button" name="next_step" id="next_step" onclick="<?php echo $onclick ?>"><?php echo $button_text ?></button>
            <input type="hidden" name="rfq_number" id="rfq_number" value=""/>
            <input type="hidden" id="status_check_limit" value="0" />
            <input type="hidden" id="Itemid" name="Itemid" value="<?php echo $this->cart_itemid ?>" />
            <input type="hidden" id="cart_itemid" value="<?php echo $this->cart_itemid ?>" />
            <input type="hidden" id="product_itemid" value="<?php echo $this->product_itemid ?>" />
                <?php } ?>
        </div>
        
        <div id="hmodal">
            <a class="cartmodal" id="modalPOEcom" rel="{handler: 'iframe', size: {x: 550, y: 600}}" 
                href="index.php?option=com_poecom&tmpl=component&task=" target="_blank" >
                <button type="button" id="modallink" style="border: none; background: none;"></button></a>
        </div>
        <div id="jtoken"><?php echo JHTML::_( 'form.token' ); ?></div>
    </div>
</div>
</div>
</div>
<script src="<?php echo $this->script?>" type="text/javascript"></script>