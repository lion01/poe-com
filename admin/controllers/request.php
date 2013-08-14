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
jimport('joomla.application.component.controllerform');
 
/**
 * RFQ Controller
 */
class PoecomControllerRequest extends JControllerForm{
    // no over rides
    
    /**
     * Generate Order from an RFQ
     */
    public function generateOrder(){
        
        $document = JFactory::getDocument();
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $convert_rfq_id = $jinput->get('id', 0, 'int');
        
        // Set the default view name and format from the Request.
        $vName = $jinput->get('view', 'Request', 'cmd');
        $vFormat = $document->getType();
     
        if (($view = $this->getView($vName, $vFormat))) {
            $view = $this->getView($vName, $vFormat);
            $model = $this->getModel('Request');
        }
        
        // Push the model into the view (as default).
        $view->setModel($model, true);
        $view->setLayout('edit');
  
         // Push document object into the view.
        $view->assignRef('document', $document);
        $view->assignRef('convert_rfq_id', $convert_rfq_id);
        $view->assignRef('id', $convert_rfq_id);
        
        $view->display();
    }
}
