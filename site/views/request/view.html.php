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
 * Request view class
 *
 * @package	Joomla.Site
 * @subpackage	com_poecom
 * @since	1.6
 */
class PoecomViewRequest extends JView {

    /**
     * Display RFQ (Request)
     * 
     * RFQ only displayed when no order was created 
     * 
     * NOTE: $send_ack controls email send so email is only sent once. If RFQ is changed
     * email acknowledgment is re-sent with updated information.
     *
     * @param	string	The template file to include
     * @since	1.6
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();
        $jinput = $app->input;

        $jsess = JFactory::getSession();

        // Get the rfq number (not id)
        $rfq_number = $jinput->get('rfq', '', 'CMD');

        //get RFQ details
        $rModel = $this->getModel('Request');
        $rfq = $rModel->getRFQDetail($rfq_number);

        if ($rfq) {
            $params = JComponentHelper::getParams('com_poecom');
            $auto_logout = $params->get('autologout', 0);
           
            if($auto_logout === '1'){
                $app->logout($rfq->juser_id);
            }
            
            $send_ack = $jsess->get('send_ack', 1, 'poecom');
            
            //send email once
            if ($rfq->email_sent === '0') {
                //prepare email body and set view values
                $body = $this->createEmailHTML($rfq, 'rfq');
                $subject = JText::_('COM_POECOM_RFQ_MAIL_SUBJECT') . ' - ' . $rfq_number;
                //send emails
                if($this->sendEmail($body, $subject)){
                    //set control flag
                    $rModel->setEmailSent($rfq->id, 1);
                }else{
                    $app->enqueueMessage(JText::_('COM_POECOM_ERROR_EMAIL'), 'error');
                }
            }

            //push values into template
            $type = 'rfq';
            $this->assignRef('type', $type);
            $this->assignRef('data', $rfq);
            $this->assignRef('shop_address', $this->shop_address);
            $this->assignRef('return_policy', $this->return_policy);
        } else {
            $app->enqueueMessage(JText::_('COM_POECOM_ERROR_RFQ_EMPTY'), 'error');
        }

        parent::display($tpl);
    }

    /**
     * Display Order function that initiates email acknowledgment
     * 
     * NOTE: $send_ack controls email send so email is only sent once. Order data is cleared
     * from cart when order is saved.
     * 
     * @param type $tpl
     */
    public function displayOrder($tpl = null) {
       
        $app = JFactory::getApplication();
        $jinput = $app->input;

        // Get inputs
        $order_id = $jinput->get('order_id', 0, 'INT');

        $oModel = $this->getModel('Order');
        //get order and all details
        $order = $oModel->getOrderDetail($order_id);
        
        if ($order) {
            $params = JComponentHelper::getParams('com_poecom');
            $auto_logout = $params->get('autologout', 0);
           
            if($auto_logout === '1'){
                $app->logout($order->juser_id);
            }
            //send email once
            if ($order->email_sent === '0') {
                //prepare email body and set view values
                $body = $this->createEmailHTML($order, 'order');
                $subject = JText::_('COM_POECOM_ORDER_MAIL_SUBJECT') . ' - ' . $order_id;
                //send emails
                if($this->sendEmail($body, $subject)){
                    //set control flag
                    $oModel->setEmailSent($order_id, 1);
                }else{
                    $app->enqueueMessage(JText::_('COM_POECOM_ERROR_EMAIL'), 'error');
                }
            }
            //push values into template
            $type = 'order';
            $this->assignRef('type', $type);
            $this->assignRef('data', $order);
            $this->assignRef('shop_address', $this->shop_address);
            $this->assignRef('return_policy', $this->return_policy);
        } else {
            $app->enqueueMessage(JText::_('COM_POECOM_ERROR_ORDER_EMPTY') . $order_id, 'error');
        }

        parent::display($tpl);
    }

