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
   
    public function updateProfile(){
        
        $view = $this->getView('Account', 'raw');
        $view->setModel($this->getModel('Account'));
          
        $view->updateProfile();
       
    }
    
    public function updateAddress(){
        
        $view = $this->getView('Account', 'raw');
        $view->setModel($this->getModel('Account'));
          
        $view->updateAddress();
       
    }
}
