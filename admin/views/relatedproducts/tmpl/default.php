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
?>
<form action="<?php echo JRoute::_('index.php?option=com_poecom'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_POECOM_RP_SEARCH_D'); ?>" />

            <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">
            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true); ?>
            </select>
            <select name="filter_group_id" class="inputbox" onchange="this.form.submit()">
                <?php echo JHtml::_('select.options', $this->group_list, 'value', 'text', $this->state->get('filter.group_id')); ?>
            </select>
            <select name="filter_product_id" class="inputbox" onchange="this.form.submit()">
                <?php echo JHtml::_('select.options', $this->product_list, 'value', 'text', $this->state->get('filter.product_id')); ?>
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
                    <?php echo JText::_('COM_POECOM_H_RP_GROUP'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_H_RP_PRODUCT'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_H_ENABLED'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($this->items) {
                foreach ($this->items as $i => $item) {
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td><?php echo $item->id; ?></td>
                        <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td><?php echo $item->group_name; ?></td>
                        <td>
                            <a href="index.php?option=com_poecom&view=relatedproduct&task=relatedproduct.edit&id=<?php echo $item->id; ?>"><?php echo $item->product_name; ?></a>
                        </td>
                        <td>
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'relatedproducts.'); ?>
                        </td>
                    </tr>
                <?php }
            }
            ?>
        </tbody>
       	<tfoot>
            <tr>
                <td colspan="5"><?php if ($this->pagination) {
                echo $this->pagination->getListFooter();
            } ?></td>
            </tr>
        </tfoot>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="relatedproducts" />
        <input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
    </div>
</form>