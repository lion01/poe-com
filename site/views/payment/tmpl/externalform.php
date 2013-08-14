<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
//echo $this->url;

//$url = "https://www.paypal.com/cgi-bin/webscr?item_name_1=ORDER+%23++-+&item_number_1=CPSOL-1.2.7&quantity_1=1&amount_1=50&cmd=_cart&upload=1&business=micah%40exps.ca&receiver_email=micah%40exps.ca&address_override=0&cpp_header_image=&return=%2Fpoecom25_dev%2Findex.php%3Foption%3Dcom_poecom%26task%3Dpayment.return&notify_url=%2Fpoecom25_dev%2Fplugins%2Fpoecom%2Fpaymentapi%2Fnotify.php&cancel_return=%2Fpoecom25_dev%2Findex.php%3Foption%3Dcom_poecom%26task%3Dpayment.cancel&test_ipn=&no_shipping=1&no_note=1https%3A%2F%2Fwww.paypal.com%2Fcgi-bin%2Fwebscr%3Fitem_name_1%3DORDER%2B%2523%2B%2B-%2B%26amp%3Bitem_number_1%3DCPSOL-1.2.7%26amp%3Bquantity_1%3D1%26amp%3Bamount_1%3D50%26amp%3Bcmd%3D_cart%26amp%3Bupload%3D1%26amp%3Bbusiness%3Dmicah%2540exps.ca%26amp%3Breceiver_email%3Dmicah%2540exps.ca%26amp%3Baddress_override%3D0%26amp%3Bcpp_header_image%3D%26amp%3Breturn%3D%252Fpoecom25_dev%252Findex.php%253Foption%253Dcom_poecom%2526task%253Dpayment.return%26amp%3Bnotify_url%3D%252Fpoecom25_dev%252Fplugins%252Fpoecom%252Fpaymentapi%252Fnotify.php%26amp%3Bcancel_return%3D%252Fpoecom25_dev%252Findex.php%253Foption%253Dcom_poecom%2526task%253Dpayment.cancel%26amp%3Btest_ipn%3D%26amp%3Bno_shipping%3D1%26amp%3Bno_note%3D1";

//$app->redirect($this->request_query);
$app->redirect('http://www.exps.ca');

