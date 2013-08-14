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
 * Customers List View
 */
class PoecomViewCustomers extends JView{
    
    protected $items;
    protected $pagination;
    protected $state;
    protected $script;

    /**
    * PayTransactions view display method
    * @return void
    */
    function display($tpl = null){
	
        // Get data from the model and assign to view
        $this->items = $this->get('Items');
        $this->pagination = $pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->script = $this->get('Script');
        // Check for errors.
        if (count($errors = $this->get('Errors'))){
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }
    
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        JLoader::register('CustomersHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/customers.php');
        
        CustomersHelper::addSubmenu('customers');
		
        $canDo = CustomersHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_CUSTOMER_LIST_TITLE'), 'customer');
        
        
        if ($canDo->get('core.edit')){
            JToolBarHelper::editList('customer.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')){
            JToolBarHelper::deleteList('', 'customers.delete', 'JTOOLBAR_DELETE');
        }
    }
}
