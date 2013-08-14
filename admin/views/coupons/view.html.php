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
 * Coupons List View
 */
class PoecomViewCoupons extends JView{
    
    protected $items;
    protected $pagination;
    protected $state;

    /**
    * PayTransactions view display method
    * @return void
    */
    function display($tpl = null){
	
        // Get data from the model and assign to view
        $this->items = $this->get('Items');
        $this->pagination = $pagination = $this->get('Pagination');
        $this->state = $this->get('State');
	
        // Check for errors.
        if (count($errors = $this->get('Errors'))){
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
	
	// Set promotion filter list
        $model = $this->getModel('Promotions');
        $promotion_list = $model->getPromotionsList();
        
        $this->assignRef('promotion_list', $promotion_list);
        
        // Set status filter list
	$model = $this->getModel('Coupons');
        $status_list = $model->getCouponStatusList();
        
        $this->assignRef('status_list', $status_list);

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }
    
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        JLoader::register('CouponsHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/coupons.php');
        
        CouponsHelper::addSubmenu('coupons');
		
        $canDo = CouponsHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_COUPON_LIST_TITLE'), 'coupon');
	
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('coupon.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('coupon.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'coupons.delete', 'JTOOLBAR_DELETE');
        }
    }
}
