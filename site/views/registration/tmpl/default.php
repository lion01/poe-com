<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<div id="dialog" title="">
    <div id="loadingDivContainer">
        <div id="dialogText"></div>
    </div>
</div>
<?php if($this->enforce_cc_address === '1'){ ?>
<div id="enforce-cc-address-msg"><?php echo JText::_('COM_POECOM_CC_ADDRESS_REQD'); ?></div>
<?php } ?>
<div class="registration<?php echo isset($this->pageclass_sfx)?$this->pageclass_sfx:'';?>">
	<form id="poecom-registration" action="<?php echo JRoute::_('index.php?option=com_poecom&task=registration.register&tmpl=component'); ?>" method="post" class="form-validate">
             <?php ?>
            <fieldset>
                <dl>
                    <?php
                    //get array of form fields and display
                    foreach ($this->form->getFieldset('userinfo') as $f) { ?>
                      <dt><?php echo strlen($f->label) ? $f->label . "  " : ''; ?></dt>
                      <dd><?php echo $f->input; ?></dd>
                    <?php } ?>
                  </dl>
            </fieldset>
        <div>
            <button type="submit" class="validate poe-button poe-corner-all"><?php echo JText::_('COM_POECOM_UPDATE_BILLING');?></button>
            <?php echo JText::_('COM_POECOM_OR');?>
            <button id="address-cancel" type="button" class="poe-button poe-corner-all" onclick="closeModal(false)"><?php echo JText::_('JCANCEL');?></button>
            <input type="hidden" name="option" value="com_poecom" />
            <input type="hidden" name="task" value="registration.register" />
            <input type="hidden" name="enforce_cc_address" value="<?php echo $this->enforce_cc_address; ?>" />
            <input type="hidden" id="ItemId" value="<?php echo $this->ItemId ?>" />
            <?php echo JHtml::_('form.token');?>
        </div>
    </form>
</div>
<script src="<?php echo $this->script?>" type="text/javascript"></script>