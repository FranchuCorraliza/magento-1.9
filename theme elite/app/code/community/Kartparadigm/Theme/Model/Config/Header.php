<?php


class Kartparadigm_Theme_Model_Config_Header
{
	/**
	 * google fonts list
	 *
	 * @var string
	 */
	private $gfonts = "No,Yes";

    public function toOptionArray()
    {
	    $fonts = explode(',', $this->gfonts);
	    $options = array();
	    foreach ($fonts as $f ){
		    $options[] = array(
			    'value' => $f,
			    'label' => $f,
		    );
	    }

        return $options;
    }

}
