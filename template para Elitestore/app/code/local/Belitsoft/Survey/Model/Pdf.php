<?php
/**
 * SugarCRM Connection Model
 *
 * @category   Belitsoft
 * @package    Belitsoft_Sugarcrm
 * @author     Belitsoft <bits@belitsoft.com>
 */

/* @var $this->_engine TCPDF */

class Belitsoft_Survey_Model_Pdf extends Varien_Object
{
	// Scale ratio for images [number of points in user unit]
	protected $_font			= 'freeserif'; //'freesans'; 'dejavusans'; 'helvetica';
	protected $_image_scale		= 2;
	protected $_engine			= null;
	protected $_name			= 'survey';
	protected $_siteurl			= '';
	protected $_header			= null;
	protected $_margin_header	= 5;
	protected $_margin_footer	= 10;
	protected $_margin_top		= 15;
	protected $_margin_bottom	= 15;
	protected $_margin_left		= 15;
	protected $_margin_right	= 15;
	protected $_isRTL			= false;

	protected $_survey			= null;
	protected $_first_answer	= null;
	protected $_answers			= null;
	protected $_questions		= null;

	protected $_graphic			= null;
	protected $_images			= null;
	protected $_appendix		= null;

	public function __construct($options = array())
	{
		parent::__construct();

		$pdf_font = Mage::getModel('belitsoft_survey/config')->getConfigData('pdf_font');
		if($pdf_font) {
			$this->_font = $pdf_font;
		}

		if (isset($options['margin-header'])) {
			$this->_margin_header = $options['margin-header'];
		}

		if (isset($options['margin-footer'])) {
			$this->_margin_footer = $options['margin-footer'];
		}

		if (isset($options['margin-top'])) {
			$this->_margin_top = $options['margin-top'];
		}

		if (isset($options['margin-bottom'])) {
			$this->_margin_bottom = $options['margin-bottom'];
		}

		if (isset($options['margin-left'])) {
			$this->_margin_left = $options['margin-left'];
		}

		if (isset($options['margin-right'])) {
			$this->_margin_right = $options['margin-right'];
		}

		if (isset($options['image-scale'])) {
			$this->_image_scale = $options['image-scale'];
		}

		if (isset($options['pdf-name'])) {
			$this->_name = $options['pdf-name'];
		}

		if (isset($options['site-url'])) {
			$this->_siteurl = $options['site-url'];
		}

		/*
		 * Setup external configuration options
		 */
		define('K_TCPDF_EXTERNAL_CONFIG', true);

		/*
		 * Path options
		 */

		// Installation path
		define("K_PATH_MAIN", Mage::getConfig()->getOptions()->getLibDir() . "/tcpdf");

		// URL path
		define("K_PATH_URL", Mage::getBaseUrl());

		// Fonts path
		define("K_PATH_FONTS", K_PATH_MAIN.'/fonts/');

		// Cache directory path
		define("K_PATH_CACHE", K_PATH_MAIN."/cache");

		// Cache URL path
		define("K_PATH_URL_CACHE", K_PATH_URL."/cache");

		// Images path
		define("K_PATH_IMAGES", K_PATH_MAIN."/images");

		// Blank image path
		define("K_BLANK_IMAGE", K_PATH_IMAGES."/_blank.png");

		/*
		 * Format options
		 */

		// Cell height ratio
		define("K_CELL_HEIGHT_RATIO", 1.25);

		// Magnification scale for titles
		define("K_TITLE_MAGNIFICATION", 1.3);

		// Reduction scale for small font
		define("K_SMALL_RATIO", 2/3);

		// Magnication scale for head
		define("HEAD_MAGNIFICATION", 1.1);

		/*
		 * Create the pdf document
		 */
		require_once(K_PATH_MAIN . '/tcpdf.php');

		// Default settings are a portrait layout with an A4 configuration using millimeters as units
		$this->_engine = new TCPDF();
		$this->_engine->SetCreator($this->_name);
		$this->_engine->SetMargins($this->_margin_left, $this->_margin_top, $this->_margin_right);
		$this->_engine->SetAutoPageBreak(TRUE, $this->_margin_bottom);
		$this->_engine->SetHeaderMargin($this->_margin_header);
		$this->_engine->SetFooterMargin($this->_margin_footer);
		$this->_engine->setImageScale($this->_image_scale);
		$this->_engine->setRTL($this->_isRTL);
		$this->_engine->setHeaderData('', 0,
			($this->_name ? $this->_name : $this->_siteurl),
			($this->_name ? $this->_name.' - ':'')
				. $this->_siteurl.' - '
				. Mage::getModel('core/date')->date('j F, Y, H:i'));

		$this->_engine->setHeaderFont(array($this->_font, '', 7));
		$this->_engine->setFooterFont(array($this->_font, '', 7));
	}