    /**
     * Create HTML for email confirmation body
     * 
     * NOTE: <table> is required to avoid email rendering problems when <div> + css is used 
     * 
     * @param object $data Order/RFQ data
     * @param string $type Either rfq or order
     * 
     * @return string $html
     */
    private function createEmailHTML($data, $type = 'order') {
        //get shop parameters
        $params = JComponentHelper::getParams('com_poecom');
        $shop_name = $params->get('shopname');
        $this->shop_url = $params->get('shopurl');
        $location_id = $params->get('billinglocation', 0);

        if ($location_id > 0) {
            $lmodel = $this->getModel('Location');
            $this->shop_address = $lmodel->getItem($location_id);

            if ($this->shop_address) {
                //send email to location address
                $ack_address = $this->shop_address->email;
            } else {
                //send email to CSR address in component params
                $ack_address = $params->get('csremail', '');
            }
        }

        //get the return policy
        $this->return_policy = '';
        $return_policy_id = $params->get('returnpolicy', 0);

        if ($return_policy_id > 0) {
            $pmodel = $this->getModel('Payment');
            $this->return_policy = $pmodel->getReturnPolicy($return_policy_id);
        }

        if ($type == 'order') {
            $title = JText::_('COM_POECOM_ORDER_TITLE');
            $number_label = JText::_('COM_POECOM_ORDER_NUMBER');
            $number = $data->id . ' (' . $data->order_status_name . ')';
            switch ($data->status_id) {
                case '2': //invoiced
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_INVOICED');
                    break;
                case '3': //paid
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_PAID');
                    break;
                case '4': //shipped
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_SHIPPED');
                    break;
                case '1': //open
                default:
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_OPEN');
                    break;
            }
        } else { //rfq
            $title = JText::_('COM_POECOM_RFQ_TITLE');
            $number_label = JText::_('COM_POECOM_RFQ_NUMBER');
            $number = $data->rfq_number;

            switch ($data->status_id) {
                case '2': //ordered
                    $message = JText::_('COM_POECOM_RFQ_MESSAGE_ORDERED');
                    break;
                case '3': //canceled
                    $message = JText::_('COM_POECOM_RFQ_MESSAGE_CANCELED');
                    break;
                case '1': //open
                default:
                    $message = JText::_('COM_POECOM_RFQ_MESSAGE_OPEN');
                    break;
            }
        }

        $this->shop_logo = $params->get('shoplogo');
        $this->carrier_logo = !empty($data->carrier->carrier_logo) ? $data->carrier->carrier_logo : '';
        $this->processor_logo = !empty($data->paymethod->logo) ? $data->paymethod->logo : '';

        $this->sender = $params->get('shopreplyemail', 'no-rely@company.com');
        $this->recipients = array();
        $this->recipients[] = $data->bt->email;
        $this->recipients[] = $ack_address;

        $html = '';

        $html .= '<table style="width:707px;">
    <tr><td colspan="2" style="font-weight:bold;font-size:14px;border:none;">' . $title . '</td></tr>
    <tr><td colspan="2">' . $message . '</td></tr>
    <tr><td colspan="2" style="font-weight:bold;font-size:14px;">' . $number_label . ' : ' . $number . '</td></tr>
    <tr>
        <td>
            <table>
                <tr>
                    <td style="font-weight:bold;font-size:14px;">' . $shop_name . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->name . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->street1 . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->street2 . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->city . ', ' . $this->shop_address->region_name . ', ' . $this->shop_address->postal_code . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->country_name . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->telephone1 . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->telephone2 . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_url . '</td> 
                </tr>
            </table>
        </td>
        <td style="vertical-align:top;">
            <img src="cid:shop_logo" alt="Shop Logo" />
        </td>
    </tr>
    <tr>
        <td>
            <table>
                <tr>
                    <td style="font-weight: bold;">' . JText::_('COM_POECOM_USER_BT_LBL') . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->fname .' '.$data->bt->lname . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->street1 . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->street2 . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->city . ', ' . $data->bt->region . ', ' . $data->bt->postal_code . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->country . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->telephone . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->email . '</td>
                </tr>
            </table>
        </td>
        <td style="vertical-align: top;">';
        
        if(!empty($data->st)){
        $html .= '<table>
                <tr>
                    <td style="font-weight: bold;">' . JText::_('COM_POECOM_USER_ST_LBL') . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->fname .' '.$data->st->lname . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->street1 . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->street2 . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->city . ', ' . $data->st->region . ', ' . $data->st->postal_code . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->country . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->telephone . '</td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </table>';
        }else{
            $html .= '<table>
                <tr>
                    <td style="font-weight: bold;">' . JText::_('COM_POECOM_USER_ST_LBL') . '</td>
                </tr>
                <tr>
                    <td>' . JText::_('COM_POECOM_USER_STBT_SAME_LABEL') . '</td>
                </tr>
            </table>';
        }
        $html .='</td>
    </tr>
</table>
<table cellspacing="0">
    <tr>
        <th style="text-align: center; border: 1px solid #ddd;">' . JText::_('COM_POECOM_CART_PROD_HD') . '</th>
        <th style="text-align: center; border: 1px solid #ddd;">' . JText::_('COM_POECOM_CART_PRICE_HD') . '</th>
        <th style="text-align: center; border: 1px solid #ddd;">' . JText::_('COM_POECOM_CART_QTY_HD') . '</th>
        <th style="text-align: center; border: 1px solid #ddd;">' . JText::_('COM_POECOM_CART_LINE_TOTAL_HD') . '</th>
    </tr>';
   
        if ($data->items) {
            $idx = 0;
            foreach ($data->items as $itm) {
                $html .= ' <tr style="border: 1px solid #ddd;">';
                $html .= '<td style="width:399px; border: 1px solid #ddd;">';
                $html .= '<ul style="list-style: none;">';
                $html .= '<li>' . JText::_('COM_POECOM_CART_LINE_SKU') . ': ' . $itm->product_sku . '</li>';
                $html .= '<li>' . $itm->list_description . '</li>';
                if($itm->properties){
                    foreach($itm->properties as $property){
                        $html .= '<li>'.$property->name. ' : '. $property->option_label. '</li>';
                    }
                }
                if ($itm->selected_options) {
                    foreach ($itm->selected_options as $op => $val) {
                        $html .= '<li>' . $op . " : " . $val . '</li>';
                    }
                }
                $html . '</ul>';
                $html .='</td>';
                $html .= '<td style="text-align: right;width:100px;border: 1px solid #ddd;">' . number_format($itm->price, 2) . '</td>';
                $html .= '<td style="text-align: center;width:100px;border: 1px solid #ddd;">' . $itm->quantity . '</td>';
                $html .= '<td style="text-align: right;width:100px;border: 1px solid #ddd;">' . number_format($itm->total, 2) . '</td>';
                $html .= '</tr>';
                $idx++;
            }
        }
       
$html .= '</table>
<table>
    <tr>
        <td style="width: 399px;">';
        if (!empty($data->carrier)) {
            $language = JFactory::getLanguage();
            //load plugin site language
            
            $language->load('plg_poecomship_'.$data->carrier->plugin, 'plugins/poecomship/'.$data->carrier->plugin.'/', $language->getTag(), true);
            
            $html .= '<table>';

            if (!empty($data->carrier->carrier_logo)) {
                $html .= '<tr><td><img src="cid:carrier_logo" alt="' . JText::_($data->carrier->carrier) . '"/></td></tr>';
            } else {
                $html .= '<tr><td>' . JText::_($data->carrier->carrier) . '</td></tr>';
            }

            $html .= '<tr><td>' . JText::_($data->carrier->service) . ' : ' . JText::_($data->carrier->eta) . '</td></tr>';
        } else {
            $html .= '<tr><td>No Carrier Information</td></tr>';
        }
        $html .= '</table>
        </td>
        <td>
            <table cellspacing="0" style="border: 1px solid #ddd;">
                <tr>
                <td style="width: 206px;">' . JText::_('COM_POECOM_CART_LINE_SUBTOTAL_LBL') . '</td>
                <td style="width:100px;text-align:right;">' . $data->currency->symbol . number_format($data->subtotal, 2) . '</td>
                </tr>
                <tr>
                <td style="width: 206px;">' . JText::_('COM_POECOM_CART_DISCOUNT_LBL') . '</td>
                <td style="width:100px;text-align:right;">' . number_format($data->total_discount, 2) . '</td>
                </tr>
                <tr>
                <td style="width: 206px;">' . JText::_('COM_POECOM_CART_PROD_TAX_LBL') . '</td>
                <td style="width:100px;text-align:right;">' . number_format($data->product_tax, 2) . '</td>
                </tr>
                <tr>
                <td style="width: 206px;">' . JText::_('COM_POECOM_CART_SHIP_COST_LBL') . '</td>
                <td style="width:100px;text-align:right;">' . number_format($data->shipping_cost, 2) . '</td>
                </tr>
                <tr>
                <td style="width: 206px;">' . JText::_('COM_POECOM_CART_SHIP_TAX_LBL') . '</td>
                <td style="width:100px;text-align:right;">' . number_format($data->shipping_tax, 2) . '</td>
                </tr>
                <tr>
                <td style="width: 206px;">' . JText::_('COM_POECOM_CART_TOTAL_LBL') . '</td>
                <td style="width:100px;text-align:right;">' . $data->currency->symbol . number_format($data->total, 2) . '</td>
                </tr>
            </table>
        </td>
    </tr>';
        if (!empty($data->coupon_code)) {
            $html .= '<tr><td>' . JText::_('COM_POECOM_COUPON_CODE_USED_LBL') . ": " . $data->coupon_code . '</td></tr>';
        }
        $html .= '</table>';
        if (!empty($data->paymethod)) {
            $language = JFactory::getLanguage();
            //load plugin site language
            $language->load('plg_poecompay_'.$data->paymethod->plugin, 'plugins/poecompay/'.$data->paymethod->plugin.'/', $language->getTag(), true);
            
            $html .= '<table>
    <tr>
        <td>
            <table>';
            if (!empty($data->paymethod->logo)) {
                $html .= '<tr><td><img src="cid:processor_logo" alt="' . JText::_($data->paymethod->name) . '"/></td></tr>';
            } else {
                $html .= '<tr><td>' . JText::_($data->paymethod->name) . '</td></tr>';
            }
            if (!empty($data->payment)) { //Show transaction
                $html .= '<tr><td>' . JText::_('COM_POECOM_PAYMENT_STATUS') . ' - ' . $data->payment->status . '<td></tr>';
                $html .= '<tr><td>' . JText::_('COM_POECOM_PAYMENT_TXN_ID') . ' : ' . $data->payment->transaction_number . '<td></tr>';
                if ($data->payment->mandatory_fields) {
                    $html .= '<tr><td>' . JText::_('COM_POECOM_PAYMT_RECEIPT_TITLE') . '<td></tr>';
                    $html = '<tr><td>';
                    foreach ($data->payment->mandatory_fields as $k => $v) {
                        $html .= '<dd>' . $k . ' : ' . $v . '</dd>';
                    }
                    $html . '</td></tr>';
                }
            }
            $html .= '</table>
        </td>
    </tr>
</table>';
        }
        if (!empty($this->return_policy)) {
            $html .= '<table>
            <tr>
                <th>' . JText::_("COM_POECOM_RETURN_POLICY_TITLE") . ' </th>
            </tr>
            <tr>
                <td>
                    ' . $this->return_policy . '
                </td>
            </tr>
        </table>';
        }

        //stop GMail trimming content
        $html .= '<label>' . date("m/d/Y h:i:s a", time()) . '</label>';
        //testing
        //echo $html;
        return $html;
    }
        /**
     * Create the html body for print view
     * 
     * Inputs set in view->display()
     * 
     * @param object $data Order/RFQ data
     * @param string $type Either rfq or order 
     * 
     * @return string $hmtl HTML string
     */
    private function createPrintViewHTML_TABLE($data, $type = 'order') {
        //get shop parameters
        $params = JComponentHelper::getParams('com_poecom');
        $shop_name = $params->get('shopname');
        $this->shop_url = $params->get('shopurl');
        $location_id = $params->get('billinglocation', 0);

        if ($location_id > 0) {
            $lmodel = $this->getModel('Location');
            $this->shop_address = $lmodel->getItem($location_id);
        }

        //get the return policy
        $this->return_policy = '';
        $return_policy_id = $params->get('returnpolicy', 0);

        if ($return_policy_id > 0) {
            $pmodel = $this->getModel('Payment');
            $this->return_policy = $pmodel->getReturnPolicy($return_policy_id);
        }
       
        if ($type == 'order') {
            $title = JText::_('COM_POECOM_ORDER_TITLE'). ' - '. JText::_('COM_POECOM_ORDER_NUMBER'). ' : ';
            $title .= $data->id . ' (' . $data->order_status_name . ')';
            switch ($data->status_id) {
                case '2': //invoiced
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_INVOICED');
                    break;
                case '3': //paid
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_PAID');
                    break;
                case '4': //shipped
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_SHIPPED');
                    break;
                case '1': //open
                default:
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_OPEN');
                    break;
            }
        } else { //rfq
            $title = JText::_('COM_POECOM_RFQ_TITLE'). ' - '.JText::_('COM_POECOM_RFQ_NUMBER'). ' : ';
            $title .= $data->rfq_number;

            switch ($data->status_id) {
                case '2': //ordered
                    $message = JText::_('COM_POECOM_RFQ_MESSAGE_ORDERED');
                    break;
                case '3': //canceled
                    $message = JText::_('COM_POECOM_RFQ_MESSAGE_CANCELED');
                    break;
                case '1': //open
                default:
                    $message = JText::_('COM_POECOM_RFQ_MESSAGE_OPEN');
                    break;
            }
        }
        $this->shop_logo = $params->get('shoplogo');
        
        $html = '';

        $html .= '<table style="width:707px; border: none;">
    <tr><td colspan="2" style="font-weight:bold;font-size:14px;">' . $title . '</td></tr>
    <tr><td colspan="2" style="padding-bottom:20px;">' . $message . '</td></tr>
    <tr>
        <td style="padding-bottom:20px;">
            <table>
                <tr>
                    <td style="font-weight:bold;font-size:14px;">' . $shop_name . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->name . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->street1 . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->street2 . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->city . ', ' . $this->shop_address->region_name . ', ' . $this->shop_address->postal_code . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->country_name . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->telephone1 . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_address->telephone2 . '</td>
                </tr>
                <tr>
                    <td>' . $this->shop_url . '</td> 
                </tr>
            </table>
        </td>
        <td style="vertical-align:top;">
            <img src="'.$this->shop_logo.'" alt="Shop Logo" />
        </td>
    </tr>
    <tr>
        <td style="padding-bottom:20px;">
            <table>
                <tr>
                    <td style="font-weight: bold;">' . JText::_('COM_POECOM_USER_BT_LBL') . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->full_name . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->street1 . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->street2 . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->city . ', ' . $data->bt->region . ', ' . $data->bt->postal_code . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->country . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->telephone . '</td>
                </tr>
                <tr>
                    <td>' . $data->bt->email . '</td>
                </tr>
            </table>
        </td>
        <td style="vertical-align: top;">';
        
        if(!empty($data->st)){
        $html .= '<table>
                <tr>
                    <td style="font-weight: bold;">' . JText::_('COM_POECOM_USER_ST_LBL') . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->full_name . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->street1 . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->street2 . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->city . ', ' . $data->st->region . ', ' . $data->st->postal_code . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->country . '</td>
                </tr>
                <tr>
                    <td>' . $data->st->telephone . '</td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </table>';
        }else{
            $html .= '<table>
                <tr>
                    <td style="font-weight: bold;">' . JText::_('COM_POECOM_USER_ST_LBL') . '</td>
                </tr>
                <tr>
                    <td>' . JText::_('COM_POECOM_USER_STBT_SAME_LABEL') . '</td>
                </tr>
            </table>';
        }
        $html .='</td>
    </tr>
</table>
<table cellspacing="0">
    <tr>
        <th style="text-align: center; border: 1px solid #ddd;">' . JText::_('COM_POECOM_CART_PROD_HD') . '</th>
        <th style="text-align: center; border: 1px solid #ddd;">' . JText::_('COM_POECOM_CART_PRICE_HD') . '</th>
        <th style="text-align: center; border: 1px solid #ddd;">' . JText::_('COM_POECOM_CART_QTY_HD') . '</th>
        <th style="text-align: center; border: 1px solid #ddd;">' . JText::_('COM_POECOM_CART_LINE_TOTAL_HD') . '</th>
    </tr>';
   
   if ($data->items) {
            $idx = 0;
            foreach ($data->items as $itm) {
                $html .= '<tr style="border: 1px solid #ddd;">';
                $html .= '<td style="width:399px; border: 1px solid #ddd;">';
                $html .= '<ul style="list-style: none;">';
                $html .= '<li>' . JText::_('COM_POECOM_CART_LINE_SKU') . ': ' . $itm->product_sku . '</li>';
                $html .= '<li>' . $itm->list_description . '</li>';
                if($itm->properties){
                    foreach($itm->properties as $property){
                        $html .= '<li>'.$property->name. ' : '. $property->option_label. '</li>';
                    }
                }
                if ($itm->selected_options) {
                    foreach ($itm->selected_options as $op => $val) {
                        $html .= '<li>' . $op . " : " . $val . '</li>';
                    }
                }
                $html . '</ul>';
                $html .='</td>';
                $html .= '<td style="text-align: right;width:93px;border: 1px solid #ddd;padding-right: 10px;">' . number_format($itm->price, 2) . '</td>';
                $html .= '<td style="text-align: center;width:98px;border: 1px solid #ddd;">' . $itm->quantity . '</td>';
                $html .= '<td style="text-align: right;width:93px;border: 1px solid #ddd;padding-right: 10px;">' . number_format($itm->total, 2) . '</td>';
                $html .= '</tr>';
                $idx++;
            }
        }
$html .= '</table>
<table>
    <tr>
        <td style="width: 400px;">';
        if (!empty($data->carrier)) {
            
            $language = JFactory::getLanguage();
            //load plugin site language
            $language->load('plg_poecomship_'.$data->carrier->plugin, 'plugins/poecomship/'.$data->carrier->plugin.'/', $language->getTag(), true);
            
            $html .= '<table>';

            if (!empty($data->carrier->carrier_logo)) {
                $html .= '<tr><td><img src="'.$data->carrier->carrier_logo.'" alt="' . JText::_($data->carrier->carrier) . '"/></td></tr>';
            } else {
                $html .= '<tr><td>' . JText::_($data->carrier->carrier) . '</td></tr>';
            }

            $html .= '<tr><td>' . JText::_($data->carrier->service) . ' : ' . JText::_($data->carrier->eta) . '</td></tr>';
        } else {
            $html .= '<tr><td>No Carrier Information</td></tr>';
        }
        $html .= '</table>
        </td>
        <td>
            <table cellspacing="0" style="border: 1px solid #ddd;">
                <tr>
                <td style="width: 196px; padding-left: 10px;">' . JText::_('COM_POECOM_CART_LINE_SUBTOTAL_LBL') . '</td>
                <td style="width:90px;text-align:right;padding-right:10px;">' . $data->currency->symbol . number_format($data->subtotal, 2) . '</td>
                </tr>';
        if($data->total_discount > 0){
        $html.= '<tr>
                <td style="width: 196px; padding-left: 10px;">' . JText::_('COM_POECOM_CART_DISCOUNT_LBL') . '</td>
                <td style="width:90px;text-align:right;padding-right:10px;">' . number_format($data->total_discount, 2) . '</td>
                </tr>';
        }
        $html .= '<tr>
                <td style="width: 196px; padding-left: 10px;">' . JText::_('COM_POECOM_CART_PROD_TAX_LBL') . '</td>
                <td style="width:90px;text-align:right;padding-right:10px;">' . number_format($data->product_tax, 2) . '</td>
                </tr>
                <tr>
                <td style="width: 196px; padding-left: 10px;">' . JText::_('COM_POECOM_CART_SHIP_COST_LBL') . '</td>
                <td style="width:90px;text-align:right;padding-right:10px;">' . number_format($data->shipping_cost, 2) . '</td>
                </tr>
                <tr>
                <td style="width: 196px; padding-left: 10px;">' . JText::_('COM_POECOM_CART_SHIP_TAX_LBL') . '</td>
                <td style="width:90px;text-align:right;padding-right:10px;">' . number_format($data->shipping_tax, 2) . '</td>
                </tr>
                <tr>
                <td style="width: 196px; padding-left: 10px;">' . JText::_('COM_POECOM_CART_TOTAL_LBL') . '</td>
                <td style="width:90px;text-align:right;padding-right:10px;">' . $data->currency->symbol . number_format($data->total, 2) . '</td>
                </tr>
            </table>
        </td>
    </tr>';
        if (!empty($data->coupon_code)) {
            $html .= '<tr><td>' . JText::_('COM_POECOM_COUPON_CODE_USED_LBL') . ": " . $data->coupon_code . '</td></tr>';
        }
        $html .= '</table>';
        if (!empty($data->paymethod)) {
            $language = JFactory::getLanguage();
            //load plugin site language
            $language->load('plg_poecompay_'.$data->paymethod->plugin, 'plugins/poecompay/'.$data->paymethod->plugin.'/', $language->getTag(), true);
            
            $html .= '<table>
    <tr>
        <td>
            <table>';
            if (!empty($data->paymethod->logo)) {
                $html .= '<tr><td><img src="'.$data->paymethod->logo.'" alt="' . JText::_($data->paymethod->name) . '"/></td></tr>';
            } else {
                $html .= '<tr><td>' . JText::_($data->paymethod->name) . '</td></tr>';
            }
            if (!empty($data->payment)) { //Show transaction
                $html .= '<tr><td>' . JText::_('COM_POECOM_PAYMENT_STATUS') . ' - ' . $data->payment->status . '<td></tr>';
                $html .= '<tr><td>' . JText::_('COM_POECOM_PAYMENT_TXN_ID') . ' : ' . $data->payment->transaction_number . '<td></tr>';
                if ($data->payment->mandatory_fields) {
                    $html .= '<tr><td>' . JText::_('COM_POECOM_PAYMT_RECEIPT_TITLE') . '<td></tr>';
                    $html = '<tr><td>';
                    foreach ($data->payment->mandatory_fields as $k => $v) {
                        $html .= '<dd>' . $k . ' : ' . $v . '</dd>';
                    }
                    $html . '</td></tr>';
                }
            }
            $html .= '</table>
        </td>
    </tr>
</table>';
        }
        if (!empty($this->return_policy)) {
            $html .= '<table>
            <tr>
                <th>' . JText::_("COM_POECOM_RETURN_POLICY_TITLE") . ' </th>
            </tr>
            <tr>
                <td>
                    ' . $this->return_policy . '
                </td>
            </tr>
        </table>';
        }

        //stop GMail trimming content
        $html .= '<label>' . date("m/d/Y h:i:s a", time()) . '</label>';
        return $html;
    }

