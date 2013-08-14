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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$fs = $this->form->getFieldSets();

$order_header = $this->form->getFieldSet('orderheader');
$order_value = $this->form->getFieldSet('ordervalue');
$promo = $this->form->getFieldset('orderpromotion');
$bt = $this->form->getFieldset('btaddress');
$st = $this->form->getFieldset('staddress');
$btst_fields = $this->form->getFieldset('shiptobill');
$carrier = $this->form->getFieldset('ordercarrier');
?>
<form action="index.php?option=com_poecom&layout=edit&id=<?php (int) $this->item->id; ?>" method="post" name="adminForm" id="order-form" class="form-validate" >
    <div class="width-30 fltlft">
        <?php if ($order_header) { ?>
            <fieldset class="adminform">
            <?php if (!empty($fs['orderheader']->label)) { ?>
                    <legend><?php echo JText::_($fs['orderheader']->label); ?></legend>
                <?php } ?>
                <dl>
                <?php foreach ($order_header as $f) { ?>
                        <dt>
                        <?php echo $f->label; ?>
                        </dt>
                        <dd><?php echo $f->input; ?>
                        </dd>
    <?php } ?>
                </dl>
            </fieldset>
<?php } ?>
    </div>
    <div class="width-30 fltlft">
<?php if ($order_value) {
    ?>
            <fieldset class="adminform">
                <?php if (!empty($fs['ordervalue']->label)) { ?>
                    <legend><?php echo JText::_($fs['ordervalue']->label); ?></legend>
                <?php } ?>
                <dl>
                    <?php foreach ($order_value as $f) { ?>
                        <dt>
                        <?php echo $f->label; ?>
                        </dt>
                        <dd><?php echo $f->input; ?>
                        </dd>
                    <?php } ?>
                </dl>
            </fieldset>
        <?php } ?>
        
    </div>
    <div class="width-30 fltlft" style="clear: left;">
        <?php if ($promo) { ?>
                <fieldset class="adminform">
                <?php if (!empty($fs['orderpromotion']->label)) { ?>
                        <legend><?php echo JText::_($fs['orderpromotion']->label); ?></legend>
                    <?php } ?>
                    <dl>
                    <?php foreach ($promo as $f) { ?>
                            <dt><?php echo $f->label; ?></dt>
                            <dd><?php echo $f->input; ?></dd>
                    <?php } ?>
                    </dl>
                </fieldset>
        <?php } ?>
    </div>
    <div class="width-30 fltlft">
        <?php if ($carrier) { ?>
                <fieldset class="adminform">
                <?php if (!empty($fs['ordercarrier']->label)) { ?>
                        <legend><?php echo JText::_($fs['ordercarrier']->label); ?></legend>
                    <?php } ?>
                    <dl>
                    <?php foreach ($carrier as $f) { ?>
                            <dt><?php echo $f->label; ?></dt>
                            <dd><?php echo $f->input; ?></dd>
                    <?php } ?>
                    </dl>
                </fieldset>
        <?php } ?>
    </div>
    <div class="width-30 fltlft" style="clear: left;">
        <?php if ($bt) { ?>
                <fieldset class="adminform">
                <?php if (!empty($fs['btaddress']->label)) { ?>
                        <legend><?php echo JText::_($fs['btaddress']->label); ?></legend>
                    <?php } ?>
                    <dl>
                    <?php foreach ($bt as $f) { ?>
                            <dt><?php echo $f->label; ?></dt>
                            <dd><?php echo $f->input; ?></dd>
                    <?php } ?>
                            <dd  style="clear:left;"><button type="button" id="updateBT" onclick="updateAddress('BT')"><?php echo JText::_('COM_POECOM_UPDATE_BT_BUTTON'); ?></button></dd>
                    </dl>
                </fieldset>
        <?php } ?>
        </div>
        <div class="width-30 fltlft">
            <fieldset class="adminform">
                <?php
                if (!empty($fs['staddress']->label)) { ?>
                        <legend><?php echo JText::_($fs['staddress']->label); ?></legend>
                    <?php } ?>
                <dl>
                        <dt><?php echo $btst_fields['jform_stbt_same']->label;?></dt>
                        <dd><?php echo $btst_fields['jform_stbt_same']->input;?></dd>
                        <dd><button type="button" id="updateBT" onclick="updateAddress('ST')"><?php echo JText::_('COM_POECOM_UPDATE_ST_BUTTON'); ?></button></dd>
                </dl>
            </fieldset>
        <?php if ($st) { ?>
            
                <fieldset id="st_fields" class="adminform">
                    <dl>
                    <?php foreach ($st as $f) { ?>
                            <dt><?php echo $f->label; ?></dt>
                            <dd><?php echo $f->input; ?></dd>
                    <?php } ?>
                    </dl>
                    
                </fieldset>
        <?php } ?>
        </div>
    <div class="width-60 fltlft" style="clear: left;">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_POECOM_ORDER_LINES'); ?></legend>
            <div id="order-list-hd">
                <ul>
                    <li><div id="order-prod-hd"><?php echo JText::_('COM_POECOM_ORDER_PROD_HD') ?></div></li>
                    <li><div id="order-price-hd"><?php echo JText::_('COM_POECOM_ORDER_PRICE_HD') ?></div></li>
                    <li><div id="order-qty-hd"><?php echo JText::_('COM_POECOM_ORDER_QTY_HD') ?></div></li>
                    <li><div id="order-line-total-hd"><?php echo JText::_('COM_POECOM_ORDER_LINE_TOTAL_HD') ?></div></li>
                </ul>
            </div>
            <?php
            $items = $this->item->lines;
          
            if ($items) {
                $idx = 0;
                foreach ($items as $itm) {
                    $html = '<div class="order-item">';
                    $html .= '<div class="order-item-id">';
                    $html .= '<div>'.$itm->product_detail->name.'</div>';
                    $html .= '<div>' . JText::_('COM_POECOM_ORDER_LINE_SKU') . ': ' . $itm->sku.'</div>';
                    $html .= '<div>'.$itm->product_detail->list_description.'</div>';
                    if (!empty($itm->product_detail->properties)) {
                        $html .= '<ul>';

                        foreach ($itm->product_detail->properties as $p) {
                            $html .= '<li>' . $p->name . " : " . $p->option_label . '</li>';
                        }
                    }
                    if (!empty($itm->selected_options)) {
                        $html .= '<ul>';

                        foreach ($itm->selected_options as $op => $val) {
                            $html .= '<li>' . $op . " : " . $val . '</li>';
                        }
                    }
                    $html .= '</ul><div><button class="poebutton" type="button" name="change_item" id="change_item" onclick="orderChangeItem(' . $idx . ')" title="' . JText::_('COM_POECOM_ORDER_LINE_CHANGE') . '">Change</button></div>';

                    $html .='</div>';
                    $html .= '<div class="order-item-price">' . number_format($itm->price, 2) . '</div>';
                    $html .= '<div class="order-item-qty"><div>' . $itm->quantity . '</div>';

                    $html .= '<div><button class="poedeletebutton" type="button" name="delete_item" id="delete_item" onclick="orderDeleteItem(' . $idx . ')" title="' . JText::_('COM_POECOM_ORDER_LINE_DELETE') . '">X</button></div>';

                    $html .= '</div>';
                    $html .= '<div class="order-item-total">' . number_format($itm->total, 2) . '</div>';
                    $html .= '</div>';

                    echo $html;

                    $idx++;
                }
            }
            ?>
        </fieldset>
    </div>
    <div class="width-40 fltrt">
        
        
        
    </div>
    <div>
        <?php
        foreach ($this->form->getFieldset('hidden') as $field) {
            echo $field->input;
        } ?>
        <input type="hidden" name="task" value="order.edit" />
        <div id="jtoken"><?php echo JHTML::_('form.token'); ?></div>
    </div>
</form>
<script src="<?php echo $this->script ?>" type="text/javascript"></script>
