<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Registration view class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class PoecomViewCart extends JView{
    
    public function updateShipping(){
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $rate_id = $jinput->get('rate_id', '', 'string');
        
        $jsess = JFactory::getSession();
        
        $shipping = $jsess->get('shipping', null, 'poecom');
        $cart = $jsess->get('cart', null, 'poecom');
        $taxes = $jsess->get('taxes', null, 'poecom');
        
        // Find the selected rate
        if($shipping->ship_methods){
            foreach($shipping->ship_methods as $sm){
                if($sm->rates){
                    foreach($sm->rates as $r){
                        if($r->id == $rate_id){
                            $shipping->cost = $r->cost;
                            $shipping->tax = $r->tax;
                            $shipping->id = $r->id;
                            
                            $jsess->set('shipping', $shipping, 'poecom');
                            $shipping = $jsess->get('shipping',null,'poecom');
                            
                            if(!$shipping->skip){
                                // Add shipping to totals
                                $model = $this->getModel('Cart');
                                $cart = $model->calculateTotal($cart, $taxes );
                                
                                $jsess->set('cart', $cart, 'poecom');
                                
                                $update = array('shipping' => $shipping->cost,
                                                'shipping_tax' => $shipping->tax,
                                                'total' => $cart['total'] );
                            }
                        }
                    }
                }
            }
        }
        
        if($update){
            
            $html = json_encode($update);
        }else{
            $html = 'update failed';
        }
        
        echo $html;
        
    }
    
    /**
     * Apply promotional discount coupon to order 
     */
    public function useCoupon(){
	$response = array('valid' => 0, 'error_msg' => '', 'discount_amount' => 0, 'total' => 0, 'product_tax' => 0);
	
	$app = JFactory::getApplication();
        $jinput = $app->input;
        
        $coupon_code = $jinput->get('coupon_code','', 'string');
        
        $jsess = JFactory::getSession();
        
        $cart = $jsess->get('cart', null, 'poecom');
        
	//get the coupon
	$model = $this->getModel('Coupon');
	if(($coupon = $model->getCouponByCode($coupon_code))){
	    //validate coupon
	    $valid = $model->validate($coupon, $cart);
	    if($valid->approved){
		//$model = $this->getModel('Cart');
		//apply coupon
		$cart = $model->applyCoupon($coupon, $cart);
		if(!$cart['discount']->enabled){
		   $response['error_msg'] = JText::_('COM_POECOM_COUPON_CODE_NOT_APPLIED_MSG')." : ".$coupon_code; 
		}else{
		    $response['valid'] = 1;
		    $response['discount_amount'] = $cart['discount_amount'];
		    $response['total'] = $cart['total'];
		    $response['product_tax'] = $cart['product_tax'];
		}
	    }else{
		$response['error_msg'] = $valid->reject_msg;
	    }  
	}else{
	    $response['error_msg'] = JText::_('COM_POECOM_COUPON_CODE_INVALID_MSG')." : ".$coupon_code;
	}
	
	if($response){
            $html = json_encode($response);
        }
        
        echo $html;
    }
    
    public function ajaxAddItem(){
        $response = new JObject();
        $response->error = 0;
        $response->showdetail = 0;
        $response->msg = JText::_('COM_POECOM_AJAX_ATC_SUCCESS');
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $jsess = JFactory::getSession();
        
        $cart = $jsess->get('cart', array(), 'poecom');
        if(!$cart){
            $jsess->set('cart', array(), 'poecom');
            $cart = array();
        }
        
        $product_id = $jinput->get('product_id', 0, 'INT');
        $qty = $jinput->get('quantity', 1, 'INT');
        
        if($product_id > 0){
            $pmodel = $this->getModel('Product');
            if (($prod = $pmodel->getProductDetail($product_id))){
                
                //check cart for existing quantity
                if(!empty($cart['items'])){
                    foreach($cart['items'] as $itm){
                        if($itm->product_id == $product_id && $itm->quantity + $qty > $prod->max_qty ){
                            //product in cart already and max qty 
                            $response->error = 1;
                            $response->msg = JText::_('COM_POECOM_AJAX_ATC_FAIL_MAX_QTY');
                        }
                    }
                }
                
                if($response->error == 0){
                
                    if(!$prod->options){
                        $model = $this->getModel('Cart');

                        $cart_item = new JObject;
                        $cart_item->product_id = $product_id;
                        $cart_item->product_sku = $prod->sku;
                        $cart_item->product_name = $prod->name;
                        $cart_item->type = $prod->type;
                        $cart_item->quantity = $qty;
                        $cart_item->properties = $prod->properties;
                        $cart_item->serial_options = '';
                        $cart_item->selected_options = '';
                        $cart_item->ship_modifiers = '';
                        $cart_item->price = $prod->price;
                        $cart_item->total = $prod->price * $qty;

                        $taxes = array();
                        if((!$cart = $model->addItem($cart, $cart_item, $taxes ))){
                            $response->error = 1;
                            $response->msg = JText::_('COM_POECOM_AJAX_ATC_FAIL_ADD');
                        }else{
                            //set currency
                             if(!isset($cart['currency'])){
                                //currency
                                $params = JComponentHelper::getParams('com_poecom');
                                $currency_id = $params->get('base_currency');

                                $model = JModel::getInstance('Currencies', 'PoecomModel');

                                $cart['currency'] = $model->getItems($currency_id);
                            }  
                            $jsess->set('cart', $cart, 'poecom');
                            $response->miniHTML = $this->updateMiniCart($cart);
                        }
                    }else{
                        //product options need to be set
                        $response->error = 1;
                        $response->showdetail = 1;
                        $response->msg = JText::_('COM_POECOM_AJAX_ATC_FAIL_OPTIONS');
                    }
                }
            }else{
                $response->error = 1;
                $response->msg = JText::_('COM_POECOM_AJAX_ATC_FAIL_PRODUCT');
            } 
        }else{
            $response->error = 1;
            $response->msg = JText::_('COM_POECOM_AJAX_ATC_FAIL_PRODUCT_ID');
        }
        
        if($response){
            $html = json_encode($response);
        }
        
        echo $html;
    }
    
    /**
     * Get updated content for mini cart
     * 
     * @param array $cart Current cart session variable
     * @return string $html
     */
    private function updateMiniCart($cart){
        $has_items = false;
        $icon = 'cart-icon-empty.png';
        $msg = JText::_('MOD_POECART_EMPTY_MSG');

        $params = JComponentHelper::getParams('com_poecom');

        $useHTTPS = $params->get('usehttps', 0);

        $base_url = JURI::root();

        if($useHTTPS == 1){
            $base_url = str_ireplace('http:','https:', $base_url);
        }
        
         if(isset($cart['items']) ){
            $has_items = true;
            $icon = 'cart-icon.png';
            $msg = JText::_('MOD_POECART_HAS_ITEMS_MSG');
        }

        $html = '<div id="poe-mini-icon">
            <img src="modules/mod_poecart/'.$icon.'" alt="Cart"/>
        </div>
        <div id="poe-mini-items">';
        if($has_items){
            $html .= '<div id="poe-mini-msg">'.count($cart['items']). ' ' . $msg.'</div>';
            $html .= '<div id="poe-mini-total">'.JText::_('MOD_POECART_TOTAL').' '.$cart['currency'][0]->symbol. number_format($cart['total'], 2).'</div>';
            $html .= '<div id="poe-mini-link"><a href="'.$base_url."index.php?option=com_poecom&view=cart".'">'.JText::_('MOD_POECART_VIEW_CART').'</a></div>';
        }else{
            $html .= '<div id="poe-mini-msg">'.$msg.'</div>';
        }
        
        return $html;
    }
}

