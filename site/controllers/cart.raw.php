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
 
/**
 * Shipping Controller
 */
class PoecomControllerCart extends JController{

    
    public function updateShipping(){
        
        $view = $this->getView('Cart', 'raw');
        $view->setModel($this->getModel('Cart'), true);
        
        $view->updateShipping();
    }
    
    public function useCoupon(){
	$view = $this->getView('Cart', 'raw');
        $view->setModel($this->getModel('Cart'), true);
	$view->setModel($this->getModel('Coupon'));
        
        $view->useCoupon();
    }
    
    /**
     * AJAX method to add an item to the cart
     */
    public function ajaxAddItem(){
        $view = $this->getView('Cart', 'raw');
        $view->setModel($this->getModel('Cart'), true);
        $view->setModel($this->getModel('Product'));


        $view->ajaxAddItem();
    }
}
