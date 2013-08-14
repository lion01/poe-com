<?php

defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Cart HTML View Class
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 9:48:34 PM
 *
 * http://www.exps.ca
 * */
jimport('joomla.application.component.view');

/**
 * HTML View class for the Cart
 */
class PoecomViewCart extends JView {

    protected $item;
    protected $state;

    // Overwriting JView display method
    function display($tpl = null) {
        // Create aplication input and params objects 
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $params = JComponentHelper::getParams('com_poecom');
        $cart_itemid = $params->get('cartitemid', 0, 'INT');
        $product_itemid = $params->get('productitemid', 0, 'INT');
        $enforce_cc_address = $params->get('enforceccaddress', 0, 'INT');

        $this->assignRef('Itemid', $cart_itemid);
        $this->assignRef('cart_itemid', $cart_itemid);
        $this->assignRef('product_itemid', $product_itemid);
        $this->assignRef('enforce_cc_address', $enforce_cc_address);

        // Get session handle
        $jsess = JFactory::getSession();

        $delete_item_idx = $jinput->get('deleteitemidx', -1, 'INT');
        $skip_login = $jinput->get('skiplogin', 0, 'INT');
        $skip_related = $jinput->get('skiprelated', 0, 'INT');

        if ($delete_item_idx > -1) {
            // Confirm token from URI query
            JRequest::checkToken('get') or die('Invalid Token');
        } else if ($_POST) {
            // Confirm token from form post
            JRequest::checkToken() or die('Invalid Token');
        }

        /**
         * Check for an item change
         * Remove cart item at $change_item_idx before updated item is added 
         */
        $change_item_idx = $jinput->get('change_item_idx', -1, 'int');

        if ($change_item_idx > -1) {
            $model = $this->getModel('Cart');
            $model->deleteItem($change_item_idx);
        }

        $currency_id = $params->get('base_currency');

        $model = $this->getModel('Currencies');

        $base_currency = $model->getItems($currency_id);
        $taxes = array();

        $p_info = $jsess->get('p_info', '', 'poecom');
        //check for product in session
        //when session expires values are cleared
        if (empty($p_info->product_id)) {
            // Get product data from input
            $product_id = (int) $jinput->get('product_id', 0, 'int');
            $related_group_id = (int) $jinput->get('related_group_id', 0, 'int');
            $product_sku = $jinput->get('product_sku', '*', 'string');
            $product_name = $jinput->get('product_name', '*', 'string');
            $product_type = $jinput->get('product_type', 0, 'int');
            $quantities = $jinput->get('quantity', 0, 'array');
            $quantity = $quantities[0];
            $max_qty = $jinput->get('max_qty', 0, 'int');
            $price = $jinput->get('price', 0, 'float');
            // from jQuery.serialize() URL encoded string of optioninput
            $serial_options = $jinput->get('serial_options', '', 'string');
            //$json_properties = $jinput->get('json_properties', '', 'string');
           
        } else {
            // Get product data from session
            $product_id = (int) $p_info->product_id;
            $related_group_id = (int) $p_info->related_group_id;
            $product_sku = $p_info->product_sku;
            $product_name = $p_info->product_name;
            $product_type = $p_info->product_type;
            $quantities = $p_info->quantities;
            $quantity = $quantities[0];
            $max_qty = $p_info->max_qty;
            $price = $p_info->price;
            // from jQuery.serialize() URL encoded string of optioninput
            $serial_options = $p_info->serial_options;
        }

        // check the user state
        $user = JFactory::getUser();
        $geo_data = $jsess->get('geodata', null, 'browser_geodata');

        if ($user->guest == 1 ) {
            // Apply default taxes
            //$jsess = JFactory::getSession();
            if (!empty($geo_data)) {
                // Set taxes
                $model = $this->getModel('Tax');
                $taxes = $model->getTaxRates($geo_data['country_id'], $geo_data['region_id']);
                $jsess->set('taxes', $taxes, 'poecom');
                $tax_msg = JText::_('COM_POECOM_TAX_COUNTRY_MSG') . ': ' . $geo_data['countryName'] . ', ' . JText::_('COM_POECOM_TAX_REGION_MSG') . ': ';
            } else {
                //location unknown
                $tax_msg = JText::_('COM_POECOM_REGION_UNKNOWN');
            }
            
            $this->assignRef('tax_msg', $tax_msg);
            
            if($params->get('showcrosssell', 0) == 1 && $product_id > 0 && $skip_related == 0 && $delete_item_idx === -1 ){
                //store product info in session
                $p_info = new JObject();
                $p_info->product_id = $product_id;
                $p_info->related_group_id = $related_group_id;
                $p_info->product_sku = $product_sku;
                $p_info->product_name = $product_name;
                $p_info->product_type = $product_type;
                $p_info->quantities = $quantities;
                $p_info->max_qty = $max_qty;
                $p_info->price = $price;
                $p_info->serial_options = $serial_options;

                $jsess->set('p_info', $p_info, 'poecom');

                $app->redirect('index.php?option=com_poecom&view=relatedproducts&related_group_id='.$related_group_id.'&product_id='.$product_id);
            }

            if ($params->get('loginprompt', 0) == 1 && $skip_login == 0 && $delete_item_idx === -1) {
                //store product info in session
                $p_info = new JObject();
                $p_info->product_id = $product_id;
                $p_info->related_group_id = $related_group_id;
                $p_info->product_sku = $product_sku;
                $p_info->product_name = $product_name;
                $p_info->product_type = $product_type;
                $p_info->quantities = $quantities;
                $p_info->max_qty = $max_qty;
                $p_info->price = $price;
                $p_info->serial_options = $serial_options;

                $jsess->set('p_info', $p_info, 'poecom');

                $app->redirect(JRoute::_('index.php?option=com_poecom&view=login'));
            }
            $user_bt = '';
            $user_st = '';
        } else {
            // User logged in
            // Get user billing and shipping addresses
            $amodel = $this->getModel('Address');

            $user_bt = $amodel->getAddress($user->id, 'BT');
           
            $user_st = $amodel->getAddress($user->id, 'ST');
            if(!empty($user_bt)){
                $user_bt->email = $user->email;
            }

            // Assign the tax 
            if (!empty($user_st)) {
                //TODO - handle ST this is really for tax method allocation 
                $country_id = $user_st->country_id;
                $region_code = $user_st->region_id;
                $country = $user_st->country;
            } else if (!empty($user_bt)) {
                //TODO - handle ST this is really for tax method allocation 
                $country_id = $user_bt->country_id;
                $region_id = $user_bt->region_id;
                $country = $user_bt->country;
            } else {
                $country_id = $geo_data['country_id'];
                $region_id = $geo_data['region_id'];
                $country = $geo_data['countryName'];
            }

            $tmodel = $this->getModel('Tax');
            $taxes = $tmodel->getTaxRates($country_id, $region_code);

            $jsess->set('taxes', $taxes, 'poecom');

            $tax_msg = JText::_('COM_POECOM_TAX_COUNTRY_MSG') . ': ' . $country . ', ' . JText::_('COM_POECOM_TAX_REGION_MSG') . ': ' . $region_code;

            $this->assignRef('tax_msg', $tax_msg);
            
        }
        $this->assignRef('user_bt', $user_bt);
        $this->assignRef('user_st', $user_st);

        $weight_uom = $params->get('weightuom');
        $length_uom = $params->get('lengthuom');

        $selected_options = array();
        
        $ship_mod = new JObject();
        $ship_mod->product_id = $product_id;
        $ship_mod->weight_modifier = 'N';
        $ship_mod->weight_modifier_value = 0;
        $ship_mod->weight_modifier_uom = $weight_uom;
        $ship_mod->length_modifier = 'N';
        $ship_mod->length_modifier_value = 0;
        $ship_mod->length_modifier_uom = $length_uom;
        $ship_mod->width_modifier = 'N';
        $ship_mod->width_modifier_value = 0;
        $ship_mod->width_modifier_uom = $length_uom;
        $ship_mod->height_modifier = 'N';
        $ship_mod->height_modifier_value = 0;
        $ship_mod->height_modifier_uom = $length_uom;

        if (strlen($serial_options)) {
            $tmp = explode("&", urldecode($serial_options));
            if ($tmp) {
                $omodel = $this->getModel('Options');

                foreach ($tmp as $t) {
                    $opt = explode("=", $t);

                    $option = $omodel->getOptionByDOMElement($product_id, $opt[0], $opt[1]);

                    switch ($option->option_type_id) {
                        case '1': // select
                            $option_display = array($option->name => $option->option_label);
                            break;
                        case '4': // inputtext
                        case '5': // property
                            $option_display = array($option->name => $opt[1]);
                            break;
                        case '2': // inputqty
                            $option_display = array($option->name => $opt[1] . " " . $option->uom);
                            break;
                        case '3': // inputsize
                            $option_display = array($option->name => $opt[1] . " " . $option->uom);
                        default:
                            $option_display = array($option->name => $opt[1]);
                            break;
                    }

                    $selected_options = array_merge($selected_options, $option_display);

                    //get shipping modifiers
                    $ship_mod  = $omodel->getShippingModifiers($opt[0], $opt[1], $ship_mod);
                }
            }
        }
        
        // Get cart data from seesion
        $cart = $jsess->get('cart', null, 'poecom');

        //Set empty array for discount
        if (!isset($cart['discount'])) {
            $cart['discount'] = array();
        }

        // Set the model
        $cart_model = $this->getModel('Cart');

        //override last page after login
        if ($p_info) {
            $cart['lastpage'] = 'product';
        }
        $update = false;
        // Add items when there are values from the product page
        // $cart will be empty when updating cart with one line item
        if ((isset($_POST['submit']) || $p_info) && $product_id > 0 && ( $cart['lastpage'] == 'product' || !$cart['idx'] > 0 )) {

            // Create cart_item for add
            $cart_item = new JObject;
            $cart_item->product_id = $product_id;
            $cart_item->product_sku = $product_sku;
            $cart_item->product_name = $product_name;
            $cart_item->type = $product_type;
            $cart_item->quantity = $quantity;
            //get product properties
            $productModel = $this->getModel('Product');
            $product_detail = $productModel->getProductDetail($product_id);
            if(!empty($product_detail->properties)){
                $cart_item->properties = $product_detail->properties;
            }else{
                $cart_item->properties = '';
            }
            
            $cart_item->serial_options = $serial_options;
            $cart_item->selected_options = $selected_options;
            $cart_item->ship_modifiers = $ship_mod;
            $cart_item->price = $price;

            if (isset($cart['idx']) && $cart['idx'] > 0) {
                $update = false;
                // Check if product in cart
                $idx = 0;
                foreach ($cart['items'] as $itm) {
                    if ($itm->product_id == $product_id && $itm->serial_options == $serial_options) {
                        // Check max order qty
                        if ($max_qty == 0 || ( $max_qty > 0 && $cart['items'][$idx]->quantity < $max_qty)) {
                            $cart['items'][$idx]->quantity = $quantity;
                            $msg = JText::_('COM_POECOM_CART_ITEM_UPDATED_MSG');
                            $app->enqueueMessage($msg, 'message');
                        } else {
                            $msg = JText::_('COM_POECOM_MAX_ORDER_QTY_MSG');
                            $app->enqueueMessage($msg, 'message');
                        }

                        // Update flag
                        $update = true;

                        continue;
                    }

                    $idx++;
                }

                if (!$update) {
                    $cart = $cart_model->addItem($cart, $cart_item, $taxes);
                }
            } else {
                $cart = $cart_model->addItem($cart, $cart_item, $taxes);
            }
        } else if ($delete_item_idx > -1) {
            // remove item 
            $cart = $cart_model->deleteItem($delete_item_idx);

            $app->enqueueMessage(JText::_('COM_POECOM_CART_ITEM_DELETE_MSG'), 'message');
        }
        
        //recalculate cart total when item updated
        //need here in case discounts, shipping rates are dependent on order total
        if($update){
            $cart = $cart_model->calculateTotal($cart, $taxes);
        }

        $cart['currency'] = $base_currency;
        $cart['lastpage'] = 'cart'; // stop update on browser reload
        if(!empty($geo_data['browser_ip'])){
            $cart['ip_address'] = $geo_data['browser_ip'];
        }else{
            $cart['ip_address'] = '';
        }

        if ($user_bt == ''){
            $cart['entrystatus'] = 'cart';
            //clear all bt/st dependent values 
            $shipping = '';
            $pay_methods = '';
            $return_policy = '';
            $terms_link = '';
            $privacy_link = '';
        } else {
            $cart['user_bt'] = $user_bt;
            $cart['user_st'] = $user_st;

            if (!isset($cart['items'])) {
                $cart['entrystatus'] = 'expired';
            } else {
                $cart['entrystatus'] = 'shipping';
            }

            $physical_products = false;
            $ship_methods = array();

            $saved_shipping = $jsess->get('shipping', null, 'poecom');

            if ($user_bt != '' &&( ( !isset($saved_shipping->ship_methods) && $cart['entrystatus'] == 'shipping') || isset($saved_shipping->address_update) && $saved_shipping->address_update == 1 || $update) ){
                // Create shipping object
                $ship_rates = new JObject();

                // check the cart for Physical products
                for ($i = 0; $i < $cart['idx']; $i++) {
                    if ($cart['items'][$i]->type == 1) {
                        $physical_products = true;
                        break;
                    }
                }

                if ($physical_products) {
                    // There is at least one Physical product in the cart
                    // Get the shipping methods
                    $smodel = $this->getModel('Shipping');

                    $ship_methods = $smodel->getShippingMethods();

                    if ($ship_methods) {
                        $ship_rates->skip = false;
                        $ship_rates->ship_methods = $ship_methods;

                        // Get rates for default
                        $idx = 0;
                        foreach ($ship_methods as $sm) {

                            $rates = $smodel->getRates($cart, $taxes, $sm->plugin);

                            $ship_methods[$idx]->rates = $rates;

                            if ($sm->sm_default == 1) {
                                if ($rates) {
                                    // first rate is the lowest
                                    $ship_rates->cost = $rates[0]->cost;
                                    $ship_rates->tax = $rates[0]->tax;
                                    $ship_rates->id = $rates[0]->id;
                                } else {
                                    $ship_rates->cost = 0;
                                    $ship_rates->tax = 0;
                                }
                            }

                            $idx++;
                        }
                    } else {
                        // No methods means no shipping charges
                        $ship_rates->skip = true;
                    }
                } else {

                    $ship_rates->skip = true;
                }

                $jsess->set('shipping', $ship_rates, 'poecom');
                $shipping = $jsess->get('shipping', null, 'poecom');
            } else {
                $shipping = $saved_shipping;
                if(isset($saved_shipping->ship_methods)){
                    $ship_methods = $saved_shipping->ship_methods;
                }else{
                    $ship_methods = '';
                }
                
            }

            if (isset($shipping->skip) && !$shipping->skip) {
                // Add shipping to totals
                $model = $this->getModel('Cart');
                $cart = $model->calculateTotal($cart, $taxes);
            }

            // Set coupon display
            $use_coupon = $params->get('usecoupon', 0);

            if ($use_coupon === '1') {
                //check for active coupons
                $model = $this->getModel('Coupon');

                if (!$model->activeCoupons($user->id)) {
                    $use_coupon = 0;
                }
            }
            $this->assignRef('use_coupon', $use_coupon);

            // Get the payment methods
            $model = $this->getModel('Payment');

            $pay_methods = $model->getPaymentMethods();

            $cc_years = array();

            //get current year
            $year = intval(date("Y"));

            for ($i = 0; $i < 10; $i++) {
                $cc_years[] = array('val' => $year + $i, 'text' => $year + $i);
            }

            $cc_months = array();

            $cc_months[] = array('val' => '01', 'text' => JText::_('COM_POECOM_JAN'));
            $cc_months[] = array('val' => '02', 'text' => JText::_('COM_POECOM_FEB'));
            $cc_months[] = array('val' => '03', 'text' => JText::_('COM_POECOM_MAR'));
            $cc_months[] = array('val' => '04', 'text' => JText::_('COM_POECOM_APR'));
            $cc_months[] = array('val' => '05', 'text' => JText::_('COM_POECOM_MAY'));
            $cc_months[] = array('val' => '06', 'text' => JText::_('COM_POECOM_JUN'));
            $cc_months[] = array('val' => '07', 'text' => JText::_('COM_POECOM_JUL'));
            $cc_months[] = array('val' => '08', 'text' => JText::_('COM_POECOM_AUG'));
            $cc_months[] = array('val' => '09', 'text' => JText::_('COM_POECOM_SEP'));
            $cc_months[] = array('val' => '10', 'text' => JText::_('COM_POECOM_OCT'));
            $cc_months[] = array('val' => '11', 'text' => JText::_('COM_POECOM_NOV'));
            $cc_months[] = array('val' => '12', 'text' => JText::_('COM_POECOM_DEC'));

            //get the return policy
            $return_policy = '';
           
            $return_policy_ids = $params->get('returnpolicy', 0);

            if (!empty($return_policy_ids)) {
                //Payment model
                $return_policy = $model->getReturnPolicy($return_policy_ids);
            }

            //get the terms of service link
            $terms_menu_ids = $params->get('termsofservice', 0);

            $terms_link = '';

            if (!empty($terms_menu_ids)) {
				if(count($terms_menu_ids) > 1){
					$menus = $app->getMenu();
					$doc = JFactory::getDocument();
					//find menu id for doc->language
					foreach($terms_menu_ids as $id){
						$menu = $menus->getItem($id);

						if($menu->language === "*"){
							$terms_menu_id = $id;
						}
						if(strtolower($menu->language) === $doc->language){
							$terms_menu_id = $id;
							break;
						}
					}
				}else{
					//use default
					$terms_menu_id = $terms_menu_ids[0];
				}
				$terms_link = 'index.php?Itemid='.$terms_menu_id;
            }

            //get the privacy link
            $privacy_menu_ids = $params->get('privacypolicy', 0);

            $privacy_link = '';
			
			if (!empty($privacy_menu_ids)) {
				if(count($privacy_menu_ids) > 1){
					$menus = $app->getMenu();
					$doc = JFactory::getDocument();
					//find menu id for doc->language
					foreach($privacy_menu_ids as $id){
						$menu = $menus->getItem($id);

						if($menu->language === "*"){
							$privacy_menu_id = $id;
						}
						if(strtolower($menu->language) === $doc->language){
							$privacy_menu_id = $id;
							break;
						}
					}
				}else{
					//use default
					$privacy_menu_id = $privacy_menu_ids[0];
				}
				$privacy_link = 'index.php?Itemid='.$privacy_menu_id;
            }
            
            $this->assignRef('cc_years', $cc_years);
            $this->assignRef('cc_months', $cc_months);
            
        }
        
        $this->assignRef('shipping', $shipping);
        $this->assignRef('pay_methods', $pay_methods);
       
        $this->assignRef('return_policy', $return_policy);
        $this->assignRef('terms_link', $terms_link);
        $this->assignRef('privacy_link', $privacy_link);

        $jsess->set('cart', $cart, 'poecom');

        //clear p_info
        $jsess->clear('p_info', 'poecom');

        // Assign data to the view
        $this->item = $cart;
         if(!isset($cart['items'])){
            //set default values
            $this->item['subtotal'] = 0;
            $this->item['product_tax'] = 0;
            $this->item['discount_amount'] = 0;
            $this->item['total'] = 0;
        }

        $script = $this->get('Script');
        $this->assignRef('script', $script);

        $this->prepareDocument();

        // Display the view
        parent::display($tpl);
    }

