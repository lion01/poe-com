<?php

defined('_JEXEC') or die('Restricted access');

/**
 * Helepr class for coupon PDF creation
 * 
 * Requires the TCPDF library
 * http://www.tcpdf.org/
 *  
 */
class CouponPDF {

    function __construct() {
        //nothing yet
    }

    public function generateFiles($promotion_id = 0, $template_file = '', $pdf_folder = '') {
        if (!$promotion_id > 0 || !strlen($template_file) || !strlen($pdf_folder)) {
            return false;
        }

        $model = JModel::getInstance('Promotion', 'PoecomModel');
        $promotion = $model->getItem($promotion_id);

        if ($promotion) {
            //set the output folder
            $folder = JPATH_ROOT . $pdf_folder;
            $promotion_folder = $folder . $promotion->name . "/";

            //if folder does not exist create it
            if (!JFolder::exists($promotion_folder)) {
                JFolder::create($promotion_folder);

                //copy index.html
                if (JFile::exists($folder . 'index.html')) {
                    JFile::copy($folder . 'index.html', $promotion_folder . 'index.html');
                }
            }

            //get the coupons
            $model = JModel::getInstance('Coupon', 'PoecomModel');
            $coupons = $model->getPromotionCoupons($promotion->id);

            if ($coupons) {
                $failed_coupons = array();


                foreach ($coupons as $coup) {
                    //create the html
                    $html = $this->createHTML($promotion, $coup, $template_file);


                    $this->outputFile($promotion_folder, $coup->coupon_code, $html);

                    $file = $promotion_folder . $coup->coupon_code . ".pdf";

                    //check that file exists
                    if (!JFile::exists($file)) {
                        $failed_coupons[] = $coup->id;
                    } else {
                        //update coupon 
                        $data = JArrayHelper::fromObject($coup);
                        $data['pdf_file'] = $pdf_folder . $promotion->name . "/" . $coup->coupon_code . ".pdf";

                        $model->save($data);
                    }
                }

                if ($failed_coupons) {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    private function createHtml($promotion, $coupon, $template) {
        $html = 'File contents not read';
        //check that $template file exists
        if (JFile::exists($template)) {
            $file_contents = JFile::read($template);

            $file_contents = str_replace('##EXPIRY', $promotion->end_time, $file_contents);
            $file_contents = str_replace('##COUPON_CODE', $coupon->coupon_code, $file_contents);

            $html = $file_contents;
        }
        return $html;
    }

    /**
     * Create PDF and output to $filename
     * 
     * @param string $file_name The full path to a file
     */
    private function outputFile($path = '', $file_name = '', $html = '') {
        require_once(JPATH_ADMINISTRATOR . '/components/com_poecom/tcpdf/config/lang/eng.php');
        require_once(JPATH_ADMINISTRATOR . '/components/com_poecom/tcpdf/tcpdf.php');

        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('POE-com Promotions');
        $pdf->SetTitle('Promotion');
        $pdf->SetSubject('Coupon');
        $pdf->SetKeywords('coupon, promotion, discount');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        //$pdf->setLanguageArray($l);
        // set font
        $pdf->SetFont('times', 'B', 20);

        // add a page
        $pdf->AddPage();

        //print using WriteHTML()
        $pdf->WriteHTML($html, true, false, true, false, 'C');

        //Close and output PDF document
        $file = $path . $file_name;
        $pdf->Output($file . '.pdf', 'F');
    }

}

?>
