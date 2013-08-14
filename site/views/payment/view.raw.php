<?php

defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Payment Raw
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 4:24:14 PM
 *
 * http://www.exps.ca
 * */
jimport('joomla.application.component.view');
jimport('joomla.utilities.date');

/**
 * Payment view class for AJAX requests
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class PoecomViewPayment extends JView {

    function processExternalForm() {

        $app = JFactory::getApplication();

        $jsess = JFactory::getSession();
        $cart = $jsess->get('cart', null, 'poecom');

        $user = JFactory::getUser();

        $today = new JDate();

        $model = $this->getModel('Request');

        $rfq_id = '';

        // Check if RFQ previously created
        if (!empty($cart['rfq_number'])) {
            $rfq_number = $cart['rfq_number'];

            // get the record id
            if (($id = $model->getId($rfq_number))) {
                $rfq_id = $id;
            }
        } else {
            $rfq_number = 'RFQ_' . $today->toUnix() . '_' . rand(1, 1000);
        }

        if(!empty($cart['user_st'])) {
            $shipping_id = $cart['user_st']->id;
        } else {
            $shipping_id = 0;
        }

        if(!empty($cart['selected_shipping'])) {
            $selected_shipping = json_encode($cart['selected_shipping']);
        } else {
            $selected_shipping = '';
        }
        
        if(!empty($cart->discount)) {
            $coupon_id = $cart['discount']->coupon_id;
        } else {
            $coupon_id = 0;
        }

        // prepare update array
        $rfq = array('id' => $rfq_id,
            'number' => $rfq_number,
            'status_id' => 1,
            'order_id' => 0,
            'request_date' => $today->toMySQL(true),
            'total' => number_format(floatval($cart['total']), 2),
            'currency_id' => $cart['currency'][0]->id,
            'juser_id' => $user->id,
            'cart' => json_encode($cart),
            'subtotal' => number_format(floatval($cart['subtotal']), 2),
            'coupon_id' => $coupon_id,
            'total_discount' => number_format(floatval($cart['discount_amount']), 2),
            'product_tax' => number_format(floatval($cart['product_tax']), 2),
            'shipping_cost' => number_format(floatval($cart['shipping_cost']), 2),
            'shipping_tax' => number_format(floatval($cart['shipping_tax']), 2),
            'selected_shipping' => $selected_shipping,
            'billing_id' => $cart['user_bt']->id,
            'shipping_id' => $shipping_id,
            'ip_address' => $cart['ip_address'],
            'email_sent' => 0);

        if (!$model->save($rfq)) {
            $app->enqueueMessage(JText::_('COM_POECOM_RFQ_INSERT_ERROR'), 'error');

            return false;
        } else {
            // set the rfq in cart 
            $cart['rfq_number'] = $rfq_number;
            $cart['entrystatus'] = 'rfq';

            $jsess->set('cart', $cart, 'poecom');
        }

        $plugin = $this->pay_method->plugin;

        // Format data array for plugin
        $data = array('cart' => $cart);

        if (strlen($plugin)) {

            $enabled = JPluginHelper::isEnabled('poecompay', $plugin);

            if ($enabled) {

                $request = 'send' . $plugin . 'Request';

                // Fire sendRequest to open extneral form
                $dispatcher = JDispatcher::getInstance();
                JPluginHelper::importPlugin('poecompay', $plugin);
                $dispatcher->register($request, 'plgPoecomPay' . $plugin);
                $result = $dispatcher->trigger($request, $data);

                // exepect $result[0] to be an array
                if ($result[0]) {
                    $url = $result[0]['url'];
                    $query = $result[0]['query'];
                    $pay_type = $this->pay_method->type;
                }

                $response = array('request_number' => $rfq_number, 'url' => $url, 'query' => ($query), 'pay_type' => $pay_type);

                $json_response = json_encode($response);

                echo $json_response;

                //$this->assignRef('url', urlencode($result[0]));
            } else {
                $app->enqueueMessage(JText::_('COM_POECOM_PLUGIN_NOT_ENABLED_ERROR'), 'error');

                echo '';
            }
        } else {
            echo '';
        }
    }

    /**
     * Create/Update order without RFQ
     * 
     * 
     * @param int $order_id
     * @param int $payment_id Payment transaction id
     * @param int $payment_status Current status of the pyament transaction
     * 
     * @return mixed boolean false or order_id  
     */
    public function createOrderWithoutRFQ($order_id = 0, $payment_id = 0, $payment_status = 1, $pay_method_id = 0) {

        $jsess = JFactory::getSession();
        $cart = $jsess->get('cart', '', 'poecom');

        $today = new JDate();

        if ($cart['user_st']) {
            $shipping_id = $cart['user_st']->id;
        } else {
            $shipping_id = '';
        }

        if ($cart['selected_shipping']) {
            $selected_shipping = json_encode($cart['selected_shipping']);
        } else {
            $selected_shipping = '';
        }

        //set order status
        switch ($payment_status) {
            case '1': //pending
                $order_status = 2; //invoiced
                break;
            case '2': //complete
                $order_status = 3; //paided
                break;
            default:
                //any other payment status 
                $order_status = 1; //open
                break;
        }

        $model = JModel::getInstance('Order', 'PoecomModel');


        if ($cart->discount) {
            $coupon_id = $cart['discount']->coupon_id;
        } else {
            $coupon_id = 0;
        }

        $juser = JFactory::getUser();

        //build order data, order lines will be inserted in store()
        $data = array(
            'id' => $order_id,
            'status_id' => $order_status,
            'rfq_id' => 0,
            'pay_method_id' => $pay_method_id,
            'payment_id' => $payment_id,
            'order_date' => $today->toMySQL(true),
            'juser_id' => $juser->id,
            'billing_id' => $cart['user_bt']->id,
            'shipping_id' => $shipping_id,
            'selected_shipping' => $selected_shipping,
            'subtotal' => $cart['subtotal'],
            'coupon_id' => $coupon_id,
            'total_discount' => $cart['discount_amount'],
            'product_tax' => $cart['product_tax'],
            'shipping_cost' => $cart['shipping_cost'],
            'shipping_tax' => $cart['shipping_tax'],
            'total' => $cart['total'],
            'currency_id' => $cart['currency'][0]->id,
            'ip_address' => $cart['ip_address'],
            'email_sent' => 0
        );

        if ($model->save($data)) {
            if(!$order_id > 0){
                $order_id = $model->getUserLastOrderId($juser->id);
            }
            
            if ($payment_id > 0) {
                //update payment transaction
                $model = JModel::getInstance('PaymentTransaction', 'PoecomModel');
                if (!$model->updateTransaction($payment_id, $order_id)) {
                    $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_PAY_TRANS_UPDATE_ERROR'), 'poecom');
                    $jsess->set('appMsgType', 'error', 'poecom');
                    return false;
                }
            }

            if ($order_id > 0 && $cart['items']) {
                $model = JModel::getInstance('OrderLine', 'PoecomModel');
                foreach ($cart['items'] as $itm) {
                    if ($itm->selected_options) {
                        $selected_options = json_encode($itm->selected_options);
                    } else {
                        $selected_options = '';
                    }

                    $data = array(
                        'id' => '',
                        'order_id' => $order_id,
                        'product_id' => $itm->product_id,
                        'selected_options' => $selected_options,
                        'quantity' => $itm->quantity,
                        'price' => $itm->price,
                        'product_tax' => $itm->tax,
                        'total' => $itm->total,
                        'juser_id' => $juser->id //access control
                    );

                    if (!$model->save($data)) {
                        $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_LINES_INSERT_ERROR'), 'poecom');
                        $jsess->set('appMsgType', 'error', 'poecom');
                        return false;
                    }
                }
            } else {
                $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_NO_LINES_ERROR'), 'poecom');
                $jsess->set('appMsgType', 'error', 'poecom');
                return false;
            }

            $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_INSERTED'), 'poecom');
            $jsess->set('appMsgType', 'message', 'poecom');

            //clear the cart
            $jsess->clear('cart', 'poecom');
            $jsess->clear('shipping', 'poecom');

            return $order_id;
        } else {
            $jsess->set('appMsg', JText::_('COM_POECOM_ORDER_INSERT_ERROR'), 'poecom');
            $jsess->set('appMsgType', 'error', 'poecom');
            return false;
        }
    }

    /**
     * Create an order for customers that pay on an account statement where payment
     * is handled outside the checkout process.
     * 
     * @return echo json encoded response
     */
    public function onAccount() {
        $app = JFactory::getApplication();

        $jsess = JFactory::getSession();
        $cart = $jsess->get('cart', null, 'poecom');

        // Check if Order ID previously created
        if ($cart['order_id'] > 0) {
            $order_id = $cart['order_id'];
        } else {
            $order_id = '';
        }
        //insert order and clear cart
        $order_id = $this->createOrderWithoutRFQ($order_id, 0, 1, $this->pay_method->id);
    
        if (!$order_id > 0) {
            $app->enqueueMessage(JText::_('COM_POECOM_RFQ_INSERT_ERROR'), 'error');

            return false;
        } else {
          $oModel = $this->getModel('Order');
          $payment_status = $oModel->getPaymentStatus($order_id);
          
            // set the rfq in cart 
            $cart['rfq_number'] = '';
            $cart['order_id'] = $order_id;
            $cart['entrystatus'] = 'order';
            $cart['pay_method'] = $this->pay_method->id;
            $cart['payment_status_id'] = $payment_status;
            $jsess->set('order', $cart, 'poecom');
        }

        $plugin = $this->pay_method->plugin;

        // Format data array for plugin
        $data = array('cart' => $cart);
        if (!empty($plugin)) {

            $enabled = JPluginHelper::isEnabled('poecompay', $plugin);

            if ($enabled) {
                $request = 'send' . $plugin . 'Request';

                // Fire sendRequest to open extneral form
                $dispatcher = JDispatcher::getInstance();
                JPluginHelper::importPlugin('poecompay', $plugin);
                $dispatcher->register($request, 'plgPoecomPay' . $plugin);
                $result = $dispatcher->trigger($request, $data);

                // exepect $result[0] to be an array
                if ($result[0]) {
                    $pay_type = $this->pay_method->type;
                    $msg = $result[0]['message'];
                }

                $response = array('order_id' => $order_id,'payment_status_id' =>$payment_status, 'pay_type' => $pay_type, 'message' => $msg);

                $json_response = json_encode($response);

                echo $json_response;
            } else {
                $app->enqueueMessage(JText::_('COM_POECOM_PLUGIN_NOT_ENABLED_ERROR'), 'error');

                echo '';
            }
        } else {
            echo '';
        }
    }

    /**
     * Create a Pre-Order (request) that stops processing at the request stage
     * Actual order are then created via the Admin by converting RFQ to Order
     * 
     * @return boolean 
     */
    public function preorder() {

        $app = JFactory::getApplication();

        $jsess = JFactory::getSession();
        $cart = $jsess->get('cart', null, 'poecom');

        $user = JFactory::getUser();

        $today = new JDate();

        $model = $this->getModel('Request');

        $rfq_id = '';

        // Check if RFQ previously created
        if (!empty($cart['rfq_number'])) {
            $rfq_number = $cart['rfq_number'];

            // get the record id
            if (($id = $model->getId($rfq_number))) {
                $rfq_id = $id;
            }
        } else {
            $rfq_number = 'RFQ_' . $today->toUnix() . '_' . $user->id;
        }
        
        if(!empty($cart['user_st'])) {
            $shipping_id = $cart['user_st']->id;
        } else {
            $shipping_id = 0;
        }

        if(!empty($cart['selected_shipping'])) {
            $selected_shipping = json_encode($cart['selected_shipping']);
        } else {
            $selected_shipping = '';
        }
        
        if(!empty($cart->discount)) {
            $coupon_id = $cart['discount']->coupon_id;
        } else {
            $coupon_id = 0;
        }

        // prepare update array
        $rfq = array('id' => $rfq_id,
            'number' => $rfq_number,
            'status_id' => 1,
            'order_id' => 0,
            'request_date' => $today->toMySQL(true),
            'total' => number_format(floatval($cart['total']), 2),
            'currency_id' => $cart['currency'][0]->id,
            'juser_id' => $user->id,
            'cart' => json_encode($cart),
            'subtotal' => number_format(floatval($cart['subtotal']), 2),
            'coupon_id' => $coupon_id,
            'total_discount' => number_format(floatval($cart['discount_amount']), 2),
            'product_tax' => number_format(floatval($cart['product_tax']), 2),
            'shipping_cost' => number_format(floatval($cart['shipping_cost']), 2),
            'shipping_tax' => number_format(floatval($cart['shipping_tax']), 2),
            'selected_shipping' => $selected_shipping,
            'billing_id' => $cart['user_bt']->id,
            'shipping_id' => $shipping_id,
            'ip_address' => $cart['ip_address'],
            'email_sent' => 0);

        if (!$model->save($rfq)) {
            $app->enqueueMessage(JText::_('COM_POECOM_RFQ_INSERT_ERROR'), 'error');

            return false;
        } else {
            // set the rfq in cart 
            $cart['rfq_number'] = $rfq_number;
            $cart['entrystatus'] = 'rfq';
            $cart['pay_method'] = $this->pay_method->id;
            $jsess->set('cart', $cart, 'poecom');
        }

        $plugin = $this->pay_method->plugin;

        // Format data array for plugin
        $data = array('cart' => $cart);

        if (strlen($plugin)) {

            $enabled = JPluginHelper::isEnabled('poecompay', $plugin);

            if ($enabled) {
                $request = 'send' . $plugin . 'Request';

                // Fire sendRequest to open extneral form
                $dispatcher = JDispatcher::getInstance();
                JPluginHelper::importPlugin('poecompay', $plugin);
                $dispatcher->register($request, 'plgPoecomPay' . $plugin);
                $result = $dispatcher->trigger($request, $data);

                // exepect $result[0] to be an array
                if ($result[0]) {
                    $pay_type = $this->pay_method->type;
                    $msg = $result[0]['message'];
                }

                $response = array('request_number' => $rfq_number, 'pay_type' => $pay_type, 'message' => $msg);

                $json_response = json_encode($response);

                echo $json_response;
            } else {
                $app->enqueueMessage(JText::_('COM_POECOM_PLUGIN_NOT_ENABLED_ERROR'), 'error');

                echo '';
            }
        } else {
            echo '';
        }
    }

    /**
     * Save the RFQ data 
     * 
     * If order payment not successful no order is created, storing ordering data
     * in request allows off line order creation from customer input once payment issue is 
     * resolved.
     * 
     * @return boolean 
     */
    function saveRequest() {
        $app = JFactory::getApplication();

        $jsess = JFactory::getSession();
        $cart = $jsess->get('cart', null, 'poecom');

        $user = JFactory::getUser();

        if ($user->id > 0) {
            //logged in user
            $today = new JDate();

            $model = $this->getModel('Request');

            $rfq_id = '';

            // Check if RFQ previously created
            if (strlen($cart['rfq_number'])) {
                $rfq_number = $cart['rfq_number'];

                // get the record id
                if (($id = $model->getId($rfq_number))) {
                    $rfq_id = $id;
                }
            } else {
                $rfq_number = 'RFQ_' . $today->toUnix() . '_' . $user->id;
            }

            // prepare update array
            $rfq = array('id' => $rfq_id,
                'number' => $rfq_number,
                'status' => 1,
                'order_id' => 0,
                'date' => $today->toMySQL(true),
                'total' => number_format(floatval($cart['total']), 2),
                'currency_code' => $cart['currency'][0]->code,
                'juser_id' => $user->id,
                'cart' => json_encode($cart));

            if (!$model->save($rfq)) {
                $app->enqueueMessage(JText::_('COM_POECOM_RFQ_INSERT_ERROR'), 'error');

                return false;
            } else {
                // set the rfq in cart 
                $cart['rfq_number'] = $rfq_number;
                $cart['entrystatus'] = 'rfq';

                $jsess->set('cart', $cart, 'poecom');

                return true;
            }
        } else {
            $app->enqueueMessage(JText::_('COM_POECOM_RFQ_INSERT_USERID_ERROR'), 'error');

            return false;
        }
    }

    /**
     * Get payment status for a request
     * AJAX method 
     */
    function getPaymentStatus() {
        $order_id = '';

        $app = JFactory::getApplication();
        $jinput = $app->input;

        $rfq_number = $jinput->get('rfq_number', '', 'string');

        if (strlen($rfq_number)) {
            $model = $this->getModel('Payment');

            $payment = $model->getPaymentInfo($rfq_number);

            if ($payment) {
                $payment_status = $payment->status_id;

                if ($payment->status_id == 2) { // complete = 2
                    if ($payment->order_id == 0) {
                        // Create an order from the RFQ
                        $model = $this->getModel('Request');
                        if (!$order_id = $model->createOrderFromRFQ($rfq_number, $payment->id, $payment_status)) {
                            $order_id = 0;
                        }
                    } else {
                        $order_id = $payment->order_id;
                    }
                }
            } else {
                $payment_status = '4'; //waiting
            }
        } else {
            $app->enqueueMessage(JText::_('COM_POECOM_PAYMENT_STATUS_ERROR_NO_RFQ'), 'error');

            $payment_status = 'failed';
        }

        $response = array('payment_status' => $payment_status, 'order_id' => $order_id);

        $json_response = json_encode($response);

        echo $json_response;
    }

    /**
     * Handle API Payment request that only creates an order
     * Create XML Request and Pass Back Response 
     */
    public function handleAPIDirectRequest() {

        $response = array(
            'rfq_number' => '',
            'pay_type' => $this->pay_method->type,
            'message' => '',
            'debug_msg' => '',
            'error' => 0,
            'cc_pay_approved' => 0,
            'blocked' => 0
        );

        $app = JFactory::getApplication();
        $jinput = $app->input;

        $agreed_tos = $jinput->get('agreed_tos', '', 'string');

        if ($agreed_tos != 'checked') {
            $response['error'] = 'tos';
        } else {
           // if ($this->saveRequest()) {

                $jsess = JFactory::getSession();
                $cart = $jsess->get('cart', null, 'poecom');

                //set the rfq_number
               // $rfq_number = $cart['rfq_number'];
               // $response['rfq_number'] = $rfq_number;

              //  $model = $this->getModel('Request');
              //  $rfq = $model->getRFQ($rfq_number);

                $plugin = $this->pay_method->plugin;

                // Assign ccData
                $cc_data = $jinput->get('cc_data', null, 'array');
                $cart['cc_data'] = $cc_data[0];

                // Format data array for plugin
                $data = array('cart' => $cart);

                if (strlen($plugin)) {

                    $enabled = JPluginHelper::isEnabled('poecompay', $plugin);

                    if ($enabled) {
                        $request = 'send' . $plugin . 'Request';

                        // Fire sendRequest to open extneral form
                        $dispatcher = JDispatcher::getInstance();
                        JPluginHelper::importPlugin('poecompay', $plugin);
                        $dispatcher->register($request, 'plgPoecomPay' . $plugin);
                        $result = $dispatcher->trigger($request, $data);

                        // exepect $result[0] to be an array
                        if ($result[0]) {

                            if ($result[0]['cc_pay_approved'] == 1) {

                                $response['cc_pay_approved'] = 1;

                                //successful transaction, save payment transaction
                                $model = $this->getModel('PaymentTransaction');

                                $pay_status = 2; //complete

                                $data = array(
                                    'id' => '',
                                    'transaction_number' => $result[0]['cc_txn_number'],
                                    'type_id' => 1,
                                    'amount' => $result[0]['txn_amount'],
                                    'pay_method_id' => $this->pay_method->id,
                                    'rfq_id' => $rfq->id,
                                    'order_id' => 0,
                                    'status_id' => $pay_status,
                                    'transaction' => json_encode($result[0])
                                );

                                if ($model->save($data)) {
                                    //transaction saved 
                                   // $pay_txn = $model->getTransactionByRFQId($rfq->id);
                                    $pay_txn = $model->getTransactionByNumber($result[0]['cc_txn_number']);

                                    //create order
                                   // $model = $this->getModel('Request');

                                    if ($this->createOrderWithoutRFQ('', $pay_txn->id, $paystatus, $this->pay_method->id)) {
                                        //order saved
                                        $response['message'] = JText::_('COM_POECOM_CC_ORDER_SAVED_MSG');
                                    } else {
                                        $response['error'] = 1;
                                        $response['message'] = JText::_('COM_POECOM_CC_TRANSACTION_SAVE_ERROR_MSG');
                                    }
                                } else {
                                    $response['error'] = 1;
                                    $response['message'] = JText::_('COM_POECOM_CC_TRANSACTION_SAVE_ERROR_MSG');
                                }
                            } else {
                                //failed transaction
                                $response['error'] = 1;
                                $response['message'] = JText::_('COM_POECOM_CC_FAILED_TRANSACTION_MSG');
                                //update pay attempts
                                $cart = $jsess->get('cart', null, 'poecom');
                                $cart['cc_attempt']++;

                                //if max not specified in API plugin params then allow only 1 attempt
                                $allow_retry = strlen($result[0]['allow_retry']) ? (int) $result[0]['allow_retry'] : 0;

                                if (!$allow_retry = 1) {
                                    $response['blocked'] = 1;
                                } else {
                                    $cc_txn_number = $cart['cc_txn_number'];

                                    if (!strlen($cc_txn_number)) {
                                        //store txn number for retries
                                        $cart['cc_txn_number'] = $result[0]['cc_txn_number'];
                                    }
                                }

                                //update cart
                                $jsess->set('cart', $cart, 'poecom');
                            }

                            //show messages
                            $response['debug_msg'] = $result[0]['debug_msg'];
                        } else {
                            //API response empty means fail point hit
                            //$response['error'] = 1;
                            $response['blocked'] = 1;
                            $response['message'] = JText::_('COM_POECOM_CC_NO_API_RESULT_MSG');
                        }
                    } else {
                        //plugin not enabled
                        //$response['error'] = 1;
                        $response['blocked'] = 1;
                        $response['message'] = JText::_('COM_POECOM_PLUGIN_NOT_ENABLED_ERROR');
                    }
                } else {
                    //plugin not found
                    //$response['error'] = 1;
                    $response['blocked'] = 1;
                    $response['message'] = JText::_('COM_POECOM_PLUGIN_NOT_FOUND_ERROR');
                }
         /*   } else {
                //request not saved
                //$response['error'] = 1;
                $response['blocked'] = 1;
                $response['message'] = JText::_('COM_POECOM_CC_RFQ_SAVE_ERROR');
            }*/
        }
        $json_response = json_encode($response);

        echo $json_response;
    }


    /**
     * Handle API Payment request
     * Create XML Request and Pass Back Response 
     */
    public function handleAPIRequest() {

        $response = array(
            'rfq_number' => '',
            'pay_type' => $this->pay_method->type,
            'message' => '',
            'debug_msg' => '',
            'error' => 0,
            'cc_pay_approved' => 0,
            'blocked' => 0
        );

        $app = JFactory::getApplication();
        $jinput = $app->input;

        $agreed_tos = $jinput->get('agreed_tos', '', 'string');

        if ($agreed_tos != 'checked') {
            $response['error'] = 'tos';
        } else {
            if ($this->saveRequest()) {

                $jsess = JFactory::getSession();
                $cart = $jsess->get('cart', null, 'poecom');

                //set the rfq_number
                $rfq_number = $cart['rfq_number'];
                $response['rfq_number'] = $rfq_number;

                $model = $this->getModel('Request');
                $rfq = $model->getRFQ($rfq_number);

                $plugin = $this->pay_method->plugin;

                // Assign ccData
                $cc_data = $jinput->get('cc_data', null, 'array');
                $cart['cc_data'] = $cc_data[0];

                // Format data array for plugin
                $data = array('cart' => $cart);

                if (strlen($plugin)) {

                    $enabled = JPluginHelper::isEnabled('poecompay', $plugin);

                    if ($enabled) {
                        $request = 'send' . $plugin . 'Request';

                        // Fire sendRequest to open extneral form
                        $dispatcher = JDispatcher::getInstance();
                        JPluginHelper::importPlugin('poecompay', $plugin);
                        $dispatcher->register($request, 'plgPoecomPay' . $plugin);
                        $result = $dispatcher->trigger($request, $data);

                        // exepect $result[0] to be an array
                        if ($result[0]) {

                            if ($result[0]['cc_pay_approved'] == 1) {

                                $response['cc_pay_approved'] = 1;

                                //successful transaction, save payment transaction
                                $model = $this->getModel('PaymentTransaction');

                                $pay_status = 2; //complete

                                $data = array(
                                    'id' => '',
                                    'transaction_number' => $result[0]['cc_txn_number'],
                                    'type_id' => 1,
                                    'amount' => $result[0]['txn_amount'],
                                    'pay_method_id' => $this->pay_method->id,
                                    'rfq_id' => $rfq->id,
                                    'order_id' => 0,
                                    'status_id' => $pay_status,
                                    'transaction' => json_encode($result[0])
                                );

                                if ($model->save($data)) {
                                    //transaction saved 
                                    $pay_txn = $model->getTransactionByRFQId($rfq->id);

                                    //create order
                                    $model = $this->getModel('Request');

                                    if ($model->createOrderFromRFQ($rfq_number, $pay_txn->id, $pay_status)) {
                                        //order saved
                                        $response['message'] = JText::_('COM_POECOM_CC_ORDER_SAVED_MSG');
                                    } else {
                                        $response['error'] = 1;
                                        $response['message'] = JText::_('COM_POECOM_CC_TRANSACTION_SAVE_ERROR_MSG');
                                    }
                                } else {
                                    $response['error'] = 1;
                                    $response['message'] = JText::_('COM_POECOM_CC_TRANSACTION_SAVE_ERROR_MSG');
                                }
                            } else {
                                //failed transaction
                                $response['error'] = 1;
                                $response['message'] = JText::_('COM_POECOM_CC_FAILED_TRANSACTION_MSG');
                                //update pay attempts
                                $cart = $jsess->get('cart', null, 'poecom');
                                $cart['cc_attempt']++;

                                //if max not specified in API plugin params then allow only 1 attempt
                                $allow_retry = strlen($result[0]['allow_retry']) ? (int) $result[0]['allow_retry'] : 0;

                                if (!$allow_retry = 1) {
                                    $response['blocked'] = 1;
                                } else {
                                    $cc_txn_number = $cart['cc_txn_number'];

                                    if (!strlen($cc_txn_number)) {
                                        //store txn number for retries
                                        $cart['cc_txn_number'] = $result[0]['cc_txn_number'];
                                    }
                                }

                                //update cart
                                $jsess->set('cart', $cart, 'poecom');
                            }

                            //show messages
                            $response['debug_msg'] = $result[0]['debug_msg'];
                        } else {
                            //API response empty means fail point hit
                            //$response['error'] = 1;
                            $response['blocked'] = 1;
                            $response['message'] = JText::_('COM_POECOM_CC_NO_API_RESULT_MSG');
                        }
                    } else {
                        //plugin not enabled
                        //$response['error'] = 1;
                        $response['blocked'] = 1;
                        $response['message'] = JText::_('COM_POECOM_PLUGIN_NOT_ENABLED_ERROR');
                    }
                } else {
                    //plugin not found
                    //$response['error'] = 1;
                    $response['blocked'] = 1;
                    $response['message'] = JText::_('COM_POECOM_PLUGIN_NOT_FOUND_ERROR');
                }
            } else {
                //request not saved
                //$response['error'] = 1;
                $response['blocked'] = 1;
                $response['message'] = JText::_('COM_POECOM_CC_RFQ_SAVE_ERROR');
            }
        }
        $json_response = json_encode($response);

        echo $json_response;
    }

}

