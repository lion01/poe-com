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
     * Get if username available
     */
    public function checkUserName(){
       $view = $this->getView('registration', 'raw');
       $model = $this->getModel('Registration');
       $view->setModel($model, true);
       $view->checkUserName();
    }
}
