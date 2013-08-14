<?php
/**
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class PoecomControllerRequest extends JController{
    
    public function display($cachable = false, $urlparams = false){
        $app = JFactory::getApplication();
        $jinput = $app->input;

        $document = JFactory::getDocument();
        //get view parameters
        $viewType = $document->getType();
        $viewName = $jinput->get('view', 'Request', 'CMD');
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
            
            //add additional models
            if (($orderModel = $this->getModel('Order'))) {
                $view->setModel($orderModel);
            }
            if (($payTxnModel = $this->getModel('PaymentTransaction'))) {
                $view->setModel($payTxnModel);
            }
            if (($addressModel = $this->getModel('Address'))) {
                $view->setModel($addressModel);
            }
            if (($paymentModel = $this->getModel('Payment'))) {
                $view->setModel($paymentModel);
            }
            if (($locationModel = $this->getModel('Location'))) {
                $view->setModel($locationModel);
            }
        }
        
        // Push document object into the view.
        $view->assignRef('document', $document);
		
        $view->display();
    }
    /**
     * Display order acknowledgment
     */
    public function displayOrder(){
        $view = $this->getView('Request', 'html');
       
        // Push the model into the view (as default).
		if (($orderModel = $this->getModel('Order'))) {
			$view->setModel($orderModel, true);
		}
        
		if (($addressModel = $this->getModel('Address'))) {
			$view->setModel($addressModel);
		}
        
		if (($paymentModel = $this->getModel('Payment'))) {
			$view->setModel($paymentModel);
		}
        
		if (($locationModel = $this->getModel('Location'))) {
			$view->setModel($locationModel);
		}
        
		if (($productModel = $this->getModel('Product'))) {
			$view->setModel($productModel);
		}
        
		if (($payTxnModel = $this->getModel('PaymentTransaction'))) {
			$view->setModel($payTxnModel);
		}
        
        $view->displayOrder();
    }
    
    /**
     * Print view for RFQ / Order confirmation display
     */
    public function printView(){
     
        $view = $this->getView('Request', 'html');
        $view->setLayout('printview');
        
		 // Push the model into the view (as default).
		if (($requestModel = $this->getModel('Request'))) {
			$view->setModel($requestModel, true);
		}
		if (($orderModel = $this->getModel('Order'))) {
			$view->setModel($orderModel, true);
		}
        
		if (($addressModel = $this->getModel('Address'))) {
			$view->setModel($addressModel);
		}
        
		if (($paymentModel = $this->getModel('Payment'))) {
			$view->setModel($paymentModel);
		}
        
		if (($locationModel = $this->getModel('Location'))) {
			$view->setModel($locationModel);
		}
        
		if (($payTxnModel = $this->getModel('PaymentTransaction'))) {
			$view->setModel($payTxnModel);
		}

        $view->printView();
    }
}
?>
