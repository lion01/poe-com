<?php

/**
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class PoecomControllerPayment extends JControllerForm {

    public function display() {

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
            $view = $this->getView($vName, $vFormat);
            $model = $this->getModel($vName);
        }

        // Push the model into the view (as default).
        $view->setModel($model, true);

        // select view method
        if (($pay_method = $model->getPaymentMethod($pay_method_id))) {

            $view->assignRef('pay_method', $pay_method);

            switch ($pay_method->type) {
                case '1': // credit card form
                    $view->setLayout('ccform');
                    break;
                case '2': // external host form
                    $view->setLayout('externalform');
                    break;
                case '3': // customer contract
                    $view->setLayout('contractform');
                    break;
                default:
                    $view->setLayout('default');
                    break;
            }
        } else {
            $app->enqueueMessage(JText::_('COM_POECOM_NO_PAY_METHOD_ERROR'), 'error');
        }

        // Push document object into the view.
        $view->assignRef('document', $document);

        $view->display();
    }

}
