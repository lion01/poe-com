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
 * FlyerSections View
 */
class PoecomViewFlyerSections extends JView {

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
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

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
        JLoader::register('FlyerSectionsHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/flyersections.php');

        FlyerSectionsHelper::addSubmenu('flyersections');

        $canDo = FlyerSectionsHelper::getActions();

        JToolBarHelper::title(JText::_('COM_POECOM_MANAGER_FLYER_SECTIONS'), 'flyersections');
        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('flyersection.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('flyersection.edit', 'JTOOLBAR_EDIT');
            JToolBarHelper::divider();
            JToolBarHelper::publishList('flyersections.publish', 'JTOOLBAR_PUBLISH',true);
            JToolBarHelper::unpublishList('flyersections.unpublish', 'JTOOLBAR_UNPUBLISH',true);
            JToolBarHelper::divider();
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'flyersections.delete', 'JTOOLBAR_DELETE');
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
