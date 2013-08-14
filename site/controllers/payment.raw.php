<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Payment
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0
 *
 * http://www.exps.ca
**/
jimport('joomla.application.component.controllerform');

class PoecomControllerPayment extends JControllerForm{
    
    public function process(){
       
        // Get the document object.
        $document = JFactory::getDocument();
        
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        // Get the payment method id
        $pay_method_id = $jinput->get('pay_method_id', 0, 'cmd');
      
        // Set the default view name and format from the Request.
        $vName = $jinput->get('view', 'Payment', 'cmd');
        $vFormat = $document->getType();

        if (($view = $this->getView($vName, $vFormat))) {
            $model = $this->getModel($vName);
        }
        
        // Push the model into the view (as default).
        $view->setModel($model, true);
  
         // Push document object into the view.
        $view->assignRef('document', $document);
        
        // select view method
        if(($pay_method = $model->getPaymentMethod($pay_method_id))){
            
            $view->assignRef('pay_method', $pay_method);
           
            switch($pay_method->type){
                case '1': // credit card
                    $payTxnModel = $this->getModel('PaymentTransaction');
                    $view->setModel($payTxnModel);
                    $view->handleAPIDirectRequest();
                    break;
                case '2': // external host form
                    $requestModel = $this->getModel('Request');
                    $view->setModel($requestModel);
                    $view->setLayout('externalform');
                    
                    $view->processExternalForm();
                    break;
                case '3': // pre-order
                    $requestModel = $this->getModel('Request');
                    $view->setModel($requestModel);
                    $view->preorder();
                    break;
                case '4': // account
                    $orderModel = $this->getModel('Order');
                    $view->setModel($orderModel, true);
                    $view->onAccount();
                    break;
                default:
                    $view->setLayout('default');
                    	
                    $view->display();
                    break;
            } 
        }else{
            $app->enqueueMessage(JText::_('COM_POECOM_NO_PAY_METHOD_ERROR'), 'error');
        }
    }
    
    /**
     * Check the payment status
     *  
     */
    public function getPaymentStatus(){
        $document = JFactory::getDocument();
        $app = JFactory::getApplication();
        $jinput = $app->input;
        
        $vName = $jinput->get('view', 'Payment', 'cmd');
        $vFormat = $document->getType();
        
        $view = $this->getView($vName, $vFormat);
        $view->setModel($this->getModel('Payment'),true);
        $view->setModel($this->getModel('Request'));
        
        $view->getPaymentStatus();
    }
}
