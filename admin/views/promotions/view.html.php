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
jimport('joomla.application.component.view');
 
/**
 * Promotions List View
 */
class PoecomViewPromotions extends JView{
    
    protected $items;
    protected $pagination;
    protected $state;
    protected $script;

    /**
    * PayTransactions view display method
    * @return void
    */
    function display($tpl = null){
	$app = JFactory::getApplication();
	$jinput = $app->input;

	$layout = $jinput->get('layout', 'default', 'STRING');
	
	switch($layout){
	    case 'generate':
		$promotion_id = $jinput->get('promotion_id', 0, 'INT');
		$promotion = array();
		
		if($promotion_id > 0){
		    $model = $this->getModel('Promotion');
		    $promotion = $model->getItem($promotion_id);
		}
		
		$model = $this->getModel('Promotions');
		$user_list = $model->getUserList();
		
		$this->assignRef('promotion', $promotion);
		$this->assignRef('user_list', $user_list);
		
		
		if($jinput->post->get('submit','','STRING') == JText::_('COM_POECOM_GENERATE_COUPONS')){
		    
		    $params = JComponentHelper::getParams('com_poecom');
		    
		    $error = array('error' => false, 'msg' => '');
		    $promotion_type_id = $jinput->get('promotion_type_id', 0, 'INT');
		    
		    
		    $data = array();
		    $data['promotion_id'] = $promotion_id;
		    $data['promotion_type_id'] = $promotion_type_id;
		    $data['user_id'] = 0;
		    $data['rfq_id'] = 0;
		    $data['order_id'] = 0;
		    $data['status_id'] = 1;
		    
		    $model = $this->getModel('Coupon');
		   
		    switch($promotion_type_id){
			case '1': //Customer Direct, coupon for each user id
			    $users = $jinput->get('promo_users','','ARRAY');
			    
			    if($users){
				foreach($users as $user){
				    //set id to prevent update!
				    $data['id'] = "";
				    $data['user_id'] = $user;
				    
				    $data['coupon_code'] = $model->generateCouponCode();
				    
				    if((!$model->save($data))){
					$error['error'] = true;
					$error['msg'] = JText::_('COM_POECOM_COUPON_SAVE_ERROR_MSG').$user. " ";
				    }
				}
			    }else{
				$error['error'] = true;
				$error['msg'] = JText::_('COM_POECOM_COUPON_SAVE_ERROR_MSG_NO_USERS');
			    }
			    break;
			case '2': //General, one coupon
			    $data['coupon_code'] = $model->generateCouponCode();
				    
			    if((!$model->save($data))){
				$error['error'] = true;
				$error['msg'] = JText::_('COM_POECOM_COUPON_SAVE_ERROR_MSG').$model->getError();
			    }
			    break;
			case '3': //Numbered, sequential numbered coupons
			    //Check if promotion already has coupons 
			    //If found get last sequence
			    $model = $this->getModel('Promotions');
			    $last_sequence = $model->getLastSequence($promotion_id);
			    
			    $start = $last_sequence >= 1 ? $last_sequence : 1;
			    
			    $coupon_count = $jinput->get('coupon_count', 0, 'INT');
			    
			    $digits = $params->get('numberlength', 6);
			    $pad = $params->get('numberpadchar', '0');
			    
			    $model = $this->getModel('Coupon');
			    for($i=$start; $i<=($coupon_count+$last_sequence); $i++){
				$number = $i;
				while(strlen($number) < $digits){
				    $number = $pad.$number;
				}
				//set id to prevent update!
				$data['id'] = "";
				$data['coupon_code'] = $model->generateCouponCode()."-".$number;
				$data['sequence_number'] = $i;
				
				if((!$model->save($data))){
				    $error['error'] = true;
				    $error['msg'] = JText::_('COM_POECOM_COUPON_SAVE_ERROR_MSG').$number. " ";
				}    
			    }
			    break;
			default:
			    $error['error'] = true;
			    $error['msg'] = JText::_('COM_POECOM_PROMOTION_TYPE_MISSING_ERROR_MSG');
			    break;
		    }
		    
		    if(!$error['error']){
			$coupons_created = true;
		    }else{
			$coupons_created = false;
		    }
		    
		    
		    
		    $this->assignRef('error', $error);
		    $this->assignRef('coupons_created', $coupons_created);
		}
		
		$this->setModalDocument();
		
		$this->assignRef('script', $this->script);
		break;
	    case 'generatepdf':
		$promotion_id = $jinput->get('promotion_id', 0, 'INT');
		$promotion = array();
		$has_coupons = false;
		$error = array('error' => false, 'msg' => '');
		$pdf_created = false;
		$templates = array();
		$templates[] = array('id' => 0, 'name' => '--Select--');
		
		$params = JComponentHelper::getParams('com_poecom');
		$template_folder = $params->get('pdftemplatefolder', '');
		$pdf_folder = $params->get('pdffolder', '');
		
		$files = JFolder::files(JPATH_ROOT.$template_folder, '.',false,false,array('index.html'));
		if($files){
		    foreach($files as $f){
			$templates[] = array('id' => $f, 'name' => $f);
		    }
		}
		
		if($promotion_id > 0){
		    $model = $this->getModel('Promotion');
		    $promotion = $model->getItem($promotion_id);

		    //check for coupons
		    if((!$has_coupons = $model->hasCoupons($promotion_id))){
			$error['error'] = true;
			$error['msg'] = JText::_('COM_POECOM_PROMOTION_NO_COUPONS_ERROR_MSG');
		    } 
		}
		
		if($jinput->post->get('submit','','STRING') == JText::_('COM_POECOM_GENERATE_PDF')){
		    //handle post submit
		    
		    $pdf_template = $jinput->get('pdf_template', '', 'STRING');
		   
		    if(strlen($pdf_template) <= 1 ){
			$error['error'] = true;
			$error['msg'] = JText::_('COM_POECOM_COUPON_PDF_GEN_NO_TEMPLATE_ERROR_MSG');
		    }else{
			$filepath = JPATH_ADMINISTRATOR.'/components/com_poecom/helpers/coupon_pdf.php';
		    
			$template = JPATH_ROOT.$template_folder.$pdf_template;

			JLoader::register('CouponPDF', $filepath);
			$pdf_coupon = new CouponPDF();
			if((!$pdf_coupon->generateFiles($promotion_id, $template, $pdf_folder))){
			    $error['error'] = true;
			    $error['msg'] = JText::_('COM_POECOM_COUPON_PDF_GEN_ERROR_MSG');
			}else{
			    $pdf_created = true;
			}
		    }
		    
		    
		    
		}
		
		$this->assignRef('promotion', $promotion);
		$this->assignRef('has_coupons', $has_coupons);
		$this->assignRef('pdf_created', $pdf_created);
		$this->assignRef('error', $error);
		$this->assignRef('templates', $templates);
		
		break;
	    default:
		// Get data from the model and assign to view
		$this->items = $this->get('Items');
		$this->pagination = $pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->script = $this->get('Script');

		// Check for errors.
		if (count($errors = $this->get('Errors'))){
		    JError::raiseError(500, implode('<br />', $errors));
		    return false;
		}

		// Set transaction type filter
		$model = $this->getModel('Promotions');
		$type_list = $model->getPromotionTypeList();
		

		$this->assignRef('type_list', $type_list);
		

		// Set transaction discount filter
		$discount_type_list = $model->getDiscountTypeList();

		$this->assignRef('discount_type_list', $discount_type_list);

		// Set the toolbar
		$this->addToolBar();

		// Set document attributes
		$this->setDocument();

		break;
	}

        // Display the template
        parent::display($tpl);
    }
    
