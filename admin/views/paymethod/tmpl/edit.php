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
<form action="index.php?option=com_poecom&layout=edit&id=<?php echo $this->item->id; ?>" method="post" name="adminForm" id="paymethod-form" class="form-validate" >
    <div class="width-40 fltlft">
        <fieldset class="adminform">
        <legend><?php echo JText::_( 'COM_POECOM_PAYMETHOD_DETAILS' ); ?></legend>
        <ul class="adminformlist">
            <?php foreach($this->form->getFieldset('details') as $field){?>
                <li><?php echo $field->label;echo $field->input;?></li>
            <?php } ?>
        </ul>
        </fieldset>
        <div>
            <?php foreach ($this->form->getFieldset('hidden') as $field) {
                echo $field->input;
            }?>
            <input type="hidden" name="task" value="paymethod.edit" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>