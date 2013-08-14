<?php
defined('_JEXEC') or die('Restricted access');
/**
 * POE-com - Site - Orders Template
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
/*
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<div class="account<?php echo $this->pageclass_sfx?>">
    <form id="poecom-account" action="<?php echo JRoute::_('index.php?option=com_poecom&tmpl=component'); ?>" method="post" class="form-validate">
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
                    <dd><?php
                        if($field->id == 'jform_region_id'){
                            echo $this->form->getInput('region_id','',
                            array($this->form->getValue('country_id', '', 38),
                            $this->form->getValue('region_id', '', 0) ));
                        }else{
                            echo $field->input;
                        }?></dd>
            <?php } ?>
                    </dl>
            </fieldset>
        <?php }
        } ?>

        <div>
            <button id="account-submit" type="submit" class="validate poe-button poe-corner-all"><?php echo JText::_('COM_POECOM_UPDATE_ADDRESS');?></button>
            <button id="account-skip" type="button" class="poe-button poe-corner-all" onclick="closeModal(true)"><?php echo JText::_('COM_POECOM_UPDATE_ADDRESS');?></button>
            <?php echo JText::_('COM_POECOM_OR');?>
            <button id="account-cancel" type="button" class="poe-button poe-corner-all" onclick="closeModal(false)"><?php echo JText::_('JCANCEL');?></button>
            <input type="hidden" name="option" value="com_poecom" />
            <input type="hidden" name="task" value="account.save" />
        <?php if($this->updated == 1){ ?>
            <input type="hidden" id="bypass" value="1" />
        <?php }?>
            <?php echo JHtml::_('form.token');?>
        </div>
    </form>
</div>
<script src="<?php echo $this->script?>" type="text/javascript"></script>
*/