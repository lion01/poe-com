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
JHtml::_('behavior.modal');
?>
<form action="<?php echo JRoute::_('index.php?option=com_poecom'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_POECOM_PROMOTION_FILTER_SEARCH_DESC'); ?>" />
            <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">
            <?php echo JHtml::_('select.genericList', $this->type_list,'filter_type','class="inputbox" onchange="this.form.submit()"', 'value', 'text', $this->state->get('filter.type')); ?>
            <?php echo JHtml::_('select.genericList', $this->discount_type_list,'filter_discount_type','class="inputbox" onchange="this.form.submit()"', 'value', 'text', $this->state->get('filter.discount_type')); ?>
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
                    <?php echo JText::_('COM_POECOM_PROMOTION_HEADING_NAME'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PROMOTION_HEADING_TYPE'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PROMOTION_HEADING_START'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PROMOTION_HEADING_END'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PROMOTION_HEADING_DISCOUNT_TYPE'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PROMOTION_HEADING_AMOUNT'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PROMOTION_HEADING_ORDER_MIN'); ?>
        	</th>
		 <th>
                    <?php echo JText::_('COM_POECOM_PROMOTION_HEADING_PRODUCT_MIN'); ?>
        	</th>
		 <th>
                    <?php echo JText::_('COM_POECOM_PROMOTION_HEADING_MAX_VALUE'); ?>
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
                        <?php echo $item->name; ?>
                    </td>
                    <td>
                        <?php echo $item->promotion_type; ?>
                    </td>
                    <td>
                        <?php echo $item->start_time; ?>
                    </td>
                    <td>
                        <?php echo $item->end_time; ?>
                    </td>
                    <td>
                        <?php echo $item->discount_type; ?>
                    </td>
                    <td>
                        <?php echo $item->discount_amount; ?>
                    </td>
                    <td>
                        <?php echo $item->order_amount_min; ?>
                    </td>
		    <td>
                        <?php echo $item->product_qty_min; ?>
                    </td>
		    <td>
                        <?php echo $item->max_value; ?>
                    </td>
            	</tr>
             <?php }
            } ?>
        </tbody>
       	<tfoot>
            <tr>
            	<td colspan="11"><?php if($this->pagination){ echo $this->pagination->getListFooter(); } ?></td>
            </tr>
        </tfoot>
    </table>
    <div class="poe-hidden-modal">
	<a class="modal" id="generateCoupons" rel="{handler: 'iframe', size: {x: 650, y: 400}}" 
                    href="index.php?option=com_poecom&view=promotions&tmpl=component" target="_blank" >
                    <button type="button" id="modallink" style="border: none; background: none;"></button></a>
    </div>
    <div>
	
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="promotions" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<script src="<?php echo $this->script?>" type="text/javascript"></script>