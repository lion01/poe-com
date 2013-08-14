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
 * Products View
 */
class PoecomViewProducts extends JView{
    protected $items;
    protected $pagination;
    protected $state;
    /**
    * Products view display method
    * @return void
    */
    function display($tpl = null){
        // Get data from the model
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

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
        JLoader::register('ProductsHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/products.php');
        
        ProductsHelper::addSubmenu('products');
		
        $canDo = ProductsHelper::getActions();
       
        JToolBarHelper::title(JText::_('COM_POECOM_MANAGER_PRODUCTS'), 'products');
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('product.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('product.edit', 'JTOOLBAR_EDIT');
                JToolBarHelper::divider();
                JToolBarHelper::publishList('products.publish', 'JTOOLBAR_PUBLISH',true);
                JToolBarHelper::unpublishList('products.unpublish', 'JTOOLBAR_UNPUBLISH',true);
                JToolBarHelper::divider();
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'products.delete', 'JTOOLBAR_DELETE');
        }
        if ($canDo->get('core.admin')){
                JToolBarHelper::divider();
                JToolBarHelper::preferences('com_poecom');
        }
    }
}
