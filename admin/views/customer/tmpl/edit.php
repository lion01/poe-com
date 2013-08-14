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
//JHtml::_('behavior.formvalidation');
?>
<div class="width-30 fltlft">
    <?php
    $fieldsets = $this->form->getFieldSets();
    $detail_fields = $this->form->getFieldset('details');
    if (!empty($detail_fields)) { ?>
        <fieldset class="adminform">
            <?php if (isset($fieldsets['details']->label)) { ?>
                <legend><?php echo JText::_($fieldsets['details']->label); ?></legend>
            <?php } ?>
            <dl>
                <?php foreach ($detail_fields as $field) { ?>
                    <dt><?php echo $field->label; ?></dt>
                    <dd><?php echo $field->input; ?></dd>
                <?php } ?>
                <div class="ajax-button">
                    <button type="button" id="update_user" onclick="updateUser()"><?php echo JText::_('COM_POECOM_UPDATE');?></button>
                </div>
            </dl>
        </fieldset>
    <?php } ?>
</div>
<div class="width-30 fltlft">
    <?php
    $bt_address = $this->form->getFieldset('btaddress');
    if (!empty($bt_address)) {
        ?>
        <fieldset class="adminform">
            <?php if (isset($fieldsets['btaddress']->label)) { ?>
                <legend><?php echo JText::_($fieldsets['btaddress']->label); ?></legend>
                <?php } ?>
            <dl>
                <?php foreach ($bt_address as $field) { ?>
                    <dt>
                    <?php echo $field->label; ?>
                    </dt>
                    <?php if ($field->id == 'jform_region_id') { ?>
                        <dd><?php echo $this->form->getInput('region_id', '', array($this->item->user_bt->country_id, $this->item->user_bt->region_id)); ?></dd>
                    <?php } else { ?>
                        <dd><?php echo $field->input; ?></dd>
                    <?php }
                }
                ?>
                <div class="ajax-button">
                    <button type="button" id="update_address" onclick="updateBT()"><?php echo JText::_('COM_POECOM_UPDATE');?></button>
                </div>
            </dl>
        </fieldset>
        <?php } ?>
</div>
<div>
    <?php
    //add hidden fields
    foreach ($this->form->getFieldset('hidden') as $field) {
        echo $field->input;
    }
    ?>
    <input type="hidden" name="task" value="customer.edit" />
    <?php echo JHtml::_('form.token'); ?>
</div>
<script src="<?php echo JURI::root().'/administrator/components/com_poecom/models/forms/poecom.js'; ?>" type="text/javascript"></script>
<script src="<?php echo $this->script ?>" type="text/javascript"></script>