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
<form action="<?php echo JRoute::_('index.php?option=com_poecom&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="location-form" class="form-validate" >
    <div class="width-40 fltlft">
        <?php
        $fs = $this->form->getFieldSets();
        $details = $this->form->getFieldset('details');

        if ($details) { ?>
            <fieldset class="adminform">
                <?php if (!empty($fs['details']->label)) { ?>
                    <legend><?php echo JText::_($fs['details']->label); ?></legend>
                <?php } ?>
                <dl>
                    <?php foreach ($details as $f) { ?>
                        <dt>
                        <?php echo $f->label; ?>
                        </dt>
                        <?php if ($f->id == 'jform_region_id') { ?>
                            <dd><?php echo $this->form->getInput('region_id', '', array($this->item->country_id, $this->item->region_id)); ?></dd>
                        <?php } else { ?>
                            <dd><?php echo $f->input; ?></dd>
                        <?php }
                    } ?>
                </dl>
            </fieldset>
        <?php } ?>
        <div>
            <?php
            foreach ($this->form->getFieldset('hidden') as $field) {
                echo $field->input;
            } ?>
            <input type="hidden" name="task" value="location.edit" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>
<script src="<?php echo $this->script; ?>" type="text/javascript"></script>