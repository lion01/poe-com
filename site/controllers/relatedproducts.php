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
 * Related Products Controller
 */
class PoecomControllerRelatedProducts extends JController {

    public function display($cachable = false, $urlparams = false) {
        $app = JFactory::getApplication();
        $jinput = $app->input;

        $document = JFactory::getDocument();
        //get view parameters
        $viewType = $document->getType();
        $viewName = $jinput->get('view', 'relatedproducts', 'CMD');
        $viewLayout = $jinput->get('layout', 'default', 'CMD');

        // Set the view              
        $jinput->set('view', $viewName);
        $jinput->set('layout', $viewLayout);

        // Get the view object
        $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

        // Get/Create the default model
        if (($model = $this->getModel($viewName))) {
            // Push the model into the view (as default)
            $view->setModel($model, true);
        }

        $view->assignRef('document', $document);

        // Display the view - no caching in admin
        $view->display();

        return $this;
    }
}
