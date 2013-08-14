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
    #poe-reg-user-exists{
	width: 100%;
	text-align: center;
    }
    
    #poe-msg{
	fontweight: bold;
	font-size: 1.5em;
	margin: 1.0em;
    }
    
    .poe-show-login{
	margin: 1.0em;
    }
</style>
<div id="poe-reg-user-exists">
    <div id="poe-msg"><?php echo JText::_('COM_POECOM_USERNAME_EXISTS_MSG'); ?></div>
    <div class="poe-show-login"><button class="poe-button poe-corner-all" type="button" id="showLogin" onclick="closeMsgModal(true)"><?php echo JText::_('COM_POECOM_GOTO_LOGIN_BUT'); ?></button></div>
    <div call="poe-show-login"><button class="poe-button poe-corner-all" type="button" id="showCart" onclick="closeMsgModal(false)"><?php echo JText::_('COM_POECOM_GOTO_CART_BUT'); ?></button></div>
</div>
<script src="<?php echo $this->script?>" type="text/javascript"></script>