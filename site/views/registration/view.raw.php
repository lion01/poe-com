<?php defined('_JEXEC') or die('Restricted access');
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
 * Registration view
 *
 */
class PoecomViewRegistration extends JView{
    /**
     * Check if username is available
     */
    public function checkUserName(){
        $response = new JObject();
        $response->found = 0;
        
	$app = JFactory::getApplication();
        $jinput = $app->input;
        
        $username = $jinput->get('username', '', 'string');
        
        if(!strlen($username)){
            $response->found = 1; 
            $response->msg = JText::_('COM_POECOM_NO_USERNAME_ERROR');
        }else{
            $lmodel = $this->getModel('Registration');
            $found = $lmodel->usernameExists($username);
            
            if($found){
                $response->found = 1;
                $response->msg = JText::_('COM_POECOM_USERNAME_FOUND_ERROR');
            }
        }
        
        $json_response = json_encode($response);
        
        echo $json_response;
    }
}
