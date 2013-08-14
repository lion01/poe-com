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
 * Product Model
 */
class PoecomModelRegistration extends JModelAdmin{
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
    public function getTable($type = 'Registration', $prefix = 'PoecomTable', $config = array()){
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
    public function getForm($data = array(), $loadData = true) {
	// Get the form.
	$form = $this->loadForm('com_poecom.registration', 'registration', array('control' => 'jform', 'load_data' => $loadData));
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
	return JURI::root(true).'/components/com_poecom/models/forms/registration.js';
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

	return $item;
    }
    
    /**
     *Create user for auto loign
     * 
     * @param array $data User data
     * @return boolean 
     */
    public function createJUser($data = null){
        $data_str = '';
        if($data == null){
            return false;
        }else{
	    // create data string
	    foreach($data as $k => $v){
	    $data_str .= $k."='".$v."',";
	    }
            
            if(strlen($data_str)){
                //split off the last comma
                $data_str = substr($data_str, 0, strlen($data_str) - 1 );
            }
        }
   
        $this->_db->setQuery($this->_db->getQuery(true)
                ->insert('#__users', 'id')
                ->set($data_str));
                
	if($this->_db->query()){
	    $id = $this->_db->insertid();

	    return $id;
	}else{
	    return false;
	}
    }
    
    /**
     * Assign Default POE-com user group
     * 
     * @param int $id User ID
     * @return boolean 
     */
    public function assignDefaultGroup($id){
	
	$params = JComponentHelper::getParams('com_poecom');
	
	$group_id = $params->get('poegroup', 0);
        
        $this->_db->setQuery($this->_db->getQuery(true)
                ->insert('#__user_usergroup_map')
                ->set('user_id='.$id.',group_id='.(int)$group_id));
                
	if($this->_db->query()){
	    return true;
	}else{
	    return false;
	}
    }
    
    /**
     * Create a Billing Address for the User
     * 
     * @param int $juser_id Joomla User Id
     * @param array $userData Registration form data
     * 
     * @return boolean True/False
     */
    public function createPOEcomAddress($juser_id, $userData){

        if(!$juser_id > 0 || !$userData){
            return false;
        }
        
        $data = array();
        
        $data['juser_id'] = $juser_id;
        
        if( !isset($userData['address_type']) || $userData['address_type'] != 'ST'){
            $data['address_type'] = 'BT';
        }
        
        $data['fname'] = $userData['fname'];
        $data['lname'] = $userData['lname'];
        $data['street1'] = $userData['street1'];
        $data['street2'] = $userData['street2'];
        $data['city'] = $userData['city'];
        $data['region_id'] = $userData['region_id'];
        $data['country_id'] = $userData['country_id'];
        $data['postal_code'] = $userData['postal_code'];
        $data['telephone'] = $userData['telephone'];
        
        $model = JModel::getInstance('address', 'PoecomModel');
        
        if($model->save($data)){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * Check if username exists
     * 
     * Users email is used as username and must be unique. Its also possible that
     * a user gets entered manually and creates a conflict with user email, so
     * check for email as well.
     * 
     * @param string $name Username
     * @return boolean 
     */
    public function usernameExists($name = ''){
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
	$q->select('COUNT(username)');
	$q->from('#__users');
	$q->where('username='.$db->quote(strtolower($name)) );
	
	$db->setQuery($q);
	
	$result = $db->loadResult();
	
	if($result != 0){
	    //either username not found or query returned -1
	    return true;
	}else{
	    //username does not exist, check email
            $q = $db->getQuery(true);
            $q->select('COUNT(email)');
            $q->from('#__users');
            $q->where('email='.$db->quote(strtolower($name)));

            $db->setQuery($q);

            $result = $db->loadResult();
            if($result != 0){
                //either email found or query returned -1
                return true;
            }else{
                return false;
            }
	}
    }
    
        /**
     * Check if username exists
     * 
     * Users email is used as username and must be unique. Its also possible that
     * a user gets entered manually and creates a conflict with user email, so
     * check for email as well.
     * 
     * @param string $email Username
     * @return boolean 
     */
    public function userEmailExists($email = ''){
        
	$db = $this->getDBO();
	
	$q = $db->getQuery(true);
	$q->select('id');
	$q->from('#__users');
	$q->where('username='.$db->quote(strtolower($email)) );
	
	$db->setQuery($q);
	
	if(($result = $db->loadResult())){
            $id = $result;
        }else{
	    //username does not exist, check email
            $q = $db->getQuery(true);
            $q->select('id');
            $q->from('#__users');
            $q->where('email='.$db->quote(strtolower($email)));

            $db->setQuery($q);

            $id = $db->loadResult(); 
	}
        return $id;
    }
    
    
    /**
     * User registration limit check
     * 
     * Based on component parameters limit the number of times a user can 
     * register with given attributes
     * 
     * @param array $userData Data provided in registration form
     * @return boolean True means allow new registration
     */
    public function userLimitCheck($userData = array()){
        
        if(empty($userData)){
            return false;
        }
        
        //get validation criteria
        $params = JComponentHelper::getParams('com_poecom');
        $unique_email = $params->get('uniqueemail', '0');
        $unique_bt = $params->get('uniquebt', '0');
        
        if(!empty($userData['email1'])){
            $user_id = $this->userEmailExists($userData['email1']);
        }
       
        if($unique_email === '1' && !empty($user_id)){
            //user found
            return false;
        }
        
        if($unique_bt === '1'){
            //check address against user found
            $addressModel = JModel::getInstance('Address', 'PoecomModel');
        
            if(( $address_id = $addressModel->getAddressId($userData))){
            
                //address found
                return false;
            }
        }
        
        return true;
    }
}
