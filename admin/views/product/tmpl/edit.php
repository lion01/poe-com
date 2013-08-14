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
$fieldsets = $this->form->getFieldsets();
if (!empty($fieldsets)) {
    foreach ($fieldsets as $f) {
        switch ($f->name) {
            case 'product':
                $fs_product = $f;
                break;
            case 'productdesc':
                $fs_product_desc = $f;
                break;
            case 'seo':
                $fs_seo = $f;
                break;
            case 'images':
                $fs_images = $f;
                break;
            case 'shipping':
                $fs_shipping = $f;
                break;
            case 'options':
                $fs_options = $f;
                break;
            default:
                //notthing
                break;
        }
    }
    ?>
    <div id="tabs">
        <ul>
            <?php
            //set tabs
            if (!empty($fs_product)) {
                ?>
                <li><a href="#tabs-1"><?php echo JText::_($fs_product->label); ?></a></li>
                <?php
            }
            if (!empty($fs_seo)) {
                ?>
                <li><a href="#tabs-2"><?php echo JText::_($fs_seo->label); ?></a></li>
                <?php
            }
            if (!empty($fs_images)) {
                ?>
                <li><a href="#tabs-3"><?php echo JText::_($fs_images->label); ?></a></li>
                <?php
            }
            if (!empty($fs_shipping)) {
                ?>
                <li><a href="#tabs-4"><?php echo JText::_($fs_shipping->label); ?></a></li>
                <?php
            }
            if (!empty($fs_options)) {
                ?>
                <li><a href="#tabs-5"><?php echo JText::_($fs_options->label); ?></a></li>
                <?php
            }
            ?>
        </ul>
        <div id="tabs-1">
            <form action="index.php?option=com_poecom&layout=edit&id=<?php echo $this->item->id; ?>" method="post" name="adminForm" id="product-form" class="form-validate">
                <?php
                //fieldset fields
                $flds = $this->form->getFieldset($fs_product->name);
                if ($flds) {
                    ?>
                    <fieldset id="fs-product">
                        <legend><?php echo JText::_($fs_product->label); ?></legend>
                        <dl>
                            <?php foreach ($flds as $f) { ?>
                                <dt>
                                <?php echo $f->label; ?>
                                </dt>
                                <dd> <?php if ($f->id == 'jform_tax_exempt_ids') {
                                        echo $this->form->getInput('tax_exempt_ids', '', $this->item->tax_exempt_ids);
                                    } else if ($f->id == 'jform_catid') {
                                        echo $this->form->getInput('catid', '', $this->item->catids);
                                    } else if ($f->id == 'jform_type') {
                                        echo $this->form->getInput('type', '', $this->item->type);
                                    } else {
                                        echo $f->input;
                                    } ?></dd>
                            <?php } ?>
                        </dl>
                    </fieldset>
                <?php }
                if (!empty($fs_product_desc)) {
                    $flds = $this->form->getFieldset($fs_product_desc->name);
                    if ($flds) {
                        ?>
                        <fieldset id="fs-product-desc">
                            <legend><?php echo JText::_($fs_product_desc->label); ?></legend>
                            <dl>
                                <?php foreach ($flds as $f) { ?>
                                    <dt><?php echo $f->label; ?></dt>
                                    <dd style="clear:both;"><?php echo $f->input;?></dd>
                                <?php } ?>
                            </dl>
                        </fieldset>
                    <?php }
                } ?>
        </div>
       <?php if (!empty($fs_seo)) { ?>
        <div id="tabs-2">
            <?php
            //fieldset fields
                $flds = $this->form->getFieldset($fs_seo->name);
                if ($flds) {
                    ?>
                    <fieldset id="fs-seo">
                        <legend><?php echo JText::_($fs_seo->label); ?></legend>
                        <dl>
                            <?php foreach ($flds as $f) { ?>
                                <dt>
                                <?php echo $f->label; ?>
                                </dt>
                                <dd><?php echo $f->input; ?></dd>
                            <?php } ?>
                        </dl>
                    </fieldset>
                <?php } ?>
        </div>
        <?php
       }
       
       if (!empty($fs_images)) { ?>
        <div id="tabs-3">
            <?php
            //fieldset fields
                $flds = $this->form->getFieldset($fs_images->name);
                if ($flds) {
                    ?>
                    <fieldset id="fs-images">
                        <legend><?php echo JText::_($fs_images->label); ?></legend>
                        <dl>
                            <?php foreach ($flds as $f) { ?>
                                <dt>
                                <?php echo $f->label; ?>
                                </dt>
                                <dd><?php echo $f->input; ?></dd>
                            <?php } ?>
                        </dl>
                    </fieldset>
                <?php } ?>
        </div>
        <?php
       }
       
       if (!empty($fs_shipping)) { ?>
        <div id="tabs-4">
            <?php
            //fieldset fields
                $flds = $this->form->getFieldset($fs_shipping->name);
                if ($flds) {
                    ?>
                    <fieldset id="fs-shipping">
                        <legend><?php echo JText::_($fs_shipping->label); ?></legend>
                        <dl>
                            <?php foreach ($flds as $f) { ?>
                                <dt>
                                <?php echo $f->label; ?>
                                </dt>
                                <dd><?php echo $f->input; ?></dd>
                            <?php } ?>
                        </dl>
                    </fieldset>
                <?php } ?>
        </div>
        <div>
                <?php
                foreach ($this->form->getFieldset('hidden') as $field) {
                    echo $field->input;
                }
                ?>
                <input type="hidden" name="task" value="product.edit" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
        </form>
        <?php
       }
       
       if (!empty($fs_options)) { ?>
        <div id="tabs-5">
            <?php
            //fieldset fields
                $flds = $this->form->getFieldset($fs_options->name);
                if ($flds) {
                    ?>
                    <fieldset id="fs-options">
                        <legend><?php echo JText::_('COM_POECOM_PRODUCT_GENERATE_OPTIONS'); ?></legend>
                        <dl>
                            <?php foreach ($flds as $f) { ?>
                                <dt>
                                <?php echo $f->label; ?>
                                </dt>
                                <dd><?php echo $f->input; ?></dd>
                            <?php } ?>
                        </dl>
                        <div id="gen-product-options">
                        <button type="button" id="gen_options" onclick="generateOptions()"><?php echo JText::_('COM_POECOM_PRODUCT_GEN_OPTIONS');?></button>
                        </div>
                    </fieldset>
                <?php } ?>
            <div id="option_list" class="width-60 fltlft">
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_POECOM_PRODUCT_OPTIONS'); ?></legend>
                    <div id="options">
                    </div>
                </fieldset>
            </div>
            <div id="hmodal">
                <a class="optionsetmodal" id="modalPOEcom" rel="{handler: 'iframe', size: {x: 550, y: 600}}" 
                   href="index.php?option=com_poecom&tmpl=component&task=" 
                   target="_blank" ><button type="button" id="modalLink" style="border: none; background: none;"></button></a>
            </div>
        </div>
        <?php
       }
       
        if (!empty($this->script)) { ?>
            <script src="<?php echo $this->script ?>" type="text/javascript"></script>
    <?php }
}?>
