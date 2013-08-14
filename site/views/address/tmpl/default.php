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

if($this->enforce_cc_address === '1'){ ?>
<div id="enforce-cc-address-msg"><?php echo JText::_('COM_POECOM_CC_ADDRESS_REQD'); ?></div>
<?php } ?>
<div class="address<?php echo isset($this->pageclass_sfx)?$this->pageclass_sfx:'';?>">
    <form id="poecom-address" action="<?php echo JRoute::_('index.php?option=com_poecom&tmpl=component'); ?>" method="post" class="form-validate">
    <?php 
        $fs = $this->form->getFieldset('shiptobill');
        
        if(!empty($fs)){ ?>
        <fieldset> 
            <?php
            foreach ($fs as $f) { ?>
            <dt><?php echo strlen($f->label) ? $f->label . "  " : ''; ?></dt>
            <dd><?php echo $f->input; ?></dd>
        </fieldset>
          <?php }
        } ?>
        <fieldset id="st_fields">
        <dl>
        <?php
        //get array of form fields and display
        foreach ($this->form->getFieldset('addrfields') as $f) { ?>
          <dt><?php echo strlen($f->label) ? $f->label . "  " : ''; ?></dt>
          <dd><?php echo $f->input; ?></dd>
        <?php } ?>
      </dl>
        </fieldset>
        <div>
            <?php
            if($this->address_type == 'ST' && $this->enforce_cc_address == '1'){ ?>
                <button id="force-stbt" type="button" class="poe-button poe-corner-all" onclick="closeModal(true)"><?php echo JText::_('COM_POECOM_OK');?></button>
            <?php 
            }else{
            ?>
            <button id="address-submit" type="submit" class="validate poe-button poe-corner-all"><?php echo JText::_('COM_POECOM_UPDATE_ADDRESS');?></button>
            <button id="address-skip" type="button" class="poe-button poe-corner-all" onclick="closeModal(true)"><?php echo JText::_('COM_POECOM_UPDATE_ADDRESS');?></button>
            <?php echo JText::_('COM_POECOM_OR'); ?>
            <button id="address-cancel" type="button" class="poe-button poe-corner-all" onclick="closeModal(false)"><?php echo JText::_('JCANCEL');?></button>
            <?php } ?>
            <input type="hidden" name="option" value="com_poecom" />
            <input type="hidden" name="task" value="address.save" />
            <input type="hidden" name="enforce_cc_address" id="enforce_cc_address" value="<?php echo $this->enforce_cc_address; ?>" />
            <input type="hidden" name="Itemid" id="Itemid" value="<?php echo $this->cart_itemid; ?>" />
        <?php if($this->updated == 1){ ?>
            <input type="hidden" id="bypass" value="1" />
        <?php }
            foreach ($this->form->getFieldset('hidden') as $f) {
                echo $f->input;
            }
            echo JHtml::_('form.token');?>
        </div>
    </form>
</div>
<script src="<?php echo $this->script?>" type="text/javascript"></script>