	public function getEngine()
	{
		return $this->_engine;
	}

	public function makeReport($data=array(), $displaySurveyInfo=true)
	{
		if (isset($data['survey'])) {
			$this->_survey = $data['survey'];
		} else if(Mage::registry('survey_survey')) {
			$this->_survey = Mage::registry('survey_survey');
		}

		if (isset($data['first_answer'])) {
			$this->_first_answer = $data['first_answer'];
		} else if(Mage::registry('survey_first_answer')) {
			$this->_first_answer = Mage::registry('survey_first_answer');
		}

		if (isset($data['answers'])) {
			$this->_answers = $data['answers'];
		} else if(Mage::registry('survey_answers')) {
			$this->_answers = Mage::registry('survey_answers');
		}

		if (isset($data['questions'])) {
			$this->_questions = $data['questions'];
		} else if(Mage::registry('survey_questions')) {
			$this->_questions = Mage::registry('survey_questions');
		}

		$this->_engine->AliasNbPages();
		$this->_engine->AddPage();

		$this->makeSurveyInfo();

		$this->makeQuestionsInfo();
	}

	public function makeResults($data=array())
	{
		if (isset($data['survey'])) {
			$this->_survey = $data['survey'];
		} else if(Mage::registry('survey_survey')) {
			$this->_survey = Mage::registry('survey_survey');
		}

		if (isset($data['graphic'])) {
			$this->_graphic = $data['graphic'];
		} else if(Mage::registry('survey_graphics')) {
			$this->_graphic = Mage::registry('survey_graphics');
		}

		if (isset($data['images'])) {
			$this->_images = $data['images'];
		} else if(Mage::registry('survey_results_graphics')) {
			$this->_images = Mage::registry('survey_results_graphics');
		}

		$this->_engine->AliasNbPages();
		$this->_engine->AddPage();

		$this->makeSurveyInfo();

		$this->makeGraphics();

		$this->makeAppendix();
	}

	public function makeSurveyInfo($survey=null, $startNewPage=false)
	{
		if(!$survey) {
			$survey = $this->_survey;
		}

		if(!($survey instanceof Belitsoft_Survey_Model_Survey)) {
			return;
		}

		$this->_engine->SetFont($this->_font, "B", 12);
		$this->_engine->Write(10, Mage::helper('belitsoft_survey')->__('Survey Information'), '', 0);
		$this->_engine->Ln();


		$this->_engine->SetFontSize(8);
		$this->_engine->Write(5, Mage::helper('belitsoft_survey')->__('Name: '), '', 0);

		$this->_engine->SetFont($this->_font, "");
		$this->_engine->Write(5, ' '.Mage::helper('belitsoft_survey')->cleanText($survey->getSurveyName()), '', 0);
		$this->_engine->Ln();

		if($performed_date = $this->_getPerformedDate()) {
			$this->_engine->SetFont($this->_font, "B");
			$this->_engine->Write(5, Mage::helper('belitsoft_survey')->__('Survey performed on: '), '', 0);

			$this->_engine->SetFont($this->_font, "");
			$this->_engine->Write(5, ' '.Mage::helper('belitsoft_survey')->cleanText($performed_date), '', 0);
			$this->_engine->Ln();
		}

		if($description = $survey->getSurveyDescription()) {
			$this->_engine->SetFont($this->_font, "B");
			$this->_engine->Write(5, Mage::helper('belitsoft_survey')->__('Description: '), '', 0);

			$this->_engine->SetFont($this->_font, "");
//			$this->_engine->Write(5, ' '.Mage::helper('belitsoft_survey')->cleanText($description), '', 0);
			$this->_engine->Ln();
			$this->_engine->writeHTML(' '.$description);
			$this->_engine->Ln();
		}

		$this->_engine->Ln(10);
	}

