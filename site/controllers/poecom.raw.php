<?php
/**
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class PoecomControllerPoecom extends JController{
   public function routeJSUrl(){
       $view = $this->getView('poecom', 'raw');
       //$view->setModel($this->getModel('Login'), true);
       $view->routeJSUrl();
   }
   
   /**
    * AJAX call handler - set full path for window.location.replace
    */
   public function fullPathJSUrl(){
       $view = $this->getView('poecom', 'raw');
       //$view->setModel($this->getModel('Login'), true);
       $view->fullPathJSUrl();
   }
}
