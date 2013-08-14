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
<div id="category-container-wrap">
    <div id="category-container">
     
        <div class="category-title">
            <label><?php echo JText::_($this->item->cat->title); ?></label>
        </div>
        <div class="category-desc">
                <?php echo $this->item->cat->description; ?>
        </div>
        <?php
         if($this->item->products){
           ?>
            <div class="category-product-list">
            <?php foreach($this->item->products as $prd){ 
                
                if(strlen($prd->thumbimage)){
                    $thumbimage = $prd->thumbimage;
                }else{
                    $thumbimage = $image_path.'thumb/no-image.jpg';
                } ?>
                <div class="category-product" onmouseover="showDescription(<?php echo $prd->id ?>)" onmouseout="hideDescription(<?php echo $prd->id ?>)">
                    <?php if($prd->price_config != "0,0,0,0,0"){
                        $from = JText::_('COM_POECOM_PRICE_FROM'). " - ";
                    }else{
                        $from = '';
                    }
                    ?>
                    <div class="category-product-price"><?php echo $from . $this->currency[0]->symbol.$prd->price; ?></div>
                    <?php
                    $url = JRoute::_('index.php?view=product&id='.$prd->slug);
                    ?>
                    <div><a href="<?php echo $url ?>"><img src="<?php echo $thumbimage; ?>" alt="<?php echo $prd->name ?>"/></a></div>
                    <div class="category-product-name"><?php echo $prd->name ?></div>
                    <div class="category-product-desc" id="category-product-desc<?php echo $prd->id ?>"><?php echo $prd->list_description ?></div>
                </div>
            <?php } ?>
             </div>
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
    function showDescription(productID){
        jQuery('#category-product-desc'+productID).show();
    }
    
    function hideDescription(productID){
        jQuery('#category-product-desc'+productID).hide();
    }
</script>