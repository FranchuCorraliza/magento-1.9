<?php

class MultiVerticalChart extends VerticalChart {
	
	function MultiVerticalChart($width = 600, $height = 250, $sections)
	{
    	parent::VerticalChart($width, $height);
		
		$this->sections = $sections;
		$this->setLabelMarginLeft(intval($height/5));
		$this->setLabelMarginRight(intval($height/8));
		$this->setLabelMarginTop(intval($height/5));
		$this->setLabelMarginBottom(intval($height/5));
		
		$this->setLabelMarginLeft(50);
        $this->setLabelMarginRight(20);
        $this->setLabelMarginTop(40);
        $this->setLabelMarginBottom(20);
	}
	
	/**
	* Compute the image layout
	*
	* @access       protected
	*/
                
	function computeLabelMargin($n)
	{
	//$this->axis = new Axis($this->yMinValue, $this->yMaxValue);
	$this->axis = new Axis(0, 90);
	$this->axis->computeBoundaries();
	$this->graphTLX = $this->margin + $this->labelMarginLeft;
	$this->graphTLY = intval(($n-1)*$this->height/$this->sections) + $this->margin + $this->labelMarginTop;
	$this->graphBRX = $this->width - $this->margin - $this->labelMarginRight;
	$this->graphBRY = intval($n*$this->height/$this->sections) - $this->margin - $this->labelMarginBottom;
	}	
	
	
	/**
	* Create the image on old image
	*
	* @access       protected
	*/
    
	        
	function createImage2()
	{
		$aquaColor = Array($this->aquaColor1, $this->aquaColor2, $this->aquaColor3, $this->aquaColor4);

		for($i = $this->graphTLY; $i < $this->graphBRY; $i++)
		{
			$color = $aquaColor[($i + 3) % 4];
			$this->primitive->line($this->graphTLX, $i, $this->graphBRX, $i, $color);
		}

		// Axis

		imagerectangle($this->img, $this->graphTLX - 1, $this->graphTLY, $this->graphTLX, $this->graphBRY, $this->axisColor1->getColor($this->img));
		imagerectangle($this->img, $this->graphTLX - 1, $this->graphBRY, $this->graphBRX, $this->graphBRY + 1, $this->axisColor1->getColor($this->img));
	}
	
	/**
	* Print the title to the image
	*
	* @access       private
	*/
                
	function printTitle($n)
	{
			if ($n == 1)
				$this->text->printCentered($this->img, 4 + ($this->labelMarginTop + $this->margin) / 2, $this->textColor, $this->title, $this->text->fontCondensedBold);
			else
				$this->text->printCentered($this->img, intval(($n-1)*$this->height/$this->sections) - 5 + ($this->labelMarginTop + $this->margin) / 2, $this->textColor, $this->title, $this->text->fontCondensedBold);
	}
	
	/**
	* Add a new sampling point to the chart
	*
	* @access       public
	* @param        Point           sampling point to add
	*/
                
	function addPoint($i, $point, $number=0)
	{
		if ( !isset($this->points[$i]) )
			$this->points[$i] = array();
		if ( !isset($this->numbers[$i]) )
			$this->numbers[$i] = array();
		if (function_exists('html_entity_decode')) {
   			$point->x = @html_entity_decode($point->x, ENT_QUOTES, 'UTF-8'); 
		}
		else { 
    		$trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        	$trans_tbl = array_flip($trans_tbl);
        	$point->x = strtr($point->x, $trans_tbl);
    	}
		$point->x = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $point->x); 
    	$point->x = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $point->x);
		array_push($this->points[$i], $point);
		array_push($this->numbers[$i], $number);
	}
	
	function renderSection($n) {
		
		$this->computeBound();
		$this->CalcBottomMargin();
		$this->computeLabelMargin($n);
		if ( $n == 1 ) {
			$this->createImage();
			
			$this->text->printText($this->img, 0, 0, $this->textColor, $this->maintitle, $this->text->fontCondensedBold);			
		}
		else 
			$this->createImage2();		
			
		$this->printLogo();
		$this->printTitle($n);
		$this->printAxis();
		$this->printBar();	
	}
	
	/**
	* Render the chart image
	*
	* @access       public
	* @param        string          name of the file to render the image to (optional)
	*/
	
	function CalcBottomMargin() 
	{
		$graphTLX = $this->margin + $this->labelMarginLeft;
		$graphBRX = $this->width - $this->margin - $this->labelMarginRight;
		$columnWidth = ($graphBRX - $graphTLX) / $this->sampleCount;
		
		foreach($this->point as $point) {
			if (ceil(strlen($point->getX())*6) > $columnWidth)
				$this->labelMarginBottom = 20 * ceil(strlen($point->getX())*6/$columnWidth);
		}		
	}
    	            
	function render($fileName = null)
	{	
			
		for ($i = 1; $i <= $this->sections; $i++) {
			$this->point = $this->points[$i];
			$this->number = $this->numbers[$i];			
			$this->usr_answer = $this->usr_answers[$i];
			$this->setTitle($this->titles[$i]);
			$this->renderSection($i);
			
		}

		if(isset($fileName))
			imagepng($this->img, $fileName);
		else
			imagepng($this->img);
	}
	
}

?>