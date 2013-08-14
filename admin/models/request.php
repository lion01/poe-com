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
jimport('joomla.application.component.modeladmin');
 
/**
 * RFQ Model
 */
class PoecomModelRequest extends JModelAdmin{
    /**
    * Method override to check if you can edit an existing record.
    *
    * @param	array	$data	An array of input data.
    * @param	string	$key	The name of the key for the primary key.
    *
    * @return	boolean
    * @since	1.6
    */
    protected function allowEdit($data = array(), $key = 'id'){
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_poecom.name.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
    public function getTable($type = 'Request', $prefix = 'PoecomTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
    * Method to get the record form.
    *
    * @param	array	$data		Data for the form.
    * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
    * @return	mixed	A JForm object on success, false on failure
    * @since	1.6
    */
    public function getForm($data = array(), $loadData = true){
        // Get the form.
        $form = $this->loadForm('com_poecom.request', 'request', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)){
            return false;
        }
        return $form;
    }
    
    /**
    * Method to get the script that have to be included on the form
    *
    * @return string	Script files
    */
    public function getScript(){
        return JURI::root(true).'/administrator/components/com_poecom/models/forms/request.js';
    }
    
	
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	1.6
    */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.request.edit.data', array());
        if (empty($data)){
            $data = $this->getItem();
        }
        return $data;
    }
    
