<?php
defined('_JEXEC') or die('Restricted Access');
/**
 * Product Options E-commerce Extension
 * 
 * @author Micah Fletcher
 * @copyright 2011 - 2012 Extensible Point Solutions Inc. All Right Reserved
 * @license GNU GPL version 3, http://www.gnu.org/copyleft/gpl.html
 * @link http://www.exps.ca
 * @version 2.5.0
 * @since 2.5
**/ 
JHtml::_('behavior.tooltip');


$function = JRequest::getCmd('function', 'jSelectNumber');
?>
<form action="<?php echo JRoute::_('index.php?option=com_poecom'); ?>" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
            <tr>
        	<th width="5">
                    <?php echo JText::_('COM_POECOM_HEADING_ID'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_H_IMAGE_SRC'); ?>
        	</th>
        	<th>
                    <?php echo JText::_('COM_POECOM_H_IMAGE_PRODUCT'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_H_IMAGE_TYPE'); ?>
        	</th>
                
        </tr>
        </thead>
        <tbody>
            <?php
            if($this->items){
                foreach($this->items as $i => $item){ ?>
            	<tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <?php echo $item->id; ?>
                    </td>
                    <td>
                        <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->id)); ?>');"><?php echo $item->src; ?></a>
                    </td>
                    <td>
                        <?php echo $item->product_name; ?>
                    </td>
                    <td>
                        <?php echo $item->type_name; ?>
                    </td>
            	</tr>
             <?php }
            } ?>
        </tbody>
       	<tfoot>
            <tr>
            	<td colspan="5"><?php if($this->pagination){ echo $this->pagination->getListFooter(); } ?></td>
            </tr>
        </tfoot>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="images" />
        <input type="hidden" name="layout" value="modal" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="function" value="<?php echo $function ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<div id="dev-foot"><div>Developed by <a href="http://www.exps.ca" target="_blank">Extensible Point Solutions Inc.</a> Copyright 2012 - All Rights Reserved</div></div>