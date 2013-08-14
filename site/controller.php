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
jimport('joomla.application.component.controller');

/**
 * Base controller class for Poecom
 * 
 * @package	Joomla.Site
 * @subpackage	com_poecom
 * @since	1.7
 */
class PoecomController extends JController {

    /**
     * Method to display a view.
     *
     * @param	boolean $cachable Not used
     * @param	array	$urlParams  Not used An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController This object to support chaining.
     * @since	1.7
     */
    public function display($cachable = false, $urlparams = false) {
        $app = JFactory::getApplication();
        $jinput = $app->input;

        $document = JFactory::getDocument();
        //get view parameters
        $viewType = $document->getType();
        $viewName = $jinput->get('view', 'poecom', 'CMD');
        $viewLayout = $jinput->get('layout', 'default', 'CMD');

        // Set the view              
        $jinput->set('view', $viewName);
        $jinput->set('layout', $viewLayout);

        // Get the view object
        $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

        // Get/Create the default model
        if (($model = $this->getModel($viewName))) {
            // Push the model into the view (as default)
            $view->setModel($model, true);
        }

        // Set view specific models only as needed
        // use unique model names becuase $view->setModel() assigns $model as reference
        switch ($viewName) {
            case 'product':
                if (($optionsModel = $this->getModel('Options'))) {
                    $view->setModel($optionsModel);
                }
                if (($currencyModel = $this->getModel('Currencies'))) {
                    $view->setModel($currencyModel);
                }
                if (($cartModel = $this->getModel('Cart'))) {
                    $view->setModel($cartModel);
                }
                if (($rpModel = $this->getModel('RelatedProducts'))) {
                    $view->setModel($rpModel);
                }
                break;
            case 'productcategory':
                if (($orderStatusModel = $this->getModel('OrderStatus'))) {
                    $view->setModel($orderStatusModel);
                }
                if (($currencyModel = $this->getModel('Currencies'))) {
                    $view->setModel($currencyModel);
                }
                break;
            case 'flyer':
                if (($currencyModel = $this->getModel('Currencies'))) {
                    $view->setModel($currencyModel);
                }
                break;
            case 'cart':
                if (($productModel = $this->getModel('Product'))) {
                    $view->setModel($productModel);
                }
                if (($optionsModel = $this->getModel('Options'))) {
                    $view->setModel($optionsModel);
                }
                if (($currencyModel = $this->getModel('Currencies'))) {
                    $view->setModel($currencyModel);
                }
                if (($taxModel = $this->getModel('Tax'))) {
                    $view->setModel($taxModel);
                }
                if (($addressModel = $this->getModel('Address'))) {
                    $view->setModel($addressModel);
                }
                if (($shipModel = $this->getModel('Shipping'))) {
                    $view->setModel($shipModel);
                }
                if (($paymentModel = $this->getModel('Payment'))) {
                    $view->setModel($paymentModel);
                }
                if (($couponModel = $this->getModel('Coupon'))) {
                    $view->setModel($couponModel);
                }
                break;
            default:
                break;
        }

        $view->assignRef('document', $document);

        // Display the view - no caching in admin
        $view->display();

        return $this;
    }
}
