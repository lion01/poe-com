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
<div id="flyer-wrap">
<?php if(!empty($this->item->header)){
    echo $this->item->header;
}
if(!empty($this->item->sections)){
    foreach($this->item->sections as $s){
        echo $s->banner;
        if(!empty($s->rows)){
            foreach($s->rows as $r){
                if(!empty($r->block1)){
                    echo '<div class="flyer-block1">';
                    echo $r->block1->product_content;
                    echo $r->block1->content;
                    echo '</div>';
                }
                if(!empty($r->block2)){
                    echo '<div class="flyer-block2">';
                    echo $r->block2->product_content;
                    echo $r->block2->content;
                    echo '</div>';
                }
                if(!empty($r->block3)){
                    echo '<div class="flyer-block3" style="float:left; margin-bottom: 20px;">';
                    echo $r->block3->product_content;
                    echo $r->block3->content;
                    echo '</div>';
                }
                if(!empty($r->block4)){
                    echo '<div class="flyer-block4" style="float:left; margin-bottom: 20px;">';
                    echo $r->block4->product_content;
                    echo $r->block4->content;
                    echo '</div>';
                }
            }
        }
    }
}
if(!empty($this->item->footer)){
    echo $this->item->footer;
}?> 
</div>
    