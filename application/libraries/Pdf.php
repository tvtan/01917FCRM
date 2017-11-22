<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

class Pdf extends TCPDF
{
	public $_fonts_list = array();
	protected $last_page_flag = false;
	private $pdf_type = '';

	function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false,$pdf_type = '', $has_background=false,$footer=0)
	{
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		$this->has_background = $has_background;
		$this->footer = $footer;
		$this->SetTopMargin(PDF_MARGIN_TOP);

		$this->pdf_type = $pdf_type;
		$lg = array();
		$lg['a_meta_charset'] = 'UTF-8';
		// set some language-dependent strings (optional)
		$this->setLanguageArray($lg);
		$this->_fonts_list = $this->fontlist;
		ob_end_clean();
	}

	public function Close() {
		$this->last_page_flag = true;
		parent::Close();
	}

	public function WatermarkText() {
		$this->setPage( 1 );
		// Get the page width/height
		$myPageWidth = $this->getPageWidth();
		$myPageHeight = $this->getPageHeight();
		// Find the middle of the page and adjust.
		$myX = ( $myPageWidth / 2 ) - 85;
		$myY = ( $myPageHeight / 2 )-45;
		// Set the transparency of the text to really light
		$this->SetAlpha(0.1);
		// Rotate 45 degrees and write the watermarking text
		$this->StartTransform();
		// $this->Rotate(45, $myX, $myY);
		$this->SetFont("helveticaB", "B", 80);
		$this->writeHTMLCell('', '', $myX, $myY, "<b>DUDOFF</b>", 0, 0, false, true, ('C'), true);
		$this->ln(30);
		$y=$this->getY();
		$this->SetFont("helveticaB", "B", 50);
		$this->writeHTMLCell('', '', $myX, $y, "<b>London</b>", 0, 0, false, true, ('C'), true);
		$this->StopTransform();
		// Reset the transparency to default
		$this->SetAlpha(1);
	}

	public function WatermarkImage() {
		//Water Mark With Image:
		$ImageW = 105; //WaterMark Size
		$ImageH = 80;

	    $this->setPage( 1 ); //WaterMark Page    

	    $myPageWidth = $this->getPageWidth();
	    $myPageHeight = $this->getPageHeight();
	    $myX = ( $myPageWidth / 2 ) - 50;  //WaterMark Positioning
	    $myY = ( $myPageHeight / 2 ) -40;

	        $this->SetAlpha(0.09);
	    $this->Image(K_PATH_IMAGES.'SACS.png', $myX, $myY, $ImageW, $ImageH, '', '', '', true, 150);

	    $this->setPage( 2 );

	    $myPageWidth = $this->getPageWidth();
	    $myPageHeight = $this->getPageHeight();
	    $myX = ( $myPageWidth / 2 ) - 50;
	    $myY = ( $myPageHeight / 2 ) -40;

        $this->SetAlpha(0.09);
	    $this->Image(K_PATH_IMAGES.'SACS.png', $myX, $myY, $ImageW, $ImageH, '', '', '', true, 150);

		//Likewise can be added for all pages after writing all pages.

		$this->SetAlpha(1);
	}

	public function WatermarkImage2() {
		// get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        $this->Image(K_PATH_IMAGES.'background_pdf.png', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();


	}


	public function Header() {

		if($this->has_background)
		{
			// get the current page break margin
	        $bMargin = $this->getBreakMargin();
	        // get current auto-page-break mode
	        $auto_page_break = $this->AutoPageBreak;
	        // disable auto-page-break
	        $this->SetAutoPageBreak(false, 0);
	        // set bacground image
	        $this->Image(K_PATH_IMAGES.'background_pdf.png', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
	        // restore auto-page-break status
	        $this->SetAutoPageBreak($auto_page_break, $bMargin);
	        // set the starting point for the page content
	        $this->setPageMark();
    	}

		$this->SetFont('helvetica', 'B', 20);
		if(get_option('prefix_header_pdf') !=""){
		 	$this->SetTextColor(142,142,142);
		 	$y            = $this->getY();
		 $this->writeHTMLCell('', '', '', $y+7, '<img src="'.get_option('prefix_header_pdf').'">', 0, 0, false, true, 'J', true);
		 	// $this->Cell(0, 15, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		 }
	}

	public function printHeader($extraY=0) {
		
		if(get_option('prefix_header_pdf') !=""){
		 	// $this->SetTextColor(142,142,142);
		 	$y            = $this->getY()+$extraY;
		 $this->writeHTMLCell('', '', '', $y, '<img src="'.get_option('prefix_header_pdf').'">', 0, 0, false, true, 'J', true);
		 	// restore auto-page-break status
	        $this->SetAutoPageBreak($auto_page_break, $bMargin);
	        // set the starting point for the page content
	        $this->setPageMark();
		 }
	}

	public function printHeader2($extraY=0) {
		
		if(get_option('pdf_header') !=""){
		 	// $this->SetTextColor(142,142,142);
		 	$y            = $this->getY()+$extraY;
		 $this->writeHTMLCell('', '', '', $y, '<img src="'.get_option('pdf_header').'">', 0, 0, false, true, 'J', true);
		 	// restore auto-page-break status
	        $this->SetAutoPageBreak($auto_page_break, $bMargin);
	        // set the starting point for the page content
	        $this->setPageMark();
		 }
	}

	public function Footer() {
        // Position at 15 mm from bottom
	
	
		$font_name = get_option('pdf_font');
	    $font_size = get_option('pdf_font_size');

	    if ($font_size == '') {
	        $font_size = 10;
	    }
	    $this->SetFont($font_name, '', $font_size);
		$this->SetFont('freesans', '', 8);
		if($this->footer==1){
				$this->SetY(-35);
			$check_note='<div style="width:100%;">'._l('check_note2').'</div>';
		}elseif($this->footer==2){
				$this->SetY(-30);
			$check_note='<div style="width:100%;">'._l('check_note3').'</div>';
		}	
		$this->writeHTMLCell('', '', '', '', $check_note, 0, 0, false, true, ('L'), true);

		// $this->SetFont($font_name, '', $font_size);

		// do_action('pdf_footer',array('pdf_instance'=>$this,'type'=>$this->pdf_type));
  //       // Set font
		// $this->SetFont('helvetica', 'I', 8);
		// if(get_option('show_page_number_on_pdf') == 1){
		// 	$this->SetTextColor(142,142,142);
		// 	// $y            = $this->getY();
		// // $this->writeHTMLCell('', '', '', $y, _l('divider'), 0, 0, false, true, 'J', true);
		// $this->Cell(0, 15,'ss', 0, false, 'R', 0, '', 0, false, 'T', 'M');
		// }
	}

	public function get_fonts_list(){
		return $this->_fonts_list;
	}

}

/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */
