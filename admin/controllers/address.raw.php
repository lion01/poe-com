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
jimport('joomla.application.component.controllerform');

class PoecomControllerAddress extends JControllerForm{
    
    public function update(){
        
        // Get the document object.
        $document	= JFactory::getDocument();
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // check the token
        $jtoken = $jinput->get('jtoken_name', null, 'string');
        $jtoken_val = $jinput->get('jtoken_value', null, 'int');
        
        $token = JUtility::getToken();
        
        // Check that the token is in a valid format.
        if ($jtoken != $token || $jtoken_val !== 1) {
            JError::raiseError(403, JText::_('JINVALID_TOKEN'));
            return false;
        }
        

      
		// Set the default view name and format from the Request.
        $vName = $jinput->get('view', 'Address', 'cmd');
        $vFormat = $document->getType();
     
        if ($view = $this->getView($vName, $vFormat)) {
            $view = $this->getView($vName, $vFormat);
            $model = $this->getModel($vName);
        }
        
        // Push the model into the view (as default).
        $view->setModel($model, true);
  
         // Push document object into the view.
        $view->assignRef('document', $document);
        
        $view->updateAddress();
    }
    
    
    /**
     * display the edit form
     * @return void
     */
    function getRegions(){
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // Set the default view name and format from the Request.
        //$vName	 = JRequest::getCmd('view', 'ProductPrice');
        $vName = $jinput->get('view', 'Address', 'cmd');
        $vFormat = 'raw';
        
        if (($view = $this->getView($vName, $vFormat))) {
            $model = $this->getModel('Countries');
            
            $view->setModel($model, true);
          
            $view->getRegions();
        }
    }
}
