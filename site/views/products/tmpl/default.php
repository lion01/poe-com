<?php
defined('_JEXEC') or die('Restricted access');
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
?>
<div>
    <div id="location-container">
        <?php foreach($this->items as $item){ ?>
            
        <div class="location">
        <div class="location-logo">
        <?php if(strlen($item->logo)){ ?>
            <img src="<?php echo JURI::base().$item->params->get('image_path').$item->logo; ?>" alt="<?php echo $item->name ?>"/>
            
        <?php }else{ ?>
            <h1><?php echo $item->name.(($item->category and $item->params->get('show_category')) ? (' ('.$item->category.')'):'') ?></h1>
        <?php } ?>
        </div>
        <div class="location-address">
            <div><?php echo $item->street1 ?></div>
            <?php if(strlen($item->street2)){ ?>
                <div><?php echo $item->street2 ?></div>
            <?php } ?>
            <div><?php echo $item->city.' , '.$item->state  ?></div>
            <div><?php echo $item->country. ' , '. $item->postal_code ?></div>
            <div><?php echo $item->phone ?></div>
            <?php if(strlen($item->email)){
                $dispatcher = JDispatcher::getInstance();
                
                $email_cloaked = JHtml::_('email.cloak', $item->email);
                ?>
            <div><?php echo $email_cloaked; ?></div>
            <?php } ?>
            <?php if(strlen($item->website) && $item->website != 'http://'){ ?>
                <div><a href="<?php echo $item->website ?>" target="_blank"><?php echo JText::_('COM_LOCATIONS_VISIT_WEBSITE')?></a></div>
            <?php } ?>
            
        </div>
        <div class="location-map">
        <label><?php echo JText::_('COM_LOCATIONS_VIEW_MAP'); ?></label>
        <form action="http://maps.google.com/maps" method="get" target="_blank">  
            <input type="hidden" name="saddr" id="saddr" value="" />     
            <input type="hidden" name="daddr" id="daddr" value="<?php echo $item->google_address ?>" />
            <input style="background: url(<?php echo JURI::base().$item->params->get('image_path').$item->params->get('map_image')?>); height:95px; width: 133px;" type="submit" value=""/>
        </form>
        <?php if(isset($item->distance)){ ?>
            <div id="location-distance"><?php echo JText::_('COM_LOCATIONS_DISTANCE_LABEL'). $item->distance . ' ' . $item->distance_uom;?></div>
        <?php } ?>
        </div>
        </div>
        <?php } ?>
    </div>
</div>