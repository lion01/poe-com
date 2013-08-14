<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Address Model
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
 * Address Model
 */
class PoecomModelAddress extends JModelForm{
   
    /**
    * Method for getting the model form
    * 
    * Either load billing or shipping XML depending on what is being updated.
    *
    * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
    * @return	mixed	A JForm object on success, false on failure
    * @since	11.1
    */
    public function getForm($data = array(),$loadData = true, $address_type = 'BT'){
        $address_XML = 'addressBT';

        if($data['address_type'] == 'ST'){
            $address_XML = 'addressST';
        }else if(!$data && $address_type == 'ST'){
            $address_XML = 'addressST';
        }

        $form = $this->loadForm('com_poecom.address', $address_XML, array('control' => 'jform', 'load_data' => $loadData));    

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

        $table = $this->getTable('address','PoecomTable');

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

        if (property_exists($item, 'params')){
            $registry = new JRegistry;
            $registry->loadString($item->params);
            $item->params = $registry->toArray();
        }

        return $item;
    }
    
    /**
    * Method to get the script that have to be included on the form
    *
    * @return string	Script files
    */
    public function getScript(){
        return JURI::root(true).'/components/com_poecom/models/forms/address.js';
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
        
        $params = JComponentHelper::getParams('com_poecom');
        
        $user = JFactory::getUser();

        if($user->id == $user_id || in_array((string)$params->get('poeadmingroup'), $user->groups, true) ){
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

        $user = JFactory::getUser();
        if(!$this->canSave($user->id)){
            
            $this->setError(JText::_('COM_POECOM_USER_MISMATCH_SAVE_ERROR'));
            return false;
        }

        // Initialise variables;
        $table = $this->getTable('address', 'PoecomTable');

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

                if($user->id != 1){
                    if($data['juser_id'] <= 0){ //has value in admin update
                        // no guest addresses
                        $data['juser_id'] = $user->id;
                    }
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
    * Delete addresses
    * 
    * NOTE: This function should only be used by Customer delete, which checks 
    * order and requests dependencies
    *
    * @param   array  &$cids  Array of ids
    *
    * @return  boolean  True means successfully deleted
    */
    public function delete(&$cids){
        $app = JFactory::getApplication();

        if (!empty($cids)) {
            //get table
            $table = $this->getTable('address', 'PoecomTable');
            
            foreach ($cids as $cid) {
                //delete Joomla user
                if (!$table->delete($cid)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        } else {
            $app->enqueueMessage(JText::_('COM_POECOM_NO_ITEM_SELECTED'), 'error');
            return false;
        }
        // Clear the component's cache
        $this->cleanCache();

        return true;
    }
    
    /**
     * Delete user addresses 
     * 
     * @param int $juser_id Joomla User Id
     * 
     * @return boolean True means addresses deleted or not addresses found for user id
     */
    public function deleteUserAddresses($juser_id = 0 ){
        //get address ids
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id');
        $q->from('#__poe_user_address');
        $q->where('juser_id=' . (int) $juser_id);
        $db->setQuery($q);

        if(($ids = $db->loadResultArray())){
            if(!$this->delete($ids)){
                return false;
            }
        }
        
        return true;
    }
    
     /**
     * Update address
     * @param int $id Address Id
     * @param array $values key => value of fields to update
     * @return boolean
     */
    public function update($id = 0, $values = array()){
        if(!empty($id) && !empty($values)){
            $db = $this->getDbo();
            $q = $db->getQuery(true);
            $q->update('#__poe_user_address');
            if(!empty($values['fname'])){
                $q->set('fname='.$db->quote($values['fname']));
            }
            if(!empty($values['lname'])){
                $q->set('lname='.$db->quote($values['lname']));
            }
            if(!empty($values['street1'])){
                $q->set('street1='.$db->quote($values['street1']));
            }
            if(!empty($values['street2'])){
                $q->set('street2='.$db->quote($values['street2']));
            }
            if(!empty($values['city'])){
                $q->set('city='.$db->quote($values['city']));
            }
            if(!empty($values['country_id'])){
                $q->set('country_id='.(int)$values['country_id']);
            }
            if(!empty($values['region_id'])){
                $q->set('region_id='.(int)$values['region_id']);
            }
            if(!empty($values['postal_code'])){
                $q->set('postal_code='.$db->quote($values['postal_code']));
            }
            if(!empty($values['telephone'])){
                $q->set('telephone='.$db->quote($values['telephone']));
            }
            
            $q->where('id='.(int)$id);
            
            $db->setQuery($q);
            
            if(!$db->query()){
                return false;
            }
        }else{
            $this->setError('id or values missing');
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Get User Address
     * 
     * Either a billing (BT) or shipping (ST) address
     * 
     * @param int $user_id
     * @param string $type Address type
     * 
     * @return object $address
     */
    public function getAddress($user_id = 0, $type = 'BT'){
        
        $q = $this->_db->getQuery(true);
        $q->select('ua.*, c.name country, c.code2 countryCode, r.name region, r.code2 regionCode');
        $q->from('#__poe_user_address ua');
        $q->innerJoin('#__geodata_country c ON c.id=ua.country_id');
        $q->innerJoin('#__geodata_region r ON r.id=ua.region_id');
        $q->where('ua.juser_id='.$user_id.' AND ua.address_type='.$this->_db->quote($type));
       
        $this->_db->setQuery($q);
        
        if(($address = $this->_db->loadObject())){
            $address->full_name = $address->fname . ' '.$address->lname;
        }
        
        return $address;
    } 
}
?>