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
$jinput = JFactory::getApplication()->input;

$function = $jinput->get('function', 'jSelectRelatedGroup', 'CMD');
?>
<form action="<?php echo JRoute::_('index.php?option=com_poecom'); ?>" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
            <tr>
        	<th width="5">
                    <?php echo JText::_('COM_POECOM_HEADING_ID'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_H_RELATED_GROUP_NAME'); ?>
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
                        <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');"><?php echo $item->name; ?></a>
                    </td>
            	</tr>
             <?php }
            } ?>
        </tbody>
       	<tfoot>
            <tr>
            	<td colspan="2"><?php if($this->pagination){ echo $this->pagination->getListFooter(); } ?></td>
            </tr>
        </tfoot>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="relatedgroups" />
        <input type="hidden" name="layout" value="modal" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="function" value="<?php echo $function ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>