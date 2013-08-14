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
<form action="index.php?option=com_poecom&layout=edit&id=<?php echo $this->item->id; ?>" method="post" name="adminForm" id="option-form" class="form-validate">
    <div class="width-50 fltlft">
        <fieldset class="adminform">
             <legend><?php echo JText::_('COM_POECOM_OPTION_DETAILS'); ?></legend>
            <ul class="adminformlist">
                <?php foreach ($this->form->getFieldset('details') as $field) {
                    if ($field->type == 'Editor') {
                        ?>
                        <div class="clr"></div>
                        <?php } ?>
                    <li><?php echo $field->label; ?>
                        <?php
                        if ($field->id == 'jform_tax_exempt_ids') {
                            echo $this->form->getInput('tax_exempt_ids', '', $this->item->tax_exempt_ids);
                        } else if ($field->id == 'jform_catid') {
                            echo $this->form->getInput('catid', '', $this->item->catids);
                        } else if ($field->id == 'jform_type') {
                            echo $this->form->getInput('type', '', $this->item->type);
                        } else {
                            echo $field->input;
                        }
                        ?>
                    </li>
<?php } ?>
            </ul>
    </div>
        <div>
    <?php
        foreach ($this->form->getFieldset('hidden') as $field) {
            echo $field->input;
        }
        ?>
        <input type="hidden" name="task" value="option.edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<div class="width-50 fltlft">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_POECOM_OPTION_VALUES'); ?></legend>
        <?php if(!empty($this->values)){
            $html = 'rows';
        }else{
            $html = 'no rows';
        } 
        echo $html;
        ?>
    </fieldset>
</div>