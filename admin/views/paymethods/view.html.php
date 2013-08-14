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
 * Payment Methods View
 */
class PoecomViewPayMethods extends JView{
    
    protected $items;
    protected $pagination;
    protected $state;

    /**
    * PayMethods view display method
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
        
        $enable_filter = array();
        $enable_filter[] = array('value' => '', 'text' => JText::_('COM_POECOM_SELECT_ENABLED'));
        $enable_filter[] = array('value' => '0', 'text' => JText::_('COM_POECOM_SELECT_ENABLED_N'));
        $enable_filter[] = array('value' => '1', 'text' => JText::_('COM_POECOM_SELECT_ENABLED_Y'));
        
        $this->assignRef('enable_filter', $enable_filter);

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }
    
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        JLoader::register('PayMethodsHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/paymethods.php');
        
        PayMethodsHelper::addSubmenu('paymethods');
		
        $canDo = PayMethodsHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_PAYMETHOD_LIST_TITLE'), 'paymethods');
        
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('paymethod.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('paymethod.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'paymethods.delete', 'JTOOLBAR_DELETE');
        }
    }
}
