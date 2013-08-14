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
 * */
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_rmgr');
$saveOrder	= $listOrder == 'ordering';
?>
<form action="<?php echo JRoute::_('index.php?option=com_poecom'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_POECOM_OPTION_VALUE_SEARCH'); ?>" />

            <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">
            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true); ?>
            </select>
            <select name="filter_product_id" class="inputbox" onchange="this.form.submit()">
                <?php echo JHtml::_('select.options', $this->product_list, 'value', 'text', $this->state->get('filter.product_id')); ?>
            </select>
        </div>
    </fieldset>

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
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_PRODUCT'); ?>
                </th>			
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_NAME'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_VALUE'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_TEXT'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_VALUE_SKU'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_MODIFIER'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_MODIFIER_VALUE'); ?>
                </th>
                <th>
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder);
                        if ($canOrder && $saveOrder) {
                                echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'optionvalues.saveorder');
                        } ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_HEADING_PUBLISHED'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($this->items) {
                foreach ($this->items as $i => $item) {
                    $ordering	= ($listOrder == 'ordering');
                    $canChange	= $user->authorise('core.edit.state',	'com_poecom');
                    ?>
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
                            <?php echo $item->option_name; ?>
                        </td>
                        <td>
                            <?php echo $item->option_value; ?>
                        </td>
                        <td>
                            <a href="index.php?option=com_poecom&view=optionvalue&task=optionvalue.edit&id=<?php echo $item->id;?>"><?php echo $item->option_label; ?></a>
                        </td>
                        <td>
                            <?php echo $item->option_value_sku; ?>
                        </td>
                        <td>
                            <?php echo $item->modifier; ?>
                        </td>
                        <td>
                            <?php echo $item->modifier_value; ?>
                        </td>
                        <td class="order">
                            <?php
                            if ($canChange) {
                                if ($saveOrder) {
                                    if ($listDirn == 'asc') {
                                        ?>
                                        <span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i - 1]->option_id == $item->option_id), 'optionvalues.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i + 1]->option_id == $item->option_id), 'optionvalues.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                    <?php } elseif ($listDirn == 'desc') { ?>
                                        <span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i - 1]->option_id == $item->option_id), 'optionvalues.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i + 1]->option_id == $item->option_id), 'optionvalues.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                    <?php
                                    }
                                }
                                $disabled = $saveOrder ? '' : 'disabled="disabled"';
                                ?>
                                <input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text-area-order" />
                            <?php
                            }else{
                                echo $item->ordering;
                            } ?>
                        </td>
                        <td>
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'optionvalues.'); ?>
                        </td>
                    </tr>
                <?php }
            }
            ?>
        </tbody>
       	<tfoot>
            <tr>
                <td colspan="11"><?php if ($this->pagination) {
                echo $this->pagination->getListFooter();
            } ?></td>
            </tr>
        </tfoot>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="optionvalues" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
    </div>
</form>