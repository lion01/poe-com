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
* Product Image Modal form field class
*/
class JFormFieldImageModal extends JFormField{
    /**
    * field type
    * @var string
    */
    protected $type = 'ImageModal';
    
    /**
    * Method to get the field input markup
    */
    protected function getInput(){
        // Load modal behavior
        JHtml::_('behavior.modal', 'a.modal');

        // Build the script
        $script = array();
        $script[] = '    function jSelectNumber_'.$this->id.'(id, id, object) {';
        $script[] = '        document.id("'.$this->id.'").value = id;';
        $script[] = '        document.id("'.$this->id.'_name").value = id;';
        $script[] = '        SqueezeBox.close();';
        $script[] = '    }';

        // Add to document head
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Setup variables for display
        $html = array();
        $link = 'index.php?option=com_poecom&amp;view=images&amp;layout=modal'.
                '&amp;tmpl=component&amp;function=jSelectNumber_'.$this->id;
        if($this->value > 0){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__poe_product_image');
            $query->where('id='.(int)$this->value);
            $db->setQuery($query);
         
            if (!$id = $db->loadResult()) {
                JError::raiseWarning(500, $db->getErrorMsg('no record found'));
            }
        }
        
        if (empty($id)) {
            $id = JText::_('COM_POECOM_F_SELECT_IMAGE');
        }
        $id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

        // The current image input field
        $html[] = '<div class="fltlft">';
        $html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$id.'" disabled="disabled" size="35" />';
        $html[] = '</div>';

        // The image select button
        $html[] = '<div class="button2-left">';
        $html[] = '  <div class="blank">';
        $html[] = '    <a id="imageModal" class="modal" title="'.JText::_('COM_POECOM_F_SELECT_IMAGE').'" href="'.$link.
                        '" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
                        JText::_('COM_POECOM_BUTTON_SELECT_IMAGE').'</a>';
        $html[] = '  </div>';
        $html[] = '</div>';
 
        // The active transaction id field
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