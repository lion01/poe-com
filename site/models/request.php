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
 * Request Model
 */
class PoecomModelRequest extends JModelAdmin
{
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
            return JFactory::getUser()->authorise('core.edit', 'com_poecom.number.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
        return JURI::root(true).'/components/com_poecom/models/forms/request.js';
    }
    
	
    /**
    * Method to get the data that should be injected in the form.
    *
    * @return	mixed	The data for the form.
    * @since	1.6
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

        if (property_exists($item, 'params')) {
                $registry = new JRegistry;
                $registry->loadString($item->params);
                $item->params = $registry->toArray();
        }

        // Convert the metadata field to an array.
        $registry = new JRegistry;
        $registry->loadString($item->metadata);
        $item->metadata = $registry->toArray();

        return $item;
    }
    
    
    /**
     * Create order from RFQ
     * 
     * 
     * @param string $rfq_number
     * @param int $payment_id Payment transaction id
     * @param int $payment_status Current status of the pyament transaction
     * 
     * @return mixed boolean false or order_id  
    */
    public function createOrderFromRFQ($rfq_number = '', $payment_id = 0, $payment_status = 1){
        if(strlen($rfq_number)){
            
            $jsess = JFactory::getSession();
            
            $rfq = $this->getRFQ($rfq_number);
            
            if($rfq){
                $today = new JDate();
                
                if($rfq->cart->user_st){
                    $shipping_id = $rfq->cart->user_st->id;
                }else{
                    $shipping_id = '';
                }
                
                if($rfq->cart->selected_shipping){
                    $selected_shipping = json_encode($rfq->cart->selected_shipping);
                }else{
                    $selected_shipping = '';
                }
                
                //set order status
                switch($payment_status){
                    case '1': //pending
                        $order_status = 2; //invoiced
                        break;
                    case '2': //complete
                        $order_status = 3; //paided
                        break;
                    default:
                        //any other payment status 
                        $order_status = 1; //open
                        break;
                }
                
                $model = JModel::getInstance('Order', 'PoecomModel');
                
                //check for existing order
                if(($existing_order_id = $model->getOrderIdByRFQ($rfq_number))){
                    $order_id = $existing_order_id;
                }else{
                    $order_id = '';
                }
              
		if($rfq->cart->discount){
		    $coupon_id = $rfq->cart->discount->coupon_id;
		}else{
		    $coupon_id = 0;
		}
		
                //build order data, order lines will be inserted in store()
                $data = array(
                    'id' => $order_id,
                    'status_id' => $order_status,
                    'rfq_id' => $rfq->id,
                    'payment_id' => $payment_id,
                    'order_date' => $today->toMySQL(true),
                    'juser_id' => $rfq->juser_id,
                    'billing_id' => $rfq->cart->user_bt->id,
                    'shipping_id' => $shipping_id,
                    'selected_shipping' => $selected_shipping ,
                    'subtotal' => $rfq->cart->subtotal,
		    'coupon_id' => $coupon_id,
		    'total_discount' => $rfq->cart->discount_amount,
                    'product_tax' => $rfq->cart->product_tax,
                    'shipping_cost' => $rfq->cart->shipping_cost,
                    'shipping_tax' => $rfq->cart->shipping_tax,
                    'total' => $rfq->cart->total,
                    'ip_address' => $rfq->cart->ip_address,
                    'email_sent' => 0
                );
            
                if($model->save($data)){
                    if(!strlen($order_id)){
                        $order_id = $model->getOrderIdByRFQ($rfq_number);
                    }
                    
                    //update request
                    if(!$this->updateRequest($rfq->id, $order_id, 2)){
                        $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_RFQ_UPDATE_ERROR'), 'poecom' );
                        $jsess->set('appMsgType', 'error', 'poecom' );
                        return false;
                    }
                    
                    //update payment transaction
                    $model = JModel::getInstance('PaymentTransaction', 'PoecomModel');
                    
                    if(!$model->updateTransaction($payment_id, $order_id)){
                        $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_PAY_TRANS_UPDATE_ERROR'), 'poecom' );
                        $jsess->set('appMsgType', 'error', 'poecom' );
                        return false;
                    }
                   
                    if($order_id > 0 && $rfq->cart->items){
                        $model = JModel::getInstance('OrderLine', 'PoecomModel');
                        foreach($rfq->cart->items as $itm){
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
                            
                            if(!$model->save($data)){
                                $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_LINES_INSERT_ERROR'), 'poecom' );
                                $jsess->set('appMsgType', 'error', 'poecom' );
                                return false;
                            }
                        } 
                    }else{
                        $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_NO_LINES_ERROR'), 'poecom' );
                        $jsess->set('appMsgType', 'error', 'poecom' );
                        return false;
                    }
                    
                    $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_INSERTED'), 'poecom' );
                    $jsess->set('appMsgType', 'message', 'poecom' );
                    
                    //clear the cart
                    $jsess->clear('cart','poecom');
                    $jsess->clear('shipping', 'poecom');
                    
                    return $order_id;
                }else{
                    $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_INSERT_ERROR'), 'poecom' );
                    $jsess->set('appMsgType', 'error', 'poecom' );
                    return false;
                }
            }else{
                $jsess->set('appMsg', JText::_('COM_POECOM_RFQ_NOT_FOUND_ERROR'), 'poecom' );
                $jsess->set('appMsgType', 'error', 'poecom' );
                return false;
            }
        }else{
            $jsess->set('appMsg', JText::_('COM_POECOM_NO_RFQ_NUMBER_ERROR'), 'poecom' );
            $jsess->set('appMsgType', 'error', 'poecom' );
            return false;
        }
    }
    
