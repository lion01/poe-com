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
 * Coupon Model
 */
class PoecomModelCoupon extends JModelAdmin{
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
    public function getTable($type = 'Coupon', $prefix = 'PoecomTable', $config = array()){
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
        $form = $this->loadForm('com_poecom.coupon', 'coupon', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_poecom.coupon.edit.data', array());
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
        
        return $item;
    }
    
    /**
     * Generate a unique coupon code
     * 
     * If code already exists call this function again
     * 
     * @return string 
     */
    public function generateCouponCode(){
	$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$code = "";
	for ($i = 0; $i < 10; $i++) {
	    $code .= $chars[mt_rand(0, strlen($chars)-1)];
	}
	
	//check code does not exist
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('coupon_code');
        $q->from('#__poe_coupon');
	$q->where('coupon_code='.$db->quote($code));
        
        $db->setQuery($q);

        if( ($result = $db->loadResult()) ){
	    //code already used
            //recursive call
	    $this->generateCouponCode();
        }else{
	    return $code;
	}
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
	   
	    //validate request
	    if($isNew){
		switch($data['promotion_type_id']){
		    case '1': //Customer Direct
			if(($id = $this->getCouponId($data['promotion_id'], $data['user_id']))){
			    $this->setError(JText::_('COM_POECOM_COUPON_DIRECT_DUPLICATE_ERROR_MSG').$id);
			    return false;
			}
			break;
		    case '2': //General
			if(($id = $this->getCouponId($data['promotion_id']))){
			    $this->setError(JText::_('COM_POECOM_COUPON_GENERAL_DUPLICATE_ERROR_MSG').$id);
			    return false;
			}
			break;
		    case '3': //Numbered
			if(($id = $this->getCouponId($data['promotion_id'],0,$data['sequence_number']))){
			    $this->setError(JText::_('COM_POECOM_COUPON_NUMBERED_DUPLICATE_ERROR_MSG').$id);
			    return false;
			}
			break;
		    default:
			$this->setError(JText::_('COM_POECOM_PROMOTION_TYPE_MISSING_ERROR_MSG'));
			return false;
			break;
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
     * Get coupon By Id 
     * Includes promotion data
     * 
     * @param int $id Coupon ID
     * @return object $coupon
     */
    public function getCouponById($id){
	$db = $this->getDBO();
	$q = $db->getQuery(true);
	$q->select('c.*,p.*');
	$q->from('#__poe_coupon c');
	$q->innerJoin('#__poe_promotion p ON p.id=c.promotion_id');
	$q->where('c.id='.(int)$id);

	$db->setQuery($q);

	$coupon = $db->loadObject();

	return $coupon;
    }
    
    /**
     * Get Coupon Id
     * 
     * Validation function checks if coupon exists depending on promotion type
     * 
     * @param int $promotion_id
     * @param int $user_id
     * @param string $sequence_number Numbering sequence for Numbered coupons
     * 
     * @return boolean 
     */
    public function getCouponId($promotion_id = 0 , $user_id = 0, $sequence_number = null){
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_coupon');
	//General - only one coupon
	$q->where('promotion_id='.(int)$promotion_id);
	
	if($user_id > 0){
	    //Customer Direct
	    $q->where('user_id='.(int)$user_id);
	}else if( strlen($sequence_number)){
	    //Numbered - user_id is always 0
	    $q->where('sequence_number='.$db->quote($sequence_number));
	}
        
        $db->setQuery($q);

        if( ($id = $db->loadResult()) ){
	   
	    return $id;
        }else{
	    return false;
	}
    }
    
    /**
     * Get coupons for a promotion
     * 
     * @param int $promotion_id
     * @return array $coupons List of coupons
     */
    public function getPromotionCoupons($promotion_id){
	$db = $this->getDBO();
	$q = $db->getQuery(true);
	$q->select('*');
	$q->from('#__poe_coupon');
	$q->where('promotion_id='.(int)$promotion_id);

	$db->setQuery($q);

	$coupons = $db->loadObjectList();
	
	return $coupons;
    }
    
    
    /**
     * Delete Coupon
     * 
     * Checks dependencies before delete
     * 
     * @param array $cids List of Coupon ids to delete
     */
    public function delete(&$cids){
        $app = JFactory::getApplication();
        
        if($cids){
            //get table
            $table = $this->getTable();
            
            foreach($cids as $cid){
                //check status
                $coupon = $this->getItem($cid);
                
                //check status not (2)paided and there is no link to an rfq or order
                if($coupon->order_id > 0 ){
		    $app->enqueueMessage(JText::_('COM_POECOM_COUPON_DELETE_ERROR_ORDER').$coupon->order_id, 'error');
                    return false;
		}else if($coupon->rfq_id > 0){
		    $app->enqueueMessage(JText::_('COM_POECOM_COUPON_DELETE_ERROR_RFQ').$coupon->rfq_id, 'error');
                    return false;
		}else if($coupon->status_id == 2){
		    $app->enqueueMessage(JText::_('COM_POECOM_COUPON_DELETE_ERROR_STATUS').$coupon->status_id, 'error');
                    return false;
		}else{
		    if (!$table->delete($cid)){
                        $this->setError($table->getError());
                        return false;
                    }
		}
            }
        }else{
            $app->enqueueMessage(JText::_('COM_POECOM_NO_ITEM_SELECTED'), 'error');
            return false;
        }
        return true;
    }
    
    /*
     * Check if promotion used in coupons
     * 
     * @param int $promotion_id
     * 
     * @return boolean True means not found, delete promotion ok
     */
    public function promotionDeleteValid($promotion_id = 0){
        $db = $this->getDbo();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_coupon');
        $q->where('promotion_id='.(int)$promotion_id);
        
        $db->setQuery($q, '', 1);
        
        if(($result = $db->loadResult())){
            return false;
        }else{
            return true;
        }
    }
}