    /**
    * Method to get a single record.
    *
    * @param   integer  $pk  The id of the primary key.
    *
    * @return  mixed    Object on success, false on failure.
    * @since   11.1
    */
    public function getItem($pk = null){
        // Initialise variables.
        $pk		= (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
        $table	= $this->getTable();

        if ($pk > 0) {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError()) {
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = JArrayHelper::toObject($properties, 'JObject');
        
        if(!empty($item->billing_id)){
            //get billing address
            $addrModel = JModel::getInstance('Address', 'PoecomModel');
            $item->user_bt = $addrModel->getItem($item->billing_id);
        }
        
         if(!empty($item->shipping_id)){
            //get billing address
            $addrModel = JModel::getInstance('Address', 'PoecomModel');
            $item->user_st = $addrModel->getItem($item->shipping_id);
        }
        
        if(!empty($item->currency_id)){
            //get currency
            $currencyModel = JModel::getInstance('Currency', 'PoecomModel');
            $item->currency = $currencyModel->getItem($item->currency_id);
        }

        //Add cart content as object
        //TODO: remove this and add assignments for order data
        if(!empty($item->cart)){
            $cart = json_decode($item->cart);
            
            if(!empty($cart)){
                $item->rfq_cart = $cart;
		
		if(!empty($cart->discount)){
		    $model = JModel::getInstance('Coupon', 'PoecomModel');
		    if(($promotion = $model->getCouponById($cart->discount->coupon_id))){
			$item->promotion = $promotion;
		    }
		}
            }
        }
        
        return $item;
    }
    
    /**
     * Get RFQ
     * 
     * @param string $rfq_number
     * 
     * @reutn object $rfq Request For Quote data
     */
    public function getRFQ($rfq_number = ''){
        $q = $this->_db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_request');
        $q->where('number='.$this->_db->Quote($rfq_number) );

        $this->_db->setQuery($q);
            
        $rfq = $this->_db->loadobject();
        
        if(strlen($rfq->cart)){
            //convert data blob to cart object
            $rfq->cart = json_decode($rfq->cart); 
        }
        
        return $rfq;
    }
    
    /**
     * Create Order from RFQ
     * 
     * 
     * @param string $id RFQ ID
     * 
     * @return mixed boolean false or order_id  
    */
    public function createOrderFromRFQ($id){
        if($id > 0){
            
            $rfq = $this->getItem($id);
            
            if($rfq){
                //set the models
                $txn_model = JModel::getInstance('PayTransaction', 'PoecomModel');
                $order_model = JModel::getInstance('Order', 'PoecomModel');
                $line_model = JModel::getInstance('OrderLine', 'PoecomModel');
                
                //set order date
                $today = new JDate();
                
                //set ship to
                if($rfq->rfq_cart->user_st){
                    $shipping_id = $rfq->rfq_cart->user_st->id;
                }else{
                    $shipping_id = '';
                }
                
                //set shipping carrier detail
                if($rfq->rfq_cart->selected_shipping){
                    $selected_shipping = json_encode($rfq->rfq_cart->selected_shipping);
                }else{
                    $selected_shipping = '';
                }
                
                //get payment id and status
                if(!empty($rfq->payment)){
                    $payment_id = $rfq->payment_id;
                    if(($pay_txn = $txn_model->getItem($payment_id))){
                        $payment_status_id = $pay_txn->status_id;
                    }else{
                        $payment_status_id = 0;
                    }
                }else if(($pay_txn = $txn_model->getRFQTransaction($id))){
                    //check for unlinked payment transaction
                    $payment_id = $pay_txn->id;
                    $payment_status_id = $pay_txn->status_id; 
                }else{
                    $payment_id = 0;
                    $payment_status_id = 0;
                    $pay_txn = null;
                }
                
                //set order status
                switch($payment_status_id){
                    case '1': //pending
                        $order_status_id = 2; //invoiced
                        break;
                    case '2': //complete
                        $order_status_id = 3; //paided
                        break;
                    default:
                        //any other payment status 
                        $order_status_id = 1; //open
                        break;
                }
                
                //check for existing order
                if(($order_id = $order_model->getOrderIdByRFQ($rfq->number))){
                    JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_ORDER_EXISTS_ERROR'). $order_id);
                    return false;
                }
		             
                //build order header data array
                $data = array(
                    'id' => '',
                    'status_id' => $order_status_id,
                    'rfq_id' => $rfq->id,
                    'payment_id' => $payment_id,
                    'order_date' => $today->toMySQL(true),
                    'juser_id' => $rfq->juser_id,
                    'billing_id' => $rfq->rfq_cart->user_bt->id,
                    'shipping_id' => $shipping_id,
                    'selected_shipping' => $selected_shipping ,
                    'subtotal' => $rfq->rfq_cart->subtotal,
                    'product_tax' => $rfq->rfq_cart->product_tax,
                    'shipping_cost' => $rfq->rfq_cart->shipping_cost,
                    'shipping_tax' => $rfq->rfq_cart->shipping_tax,
                    'total' => $rfq->rfq_cart->total,
                    'ip_address' => $rfq->rfq_cart->ip_address
                );
              
		//if(1 == 2){
                if($order_model->save($data)){
                   
                    if(!$order_id = $order_model->getOrderIdByRFQ($rfq->number)){
                        JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_ORDER_NOT_FOUND_ERROR'). $order_id);
                        return false;
                    }
                    
                    //update request
                    $rfq->order_id = $order_id;
                    $rfq->status_id = 2;
                    
                    //convert to array for save()
                    $rfq_array = JArrayHelper::fromObject($rfq);
                    
                    if(!$this->save($rfq_array)){
                        JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_ORDER_UPDATE_ERROR'). $rfq->id. '<br/>'. $this->getError());
                        return false;
                    }
                    
                    //update payment transaction
                    if($pay_txn){
                        $pay_txn->order_id = $order_id;
                        
                        //convert to array for save()
                        $txn_array = JArrayHelper::fromObject($pay_txn);

                        if(!$txn_model->save($txn_array)){
                            JError::raiseWarning(500, JText::_('COM_POECOM_PAY_TXN_ORDER_UPDATE_ERROR'). $rfq->payment_id. '<br/>'. $this->getError());
                            return false;
                        }
                    }
                    
                    if($order_id > 0 && $rfq->rfq_cart->items){
                        
                        foreach($rfq->rfq_cart->items as $itm){
                            if($itm->selected_options){
                                $selected_options =  json_encode($itm->selected_options);
                            }else{
                                $selected_options = '';
                            }
                            
                            $data = array(
                                'id' => '',
                                'order_id' => $order_id,
                                'product_id' => $itm->product_id,
                                'selected_options' => $selected_options,
                                'quantity' => $itm->quantity,
                                'price' => $itm->price,
                                'product_tax' => $itm->tax,
                                'total' => $itm->total,
                                'juser_id' => $rfq->juser_id //access control
                            );
                            
                            if(!$line_model->save($data)){
                                JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_ORDER_LINES_INSERT_ERROR'). $order_id. '<br/>'. $this->getError());
                                return false;
                            }
                        } 
                    }else{
                        JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_ORDER_NO_LINES_ERROR'). $order_id. '<br/>'. $this->getError());
                        return false;
                    }
                    return true;
                }else{
                    JError::raiseWarning(500, JText::_('COM_POECOM_ORDER_INSERT_ERROR'). $id. '<br/>'. $this->getError());
                    return false;
                }
            }else{
                JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_NOT_FOUND_ERROR'). $id);
                return false;
            }
        }else{
            JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_NOT_FOUND_INVALID_ID_ERROR'). $id);
            return false;
        }
        
        
    }
    
    /**
    * Over ride Save method that does not used content plugin
    *
    * @param   array  $data  The form data.
    *
    * @return  boolean  True on success, False on error.
    *
    * @since   11.1
    */
    public function save($data){
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $bt = $jinput->get('bt', null, 'ARRAY');
        $st = $jinput->get('st', null, 'ARRAY');
        
        // Initialise variables
        $table = $this->getTable();
        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;


        // Allow an exception to be thrown.
        try{
            // Load the row if saving an existing record.
            if ($pk > 0){
                $table->load($pk);
                $isNew = false;
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

        $pkName = $table->getKeyName();

        if (isset($table->$pkName)){
            $this->setState($this->getName() . '.id', $table->$pkName);
        }
        $this->setState($this->getName() . '.new', $isNew);
        
        $addrModel = JModel::getInstance('Address', 'PoecomModel');
        
        //update BT address - expected to always be set
        if(!empty($data['billing_id']) && !empty($bt)){
            $bt['id'] = $data['billing_id'];
            $bt['juser_id'] = $data['juser_id'];
            $bt['address_type'] = 'BT';
            
            if(!$addrModel->save($bt)){
                $app->enqueueMessage(JText::_('COM_POECOM_ADDRESS_SAVE_ERROR'). ' ' .$addrModel->getError() , 'error') ;
            }
        }
        
        //update ST address - may not exist
        if(!empty($data['shipping_id'])){
            //update existing
            $st['id'] = $data['shipping_id'];
        }else if(empty($data['shipping_id']) && !empty($st) && $data['stbt_same'] == '0'){
            //check for existing
            $id = $addrModel->getAddress('ST',$data['juser_id']);
            
            if(!empty($id)){
                //update
                $st['id'] = $id;
            }else{
                //insert new
                $st['id'] = '';
            }
        }
        
        if(!empty($st)){
            $st['juser_id'] = $data['juser_id'];
            $st['address_type'] = 'ST';
            if(!$addrModel->save($st)){
                $app->enqueueMessage(JText::_('COM_POECOM_ADDRESS_SAVE_ERROR'). ' ' .$addrModel->getError() , 'error') ;
            }else if(empty($data['shipping_id'])){
                //update the request shipping_id of last insert
                $data['shipping_id'] = $addrModel->_db->insertid();
                $this->updateShippingId($data['shipping_id']);
            }
        }

        return true;
    }
    /*
     * Update RFQ shipping id
     * 
     * @param int $shipping_id Shipping address id
     * @param int $rfq_id 
     * 
     * @return boolean
     */
    public function updateShippingId($id){
        $db = Jfactory::getDbo();
        $q = $db->getQuery(true);
        $q->update('#__poe_request');
        $q->set('shipping_id='.(int)$id);
        $q->where('id='.(int)$rfq_id);
        
        $db->setQuery($q);
        
        if($db->query()){
            return true;
        }else{
            return false;
        }
    }
    
    
    /**
     * Set request order id to 0 
     * 
     * Used when deleting an order
     * 
     * @param int $id Request Id 
     */
    public function resetOrderId($id = 0){
        //get the rfq
        $rfq = $this->getItem((int)$id);
        
        if($rfq){
            $rfq->order_id = 0; // no order
            $rfq->status_id = 1; // open
            $rfq = JArrayHelper::fromObject($rfq);
            if(!$this->save($rfq)){
                JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_ORDER_RESET_ERROR'). $id. '<br/>'. $this->getError());
                return false;
            }
        }else{
            JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_NOT_FOUND_ERROR') . $id);
            return false;
        }
        return true;
    }
    
    /**
     * Get RFQ Status 
     * 
     * @param int $id RFQ id
     * @return int $status_id 
     */
    public function getStatus($id){
        $db = $this->getDBO();
        $q = $db->getQuery(true);
        $q->select('status_id');
        $q->from('#__poe_request');
        $q->where('id='.(int)$id);

        $db->setQuery($q);
        
        $status_id = $db->loadResult();
        
        return $status_id;
    }
    
    
    
    /**
     * Delete RFQ
     * 
     * Checks dependencies before delete
     * 
     * @param array $cids List of RFQ ids to delete
     */
    public function delete(&$cids){
        if($cids){
            $error = false;
            
            //get table
            $table = $this->getTable();
            
            //get models
            $pay_model = JModelAdmin::getInstance('PayTransaction', 'PoecomModel');
            $order_model = JModelAdmin::getInstance('Order', 'PoecomModel');
            
            foreach($cids as $cid){
                //check status
                $status_id = $this->getStatus($cid);
                
                //status 2 is ordered
                if($status_id == 2){
                    JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_LINKED_TO_ORDER_ERROR'));
                    $error = true; 
                }else{
                    //check for payment transaction
                    if(($pay_trans = $pay_model->checkRFQ($cid))){
                        JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_LINKED_TO_PAYMENT_ERROR'));
                        $error = true;
                    }else{
                        //check for order
                        if($order_model->checkRFQ($cid)){
                            JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_LINKED_TO_ORDER_ERROR'));
                            $error = true;
                        }
                    }
                }
            }
            
            if(!$error){
                foreach($cids as $cid){
                    if (!$table->delete($cid)){
                        $this->setError($table->getError());
                        return false;
                    }
                } 
            }else{
                return false;
            }
            
            return true;
        }else{
            JError::raiseWarning(500, JText::_('COM_POECOM_NO_ITEM_SELECTED'));
            return false;
        }
    }
    /**
     * Get list of countries
     * 
     * @return array $countries Array of objects
     */
    public function getCountries(){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id value, name text');
        $q->from('#__geodata_country');
        $q->where('enabled=1');
        $q->order('name');
        $db->setQuery($q);
        
        $countries = $db->loadObjectList();
        
        return $countries;
        
    }
    /*
     * @param int $country_id
     * 
     * @return array $regions Array of objects
     */
    public function getRegions($country_id = 0){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id value, name text');
        $q->from('#__geodata_region');
        $q->where('country_id='.(int)$country_id);
        $q->order('name');
        $db->setQuery($q);
        
        $regions = $db->loadObjectList();
        
        return $regions;
    }
    
    /*
     * Check if coupon used in orders
     * 
     * @param int $coupon_id
     * 
     * @return boolean True means not found, delete coupon ok
     */
    public function couponDeleteValid($coupon_id = 0){
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_request');
        $q->where('coupon_id='.(int)$coupon_id);
        
        $db->setQuery($q, '', 1);
        
        if(($result = $db->loadResult())){
            return false;
        }else{
            return true;
        }
    }
    
    /*
     * Delete Validation for a field
     * 
     * Check if field valule found in table
     * 
     * @param string $field Table field name
     * @param mixed $value Field value to check
     * 
     * @return boolean True means not found delete ok
     */
    public function deleteValidation($field = '', $value = ''){
        if(!empty($field) && !empty($value)){
            $db = $this->getDbo();
            $q = $db->getQuery(true);
            $q->select('id');
            $q->from('#__poe_request');
            $q->where($db->nameQuote($field).'=' . $db->Quote($value));
            $db->setQuery($q, 0, 1);

            if(($id = $db->loadResult())){
                $this->setError($id);
                return false;
            }
        }else{
            $this->setError('Field or value missing');
            return false;
        }
        
        return true;
    }
}
