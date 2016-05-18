<?php
/**
 * SugarCRM Connection Model
 *
 * @category   Belitsoft
 * @package    Belitsoft_Sugarcrm
 * @author     Belitsoft <bits@belitsoft.com>
 */

require Mage::getConfig()->getOptions()->getLibDir().'/libchart/libchart.php';

class Belitsoft_Survey_Model_Graphic extends Varien_Object
{
	protected $options = array('Pie');
	protected $height = 200; //for bar used $height/2
	protected $width = 600;
	protected $base_path = 'survey/gen_images/';
	protected $image_path = '';
	protected $colors = array(
		'axisColor1'	=> 'C9C9C9',
		'axisColor2'	=> '9E9E9E',
						
		'aquaColor1'	=> 'F2F2F2',
		'aquaColor2'	=> 'E7E7E7',
		'aquaColor3'	=> 'EFEFEF',
		'aquaColor4'	=> 'FDFDFD',
						
		'barColor1'		=> '2A47B5',
		'barColor2'		=> '21388F',
		'barColor3'		=> 'FF0000',
		'barColor4'		=> '75758F'
	);
	
	public function __construct($args = array())
	{
		parent::__construct();

		$this->image_path = Mage::getConfig()->getOptions()->getMediaDir().'/'.$this->base_path;
		
		if(!empty($args) && is_array($args)) {
			if(isset($args['options'])) {
				$this->options = $args['options'];
			}
		} else {
			$graphic_type = Mage::getModel('belitsoft_survey/config')->getConfigData('graphic_type');
			if($graphic_type) {
				$this->options = array($graphic_type);
			}
		}

		$this->_createWriteableDir();
	}
	
	public function getImageDir()
	{
		return $this->image_path; 
	}
		
	public function getImageUrl()
	{
		return Mage::getBaseUrl('media').$this->base_path; 
	}
	
	public function getImage($survey, $question, $start_id = 0)
	{
		return false;
	}
		
	public function getText($survey, $question)
	{
		return false;
	}
	
	function createImage(&$sectionsz, &$titlesz, &$answersz, $maintitle, $max_value)
	{
		if (count($sectionsz) > 1) {
			$filenames = array();
			foreach($sectionsz as $i => $sections) {				
				if (in_array('Bar', $this->options)) {
					$filenames[] = $this->createBarImage(array(1=>$sectionsz[$i]), array(1=>$titlesz[$i]), array(1=>$answersz[$i]), $maintitle, $max_value);
				} else {
					$filenames[] = $this->createPieImage(array(1=>$sectionsz[$i]), array(1=>$titlesz[$i]), array(1=>$answersz[$i]), $maintitle, $max_value);
				}
			}

			return $filenames;

		} else {		
			if (in_array('Bar',$this->options)) {
				return $this->createBarImage($sectionsz, $titlesz, $answersz, $maintitle, $max_value);
			} else {
				return $this->createPieImage($sectionsz, $titlesz, $answersz, $maintitle, $max_value);
			}
		}
	}

	function createBarImage($sections, $titles, $answers, $maintitle, $max_value)
	{
		global $sfConfig_absolute_path;
		
		$num_sections = count($sections);

		$chart = new MultiVerticalChart($this->width, $this->height*$num_sections, $num_sections);
		$chart->maxval = $max_value;
		$chart->maintitle = $maintitle;
		if (isset($this->options['colors'])) {
			$chart->img_colors = $this->options['colors'];
		} else {
			$chart->img_colors = $this->colors;
		}

		for($i = 1; $i <= $num_sections; $i++) {
			$rows = $sections[$i];
			foreach($rows as $row) {
				$chart->addPoint($i, new Point($row->label, $row->percent), $row->number);
			}
			$chart->usr_answers[$i] = $answers[$i];
			$chart->titles[$i] = $titles[$i];
		}

		$filename = (strlen(date('d')) < 2? '0'.date('d'): date('d')).'_'.md5(uniqid(time())).'.png';
	
		$chart->render($this->image_path.$filename); 

		return $filename;

	}
	
	function createPieImage($sections, $titles, $answers, $maintitle, $max_value)
	{
		global $sfConfig_absolute_path;
		
		$num_sections = count($sections);
				
		$chart = new MultiPieChart($this->width, $this->height*$num_sections, $num_sections);	
		$chart->maxval = $max_value;
		$chart->maintitle = $maintitle;
		if (isset($this->options['colors']))
			$chart->img_colors = $this->options['colors'];
		else
			$chart->img_colors = $this->colors;
		for($i = 1; $i <= $num_sections; $i++) {
			$rows = $sections[$i];
			foreach($rows as $row) {
				$chart->addPoint($i, new Point($row->label.' ('.$row->number.')', $row->number) );
			}
			$chart->usr_answers[$i] = $answers[$i];
			$chart->titles[$i] = $titles[$i];
		}

		$filename = (strlen(date('d')) < 2? '0'.date('d'): date('d')).'_'.md5(uniqid(time())).'.png';

		$chart->render($this->image_path.$filename); 
		
		return $filename;
	}
	
	public function clearOldImages($day = null)
	{
		if($day == null) {
			$day = date('d');
		} elseif(strlen($day) < 2) {
			$day = '0'.$day;
		}
			
		$current_dir = opendir( $this->image_path );
		$old_umask = umask(0);
		while (false !== ($entryname = readdir( $current_dir ))) {
			if ($entryname != '.' and $entryname != '..') {
				if (!is_dir( $this->image_path . $entryname ) && substr($entryname, 0, 2) != $day) {
					@chmod( $this->image_path . $entryname, 0777);
					unlink( $this->image_path . $entryname );
				}
			}
		}
		umask($old_umask);
		closedir( $current_dir );
	}

	/**
     * Create Writeable directory if it doesn't exist
     *
     * @param string Absolute directory path
     * @return void
     */
    protected function _createWriteableDir()
    {
        $io = new Varien_Io_File();
        if (!$io->isWriteable($this->image_path) && !$io->mkdir($this->image_path, 0777, true)) {
            Mage::throwException(Mage::helper('catalog')->__("Cannot create writeable directory '%s'.", $this->image_path));
        }
    }
	
}