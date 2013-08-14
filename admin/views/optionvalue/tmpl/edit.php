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
$fieldsets = $this->form->getFieldsets();
?>
<form action="<?php echo JRoute::_('index.php?option=com_poecom&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="product-form" class="form-validate">
    <?php
    if($fieldsets){
        foreach($fieldsets as $fs){
            $fields = $this->form->getFieldset($fs->name);
            if($fields && $fs->name == 'details'){ ?>
                <div class="width-60 fltlft">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_('COM_POECOM_OPTION_VALUE_DETAILS'); ?></legend>
                        <?php if($fields){ ?>
                        <dl>
                        <?php foreach($fields as $f){ 
                            if($f->id != 'jform_description'){?>
                            
                            <dd><?php echo $f->label.$f->input ?></dd> 
                            <?php }else{ 
                                //editor - need to clear toggle element ?>
                                <div class="clr"></div>
                                <?php echo $f->label; ?>
                                <div class="clr"></div>
                                <?php echo $f->input;
                             }
                           }  
                        } ?>
                        </dl>
                    </fieldset>
                </div>
        <?php
            }else if($fields && $fs->name == 'shipping'){ ?>
                <div class="width-40 fltrt">
                <?php
                    echo JHtml::_('sliders.start', 'optionvalue-slider');
                    echo JHtml::_('sliders.panel', JText::_($fs->label), $fs->name . '-params');
                    
                    if (isset($fs->description) && trim($fs->description)){ ?>
                        <p class="tip"><?php echo $this->escape(JText::_($fs->description)); ?></p>
                    <?php }   ?>
                    
                    <fieldset class="panelform" >
                        <ul class="adminformlist">
                            <?php foreach ($fields as $f) { ?>
                                <li><?php echo $f->label.$f->input; ?></li>
                            <?php } ?>
                        </ul>
                    </fieldset>
                <?php echo JHtml::_('sliders.end'); ?>
            </div>
        <?php  }
            }
    } ?>
    <div>
        <input type="hidden" name="task" value="optionvalue.edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<script src="<?php //echo $this->script ?>" type="text/javascript"></script>