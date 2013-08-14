<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Cart Model
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 9:49:31 PM
 *
 * http://www.exps.ca
**/
jimport('joomla.application.component.modelitem');
 
/**
 * Cart Model that handles cart display and update functions
 */
class PoecomModelCart extends JModel{
    
	protected function populateState(){
		$app = JFactory::getApplication();
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}
 
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Cart', $prefix = 'PoecomTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
    
    
    /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'components/com_poecom/models/forms/cart.js';
	}
    
    /**
     * Delete item from cart
     * 
     * @param int $index $cart['items'] index position to delete
     * 
     * @return array $cart Updated cart
     * 
     */
    public function deleteItem($index ){
        $jsess = JFactory::getSession();
        $cart = $jsess->get('cart', null, 'poecom');
        
        // clear the shipping
        $jsess->clear('shipping', 'poecom');
            
        // remove item 
        if($cart && $index > -1){
            unset($cart['items'][$index]);
            
            if(isset($cart['idx'])){
                $cart['idx']--;
            }else{
                $cart['idx'] = 0;
            }
            
            
            if($cart['idx'] <= 0){
                // clear the cart
                $jsess->clear('cart','poecom');
                
                $cart = $jsess->get('cart', null, 'poecom');
            }else{
                //reindex array
                $items = array();
                foreach($cart['items'] as $itm){
                    $items[] = $itm;
                }
                
                if($items){
                    $cart['items'] = $items;
                }
                
                
                // update cart idx
                $jsess->set('cart', $cart, 'poecom');
                
                $cart = $this->calculateTotal($cart, null, 'delete');
            }
        }
        
        return $cart;
    }
    
    /**
     * Add item to the cart
     * 
     * @param array $cart Array of cart items pulled from session namespace poecom
     * @param object $cart_item Object with item properties
     * @param array $taxes Taxes to apply to items in cart
     * 
     * Updates session 
     */
    public function addItem($cart = array(), $cart_item = null, $taxes = array()){
        
        if($cart_item){
            $jsess = JFactory::getSession();
            // clear the shipping
            $jsess->clear('shipping', 'poecom');
            
            if(isset($cart['idx'])){
                $cart['idx']++;
                $cart['items'][] = $cart_item;
            }else{
                $cart['idx'] = 1;
                $cart['items'] = array();
                $cart['items'][0] = $cart_item;
            }

            $cart = $this->calculateTotal($cart, $taxes);
             
            return $cart;
        }else{
            return false;
        }
    }
    
    
    /**
     * Get the tax for a product (order line)
     * 
     * @param float $line_total Total for the order line
     * @param int $product_id 
     * @param array $taxes Array of tax rates to apply to order line
     * 
     * @return float $product_tax Total Order Line tax amount
     */
    public function getProductTax($line_total = 0, $product_id = 0, $taxes = array()){
        $product_tax = 0;
        
        if($taxes && $line_total > 0 && $product_id > 0){
            // load tax model
            $model = JModel::getInstance('tax', 'PoecomModel');
      
            $product_tax_rate = $model->getProductTaxRate($product_id, $taxes);
           
            $product_tax = floatval($line_total * $product_tax_rate);
        }
       
        return $product_tax;
    }
    
    /**
     * Set the discount line total for an item in the cart
     * 
     * @param object $item Cart line item
     * @param array $cart 
     * @param object $discount Holds discount parameters
     * 
     * @return float $line_total Disocunted line total
     */
    public function setLineTotalDiscount($item,$cart, $discount){
	$line_total = $item->total;
	
	//product discount type for product id
	if($discount->type == 2){
	    //product discount
	    if(in_array($item->product_id, $discount->product_list)){
		//TODO:
		/**
		 * How to apply disocunt?
		 * Fixed means fixed amount by product which might be limited by quantity
		 * Percentage means set percentage by product which might be limited by quantity
		 */
	    }
	}else if($discount->type == 1){
	    //order discount
	    if($discount->method == 1){
		//Fixed
		$item_count = 1;
		//get item count
		for($i=0;$i<$cart['idx'];$i++){
		    $item_count += $cart['items'][i]->quantity;
		}
		
		//apply discount equally over all items
		$calc_discount = floatval($discount->amount/$item_count);
		
		//make sure discount not more than price
		$item_discount = $calc_discount > $item->price ? $item->price : $calc_discount;
		
		$line_total = ($item->price - $item_discount) * $item->quantity;
		
	    }else if($discount->method == 2){
		//Percentage
		$line_total = floatval(($item->price - ($item->price * 1/$discount->amount)) * $item->quantity);
	    }
	}
	
	return $line_total;
    }
    
    /**
     * Calculate order totals and assign to sesssion
     * 
     * @array $cart Array of order lines and totals
     * 
     * @return array $cart Updated array
     */
    public function calculateTotal($cart, $taxes = array(), $mode = ''){
        $line_subtotal = 0;
	$line_discount = 0;
        $total = 0;
        $line_product_tax = 0;
	
        for($i=0;$i<$cart['idx'];$i++){
            // loop through the cart and total item product price,
            // product tax, subtotal and total
            // only for updates and add
            if($mode != 'delete'){
		//undiscounted item total
		$cart['items'][$i]->total = floatval($cart['items'][$i]->price) * intval($cart['items'][$i]->quantity);
		
		//set discount total
		if(isset($cart['discount']->enabled) && $cart['discount']->enabled){
		    $cart['items'][$i]->discounted_total = $this->setLineTotalDiscount($cart['items'][$i],$cart, $cart['discount']); 
		    $cart['items'][$i]->discount_amount = $cart['items'][$i]->total - $cart['items'][$i]->discounted_total;
		}else{
		    $cart['items'][$i]->discounted_total = $cart['items'][$i]->total;
		}
		
		$cart['items'][$i]->tax = floatval($this->getProductTax($cart['items'][$i]->discounted_total, $cart['items'][$i]->product_id, $taxes));
            
	    }
            if(isset($cart['items'][$i]->discount_amount)){
                $line_discount += $cart['items'][$i]->discount_amount;
            }
            if(isset($cart['items'][$i]->total)){
                $line_subtotal += $cart['items'][$i]->total;
            }
            if(isset($cart['items'][$i]->tax)){
                $line_product_tax += $cart['items'][$i]->tax;
            }
        }
	
	//adjust to two place precision
	$subtotal = round($line_subtotal, 2);
	
	$product_tax = round($line_product_tax, 2);
	$discount = round($line_discount, 2);
	
	if($discount > 0 && $cart['discount']->status_id != 2){
	    //update discount status
	    $cart['discount']->status_id = 2;
	    
	    //update coupon status
	    $model = JModel::getInstance('Coupon', 'PoecomModel');
	    if(($coupon = $model->getCoupon($cart['discount']->id) )){
		$coupon->status_id = 2;
		
		$data = JArrayHelper::fromObject($coupon);
		
		$model->save($data);
	    }
	}
	
        
        $jsess = JFactory::getSession();
        $shipping = $jsess->get('shipping', null, 'poecom');
        
        if($shipping && !$shipping->skip){
            $shipping_cost = floatval($shipping->cost);
            $shipping_tax = floatval($shipping->tax);
            $total = $subtotal + $product_tax + round($shipping_cost,2)  + round($shipping_tax,2) - $discount ;
            
            // Update carrier info
            // Find the selected rate
            $selected_shipping = new JObject();
            
            if($shipping->ship_methods){
                foreach($shipping->ship_methods as $sm){
                    if($sm->rates){
                        foreach($sm->rates as $r){
                            if($r->id == $shipping->id){
                                //set carrier info
                                $selected_shipping->carrier = $sm->name;
                                $selected_shipping->carrier_logo = $sm->logo;
                                $selected_shipping->service = $r->service;
                                $selected_shipping->eta = $r->eta;
                                $selected_shipping->plugin = $sm->plugin;

                                break;
                            }
                        }
                    }
                }
            }else{
                $selected_shipping->carrier = 'none';
            }
            
            $cart['selected_shipping'] = $selected_shipping;
            
        }else{
            $shipping_cost = $shipping_tax = 0;
            $total = $subtotal + $product_tax - $discount;
        }
        
        $cart['shipping_cost'] = $shipping_cost;
        $cart['shipping_tax'] = $shipping_tax;
        $cart['subtotal'] = $subtotal;
	$cart['discount_amount'] = $discount;
        $cart['product_tax'] = $product_tax;
        $cart['tax_total'] = $shipping_tax + $product_tax;
        $cart['total'] = $total;
        
        // enable confirmation email
        //$jsess->set('send_ack', '1', 'poecom');
        
        return $cart;
    }
}
