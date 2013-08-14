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
* RFQ Modal form field class
*/
class JFormFieldRFQModal extends JFormField{
    /**
    * field type
    * @var string
    */
    protected $type = 'RFQModal';
    
    /**
    * Method to get the field input markup
    */
    protected function getInput(){
        // Load modal behavior
        JHtml::_('behavior.modal', 'a.modal');

        // Build the script
        $script = array();
        $script[] = '    function jSelectNumber_'.$this->id.'(id, number, object) {';
        $script[] = '        document.id("'.$this->id.'").value = id;';
        $script[] = '        document.id("'.$this->id.'_name").value = number;';
        $script[] = '        SqueezeBox.close();';
        $script[] = '    }';

        // Add to document head
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Setup variables for display
        $html = array();
        $link = 'index.php?option=com_poecom&amp;view=requests&amp;layout=modal'.
                '&amp;tmpl=component&amp;function=jSelectNumber_'.$this->id;

        if(!empty($this->value)){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('number');
            $query->from('#__poe_request');
            $query->where('id='.(int)$this->value);
            $db->setQuery($query);
            if (!$number = $db->loadResult()) {
                JError::raiseWarning(500, $db->getErrorMsg());
            }
        }
        if (empty($number)) {
            $number = JText::_('COM_POECOM_FIELD_SELECT_RFQ_NUMBER');
        }
        $number = htmlspecialchars($number, ENT_QUOTES, 'UTF-8');

        // The current book input field
        $html[] = '<div class="fltlft">';
        $html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$number.'" disabled="disabled" size="35" />';
        $html[] = '</div>';

        // The book select button
        $html[] = '<div class="button2-left">';
        $html[] = '  <div class="blank">';
        $html[] = '    <a class="modal" title="'.JText::_('COM_POECOM_SELECT_RFQ_NUMBER').'" href="'.$link.
                        '" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
                        JText::_('COM_POECOM_BUTTON_SELECT_NUMBER').'</a>';
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