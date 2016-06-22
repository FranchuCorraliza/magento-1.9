<?php
set_time_limit(0);
 class MGS_Autoimport_Model_Cron
{
         public function importar()
        {    
			Mage::log('Fin proceso: '.date("Ymd H:i s"),null,"log_guillermo.csv");
         }
} 

?>