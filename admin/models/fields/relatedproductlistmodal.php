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
* Related Product List Modal form field class
*/
class JFormFieldRelatedProductListModal extends JFormField{
    /**
    * field type
    * @var string
    */
    protected $type = 'RelatedProductListModal';
    
    /**
    * Method to get the field input markup
    */
    protected function getInput(){
        // Load modal behavior
        JHtml::_('behavior.modal', 'a.modal');

        // Build the script
        $script = array();
        $script[] = '    function jSelectRelatedGroup_'.$this->id.'(id, name) {';
        $script[] = '        document.id("'.$this->id.'").value = id;';
        $script[] = '        document.id("'.$this->id.'_name").value = name;';
        $script[] = '        SqueezeBox.close();';
        $script[] = '    }';

        // Add to document head
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Setup variables for display
        $html = array();
        $link = 'index.php?option=com_poecom&amp;view=relatedgroups&amp;layout=modal'.
                '&amp;tmpl=component&amp;function=jSelectRelatedGroup_'.$this->id;
        if($this->value > 0){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('rg.id,rg.name');
            $query->from('#__poe_related_product_group rg');
            $query->where('rg.id='.(int)$this->value);
            $db->setQuery($query);
         
            $related_group = $db->loadObject();
        }
        
        if (empty($related_group)) {
            $name = JText::_('COM_POECOM_SELECT_RELATED_GROUP');
        }else{
            $name = htmlspecialchars($related_group->name, ENT_QUOTES, 'UTF-8');
        }
        

        // The current image input field
        $html[] = '<div class="fltlft">';
        $html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$name.'" disabled="disabled" size="40" />';
        $html[] = '</div>';

        // The image select button
        $html[] = '<div class="button2-left">';
        $html[] = '  <div class="blank">';
        $html[] = '    <a id="relatedGroupModal" class="modal" title="'.JText::_('COM_POECOM_SELECT_RELATED_GROUP').'" href="'.$link.
                        '" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
                        JText::_('COM_POECOM_BUTTON_SELECT_RELATED_GROUP').'</a>';
        $html[] = '  </div>';
        $html[] = '</div>';
 
        // The active related group id field
        if (0 == (int)$this->value) {
            $value = '';
        } else {
            $value = (int)$this->value;
        }
 
        // class='required' for client side validation
        $class = '';
        if ($this->required) {
                $class = ' class="required modal-value"';
        }

        $html[] = '<input type="hidden" id="'.$this->id.'"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

        return implode("\n", $html);
  }
}