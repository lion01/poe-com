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
 * Order Model
 */
class PoecomModelOrder extends JModelAdmin{
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
    public function getTable($type = 'Order', $prefix = 'PoecomTable', $config = array()){
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
        $form = $this->loadForm('com_poecom.order', 'order', array('control' => 'jform', 'load_data' => $loadData));
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
        return JURI::root(true).'/administrator/components/com_poecom/models/forms/order.js';
    }
    
	
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	1.6
    */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.product.edit.data', array());
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
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
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

        //add order lines
        if($item->id > 0 ){
            $lineModel = JModel::getInstance('OrderLines', 'PoecomModel');
            $lineModel->setState('filter.order', $item->id);
            
            $item->lines = $lineModel->getItems();
        }
        
        //add selected shipping
        if(!empty($item->selected_shipping)){
            $item->carrier = json_decode($item->selected_shipping);
            
            //test the delivery date
            if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $item->carrier->eta)){
                
                if(!empty($item->carrier->eta)){
                    //some methods don't set a eta date, but have a string message
                    $item->carrier->service .= ' : '.$item->carrier->eta;
                }
                $item->carrier->eta = '';
            }
        }else{
            $item->carrier = '';
        }
        
        //add user_bt
        $addressModel = JModel::getInstance('Address', 'PoecomModel');
        if($item->billing_id > 0){
            $item->user_bt = $addressModel->getItem($item->billing_id);
        }
        
        //add user_st
        if($item->shipping_id > 0 ){
            $item->user_st = $addressModel->getItem($item->shipping_id);
        }
	
	if($item->coupon_id > 0){
                
	    $couponModel = JModel::getInstance('Coupon', 'PoecomModel');
	    if(($promotion = $couponModel->getCouponById($item->coupon_id))){
		$item->promotion = $promotion;
	    }
	}
	
        return $item;
    }
    
    public function checkRFQ($rfq_id){
        $db = $this->getDBO();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_order');
        $q->where('rfq_id='.(int)$rfq_id);
        
        $db->setQuery($q);
        
        $order = $db->loadResultArray();
        
        if($order){
            return true;
        }else{
            return false;
        }
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
     * Over ride method that calls parent::save 
     * 
     * Included for possible extension
     * 
     * @param array $data Form values to store
     * 
     * @return boolean True = order data stored
     */
    public function save($data){
        
        // Save the order item 
        if(!parent::save($data)){
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * Get Order Status 
     * 
     * @param type $id
     * @return type 
     */
    public function getStatus($id){
        $db = $this->getDBO();
        $q = $db->getQuery(true);
        $q->select('status_id');
        $q->from('#__poe_order');
        $q->where('id='.(int)$id);

        $db->setQuery($q);
        
        $status_id = $db->loadResult();
        
        return $status_id;
    }
    
    /**
     * Set order payment transaction id to 0 
     * 
     * Used when deleting an payment transaction
     * 
     * @param int $id Order Id 
     */
    public function resetPaymentId($id = 0){
        //get the order
        $order = $this->getItem((int)$id);
        
        if($order){
            $order->payment_id = 0; // no order
            $order->status_id = 2; // open
            $order = JArrayHelper::fromObject($order);
            if(!$this->save($order)){
                JError::raiseWarning(500, JText::_('COM_POECOM_ORDER_PAY_TXN_RESET_ERROR'). $id. '<br/>'. $this->getError());
                return false;
            }
        }else{
            JError::raiseWarning(500, JText::_('COM_POECOM_ORDER_NOT_FOUND_ERROR') . $id);
            return false;
        }
        return true;
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
            $line_model = JModelAdmin::getInstance('OrderLine', 'PoecomModel');
            $pay_model = JModelAdmin::getInstance('PayTransaction', 'PoecomModel');
            $rfq_model = JModelAdmin::getInstance('Request', 'PoecomModel');
            
            foreach($cids as $cid){
                //check status
                $order = $this->getItem($cid);
                
                //check status not (3)paided or (4)shipped
                if($order->status_id == 3 || $order->satus_id == 4){
                    JError::raiseWarning(500, JText::_('COM_POECOM_ORDER_STATUS_DELETE_ERROR'));
                    $error = true; 
                }else{
                    //delete the lines
                    if($order->lines){
                        $line_ids = array();
                        foreach($order->lines as $line){
                            array_push($line_ids, $line->id);
                        }
                        
                        //Start here - delete lines
                        if($line_ids){
                            
                            if(!$line_model->delete($line_ids)){
                                JError::raiseWarning(500, JText::_('COM_POECOM_ORDER_LINES_DELETE_ERROR').$order->id);
                                $error = true;
                            }else if(!empty($order->payment_id) &&!$pay_model->resetOrderId($order->payment_id)){
                                //reset payment transaction order_id to 0
                                JError::raiseWarning(500, JText::_('COM_POECOM_PAY_TXN_ORDER_RESET_ERROR'). $order->payment_id);
                                $error = true;
                            }else if(!empty($order->rfq_id) && !$rfq_model->resetOrderId($order->rfq_id)){
                                //reset rfq order_id to 0
                                JError::raiseWarning(500, JText::_('COM_POECOM_RFQ_ORDER_RESET_ERROR'). $order->rfq_id);
                                $error = true;
                            }
                        }
                    }
                }  
            }
        
            //delete the order header  
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
        }else{
            JError::raiseWarning(500, JText::_('COM_POECOM_NO_ITEM_SELECTED'));
        }
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
        $q->from('#__poe_order');
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
            $q->from('#__poe_order');
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
