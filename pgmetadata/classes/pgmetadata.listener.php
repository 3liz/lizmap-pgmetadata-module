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

        $jscode = array(
            'var pgmetadataConfig = '.json_encode($pgmetadataConfig),
        );

        $event->add(
            array(
                'js' => $js,
                'jscode' => $jscode,
            )
        );
    }
}