    /**
     * Prepares the document properties
     * */
    protected function prepareDocument() {
       //set noindex nofollow
        $this->document->setMetaData('robots',"noindex,nofollow");
        
        //set dialog strings
        JText::script('COM_POECOM_UPDATE_SHIPPING');
        JText::script('COM_POECOM_CREATE_RFQ');
        JText::script('COM_POECOM_PAY_TXN');
        JText::script('COM_POECOM_MAKE_PAYMT');
        JText::script('COM_POECOM_ORDER_SAVED');
        JText::script('COM_POECOM_TXN_DECLINED');
        JText::script('COM_POECOM_TXN_DECLINED_MSG');
        JText::script('COM_POECOM_TXN_BLOCKED');
        JText::script('COM_POECOM_TXN_BLOCKED_MSG');
        JText::script('COM_POECOM_PAYMT_RECD');
        JText::script('COM_POECOM_PAYMT_RECD_MSG');
        JText::script('COM_POECOM_SHOW_ORDER');
        JText::script('COM_POECOM_TOS');
        JText::script('COM_POECOM_TOS_MSG');
        JText::script('COM_POECOM_RFQ_RECD');
        JText::script('COM_POECOM_RFQ_RECD_MSG');
        JText::script('COM_POECOM_VALIDATE_COUPON');
        JText::script('COM_POECOM_COUPON_APPLIED');
        JText::script('COM_POECOM_COUPON_NOT_APPLIED');
        JText::script('COM_POECOM_COUPON_INVALID');
        JText::script('COM_POECOM_COUPON_INVALID_MSG1');
        JText::script('COM_POECOM_COUPON_INVALID_MSG2');
        JText::script('COM_POECOM_PLEASE_WAIT');
    }
}

