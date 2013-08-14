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
<style>
#product-image { /*left: 20px*/ }
#product-nav { clear: both; float: left; margin: 15px }
#product-nav li { width: 50px; float: left; margin: 8px; list-style: none }
#product-nav a { width: 50px; padding: 3px; display: block; border: 1px solid #ccc; }
#product-nav li.activeSlide a { background: #88f }
#product-nav a:focus { outline: none; }
#product-nav img { border: none; display: block }

#enlarge{
   /* position: absolute; */
    z-index:1001;
  /*  top: 320px;
    left: 300px; */
    border: none;
    background: none;
}
#imgzoom{
    border:none;
    background: none;
}
#nav-container{
    float: left;
    width: 100px;
}
</style>
<div id="product-container">
<div id="product-image-wrap">
    <div id="product-image" style="min-height: 300px;">
       <?php { 
        $style_width = 440;
        if($this->item->main_images){
            //should be more than one image
            $idx=0;
            foreach($this->item->main_images as $img){ 
                //scale height, $style_width is container width from page style
                if($img->width > $style_width){
                    $img_height = $style_width/$img->width * $img->height;
                }else{
                    $img_height = $img->height;
                }?>
                <div class="img-container">
                <a id="img<?php echo $idx;?>" href="<?php echo $img->src; ?>" class="jcepopup" rel="title[<?php echo $this->item->name;?>];caption[<?php echo $img->caption;?>];group[product]"><img src="<?php echo $img->src; ?>" width="<?php echo $style_width; ?>" height="<?php echo $img_height; ?>" alt="<?php echo $img->alt;?>" /></a>
                </div>
                    <?php $idx++;
            } 
        }else{ ?>
        <div id="one-img-container"><?php
            //only one image
            if($this->item->show_zoom === '1'){?>
                <a id="img0" href="<?php echo $this->item->mainimage; ?>" class="jcepopup" 
                   rel="title[<?php echo $this->item->name;?>];caption[<?php echo $this->item->caption;?>];">
                    <img src="<?php echo $this->item->mainimage; ?>" width="<?php echo $this->item->main_image_width; ?>" alt="<?php echo $img->alt;?>" /></a>
        <?php }else{ ?>
                <img src="<?php echo $this->item->mainimage; ?>" width="<?php echo $this->item->main_image_width; ?>" alt="<?php echo $img->alt;?>" />
        <?php } ?>
        </div><?php 
        } 
    }
?>
    </div>
<!--    <?php if(!$this->item->main_images && $this->item->show_zoom === '1'){ ?>
        <div id="prod-zoom-wrap">
            <div id="prod-zoom">
            <button type="button" id="enlarge" onclick="showMediaPopUp()"><img src="media/com_poecom/images/magnify.png"/></button>
            </div>
        </div>
    <?php } ?> -->
