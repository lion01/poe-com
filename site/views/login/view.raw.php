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

jimport('joomla.application.component.view');

/**
 * Login View
 *
 * @package	Joomla.site
 * @subpackage	com_poecom
 * @since 	1.5
 */
class PoecomViewLogin extends JView{
    /**
    * View form
    *
    * @var		form
    */
   // protected $form = null;
    
    /**
    * display method of Product view
    * @return void
    */
    public function display($tpl = null){
	// Display the template
	parent::display($tpl); 
    }

    /**
     * Try login 
     */
    public function tryLogin(){
     
	$response = array("valid" => 0, "errormsg" => "");
	$app = JFactory::getApplication();
        $jinput = $app->input;
	
	//check the token
	$token_sent = $jinput->get('jtokenname', '', 'STRING');
	    
	$token = JUtility::getToken();

	if($token_sent != $token){
	    die(JText::_('COM_POECOM_NEED_TOKEN_LOGIN'));
	}
	
	$jsess = JFactory::getSession();
	$login_attempts = $jsess->get('login_attempt', 0, 'poecom');
	
	$login_attempts++;
	
	$jsess->set('login_attempt', $login_attempts, 'poecom');
		
	// Get the log in credentials.
	$credentials = array();
	$credentials['username'] = $jinput->get('username', '', 'STRING');
	$credentials['password'] = $jinput->get('password', '', 'STRING');
     
	// Try to log in
	if(true === $app->login($credentials, array())){
	    // User logged in
	    $response["valid"] = '1';
	    $jsess->set('login_attempt', 0, 'poecom');
            
            // Destroy any other session instances that could occur if user login
            // used in more than one browser
            $session_id = $jsess->getId();
            $user = JFactory::getUser();
            $model = $this->getModel();
            $model->clearSessions($session_id, $user->id);
            
	}else{
	    $params = JComponentHelper::getParams('com_poecom');
	    $max = $params->get('maxlogin', 3);
	    
	    // handle failed login attempt
	    if($login_attempts > $max){
		$response["errormsg"] = JText::_('COM_POECOM_LOGIN_MAX_ATTEMPT_MSG');
		//$jsess->clear('cart', 'poecom');
		//$jsess->clear('p_info', 'poecom');
		$jsess->destroy();
		JUtility::getToken(true);
	    }else{
		
		$response["errormsg"] = JText::_('COM_POECOM_AUTO_LOGIN_ERROR');
	    }
	    
	}
	$json_response = json_encode($response);
	
	echo $json_response;
    }
}
