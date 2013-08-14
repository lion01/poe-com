<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Order Model
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 4:20:21 PM
 *
 * http://www.exps.ca
**/
jimport('joomla.application.component.modelform');
 
/**
 * Order Model
 */
class PoecomModelOrder extends JModelForm{
   
    /**
    * Method for getting the model form
    * 
    *
    * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
    * @return	mixed	A JForm object on success, false on failure
    * @since	11.1
    */
    public function getForm($data = array(),$loadData = true){
        

        $form = $this->loadForm('com_poecom.order', $address_XML, array('control' => 'jform', 'load_data' => $loadData));    

        if (empty($form)){
            return false;
        }
        return $form;
    }
    
    
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	11.1
    */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.user.edit.data', array());
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
    *
    * @since   11.1
    */
    public function getItem($pk = null){

        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

        $table = $this->getTable('order','PoecomTable');

        if ($pk > 0){
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError()){
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = JArrayHelper::toObject($properties, 'JObject');
        
        if($item){
            //get order status display name
            $item->order_status_name = $this->getOrderStatusName($item->status_id);
        }

        return $item;
    }
    
    /**
    * Method to get the script that have to be included on the form
    *
    * @return string	Script files
    */
    public function getScript(){
        return JURI::root(true).'/components/com_poecom/models/forms/order.js';
    }
    
    /**
    * Prepare and sanitise the table data prior to saving.
    *
    * @param   JTable  &$table  A reference to a JTable object.
    *
    * @return  void
    *
    * @since   11.1
    */
    protected function prepareTable(&$table){
        // Derived class will provide its own implementation if required.
    }
    
    /**
    * Method to test whether a record can be saved.
    * Only the user can save their address
    *
    * @param   object  $record  A record object.
    *
    * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
    *
    * @since   2.0
    */
    protected function canSave($user_id){
        $user = JFactory::getUser();

        if($user->id == $user_id){
            return true;
        }else{
            return false;
        }
    }    
    
    
    /**
    * Method to save the form data.
    *
    * @param   array  $data  The form data.
    *
    * @return  boolean  True on success, False on error.
    *
    * @since   11.1
    */
    public function save($data){
	   
        if(!$this->canSave($data['juser_id'])){
            $this->setError(JText::_('COM_POECOM_USER_MISMATCH_SAVE_ERROR'));
            return false;
        }

        // Initialise variables;
        $table = $this->getTable('order', 'PoecomTable');

        $key = $table->getKeyName();

        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');

        $isNew = true;

        // Allow an exception to be thrown.
        try{
            // Load the row if saving an existing record.
            if ($pk > 0){
                    $table->load($pk);
                    $isNew = false;
            }else{
                $user = JFactory::getUser();

                if($user->id > 1){
                    // no guest orders
                    $data['juser_id'] = $user->id;
                }else{
                    $this->error(JText::_('COM_POECOM_NOT_LOGGED_IN_USER_FOR_ADDR'));
                    return false;
                }
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

        return true;
    }
    
    /**
    * Method to test whether a record can be deleted.
    * Only the user can delete their address
    *
    * @param   object  $record  A record object.
    *
    * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
    *
    * @since   11.1
    */
    protected function canDelete($record){
        $user = JFactory::getUser();

        if($user->id == $record->juser_id){
            return true;
        }else{
            return false;
        }
    }
    
    
    /**
    * Method to delete one or more records.
    *
    * @param   array  &$pks  An array of record primary keys.
    *
    * @return  boolean  True if successful, false if an error occurs.
    *
    * @since   11.1
    */
    public function delete($pk){
        // Initialise variables.
        $table = $this->getTable('order', 'PoecomTable');


        if ($table->load($pk)){

            if ($this->canDelete($table)){
                if (!$table->delete($pk)){
                        $this->setError($table->getError());
                        return false;
                }
            }else{
                $error = $this->getError();
                if ($error){
                        JError::raiseWarning(500, $error);
                        return false;
                }else{
                        JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
                        return false;
                }
            }
        }else{
            $this->setError($table->getError());
            return false;
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }
    
    /**
     * Get order ID for an RFQ number
     * 
     * RQF Number is a string value Like RFQ_TIMESTAMP_USERID
     * Here we are after rfq.id the row id
     * 
     * @param string $rfq_number
     * 
     *   
    **/
    public function getOrderIdByRFQ($rfq_number = ''){
        if(strlen($rfq_number)){
            $q = $this->_db->getQuery(true);
            $q->select('ord.id');
            $q->from('#__poe_order ord');
            $q->innerJoin('#__poe_request r ON r.id=ord.rfq_id');
            $q->where('r.number='.$this->_db->quote($rfq_number));

            $this->_db->setQuery($q);

            $order_id = $this->_db->loadResult();

            return $order_id;
        }else{
            return false;
        }
    }
    /**
     * Get last order id for a specific user
     * 
     * @param int $juser_id Joomla user id
     * @return int $order_id
     */
    public function getUserLastOrderId($juser_id){
        $order_id = 0;
        
        $q = $this->_db->getQuery(true);
        $q->select('MAX(id)');
        $q->from('#__poe_order');
        $q->where('juser_id='.(int)$juser_id);
       
        $this->_db->setQuery($q);

        if(($result = $this->_db->loadResult())){
            $order_id = $result;
        }
        return $order_id;
    }
    public function getPaymentStatus($order_id){
        return 1; //pending
    }
    
    /**
     * Get Order Status name
     * @param int $status_id 
     * 
     * @return mixed boolen false / string 
     */
    public function getOrderStatusName($status_id = 0){
        
        $q = $this->_db->getQuery(true);
        $q->select('name');
        $q->from('#__poe_order_status');
        $q->where('id='.(int)$status_id);

        $this->_db->setQuery($q);

        $name = $this->_db->loadResult();

        return $name;
       
    }
    /**
     * Get order lines for a specifc order id
     * @param int $id Order Id
     * @return object $lines Order Lines
     */
    public function getOrderLines($id){
        $q = $this->_db->getQuery(true);
        $q->select('ln.*,p.name product_name, p.sku product_sku');
        $q->from('#__poe_order_line ln');
        $q->innerJoin('#__poe_product p ON p.id=ln.product_id');
        $q->where('ln.order_id='.(int)$id);

        $this->_db->setQuery($q);

        if(($lines = $this->_db->loadObjectList())){
            $model = JModel::getInstance('Product', 'POEcomModel');
            $idx = 0;
            foreach($lines as $ln){
                $lines[$idx]->selected_options = json_decode($ln->selected_options);
                $lines[$idx]->list_description = '';
                $lines[$idx]->properties = '';
                $product = $model->getProductDetail($ln->product_id);
               
                if(!empty($product->list_description)){
                    $lines[$idx]->list_description = $product->list_description;
                }
                if(!empty($product->properties)){
                    $lines[$idx]->properties = $product->properties;
                }
                
                $idx++;
            }
        }

        return $lines;
    }
    
    /**
     * Get order addresses
     * 
     * @param int $id Address Id
     * 
     * @return object Address
     */
    public function getOrderAddress($id){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('a.*, c.name country, r.name region, ju.email email');
        $q->from('#__poe_user_address a');
        $q->innerJoin('#__geodata_country c ON c.id=a.country_id');
        $q->innerJoin('#__geodata_region r ON r.id=a.region_id');
        $q->leftJoin('#__users ju ON ju.id=a.juser_id');
        $q->where('a.id='.(int)$id);
     
        $db->setQuery($q);
        
        $address = $db->loadObject();
    
        return $address;
    }
    /**
     * Get the order currency
     * @param int $id Currency Id
     * @return object Currency
     */
    public function getOrderCurrency($id){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_currency');
        $q->where('id='.(int)$id);
        $db->setQuery($q);
        
        $currency = $db->loadObject();
        
        return $currency;
    }
    /**
     * Get payment method
     * @param int $id Payment Method Id
     * @return object $method
     */
    public function getPayMethod($id){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_payment_method');
        $q->where('id='.(int)$id);
        $db->setQuery($q);
     
        if(($paymethod = $db->loadObject())){
            $lang = JFactory::getDocument()->language;
            $paymethod->logo .= $lang.'-'.$paymethod->plugin.'-logo.png';
        }
        
        return $paymethod;
    }
    /**
     * Get complete order detail
     * 
     * Used in print views and confirmation documents
     * 
     * @param int $id Order id
     * @return object $order
     */
    public function getOrderDetail($id){
        $order = $this->getItem($id);
        
        if($order){
            //get order lines
            $order->items = $this->getOrderLines($order->id);
           
            //get bt address
            $order->bt = $this->getOrderAddress($order->billing_id);
            //get st address
            if(!empty($order->shipping_id)){
                $order->st = $this->getOrderAddress($order->shipping_id);
            }
            //get currency info
            $order->currency = $this->getOrderCurrency($order->currency_id);
            
            //get payment method type
            $order->paymethod = $this->getPayMethod($order->pay_method_id);

            //get payment transaction
            if($order->payment_id > 0){
                $pModel = JModel::getInstance('PaymentTransaction', 'POEcomModel');
                $order->payment = $pModel->getTransactionByOrderId($id);
                //receipt fields are fields that must be displayed in the transaction receipt
                //per API requirements, e.g. Moneris
                if($order->payment->receipt_fields){
                    $receipt_fields = array();
                    $required_fields = json_decode($order->payment->receipt_fields);

                    $txn = json_decode($order->payment->transaction);

                    if($required_fields && $txn){
                        foreach($required_fields as $field){
                            $receipt_fields[$field] = $txn->$field;
                        }
                    }
                    $order->payment->mandatory_fields = $required_fields;
               }
            }else{
                $order->payment = '';
            }
            
            //get discount info
            if($order->coupon_id > 0 && $order->total_discount > 0){
                $cModel = JModel::getInstance('Coupon', 'POEcomModel');
                $coupon = $cModel->getCoupon($order->coupon_id);
                $order->coupon_code = $coupon->coupon_code;
            }
           
            //prepare carrier info
            if(strlen($order->selected_shipping)){
                $order->carrier = json_decode($order->selected_shipping);
            }
        }
        
        return $order;
    }
    
    /**
     * Update email sent field
     * 
     * @param int $order_id
     * @param int $value
     * @return boolean True on success
     */
    public function setEmailSent($order_id = 0, $value = 0){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->update('#__poe_order');
        $q->set('email_sent='.(int)$value);
        $q->where('id=' . (int) $order_id);
        $db->setQuery($q);

        if(!$db->query()){
            return false;
        }
        
        return true;
    }
}
?>