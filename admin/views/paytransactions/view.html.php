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
 * Payment Transaction List View
 */
class PoecomViewPayTransactions extends JView{
    
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
        
        // Set transaction type filter
        $model = $this->getModel('PayTransactionType');
        $type_list = $model->getList();
        
        $this->assignRef('type_list', $type_list);
        
        // Set transaction status filter
        $model = $this->getModel('PayTransactionStatus');
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
        JLoader::register('PayTransactionsHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/paytransactions.php');
        
        PayTransactionsHelper::addSubmenu('paytransactions');
		
        $canDo = PayTransactionsHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_PAYTRANSACTION_LIST_TITLE'), 'paytransactions');
        /* Currently not allowed
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('paytransaction.add', 'JTOOLBAR_NEW');
        } */
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('paytransaction.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'paytransactions.delete', 'JTOOLBAR_DELETE');
        }
    }
}
