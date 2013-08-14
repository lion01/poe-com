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
 * Payment Transaction Model
 */
class PoecomModelPayTransaction extends JModelAdmin{
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
    public function getTable($type = 'PaymentTransaction', $prefix = 'PoecomTable', $config = array()){
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
        $form = $this->loadForm('com_poecom.paytransaction', 'paytransaction', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)){
            return false;
        }
        return $form;
    }
    
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	1.6
    */
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_poecom.paytransaction.edit.data', array());
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
        
        if($item){
            //get rfq number
            $model = JModel::getInstance('Request', 'PoecomModel');
            
            $rfq = $model->getItem($item->rfq_id);
            
            if($rfq){
                $item->rfq_number = $rfq->number;
            }
            
            //create txn_display
            //decode transaction
            if(strlen($item->transaction)){
                $txn = json_decode($item->transaction);
                $txn_display = '';

                if($txn){
                    foreach($txn as $k => $v){
                        $txn_display .= $k . ": ".$v."\n";
                    }
                }

                $item->txn_display = $txn_display;
            }
        }
        
        return $item;
    }
    
    /**
     * Get transaction by RFQ id
     * 
     * @param int $rfq_id 
     * 
     * @return object/boolean
     */
    public function getRFQTransaction($rfq_id){
        $db = $this->getDBO();
        $q = $db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_payment_transaction');
        $q->where('rfq_id='.(int)$rfq_id);
        
        $db->setQuery($q);

        if($pay_trans = $db->loadObject()){
            return $pay_trans;
        }else{
            return false;
        }
    }
    
    
    /**
     * Check if there are any transactions linked to an RFQ id
     * 
     * @param int $rfq_id
     * @return boolean 
     */
    public function checkRFQ($rfq_id){
        $db = $this->getDBO();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_payment_transaction');
        $q->where('rfq_id='.(int)$rfq_id);
        
        $db->setQuery($q);
        
        $pay_trans = $db->loadResultArray();
        
        if($pay_trans){
            return $pay_trans;
        }else{
            return false;
        }
    }
    
    /**
     * Set transaction order id to 0 
     * 
     * Used when deleting an order
     * 
     * @param int $id Transaction Id 
     */
    public function resetOrderId($id = 0){
        //get the transaction
        $txn = $this->getItem((int)$id);
        
        if($txn){
            $txn->order_id = 0;
            $txn = JArrayHelper::fromObject($txn);
           
            if(!$this->save($txn)){
                JError::raiseWarning(500, JText::_('COM_POECOM_PAY_TXN_ORDER_RESET_ERROR'). $id. $this->getError());
                return false;
            }
        }else{
            JError::raiseWarning(500, JText::_('COM_POECOM_PAY_TXN_NOT_FOUND_ERROR') . $id);
            return false;
        }
        return true;
    }
    
  
    
    /**
    * Over ride Save method that does not use content plugin
    *
    * @param   array  $data  The form data.
    *
    * @return  boolean  True on success, False on error.
    *
    * @since   11.1
    */
    public function save($data){
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
        }
        catch (Exception $e){
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
     * Delete Payment Transaction
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
            
            $order_model = JModel::getInstance('Order', 'POEcomModel');
            
            foreach($cids as $cid){
                //check status
                $txn = $this->getItem($cid);
                
                //check status not (3)paided or (4)shipped
                if($txn->order_id > 0 ){
                    //reset payment_id on order
                    if(!$order_model->resetPaymentId($txn->order_id)){
                        JError::raiseWarning(500, JText::_('COM_POECOM_ORDER_PAY_TXN_RESET_ERROR').$txn->order_id);
                        $error = true; 
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

}
