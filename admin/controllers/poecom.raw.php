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
jimport('joomla.application.component.controller');

class PoecomControllerPoecom extends JController{
   public function routeJSUrl(){
       $view = $this->getView('poecom', 'raw');
       $view->routeJSUrl();
   }
   
   /**
    * AJAX call handler - set full path for window.location.replace
    */
   public function fullPathJSUrl(){
       $view = $this->getView('poecom', 'raw');
       $view->fullPathJSUrl();
   }
}
