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

$jinput = JFactory::getApplication()->input;
$function = $jinput->get('function', 'jSelectOption', 'CMD');
?>
<form action="index.php?option=com_poecom" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
            <tr>
                <th width="5">
                    <?php echo JText::_('COM_POECOM_HEADING_ID'); ?>
                </th>		
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_NAME'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_PRODUCT'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_SKU'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_OPTION_TYPE'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_UOMID'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_DETAILID'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_POECOM_OPTIONS_HEADING_SORT'); ?>
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
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td>
                            <?php echo $item->id; ?>
                        </td>
                        <td>
                            <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)." : ". $item->product_name." : ". $item->product_sku); ?>');"><?php echo $item->name; ?></a>
                        </td>
                        <td>
                            <?php echo $item->product_name; ?>
                        </td>
                        <td>
                            <?php echo $item->option_sku; ?>
                        </td>
                        <td>
                            <?php echo $item->type_name; ?>
                        </td>
                        <td>
                            <?php echo $item->uom_name; ?>
                        </td>
                        <td>
                            <?php echo $item->detail_name; ?>
                        </td>
                        <td>
                            <?php echo $item->sort_order; ?>
                        </td>
                        <td>
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'options.'); ?>
                        </td>
                    </tr>
                <?php
                }
            }
            ?>
        </tbody>
       	<tfoot>
            <tr>
                <td colspan="9"><?php if ($this->pagination) {
                echo $this->pagination->getListFooter();
            } ?></td>
            </tr>
        </tfoot>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="options" />
        <input type="hidden" name="layout" value="modal" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="function" value="<?php echo $function ?>" />
        <input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
    </div>
</form>