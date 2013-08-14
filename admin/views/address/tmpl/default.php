<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Address Template
 * 
 * @package com_poecom
 * @author Micah Fletcher
 * @copyright 2010 - 2012 (C) Extensible Point Solutions Inc. All Right Reserved.
 * @license GNU GPLv3, http://www.gnu.org/licenses/gpl.html
 *
 * @since 2.0 - 3/11/2012 4:18:55 PM
 *
 * http://www.exps.ca
**/

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<style>
#jform_stbt_same{
    margin: 0;
    padding: 0;
}

#jform_stbt_same label{
    margin-left: 0.75em;
    margin-right: 0.75em;
}


#address-skip, #address-cancel{
    cursor: pointer;
    padding: 3px 5px 3px 7px;
    font-weight: bold;
    line-height: 1.2em;
    font-family: arial;
}

</style>
<div class="address<?php echo $this->pageclass_sfx?>">
	<form id="poecom-address" action="<?php echo JRoute::_('index.php?option=com_poecom&tmpl=component'); ?>" method="post" class="form-validate">
<?php foreach ($this->form->getFieldsets() as $fieldset){ 
	 $fields = $this->form->getFieldset($fieldset->name);
	 if (count($fields)){
        if($fieldset->name == 'shiptofields'){ ?>
            <fieldset id="st_fields">
        <?php }else{ ?>    
            <fieldset>
		<?php }
        
		if (isset($fieldset->label)){?>
			<legend><?php echo JText::_($fieldset->label);?></legend>
		<?php } ?>
			<dl>
		<?php foreach($fields as $field){ ?>
				<dt>
				<?php echo $field->label; ?>
				</dt>
				<dd><?php echo $field->input;?></dd>
		<?php } ?>
			</dl>
		</fieldset>
<?php }
} ?>
        
		<div>
			<button id="address-submit" type="submit" class="validate"><?php echo JText::_('COM_POECOM_UPDATE_ADDRESS');?></button>
            <button id="address-skip" type="button" onclick="closeModal(true)"><?php echo JText::_('COM_POECOM_UPDATE_ADDRESS');?></button>
			<?php echo JText::_('COM_POECOM_OR');?>
			<button id="address-cancel" type="button" onclick="closeModal(false)"><?php echo JText::_('JCANCEL');?></button>
			<input type="hidden" name="option" value="com_poecom" />
			<input type="hidden" name="task" value="address.save" />
            <?php if($this->updated == 1){ ?>
                <input type="hidden" id="bypass" value="1" />
            <?php }?>
			<?php echo JHtml::_('form.token');?>
		</div>
	</form>
</div>
<script src="<?php echo $this->script?>" type="text/javascript"></script>