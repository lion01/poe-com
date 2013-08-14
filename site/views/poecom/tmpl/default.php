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
<div id="pope-login-wrap">
    <div id="poe-login-container">
	<div id="poe-login">
	    <div id="poe-login-info"><?php echo JText::_('COM_POECOM_LOGIN_INFO'); ?></div>
	    <div class="poe-login-lbl"><?php echo JText::_('COM_POECOM_LOGIN_USER')?></div>
	    <div class="poe-login-input"><input type="text" id="username" value=""/></div>
	    <div class="poe-login-lbl"><?php echo JText::_('COM_POECOM_LOGIN_PASS')?></div>
	    <div class="poe-login-input"><input type="password" id="password" value=""/></div>
	    <div id="poe-login-forgot">
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('COM_POECOM_LOGIN_FORGOT_PASS'); ?></a>
	    </div>
	    <div id="jtoken"><?php echo JHTML::_( 'form.token' ); ?></div>
	    <div id="poe-login-but-wrap">
		<div id="poe-login-but1"><button type="button" class="poe-button poe-corner-all" id="loginnow" onclick="tryLogin()"><?php echo JText::_('COM_POECOM_LOGIN_BUT'); ?></button></div>
		<div id="poe-login-but2"><button type="button" class="poe-button poe-corner-all" id="skiplogin" onclick="skipLogin()"><?php echo JText::_('COM_POECOM_NO_LOGIN_BUT'); ?></button></div>
	    </div>
	</div>
    </div>
</div>
<script src="<?php echo $this->script; ?>" type="text/javascript"></script>