    /**
    * Setting the toolbar
    */
    protected function addToolBar(){
        JLoader::register('PromotionsHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/promotions.php');
        
        PromotionsHelper::addSubmenu('promotions');
		
        $canDo = PromotionsHelper::getActions();
        
        // Set title and icon class
        JToolBarHelper::title(JText::_('COM_POECOM_PROMOTION_LIST_TITLE'), 'promotion');
	
	if($canDo->get('core.admin')){
	    JToolBarHelper::custom('', 'generate-pdf.png', 'generate-pdf.png',  'COM_POECOM_GENERATE_COUPON_PDF');
	}
	
	if($canDo->get('core.admin')){
	    JToolBarHelper::custom('', 'generate-coupon.png', 'generate-coupon.png',  'COM_POECOM_GENERATE_COUPONS');
	}
        
        if ($canDo->get('core.create')){
                JToolBarHelper::addNew('promotion.add', 'JTOOLBAR_NEW');
        }
        if ($canDo->get('core.edit')){
                JToolBarHelper::editList('promotion.edit', 'JTOOLBAR_EDIT');
        }
        if ($canDo->get('core.delete')){
                JToolBarHelper::deleteList('', 'promotions.delete', 'JTOOLBAR_DELETE');
        }
    }
    
    private function setDocument(){
	JText::script('COM_POECOM_SELECT_ONE_MSG');
	JText::script('COM_POECOM_SELECT_MAX_ONE_MSG');
    }
    
    private function setModalDocument(){
	$doc = JFactory::getDocument();
	$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_poecom/assets/css/poecom.css');
	
	$this->script = JURI::root(true).'/administrator/components/com_poecom/models/forms/coupon.generate.js';
	
	JText::script('COM_POECOM_NO_USERS_SELECTED_MSG');
    }
	
}
