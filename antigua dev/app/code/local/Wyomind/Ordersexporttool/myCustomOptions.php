<?php

/* ---------------------------------------------------------------------------------------------------------- */
/* FOR DEVELOPERS ONLY                                                                                        */
/* ---------------------------------------------------------------------------------------------------------- */

class Wyomind_Ordersexporttool_Model_MyCustomOptions extends Wyomind_Ordersexporttool_Model_Profiles {
    /* ---------------------------------------------------------------------------------------------------------- */
    /* this method transforms the current value to a transformed value                                           */
    /* ---------------------------------------------------------------------------------------------------------- */

    public function _eval($exp, $value) {

        switch ($exp['options'][$this->option]) {


            case "string_format" :

                /* This is a simple fucntion to get a well formated string for name, address...  */
                $value = ucwords(strtolower(strip_tags($value)));
                // skipoptions method tells the main class that this option has been proceeded
                $this->skipOptions(1);
                return $value;
                break;

            case "date" :

                /* This is a simple fucntion to get a well formated date base on date foramt (argument 2)  */
                if ($value != null)
                    $value = date($exp['options'][$this->option + 1], strtotime($value));
                else
                    $value = null;
                // skipoptions method tells the main class that this option ans the argument have been proceeded 
                $this->skipOptions(2);
                return $value;
                break;
            case "split" :
                
                if(is_array($value)) $value = $value[(int)$exp['options'][$this->option + 1]];
                $this->skipOptions(2);
                return $value;
                break;

            case "breakline" :
                $this->skipOptions(1);
                return $value . "/breakline/";
                break;
            /* ---------------------------------------------------------------------------------------------------------- */
            /*             * ************ DO NOT CHANGE THESE LINES **************                                        */
            /* ---------------------------------------------------------------------------------------------------------- */
            default :


                // this is the old way to treat the options
                eval('$value=' . $exp['options'][$this->option] . '($value);');
                // this is the new way to treat the options
                //eval(stripslashes('$value=' . str_replace('this', '$value', $exp['options'][$this->option]) . ';'));
                $this->skipOptions(1);
                return $value;

                break;
        }
    }

}

/* This is another (and the simpliest) way to create a new function : this one transforms a formatted date (yyyy-mm-dd) to another date format */
/* In your data feed configuration you can use for example  {created_at,[dateformat(this,'d/m/Y')]} */

function dateformat($date, $format) {
    return date($format, strtotime($date));
}