    /**
     * Update RFQ order id and status
     * 
     * @param int $rfq_id RFQ to update
     * @param int $order_id
     * @param int $status RFQ status 2 = ordered
     * 
     * @return boolean 
     */
    public function updateRequest($rfq_id, $order_id, $status_id){
        $q = $this->_db->getQuery(true);
        $q->update('#__poe_request');
        $q->set('order_id='.$order_id);
        $q->set('status_id='.$status_id);
        $q->where('id='.$rfq_id );

        $this->_db->setQuery($q);
        if(!$this->_db->query()){
            return false;
        }else{
            return true;
        }
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
     * Get RFQ ID for RFQ Number
     * 
     * @param string $rfq_number
     * 
     * @return int/boolean ID on success, false on failure
     */
    public function getID($rfq_number = ''){
        
        $q = $this->_db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_request');
        $q->where('number='.$this->_db->Quote($rfq_number) );

        $this->_db->setQuery($q);
            
        $id = $this->_db->loadResult();
        
        return $id;
    }
    /**
     * Get payment method type
     * @param int $id Payment Method Id
     * @return int $type
     */
    public function getPayMethodType($id){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('type');
        $q->from('#__poe_payment_method');
        $q->where('id='.(int)$id);
        $db->setQuery($q);
     
        $type = 0;
        if(($result = $db->loadResult())){
            $type = $result;
        }
        return $type;
    }
   
    /**
     * Get complete RFQ detail
     * 
     * Used in print views and confirmation documents
     * 
     * @param string $rfq_number 
     * 
     * @return object $order
     */
    public function getRFQDetail($rfq_number){
        $rfq = $this->getRFQ($rfq_number);
      
        if($rfq->cart){
            
            //get properties and values from cart
            $data = $rfq->cart;
            $data->status_id = $rfq->status_id; 
            $data->rfq_number = $rfq_number;
            //get order lines
            //$rfq->items = $rfq->cart->items;
            if($data->items){
                $model = JModel::getInstance('Product', 'POEcomModel');
                $idx = 0;
                foreach($data->items as $ln){
                    $data->items[$idx]->selected_options = $ln->selected_options;
                    $data->items[$idx]->list_description = $model->getListDescription($ln->product_id);
                    $idx++;
                }
            }
           
            //get bt address
            $data->bt = $rfq->cart->user_bt;
            //get st address
            $data->st = $rfq->cart->user_st;
         
            //get payment method type
            if(!empty($rfq->cart->pay_method)){
                $data->paymethod_type = $this->getPayMethodType($rfq->cart->pay_method);
            }else{
                $data->paymethod_type = '';
            }
            
           
            //get payment transaction
            if(!empty($data->payment_id)){
                $pModel = JModel::getInstance('PaymentTransaction', 'POEcomModel');
                $data->payment = $pModel->getTransactionByOrderId($data->order_id);
                //receipt fields are fields that must be displayed in the transaction receipt
                //per API requirements, e.g. Moneris
                if(!empty($data->payment->receipt_fields)){
                    $receipt_fields = array();
                    $required_fields = json_decode($data->payment->receipt_fields);

                    $txn = json_decode($rfq->payment->transaction);

                    if($required_fields && $txn){
                        foreach($required_fields as $field){
                            $receipt_fields[$field] = $txn->$field;
                        }
                    }
                    $data->payment->mandatory_fields = $required_fields;
               }
            }
            
            //get discount info
            if(!empty($data->discount->coupon_id) && $data->total_discount > 0){
                $cModel = JModel::getInstance('Coupon', 'POEcomModel');
                $coupon = $cModel->getCoupon($data->discount->coupon_id);
                $data->coupon_code = $coupon->coupon_code;
            }
            
            $data->carrier = $data->selected_shipping;
            
            //clean up - $data will only contain values for views
            unset($data->user_bt);
            unset($data->user_st);
            unset($data->entrystatus);
            unset($data->idx);
            unset($data->ip_address);
            unset($data->lastpage);
        }
        return $data;
    }
    
    /**
     * Update email sent field
     * 
     * @param int $rfq_id
     * @param int $value
     * @return boolean True on success
     */
    public function setEmailSent($rfq_id = 0, $value = 0){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->update('#__poe_request');
        $q->set('email_sent='.(int)$value);
        $q->where('id=' . (int) $rfq_id);
        $db->setQuery($q);

        if(!$db->query()){
            return false;
        }
        
        return true;
    }
}
