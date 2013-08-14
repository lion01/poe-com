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
 * Order List View
 */
class PoecomViewOrders extends JView{
    
    protected $items;
    protected $pagination;
    protected $state;

    /**
    * Orders view display method
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
        
        $model = $this->getModel('OrderStatus');
        $status_list = $model->getList();
        
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
        JLoader::register('OrdersHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/orders.php');
        
        OrdersHelper::addSubmenu('orders');
		
        $canDo = OrdersHelper::getActions();
       
        JToolBarHelper::title(JText::_('COM_POECOM_ORDER_LIST_TITLE'), 'orders');
      /* TODO: add new order  
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('order.add', 'JTOOLBAR_NEW');
        } */
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('order.edit', 'JTOOLBAR_EDIT');
        }
        
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'orders.delete', 'JTOOLBAR_DELETE');
        }
    }
}
