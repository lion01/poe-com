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
 * Product Options View
 */
class PoecomViewOptionValues extends JView{
    
    protected $items;
    protected $pagination;
    protected $state;

    /**
    * Products view display method
    * @return void
    */
    function display($tpl = null){
        // Get data from the model and assign to view
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
 
        // Check for errors.
        if (count($errors = $this->get('Errors'))){
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        
        // Get the Product List for filter
        JLoader::register('ProductList', JPATH_COMPONENT_ADMINISTRATOR.'/assets/html/productlist.php'); 
        
        $product_list = new ProductList();
        
        $this->product_list = $product_list->getOptions();
		
        // Set the toolbar
        $this->addToolBar();
 
        // Display the template
        parent::display($tpl);
        
    }
    
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        JLoader::register('OptionValuesHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/optionvalues.php');
        
        OptionValuesHelper::addSubmenu('optionvalues');
		
        $canDo = OptionValuesHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_MANAGER_OPTION_VALUES'), 'optionvalues');
        
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('optionvalue.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('optionvalue.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'optionvalues.delete', 'JTOOLBAR_DELETE');
        }
        if ($canDo->get('core.admin')){
                JToolBarHelper::divider();
                JToolBarHelper::preferences('com_poecom');
        }
    }
}
