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
 * */
jimport('joomla.application.component.modelitem');

/**
 * FLyer Model
 */
class PoecomModelFlyer extends JModel {

    /**
     * @var object item
     */
    protected $item;
    protected $items;

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return	void
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication();

        // Load the application parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        parent::populateState();
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Flyer', $prefix = 'PoecomTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Get FLyer data
     * 
     * @return object $flyer Data for flyer view template
     */
    public function getFlyer() {

        $jinput = JFactory::getApplication()->input;
        // Get the flyer id from menu param
        $flyerid = $jinput->get('flyerid', 0, 'int');

        if ($flyerid > 0) {
            //get category info
            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->select('f.*');
            $q->from('#__poe_flyer f');
            //$q->leftJoin('#__poe_flyer_section fs ON fs.flyer_id=f.id');
            $q->where('f.id=' . (int) $flyerid);

            $db->setQuery($q);

            if (( $flyer = $db->loadObject())) {
                //get flyer sections
                $q = $db->getQuery(true);
                $q->select('fs.*');
                $q->from('#__poe_flyer_section fs');
                //$q->leftJoin('#__poe_flyer_section fs ON fs.flyer_id=f.id');
                $q->where('fs.flyer_id=' . (int) $flyerid);

                $db->setQuery($q);

                if (($sections = $db->loadObjectList())) {
                    $flyer->sections = $sections;
                } else {
                    $flyer->sections = '';
                }

                if (!empty($flyer->sections)) {
                    $idx = 0;
                    foreach ($flyer->sections as $fs) {
                        $q = $db->getQuery(true);
                        $q->select('r.*');
                        $q->from('#__poe_flyer_row r');
                        $q->where('r.section_id=' . (int) $fs->id);

                        $db->setQuery($q);
                        if (($rows = $db->loadObjectList())) {
                            $idx2 = 0;
                            foreach ($rows as $r) {
                                if (!empty($r->block1)) {
                                    
                                    $block1 = $this->getBlock($r->block1);

                                    if (!empty($block1->product_id) && !empty($block1->template)) {
                                        $block1->product_content = $this->loadTemplate($block1);
                                    }

                                    $rows[$idx2]->block1 = $block1;
                                }
                                if (!empty($r->block2)) {
                                    $block2 = $this->getBlock($r->block2);

                                    if (!empty($block2->product_id) && !empty($block2->template)) {
                                        $block2->product_content = $this->loadTemplate($block2);
                                    }
                                    $rows[$idx2]->block2 = $block2;
                                }
                                
                                if (!empty($r->block3)) {
                                    $block3 = $this->getBlock($r->block3);

                                    if (!empty($block3->product_id) && !empty($block3->template)) {
                                        $block3->product_content = $this->loadTemplate($block3);
                                    }
                                    $rows[$idx2]->block3 = $block3;
                                }
                                if (!empty($r->block4)) {
                                    $block4 = $this->getBlock($r->block4);

                                    if (!empty($block4->product_id) && !empty($block4->template)) {
                                        $block4->product_content = $this->loadTemplate($block4);
                                    }
                                    $rows[$idx2]->block2 = $block4;
                                }
                                $idx2++;
                            }
                            $flyer->sections[$idx]->rows = $rows;
                        } else {
                            $flyer->sections[$idx]->rows = '';
                        }

                        $idx++;
                    }
                }
            }
        } else {
            $flyer = '';
        }

        return $flyer;
    }

    /**
     * Get flyer block content
     * 
     * Content may / may not include product or content values
     * 
     * @param int $id FLyer Block Id
     * @return object $block Block content or empty string
     */
    public function getBlock($id = 0) {
        $block = '';
        if (!empty($id)) {
            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->select('fb.*, p.id,p.description, p.menu_id,p.alias, p.name, p.sku,p.price,p.mainimage,p.thumbimage');
            $q->from('#__poe_flyer_block fb');
            $q->leftJoin('#__poe_product p ON p.id=fb.product_id');
            $q->where('fb.id=' . (int) $id);

            $db->setQuery($q);

            if (($result = $db->loadObject())) {
                $block = $result;

                if (!empty($block->product_id)) {
                    //get product options
                    $optionModel = JModel::getInstance('Options', 'PoecomModel');
                    $block->product_options = $optionModel->getItems($block->product_id);
                }
            }
        }

        return $block;
    }

    public function loadTemplate($block) {
        $content = '';
        $html = '';
        $atc = '';
        $block_content = '';
        
        if (empty($block->product_id) && !empty($block->template)) {
            return $content;
        }

        $file = JPATH_ADMINISTRATOR . '/components/com_poecom/assets/flyer/' . $block->template;
        
        if (JFile::exists($file)) {
            $f = file_get_contents($file);
            
            if (!empty($f)) {
                if (!empty($block->product_options)) {
                    
                    $update_price = 'class="optioninput';
                    foreach ($block->product_options as $op) {
                        $html .= '<div class="flyer-option-container">
            <div class="flyer-option-label">' . $op->name . ':</div>
            <div class="flyer-option-input">';
                        switch ($op->option_type_id) {
                            case '1': // select
                                if ($op->values) {
                                    $html .= JHtml::_('select.genericList', $op->values, $op->dom_element, $update_price, 'option_value', 'option_label', $op->selected_value);
                                }
                                break;
                            case '2': // inputqty
                                $html .= '<input ' . $update_price . ' name="' . $op->dom_element . '" id="' . $op->dom_element . '" value="' . $op->selected_value . '" />';
                                break;
                            case '3': // inputsize
                            case '4': // inputtext
                                $html .= '<input ' . $update_price . ' name="' . $op->dom_element . '" id="' . $op->dom_element . '" value="' . $op->selected_value . '" />';
                                break;
                            case '5': // property
                                $html .= '<label name="' . $op->dom_element . '" id="' . $op->dom_element . '" >' . $op->values[0]->option_label . '</label>';
                                break;
                            default:
                                break;
                        }
                        $html .= '</div></div>';
                    }
                }

                if (!empty($block->content) && $block->content != '<div> </div>') {
                    $block_content = $block->content;
                }
                
                if(!empty($block->menu_id)){
                    $atc = '<a href="'.JRoute::_('index.php?Itemid='.$block->menu_id).'">Place Order</a>';
                }else{
                   $atc = '<a href="'.JRoute::_('index.php?option=com_poecom&view=product&id='.$block->id.':'.$block->alias.'&Itemid=527').'">Place Orders</a>';
                 //  $atc = '<a href="http://poe-com.com">Place Orders</a>'; 
                }
                
                $find = array('{productname}','{productimage}','{productpricetext}','{productprice}','{productdesc}','{productproperties}','{productatc}','{blockcontent}');
                $replace = array($block->name, $block->thumbimage,JText::_('COM_POECOM_FLYER_PRICE_TEXT'), $block->price, $block->description,$html,$atc, $block_content );

            }

            $content = str_ireplace($find, $replace, $f);
        }

        return $content;
    }

}
