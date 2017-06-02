<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Fpc_Helper_Fpcmf_Contentmf
{
    /**
     * @param string $content
     * @return void
     */
    public function clearWrappers(&$content)
    {
        $content = preg_replace(Mirasvit_Fpc_Model_Configmf::HTML_NAME_PATTERN_OPEN, '', $content);
        $content = preg_replace(Mirasvit_Fpc_Model_Configmf::HTML_NAME_PATTERN_CLOSE, '', $content);

        $sid = array('___SID=U&amp;','___SID=U&','?___SID=U');
        $content = str_replace($sid, '', $content);

        $formKey = Mirasvit_Fpc_Helper_Fpcmf_Sessionmf::getFormKey();

        $content = preg_replace(
            '/<input type="hidden" name="form_key" value="(.*?)" \\/>/i',
            '<input type="hidden" name="form_key" value="' . $formKey . '" />',
            $content
        );

        $content = preg_replace(
            '/name="form_key" type="hidden" value="(.*?)" \\/>/i',
            'name="form_key" type="hidden" value="' . $formKey . '" />',
            $content
        );

        $content = preg_replace(
            '/\\/form_key\\/([^\"\'\/\s])+(\/|\"|\')/i',
            '/form_key/' . $formKey . "$2",
            $content
        );

        $content = preg_replace(
            '/\\/form_key' . '\\\\' . '\\/(.*?)' . '\\\\' . '\\//i',
            '/form_key\/' . $formKey . '\/',
            $content
        );

        return $this;
    }
}
