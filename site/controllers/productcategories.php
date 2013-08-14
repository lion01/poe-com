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
jimport('joomla.application.component.controlleradmin');

/**
 * Products Controller
 */
class PoecomControllerProductCategories extends JController {

    public function find() {

        $view = $this->getView('ProductCategories', 'html');

        $view->setModel($this->getModel('ProductCategories'), true);

        // Get the document object.
        $document = JFactory::getDocument();

        // Push document object into the view.
        $view->assignRef('document', $document);

        $view->find();
    }
}