    /**
     * Create the html body for print view
     * 
     * Inputs set in view->display()
     * 
     * @param object $data Order/RFQ data
     * @param string $type Either rfq or order 
     * 
     * @return string $hmtl HTML string
     */
    private function createPrintViewHTML($data, $type = 'order') {
        //get shop parameters
        $params = JComponentHelper::getParams('com_poecom');
        $shop_name = $params->get('shopname');
        $this->shop_url = $params->get('shopurl');
        $location_id = $params->get('billinglocation', 0);

        if ($location_id > 0) {
            $lmodel = $this->getModel('Location');
            $this->shop_address = $lmodel->getItem($location_id);
        }

        //get the return policy
        $this->return_policy = '';
        $return_policy_id = $params->get('returnpolicy', 0);

        if ($return_policy_id > 0) {
            $pmodel = $this->getModel('Payment');
            $this->return_policy = $pmodel->getReturnPolicy($return_policy_id);
        }
        $html = '';
        $html .= '<div id="content"><div id="cart-wrap"><div id="cart-container">';

        if ($type == 'order') {
            $title = JText::_('COM_POECOM_ORDER_TITLE'). ' - '. JText::_('COM_POECOM_ORDER_NUMBER') . ' ';
            
            $title .= $data->id . ' (' . $data->order_status_name . ')';
            switch ($data->status_id) {
                case '2': //invoiced
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_INVOICED');
                    break;
                case '3': //paid
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_PAID');
                    break;
                case '4': //shipped
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_SHIPPED');
                    break;
                case '1': //open
                default:
                    $message = JText::_('COM_POECOM_ORDER_MESSAGE_OPEN');
                    break;
            }
        } else { //rfq
            $title = JText::_('COM_POECOM_RFQ_TITLE').' - '.JText::_('COM_POECOM_RFQ_NUMBER').' ';
            $title .= $data->rfq_number;

            switch ($data->status_id) {
                case '2': //ordered
                    $message = JText::_('COM_POECOM_RFQ_MESSAGE_ORDERED');
                    break;
                case '3': //canceled
                    $message = JText::_('COM_POECOM_RFQ_MESSAGE_CANCELED');
                    break;
                case '1': //open
                default:
                    $message = JText::_('COM_POECOM_RFQ_MESSAGE_OPEN');
                    break;
            }
        }

        $html .= '<div id="confirmation-title">' . $title . '</div>';

        if ($message) {
            $html .= '<div id="confirmation-msg">' . $message . '</div>';
        }

        $html .= '<div id="shop-info">';

        $this->shop_logo = $params->get('shoplogo');
        if (!empty($data->carrier->carrier_logo)) {
            $this->carrier_logo = $data->carrier->carrier_logo;
        }

        $html .= '<div id="shop-logo"><img src="' . $this->shop_logo . '" alt="Shop Logo" /></div>';

        if (!empty($this->shop_address)) {

            $html .= '<div id="shop-address">';
            $html .= '<div>
                        <dl>
                            <dt class="cart-title">' . $shop_name . '</dt>
                            <dd>' . $this->shop_address->name . '</dd>
                            <dd>' . $this->shop_address->street1 . '</dd>
                            <dd>' . $this->shop_address->street2 . '</dd>
                            <dd>' . $this->shop_address->city . ', ' . $this->shop_address->region_name . ', ' . $this->shop_address->postal_code . '</dd>
                            <dd>' . $this->shop_address->country_name . '</dd>
                            <dd>' . $this->shop_address->telephone1 . '</dd>
                            <dd>' . $this->shop_address->telephone2 . '</dd>
                            <dd>' . $this->shop_url . '</dd>
                        </dl>
                    </div>
                    </div>';
        }

        $html .= '</div>';

        $html .= '<div id="cart-address">';
        $html .= '<div id="bt-address">';
        $html .= '<div>
                    <dl>
                        <dt class="cart-title">' . JText::_('COM_POECOM_USER_BT_LBL') . '</dt>
                        <dd>' . $data->bt->full_name . '</dd>
                        <dd>' . $data->bt->street1 . '</dd>
                        <dd>' . $data->bt->street2 . '</dd>
                        <dd>' . $data->bt->city . ', ' . $data->bt->region . ', ' . $data->bt->postal_code . '</dd>
                        <dd>' . $data->bt->country . '</dd>
                        <dd>' . $data->bt->telephone . '</dd>
                        <dd>' . $data->bt->email . '</dd>
                    </dl>
                    </div>
                    </div>
            <div id="st-address">';
        if (!empty($data->st)) {
            $html .= '<div>
                    <dl>
                        <dt class="cart-title">' . JText::_('COM_POECOM_USER_ST_LBL') . '</dt>
                        <dd>' . $data->st->full_name . '</dd>
                        <dd>' . $data->st->street1 . '</dd>
                        <dd>' . $data->st->street2 . '</dd>
                        <dd>' . $data->st->city . ', ' . $data->st->region . ', ' . $data->st->postal_code . '</dd>
                        <dd>' . $data->st->country . '</dd>
                        <dd>' . $data->st->telephone . '</dd>
                    </dl>
                </div>';
        } else {
            $html .= '<div>
                        <dl>
                        <dt class="cart-title">' . JText::_('COM_POECOM_USER_ST_LBL') . '</dt>
                        <dd></dd>
                        <dd>' . JText::_('COM_POECOM_USER_STBT_SAME_LABEL') . '</dd>
                    </dl>
                    </div>';
        }
        $html .= '</div>
        </div>
        <div id="cart-list">
            <div id="cart-list-hd">
                <ul>
                    <li><div id="cart-prod-hd">' . JText::_('COM_POECOM_CART_PROD_HD') . '</div></li>
                    <li><div id="cart-price-hd">' . JText::_('COM_POECOM_CART_PRICE_HD') . '</div></li>
                    <li><div id="cart-qty-hd">' . JText::_('COM_POECOM_CART_QTY_HD') . '</div></li>
                    <li><div id="cart-line-total-hd">' . JText::_('COM_POECOM_CART_LINE_TOTAL_HD') . '</div></li>
                </ul>
            </div>
            <div id="cart-container-items">';

        if ($data->items) {
            $idx = 0;
            foreach ($data->items as $itm) {
                $html .= '<div class="cart-item">';
                $html .= '<div class="cart-item-id">';
                $html .= '<ul style="list-style: none;">';
                $html .= '<li>' . JText::_('COM_POECOM_CART_LINE_SKU') . ': ' . $itm->product_sku . '</li>';
                $html .= '<li>' . $itm->list_description . '</li>';
                if($itm->properties){
                    foreach($itm->properties as $property){
                        $html .= '<li>'.$property->name. ' : '. $property->option_label. '</li>';
                    }
                }
                if ($itm->selected_options) {
                    foreach ($itm->selected_options as $op => $val) {
                        $html .= '<li>' . $op . " : " . $val . '</li>';
                    }
                }
                $html . '</ul>';
                $html .='</div>';
                $html .= '<div class="cart-item-price">' . number_format($itm->price, 2) . '</div>';
                $html .= '<div class="cart-item-qty"><div>' . $itm->quantity . '</div>';
                $html .= '</div>';
                $html .= '<div class="cart-item-total">' . number_format($itm->total, 2) . '</div>';
                $html .= '</div>';

                $idx++;
            }
        }
        $html .= '</div></div>
                <div id="cart-bottom-container">
                <div id="shipping-wrap">
                    <div id="cart-shipping">';
        if (!empty($data->carrier)) {
            $language = JFactory::getLanguage();
            //load plugin site language
            $language->load('plg_poecomship_'.$data->carrier->plugin, 'plugins/poecomship/'.$data->carrier->plugin.'/', $language->getTag(), true);
                
            $html .= '<div class="carrier-container"><label>' . JText::_('COM_POECOM_ORDER_EMAIL_SHIP_METHOD') . '</label><div class="carrier-logo">';

            if (!empty($this->carrier_logo)) {
                $html .= '<img src="' . $this->carrier_logo . '" alt="' . JText::_($data->carrier->carrier) . '"/>';
            } else {
                $html .= JText::_($data->carrier->carrier);
            }

            $html .= '</div>';
            $html .= '<div class="ship-rate-container">';
            $html .= '<div class="ship-rate-radio">';
            $html .= '</div><div class="ship-rate">';
            $html .= '<div class="ship-rate-service">' . JText::_($data->carrier->service) . '</div>';
            $html .= '<div class="ship-rate-eta">' . JText::_($data->carrier->eta) . '</div></div></div>';
            $html .= '</div>';
        }
        $html .= '</div>
            <div id="cart-totals-wrap">
                <div id="cart-list-totals">
                    <div id="cart-totals-lbl">
                        <ul>
                            <li><div class="cart-lbl">' . JText::_('COM_POECOM_CART_LINE_SUBTOTAL_LBL') . '</div></li>';
                            if($data->total_discount > 0){
			    $html .='<li><div class="cart-lbl">' . JText::_('COM_POECOM_CART_DISCOUNT_LBL') . '</div></li>';
                            }
                            $html .= '<li><div class="cart-lbl">' . JText::_('COM_POECOM_CART_PROD_TAX_LBL') . '</div></li>
                            <li><div class="cart-lbl">' . JText::_('COM_POECOM_CART_SHIP_COST_LBL') . '</div></li>
                            <li><div class="cart-lbl">' . JText::_('COM_POECOM_CART_SHIP_TAX_LBL') . '</div></li>
                            <li><div class="cart-lbl">' . JText::_('COM_POECOM_CART_TOTAL_LBL') . '</div></li>
                        </ul>
                    </div>
                    <div id="cart-totals-amt">
                        <ul>
                            <li><div class="cart-totals">' . $data->currency->symbol . number_format($data->subtotal, 2) . '</div></li>';
                            if($data->total_discount > 0){
			    $html .='<li><div class="cart-totals" id="discount_amount">' . number_format($data->total_discount, 2) . '</div></li>';
                            }
                            $html .= '<li><div class="cart-totals">' . number_format($data->product_tax, 2) . '</div></li>
                            <li><div class="cart-totals" id="shipping_cost">' . number_format($data->shipping_cost, 2) . '</div></li>
                            <li><div class="cart-totals" id="shipping_tax">' . number_format($data->shipping_tax, 2) . '</div></li>
                            <li><div class="cart-totals" id="total">' . $data->currency->symbol . number_format($data->total, 2) . '</div></li>
                        </ul>
                    </div>
                </div></div></div>';
        if (!empty($data->coupon_code)) {
            $html .= '<div id="poe-coupon">
			<label>' . JText::_('COM_POECOM_COUPON_CODE_USED_LBL') . ": " . $data->coupon_code . '</label>
			    </div>';
        }

        $html .= '<div id="pay-methods">';
        if (!empty($data->paymethod)) { 
            $language = JFactory::getLanguage();
            //load plugin site language
            $language->load('plg_poecompay_'.$data->paymethod->plugin, 'plugins/poecompay/'.$data->paymethod->plugin.'/', $language->getTag(), true);
            
            $html .= '<div class="pay-method"><label>' . JText::_('COM_POECOM_ORDER_EMAIL_PAY_METHOD') . '</label>
                <div class="pay-method-logo">';
            if (!empty($data->paymethod->logo)) {
                $html .= '<img src="' . $data->paymethod->logo . '" alt="' . JText::_($data->paymethod->name) . '"/>';
            } else {
                $html .= JText::_($data->paymethod->name);
            }
            $html .= '</div>';
            if (!empty($this->data->payment)) { //Show payment transaction
                $html .= '<div class="pay-method-radio">
                    <div>' . JText::_('COM_POECOM_PAYMENT_STATUS') . ' - ' . $data->payment->status . '</div>
                    <div>' . JText::_('COM_POECOM_PAYMENT_TXN_ID') . ' : ' . $data->payment->transaction_number . '</div>';
                if ($data->payment->mandatory_fields) {
                    $html .= '<div>
                    <dl>
                        <dt id="poe-txn-receipt">' . JText::_('COM_POECOM_PAYMT_RECEIPT_TITLE') . '</dt>';
                    foreach ($data->payment->mandatory_fields as $k => $v) {
                        $html .= '<dd>' . $k . ' : ' . $v . '</dd>';
                    }
                    $html .= '</dl></div>';
                }
                $html .= '</div>';
            }
        }
        $html .= '</div></div>';

        if (strlen($this->return_policy)) {
            $html .= '<div id="cart-return-policy">
            <div>' . JText::_('COM_POECOM_RETURN_POLICY_TITLE') . '</div>' . $this->return_policy . '</div>';
        }

        $html .= '</div></div>';

        return $html;
    }

