<?php

?>
<table width="707">
    <tr><td colspan="2"><?php echo $title; ?></td></tr>
    <tr><td colspan="2"><?php echo $message; ?></td></tr>
    <tr><td colspan="2"><?php echo $number_label . ' : ' . $number; ?></td></tr>
    <tr>
        <td>
            <table>
                <tr>
                    <td><?php echo $shop_name; ?></td>
                    <td><?php echo $this->shop_address->name; ?></td> 
                    <td><?php echo $this->shop_address->street1; ?></td>
                    <td><?php echo $this->shop_address->street2; ?></td> 
                    <td><?php echo $this->shop_address->city . ', ' . $this->shop_address->region_name . ', ' . $this->shop_address->postal_code; ?></td> 
                    <td><?php echo $this->shop_address->country_name; ?></td> 
                    <td><?php echo $this->shop_address->telephone1; ?></td> 
                    <td><?php echo $this->shop_address->telephone2; ?></td> 
                    <td><?php echo shop_url; ?></td> 
                </tr>
            </table>
        </td>
        <td>
            <img src="<?php echo $this->shop_logo; ?>" alt="Shop Logo" />
        </td>
    </tr>
    <tr>
        <td>
            <table>
                <tr>
                    <td><?php echo JText::_('COM_POECOM_USER_BT_LBL'); ?></td>
                    <td><?php echo $data->bt->full_name; ?></td>
                    <td><?php echo $data->bt->street1; ?></td>
                    <td><?php echo $data->bt->street2; ?></td>
                    <td><?php echo $data->bt->city . ', ' . $data->bt->region . ', ' . $data->bt->postal_code; ?></td>
                    <td><?php echo $data->bt->country; ?></td>
                    <td><?php echo $data->bt->telephone; ?></td>
                    <td><?php echo $data->bt->email; ?></td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td><?php echo JText::_('COM_POECOM_USER_ST_LBL'); ?></td>
                    <td><?php echo $data->st->full_name; ?></td>
                    <td><?php echo $data->st->street1; ?></td>
                    <td><?php echo $data->st->street2; ?></td>
                    <td><?php echo $data->st->city . ', ' . $data->st->region . ', ' . $data->st->postal_code; ?></td>
                    <td><?php echo $data->st->country; ?></td>
                    <td><?php echo $data->st->telephone; ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table>
    <tr>
        <th><?php echo JText::_('COM_POECOM_CART_PROD_HD'); ?></th>
        <th><?php echo JText::_('COM_POECOM_CART_PRICE_HD'); ?></th>
        <th><?php echo JText::_('COM_POECOM_CART_QTY_HD'); ?></th>
        <th><?php echo JText::_('COM_POECOM_CART_LINE_TOTAL_HD'); ?></th>
    </tr>
    <tr>
        <td><?php
echo JText::_('COM_POECOM_CART_LINE_SKU') . ': ' . $itm->product_sku;
if ($itm->selected_options) {
    echo '<ul>';
    echo '<li>' . $itm->list_description . '<li>';
    foreach ($itm->selected_options as $op => $val) {
        echo '<li>' . $op . " : " . $val . '</li>';
    }
    echo '</ul>';
}
?></td>
        <td><?php echo number_format($itm->price, 2); ?></td>
        <td><?php echo $itm->quantity; ?></td>
        <td><?php echo number_format($itm->total, 2); ?></td>
    </tr>
</table>
<table>
    <tr>
        <td>
            <?php if (!empty($data->carrier)) { ?>
                <table>
                    <?php
                    if (strlen($this->carrier_logo)) {
                        echo '<tr><td><img src="' . $this->carrier_logo . '" alt="' . JText::_($data->carrier->carrier) . '"/></td></tr>';
                    } else {
                        echo '<tr><td>' . JText::_($data->carrier->carrier) . '</td></tr>';
                    }

                    echo '<tr><td>' . JText::_($data->carrier->service) . ' : ' . JText::_($data->carrier->eta) . '</td></tr>';
                } else {
                    echo '<tr><td>No Carrier Information</td></tr>';
                }
                ?>          </table>
        </td>
        <td>
            <table>

                <td><?php echo JText::_('COM_POECOM_CART_LINE_SUBTOTAL_LBL'); ?></td>
                <td><?php echo JText::_('COM_POECOM_CART_DISCOUNT_LBL'); ?></td>
                <td><?php echo JText::_('COM_POECOM_CART_PROD_TAX_LBL'); ?></td>
                <td><?php echo JText::_('COM_POECOM_CART_SHIP_COST_LBL'); ?></td>
                <td><?php echo JText::_('COM_POECOM_CART_SHIP_TAX_LBL'); ?></td>
                <td><?php echo JText::_('COM_POECOM_CART_TOTAL_LBL'); ?></td>
            </table>
            <table>
                <td><?php echo $data->currency[0]->symbol . number_format($data->subtotal, 2); ?></td>
                <td><?php echo number_format($data->discount_amount, 2); ?></td>
                <td><?php echo number_format($data->product_tax, 2); ?></td>
                <td><?php echo number_format($data->shipping_cost, 2); ?></td>
                <td><?php echo number_format($data->shipping_tax, 2); ?></td>
                <td><?php echo $data->currency[0]->symbol . number_format($data->total, 2); ?></td>
            </table>
        </td>
    </tr>
    <?php if(!empty($data->coupon_code)){ ?>
        <tr><td><?php echo JText::_('COM_POECOM_COUPON_CODE_USED_LBL') . ": " . $data->coupon_code; ?></td></tr>
    <?php } ?>
</table>
<?php if (!empty($data->payment)) { ?>
<table>
    <tr>
        <td>
            <table>
                    <?php
                    if (strlen($this->processor_logo)) {
                        echo '<tr><td><img src="' . $this->processor_logo . '" alt="' . JText::_($data->payment->name) . '"/></td></tr>';
                    } else {
                        echo '<tr><td>' . JText::_($data->payment->name) . '</td></tr>';
                    }
                    if ($data->payment->type != 3) { //Post checkout payment
                        echo '<tr><td>' . JText::_($data->payment->name) . ' : ' . JText::_('COM_POECOM_PAYMENT_STATUS') . ' - ' . $data->payment->status . '<td></tr>';
                        echo '<tr><td>' . JText::_('COM_POECOM_PAYMENT_TXN_ID') . ' : ' . $data->payment->transaction_number . '<td></tr>';
                        if ($data->payment->mandatory_fields) {
                            echo '<tr><td>' . JText::_('COM_POECOM_PAYMT_RECEIPT_TITLE') . '<td></tr>';
                            $html = '<tr><td>';
                            foreach ($data->payment->mandatory_fields as $k => $v) {
                                $html .= '<dd>' . $k . ' : ' . $v . '</dd>';
                            }
                            echo $html . '</td></tr>';
                        }
                    }
                    ?>
                </table>
        </td>
    </tr>
</table>
<?php }
if(!empty($this->return_policy) ){ ?>
<table>
    <tr>
        <th>
            <?php echo  JText::_('COM_POECOM_RETURN_POLICY_TITLE');?>
        </th>
    </tr>
    <tr>
        <td>
            <?php echo $this->return_policy; ?>
        </td>
    </tr>
</table>
<?php } ?>
