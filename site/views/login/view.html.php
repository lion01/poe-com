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

jimport('joomla.application.component.view');

/**
 * Login View
 *
 * @package	Joomla.site
 * @subpackage	com_poecom
 * @since 	1.5
 */
class PoecomViewLogin extends JView{
    /**
    * View form
    *
    * @var		form
    */
   // protected $form = null;
    
    /**
    * display method of Product view
    * @return void
    */
    public function display($tpl = null){
        
        $params = JComponentHelper::getParams('com_poecom');
        $cart_itemid = $params->get('cartitemid', 0, 'int');
        
        $this->assignRef('cart_itemid', $cart_itemid);
	
	//$script = $this->get('Script');
	$script = JURI::root(true).'/components/com_poecom/models/forms/login.js';
	
	$this->assignRef('script', $script);

	// Display the template
	parent::display($tpl); 
    }
}
