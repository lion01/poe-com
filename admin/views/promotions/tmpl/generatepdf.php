<?php
defined('_JEXEC') or die('Restricted Access');
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
JHtml::_('behavior.modal');
?>
<div id="coupon-gen-msg">
<?php if($this->pdf_created){
    //message after PDF generate
    echo JText::_('COM_POECOM_PDF_GENERATED_MSG');
}else if(!$this->has_coupons && $this->error['error']){
    //message if coupons not generated yet
    echo $this->error['msg'];
}else{
    //show the form
    if($this->error['error']){
	echo $this->error['msg'];
    }?>
</div>
<div id="gen-promo">
    <dl>
	<dt><?php echo JText::_('COM_POECOM_PROMOTION_DETAILS'); ?><dt>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_ID')." : ".$this->promotion->id; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_NAME_LABEL')." : ".$this->promotion->name; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_TYPE_LABEL')." : ".$this->promotion->promotion_type; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_DISCOUNT_TYPE_LABEL')." : ".$this->promotion->discount_type; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_DISCOUNT_AMT_TYPE_LABEL')." : ".$this->promotion->discount_amount_type; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_DISCOUNT_AMT_LABEL')." : ".$this->promotion->discount_amount; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_ORDER_AMT_MIN_LABEL')." : ".$this->promotion->order_amount_min; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_PRODUCT_QTY_MIN_LABEL')." : ".$this->promotion->product_qty_min; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_MAX_VALUE_LABEL')." : ".$this->promotion->max_value; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_START_LABEL')." : ".$this->promotion->start_time; ?></dd>
	<dd><?php echo JText::_('COM_POECOM_PROMOTION_FIELD_END_LABEL')." : ".$this->promotion->end_time; ?></dd>
    </dl>
</div>
<div id="promo-instruction">
    <?php echo JText::_('COM_POECOM_PROMOTION_PDF_INSTR'); ?>
</div>

<div>
<form action="<?php echo JRoute::_('index.php?option=com_poecom&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="promo-direct">
	<div><label><?php echo JText::_('COM_POECOM_PROMOTION_PDF_TEMPLATE_LABEL'); ?></label></div>
	<div><?php echo JHTML::_('select.genericList', $this->templates, 'pdf_template', null, 'id', 'name'); ?></div>
    </div>
    <div id="promo-fields">
	<input class="poe-button poe-corner-all" type="submit" name="submit" value="<?php echo JText::_('COM_POECOM_GENERATE_PDF') ?>" />
	<input type="hidden" name="promotion_id" id="promotion_id" value="<?php echo $this->promotion->id;?>" />
	<input type="hidden" name="promotion_type_id" id="promotion_type_id" value="<?php echo $this->promotion->promotion_type_id;?>" />
        <input type="hidden" name="task" value="generatepdf" />
	<input type="hidden" name="layout" value="generatepdf" />
        <input type="hidden" name="view" value="promotions" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
</div>
<?php } ?>
<div id="dev-foot"><div>Developed by <a href="http://www.exps.ca" target="_blank">Extensible Point Solutions Inc.</a> Copyright 2012 - All Rights Reserved</div></div>
<script src="<?php echo $this->script?>" type="text/javascript"></script>