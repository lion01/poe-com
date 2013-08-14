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
 * HTML View class for the Poecom Component Product Categories
 */
class PoecomViewProductCategories extends JView {

    protected $form;

    // Overwriting JView display method
    public function display($tpl = null) {

        // Assign data to the view
        $this->items = $this->get('Items');
        $this->form = $this->get('Form');
        $this->state = $this->get('State');

        $this->params = $this->state->get('params');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {

            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->prepareDocument();

        // Display the view
        parent::display($tpl);
    }

    /*
      public function find(){
      $task = JRequest::getVar('task', '');
      $postalcode = JRequest::getVar('postalcode', '');
      $lat = JRequest::getVar('latitude', '');
      $lon = JRequest::getVar('longitude', '');

      // Assign data to the view
      $items = $this->get('Items');

      // Create new array ordered by distance
      $location = new JObject;
      $location->latitude = $lat;
      $location->longitude = $lon;

      $data = array('locations' => $items, 'location' => $location);

      JPluginHelper::importPlugin('exps', 'distance');

      $dispatcher = JDispatcher::getInstance();

      $locations = $dispatcher->trigger('getLocationsList', $data);

      $this->items = $locations[0];
      $this->form	= $this->get('Form');
      $this->state	= $this->get('State');
      $this->params	= $this->state->get('params');

      $this->prepareDocument();

      // Display the view
      parent::display($tpl);

      }
     */

    /**
     * Prepares the document.
     *
     * @since	1.6
     */
    protected function prepareDocument() {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_POECOM_PRODUCTS'));
        }

        $title = $this->params->get('page_title', '');
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }

}

