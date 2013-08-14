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
<div id="dialog" title="">
    <div id="loadingDivContainer">
        <div id="dialogText"></div>
    </div>
</div>
<div id="related-products-wrap">
    <div id="related-products-intro">
    <?php
    echo JText::_('COM_POECOM_RELATED_PRODUCT_INTRO'); ?>
    </div>
    <div id="related-products-skip">
        <a id="related-checkout-go" href="index.php?option=com_poecom&view=cart&skiprelated=1"><?php echo JText::_('COM_POECOM_RELATED_PRODUCT_SKIP'); ?></a>
    </div>
    <?php
    if(!empty($this->rp)){
        $html = '';
        foreach($this->rp as $rp){ 
            $html .= '<div class="rp-product">';
            $html .= '<div class="rp-product-img"><img src="'.$rp->thumbimage.'" title="'.$rp->name.'" /></div>';
            $html .= '<div class="rp-product-title">'.$rp->name.'</div>';
            $html .= '<div class="rp-product-desc">'.$rp->description.'</div>';
            $html .= '<div class="rp-product-atc-wrap">';
            $html .= '<div class="rp-product-price">'.$this->currency_symbol.' '.$rp->price.'</div>';
            $html .= '<div class="rp-product-atc"><button type="button" onclick="rpAddToCart('.$rp->product_id.')">'.JText::_('COM_POECOM_ADDTOCART_ADD').'</button></div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        echo $html;
    } ?>
    <div id="jtoken"><?php echo JHTML::_( 'form.token' ); ?></div>
</div>
<script src="<?php echo $this->script;?>"></script>