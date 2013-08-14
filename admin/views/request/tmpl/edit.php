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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form action="index.php?option=com_poecom&layout=edit&id=<?php echo $this->item->id; ?>" method="post" name="adminForm" id="request-form" class="form-validate" >
    <div class="width-30 fltlft">
    <?php
         $fieldsets = $this->form->getFieldSets();
	 $rfq_record_fields = $this->form->getFieldset('rfqrecord');
	 if (!empty($rfq_record_fields)){ ?>
        <fieldset class="adminform">
        <?php if (isset($fieldsets['rfqrecord']->label)){?>
            <legend><?php echo JText::_($fieldsets['rfqrecord']->label);?></legend>
        <?php } ?>
            <dl>
        <?php foreach($rfq_record_fields as $field){ ?>
                <dt>
                <?php echo $field->label; ?>
                </dt>
                <dd><?php echo $field->input;?></dd>
        <?php } ?>
            </dl>
        </fieldset>
<?php } ?>
    </div>
    <div class="width-30 fltlft">
        <?php $rfq_value_fields = $this->form->getFieldset('rfqvalue'); ?>
            <fieldset class="adminform" >
                <?php if (isset($fieldsets['rfqvalue']->label)){?>
            <legend><?php echo JText::_($fieldsets['rfqvalue']->label);?></legend>
        <?php } ?>
                <ul class="adminformlist">
                    <li><?php echo $rfq_value_fields['jform_subtotal']->label .
                            $this->form->getInput('subtotal','',  number_format($this->item->rfq_cart->subtotal,2)); ?>
                    </li>
		    <li><?php echo $rfq_value_fields['jform_discounttotal']->label .
                            $this->form->getInput('discounttotal','',  number_format($this->item->rfq_cart->discount_amount,2)); ?>
                    </li>
                    <li><?php echo $rfq_value_fields['jform_product_tax']->label .
                            $this->form->getInput('product_tax','',  number_format($this->item->rfq_cart->product_tax,2)); ?>
                    </li>
                    <li><?php echo $rfq_value_fields['jform_shipping_cost']->label .
                            $this->form->getInput('shipping_cost','',  number_format($this->item->rfq_cart->shipping_cost,2)); ?>
                    </li>
                    <li><?php echo $rfq_value_fields['jform_shipping_tax']->label .
                            $this->form->getInput('shipping_tax','',  number_format($this->item->rfq_cart->shipping_tax,2)); ?>
                    </li>
                    <li><?php echo $rfq_value_fields['jform_total']->label .'<div style="float:left;">'.
                            $this->form->getInput('total','',  number_format($this->item->rfq_cart->total, 2)).
                            '<label style="min-width: 20px; clear: none;">'.$this->item->currency->code.'</label></div>';?>
                    </li>
                    </ul>
            </fieldset>
    </div>
    <div class="width-30 fltlft" style="clear: both;">
       
        <fieldset class="adminform" >
             
            <legend><?php echo JText::_('COM_POECOM_USER_BT');?></legend>
        <?php if(!empty($this->item->user_bt)){ 
            $bt = $this->item->user_bt;?>
            <ul class="adminformlist">
                <li><label><?php echo JText::_('COM_POECOM_USER_FNAME'); ?></label>
                    <input name="bt[fname]" id="bt_fname" value="<?php echo $bt->fname; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_LNAME'); ?></label>
                    <input name="bt[lname]" id="bt_lname" value="<?php echo $bt->lname; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_STREET1'); ?></label>
                    <input name="bt[street1]" id="bt_street1" value="<?php echo $bt->street1; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_STREET2'); ?></label>
                    <input name="bt[street2]" id="bt_street2" value="<?php echo $bt->street2; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_CITY'); ?></label>
                    <input name="bt[city]" id="bt_city" value="<?php echo $bt->city; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_COUNTRY'); ?></label>
                    <?php echo JHTML::_('select.genericList', $this->countries, 'bt[country_id]', 'onchange="updateRegions(\'BT\')"', 'value', 'text', $bt->country_id, 'bt_country_id'); ?>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_REGION'); ?></label>
                    <?php echo JHTML::_('select.genericList', $this->regions, 'bt[region_id]', null, 'value', 'text', $bt->region_id, 'jform_region_id'); ?>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_POSTAL'); ?></label>
                    <div id="regions"><input name="bt[postal_code]" id="bt_postal_code" value="<?php echo $bt->postal_code; ?>"/></div>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_TELEPHONE'); ?></label>
                    <input name="bt[telephone]" id="bt_telephone" value="<?php echo $bt->telephone; ?>"/>
                </li>
                </ul>
             <?php } ?>
        </fieldset>
    </div>
    <div class="width-30 fltlft">
        <?php
        
        $rfq_st_fields = $this->form->getFieldset('rfqstaddess'); ?>
       
        <fieldset id="st_fields" class="adminform" >
            <?php if (isset($fieldsets['rfqstaddess']->label)){?>
            <legend><?php echo JText::_($fieldsets['rfqstaddess']->label);?></legend>
        <?php } ?>
            <ul class="adminformlist">
                <?php
                $st = !empty($this->item->user_st)?$this->item->user_st:0;
                if(empty($st)){ 
                    $stbtSame = 1;
                    $st_fname = '';
                    $st_lname = '';
                    $st_street1 = '';
                    $st_street2 = '';
                    $st_city = '';
                    $st_country_id = '';
                    $st_region_id = '';
                    $st_postalcode = '';
                    $st_telephone = '';
                }else{
                    //different ST
                    $stbtSame = 0;
                    $st_fname = $st->fname;
                    $st_lname = $st->lname;
                    $st_street1 = $st->street1;
                    $st_street2 = $st->street2;
                    $st_city = $st->city;
                    $st_country_id = $st->country_id;
                    $st_region_id = $st->region_id;
                    $st_postalcode = $st->postal_code;
                    $st_telephone = $st->telephone;
                }
                ?>
                <li><?php echo $rfq_st_fields['jform_stbt_same']->label .
                            $this->form->getInput('stbt_same','',  $stbtSame); ?>
                    </li>
                    <div id="stfields">
                <li><label><?php echo JText::_('COM_POECOM_USER_FNAME'); ?></label>
                    <input name="st[fname]" id="st_fname" value="<?php echo $st_fname; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_LNAME'); ?></label>
                    <input name="st[lname]" id="st_lname" value="<?php echo $st_lname; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_STREET1'); ?></label>
                    <input name="st[street1]" id="st_street1" value="<?php echo $st_street1; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_STREET2'); ?></label>
                    <input name="st[street2]" id="st_street2" value="<?php echo $st_street2; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_CITY'); ?></label>
                    <input name="st[city]" id="st_city" value="<?php echo $st_city; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_COUNTRY'); ?></label>
                    <?php echo JHTML::_('select.genericList', $this->countries, 'st[country_id]', 'onchange="updateRegions(\'ST\')"', 'value', 'text', $st_country_id, 'st_country_id');  ?>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_REGION'); ?></label>
                    <?php echo JHTML::_('select.genericList', $this->regions, 'st[region_id]', null, 'value', 'text', $st_region_id, 'jform_st_region_id'); ?>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_POSTAL'); ?></label>
                    <input name="st[postal_code]" id="st_postal_code" value="<?php echo $st_postalcode; ?>"/>
                </li>
                <li><label><?php echo JText::_('COM_POECOM_USER_TELEPHONE'); ?></label>
                    <input name="st[telephone]" id="st_telephone" value="<?php echo $st_telephone; ?>"/>
                </li></div>
                </ul>
        </fieldset>
    </div>
    <div class="width-60 fltlft" style="clear: both;">
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
        $items = $this->item->rfq_cart->items;
            if($items){
                $idx = 0;
                foreach($items as $itm){
                    $html = '<div class="order-item">';
                    $html .= '<div class="order-item-id">'.JText::_('COM_POECOM_ORDER_LINE_SKU').': '.$itm->product_sku;
                        if($itm->selected_options){
                            $html .= '<ul>';
                        
                            foreach($itm->selected_options as $op => $val){
                                $html .= '<li>'.$op. " : ". $val. '</li>';
                            }
                        }
                    $html .= '</ul><div><button class="poebutton" type="button" name="change_item" id="change_item" onclick="orderChangeItem('.$idx.')" title="'.JText::_('COM_POECOM_ORDER_LINE_CHANGE').'">Change</button></div>';
                            
                    $html .='</div>';
                    $html .= '<div class="order-item-price">'.number_format($itm->price, 2).'</div>';
                    $html .= '<div class="order-item-qty"><div>'.$itm->quantity.'</div>';
                    
                    $html .= '<div><button class="poedeletebutton" type="button" name="delete_item" id="delete_item" onclick="orderDeleteItem('.$idx.')" title="'.JText::_('COM_POECOM_ORDER_LINE_DELETE').'">X</button></div>';
                    
                    $html .= '</div>';
                    $html .= '<div class="order-item-total">'.number_format($itm->total, 2).'</div>';
                    $html .= '</div>';
                    
                    echo $html;
                    
                    $idx++;
                }
            }
        ?>
        </fieldset>
    </div>
    <div class="width-30 fltlft" style="clear: both;">
    <?php $rfq_promo_fields = $this->form->getFieldset('rfqpromotion'); ?>
            <fieldset class="adminform" >
                <?php if (isset($fieldsets['rfqpromotion']->label)){?>
            <legend><?php echo JText::_($fieldsets['rfqpromotion']->label);?></legend>
        <?php } ?>
                <ul class="adminformlist">
                    <?php
                        $promo_name = !empty($this->item->promotion->name)?$this->item->promotion->name:'';
                        $promo_coupon = !empty($this->item->promotion->coupon_code)?$this->item->promotion->coupon_code:'';
                    ?>
                    <li><?php echo $rfq_promo_fields['jform_promotionname']->label .
                            $this->form->getInput('promotionname','',  $promo_name); ?>
                    </li>
                    <li><?php echo $rfq_promo_fields['jform_couponcode']->label .
                            $this->form->getInput('couponcode','',  $promo_coupon); ?>
                    </li>
                    </ul>
            </fieldset>
    </div>
    <div class="width-30 fltlft">
        <?php $rfq_carrier_fields = $this->form->getFieldset('rfqcarrier'); ?>
        <fieldset class="adminform" >
            <?php if (isset($fieldsets['rfqcarrier']->label)){?>
            <legend><?php echo JText::_($fieldsets['rfqcarrier']->label);?></legend>
        <?php } ?>
            <ul class="adminformlist">
                <li><?php echo $rfq_carrier_fields['jform_carrier']->label .
                        $this->form->getInput('carrier','',  $this->item->rfq_cart->selected_shipping->carrier); ?>
                </li>
                <li><?php echo $rfq_carrier_fields['jform_carrier_logo']->label .
                        $this->form->getInput('carrier_logo','', $this->item->rfq_cart->selected_shipping->carrier_logo); ?>
                </li>
                <li><?php echo $rfq_carrier_fields['jform_service']->label .
                        $this->form->getInput('service','',  $this->item->rfq_cart->selected_shipping->service); ?>
                </li>
                    <li><?php echo $rfq_carrier_fields['jform_eta']->label// .
                    //   $this->form->getInput('eta','',  $this->item->rfq_cart->selected_shipping->eta); ?>
                </li>
                </ul>
        </fieldset>
    </div>
    <div>
        <?php
        //add hidden fields
        foreach($this->form->getFieldset('hidden') as $field){ 
           echo $field->input;
        }
        ?>
        <input type="hidden" name="task" value="request.edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<script src="<?php echo $this->script?>" type="text/javascript"></script>