	public function makeQuestionsInfo($questions=null, $answers=null)
	{
		if(!$questions) {
			$questions = $this->_questions;
		}

		if(!is_array($questions)) {
			return;
		}

		if(!$answers) {
			$answers = $this->_answers;
		}

		if(!is_array($answers)) {
			return;
		}

		$this->_engine->SetFont($this->_font, "B", 10);
		$this->_engine->Write(10, Mage::helper('belitsoft_survey')->__('Questions'), '', 0);
		$this->_engine->Ln();

		$this->_engine->SetFontSize(8);
		$this->_engine->SetFont($this->_font, "");

		foreach($questions as $question) {
			$q_type = $question->getQuestionType();
			if(!$q_type || $q_type == 'shortanswer') {
				$q_type = 'default';
			}

			Mage::unregister('survey_report_current_question');
			Mage::register('survey_report_current_question', $question);
			try {
				$quest_html = (string) Mage::getSingleton('core/layout')->createBlock('belitsoft_survey/frontend_report_'.$q_type)->setPdf()->toHtml();
			} catch(Exception $e) {
				$quest_html = (string) Mage::getSingleton('core/layout')->createBlock('belitsoft_survey/frontend_report_default')->setPdf()->toHtml();
			}
			if(!$quest_html) {
				$quest_html = (string) Mage::getSingleton('core/layout')->createBlock('belitsoft_survey/frontend_report_default')->setPdf()->toHtml();
			}

			$this->_engine->writeHTML($quest_html, true);
			$this->_engine->Ln(1);
		}
	}

	public function makeGraphics($graphic=null, $images=null)
	{
		if(!$graphic) {
			$graphic = $this->_graphic;
		}

		if(!($graphic instanceof Belitsoft_Survey_Model_Graphic)) {
			return;
		}

		if(!$images) {
			$images = $this->_images;
		}

		if(!is_array($images)) {
			return;
		}

		$this->_engine->SetFontSize(6);

		foreach($images as $img) {
			if($img->is_image === true) {
				$image_size = getimagesize($graphic->getImageDir().$img->data);
				$this->_engine->Image($graphic->getImageDir().$img->data, 20, $this->_engine->getY()+5);
				$this->_engine->Ln(intval($image_size[1]/4));
			} else {
				$this->_appendix[] = $img;

				$quest_html  = '<strong>'.$img->title.'</strong>';
				$quest_html .=  Mage::helper('belitsoft_survey')->__('See appendix #%s', count($this->_appendix));

				$this->_engine->writeHTML($quest_html, true);
			}
		}
	}

	public function makeAppendix()
	{
		if(is_null($this->_appendix)) {
			return;
		}

		$counter = 1;
		foreach($this->_appendix as $appendix) {
			$this->_engine->AddPage();

			$this->_engine->SetFont($this->_font, "B", 10);
			$this->_engine->Write(10, Mage::helper('belitsoft_survey')->__('Appendix #%s', $counter), '', 0);
			$this->_engine->Ln(5);

			$this->_engine->SetFontSize(8);
			$this->_engine->writeHTML($appendix->title, true);
			$this->_engine->Ln(5);

			$this->_engine->SetFont($this->_font, "", 6);

			$quest_html = '';
			foreach($appendix->data as $user_answer) {
				$quest_html .= '<div style="border-bottom: 1px solid #ccc">'.nl2br(Mage::helper('belitsoft_survey')->cleanText($user_answer)).'</div>';
			}

			$this->_engine->writeHTML($quest_html, true);
			$counter++;
		}
	}

	public function getPDF()
	{
		$data = $this->_engine->Output('', 'S');

		@ob_end_clean();
		header("Content-type: application/pdf");
		header("Content-Length: ".strlen(ltrim($data)));
		header("Content-Disposition: attachment; filename=report.pdf");
		echo $data;
		exit;
	}

	protected function _makeQuestionPickone()
	{

	}

	protected function _getPerformedDate()
	{
		if($this->_first_answer instanceof Belitsoft_Survey_Model_Answer) {
			return $this->_first_answer->getCreationDate();
		}

		return '';
	}

}