</div>
<div id="prod-right-wrap">
    <h1><?php echo $this->item->name; ?></h1>
    <div id="product-options">
    <div id="prod-price" class="product-price">
        <div><?php echo JText::_('COM_POECOM_PRODUCT_PRICE_LBL'); ?></div>
        <div>
            <label id="currency"><?php echo $this->item->currency[0]->symbol ?></label>
        </div>
        <div>
            <label id="price-display"><?php echo $this->item->price ?></label>
        </div>
        <div id="loadingDiv"></div>
    </div>
    <div id="prod-options-title"><?php
        if($this->item->options){
            echo JText::_('COM_POECOM_PRODUCT_OPTIONS_LBL');
        } ?></div>
    <?php 
        if($this->cart_itemid > 0){
            $action_url = 'index.php?Itemid='.$this->cart_itemid;
        }else{
            $action_url = 'index.php?option=com_poecom&view=cart&Itemid=';
        }
     ?>
    <form class="poe-addtocart-form" name="addtocart" id="<?php echo uniqid('addtocart_')?>" action="<?php echo JRoute::_($action_url,true,$this->useHTTPS); ?> " method="post">
    <div id="poe-option">    
    <?php if($this->item->options){
    
        foreach($this->item->options as $op){ 
            
            // Set default class
            $update_price = 'class="optioninput';
            
            // Add option call if set
            if(strlen($op->class)){
                $update_price .= ' '.$op->class.'"';
            }else{
                $update_price .= '"';
            }
            
            // Set price update control
            if($op->price_control_id == 1){
                $update_price .= ' onchange="updatePrice(this, false)"';
            }
            
        ?>
        <div class="option-container">
            <div class="option-label"><?php echo $op->name; ?>:</div>
            <div class="option-input"><?php 
                switch($op->option_type_id){
                    case '1': // select
                        if($op->values){
                            echo JHtml::_('select.genericList', $op->values, $op->dom_element, $update_price, 'option_value', 'option_label', $op->selected_value );
                        }
                        break;
                    case '2': // inputqty
                        echo '<input '.$update_price.' name="'.$op->dom_element.'" id="'.$op->dom_element.'" value="'.$op->selected_value.'" />';
                        break;
                    case '3': // inputsize
                    case '4': // inputtext
                        echo '<input '.$update_price.' name="'.$op->dom_element.'" id="'.$op->dom_element.'" value="'.$op->selected_value.'" />';
                        break;
                    case '5': // property
                        echo '<label name="'.$op->dom_element.'" id="'.$op->dom_element.'" >'.$op->values[0]->option_label.'</label>';
                        break;
                    default:
                        break;
                }
             ?>
             </div>
       </div>
       <?php }
      
    } ?>
        <input type="hidden" name="product_id" id="product_id" value="<?php echo $this->item->id ?>" />
        <input type="hidden" name="related_group_id" id="related_group_id" value="<?php echo $this->item->related_group_id ?>" />
        <input type="hidden" name="product_sku" id="product_sku" value="<?php echo $this->item->sku ?>" />
        <input type="hidden" name="product_name" id="product_name" value="<?php echo $this->item->name ?>" />
        <input type="hidden" name="product_type" id="product_type" value="<?php echo $this->item->type ?>" />
        <input type="hidden" name="price" id="price" value="<?php echo $this->item->price ?>" />
        <input type="hidden" name="serial_options" id="serial_options" value="<?php echo $this->serial_options ?>" />
      <!--  <input type="hidden" name="json_properties" id="json_properties" value="<?php echo $this->json_properties ?>" /> -->
        <input type="hidden" name="max_qty" id="max_qty" value="<?php echo $this->item->max_qty ?>" />
        <input type="hidden" name="run_update" id="run_update" value="0" />
        <input type="hidden" name="change_item_idx" id="change_item_idx" value="<?php echo $this->change_item_idx ?>" />
        <?php echo JHTML::_( 'form.token' ); 
         if($this->item->order_allowed === '1'){?>
        <div id="quantity-block">
            <div class="poe-qtybox">
                <label class="poe-qtybox-label"><?php echo JText::_('COM_POECOM_QTY_LBL'); ?></label>
                <input type="text" id="quantity<?php echo $this->item->id?>" name="quantity[]" class="inputboxquantity" onfocus="quantityFocus()" onblur="quantityBlur()" name="quantity_adjust" size="3" value="<?php echo $this->item->default_qty ?>" />
            </div>
            <div class="poe-qty-updown">
                 <div class="quantity-box-button quantity-box-button-up" onclick="qtyUp()"><span>+</span></div>
                 <div class="quantity-box-button quantity-box-button-down" onclick="qtyDown()"><span>-</span></div>
            </div>
        </div>
        <div class="poe-submit"> 
          <?php if(!$this->block_order){ ?>
            <input class="poe-button poe-corner-all" type="submit" value="<?php echo JText::_('COM_POECOM_ADDTOCART');?>" name="submit" id="addtocart"/><input style="display:none;" type="button" value="Get Price" id="get-price-but" onclick="quantityFocus()" />
          <?php }else{
            echo JText::_('COM_POECOM_BLOCKED_ORDERING_ENQUIRY'); 
            $subject = urlencode($this->item->name);
            ?>
            <a href="mailto:<?php echo $this->rfqemail ?>?subject=<?php echo $subject ?>"><?php echo JText::_('COM_POECOM_ORDERING_ENQUIRY'); ?></a>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
    </form>
</div>
    <div id="poe-tabs-container">
        <ul class="poe-tabs">
            <?php               
                if(!empty($this->item->tabs)){

                    $tab_html = '';
                    $tab_id = 1;
                    foreach($this->item->tabs as $t ){
                        
                        $tab_html .= '<li><a href=#tab_'.$tab_id++.'>'.$t['label'].'</a></li>';
                    }
                    
                    if(strlen($tab_html)){
                        echo $tab_html;
                    }
                }
            ?>
        </ul>
        <div class="poe-tab-content-wrap">
            <?php
                if(!empty($this->item->tabs)){

                    $tab_html = '';
                    $tab_id = 1;
                    foreach($this->item->tabs as $t ){
                        
                        $tab_html .= '<div id=tab_'.$tab_id++.' class="poe-tab-content" >' .html_entity_decode($t['content']);
                        if($tab_id == 1){
                            if($this->item->options){
                                foreach($this->item->options as $op){
                                    if($op->option_type_id == '5'){ // properties only
                                        $tab_html .= '<div class="tab-option-container">
                                            <div class="tab-option-label">'.$op->name .'</div>';
                                        $tab_html .= '<div class="tab-option-value"><label class="inputproperty">'.$op->option_sku.'</label></div>';
                                        $tab_html .= '</div>';
                                    }
                                }
                            } 
                            $tab_html .= '</div>';
                        }else{
                            $tab_html .= '</div>';
                        }
                    }
                    
                    if(strlen($tab_html)){
                        echo $tab_html;
                    }
                }
            ?>
        </div>
    </div> 
</div>
</div>
<?php if($this->related_products){ ?>
    <div id="poe-related-title"><?php echo JText::_('COM_POECOM_RELATED_PRODUCTS_TITLE');?></div>
    <div id="poe-related-wrap">
        <?php
            $rp_html = '';
            foreach($this->related_products as $rp){
                $url = JRoute::_(JURI::base().'index.php?option=com_poecom&view=product&id='.$rp->product_id.':'.$rp->alias);
                $rp_html .= '<div class="related-product-wrap">';
                $rp_html .= '<div class="related-product-img">';
                $rp_html .= '<a href="'.$url.'"><img src="'.$rp->thumbimage.'" width="120" alt="'.$rp->name.'"/></a>';
                $rp_html .= '</div>';
                $rp_html .= '<div class="related-product-title">'.$rp->name.'</div>';
                $rp_html .= '<div class="related-product-price">'.$rp->price.'</div>';
                $rp_html .= '</div>';
            }
            
            echo $rp_html;
         ?>
    </div>
<?php } ?>
<script type="text/javascript" src="<?php echo JURI::root(true).'/administrator/components/com_poecom/models/forms/jquery.validate.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo JURI::root(true).'/components/com_poecom/models/forms/product.js'; ?>"></script>