<?php


class Kartparadigm_Theme_Model_Config_Font
{
	/**
	 * google fonts list
	 *
	 * @var string
	 */
	private $gfonts = "Abel,Arial,Abril Fatface,Aclonica,Acme,Actor,Adamina,Aguafina Script,Aladin,Aldrich,Alegreya,Alegreya SC,Alex Brush,Alfa Slab One,Alice,Alike,Alike Angular,Allan,Allerta,Allerta Stencil,Allura,Almendra,Almendra SC,Amaranth,Amatic SC,Amethysta,Andada,Andika,Annie Use Your Telescope,Anonymous Pro,Antic,Anton,Arapey,Arbutus,Architects Daughter,Arimo,Arizonia,Armata,Artifika,Arvo,Asap,Asset,Astloch,Asul,Atomic Age,Aubrey,Bad Script,Balthazar,Bangers,Basic,Baumans,Belgrano,Bentham,Bevan,Dosis,Pacifico,Roboto,Niconne,Oswald,Merriweather,Lobster,	
Orbitron,Open Sans";

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
