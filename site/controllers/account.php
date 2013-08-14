<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Account Controller
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 4:21:36 PM
 *
 * http://www.exps.ca
**/
jimport('joomla.application.component.controllerform');

class PoecomControllerAccount extends JControllerForm {

    public function display(){
        
        // Get the document object.
        $document = JFactory::getDocument();
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // Set the default view name and format from the Request.
        $vName = $jinput->get('view', 'Account', 'cmd');
        $vFormat = $document->getType();
    
        if ($view = $this->getView($vName, $vFormat)) {
            $view = $this->getView($vName, $vFormat);
            $model = $this->getModel($vName);
        }
        
        // Push the model into the view (as default).
        $view->setModel($model, true);
        //$view->setModel($this->getModel('Order'));
        
        // Push document object into the view.
        $view->assignRef('document', $document);
		
        $view->display();
    }
}
