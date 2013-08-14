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
 * */
jimport('joomla.application.component.view');

/**
 * OptionSets View
 */
class PoecomViewOptionSets extends JView {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Products view display method
     * @return void
     */
    function display($tpl = null) {
        // Get data from the model and assign to view
        $this->items = $this->get('Items');
        $this->pagination = $pagination = $this->get('Pagination');
        $this->state = $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        // Get the Product List for filter
        JLoader::register('ProductList', JPATH_COMPONENT_ADMINISTRATOR . '/assets/html/productlist.php');

        $product_list = new ProductList();

        $this->product_list = $product_list->getOptions();

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() {
        JLoader::register('OptionSetsHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/optionsets.php');

        OptionSetsHelper::addSubmenu('optionsets');

        $canDo = OptionSetsHelper::getActions();

        JToolBarHelper::title(JText::_('COM_POECOM_MANAGER_OPTIONSETS'), 'optionsets');
        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('optionset.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('optionset.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'optionsets.delete', 'JTOOLBAR_DELETE');
        }
        if ($canDo->get('core.admin')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_poecom');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument() {
        //	$document = JFactory::getDocument();
        //	$document->setTitle(JText::_('COM_POECOM_ADMINISTRATION'));
        //   $document->addStyleDeclaration('.icon-48-products {background-image: url(../media/com_poecom/images/poecom-tree-48.png);}');
    }

}
