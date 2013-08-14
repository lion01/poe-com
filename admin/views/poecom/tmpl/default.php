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

$image_path = JURI::root() . 'media/com_poecom/images/';
?>
<div class="poecom-admin">
    <fieldset>
        <legend><?php echo JText::_('COM_POECOM_CP_MANAGE'); ?></legend>
        <div class="poecom-cpanel">
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=requests">
                        <img src="<?php echo $image_path . 'icon-48-requests.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_REQUESTS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=orders">
                        <img src="<?php echo $image_path . 'icon-48-orders.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_ORDERS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=paytransactions">
                        <img src="<?php echo $image_path . 'icon-48-payments.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_PAYMENTS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=promotions">
                        <img src="<?php echo $image_path . 'icon-48-promotions.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_PROMOTIONS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=coupons">
                        <img src="<?php echo $image_path . 'icon-48-coupons.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_COUPONS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=flyers">
                        <img src="<?php echo $image_path . 'icon-48-flyers.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_FLYERS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=customers">
                        <img src="<?php echo $image_path . 'icon-48-customers.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_CUSTOMERS'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><?php echo JText::_('COM_POECOM_CP_CONFIG'); ?></legend>
        <div class="poecom-cpanel">
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=locations">
                        <img src="<?php echo $image_path . 'icon-48-locations.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_LOCATIONS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=taxes">
                        <img src="<?php echo $image_path . 'icon-48-taxes.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_TAXES'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_categories&view=categories&subview=products&extension=com_poecom">
                        <img src="<?php echo $image_path . 'icon-48-categories.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_CATEGORIES'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=products">
                        <img src="<?php echo $image_path . 'icon-48-products.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_PRODUCTS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=images">
                        <img src="<?php echo $image_path . 'icon-48-images.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_IMAGES'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=relatedproducts">
                        <img src="<?php echo $image_path . 'icon-48-relatedproducts.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_RELATED_PRODUCTS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=relatedgroups">
                        <img src="<?php echo $image_path . 'icon-48-relatedgroups.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_RELATED_GROUPS'); ?></span>
                    </a>
                </div>
            </div>

        </div>

        <div class="poecom-cpanel">
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=optionsets">
                        <img src="<?php echo $image_path . 'icon-48-optionsets.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_OPTIONSETS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=options">
                        <img src="<?php echo $image_path . 'icon-48-options.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_OPTIONS'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=optionvalues">
                        <img src="<?php echo $image_path . 'icon-48-optionvalues.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_OPTION_VALUES'); ?></span>
                    </a>
                </div>
            </div>
            <div class="poecom-icon-wrapper">
                <div class="poecom-icon">
                    <a href="index.php?option=com_poecom&view=shipmethods">
                        <img src="<?php echo $image_path . 'icon-48-shipmethods.png'; ?>"/>
                        <span><?php echo JText::_('COM_POECOM_CP_SHIP'); ?></span>
                    </a>
                </div>
                <div class="poecom-icon-wrapper">
                    <div class="poecom-icon">
                        <a href="index.php?option=com_poecom&view=packages">
                            <img src="<?php echo $image_path . 'icon-48-packages.png'; ?>"/>
                            <span><?php echo JText::_('COM_POECOM_CP_PKG'); ?></span>
                        </a>
                    </div>
                </div>
                <div class="poecom-icon-wrapper">
                    <div class="poecom-icon">
                        <a href="index.php?option=com_poecom&view=paymethods">
                            <img src="<?php echo $image_path . 'icon-48-paymethods.png'; ?>"/>
                            <span><?php echo JText::_('COM_POECOM_CP_PAY'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
    </fieldset>
</div>