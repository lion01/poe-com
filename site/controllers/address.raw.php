<?php
/**
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class PoecomControllerAddress extends JControllerForm{
    
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
            $view->setModel($model);
          
            $view->getRegions();
        }
    }
}
