<?php
/**
 * @author    Laurent Jouanneau
 * @copyright 2022 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence (MPL)
 */
class pgmdadminCtrl extends jController
{

    // Configure access for jacl2 rights management
    public $pluginParams = array(
        '*' => array( 'jacl2.right'=>'lizmap.admin.access'),
    );

    public function index()
    {
        $rep = $this->getResponse('html');

        $tpl = new jTpl();

        try {
            // we get possible existing parameters that we don't edit
            $profile = jProfiles::get('jdb', 'pgmetadata', true);
            unset($profile['_name']);
            if (!isset($profile['port']) || $profile['port'] == '') {
                $profile['port'] = '5432';
            }
        }
        catch (\jException $e) {
            // profile unknown
            $profile = array(
                'host' => '',
                'port' => '5432',
                'database' => '',
                'user' => '',
                'password' => '',
            );
        }

        $tpl->assign('profile', $profile);

        $rep->body->assign('MAIN', $tpl->fetch('config'));
        return $rep;
    }

    public function check()
    {
        $rep = $this->getResponse('json');

        try {

            $checker = new \PgMetadata\RDFDCatChecker();
            $result = $checker->checkDCatSupport('pgmetadata');

        } catch (\PgMetaData\DCatSupportException $e) {
            $rep->data = array (
                'status' => 'error',
                'reason' => 'bad_profile'
            );
            return $rep;
        }

        switch($result) {
            case $checker::STATUS_OK:
                $rep->data = array (
                    'status' => 'ok',
                    'reason' => 'ok_profile_ok_view'
                );
                return $rep;
                break;
            case $checker::STATUS_NO_VIEW:
                $rep->data = array (
                    'status' => 'error',
                    'reason' => 'ok_profile_no_view'
                );
                return $rep;
                break;
            case $checker::STATUS_BAD_PROFILE:
                $rep->data = array (
                    'status' => 'error',
                    'reason' => 'bad_profile'
                );
                return $rep;
                break;
        }


        try {
            $result = $checker->checkDCatSupport('');
        } catch (\PgMetaData\DCatSupportException $e) {
            $rep->data = array (
                'status' => 'error',
                'reason' => 'bad_profile'
            );
            return $rep;
        }
        switch($result) {
            case $checker::STATUS_OK:
                $rep->data = array (
                    'status' => 'ok',
                    'reason' => 'no_profile_ok_view'
                );
                break;
            case $checker::STATUS_NO_VIEW:
                $rep->data = array (
                    'status' => 'error',
                    'reason' => 'no_profile_no_view'
                );
                break;
            case $checker::STATUS_BAD_PROFILE:
                $rep->data = array (
                    'status' => 'error',
                    'reason' => 'no_database'
                );
                break;
        }

        return $rep;
    }

    public function save()
    {
        $rep = $this->getResponse('json');

        try {
            // we get possible existing parameters that we don't edit
            $profile = jProfiles::get('jdb', 'pgmetadata', true);
            unset($profile['_name']);
        }
        catch (\jException $e) {
            // profile unknown
            $profile = array();
        }
        $profile['driver'] = 'pgsql';
        $profile['host'] = $this->param('pgHost');
        $profile['port'] = $this->param('pgPort');
        $profile['database'] = $this->param('pgDatabase');
        $profile['user'] = $this->param('pgUser');
        $profile['password'] = $this->param('pgPassword');
        $profile['timeout'] = rand(1,60);

        jProfiles::createVirtualProfile('jdb', 'pgmetadata', $profile);

        try {
            // RDFDCat search the profile that is used for
            $checker = new \PgMetadata\RDFDCatChecker();
            $result = $checker->checkDCatSupport('pgmetadata');
        } catch (\PgMetaData\DCatSupportException $e) {
            $rep->data = array (
                'status' => 'error',
                'reason' => 'bad_profile'
            );
            return $rep;
        }

        if ($result != $checker::STATUS_BAD_PROFILE) {
            $ini = new jIniFileModifier(jApp::configPath('profiles.ini.php'));
            $ini->removeValue('pgmetadata', 'jdb');
            $ini->setValues($profile, 'jdb:pgmetadata');
            $ini->save();
        }

        switch($result) {
            case $checker::STATUS_OK:
                $rep->data = array (
                    'status' => 'ok',
                    'reason' => 'ok_profile_ok_vue'
                );
                return $rep;
                break;
            case $checker::STATUS_NO_VIEW:
                $rep->data = array (
                    'status' => 'error',
                    'reason' => 'ok_profile_no_view'
                );
                return $rep;
                break;
            case $checker::STATUS_BAD_PROFILE:
                $rep->data = array (
                    'status' => 'error',
                    'reason' => 'bad_profile'
                );
                return $rep;
                break;
        }

        return $rep;
    }
}