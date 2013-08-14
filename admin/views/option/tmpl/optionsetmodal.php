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
?>
<div style="float: left;">
    <?php if($this->option_idx == -1){ ?>
        <button type="button" id="saveOption" onclick="saveOption(-1)"><?php echo JText::_('COM_POECOM_SAVE_OPTION'); ?></button>
    <?php }else{ ?>
        <button type="button" id="updateOption" onclick="saveOption(<?php echo $this->option_idx;?>)"><?php echo JText::_('COM_POECOM_UPDATE_OPTION'); ?></button>
    <?php } ?>
    <form action="" method="post" name="adminForm" id="option-form">
        <div class="fltlft">
            <fieldset class="adminform">
                <legend><?php echo JText::_('COM_POECOM_OPTION_DETAILS'); ?></legend>
                <ul class="adminformlist">
                    <?php
                    foreach ($this->form->getFieldset('details') as $field) {
                        if ($field->id != 'jform_product_id' &&
                                $field->id != 'jform_dom_element' &&
                                $field->id != 'jform_ordering') {

                            if ($field->type == 'Editor') {
                                ?>
                                <div class="clr"></div>
                            <?php } ?>
                            <li><?php echo $field->label; ?>
                                <?php
                                if ($field->id == 'jform_type') {
                                    echo $this->form->getInput('type', '', $this->item->type);
                                } else {
                                    echo $field->input;
                                }
                            }
                            ?>
                        </li>
                    <?php } ?>
                </ul>
        </div>
        <div>
            <input type="hidden" id="optionset_id" value="<?php echo $this->optionset_id; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>
</div>
<script src="<?php echo JURI::root() . 'administrator/components/com_poecom/models/forms/modal.option.js'; ?>"></script>