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
<div class="width-50 fltlft">
    <fieldset class="adminform">
         <legend><?php echo JText::_('COM_POECOM_FLYER_ROW_DETAILS'); ?></legend>
         <form action="index.php?option=com_poecom&layout=edit&id=<?php echo $this->item->id; ?>" method="post" name="adminForm" id="flyerrow-form" class="form-validate">
        <ul class="adminformlist">
            <?php foreach ($this->form->getFieldset('details') as $field) { ?>
                <li style="clear:both;"><?php echo $field->label.$field->input; ?></li>
            <?php } ?>
        </ul>
        <div>
        <?php
            foreach ($this->form->getFieldset('hidden') as $field) {
                echo $field->input;
            }
            ?>
            <input type="hidden" name="task" id="theid" value="flyer.edit" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
        </form>
    </fieldset>
</div>
<?php if(!empty($this->script)){ ?>
<script type="text/javascript" src="<?php echo $this->script; ?>"></script>
<?php } ?>
