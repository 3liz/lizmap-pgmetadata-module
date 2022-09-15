<?php
/**
* @package   lizmap
* @subpackage pgmetadataAdmin
* @author    Laurent Jouanneau
* @copyright 2022 3liz
* @link      http://3liz.com
* @license    Mozilla Public License : http://www.mozilla.org/MPL/
*/

/**
 * @deprecated for Lizmap 3.4/3.5 only
 */
class pgmetadataAdminModuleInstaller extends jInstallerModule {

    function install() {
        if ($this->entryPoint->getEpId() == 'admin') {
            $localConfigIni = $this->entryPoint->localConfigIni->getMaster();

            $adminControllers = $localConfigIni->getValue('admin', 'simple_urlengine_entrypoints');
            $mbCtrl = 'pgmetadataAdmin~*@classic';
            if (strpos($adminControllers, $mbCtrl) === false) {
                // let's register pgmetadataAdmin controllers
                $adminControllers .= ', '.$mbCtrl;
                $localConfigIni->setValue('admin', $adminControllers, 'simple_urlengine_entrypoints');
            }
        }
    }
}
