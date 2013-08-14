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


$function = JRequest::getCmd('function', 'jSelectTransaction');
?>
<form action="<?php echo JRoute::_('index.php?option=com_poecom'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_POECOM_PAYMENT_FILTER_SEARCH_DESC'); ?>" />
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
        	<th>
                    <?php echo JText::_('COM_POECOM_PAYMENT_HEADING_NUMBER'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PAYMENT_HEADING_METHOD'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PAYMENT_HEADING_RFQ'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PAYMENT_HEADING_ORDERID'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_PAYMENT_HEADING_STATUS'); ?>
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
                        <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->transaction_number)); ?>');"><?php echo $this->escape($item->transaction_number); ?></a>
                    </td>
                    <td>
                        <?php echo $item->pay_method_name; ?>
                    </td>
                    <td>
                        <?php echo $item->rfq_number; ?>
                    </td>
                    <td>
                        <?php echo $item->order_id; ?>
                    </td>
                    <td>
                        <?php echo $item->payment_status; ?>
                    </td>
            	</tr>
             <?php }
            } ?>
        </tbody>
       	<tfoot>
            <tr>
            	<td colspan="6"><?php if($this->pagination){ echo $this->pagination->getListFooter(); } ?></td>
            </tr>
        </tfoot>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="paytransactions" />
        <input type="hidden" name="layout" value="modal" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="function" value="<?php echo $function ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<div id="dev-foot"><div>Developed by <a href="http://www.exps.ca" target="_blank">Extensible Point Solutions Inc.</a> Copyright 2012 - All Rights Reserved</div></div>