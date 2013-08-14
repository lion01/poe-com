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
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_POECOM_TABS_SEARCH_DESC'); ?>" />
            <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">
            <?php echo JHtml::_('select.genericList', $this->published_list,'filter_published','class="inputbox" onchange="this.form.submit()"', 'value', 'text', $this->state->get('filter.published')); ?>
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
                    <?php echo JText::_('COM_POECOM_TAB_HD_PRODUCT'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_TAB_HD_LABEL'); ?>
                </th>
		 <th>
                    <?php echo JText::_('COM_POECOM_HD_ORD'); ?>
        	</th>
                <th>
                    <?php echo JText::_('COM_POECOM_HD_PUB'); ?>
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
                        <?php echo $item->product_name; ?>
                    </td>
                    <td>
                        <a href="index.php?option=com_poecom&view=producttab&task=producttab.edit&id=<?php echo $item->id; ?>" /><?php echo $item->label; ?></a>
                    </td>
                    <td>
                        <?php echo $item->ordering; ?>
                    </td>
		    <td>
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'producttabs.',true); ?>
                    </td>
            	</tr>
             <?php }
            } ?>
        </tbody>
       	<tfoot>
            <tr>
            	<td colspan="7"><?php if($this->pagination){ echo $this->pagination->getListFooter(); } ?></td>
            </tr>
        </tfoot>
    </table>
    <div>
	
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="producttabs" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>