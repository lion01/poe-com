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
 * Base controller class for Poecom
 * 
 * @package	Joomla.Site
 * @subpackage	com_poecom
 * @since		1.7
 */
class PoecomControllerProductPrice extends JController{
    
    public function display($cachable = false, $urlparams = false){
        parent::display();
    }
    
    public function getProductPrice(){
        $jinput = JFactory::getApplication()->input;
        $viewName = $jinput->get('view', 'product', 'CMD');
        
        if (($view = $this->getView($viewName, 'raw'))) {
            if (($optionsModel = $this->getModel('Options'))) {
                    $view->setModel($optionsModel);
                }
            if (($productModel = $this->getModel('Product'))) {
                $view->setModel($productModel);
            }
            
            $view->getProductPrice();
        }
    }
}
