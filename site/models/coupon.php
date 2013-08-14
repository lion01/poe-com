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
jimport('joomla.application.component.model');
 
/**
 * Coupon Model
 */
class PoecomModelCoupon extends JModel{
    
    /**
     * Get coupon by ID
     * 
     * @param int $id Coupon ID
     * @return object 
     */
    public function getCoupon($id){
	$db = $this->getDBO();
	$q = $db->getQuery(true);
	$q->select('*');
	$q->from('#__poe_coupon');
	$q->where('id='.(int)$id);
	
	$db->setQuery($q);
	
	$coupon = $db->loadObject();
	
	return $coupon;
    }
    
    /**
     * Get coupon and related promotion for a coupon code
     * 
     * @param string $coupon_code
     * @return object/false Coupon object 
     */
    public function getCouponByCode($coupon_code){
	$db = $this->getDBO();
	$q = $db->getQuery(true);
	$q->select('c.*, c.id coupon_id,p.*');
	$q->from('#__poe_coupon c');
	$q->innerJoin('#__poe_promotion p ON p.id=c.promotion_id');
	$q->where('c.coupon_code='.$db->Quote($coupon_code));
	
	$db->setQuery($q);
	
	$coupon = $db->loadObject();
	
	return $coupon;
    }
    
    /**
     * Validate a coupon based on promotion paramters and cart totals
     * 
     * @param object $coupon Coupon/Promotion object
     * @param object $cart
     * @return \JObject Containing approved code and optional error message 
     */
    public function validate($coupon, $cart){
	$valid = new JObject();
	$valid->approved = false;
	
	//Test coupon status
	if($coupon->status_id == 1){
	    $user_id = JFactory::getUser();
	    //Start testing based on promotion type
	    if($coupon->promotion_type_id == 1 && $user_id != $coupon->user_id){
		//Customer Direct, check user id  
		$valid->reject_msg = JText::_('COM_POECOM_COUPON_USER_MATCH_ERROR');
	    }else{
		//Check discount type
		if($coupon->discount_type_id == 1 ){
		    //Order discount
		    if($coupon->order_amount_min > 0 && $cart['subtotal'] < $coupon->order_amount_min){
			 //min order amount
			$valid->reject_msg = JText::_('COM_POECOM_COUPON_ORDER_MIN_ERROR'). $coupon->order_amount_min;
		    }
		}else{
		    //product discount
		    $product_list = json_decode($coupon->products);
		    $product_found = false;
		    
		    if($product_list){
			//check that product is in the cart
			foreach($cart->items as $itm){
			    if(in_array($itm->product_id, $product_list)){
				//product found, check min qty
				//TODO:
				$product_found = true;
				break;
			    }
			}
		    }else{
			$valid->reject_msg = JText::_('COM_POECOM_COUPON_NO_PRODUCTS_ERROR');
		    }
		}
		
		if(!strlen($valid->reject_msg)){
		    //check date range
		    $now = localtime();
		    $now_time = mktime($now[2],$now[1],$now[2],$now[4]+1,$now[3],$now[5]+1900);
		    
		    $start_time = $this->convertDateTime($coupon->start_time);
		    $end_time = $this->convertDateTime($coupon->end_time);
		    
		    if($now_time >= $start_time && $now_time <= $end_time){
			//Within promotion range
			$valid->approved = true;
		    }else{
			$valid->reject_msg = JText::_('COM_POECOM_COUPON_NOT_IN_TIME_RANGE_ERROR');
		    }
		}
		
	    }
	    
	}else{
	    //coupon is either used or expired
	    if($coupon->status_id == 2){
		$valid->reject_msg = JText::_('COM_POECOM_COUPON_USED_ERROR');
	    }else{
		$valid->reject_msg = JText::_('COM_POECOM_COUPON_EXPIRED_ERROR');
	    }
	}

	return $valid;
    }
    
    /**
     * Convert MySQL datetime to UNIX timestamp
     * 
     * @param string $datetime MySQL DateTime
     * @return string Unix Timestamp 
     */
    function convertDateTime($datetime) {
	list($date, $time) = explode(' ', $datetime);
	list($year, $month, $day) = explode('-', $date);
	list($hours, $minutes, $seconds) = explode(':', $time);

	$UnixTimestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
	return $UnixTimestamp;
    }
    
    /**
     * Set discount paramters and assign to $cart session variable
     * @param type $coupon
     * @param array $cart 
     */
    function applyCoupon($coupon, $cart){
	$jsess = JFactory::getSession();
	
	$discount = new JObject();
	$discount->enabled = true;
	$discount->coupon_id = $coupon->coupon_id;
	$discount->status_id = $coupon->status_id;
	$discount->coupon_code = $coupon->coupon_code;
	$discount->type = $coupon->discount_type_id;
	$discount->method = $coupon->discount_amount_type_id;
	$discount->amount = $coupon->discount_amount;
	$discount->max_amount = $coupon->max_value;
	
	if($coupon->discount_type_id == 2){
	    //TODO: product discounting
	    $product_list = json_decode($coupon->products);
	    $discount->products = $product_list;
	}
	
	$cart['discount'] = $discount;
	
	//get taxes
	$taxes = $jsess->get('taxes', array(), 'poecom');
	
	$model = JModel::getInstance('Cart', 'PoecomModel');
	$cart = $model->calculateTotal($cart, $taxes);
	
	//update cart
	$jsess->set('cart',$cart,'poecom');
	
	return $cart;
    }
    
    /**
    * Update existing coupon
    *
    * @param   array  $data  Coupon data
    *
    * @return  boolean  True on success, False on error.
    */
    public function save($data){
	// Initialise variables;
	$table = $this->getTable();
	$key = $table->getKeyName();
	$pk = $data[$key];

	// Allow an exception to be thrown.
	try{
	    // Load the row if saving an existing record.
	    if ($pk > 0){
		$table->load($pk);
	    }else{
		//new record not allowed here
		return false;
	    }

	    // Bind the data.
	    if (!$table->bind($data)){
		$this->setError($table->getError());
		return false;
	    }

	    // Prepare the row for saving
	    $this->prepareTable($table);

	    // Check the data.
	    if (!$table->check()){
		$this->setError($table->getError());
		return false;
	    }

	    // Store the data.
	    if (!$table->store()){
		$this->setError($table->getError());
		return false;
	    }

	    // Clean the cache.
	    $this->cleanCache();

	}catch (Exception $e){
	    $this->setError($e->getMessage());

	    return false;
	}

	return true;
    }
    
    /**
     * Check for active coupons
     * 
     * First checks for user specific coupons and then for general or numbered coupons
     * 
     * @param int $user_id
     * @return boolean False no active coupons
     */
    public function activeCoupons($user_id){
        $db = JFactory::getDbo();
        
        //Active Customer Direct
        $q = $db->getQuery(true);
        $q->select('COUNT(id');
        $q->from('#__poe_coupon');
        $q->where('user_id=' . (int) $user_id);
        $q->where('status_id=1 || status_id=2');
        
        $db->setQuery($q);

        if( (!$result = $db->loadResult() )){
            //General or Numbered
            $q = $db->getQuery(true);
            $q->select('COUNT(id');
            $q->from('#__poe_coupon');
            $q->where('user_id=0');
            $q->where('status_id=1 || status_id=2');

            $db->setQuery($q);
            
            $result = $db->loadResult();
            
            if($result > 0){
                return true;
            }
        }
        
        return false;
    }
}
