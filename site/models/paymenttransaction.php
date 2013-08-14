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
 * Payment Transaction Model
 */
class PoecomModelPaymentTransaction extends JModel{
    
    /**
     * Get Payment Transaction(s) for an RFQ
     * 
     * This function is called before an order is inserted
     * 
     * @param string $rfq_number String linking a payment notification to an RFQ 
     */
    public function getPaymentInfo($rfq_number = ""){
        $q = $this->_db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_payment_transaction');
        $q->where('rfq_number='.$this->_db->Quote($rfq_number) );

        $this->_db->setQuery($q);
            
        $payment = $this->_db->loadObjectList();
        
        return $payment;
    }
    
    
    /**
     * Update Transaction Order ID
     * 
     * @param int $transaction_id 
     * @param int $order_id
     * 
     * @return boolean 
     */
    public function updateTransaction($transaction_id = 0, $order_id = 0 ){
       
        $q = $this->_db->getQuery(true);
        $q->update('#__poe_payment_transaction');
        $q->set('order_id='.$order_id);
        $q->where('id='.$transaction_id );

        $this->_db->setQuery($q);
            
        if($this->_db->query()){
            return true;
        }else{
            return false;
        }
    }
    
    
    /**
     * Get Transactions for an Order
     * 
     * @param int $transaction_id
     * 
     * @return object $transaction Payment transaction with Pay Method 
     */
    public function getTransaction($transaction_id = 0){
        
        $q = $this->_db->getQuery(true);
        $q->select(array('pt.*','pm.name','pm.logo'));
        $q->from('#__poe_payment_transaction pt');
        $q->innerJoin('#__poe_payment_method pm ON pm.id=pt.pay_method_id');
        $q->where('pt.id='.(int)$transaction_id );

        $this->_db->setQuery($q);
        
        $transaction = $this->_db->loadObject();
        
        return $transaction;
    }
    
    /**
     * Get transaction by Order Id
     * 
     * For request/order display
     * 
     * @param int $order_id
     * 
     * @return object $transaction 
     */
    public function getTransactionByOrderId($order_id = 0){
        $q = $this->_db->getQuery(true);
        $q->select(array('pt.id','pt.transaction_number','pt.transaction','ps.name status','pm.name','pm.logo', 'pm.receipt_fields'));
        $q->from('#__poe_payment_transaction pt');
        $q->innerJoin('#__poe_payment_method pm ON pm.id=pt.pay_method_id');
        $q->innerJoin('#__poe_payment_status ps ON ps.id=pt.status_id');
        $q->where('pt.order_id='.(int)$order_id );

        $this->_db->setQuery($q);
        
        $transaction = $this->_db->loadObject();
        
        return $transaction;
    }
    
    
    /**
     * Get transaction by RFQ Id
     * 
     * For request/order display
     * 
     * @param int $rfq_id
     * 
     * @return object $transaction 
     */
    public function getTransactionByRFQId($rfq_id = 0){
        $q = $this->_db->getQuery(true);
        $q->select(array('pt.id','pt.transaction_number','ps.name status','pm.name','pm.logo'));
        $q->from('#__poe_payment_transaction pt');
        $q->innerJoin('#__poe_payment_method pm ON pm.id=pt.pay_method_id');
        $q->innerJoin('#__poe_payment_status ps ON ps.id=pt.status_id');
        $q->where('pt.rfq_id='.(int)$rfq_id );

        $this->_db->setQuery($q);
        
        $transaction = $this->_db->loadObject();
        
        return $transaction;
    }
    
    /**
     * Get transaction by Transaction Number
     * 
     * For order display
     * 
     * @param string $txn_number Transaction number assigned by payment gateway
     * 
     * @return object $transaction 
     */
    public function getTransactionByNumber($txn_number = ''){
        $q = $this->_db->getQuery(true);
        $q->select(array('pt.id','pt.transaction_number','ps.name status','pm.name','pm.logo'));
        $q->from('#__poe_payment_transaction pt');
        $q->innerJoin('#__poe_payment_method pm ON pm.id=pt.pay_method_id');
        $q->innerJoin('#__poe_payment_status ps ON ps.id=pt.status_id');
        $q->where('pt.transaction_number='.$this->_db->Quote('$txn_number') );

        $this->_db->setQuery($q);
        
        $transaction = $this->_db->loadObject();
        
        return $transaction;
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
    * Method to save the form data.
    *
    * @param   array  $data  The form data.
    *
    * @return  boolean  True on success, False on error.
    *
    * @since   11.1
    */
    public function save($data){
        // Initialise variables;
        $dispatcher = JDispatcher::getInstance();
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
/*
        $pkName = $table->getKeyName();

        if (isset($table->$pkName)){
            $this->setState($this->getName() . '.id', $table->$pkName);
        }
        $this->setState($this->getName() . '.new', $isNew);
*/
        return true;
    }
}
