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
class PoecomControllerProducts extends JController {

    public function find() {

        $view = $this->getView('Products', 'html');
        $model = $this->getModel('products');

        $view->setModel($model, true);

        // Get the document object.
        $document = JFactory::getDocument();

        // Push document object into the view.
        $view->assignRef('document', $document);

        $view->find();
    }

    public function displayTopProducts() {
        $view = $this->getView('Products', 'html');
        $model = $this->getModel('products');

        $view->setModel($model, true);

        // Get the document object.
        $document = JFactory::getDocument();

        // Push document object into the view.
        $view->assignRef('document', $document);

        $view->displayTopProducts();
    }

}
