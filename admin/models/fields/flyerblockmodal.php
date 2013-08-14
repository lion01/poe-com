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
jimport('joomla.form.formfield');

/**
 * Flyer Block Modal form field class
 */
class JFormFieldFlyerBlockModal extends JFormField {

    /**
     * field type
     * @var string
     */
    protected $type = 'FlyerBlockModal';

    /**
     * Method to get the field input markup
     */
    protected function getInput() {
        // Load modal behavior
        JHtml::_('behavior.modal', 'a.modal');

        // Build the script
        $script = array();
        $script[] = '    function jSelectBlock_' . $this->id . '(id, title, object) {';
        $script[] = '        document.id("' . $this->id . '").value = id;';
        $script[] = '        document.id("' . $this->id . '_name").value = title;';
        $script[] = '        SqueezeBox.close();';
        $script[] = '    }';
        $script[] = '    function clearBlock_' . $this->id . '(){';
        $script[] = '        document.id("' . $this->id . '").value = "";';
        $script[] = '        document.id("' . $this->id . '_name").value = "'.JText::_('COM_POECOM_F_SELECT_FLYER_BLOCK').'";';
        $script[] = '    }';

        // Add to document head
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Setup variables for display
        $html = array();
        $link = 'index.php?option=com_poecom&amp;view=flyerblocks&amp;layout=modal' .
                '&amp;tmpl=component&amp;function=jSelectBlock_' . $this->id;
        if ($this->value > 0 ) {
            $db = JFactory::getDbo();
            $q = $db->getQuery(true);
            $q->select('id, title');
            $q->from('#__poe_flyer_block');
            $q->where('id=' . (int) $this->value);

            $db->setQuery($q);
            $block = $db->loadObject();

            if (!$block) {
                $app = JFactory::getApplication();
                $msg = $db->getErrorMsg();
                if(empty($msg)){
                    //no error, but no record in result
                    $msg = JText::_('COM_POECOM_NO_RECORDS');
                }
                
                $app->enqueueMessage($msg, 'info');
            }
        }

        if (empty($block->id)) {
            $title = JText::_('COM_POECOM_F_SELECT_FLYER_BLOCK');
        }else{
            $title = htmlspecialchars($block->title, ENT_QUOTES, 'UTF-8');
        }

        // The current image input field
        $html[] = '<div class="fltlft">';
        $html[] = '  <input type="text" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
        $html[] = '</div>';

        // The image select button
        $html[] = '<div class="button2-left">';
        $html[] = '  <div class="blank">';
        $html[] = '    <a id="blockModal" class="modal" title="' . JText::_('COM_POECOM_F_SELECT_FLYER_BLOCK') . '" href="' . $link .
                '" rel="{handler: \'iframe\', size: {x:800, y:450}}">' .
                JText::_('COM_POECOM_BUTTON_SELECT_FLYER_BLOCK') . '</a>';
        $html[] = '  </div>';
        $html[] = '</div>';
        
        //clear button
        $html[] = '<div><button type="button" onclick="clearBlock_'.$this->id.'()">Remove</button></div>';

        // The active transaction id field
        if (0 == (int) $this->value) {
            $value = '';
        } else {
            $value = (int) $this->value;
        }

        // class='required' for client side validation
        $class = '';
        if ($this->required) {
            $class = ' class="required modal-value"';
        }

        $html[] = '<input type="hidden" id="' . $this->id . '"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

        return implode("\n", $html);
    }

}