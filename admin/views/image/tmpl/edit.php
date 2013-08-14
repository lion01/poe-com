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
//$params = $this->form->getFieldsets('params');
?>
<form action="<?php echo JRoute::_('index.php?option=com_poecom&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="image-form" class="form-validate">
    <div class="width-60 fltlft">
        <?php
        foreach ($this->form->getFieldsets() as $fieldset) {
            $fields = $this->form->getFieldset($fieldset->name);

            if (count($fields)) {
                ?>
                <fieldset class="adminform">
                    <?php if (isset($fieldset->label)) { ?>
                        <legend><?php echo JText::_($fieldset->label); ?></legend>
                        <?php } ?>
                    <dl>
                        <?php foreach ($fields as $field) { ?>
                            <dt>
                            <?php echo $field->label; ?>
                            </dt>
                            <dd><?php echo $field->input; ?></dd>
                <?php } ?>
                    </dl>
                </fieldset>
    <?php }
}
?>
        <div>
            <input type="hidden" name="task" value="image.edit" />
<?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>
<script src="<?php echo $this->script ?>" type="text/javascript"></script>