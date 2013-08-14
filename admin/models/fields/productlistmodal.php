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
 jimport('joomla.form.formfield');
 
/**
* Product Modal form field class
*/
class JFormFieldProductListModal extends JFormField{
    /**
    * field type
    * @var string
    */
    protected $type = 'ProductListModal';
    
    /**
    * Method to get the field input markup
    */
    protected function getInput(){
        // Load modal behavior
        JHtml::_('behavior.modal', 'a.modal');

        // Build the script
        $script = array();
        $script[] = '    function jSelectProduct_'.$this->id.'(id, name, object) {';
        $script[] = '        document.id("'.$this->id.'").value = id;';
        $script[] = '        document.id("'.$this->id.'_name").value = name;';
        $script[] = '        SqueezeBox.close();';
        
        if(!empty($this->element['onchange'])){
            $script[] =  $this->element['onchange'].';';
        }
        
        $script[] = '    }';

        // Add to document head
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Setup variables for display
        $html = array();
        $link = 'index.php?option=com_poecom&amp;view=products&amp;layout=modal'.
                '&amp;tmpl=component&amp;function=jSelectProduct_'.$this->id;
        if($this->value > 0){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id, name, sku');
            $query->from('#__poe_product');
            $query->where('id='.(int)$this->value);
            $db->setQuery($query);
         
            if (!$product = $db->loadObject()) {
                JError::raiseWarning(500, $db->getErrorMsg('no record found'));
            }
        }
        
        if (empty($product)) {
            $name = JText::_('COM_POECOM_SELECT_PRODUCT');
        }else{
            $name = htmlspecialchars($product->name . ' : ' . $product->sku, ENT_QUOTES, 'UTF-8');
        }
        

        // The current image input field
        $html[] = '<div class="fltlft">';
        $html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$name.'" disabled="disabled" size="35" />';
        $html[] = '</div>';

        // The image select button
        $html[] = '<div class="button2-left">';
        $html[] = '  <div class="blank">';
        $html[] = '    <a id="productModal" class="modal" title="'.JText::_('COM_POECOM_SELECT_PRODUCT').'" href="'.$link.
                        '" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
                        JText::_('COM_POECOM_BUTTON_SELECT_PRODUCT').'</a>';
        $html[] = '  </div>';
        $html[] = '</div>';
 
        // The active transaction id field
        if (0 == (int)$this->value) {
            $value = '';
        } else {
            $value = (int)$this->value;
        }
 
        // required added to element['class'] when required="true" in XML
        $class = '';
 
        if (!empty($this->element['class'])) {
                $class .= ' '.$this->element['class'];
        }
        
        if(!empty($class)){
            $class = ' class="'.trim($class).'"';
        }
        
        $html[] = '<input type="hidden" id="'.$this->id.'"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

        return implode("\n", $html);
  }
 
}