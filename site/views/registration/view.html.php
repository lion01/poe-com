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
    
    protected $data;
    protected $form;
    protected $params;
    protected $state;

    /**
    * Method to display the view.
    *
    * @param	string	The template file to include
    * @since	1.6
    */
    public function display($tpl = null){
	
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $ItemId = $jinput->get('ItemId', 0, 'int');

        $this->assignRef('ItemId', $ItemId);
        
        $params = JComponentHelper::getParams('com_poecom');
        $enforce_cc_address = $params->get('enforceccaddress', 0, 'INT');

        $this->assignRef('enforce_cc_address', $enforce_cc_address);
        
        // Get the view data.
        $this->data	= $this->get('Data');
        $this->form	= $this->get('Form');
        $this->state	= $this->get('State');
        $this->params	= $this->state->get('params');
        $script = $this->get('Script');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->assignRef('script', $script);

        $this->setDocument();

        parent::display($tpl);
    }
    
    public function userExists($tpl = null){
	
	$script = JURI::root(true).'/components/com_poecom/models/forms/login.js';
	
	$this->assignRef('script', $script);
	
	parent::display($tpl);
    }
    
    /**
    * Method to set up the document properties
    *
    * @return void
    */
    protected function setDocument(){
        JText::script('COM_POECOM_EMAIL_REGISTERED');
        JText::script('COM_POECOM_CLOSE');
		JText::script('COM_POECOM_EMAIL_MATCH');
		JText::script('COM_POECOM_EMAIL_NO_MATCH');
    }
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
        }else{
            $lmodel = $this->getModel('Registration');
            $found = $lmodel->usernameExists($username);
            
            if($found){
                $response->found = 1;
            }
        }
        
        $json_response = json_encode($response);
        
        echo $json_response;
    }
}
