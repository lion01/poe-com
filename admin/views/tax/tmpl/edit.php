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
<form action="index.php?option=com_poecom&layout=edit&id=<?php echo (int) $this->item->id; ?>" method="post" name="adminForm" id="tax-form" class="form-validate" >
    <div class="width-50 fltlft">
        <?php
        $fieldsets = $this->form->getFieldSets();
        $detail_fields = $this->form->getFieldset('details');
        if (!empty($detail_fields)) {
            ?>
            <fieldset class="adminform">
                <?php if (isset($fieldsets['details']->label)) { ?>
                    <legend><?php echo JText::_($fieldsets['details']->label); ?></legend>
                    <?php } ?>
                <dl>
                    <?php foreach ($detail_fields as $field) { ?>
                        <dt>
                        <?php echo $field->label; ?>
                        </dt>
                        <?php if ($field->id == 'jform_region_id') { ?>
                            <dd><?php echo $this->form->getInput('region_id', '', array($this->item->country_id, $this->item->region_id)); ?></dd>
                        <?php } else { ?>
                            <dd><?php echo $field->input; ?></dd>
                        <?php }
                    }
                    ?>

                </dl>
            </fieldset>
            <?php } ?>
        <div>
            <?php
            //add hidden fields
            foreach ($this->form->getFieldset('hidden') as $field) {
                echo $field->input;
            }
            ?>
            <input type="hidden" name="task" value="tax.edit" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>
<script src="<?php echo $this->script ?>" type="text/javascript"></script>