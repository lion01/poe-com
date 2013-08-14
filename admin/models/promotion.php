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
 * Promotion Model
 */
class PoecomModelPromotion extends JModelAdmin{
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
    public function getTable($type = 'Promotion', $prefix = 'PoecomTable', $config = array()){
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
        $form = $this->loadForm('com_poecom.promotion', 'promotion', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_poecom.promotion.edit.data', array());
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
	    //add names related to ids
	    $item->promotion_type = $this->getPromotionType($item->promotion_type_id);
	    $item->discount_type = $this->getDiscountType($item->discount_type_id);
	    $item->discount_amount_type = $this->getDiscountAmountType($item->discount_amount_type_id);
	    
	}
        return $item;
    }
    
    /**
     * Get the label label for promotion type
     * 
     * @param int $id
     * @return string
     */
    private function getPromotionType($id = 0){
	$label = '';
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('name');
        $q->from('#__poe_promotion_type');
	$q->where('id='.(int)$id);
        
        $db->setQuery($q);

        if( ($result = $db->loadResult()) ){
            $label = $result;
        }
	
	return $label;
    }
    
    /**
     * Get the label label for discount type
     * 
     * @param int $id
     * @return string
     */
    private function getDiscountType($id = 0){
	$label = '';
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('name');
        $q->from('#__poe_discount_type');
	$q->where('id='.(int)$id);
        
        $db->setQuery($q);

        if( ($result = $db->loadResult()) ){
            $label = $result;
        }
	
	return $label;
    }
    
    /**
     * Get the label label for discount amount type
     * 
     * @param int $id
     * @return string
     */
    private function getDiscountAmountType($id = 0){
	$label = '';
	
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('name');
        $q->from('#__poe_discount_amount_type');
	$q->where('id='.(int)$id);
        
        $db->setQuery($q);

        if( ($result = $db->loadResult()) ){
            $label = $result;
        }
	
	return $label;
    }
    
    /**
     * Check if promotion has coupons
     * 
     * @param int $id Promotion Id
     * @return boolean 
     */
    public function hasCoupons($id = 0 ){
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
        $q->select('COUNT(id)');
        $q->from('#__poe_coupon');
	$q->where('promotion_id='.(int)$id);
        
        $db->setQuery($q);

        $result = $db->loadResult();
	if($result > 0){
            return true;
        }else{
	    return false;
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
	
	if(strlen($data['end_time'])){
	    $date_parts = explode(" ",$data['end_time'] );
	    
	    if(count($date_parts) >= 1 ){
		$data['end_time'] = $date_parts[0]." 23:59:59";
	    }
	}
	
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
    
    //TODO: 
    /**
     * Delete Promotion
     * 
     * Checks dependencies before delete
     * 
     * @param array $cids List of Promotion ids to delete
     */
    public function delete(&$cids){
        $app = JFactory::getApplication();
        if($cids){
            //get table
            $table = $this->getTable();
            //get models
            $couponModel = JModel::getInstance('Coupon', 'POEcomModel');
            
            foreach($cids as $cid){
                //check coupons
                if(!$couponModel->PromotionDeleteValid($cid)){
                    $app->enqueueMessage(JText::_('COM_POECOM_PROMO_DELETE_COUPON_FOUND'), 'error');
                    return false;
                }
                
                if (!$table->delete($cid)){
                    $this->setError($table->getError());
                    return false;
                }
            }
        
        }else{
            $app->enqueueMessage(JText::_('COM_POECOM_NO_ITEM_SELECTED'),'error');
        }
        return true;
    }

}