    /**
     * Print view - Used in checkout to allow order printing
     * @param string $tpl Template to use
     */
    public function printView($tpl = null) {
        $app = JFactory::getApplication();
        $jinput = $app->input;

        $doc_type = $jinput->get('doc_type', 'rfq', 'STRING');
        $doc_id = $jinput->get('doc_id', 0, 'STRING');

        if ($doc_type == 'rfq') {
            //display a RFQ
            $rModel = $this->getModel('Request');
            //get rfq and all details
            $rfq = $rModel->getRFQDetail($doc_id);
            //create HTML for display
            $html = $this->createPrintViewHTML($rfq, 'rfq');
        } else if ($doc_type == 'order') {
            //get order detail
            $oModel = $this->getModel('Order');
            //get order and all details
            $order = $oModel->getOrderDetail($doc_id);
            //create HTML for display
            $html = $this->createPrintViewHTML($order, 'order');
        } else {
            $app->enqueueMessage(JText::_('COM_POECOM_ERROR_RFQ_EMPTY'), 'error');
        }

        $this->assignRef('html', $html);

        parent::display($tpl);
    }

    /**
     * Prepare email and send to recipient list
     * 
     * @param string $body HTML for email body
     * @param int $order_id
     */
    private function sendEmail($body = '', $subject = 'POE-com Message') {
       
        if (!empty($body) && !empty($this->recipients)) {
            //get JMailer which uses PHPMailer
            jimport('joomla.mail.mail');
            $mail = new JMail();

            $sitename = JFactory::getApplication()->getCfg('sitename');
            $mail->setSender(array($this->sender, $sitename));
            $mail->addRecipient($this->recipients, '');
            
            $mail->setSubject($subject);

            //set shop logo
            if ($this->shop_logo) {
                $mail->AddEmbeddedImage($this->shop_logo, 'shop_logo', 'shoplogo.png', 'base64', 'image/png');
            }
            //set carrier logo
            if ($this->carrier_logo) {
                $mail->AddEmbeddedImage($this->carrier_logo, 'carrier_logo', 'carrierlogo.png', 'base64', 'image/png');
            }
            //set payment logo
            if (strlen($this->processor_logo)) {
                $mail->AddEmbeddedImage($this->processor_logo, 'processor_logo', 'processorlogo.png', 'base64', 'image/png');
            }

            $html = '<html><body>'.$body.'</body></html>';

            $mail->setBody($html);
            $mail->IsHTML(true);
            
             if ($mail->Send() === true) {
                return true;
            } else {
                return false;
            }
        }
    }
}
