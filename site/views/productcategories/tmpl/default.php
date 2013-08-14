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

// Get the Products root node
$params =& JComponentHelper::getParams('com_poecom');
$image_path = $params->getValue('productimagepath');

?>
<div>
    <div id="category-container">
        <?php foreach($this->items as $item){ ?>
        <div class="category-title">
            <label><?php echo JText::_($item->category_title); ?></label>
        </div>   
        <div class="category-image">
            <img src="<?php echo $item->category_image ?>" alt="<?php echo $item->category_title ?>" />
        </div>
        
        <div class="category-desc">
                <?php echo $item->category_desc; ?>
        </div>
        <?php
         if($item->products){?>
            <div class="category-product-list">
            <?php foreach($item->products as $prd){ 
                
                if(strlen($prd->thumbimage)){
                    $thumbimage = $prd->thumbimage;
                }else{
                    $thumbimage = $image_path.'thumb/no-image.jpg';
                } ?>
                
                <div class="category-product">
                    <div><a href="index.php?option=Com_poecom&view=product&id=<?php echo $prd->id ?>"><img src="<?php echo $thumbimage; ?>" alt="<?php echo $prd->name ?>"/></a></div>
                    <div class="category-product-name"><?php echo $prd->name ?></div>
                </div>
            <?php } ?>
             </div>
         <?php   } ?>
         
        <?php } ?>
    </div>
</div>