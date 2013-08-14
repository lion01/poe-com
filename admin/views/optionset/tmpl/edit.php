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
JHtml::_('behavior.modal', 'a.optionsetmodal');
JHtml::_('behavior.formvalidation');
?>

<div class="width-30 fltlft">
    <fieldset class="adminform">
         <legend><?php echo JText::_('COM_POECOM_OPTIONSET_DETAILS'); ?></legend>
         <form action="index.php?option=com_poecom&layout=edit&id=<?php echo $this->item->id; ?>" method="post" name="adminForm" id="option-form" class="form-validate">
        <ul class="adminformlist">
            <?php foreach ($this->form->getFieldset('details') as $field) { ?>
                <li><?php echo $field->label.$field->input; ?></li>
            <?php } ?>
        </ul>
             <?php if($this->editmode){ ?>
             <button type="button" onclick="update()" ><?php echo JText::_('COM_POECOM_UPDATE'); ?></button>
             <?php }?>
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
    </fieldset>
</div>
<div id="option_list" class="width-60 fltlft">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_POECOM_OPTIONSET_OPTIONS'); ?></legend>
        <div id="options">
        </div>
    </fieldset>
</div>
<div id="hmodal">
    <a class="optionsetmodal" id="modalPOEcom" rel="{handler: 'iframe', size: {x: 550, y: 600}}" 
       href="index.php?option=com_poecom&tmpl=component&task=" 
       target="_blank" ><button type="button" id="modalLink" style="border: none; background: none;"></button></a>
</div>
<script type="text/javascript" src="<?php echo $this->script; ?>"></script>