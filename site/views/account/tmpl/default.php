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
<div id="dialog" title="">
    <div id="loadingDivContainer">
        <div id="loadingDiv"></div>
        <div id="dialogText"></div>
    </div>
</div>
<div id="accountinfo">
     <?php foreach ($this->form->getFieldsets() as $fieldset){ 
         $header_title = '';
         $action = 'index.php?option=com_poecom';
         $onclick = 'onclick="update';
         $update_button_text = '';
         switch($fieldset->name){
             case 'profile':
                 $header_title = JText::_('COM_POECOM_FS_ACCT_PROFILE');
                 $action .= '&task=account.updateUserInfo&tmpl=component';
                 $onclick .= 'Profile()"';
                 $update_button_text = JText::_('COM_POECOM_FS_ACCT_UPDATE_PROFILE');
                 break;
             case 'billtofields':
                 $header_title = JText::_('COM_POECOM_FS_ACCT_BT');
                 $action .= '&task=account.updateAddress&tmpl=component';
                 $onclick .= 'BT()"';
                 $update_button_text = JText::_('COM_POECOM_FS_ACCT_UPDATE_BT');
                 break;
             case 'shiptofields':
                 $header_title = JText::_('COM_POECOM_FS_ACCT_ST');
                 $action .= '&task=account.updateAddress&tmpl=component';
                 $onclick .= 'ST()"';
                 $update_button_text = JText::_('COM_POECOM_FS_ACCT_UPDATE_ST');
                 break;
             case 'requests':
                 $header_title = JText::_('COM_POECOM_FS_ACCT_REQUESTS');
                 //$action .= '&task=account.updateUserInfo&tmpl=component';
                 break;
             case 'orders':
                 $header_title = JText::_('COM_POECOM_FS_ACCT_ORDERS');
                 //$action .= '&task=account.updateUserInfo&tmpl=component';
                 break;
             default:
                 break;
         }
	 $fields = $this->form->getFieldset($fieldset->name);
	 if (count($fields)){ ?>
            <h3><?php echo $header_title ?></h3>
            <div>
            <?php  if($fieldset->name == 'profile' ||  $fieldset->name == 'billtofields' || $fieldset->name == 'shiptofields'){ ?>
                <form id="poecom-account-<?php echo $fieldset->name?>" action="<?php echo JRoute::_($action); ?>" method="post" class="form-validate">
                
            <?php foreach($fields as $field){ ?>
                    
                    <dt class="account-dt"><?php echo $field->label; ?></dt>
                    <dd class="account-dd"><?php
                        if($field->id == 'jform_region_id'){
                            echo $this->form->getInput('region_id','',
                            array($this->form->getValue('country_id', '', 38),
                            $this->form->getValue('region_id', '', 0) ));
                        }else if($field->id == 'jform_stregion_id'){
                            echo $this->form->getInput('stregion_id','',
                            array($this->form->getValue('stcountry_id', '', 38),
                            $this->form->getValue('stregion_id', '', 0) ));
                        }else{
                            echo $field->input;
                        }?></dd>
            <?php } ?>
                    </dl>
                    <button class="poe-button poe-corner-all" type="button" id="update<?php echo $fieldset->name ?>" <?php echo $onclick ?> ><?php echo $update_button_text ?></button>
                    
                </form>
                <?php } ?>
            </div>
            <?php }
     } ?>
            <h3><?php echo JText::_('COM_POECOM_ACCT_REQUESTS') ?></h3>  
            <div>
                <div id="acct-request-hd">
                    <div class="acct-rfq"><?php echo JText::_('COM_POECOM_ACCT_RFQ_NUM_H')?></div>
                    <div class="acct-status"><?php echo JText::_('COM_POECOM_ACCT_RFQ_STATUS_H')?></div>
                    <div class="acct-order"><?php echo JText::_('COM_POECOM_ACCT_RFQ_ORDER_H')?></div>
                    <div class="acct-date"><?php echo JText::_('COM_POECOM_ACCT_RFQ_DATE_H')?></div>
                    <div class="acct-total"><?php echo JText::_('COM_POECOM_ACCT_RFQ_TOTAL_H')?></div>
                    <div class="acct-curr"><?php echo JText::_('COM_POECOM_ACCT_RFQ_CUR_H')?></div>
                </div>
               <?php if($this->requests){ 
                   foreach($this->requests as $r) {
                       $onclick = " onclick=\"openPrintView('".$r->number."')\""; ?>
                <div class="acct-rfq-wrap">
                    <div class="acct-rfq"><?php echo $r->number ?><button type="button" id="rfqview<?php $idx ?>" <?php echo $onclick ?>>View</button></div>
                    <div class="acct-status"><?php echo $r->status_name ?></div>
                    <div class="acct-order"><?php echo $r->order_id ?></div>
                    <div class="acct-date"><?php echo $r->date ?></div>
                    <div class="acct-total"><?php echo $r->total ?></div>
                    <div class="acct-curr"><?php echo $r->currency_code ?></div>
                </div>
               <?php 
                   }
               } ?>
            </div>
            <h3><?php echo JText::_('COM_POECOM_ACCT_ORDERS') ?></h3>  
            <div>
                <div id="acct-order-hd">
                    <div class="acct-order"><?php echo JText::_('COM_POECOM_ACCT_RFQ_ORDER_H')?></div>
                    <div class="acct-date"><?php echo JText::_('COM_POECOM_ACCT_RFQ_DATE_H')?></div>
                    <div class="acct-status"><?php echo JText::_('COM_POECOM_ACCT_RFQ_STATUS_H')?></div>
                    <div class="acct-rfq"><?php echo JText::_('COM_POECOM_ACCT_RFQ_NUM_H')?></div>
                    <div class="acct-ship"><?php echo JText::_('COM_POECOM_ACCT_ORDER_SHIP_H')?></div>
                    <div class="acct-total"><?php echo JText::_('COM_POECOM_ACCT_RFQ_TOTAL_H')?></div>
                </div>
                <?php if($this->orders){ 
                    $idx=1;
                    foreach($this->requests as $o) {
                        $onclick = " onclick=\"openPrintView('".$o->id."')\"";?>
                 <div class="acct-order-wrap">
                     <div class="acct-order"><?php echo $o->id ?><button type="button" id="orderview<?php $idx ?>" <?php echo $onclick ?> >View</button></div>
                    <div class="acct-date"><?php echo $o->order_date ?></div>
                    <div class="acct-status"><?php echo $o->status_name ?></div>
                    <div class="acct-rfq"><?php echo $o->rfq_number ?></div>
                    <div class="acct-ship"><?php echo $o->shipping ?></div>
                    <div class="acct-total"><?php echo $o->total ?></div>
                </div>
               <?php 
                    $idx++;
                   }
               } ?>
            </div>
</div>
<input type="hidden" name="juser_id" id="juser_id" value="<?php echo $this->juser_id ?>"/>
<div id="jtoken">
    <?php echo JHtml::_('form.token');?>
</div>
<script src="<?php echo $this->script?>" type="text/javascript"></script>
