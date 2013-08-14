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
 * Payment Model
 */
class PoecomModelPayment extends JModelAdmin{
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
    public function getTable($type = 'Payment', $prefix = 'PoecomTable', $config = array()){
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
	$form = $this->loadForm('com_poecom.payment', 'payment', array('control' => 'jform', 'load_data' => $loadData));
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
	return 'components/com_poecom/models/forms/payment.js';
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

	if (property_exists($item, 'params')) {
	    $registry = new JRegistry;
	    $registry->loadString($item->params);
	    $item->params = $registry->toArray();
	}

	// Convert the metadata field to an array.
	$registry = new JRegistry;
	$registry->loadString($item->metadata);
	$item->metadata = $registry->toArray();

	// Convert tax_exempt_ids to an array
	$item->tax_exempt_ids = json_decode($item->tax_exempt_ids);

	return $item;
    }
    
    
    public function getPaymentMethods(){
        // Get list of payment methods
        $q = $this->_db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_payment_method');
        $q->where('pm_enabled=1');
        $q->order('sort_order');
       
        $this->_db->setQuery($q);
            
        $pay_methods = $this->_db->loadObjectList();
        
        if($pay_methods){
            $lang = JFactory::getDocument()->language;
            $idx = 0;
            foreach($pay_methods as $pm){
                //set image
                $pay_methods[$idx]->logo = $pm->logo . $lang.'-'.$pm->plugin.'-logo.png';
           
                if($pm->type == 1){
                    $pay_methods[$idx]->accepted_cards = array();
                    //credit card direct
                    //get accepted card detail
                    $plugin_helper = new pluginHelper();
                    $params = $plugin_helper->getPluginParams($pm->plugin, 'poecompay');
                   
                    if($params->accepted_cards){
                        foreach($params->accepted_cards as $card){
                            $q = $this->_db->getQuery(true);
                            $q->select('*');
                            $q->from('#__poe_credit_cards');
                            $q->where('code="'.(string)$card.'"');
                            
                            $this->_db->setQuery($q);
                            
                            if(($card_info = $this->_db->loadObject())){
                                $pay_methods[$idx]->accepted_cards[] = $card_info;
                            }
                        }
                    }
                }
                $idx++;
            }
        }
     
        return $pay_methods;
    }
    
    /**
     * Get a Payment Method by ID
     * 
     * @param int $id Payment Method ID
     * 
     * @return object/false $pay_method
     */
    public function getPaymentMethod($id){
       
        $q = $this->_db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_payment_method');
        $q->where('id='.$this->_db->Quote($id) );

        $this->_db->setQuery($q);
            
        $pay_method = $this->_db->loadObject();
        
        return $pay_method;
    }
    
    /**
     * Get Payment Transaction for an RFQ
     * 
     * This function is called before an order is inserted
     * The transaction will be created when payment API returns a notification response
     * 
     * @param string $rfq_number String linking a payment notification to an RFQ 
     */
    public function getPaymentInfo($rfq_number = ""){
        $q = $this->_db->getQuery(true);
        $q->select('t.*');
        $q->from('#__poe_payment_transaction t');
        $q->innerJoin('#__poe_request r ON r.id=t.rfq_id');
        $q->where('r.number='.$this->_db->Quote($rfq_number) );

        $this->_db->setQuery($q);
            
        $payment = $this->_db->loadObject();
        
        return $payment;
    }
    /**
     * Get payment method for and RFQ
     * @param int $rfq_id
     * @return object $paymethod
     */
    public function getPaymentMethodByRFQId($rfq_id = 0){
        $paymethod = '';
        
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_request');
        $q->where('id=' . (int) $rfq_id);
        $db->setQuery($q);

       if (($rfq = $db->loadObject() )){
           //cart has payment method
           $cart = json_decode($rfq->cart);
        
           if($cart){
               $paymethod = $this->getPaymentMethod($cart->pay_method_id);
           }
       }
       return $paymethod;
    }
    
     /**
     * Get the return policy content for a specific language
     * 
     * @param array $article_ids Array of article ids
     * @return object $return_policy  
     */
    public function getReturnPolicy($article_ids = array()){
 
        $return_policy = '';
        if(!empty($article_ids)){
            $doc = JFactory::getDocument();
            
            $q = $this->_db->getQuery(true);
            $q->select('*');
            $q->from('#__content');
            $q->where('id IN ('.implode(",",$article_ids).')' );

            $this->_db->setQuery($q);
			
            if(($result = $this->_db->loadObjectList())){

                //set as default
                $policy = $result[0];

                if(count($result) > 1){
                    //find right language
                    foreach($result as $r){
                        if(strtolower($r->language) === $doc->language){
                            $policy = $r;
                            break;
                        }
                    }
                }
                if(!empty($policy->fulltext)){
                    $return_policy = $policy->fulltext;
                }else{
                    $return_policy = $policy->introtext;
                }
            } 
        }

        return $return_policy;
    }
}
