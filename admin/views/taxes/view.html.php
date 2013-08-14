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
 * Taxes List View
 */
class PoecomViewTaxes extends JView{
    
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
        JLoader::register('TaxesHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/taxes.php');
        
        TaxesHelper::addSubmenu('taxes');
		
        $canDo = TaxesHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_TAX_LIST_TITLE'), 'tax');
        
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('tax.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')){
            JToolBarHelper::divider();
            JToolBarHelper::publishList();
            JToolBarHelper::unpublishList();
            JToolBarHelper::divider();
                JToolBarHelper::editList('tax.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'taxes.delete', 'JTOOLBAR_DELETE');
        }
    }
}
