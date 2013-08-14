<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Registration Controller
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 8:35:13 AM
 *
 * http://www.exps.ca
**/
jimport('joomla.application.component.controllerform');

class PoecomControllerRegistration extends JControllerForm{
    
    /**
     * Register new users and auto login
     * 
     * @return boolean 
     */
    function register(){
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // check the token
        $check_token = $jinput->get(JUtility::getToken(), null, 'int');
        
	// Check that the token is in a valid format.
	if ($check_token === null || $check_token !== 1) {
		JError::raiseError(403, JText::_('JINVALID_TOKEN'));

		return false;
	}
        
        // check the user state
        $user = JFactory::getUser();
		
        // Get the user data.
	$userData = $jinput->get('jform', array(), 'array');

	// If not logged in user try to create new user
	if ($user->guest == 1) {
	    $model = $this->getModel('Registration', 'PoecomModel');
	    
	    //check if email address already used
	    //if(($exists = $model->usernameExists($userData['email1']))){
            if((!$allow = $model->userLimitCheck($userData))){
		//Show view with login link and message
		$view = $this->getView('Registration', 'html');
		$view->setLayout('userexists');
		$view->userExists();
	    }else{
         
		$mysqldate = JFactory::getDate()->toSql();
		$pass = JUserHelper::genRandomPassword();

		$salt = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($pass, $salt);
		$password = $crypt . ':' . $salt;

		// prepare Joomla user data
		$new_user = array(
		'username' => $userData['email1'],
		'email' => $userData['email1'],
		'name' => $userData['fname']. " ". $userData['lname'],
		'password'=> $password,
		'usertype' => 'deprecated',
		'block' => 0,
		'sendEmail' => 0,
		'registerDate' => $mysqldate,
		'lastVisitDate' => $mysqldate,
		'activation'=> 0,
		'params' => '{"admin_style":"","admin_language":"","language":"","editor":"","helpsite":"","timezone":""}'
		);

		// Try to create user
		if(($id = $model->createJUser($new_user))){

		    // User created now assign to default group
		    $model->assignDefaultGroup($id);

		    // insert poecom BT
		    $user = JFactory::getUser(intval($id));

		    // Get the log in credentials.
		    $credentials = array();
		    $credentials['username'] = $userData['email1'];
		    $credentials['password'] = $pass;

		    // Try to log in
		    if(true === $app->login($credentials, array())){
			// User created and logged in
			// Create new POE-com BT address
			if(($bt_id = $model->createPOEcomAddress($id, $userData))){
			    $this->setShipTo();
			}else{
			    // handle failed address insert
			    $app->enqueueMessage(JText::_('COM_POECOM_ADDRESS_CREATE_ERROR'), 'error');
			    return false;
			} 

		    }else{
			// handle failed address insert
			$app->enqueueMessage(JText::_('COM_POECOM_AUTO_LOGIN_ERROR'), 'error');
			return false;
		    }
		}else{
		    // handle failed address insert
		    $app->enqueueMessage(JText::_('COM_POECOM_AUTO_USER_ERROR'), 'error');
		    return false;
		}
	    }
	}else{
            //user logged and trying to set Billing Address where none exists
            //re-direct to address form
            $url = 'index.php?option=com_poecom&view=address&address_type=BT';
            $app->redirect($url);
        }
    }
    
    /**
     *Set shipping addres view
     */
    public function setShipTo(){
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $jinput->set('address_type', 'ST', 'cmd');
        
        $view = $this->getView('Address', 'html');
        $model = $this->getModel('address');
     
        $view->setModel($model, true);
        
        // Get the document object.
	$document = JFactory::getDocument();
        
        // Push document object into the view.
	$view->assignRef('document', $document);
        
        $view->display();
    }
    
    /**
     * Set registration address (BT)
     * 
     * @return boolean 
     */
    public function setAddress(){
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // check the token
        $check_token = $jinput->get(JUtility::getToken(), null, 'int');
        
	// Check that the token is in a valid format.
	if ($check_token === null || $check_token !== 1) {
	    JError::raiseError(403, JText::_('JINVALID_TOKEN'));

	    return false;
	}
        
        $view = $this->getView('Address', 'html');
        $model = $this->getModel('address');
     
        $view->setModel($model, true);
        
        // Get the document object.
	$document = JFactory::getDocument();
        
        // Push document object into the view.
	$view->assignRef('document', $document);
        
        // Show address form again
        //$valid_address = 0;

        // check the user state
        $user = JFactory::getUser();
        
        if(($id = $user->get('id'))){
            // Get the address data.
	    $address_data = $jinput->get('jform', array(), 'array');
        
           $model = $this->getModel('Registration', 'PoecomModel');
           
           if(($address_id = $model->createPOEcomAddress($id, $address_data))){
                $view->closeModal($address_id);
           }else{
                // show address form again
                $view->display();
           }
        }   
    }
}
