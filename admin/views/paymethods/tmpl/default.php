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
?>
<form action="<?php echo JRoute::_('index.php?option=com_poecom'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_POECOM_PAYMETHODS_FILTER_SEARCH_DESC'); ?>" />
            <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">
            <select name="filter_enabled" class="inputbox" onchange="this.form.submit()">
                <?php echo JHtml::_('select.options', $this->enable_filter, 'value', 'text', $this->state->get('filter.enabled'));?>    
            </select>
        </div>
    </fieldset>
    <div class="clr"> </div>
    <table class="adminlist">
        <thead>
            <tr>
        	<th width="5">
        		<?php echo JText::_('COM_POECOM_HEADING_ID'); ?>
        	</th>
        	<th width="20">
        		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
        	</th>			
        	<th>
        		<?php echo JText::_('COM_POECOM_PAYMETHOD_HEADING_NAME'); ?>
        	</th>
            <th>
        		<?php echo JText::_('COM_POECOM_PAYMETHOD_HEADING_LOGO'); ?>
        	</th>
             <th>
        		<?php echo JText::_('COM_POECOM_PAYMETHOD_HEADING_PLUGIN'); ?>
        	</th>
            <th>
        		<?php echo JText::_('COM_POECOM_HEADING_DEFAULT'); ?>
        	</th>
            <th>
        		<?php echo JText::_('COM_POECOM_HEADING_ENABLED'); ?>
        	</th>
            <th>
        		<?php echo JText::_('COM_POECOM_HEADING_SORT'); ?>
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
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td>
                        <a href="index.php?option=com_poecom&view=paymethod&task=paymethod.edit&id=<?php echo $item->id; ?>"><?php echo $item->name; ?></a>
                    </td>
                    <td>
                        <?php echo $item->logo; ?>
                    </td>
                    <td>
                        <a href="index.php?option=com_plugins&task=plugin.edit&extension_id=<?php echo $item->extension_id; ?>"><?php echo $item->plugin; ?></a>
                    </td>
                    <td>
                        <?php echo $item->pm_default == 1?JText::_('JYes'):''; ?>
                    </td>
                    <td>
                        <?php echo $item->enabled == 1?JText::_('JYes'):JText::_('JNo'); ?>
                    </td>
                    <td>
                        <?php echo $item->sort_order; ?>
                    </td>
            	</tr>
             <?php }
            } ?>
        </tbody>
       	<tfoot>
            <tr>
            	<td colspan="8"><?php if($this->pagination){ echo $this->pagination->getListFooter(); } ?></td>
            </tr>
        </tfoot>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="paymethods" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>