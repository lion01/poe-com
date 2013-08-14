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
 * Account Model
 */
class PoecomModelAccount extends JModelAdmin
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
            return JFactory::getUser()->authorise('core.edit', 'com_poecom.account.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
    public function getTable($type = 'Address', $prefix = 'PoecomTable', $config = array()){
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
        $form = $this->loadForm('com_poecom.account', 'account', array('control' => 'jform', 'load_data' => $loadData));
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
        return JURI::root(true).'/components/com_poecom/models/forms/account.js';
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
    public function getItem(){
        $juser = JFactory::getUser();
        $item = new JObject();
        
        if($juser->id > 1){
            //get user info
            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->select('u.name, u.username, u.email');
            $q->from('#__users u');
            $q->where('u.id=' . (int) $juser->id);
            $q->where('u.block=0');
            $db->setQuery($q);

            if( ($item = $db->loadObject()) ){
                
                //get BT info
                $q = $db->getQuery(true);
                $q->select('*');
                $q->from('#__poe_user_address');
                $q->where('juser_id=' . (int) $juser->id);
                $q->where('address_type='.$db->quote('BT'));
                $db->setQuery($q);
                
                $item->bt = $db->loadObject();
                
                //get ST info
                $q = $db->getQuery(true);
                $q->select('*');
                $q->from('#__poe_user_address');
                $q->where('juser_id=' . (int) $juser->id);
                $q->where('address_type='.$db->quote('ST'));
                $db->setQuery($q);
                
                $item->st = $db->loadObject();
                
                //get Request info
                $q = $db->getQuery(true);
                $q->select('r.id,r.number,rs.name status_name,r.order_id,r.date,r.total,r.currency_code');
                $q->from('#__poe_request r');
                $q->innerJoin('#__poe_request_status rs ON rs.id=r.status_id');
                $q->where('r.juser_id=' . (int) $juser->id);
                
                $db->setQuery($q);
       
                $item->requests = $db->loadObjectList();
                
                //get Order info
                $q = $db->getQuery(true);
                $q->select('o.id,o.order_date,o.rfq_id,os.name status_name,o.selected_shipping, o.total');
                $q->from('#__poe_order o');
                $q->innerJoin('#__poe_order_status os ON os.id=r.status_id');
                $q->where('r.juser_id=' . (int) $juser->id);
                
                $db->setQuery($q);
                
                $item->orders = $db->loadObjectList();
                
            }
        }

        return $item;
    }
    
    /**
     * Get user account info
     * 
     * @param int $id User ID
     * @return object $user
     */
    public function getUser($id = 0){
         //get user info
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id,name,email,password');
        $q->from('#__users');
        $q->where('id=' . (int) $id);
        $q->where('block=0');
        $db->setQuery($q);
        
        $user = $db->loadObject();
        
        return $user;
    }
    /**
     * Update user editable fields
     * 
     * @param object $user
     * @return boolean
     */
    public function updateUser($user = null){
        //validate
        if(!$user->id > 1 ||
                !strlen($user->name) ||
                !strlen($user->password) ||
                !strlen($user->email)){
            return false;
        }
        
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->update('#__users');
        $q->set('name ='.$db->Quote($user->name))
                ->set('password='.$db->Quote($user->password))
                ->set('email='.$db->Quote($user->email));
        $q->where('id=' . (int) $user->id);
        $db->setQuery($q);

        if((!$result = $db->query()) ){
            return false;
        }else{
            return true;
        }
    }
    /**
     * Get Customer Address
     * @param int $id Address Id
     * @return object $address
     */
    public function getAddress($id = 0){
         //get address info
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('*');
        $q->from('#__poe_user_address');
        $q->where('id=' . (int) $id);
        $db->setQuery($q);
        
        $address = $db->loadObject();
        
        return $address;
    }
    /**
     * Update customer address - can be BT or ST
     * @param object $address
     * @return boolean
     */
    public function updateAddress($address = null){
        
        //validate
        if(!$address->id > 0 ||
                strlen($address->fname) == 0 ||
                strlen($address->lname) == 0 ||
                strlen($address->street1) == 0 ||
                strlen($address->city) == 0 ||
                !$address->country_id > 0 ||
                !$address->region_id > 0 ||
                strlen($address->postal_code) == 0 ||
                strlen($address->telephone) == 0 ){
            return false;
        }
       
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->update('#__poe_user_address');
        $q->set('fname ='.$db->Quote($address->fname))
                ->set('lname='.$db->Quote($address->lname))
                ->set('street1='.$db->Quote($address->street1))
                ->set('street2='.$db->Quote($address->street2))
                ->set('city='.$db->Quote($address->city))
                ->set('country_id='.(int)$address->country_id)
                ->set('region_id='.(int)$address->region_id)
                ->set('postal_code='.$db->Quote($address->postal_code))
                ->set('telephone='.$db->Quote($address->telephone))
                ;
        $q->where('id=' . (int) $address->id);
        $db->setQuery($q);

        if((!$result = $db->query()) ){
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * Insert customer address - can be BT or ST
     * @param object $address
     * @return boolean
     */
    public function insertAddress($address = null){
        //validate
        if(
                !$address->juser_id > 1 ||
                strlen($address->address_type) == 0 ||
                strlen($address->fname) == 0 ||
                strlen($address->lname) == 0 ||
                strlen($address->street1) == 0 ||
                strlen($address->city) == 0 ||
                !$address->country_id > 0 ||
                !$address->region_id > 0 ||
                strlen($address->postal_code) == 0 ||
                strlen($address->telephone) == 0 ){
            return false;
        }
        
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->insert('#__poe_user_address');
        $q->set(array(
            "juser_id=".(int)$address->juser_id,
            "address_type=".$db->Quote($address->address_type),
            "fname=".$db->Quote($address->fname),
            "lname=".$db->Quote($address->lname),
            "street1=".$db->Quote($address->street1),
            "street2=".$db->Quote($address->street2),
            "city=".$db->Quote($address->city),
            "region_id=".(int)$address->region_id,
            "country_id=".(int)$address->country_id,
            "postal_code=".$db->Quote($address->postal_code),
            "telephone=".$db->Quote($address->telephone)
            ));
        $db->setQuery($q);

        if((!$result = $db->query()) ){
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * Delete a customer address
     * 
     * @param int $id Address Id
     * @return boolean
     */
    public function deleteAddress($id = 0){
        if(!$id > 0 ){
            return false;
        }
        
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->delete('#__poe_user_address');
        $q->where('id='.(int)$id);
        $db->setQuery($q);

        if((!$result = $db->query()) ){
            return false;
        }else{
            return true;
        }   
    }
}
