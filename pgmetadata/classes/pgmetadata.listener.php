<?php

/**
 * @author    Pierre DRILLIN
 * @copyright 2020 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence
 */
class pgmetadataListener extends jEventListener
{
    public function ongetMapAdditions($event)
    {
        $js = array();
        $jscode = array();

        $pgmetadataConfig = array();

        $pgmetadataConfig['urls']['index'] = jUrl::get('pgmetadata~service:index');

        $js = array();
        $js[] = jUrl::get('jelix~www:getfile', array('targetmodule' => 'pgmetadata', 'file' => 'pgmetadata.js'));

        $css = array();
        $css[] = jUrl::get('jelix~www:getfile', array('targetmodule' => 'pgmetadata', 'file' => 'pgmetadata.css'));

        $jscode = array(
            'var pgmetadataConfig = '.json_encode($pgmetadataConfig).';',
        );

        // Add translation
        $locales = $this->getLocales();
        $jscode[] = 'var pgmetadataLocales = '.json_encode($locales).';';

        $event->add(
            array(
                'js' => $js,
                'jscode' => $jscode,
                'css' => $css,
            )
        );
    }

    private function getLocales($lang = null)
    {
        if (!$lang) {
            $lang = jLocale::getCurrentLang().'_'.jLocale::getCurrentCountry();
        }

        $data = array();
        $path = jApp::getModulePath('pgmetadata').'locales/'.$lang.'/pgmetadata.UTF-8.properties';
        if (file_exists($path)) {
            $lines = file($path);
            foreach ($lines as $lineNumber => $lineContent) {
                if (!empty($lineContent) and $lineContent != '\n') {
                    $exp = explode('=', trim($lineContent));
                    if (!empty($exp[0])) {
                        $data[$exp[0]] = jLocale::get('pgmetadata~pgmetadata.'.$exp[0], null, $lang);
                    }
                }
            }
        }

        return $data;
    }
}
